<?php
class School {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllSchools(){
        $this->db->query("SELECT s.*, sp.name AS plan_name
                          FROM schools s
                          LEFT JOIN school_plans sp ON sp.id = s.plan_id
                          ORDER BY s.created_at DESC");
        return $this->db->resultSet();
    }

    public function findByCode($code){
        $this->db->query("SELECT * FROM schools WHERE code = :code LIMIT 1");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function createSchool($data){
        $this->db->query("INSERT INTO schools (code, name, domain, status, plan_id)
                          VALUES (:code, :name, :domain, :status, :plan_id)");
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':domain', $data['domain'] ?: null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':plan_id', !empty($data['plan_id']) ? (int)$data['plan_id'] : null);
        return $this->db->execute();
    }

    public function updateStatus($id, $status){
        $this->db->query("UPDATE schools SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', (int)$id);
        return $this->db->execute();
    }

    public function countSchools(){
        $this->db->query("SELECT COUNT(*) AS total FROM schools");
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }

    public function existsByCode($code){
        return (bool)$this->findByCode($code);
    }

    public function existsByDomain($domain){
        if ($domain === null || $domain === '') {
            return false;
        }

        $this->db->query("SELECT id FROM schools WHERE domain = :domain LIMIT 1");
        $this->db->bind(':domain', $domain);
        return (bool)$this->db->single();
    }

    public function findById($id){
        $this->db->query("SELECT * FROM schools WHERE id = :id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        return $this->db->single();
    }

    public function deleteSchool($id){
        $id = (int)$id;
        if ($id <= 1) {
            return false; // Protect primary default school from deletion
        }

        try {
            // Delete dependent site_settings
            $this->db->query("DELETE FROM site_settings WHERE school_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // Delete school record (roles, subscriptions cascade via FK)
            $this->db->query("DELETE FROM schools WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Failed to delete school ID $id: " . $e->getMessage());
            return false;
        }
    }
}
