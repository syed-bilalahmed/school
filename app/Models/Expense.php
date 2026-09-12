<?php
class Expense {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getHeads(){
        $this->db->query("SELECT * FROM expense_heads WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    
    public function addHead($data){
        $this->db->query("INSERT INTO expense_heads (school_id, exp_category, description) VALUES (:school_id, :cat, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':cat', $data['exp_category']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }
    
    public function deleteHead($id){
        $this->db->query("DELETE FROM expense_heads WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getExpenses($search_text = ""){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT e.*, eh.exp_category 
                FROM expenses e 
                LEFT JOIN expense_heads eh ON e.exp_head_id = eh.id 
                WHERE e.school_id = :school_id ";
        
        if(!empty($search_text)){
            $sql .= "AND (e.name LIKE :s1 OR e.invoice_no LIKE :s2 OR eh.exp_category LIKE :s3) ";
        }
        
        $sql .= "ORDER BY e.date DESC";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($search_text)){
            $term = "%$search_text%";
            $this->db->bind(':s1', $term);
            $this->db->bind(':s2', $term);
            $this->db->bind(':s3', $term);
        }
        return $this->db->resultSet();
    }

    public function getExpensesPaginated($search_text = "", $limit = 25, $offset = 0){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT e.*, eh.exp_category
                FROM expenses e
                LEFT JOIN expense_heads eh ON e.exp_head_id = eh.id 
                WHERE e.school_id = :school_id ";

        if(!empty($search_text)){
            $sql .= "AND (e.name LIKE :s1 OR e.invoice_no LIKE :s2 OR eh.exp_category LIKE :s3) ";
        }

        $sql .= "ORDER BY e.date DESC LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($search_text)){
            $term = "%$search_text%";
            $this->db->bind(':s1', $term);
            $this->db->bind(':s2', $term);
            $this->db->bind(':s3', $term);
        }
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function countExpenses($search_text = ""){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT COUNT(*) as total
                FROM expenses e
                LEFT JOIN expense_heads eh ON e.exp_head_id = eh.id 
                WHERE e.school_id = :school_id ";

        if(!empty($search_text)){
            $sql .= "AND (e.name LIKE :s1 OR e.invoice_no LIKE :s2 OR eh.exp_category LIKE :s3) ";
        }

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($search_text)){
            $term = "%$search_text%";
            $this->db->bind(':s1', $term);
            $this->db->bind(':s2', $term);
            $this->db->bind(':s3', $term);
        }

        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }

    public function addExpense($data){
        $this->db->query("INSERT INTO expenses (school_id, exp_head_id, name, invoice_no, date, amount, description, documents) 
                          VALUES (:school_id, :hid, :name, :inv, :date, :amt, :desc, :doc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':hid', $data['exp_head_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':inv', $data['invoice_no']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':amt', $data['amount']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':doc', $data['documents']);
        return $this->db->execute();
    }

    public function deleteExpense($id){
        $this->db->query("DELETE FROM expenses WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
