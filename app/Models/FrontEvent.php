<?php
class FrontEvent {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getEvents(){
        $this->db->query("SELECT * FROM front_events WHERE school_id = :school_id ORDER BY start_date DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getEventById($id){
        $this->db->query("SELECT * FROM front_events WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addEvent($data){
        $this->db->query("INSERT INTO front_events (school_id, title, description, start_date, venue, image) VALUES (:school_id, :title, :desc, :start, :venue, :image)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':start', $data['start_date']);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':image', $data['image']);
        return $this->db->execute();
    }

    public function updateEvent($data){
        $this->db->query("UPDATE front_events SET title = :title, description = :desc, start_date = :start, venue = :venue, image = :image WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':start', $data['start_date']);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':image', $data['image']);
        return $this->db->execute();
    }

    public function deleteEvent($id){
        $this->db->query("DELETE FROM front_events WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
