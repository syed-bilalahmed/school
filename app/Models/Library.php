<?php
class Library {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Book Management
    public function getBooks(){
        $this->db->query("SELECT * FROM books WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getBooksPaginated($limit, $offset){
        $this->db->query("SELECT * FROM books WHERE school_id = :school_id ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function countBooks(){
        $this->db->query("SELECT COUNT(*) as total FROM books WHERE school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }
    
    public function getBookById($id){
        $this->db->query("SELECT * FROM books WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addBook($data){
        $this->db->query("INSERT INTO books (school_id, book_title, book_no, isbn, author, publisher, rack_no, qty, price, post_date) 
                          VALUES (:school_id, :title, :no, :isbn, :auth, :pub, :rack, :qty, :price, :date)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['book_title']);
        $this->db->bind(':no', $data['book_no']);
        $this->db->bind(':isbn', $data['isbn']);
        $this->db->bind(':auth', $data['author']);
        $this->db->bind(':pub', $data['publisher']);
        $this->db->bind(':rack', $data['rack_no']);
        $this->db->bind(':qty', $data['qty']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':date', $data['post_date']);
        return $this->db->execute();
    }

    public function deleteBook($id){
        $this->db->query("DELETE FROM books WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Issue/Return
    public function getIssuedBooks($filterType = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT bi.*, b.book_title, b.book_no, b.author, b.isbn,
                       u.name as user_name, u.role, u.email,
                       s.admission_no, c.class_name, sec.section_name,
                       st.staff_code, st.department, st.designation
                FROM book_issues bi
                JOIN books b ON bi.book_id = b.id
                JOIN users u ON bi.user_id = u.id
                LEFT JOIN students s ON u.id = s.user_id AND s.school_id = :school_id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN sections sec ON s.section_id = sec.id
                LEFT JOIN staff st ON u.id = st.user_id AND st.school_id = :school_id
                WHERE bi.is_returned = 0 AND (bi.school_id = :school_id OR bi.school_id IS NULL)";
        
        if ($filterType === 'student') {
            $sql .= " AND bi.user_type = 'student'";
        } elseif ($filterType === 'faculty' || $filterType === 'staff') {
            $sql .= " AND bi.user_type IN ('faculty', 'staff', 'teacher')";
        }

        $sql .= " ORDER BY bi.issue_date DESC, bi.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    public function issueBook($data){
        // Check availability
        $book = $this->getBookById($data['book_id']);
        if($book->qty <= 0) return false;

        $this->db->query("INSERT INTO book_issues (school_id, book_id, user_id, user_type, issue_date, due_date) 
                          VALUES (:school_id, :bid, :uid, :utype, :idate, :ddate)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':bid', $data['book_id']);
        $this->db->bind(':uid', $data['user_id']);
        $this->db->bind(':utype', $data['user_type']);
        $this->db->bind(':idate', $data['issue_date']);
        $this->db->bind(':ddate', $data['due_date']);
        
        if($this->db->execute()){
            // Decrease Qty
            $this->db->query("UPDATE books SET qty = qty - 1 WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            $this->db->bind(':id', $data['book_id']);
            $this->db->execute();
            return true;
        }
        return false;
    }

    public function returnBook($id, $return_date, $fine = 0.00){
        // Get Issue details to increase Qty
        $this->db->query("SELECT book_id FROM book_issues WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $issue = $this->db->single();

        $this->db->query("UPDATE book_issues SET return_date = :rdate, fine = :fine, is_returned = 1 WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':rdate', $return_date);
        $this->db->bind(':fine', (float)$fine);
        $this->db->bind(':id', $id);
        
        if($this->db->execute()){
             $this->db->query("UPDATE books SET qty = qty + 1 WHERE id = :bid AND school_id = :school_id");
             $this->db->bind(':school_id', TenantContext::getSchoolId());
             $this->db->bind(':bid', $issue->book_id);
             $this->db->execute();
             return true;
        }
        return false;
    }
    
    // Get Students for Issue Book
    public function getStudentsForIssue(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT u.id as user_id, u.name, u.email, u.role, 
                                 s.id as student_id, s.admission_no, s.roll_no,
                                 c.class_name, sec.section_name
                          FROM users u
                          LEFT JOIN students s ON u.id = s.user_id AND s.school_id = :school_id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE u.role = 'student' AND (u.school_id = :school_id OR u.school_id IS NULL)
                          ORDER BY u.name ASC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    // Get Faculty / Staff for Issue Book
    public function getStaffForIssue(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT u.id as user_id, u.name, u.email, u.role,
                                 st.staff_code, st.department, st.designation
                          FROM users u
                          LEFT JOIN staff st ON u.id = st.user_id AND st.school_id = :school_id
                          WHERE u.role IN ('teacher', 'accountant', 'librarian', 'receptionist', 'admin', 'staff')
                            AND (u.school_id = :school_id OR u.school_id IS NULL)
                          ORDER BY u.name ASC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    // Search Members (Students/Staff) for Issue
    public function searchMembers($term){
        $this->db->query("SELECT id, name, role, email FROM users WHERE (name LIKE :t1 OR email LIKE :t2) AND (school_id = :school_id OR school_id IS NULL) LIMIT 20");
        $param = "%$term%";
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        $this->db->bind(':t1', $param);
        $this->db->bind(':t2', $param);
        return $this->db->resultSet();
    }
}

