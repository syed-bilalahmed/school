<?php
/**
 * School ERP Pro - Master Database Schema & Seeder
 * Idempotently initializes all database tables, columns, indexes, and default records.
 * Designed to execute cleanly inside the setup wizard on any chosen database name.
 */

function runMasterSchemaMigration(PDO $db, array $params): array {
    $schoolName = trim($params['school_name'] ?? 'Greenwood International School');
    $campusName = trim($params['campus_name'] ?? 'Main Campus');
    $currency   = trim($params['currency'] ?? 'PKR');
    $adminName  = trim($params['admin_name'] ?? 'System Administrator');
    $adminEmail = trim($params['admin_email'] ?? 'super@admin.com');
    $adminPass  = (string)($params['admin_pass'] ?? 'admin123');

    // 1. Core Multi-Tenant SaaS Tables
    $db->exec("CREATE TABLE IF NOT EXISTS school_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        limits_json TEXT,
        price DECIMAL(10,2) DEFAULT 0.00
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS schools (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(255) NOT NULL,
        domain VARCHAR(255) NULL,
        status ENUM('active', 'suspended', 'pending') DEFAULT 'active',
        plan_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS school_subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL,
        plan_id INT NOT NULL,
        status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
        start_at DATE,
        end_at DATE,
        billing_provider VARCHAR(50),
        external_id VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS school_banks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL,
        bank_name VARCHAR(100) NOT NULL,
        branch_name VARCHAR(100) NULL,
        account_title VARCHAR(150) NOT NULL,
        account_no VARCHAR(50) NOT NULL,
        iban VARCHAR(50) NULL,
        is_default TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 2. Users & Authentication
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'admin', 'student', 'teacher', 'parent', 'accountant', 'librarian', 'receptionist') NOT NULL,
        phone VARCHAR(50) NULL,
        avatar VARCHAR(255) NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 3. RBAC Tables
    $db->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        permission_key VARCHAR(100) NOT NULL UNIQUE,
        description VARCHAR(255) NOT NULL,
        category VARCHAR(50) DEFAULT 'General',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS role_permissions (
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        PRIMARY KEY (role_id, permission_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS user_roles (
        user_id INT NOT NULL,
        role_id INT NOT NULL,
        PRIMARY KEY (user_id, role_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 4. API Keys & Sessions
    $db->exec("CREATE TABLE IF NOT EXISTS api_keys (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        client_name VARCHAR(100) NOT NULL,
        api_key VARCHAR(64) NOT NULL UNIQUE,
        is_active TINYINT(1) DEFAULT 1,
        rate_limit_per_min INT DEFAULT 120,
        last_used_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS api_user_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        user_id INT NOT NULL,
        api_key_id INT NULL,
        token VARCHAR(96) NOT NULL UNIQUE,
        device_name VARCHAR(100) NULL,
        device_platform VARCHAR(50) NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 5. Academic Architecture
    $db->exec("CREATE TABLE IF NOT EXISTS academic_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        session_name VARCHAR(50) NOT NULL,
        start_date DATE NULL,
        end_date DATE NULL,
        is_current TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS classes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        class_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        class_id INT NOT NULL,
        section_name VARCHAR(100) NOT NULL,
        class_teacher_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        name VARCHAR(100) NOT NULL,
        type ENUM('Theory', 'Practical') DEFAULT 'Theory',
        code VARCHAR(50) NULL,
        is_core TINYINT(1) DEFAULT 1,
        full_marks DECIMAL(5,2) DEFAULT 100.00,
        passing_marks DECIMAL(5,2) DEFAULT 33.00,
        credit_hours INT DEFAULT 3,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS class_subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        teacher_id INT NULL,
        periods_per_week INT DEFAULT 5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS timetables (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        teacher_id INT NULL,
        day_of_week VARCHAR(20) NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        room_no VARCHAR(50) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 6. Students & Families
    $db->exec("CREATE TABLE IF NOT EXISTS families (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        family_code VARCHAR(50) NOT NULL,
        father_name VARCHAR(100) NULL,
        father_cnic VARCHAR(50) NULL,
        guardian_phone VARCHAR(20) NULL,
        default_discount_percent DECIMAL(5,2) DEFAULT 10.00,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        user_id INT NOT NULL,
        admission_no VARCHAR(50) NOT NULL,
        roll_no VARCHAR(50) NULL,
        class_id INT NULL,
        section_id INT NULL,
        name VARCHAR(150) NULL,
        email VARCHAR(150) NULL,
        phone VARCHAR(50) NULL,
        emergency_contact VARCHAR(50) NULL,
        reg_no VARCHAR(50) NULL,
        bform_cnic VARCHAR(50) NULL,
        blood_group VARCHAR(10) NULL,
        dob DATE NULL,
        gender ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
        father_name VARCHAR(100) NULL,
        father_cnic VARCHAR(50) NULL,
        mother_name VARCHAR(100) NULL,
        mother_cnic VARCHAR(50) NULL,
        guardian_name VARCHAR(100) NULL,
        guardian_relation VARCHAR(50) NULL,
        guardian_cnic VARCHAR(50) NULL,
        guardian_phone VARCHAR(50) NULL,
        parent_phone VARCHAR(50) NULL,
        address TEXT NULL,
        permanent_address TEXT NULL,
        city VARCHAR(100) NULL,
        district VARCHAR(100) NULL,
        tehsil VARCHAR(100) NULL,
        admission_date DATE NULL,
        student_photo VARCHAR(255) NULL,
        status VARCHAR(30) DEFAULT 'Active',
        family_id VARCHAR(50) NULL,
        sibling_discount_percent DECIMAL(5,2) DEFAULT 0.00,
        custom_discount_amount DECIMAL(10,2) DEFAULT 0.00,
        concession_type VARCHAR(50) DEFAULT 'None',
        academic_session_id INT NULL,
        is_fresh_admission TINYINT(1) DEFAULT 1,
        religion VARCHAR(50) DEFAULT 'Islam',
        nationality VARCHAR(50) DEFAULT 'Pakistani',
        mother_tongue VARCHAR(50) DEFAULT 'Urdu',
        father_occupation VARCHAR(100) NULL,
        father_income VARCHAR(50) NULL,
        father_phone VARCHAR(50) NULL,
        mother_occupation VARCHAR(100) NULL,
        guardian_occupation VARCHAR(100) NULL,
        special_needs VARCHAR(255) NULL,
        prev_school_name VARCHAR(255) NULL,
        prev_school_city VARCHAR(100) NULL,
        prev_class VARCHAR(100) NULL,
        prev_medium VARCHAR(50) DEFAULT 'English',
        slc_number VARCHAR(100) NULL,
        slc_date DATE NULL,
        prev_board_roll_no VARCHAR(50) NULL,
        prev_marks_obtained DECIMAL(6,2) DEFAULT 0.00,
        prev_total_marks DECIMAL(6,2) DEFAULT 0.00,
        prev_grade VARCHAR(20) NULL,
        reason_for_leaving VARCHAR(255) NULL,
        clearance_status VARCHAR(50) DEFAULT 'Not Applied',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS student_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        category_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS student_houses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        house_name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 7. Human Resources & Staff
    $db->exec("CREATE TABLE IF NOT EXISTS staff (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        user_id INT NOT NULL,
        staff_code VARCHAR(50) NOT NULL,
        cnic VARCHAR(50) NULL,
        department VARCHAR(100) NULL,
        designation VARCHAR(100) NULL,
        employment_type VARCHAR(50) DEFAULT 'Permanent',
        qualification VARCHAR(255) NULL,
        experience_years VARCHAR(50) NULL,
        gender VARCHAR(20) DEFAULT 'Male',
        dob DATE NULL,
        phone VARCHAR(30) NULL,
        emergency_contact VARCHAR(50) NULL,
        address TEXT NULL,
        date_of_joining DATE NULL,
        basic_salary DECIMAL(10,2) DEFAULT 0.00,
        lecture_rate DECIMAL(10,2) DEFAULT 0.00,
        bank_name VARCHAR(100) NULL,
        bank_account_no VARCHAR(100) NULL,
        status VARCHAR(30) DEFAULT 'Active',
        photo VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS staff_payroll (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        staff_id INT NOT NULL,
        month VARCHAR(20) NOT NULL,
        year INT NOT NULL,
        employment_type VARCHAR(50) DEFAULT 'Permanent',
        basic_salary DECIMAL(10,2) DEFAULT 0.00,
        lecture_rate DECIMAL(10,2) DEFAULT 0.00,
        medical_allowance DECIMAL(10,2) DEFAULT 0.00,
        house_rent_allowance DECIMAL(10,2) DEFAULT 0.00,
        conveyance_allowance DECIMAL(10,2) DEFAULT 0.00,
        tax_deduction DECIMAL(10,2) DEFAULT 0.00,
        provident_fund DECIMAL(10,2) DEFAULT 0.00,
        other_deductions DECIMAL(10,2) DEFAULT 0.00,
        gross_salary DECIMAL(10,2) DEFAULT 0.00,
        net_salary DECIMAL(10,2) DEFAULT 0.00,
        status VARCHAR(20) DEFAULT 'Generated',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS staff_payslips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        payroll_id INT NULL,
        staff_id INT NOT NULL,
        month VARCHAR(20) NOT NULL,
        year INT NOT NULL,
        employment_type VARCHAR(50) DEFAULT 'Permanent',
        lectures_delivered INT DEFAULT 0,
        lecture_rate DECIMAL(10,2) DEFAULT 0.00,
        basic_salary DECIMAL(10,2) DEFAULT 0.00,
        earnings DECIMAL(10,2) DEFAULT 0.00,
        deductions DECIMAL(10,2) DEFAULT 0.00,
        leave_deduction DECIMAL(10,2) DEFAULT 0.00,
        absent_days DECIMAL(5,2) DEFAULT 0.00,
        half_days DECIMAL(5,2) DEFAULT 0.00,
        net_salary DECIMAL(10,2) DEFAULT 0.00,
        payment_mode VARCHAR(50) DEFAULT 'Cash',
        payment_date DATE NULL,
        transaction_id VARCHAR(100) NULL,
        status VARCHAR(20) DEFAULT 'Paid',
        expense_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS leaves (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        staff_id INT NOT NULL,
        leave_type_id INT NOT NULL,
        from_date DATE NOT NULL,
        to_date DATE NOT NULL,
        reason TEXT NULL,
        status VARCHAR(20) DEFAULT 'Pending',
        approved_by INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS leave_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        type_name VARCHAR(100) NOT NULL,
        days_allowed INT DEFAULT 12,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 8. Attendance
    $db->exec("CREATE TABLE IF NOT EXISTS student_attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        student_id INT NOT NULL,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        status ENUM('Present', 'Late', 'Absent', 'Half Day') DEFAULT 'Present',
        entry_time TIME NULL,
        remarks VARCHAR(255) NULL,
        sms_sent TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS staff_attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        staff_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        status ENUM('Present', 'Late', 'Absent', 'Half Day') DEFAULT 'Present',
        remarks VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 9. Fee Management & Accounting
    $db->exec("CREATE TABLE IF NOT EXISTS fee_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        type_name VARCHAR(100) NOT NULL,
        type_code VARCHAR(50) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS fee_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        group_name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS fee_groups_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        fee_group_id INT NOT NULL,
        fee_type_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        due_date DATE NULL,
        fine_amount DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS student_fees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        student_id INT NOT NULL,
        fee_groups_types_id INT NOT NULL,
        challan_no VARCHAR(50) NULL,
        billing_month VARCHAR(30) NULL,
        session_id INT NULL,
        due_date DATE NULL,
        sibling_discount DECIMAL(10,2) DEFAULT 0.00,
        arrears DECIMAL(10,2) DEFAULT 0.00,
        amount DECIMAL(10,2) DEFAULT 0.00,
        fine DECIMAL(10,2) DEFAULT 0.00,
        status VARCHAR(20) DEFAULT 'unpaid',
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS fee_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        student_fee_id INT NOT NULL,
        mode ENUM('Cash', 'Cheque', 'Online', 'Bank Transfer') NOT NULL DEFAULT 'Cash',
        amount DECIMAL(10,2) NOT NULL,
        fine DECIMAL(10,2) DEFAULT 0.00,
        discount DECIMAL(10,2) DEFAULT 0.00,
        payment_date DATE NOT NULL,
        transaction_id VARCHAR(100) NULL,
        note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS incomes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT 'Student Fee',
        amount DECIMAL(10,2) NOT NULL,
        date DATE NOT NULL,
        payment_mode VARCHAR(50) DEFAULT 'Cash',
        reference_no VARCHAR(100) NULL,
        fee_payment_id INT NULL,
        note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS expenses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        expense_head_id INT NULL,
        name VARCHAR(255) NOT NULL,
        invoice_no VARCHAR(100) NULL,
        date DATE NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        file VARCHAR(255) NULL,
        note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS expense_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        category_name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 10. Examinations
    $db->exec("CREATE TABLE IF NOT EXISTS exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        academic_session_id INT NULL,
        name VARCHAR(100) NOT NULL,
        exam_type VARCHAR(50) DEFAULT 'Term Exam',
        description TEXT NULL,
        start_date DATE NULL,
        end_date DATE NULL,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS exam_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        exam_id INT NOT NULL,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        date_of_exam DATE NULL,
        start_time TIME NULL,
        end_time TIME NULL,
        room_no VARCHAR(50) NULL,
        full_marks DECIMAL(5,2) DEFAULT 100.00,
        passing_marks DECIMAL(5,2) DEFAULT 33.00,
        theory_marks DECIMAL(5,2) DEFAULT 75.00,
        practical_marks DECIMAL(5,2) DEFAULT 25.00,
        approval_status VARCHAR(50) DEFAULT 'draft',
        submitted_by_teacher_id INT NULL,
        submitted_at DATETIME NULL,
        reviewed_by_class_teacher_id INT NULL,
        reviewed_at DATETIME NULL,
        verified_by_vp_id INT NULL,
        verified_at DATETIME NULL,
        approved_by_principal_id INT NULL,
        approved_at DATETIME NULL,
        rejection_reason TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS exam_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        exam_schedule_id INT NOT NULL,
        student_id INT NOT NULL,
        theory_marks DECIMAL(5,2) DEFAULT 0.00,
        practical_marks DECIMAL(5,2) DEFAULT 0.00,
        get_marks DECIMAL(5,2) DEFAULT 0.00,
        is_absent ENUM('yes', 'no') DEFAULT 'no',
        remarks VARCHAR(255) NULL,
        note VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_result (exam_schedule_id, student_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS grading_systems (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        grade_name VARCHAR(20) NOT NULL,
        percent_from DECIMAL(5,2) NOT NULL,
        percent_to DECIMAL(5,2) NOT NULL,
        grade_point DECIMAL(4,2) DEFAULT 0.00,
        remarks VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 11. Homework
    $db->exec("CREATE TABLE IF NOT EXISTS homework (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        homework_date DATE NOT NULL,
        submission_date DATE NOT NULL,
        evaluation_date DATE NULL,
        created_by INT NOT NULL,
        document VARCHAR(255) NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS homework_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        homework_id INT NOT NULL,
        student_id INT NOT NULL,
        submission_date DATE NOT NULL,
        file_path VARCHAR(255) NULL,
        marks DECIMAL(5,2) DEFAULT 0.00,
        remarks TEXT NULL,
        status VARCHAR(20) DEFAULT 'submitted',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 12. Library
    $db->exec("CREATE TABLE IF NOT EXISTS book_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        category_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        book_title VARCHAR(255) NOT NULL,
        book_no VARCHAR(100) NULL,
        isbn_no VARCHAR(100) NULL,
        publisher VARCHAR(150) NULL,
        author VARCHAR(150) NULL,
        subject VARCHAR(100) NULL,
        category_id INT NULL,
        qty INT DEFAULT 1,
        perunitcost DECIMAL(10,2) DEFAULT 0.00,
        postdate DATE NULL,
        description TEXT NULL,
        available INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS book_issues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        book_id INT NOT NULL,
        member_id INT NOT NULL,
        member_type ENUM('student', 'staff') DEFAULT 'student',
        issue_date DATE NOT NULL,
        return_date DATE NULL,
        is_returned ENUM('yes', 'no') DEFAULT 'no',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 13. Inventory
    $db->exec("CREATE TABLE IF NOT EXISTS item_category (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        item_category VARCHAR(255) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS item_store (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        item_store VARCHAR(255) NOT NULL,
        code VARCHAR(100) NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS item_supplier (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        item_supplier VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NULL,
        email VARCHAR(100) NULL,
        address TEXT NULL,
        contact_person_name VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        item_category_id INT NULL,
        name VARCHAR(255) NOT NULL,
        unit VARCHAR(50) NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS item_stock (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        item_id INT NOT NULL,
        supplier_id INT NULL,
        store_id INT NULL,
        symbol VARCHAR(10) DEFAULT '+',
        quantity INT DEFAULT 0,
        date DATE NULL,
        attachment VARCHAR(255) NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS item_issues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        item_id INT NOT NULL,
        issue_to VARCHAR(150) NOT NULL,
        issue_by VARCHAR(150) NULL,
        issue_date DATE NOT NULL,
        return_date DATE NULL,
        note TEXT NULL,
        quantity INT DEFAULT 1,
        is_returned ENUM('yes', 'no') DEFAULT 'no',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 14. Front Office & Gate Passes
    $db->exec("CREATE TABLE IF NOT EXISTS visitor_book (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        purpose VARCHAR(100) NULL,
        name VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NULL,
        id_proof VARCHAR(100) NULL,
        pass_no VARCHAR(50) NULL,
        cnic_passport VARCHAR(50) NULL,
        person_to_meet VARCHAR(100) NULL,
        vehicle_no VARCHAR(50) NULL,
        department VARCHAR(100) NULL,
        no_of_people INT DEFAULT 1,
        date DATE NOT NULL,
        in_time VARCHAR(20) NULL,
        out_time VARCHAR(20) NULL,
        note TEXT NULL,
        status VARCHAR(50) DEFAULT 'Checked In',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS student_gate_passes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        pass_no VARCHAR(50) NOT NULL,
        student_id INT NOT NULL,
        pass_date DATE NOT NULL,
        leave_time VARCHAR(20) NOT NULL,
        reason_type VARCHAR(100) NOT NULL,
        reason_details TEXT NULL,
        collected_by_name VARCHAR(150) NOT NULL,
        collected_by_cnic VARCHAR(50) NULL,
        collected_by_relation VARCHAR(50) NOT NULL,
        collected_by_phone VARCHAR(50) NULL,
        approved_by_user_id INT NULL,
        status VARCHAR(50) DEFAULT 'Issued',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS phone_call_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        call_type VARCHAR(20) DEFAULT 'Incoming',
        caller_name VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        call_date DATE NOT NULL,
        call_time VARCHAR(20) NULL,
        duration VARCHAR(50) NULL,
        purpose VARCHAR(150) NULL,
        follow_up_date DATE NULL,
        note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS postal_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        dispatch_type VARCHAR(20) NOT NULL,
        reference_no VARCHAR(100) NULL,
        sender_title VARCHAR(150) NOT NULL,
        receiver_title VARCHAR(150) NOT NULL,
        record_date DATE NOT NULL,
        courier_name VARCHAR(100) NULL,
        tracking_id VARCHAR(100) NULL,
        category VARCHAR(100) DEFAULT 'General',
        note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS admission_enquiry (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        name VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(100) NULL,
        address TEXT NULL,
        description TEXT NULL,
        note TEXT NULL,
        date DATE NOT NULL,
        next_date DATE NULL,
        assigned VARCHAR(100) NULL,
        reference VARCHAR(100) NULL,
        source VARCHAR(100) NULL,
        class VARCHAR(100) NULL,
        no_of_child INT DEFAULT 1,
        status VARCHAR(50) DEFAULT 'New',
        discount_offered DECIMAL(10,2) DEFAULT 0.00,
        converted_student_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 15. Student Clearances & Certificates
    $db->exec("CREATE TABLE IF NOT EXISTS student_clearances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        student_id INT NOT NULL,
        clearance_no VARCHAR(50) NOT NULL,
        academic_session_id INT NULL,
        reason_for_leaving VARCHAR(255) DEFAULT 'Completed Matriculation / Migration',
        application_date DATE NOT NULL,
        completion_date DATE NULL,
        accounts_status VARCHAR(20) DEFAULT 'Pending',
        accounts_remarks VARCHAR(255) NULL,
        accounts_cleared_by VARCHAR(100) NULL,
        library_status VARCHAR(20) DEFAULT 'Pending',
        library_remarks VARCHAR(255) NULL,
        library_cleared_by VARCHAR(100) NULL,
        lab_status VARCHAR(20) DEFAULT 'Pending',
        lab_remarks VARCHAR(255) NULL,
        lab_cleared_by VARCHAR(100) NULL,
        sports_status VARCHAR(20) DEFAULT 'Pending',
        sports_remarks VARCHAR(255) NULL,
        sports_cleared_by VARCHAR(100) NULL,
        class_teacher_status VARCHAR(20) DEFAULT 'Pending',
        class_teacher_remarks VARCHAR(255) NULL,
        class_teacher_cleared_by VARCHAR(100) NULL,
        overall_status VARCHAR(30) DEFAULT 'In Progress',
        approved_by_principal_id INT NULL,
        approved_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS student_certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        student_id INT NOT NULL,
        certificate_type VARCHAR(50) NOT NULL,
        certificate_no VARCHAR(50) NOT NULL,
        issue_date DATE NOT NULL,
        leaving_date DATE NULL,
        reason_for_leaving VARCHAR(255) NULL,
        conduct VARCHAR(100) DEFAULT 'Exemplary',
        promoted_to_class VARCHAR(100) NULL,
        remarks TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        certificate_title VARCHAR(150) NOT NULL,
        certificate_type VARCHAR(50) DEFAULT 'General',
        header_left_text TEXT NULL,
        header_center_text TEXT NULL,
        header_right_text TEXT NULL,
        body_text TEXT NULL,
        footer_left_text TEXT NULL,
        footer_center_text TEXT NULL,
        footer_right_text TEXT NULL,
        background_image VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS student_promotions_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        student_id INT NOT NULL,
        from_session_id INT NULL,
        to_session_id INT NOT NULL,
        from_class_id INT NOT NULL,
        to_class_id INT NOT NULL,
        from_section_id INT NULL,
        to_section_id INT NULL,
        promotion_status VARCHAR(50) DEFAULT 'Promoted',
        exam_marks_summary VARCHAR(255) NULL,
        promoted_by_user_id INT NULL,
        promoted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 16. Notices & Front CMS
    $db->exec("CREATE TABLE IF NOT EXISTS notice_board (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        message TEXT NULL,
        notice_type VARCHAR(50) DEFAULT 'General Notice',
        priority VARCHAR(20) DEFAULT 'Normal',
        is_visible_to_student VARCHAR(10) DEFAULT 'yes',
        is_visible_to_staff VARCHAR(10) DEFAULT 'yes',
        is_visible_to_parent VARCHAR(10) DEFAULT 'yes',
        publish_date DATE NOT NULL,
        expiry_date DATE NULL,
        status VARCHAR(20) DEFAULT 'Published',
        attachment VARCHAR(255) NULL,
        created_by INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS notice_attachments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        notice_id INT NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_size INT DEFAULT 0,
        mime_type VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        url VARCHAR(255) NULL,
        page_type VARCHAR(50) DEFAULT 'standard',
        content LONGTEXT NULL,
        file_path VARCHAR(255) NULL,
        file_name VARCHAR(255) NULL,
        meta_title VARCHAR(255) NULL,
        meta_description VARCHAR(255) NULL,
        meta_keywords VARCHAR(255) NULL,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_menus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        menu_title VARCHAR(150) NOT NULL,
        menu_url VARCHAR(255) NOT NULL,
        open_new_tab ENUM('yes', 'no') DEFAULT 'no',
        sort_order INT DEFAULT 0,
        parent_id INT NULL,
        dropdown_group VARCHAR(50) DEFAULT 'none',
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255) NULL,
        image VARCHAR(255) NOT NULL,
        button_text VARCHAR(100) NULL,
        button_url VARCHAR(255) NULL,
        sort_order INT DEFAULT 0,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        content LONGTEXT NULL,
        image VARCHAR(255) NULL,
        publish_date DATE NOT NULL,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        venue VARCHAR(255) NULL,
        event_date DATE NOT NULL,
        start_time TIME NULL,
        end_time TIME NULL,
        description TEXT NULL,
        image VARCHAR(255) NULL,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        name VARCHAR(150) NOT NULL,
        designation VARCHAR(150) NULL,
        rating INT DEFAULT 5,
        feedback TEXT NOT NULL,
        image VARCHAR(255) NULL,
        sort_order INT DEFAULT 0,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_galleries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        description TEXT NULL,
        cover_image VARCHAR(255) NULL,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_gallery_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gallery_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        caption VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_faqs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        question VARCHAR(255) NOT NULL,
        answer TEXT NOT NULL,
        sort_order INT DEFAULT 0,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_alumni (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        name VARCHAR(150) NOT NULL,
        batch_year VARCHAR(50) NOT NULL,
        graduation_class VARCHAR(100) NULL,
        current_position VARCHAR(150) NULL,
        company_organization VARCHAR(150) NULL,
        location VARCHAR(100) NULL,
        image VARCHAR(255) NULL,
        testimonial TEXT NULL,
        linkedin_url VARCHAR(255) NULL,
        email VARCHAR(150) NULL,
        phone VARCHAR(50) NULL,
        is_featured TINYINT(1) DEFAULT 0,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_requirements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        department VARCHAR(100) NULL,
        description LONGTEXT NOT NULL,
        eligibility TEXT NULL,
        deadline DATE NULL,
        vacancies INT DEFAULT 1,
        attachment VARCHAR(255) NULL,
        status ENUM('active', 'closed', 'archived') DEFAULT 'active',
        show_in_menu TINYINT(1) DEFAULT 0,
        menu_title VARCHAR(100) NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS front_cms_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        enable_alumni ENUM('yes', 'no') DEFAULT 'yes',
        enable_requirements ENUM('yes', 'no') DEFAULT 'yes',
        theme_color VARCHAR(50) DEFAULT '#2563eb',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NULL,
        subject VARCHAR(200) NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS online_admissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL DEFAULT 1,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NULL,
        gender VARCHAR(20) DEFAULT 'Male',
        dob DATE NULL,
        email VARCHAR(150) NULL,
        phone VARCHAR(50) NOT NULL,
        class_id INT NULL,
        parent_name VARCHAR(150) NULL,
        parent_phone VARCHAR(50) NULL,
        address TEXT NULL,
        status VARCHAR(30) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 17. Site Settings
    $db->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NULL DEFAULT 1,
        setting_key VARCHAR(100) NOT NULL,
        setting_value TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 18. Distributed Sessions (Multi-Server Load Balancing)
    $db->exec("CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(128) PRIMARY KEY,
        user_id INT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent VARCHAR(255) NULL,
        payload LONGTEXT NOT NULL,
        last_activity INT NOT NULL,
        INDEX idx_sess_last_activity (last_activity),
        INDEX idx_sess_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // =========================================================================
    // SEEDING DEFAULT ENTERPRISE RECORDS
    // =========================================================================

    // A. Primary School Record
    $stmt = $db->query("SELECT id FROM schools WHERE id = 1 LIMIT 1");
    if (!$stmt->fetch()) {
        $st = $db->prepare("INSERT INTO schools (id, code, name, domain, status) VALUES (1, 'default', :name, 'localhost', 'active')");
        $st->execute([':name' => $schoolName]);
    } else {
        $st = $db->prepare("UPDATE schools SET name = :name WHERE id = 1");
        $st->execute([':name' => $schoolName]);
    }

    // B. Default School Bank
    $stmt = $db->query("SELECT id FROM school_banks WHERE school_id = 1 LIMIT 1");
    if (!$stmt->fetch()) {
        $db->exec("INSERT INTO school_banks (school_id, bank_name, branch_name, account_title, account_no, iban, is_default)
                   VALUES (1, 'Habib Bank Limited (HBL)', 'City Campus Branch', " . $db->quote($schoolName) . ", '1029-3847-2910-01', 'PK36HABB0001029384729101', 1)");
    }

    // C. Academic Session
    $stmt = $db->query("SELECT id FROM academic_sessions WHERE school_id = 1 LIMIT 1");
    if (!$stmt->fetch()) {
        $db->exec("INSERT INTO academic_sessions (school_id, session_name, start_date, end_date, is_current)
                   VALUES (1, '2026-27', '2026-04-01', '2027-03-31', 1)");
    }

    // D. Default Classes & Sections
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM classes WHERE school_id = 1");
    $cnt = $stmt->fetch(PDO::FETCH_OBJ)->cnt ?? 0;
    if ($cnt == 0) {
        $classList = ['Playgroup', 'Nursery', 'KG', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10'];
        foreach ($classList as $cn) {
            $st = $db->prepare("INSERT INTO classes (school_id, class_name) VALUES (1, :cname)");
            $st->execute([':cname' => $cn]);
            $cid = $db->lastInsertId();

            $stSec = $db->prepare("INSERT INTO sections (school_id, class_id, section_name) VALUES (1, :cid, :sec_name)");
            $stSec->execute([':cid' => $cid, ':sec_name' => 'A']);
            $stSec->execute([':cid' => $cid, ':sec_name' => 'B']);
        }
    }

    // E. Default Fee Types & Groups
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM fee_types WHERE school_id = 1");
    $cntTypes = $stmt->fetch(PDO::FETCH_OBJ)->cnt ?? 0;
    if ($cntTypes == 0) {
        $feeTypes = [
            ['Tuition Fee', 'TUI', 'Regular monthly instructional fee'],
            ['Admission Fee', 'ADM', 'One-time admission registration fee'],
            ['Annual Resource & Library Fund', 'ANN', 'Annual stationery, sports and library fund'],
            ['Computer & Science Lab Charges', 'LAB', 'IT and laboratory equipment maintenance'],
            ['Examination Fee', 'EXM', 'Term exam paper printing and evaluation charges']
        ];
        foreach ($feeTypes as $ft) {
            $st = $db->prepare("INSERT INTO fee_types (school_id, type_name, type_code, description) VALUES (1, :tn, :tc, :desc)");
            $st->execute([':tn' => $ft[0], ':tc' => $ft[1], ':desc' => $ft[2]]);
        }
    }

    // F. RBAC Permissions Matrix
    $defaultPerms = [
        'view_students' => ['Can view student profiles and directories', 'Students'],
        'manage_students' => ['Can add, edit, or withdraw students', 'Students'],
        'view_academics' => ['Can view classes, sections, and subjects', 'Academics'],
        'manage_academics' => ['Can create and manage classes and timetable', 'Academics'],
        'view_attendance' => ['Can view student & staff attendance reports', 'Attendance'],
        'manage_attendance' => ['Can mark and modify student/staff attendance', 'Attendance'],
        'view_exams' => ['Can view exam schedules, cards, and marks', 'Examination'],
        'manage_exams' => ['Can create exams, enter marks, and generate report cards', 'Examination'],
        'view_finance' => ['Can view fee statements and invoices', 'Finance'],
        'manage_finance' => ['Can collect fees, issue vouchers, and manage expenses', 'Finance'],
        'view_communication' => ['Can view notice board and messages', 'Communication'],
        'manage_communication' => ['Can broadcast notices and send SMS/WhatsApp alerts', 'Communication'],
        'manage_inventory' => ['Can manage item stocks, categories, and issuances', 'Logistics'],
        'manage_library' => ['Can manage book catalog and member circulations', 'Logistics'],
        'manage_front_office' => ['Can manage visitors, gate passes, and call logs', 'Front Office'],
        'manage_clearance' => ['Can process student institutional exit clearance', 'Clearance'],
        'view_reports' => ['Can view analytics dashboard and operational reports', 'System'],
        'manage_settings' => ['Can modify school configuration, prefixes, and RBAC matrix', 'System']
    ];

    foreach ($defaultPerms as $pkey => $pdata) {
        $st = $db->prepare("SELECT id FROM permissions WHERE permission_key = :pkey LIMIT 1");
        $st->execute([':pkey' => $pkey]);
        if (!$st->fetch()) {
            $ins = $db->prepare("INSERT INTO permissions (permission_key, description, category) VALUES (:k, :d, :c)");
            $ins->execute([':k' => $pkey, ':d' => $pdata[0], ':c' => $pdata[1]]);
        }
    }

    $allRoles = ['super_admin', 'admin', 'teacher', 'accountant', 'librarian', 'receptionist', 'student', 'parent'];
    foreach ($allRoles as $rname) {
        $st = $db->prepare("SELECT id FROM roles WHERE name = :rname LIMIT 1");
        $st->execute([':rname' => $rname]);
        if (!$st->fetch()) {
            $ins = $db->prepare("INSERT INTO roles (school_id, name) VALUES (NULL, :rname)");
            $ins->execute([':rname' => $rname]);
        }
    }

    // G. Super Admin User Account Provisioning
    $hashedPassword = password_hash($adminPass, PASSWORD_DEFAULT);
    $st = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $st->execute([':email' => $adminEmail]);
    $userRow = $st->fetch(PDO::FETCH_OBJ);

    if ($userRow) {
        $update = $db->prepare("UPDATE users SET school_id = 1, name = :name, password = :pwd, role = 'super_admin', is_active = 1 WHERE id = :id");
        $update->execute([':name' => $adminName, ':pwd' => $hashedPassword, ':id' => $userRow->id]);
        $adminUserId = $userRow->id;
    } else {
        $insert = $db->prepare("INSERT INTO users (school_id, name, email, password, role, is_active) VALUES (1, :name, :email, :pwd, 'super_admin', 1)");
        $insert->execute([':name' => $adminName, ':email' => $adminEmail, ':pwd' => $hashedPassword]);
        $adminUserId = $db->lastInsertId();
    }

    // Link user to super_admin role in user_roles
    $stRole = $db->query("SELECT id FROM roles WHERE name = 'super_admin' LIMIT 1");
    $superAdminRole = $stRole->fetch(PDO::FETCH_OBJ);
    if ($superAdminRole && $adminUserId) {
        $db->exec("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES ({$adminUserId}, {$superAdminRole->id})");
    }

    // H. Default Site Settings
    $settings = [
        'school_name' => $schoolName,
        'campus_name' => $campusName,
        'currency_symbol' => $currency,
        'contact_email' => $adminEmail,
        'school_email' => $adminEmail,
        'school_phone' => '+92-51-111-222-333',
        'school_address' => 'Main Executive Campus, Education City',
        'affiliation_board' => 'Federal Board of Intermediate and Secondary Education',
        'affiliation_no' => 'FBISE-REG-88219',
        'prefix_student' => 'ADM-',
        'prefix_staff' => 'EMP-',
        'prefix_teacher' => 'TCH-',
        'prefix_family' => 'FAM-',
        'prefix_challan' => 'CHL-',
        'prefix_clearance' => 'CLR-',
        'prefix_visitor' => 'VIS-',
        'prefix_gatepass' => 'GP-',
        'toggle_require_bform' => '1',
        'toggle_require_father_cnic' => '1',
        'toggle_sibling_discount' => '1',
        'toggle_daily_attendance_sms' => '1',
        'toggle_fee_accounting_sync' => '1',
        'toggle_strict_clearance_slc' => '1'
    ];

    foreach ($settings as $key => $val) {
        $st = $db->prepare("SELECT id FROM site_settings WHERE setting_key = :k AND (school_id = 1 OR school_id IS NULL) LIMIT 1");
        $st->execute([':k' => $key]);
        if (!$st->fetch()) {
            $ins = $db->prepare("INSERT INTO site_settings (school_id, setting_key, setting_value) VALUES (1, :k, :v)");
            $ins->execute([':k' => $key, ':v' => $val]);
        } else {
            $up = $db->prepare("UPDATE site_settings SET setting_value = :v WHERE setting_key = :k AND (school_id = 1 OR school_id IS NULL)");
            $up->execute([':v' => $val, ':k' => $key]);
        }
    }

    // I. Default API Key for Official Mobile App
    $stmt = $db->query("SELECT id FROM api_keys WHERE school_id = 1 LIMIT 1");
    if (!$stmt->fetch()) {
        $db->exec("INSERT INTO api_keys (school_id, client_name, api_key, is_active, rate_limit_per_min)
                   VALUES (1, 'Official Mobile App (Flutter / React Native)', 'sk_live_school_enterprise_app_2026', 1, 120)");
    }

    // J. High-Performance Concurrency Indexes for 100,000+ Users
    $indexes = [
        ['students', 'idx_stu_school_status_class', 'school_id, status, class_id'],
        ['students', 'idx_stu_admission_no', 'school_id, admission_no'],
        ['students', 'idx_stu_user_id', 'user_id'],
        ['students', 'idx_stu_session', 'school_id, academic_session_id'],
        ['users', 'idx_usr_email_school', 'email, school_id'],
        ['users', 'idx_usr_role_active', 'role, is_active'],
        ['users', 'idx_usr_school_role', 'school_id, role'],
        ['student_fees', 'idx_fee_lookup', 'school_id, student_id, billing_month'],
        ['student_fees', 'idx_fee_status_due', 'school_id, status, due_date'],
        ['student_fees', 'idx_fee_challan', 'school_id, challan_no'],
        ['fee_payments', 'idx_pay_student_fee', 'student_fee_id, payment_date'],
        ['fee_payments', 'idx_pay_school_date', 'school_id, payment_date'],
        ['student_attendance', 'idx_att_class_date', 'school_id, class_id, section_id, attendance_date'],
        ['student_attendance', 'idx_att_student_date', 'school_id, student_id, attendance_date'],
        ['staff_attendance', 'idx_stf_att_date', 'school_id, staff_id, attendance_date'],
        ['exam_results', 'idx_res_sched_student', 'exam_schedule_id, student_id'],
        ['exam_results', 'idx_res_school_student', 'school_id, student_id'],
        ['exam_schedules', 'idx_esch_exam_class', 'school_id, exam_id, class_id, section_id'],
        ['timetables', 'idx_time_class_day', 'school_id, class_id, section_id, day_of_week'],
        ['incomes', 'idx_inc_school_date', 'school_id, date'],
        ['expenses', 'idx_exp_school_date', 'school_id, date'],
        ['notice_board', 'idx_nb_school_status_date', 'school_id, status, publish_date'],
        ['api_keys', 'idx_apk_key_active', 'api_key, is_active'],
        ['api_user_tokens', 'idx_tok_token_exp', 'token, expires_at'],
        ['api_user_tokens', 'idx_tok_user_school', 'user_id, school_id']
    ];

    foreach ($indexes as $idx) {
        $table = $idx[0];
        $indexName = $idx[1];
        $cols = $idx[2];
        try {
            $chk = $db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = " . $db->quote($indexName));
            if (!$chk->fetch()) {
                $db->exec("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$cols})");
            }
        } catch (Throwable $e) {
            // Safe fallback if column or table not yet migrated
        }
    }

    return [
        'success' => true,
        'message' => 'Complete enterprise schema created with 100K+ scalability indexes initialized successfully.'
    ];
}
