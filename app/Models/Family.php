<?php
// app/Models/Family.php

class Family {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all families with student count
    public function getFamilies() {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT f.*, COUNT(s.id) as total_children
                          FROM families f
                          LEFT JOIN students s ON (s.family_id = f.family_code OR s.father_cnic = f.father_cnic) AND s.school_id = :school_id_join
                          WHERE f.school_id = :school_id
                          GROUP BY f.id, f.family_code, f.father_name, f.father_cnic, f.guardian_phone, f.default_discount_percent, f.notes, f.created_at
                          ORDER BY f.id DESC");
        $this->db->bind(':school_id_join', $schoolId);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    public function getFamilyById($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM families WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function getFamilyByCode($code) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM families WHERE family_code = :code AND school_id = :school_id LIMIT 1");
        $this->db->bind(':code', trim($code));
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function createFamily($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $code = !empty($data['family_code']) ? trim($data['family_code']) : $this->generateNextFamilyCode($schoolId);

        $this->db->query("INSERT INTO families (school_id, family_code, father_name, father_cnic, guardian_phone, default_discount_percent, notes) 
                          VALUES (:school_id, :code, :father_name, :father_cnic, :phone, :discount, :notes)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':code', $code);
        $this->db->bind(':father_name', trim($data['father_name'] ?? ''));
        $this->db->bind(':father_cnic', trim($data['father_cnic'] ?? ''));
        $this->db->bind(':phone', trim($data['guardian_phone'] ?? ''));
        $this->db->bind(':discount', !empty($data['default_discount_percent']) ? (float)$data['default_discount_percent'] : 10.00);
        $this->db->bind(':notes', trim($data['notes'] ?? ''));

        if ($this->db->execute()) {
            return $code;
        }
        return false;
    }

    public function updateFamily($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("UPDATE families 
                          SET father_name = :father_name, father_cnic = :father_cnic, guardian_phone = :phone, 
                              default_discount_percent = :discount, notes = :notes 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':father_name', trim($data['father_name'] ?? ''));
        $this->db->bind(':father_cnic', trim($data['father_cnic'] ?? ''));
        $this->db->bind(':phone', trim($data['guardian_phone'] ?? ''));
        $this->db->bind(':discount', !empty($data['default_discount_percent']) ? (float)$data['default_discount_percent'] : 10.00);
        $this->db->bind(':notes', trim($data['notes'] ?? ''));
        $this->db->bind(':id', (int)$data['id']);
        $this->db->bind(':school_id', $schoolId);

        return $this->db->execute();
    }

    public function deleteFamily($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM families WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Get all enrolled children / siblings belonging to this family code
    public function getChildrenByFamilyCode($family_code) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.school_id = :school_id AND (s.family_id = :code OR s.father_cnic = (SELECT father_cnic FROM families WHERE family_code = :code2 AND school_id = :school_id2 LIMIT 1))
                          ORDER BY s.admission_date ASC, s.id ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':code', trim($family_code));
        $this->db->bind(':code2', trim($family_code));
        $this->db->bind(':school_id2', $schoolId);
        return $this->db->resultSet();
    }

    // Auto-detect or link family by Father CNIC
    public function findOrCreateFamilyByCnic($father_cnic, $father_name, $phone) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $cnic = trim($father_cnic);
        if (empty($cnic)) {
            return null;
        }

        $this->db->query("SELECT family_code FROM families WHERE father_cnic = :cnic AND school_id = :school_id LIMIT 1");
        $this->db->bind(':cnic', $cnic);
        $this->db->bind(':school_id', $schoolId);
        $existing = $this->db->single();
        if ($existing) {
            return $existing->family_code;
        }

        // Auto create family code
        $familyCode = $this->generateNextFamilyCode($schoolId);
        $this->createFamily([
            'family_code' => $familyCode,
            'father_name' => $father_name,
            'father_cnic' => $cnic,
            'guardian_phone' => $phone,
            'default_discount_percent' => 10.00,
            'notes' => 'Auto-created from admission father CNIC'
        ]);
        return $familyCode;
    }

    // Auto-apply Sibling Discount across children in a family
    public function autoApplySiblingDiscount($family_code) {
        $children = $this->getChildrenByFamilyCode($family_code);
        $count = count($children);
        if ($count <= 1) {
            return;
        }

        $family = $this->getFamilyByCode($family_code);
        $defaultDisc = $family ? (float)$family->default_discount_percent : 10.00;

        // Policy: 1st child 0%, 2nd child 10%, 3rd child 20%
        $schoolId = TenantContext::getSchoolId() ?: 1;
        foreach ($children as $index => $child) {
            $discount = 0.00;
            if ($index == 1) {
                $discount = $defaultDisc; // 2nd child
            } elseif ($index >= 2) {
                $discount = min($defaultDisc * 2, 50.00); // 3rd child and beyond
            }

            $this->db->query("UPDATE students 
                              SET family_id = :code, sibling_discount_percent = :disc, 
                                  concession_type = CASE WHEN :disc_cond > 0 THEN 'Sibling Discount' ELSE concession_type END 
                              WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':code', trim($family_code));
            $this->db->bind(':disc', $discount);
            $this->db->bind(':disc_cond', $discount);
            $this->db->bind(':id', $child->id);
            $this->db->bind(':school_id', $schoolId);
            $this->db->execute();
        }
    }

    private function generateNextFamilyCode($schoolId) {
        $this->db->query("SELECT MAX(id) as max_id FROM families WHERE school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $res = $this->db->single();
        $next = ($res && $res->max_id) ? ((int)$res->max_id + 1) : 1;
        return 'FAM-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
