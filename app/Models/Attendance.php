<?php
class Attendance {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // --- Student Attendance ---
    
    // Get attendance for a class on a specific date with parent contact & details
    public function getStudentAttendance($class_id, $section_id, $date){
        $this->db->query("SELECT s.id as student_id, COALESCE(u.name, s.name) as name, s.admission_no, s.roll_no,
                                 s.father_name, s.parent_phone, s.bform_cnic, s.student_photo,
                                 sa.id as attendance_id, sa.attendance_type, sa.remark, sa.entry_time, sa.sms_sent
                          FROM students s
                          LEFT JOIN users u ON s.user_id = u.id
                          LEFT JOIN student_attendance sa 
                          ON s.id = sa.student_id AND sa.date = :date
                          WHERE s.class_id = :class_id AND s.section_id = :section_id AND s.school_id = :school_id
                          ORDER BY CAST(s.roll_no AS UNSIGNED) ASC, u.name ASC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':class_id', (int)$class_id);
        $this->db->bind(':section_id', (int)$section_id);
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    public function getAttendanceByStudent($student_id){
        $this->db->query("SELECT * FROM student_attendance 
                          WHERE student_id = :sid AND school_id = :school_id
                          ORDER BY date DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':sid', (int)$student_id);
        return $this->db->resultSet();
    }

    public function saveStudentAttendance($student_id, $class_id, $section_id, $date, $type, $remark='', $entry_time=null){
        if(!$entry_time && ($type === 'Present' || $type === 'Late')){
            $entry_time = date('H:i:s');
        }

        $this->db->query("INSERT INTO student_attendance (school_id, student_id, class_id, section_id, date, attendance_type, remark, entry_time)
                          VALUES (:school_id, :sid, :cid, :secid, :date, :type, :remark, :entry_time)
                          ON DUPLICATE KEY UPDATE 
                          attendance_type = :type_update, 
                          remark = :remark_update,
                          entry_time = COALESCE(:time_update, entry_time)");
        
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':sid', (int)$student_id);
        $this->db->bind(':cid', (int)$class_id);
        $this->db->bind(':secid', (int)$section_id);
        $this->db->bind(':date', $date);
        $this->db->bind(':type', $type);
        $this->db->bind(':remark', trim($remark));
        $this->db->bind(':entry_time', $entry_time);
        
        $this->db->bind(':type_update', $type);
        $this->db->bind(':remark_update', trim($remark));
        $this->db->bind(':time_update', $entry_time);
        
        return $this->db->execute();
    }

    // --- MODULE 7: MONTHLY ATTENDANCE REGISTER MATRIX ---
    public function getMonthlyAttendanceRegister($class_id, $section_id, $year, $month){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $class_id = (int)$class_id;
        $section_id = (int)$section_id;
        $year = (int)$year;
        $month = (int)$month;

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $daysInMonth = (int)date('t', strtotime($startDate));
        $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

        // Build days metadata (weekday, weekend indicator)
        $daysMeta = [];
        $totalWorkingDays = 0;
        for($d = 1; $d <= $daysInMonth; $d++){
            $curDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayOfWeek = date('D', strtotime($curDate));
            $isSunday = ($dayOfWeek === 'Sun');
            if(!$isSunday) $totalWorkingDays++;

            $daysMeta[$d] = [
                'day' => $d,
                'date' => $curDate,
                'day_name' => $dayOfWeek,
                'is_weekend' => $isSunday
            ];
        }

        // 1. Fetch Students
        $this->db->query("SELECT s.id as student_id, COALESCE(u.name, s.name) as name, s.roll_no, s.admission_no,
                                 c.class_name, sec.section_name
                          FROM students s
                          LEFT JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.class_id = :cid AND s.section_id = :secid AND s.school_id = :school_id
                          ORDER BY CAST(s.roll_no AS UNSIGNED) ASC, u.name ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', $class_id);
        $this->db->bind(':secid', $section_id);
        $students = $this->db->resultSet();

        // 2. Fetch all attendance logs for this month
        $this->db->query("SELECT student_id, DAY(date) as day_num, attendance_type, remark
                          FROM student_attendance
                          WHERE class_id = :cid AND section_id = :secid AND school_id = :school_id
                            AND date BETWEEN :start AND :end");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', $class_id);
        $this->db->bind(':secid', $section_id);
        $this->db->bind(':start', $startDate);
        $this->db->bind(':end', $endDate);
        $logs = $this->db->resultSet();

        $matrix = [];
        foreach($logs as $l){
            $matrix[$l->student_id][$l->day_num] = $l->attendance_type;
        }

        // 3. Compile Student Rosters & Month Totals
        $compiledStudents = [];
        $dailyClassPresence = array_fill(1, $daysInMonth, 0);

        foreach($students as $st){
            $pCount = 0;
            $aCount = 0;
            $lCount = 0;
            $hCount = 0;
            $dayLogs = [];

            for($d = 1; $d <= $daysInMonth; $d++){
                $type = $matrix[$st->student_id][$d] ?? null;
                $dayLogs[$d] = $type;

                if($type === 'Present'){
                    $pCount++;
                    $dailyClassPresence[$d]++;
                } elseif($type === 'Absent'){
                    $aCount++;
                } elseif($type === 'Late'){
                    $lCount++;
                    $dailyClassPresence[$d]++;
                } elseif($type === 'Half Day'){
                    $hCount++;
                    $dailyClassPresence[$d] += 0.5;
                }
            }

            // Attendance Percentage: (Present + Late + 0.5*Half) / Total Working Days
            $effectivePresence = $pCount + $lCount + ($hCount * 0.5);
            $attPercentage = $totalWorkingDays > 0 ? round(($effectivePresence / $totalWorkingDays) * 100, 1) : 100;

            $compiledStudents[] = [
                'student_id' => $st->student_id,
                'name' => $st->name,
                'roll_no' => $st->roll_no,
                'admission_no' => $st->admission_no,
                'days' => $dayLogs,
                'present' => $pCount,
                'absent' => $aCount,
                'late' => $lCount,
                'half_day' => $hCount,
                'percentage' => $attPercentage
            ];
        }

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => date('F Y', strtotime($startDate)),
            'days_in_month' => $daysInMonth,
            'working_days' => $totalWorkingDays,
            'days_meta' => $daysMeta,
            'students' => $compiledStudents,
            'daily_presence' => $dailyClassPresence,
            'total_enrolled' => count($students)
        ];
    }

    // Get Absentee List for today to preview parent alerts
    public function getTodayAbsentNotificationList($class_id, $section_id, $date){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.id as student_id, COALESCE(u.name, s.name) as name, s.roll_no, s.admission_no,
                                 s.father_name, s.parent_phone, c.class_name, sec.section_name,
                                 sa.attendance_type, sa.remark
                          FROM student_attendance sa
                          JOIN students s ON sa.student_id = s.id
                          LEFT JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE sa.class_id = :cid AND sa.section_id = :secid AND sa.date = :date
                            AND sa.attendance_type IN ('Absent', 'Late') AND sa.school_id = :school_id
                          ORDER BY CAST(s.roll_no AS UNSIGNED) ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', (int)$class_id);
        $this->db->bind(':secid', (int)$section_id);
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    // --- Staff Attendance ---
    public function getStaffAttendance($date, $role = null){
        $sql = "SELECT u.id as staff_id, u.name, u.email, u.role,
                sa.id as attendance_id, sa.attendance_type, sa.remark
                FROM users u
                LEFT JOIN staff_attendance sa 
                ON u.id = sa.staff_id AND sa.date = :date
                WHERE u.school_id = :school_id AND u.role IN ('admin', 'teacher', 'super_admin', 'librarian', 'receptionist')";
        
        if($role){
            $sql .= " AND u.role = :role";
        }
        
        $sql .= " ORDER BY u.role, u.name";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':date', $date);
        if($role) $this->db->bind(':role', $role);
        
        return $this->db->resultSet();
    }

    public function saveStaffAttendance($staff_id, $date, $type, $remark=''){
        $this->db->query("INSERT INTO staff_attendance (school_id, staff_id, date, attendance_type, remark)
                          VALUES (:school_id, :sid, :date, :type, :remark)
                          ON DUPLICATE KEY UPDATE 
                          attendance_type = :type_update, 
                          remark = :remark_update");
        
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':sid', $staff_id);
        $this->db->bind(':date', $date);
        $this->db->bind(':type', $type);
        $this->db->bind(':remark', $remark);
        
        $this->db->bind(':type_update', $type);
        $this->db->bind(':remark_update', $remark);
        
        return $this->db->execute();
    }
}
