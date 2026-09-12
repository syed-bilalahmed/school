<?php
class Inventory {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // --- Masters (Category, Store, Supplier) ---
    public function getCategories(){
        $this->db->query("SELECT * FROM item_category WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    public function addCategory($data){
        $this->db->query("INSERT INTO item_category (school_id, item_category, description) VALUES (:school_id, :name, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['item_category']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }
    public function getStores(){
        $this->db->query("SELECT * FROM item_store WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    public function addStore($data){
        $this->db->query("INSERT INTO item_store (school_id, item_store, code, description) VALUES (:school_id, :name, :code, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['item_store']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }
    public function getSuppliers(){
        $this->db->query("SELECT * FROM item_supplier WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    public function addSupplier($data){
        $this->db->query("INSERT INTO item_supplier (school_id, item_supplier, phone, email, address, contact_person_name) VALUES (:school_id, :name, :phone, :email, :addr, :person)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['item_supplier']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':addr', $data['address']);
        $this->db->bind(':person', $data['contact_person_name']);
        return $this->db->execute();
    }

    // --- Items ---
    public function getItems(){
        $this->db->query("SELECT i.*, c.item_category FROM items i LEFT JOIN item_category c ON i.item_category_id = c.id WHERE i.school_id = :school_id ORDER BY i.id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    public function addItem($data){
        $this->db->query("INSERT INTO items (school_id, item_category_id, name, unit, description) VALUES (:school_id, :cid, :name, :unit, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':cid', $data['item_category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':unit', $data['unit']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }

    // --- Stock ---
    // Note: In a real system, stock would update a 'total_quantity' in items table or sum dynamically. 
    // We will calculate available stock dynamically for simplicity.
    public function addStock($data){
        $this->db->query("INSERT INTO item_stock (school_id, item_id, supplier_id, store_id, quantity, date, attachment, description) VALUES (:school_id, :iid, :supid, :stid, :qty, :date, :att, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':iid', $data['item_id']);
        $this->db->bind(':supid', $data['supplier_id']);
        $this->db->bind(':stid', $data['store_id']);
        $this->db->bind(':qty', $data['quantity']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':att', $data['attachment']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }
    
    public function getStockList(){
        $this->db->query("SELECT st.*, i.name as item_name, i.unit, c.item_category, sup.item_supplier, sto.item_store 
                          FROM item_stock st 
                          JOIN items i ON st.item_id = i.id 
                          LEFT JOIN item_category c ON i.item_category_id = c.id
                          LEFT JOIN item_supplier sup ON st.supplier_id = sup.id
                          LEFT JOIN item_store sto ON st.store_id = sto.id
                          WHERE st.school_id = :school_id
                          ORDER BY st.date DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    
    // Calculate total stock for an item
    public function getAvailableStock($item_id){
        // Sum additions
        $this->db->query("SELECT SUM(quantity) as total_added FROM item_stock WHERE item_id = :iid AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':iid', $item_id);
        $added = $this->db->single()->total_added ?? 0;
        
        // Sum issues
        $this->db->query("SELECT SUM(quantity) as total_issued FROM item_issue WHERE item_id = :iid AND is_returned = 0 AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':iid', $item_id);
        $issued = $this->db->single()->total_issued ?? 0;
        
        return $added - $issued;
    }

    public function issueItem($data){
        // Check availability
        $available = $this->getAvailableStock($data['item_id']);
        if($available < $data['quantity']) return false;

        $this->db->query("INSERT INTO item_issue (school_id, issue_type, issue_to, issue_by, issue_date, return_date, note, item_category_id, item_id, quantity) 
                          VALUES (:school_id, :type, :to, :by, :date, :rdate, :note, :cat, :item, :qty)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':type', $data['issue_type']);
        $this->db->bind(':to', $data['issue_to']);
        $this->db->bind(':by', $data['issue_by']);
        $this->db->bind(':date', $data['issue_date']);
        $this->db->bind(':rdate', $data['return_date']);
        $this->db->bind(':note', $data['note']);
        $this->db->bind(':cat', $data['item_category_id']);
        $this->db->bind(':item', $data['item_id']);
        $this->db->bind(':qty', $data['quantity']);
        return $this->db->execute();
    }
    
    public function getIssuedItems(){
         $this->db->query("SELECT iss.*, i.name as item_name, c.item_category, u.name as user_name, u.role as user_role 
                          FROM item_issue iss 
                          JOIN items i ON iss.item_id = i.id 
                          LEFT JOIN item_category c ON i.item_category_id = c.id
                          LEFT JOIN users u ON iss.issue_to = u.id
                          WHERE iss.school_id = :school_id
                          ORDER BY iss.issue_date DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
    
    public function returnItem($id){
        $this->db->query("UPDATE item_issue SET is_returned = 1 WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
