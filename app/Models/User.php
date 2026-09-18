<?php
class User {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Find user by email
    public function findUserByEmail($email){
        $tenantAware = TenantContext::isEstablished();
        if ($tenantAware) {
            $this->db->query('SELECT * FROM users WHERE email = :email AND (school_id = :school_id OR role = :super_admin)');
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            $this->db->bind(':super_admin', 'super_admin');
        } else {
            $this->db->query('SELECT * FROM users WHERE email = :email');
        }
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($this->db->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }

    public function getUserByEmail($email){
        if (TenantContext::isEstablished()) {
            $this->db->query("SELECT * FROM users WHERE email = :email AND (school_id = :school_id OR role = :super_admin)");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            $this->db->bind(':super_admin', 'super_admin');
        } else {
            $this->db->query("SELECT * FROM users WHERE email = :email");
        }
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    // Login User
    public function login($email, $password){
        if (TenantContext::isEstablished()) {
            $this->db->query('SELECT * FROM users WHERE email = :email AND (school_id = :school_id OR role = :super_admin)');
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            $this->db->bind(':super_admin', 'super_admin');
        } else {
            $this->db->query('SELECT * FROM users WHERE email = :email');
        }
        $this->db->bind(':email', $email);

        $row = $this->db->single();
        
        if($row){
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)){
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function ensureUserProfileColumns() {
        try {
            $this->db->query("SHOW COLUMNS FROM users LIKE 'phone'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE users ADD COLUMN phone VARCHAR(50) NULL AFTER email");
                $this->db->execute();
            }
        } catch (Throwable $e) {}

        try {
            $this->db->query("SHOW COLUMNS FROM users LIKE 'avatar'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER role");
                $this->db->execute();
            }
        } catch (Throwable $e) {}

        try {
            $this->db->query("SHOW COLUMNS FROM users LIKE 'bio'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER avatar");
                $this->db->execute();
            }
        } catch (Throwable $e) {}

        try {
            $this->db->query("SHOW COLUMNS FROM users LIKE 'address'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE users ADD COLUMN address TEXT NULL AFTER bio");
                $this->db->execute();
            }
        } catch (Throwable $e) {}
    }

    public function getUserById($id){
        $this->ensureUserProfileColumns();
        $this->db->query("SELECT * FROM users WHERE id = :id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        return $this->db->single();
    }

    public function updateProfile($data){
        $this->ensureUserProfileColumns();

        $userId = (int)$data['id'];
        $name = trim($data['name']);
        $email = trim($data['email']);
        $phone = trim($data['phone'] ?? '');
        $bio = trim($data['bio'] ?? '');
        $address = trim($data['address'] ?? '');
        $avatar = $data['avatar'] ?? null;

        $fields = [
            'name = :name',
            'email = :email',
            'phone = :phone',
            'bio = :bio',
            'address = :address'
        ];
        if (!empty($avatar)) {
            $fields[] = 'avatar = :avatar';
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':bio', $bio);
        $this->db->bind(':address', $address);
        if (!empty($avatar)) {
            $this->db->bind(':avatar', $avatar);
        }
        $this->db->bind(':id', $userId);
        $res = $this->db->execute();

        if ($res) {
            // Synchronize name & phone with staff record if exists
            try {
                $staffSql = "UPDATE staff SET name = :name, phone = :phone, address = :address WHERE user_id = :uid";
                $this->db->query($staffSql);
                $this->db->bind(':name', $name);
                $this->db->bind(':phone', $phone);
                $this->db->bind(':address', $address);
                $this->db->bind(':uid', $userId);
                $this->db->execute();
            } catch (Throwable $e) {}

            // Synchronize name & email with students record if exists
            try {
                $studentSql = "UPDATE students SET name = :name, email = :email WHERE user_id = :uid";
                $this->db->query($studentSql);
                $this->db->bind(':name', $name);
                $this->db->bind(':email', $email);
                $this->db->bind(':uid', $userId);
                $this->db->execute();
            } catch (Throwable $e) {}
        }

        return $res;
    }

    public function changePassword($id, $new_password){
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = :pass WHERE id = :id");
        $this->db->bind(':pass', $hashed);
        $this->db->bind(':id', (int)$id);
        return $this->db->execute();
    }

    public function createPasswordResetToken($userId, $tokenHash, $pinHash, $expiresAt = null){
        $this->db->query('DELETE FROM password_resets WHERE user_id = :user_id OR expires_at < NOW()');
        $this->db->bind(':user_id', (int)$userId);
        $this->db->execute();

        // Use the database clock so PHP/MySQL timezone differences cannot expire fresh links.
        $this->db->query('INSERT INTO password_resets (user_id, token_hash, pin_hash, expires_at) VALUES (:user_id, :token_hash, :pin_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))');
        $this->db->bind(':user_id', (int)$userId);
        $this->db->bind(':token_hash', $tokenHash);
        $this->db->bind(':pin_hash', $pinHash);
        return $this->db->execute();
    }

    public function getUserByPasswordResetToken($tokenHash){
        $this->db->query('SELECT u.*, pr.token_hash, pr.pin_hash FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token_hash = :token_hash AND pr.expires_at > NOW() LIMIT 1');
        $this->db->bind(':token_hash', $tokenHash);
        return $this->db->single();
    }

    public function verifyPasswordResetPin($tokenHash, $pin){
        $this->db->query('SELECT pin_hash FROM password_resets WHERE token_hash = :token_hash AND expires_at > NOW() LIMIT 1');
        $this->db->bind(':token_hash', $tokenHash);
        $row = $this->db->single();
        return $row && !empty($row->pin_hash) && password_verify($pin, $row->pin_hash);
    }

    public function deletePasswordResetToken($tokenHash){
        $this->db->query('DELETE FROM password_resets WHERE token_hash = :token_hash');
        $this->db->bind(':token_hash', $tokenHash);
        return $this->db->execute();
    }

    // Get users by role
    public function getUsersByRole($role){
        if (TenantContext::isEstablished()) {
            $this->db->query("SELECT * FROM users WHERE role = :role AND school_id = :school_id");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
        } else {
            $this->db->query("SELECT * FROM users WHERE role = :role");
        }
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }

    public function countUsersByRole($role){
        if (TenantContext::isEstablished()) {
            $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = :role AND school_id = :school_id");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
        } else {
            $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = :role");
        }
        $this->db->bind(':role', $role);
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }

    // Register User (reusable)
    public function register($data){
        $this->db->query('INSERT INTO users (school_id, name, email, password, role) VALUES(:school_id, :name, :email, :password, :role)');
        $this->db->bind(':school_id', $data['school_id'] ?? TenantContext::getSchoolId());
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getUserPermissions($user_id) {
        // Simple initial implementation:
        // Assume users have a primary role in `users` table, which maps to the `roles` table.
        // For a more complex setup where a user has multiple roles via `user_roles`, query that table.
        // Given our seeder uses global role names (like 'admin', 'teacher'), we join on those.
        
        $this->db->query("
            SELECT DISTINCT p.permission_key 
            FROM users u
            JOIN roles r ON u.role = r.name AND (r.school_id IS NULL OR r.school_id = u.school_id)
            JOIN role_permissions rp ON r.id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE u.id = :user_id
        ");
        $this->db->bind(':user_id', $user_id);
        
        $results = $this->db->resultSet();
        $permissions = [];
        if ($results) {
            foreach ($results as $row) {
                $permissions[] = $row->permission_key;
            }
        }
        return $permissions;
    }

    // Get all system users with search, role and tenant filter
    public function getAllUsers($search = null, $role = null){
        $sql = "SELECT u.*, s.name as school_name 
                FROM users u 
                LEFT JOIN schools s ON u.school_id = s.id 
                WHERE 1=1";
        
        $params = [];
        if (TenantContext::isEstablished() && (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin')) {
            $sql .= " AND (u.school_id = :school_id OR u.role = 'super_admin')";
            $params[':school_id'] = TenantContext::getSchoolId();
        }

        if (!empty($role)) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $role;
        }

        if (!empty($search)) {
            $sql .= " AND (u.name LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY u.id DESC";

        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }
        return $this->db->resultSet() ?: [];
    }

    // Delete user
    public function deleteUser($id){
        $id = (int)$id;
        if ($id <= 1) {
            return false;
        }
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Check if email is already registered by another user account
    public function isEmailTakenByOtherUser($email, $userId) {
        $this->db->query("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $this->db->bind(':email', trim($email));
        $this->db->bind(':id', (int)$userId);
        $row = $this->db->single();
        return !empty($row);
    }

    // Comprehensive administrator profile and credentials update
    public function updateUserByAdmin($id, $data) {
        $id = (int)$id;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = trim($data['role'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($name) || empty($email)) {
            return ['success' => false, 'message' => 'User name and login email are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please provide a valid email address format.'];
        }

        if ($this->isEmailTakenByOtherUser($email, $id)) {
            return ['success' => false, 'message' => 'The email address "' . $email . '" is already assigned to another user account.'];
        }

        $existingUser = $this->getUserById($id);
        if (!$existingUser) {
            return ['success' => false, 'message' => 'User account could not be found.'];
        }

        // Primary Super Admin (id = 1) cannot be demoted from admin/super_admin
        if ($id === 1 && !in_array($role, ['admin', 'super_admin'])) {
            $role = $existingUser->role;
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters long.'];
            }
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $this->db->query("UPDATE users SET name = :name, email = :email, role = :role, password = :pass WHERE id = :id");
            $this->db->bind(':pass', $hashed);
        } else {
            $this->db->query("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
        }

        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':role', $role);
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            // Synchronize related tables
            try {
                // If student, sync name and email
                $this->db->query("UPDATE students SET name = :name, email = :email WHERE user_id = :uid");
                $this->db->bind(':name', $name);
                $this->db->bind(':email', $email);
                $this->db->bind(':uid', $id);
                $this->db->execute();
            } catch (Exception $e) {}

            try {
                // If staff, sync name
                $this->db->query("UPDATE staff SET name = :name WHERE user_id = :uid");
                $this->db->bind(':name', $name);
                $this->db->bind(':uid', $id);
                $this->db->execute();
            } catch (Exception $e) {}

            return ['success' => true, 'message' => "User account for '{$name}' was successfully updated."];
        }

        return ['success' => false, 'message' => 'Failed to save changes to the database.'];
    }

    /**
     * Generate random cryptographically secure password
     */
    public static function generateSecurePassword($length = 9) {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%&*';
        $pass = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $pass .= $chars[random_int(0, $max)];
        }
        return $pass;
    }

    /**
     * Administrator User Creation with auto-generated secure password
     */
    public function createUserByAdmin($data) {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = trim($data['role'] ?? 'teacher');
        $password = trim($data['password'] ?? '');

        if (empty($name) || empty($email)) {
            return ['success' => false, 'message' => 'Full name and email address are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }

        if ($this->findUserByEmail($email)) {
            return ['success' => false, 'message' => "A user account with email '{$email}' already exists."];
        }

        if (empty($password)) {
            $password = self::generateSecurePassword();
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters long.'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $schoolId = TenantContext::getSchoolId() ?: 1;

        $this->db->query("INSERT INTO users (school_id, name, email, password, role) VALUES (:school_id, :name, :email, :password, :role)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $hashed);
        $this->db->bind(':role', $role);

        if ($this->db->execute()) {
            $newUserId = $this->db->lastInsertId();
            return [
                'success' => true,
                'user_id' => $newUserId,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'plain_password' => $password,
                'message' => "User account for '{$name}' created successfully."
            ];
        }

        return ['success' => false, 'message' => 'Database error: failed to create user.'];
    }

    /**
     * Ensure non-super-admin demo accounts exist for 1-click role logins.
     * Strictly excludes super_admin as requested.
     */
    public function ensureRoleDemoAccounts() {
        // Ensure users table role column supports standard string names
        try {
            $this->db->query("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL");
            $this->db->execute();
        } catch (Throwable $t) {
            // Ignore if already modified or no permission
        }

        $schoolId = 1;
        if (class_exists('TenantContext') && TenantContext::getSchoolId()) {
            $schoolId = (int)TenantContext::getSchoolId();
        }

        $rolesConfig = [
            'admin' => [
                'role' => 'admin',
                'name' => 'School Administrator',
                'email' => 'admin@school.com',
                'password' => '123456',
                'icon' => 'fa-shield-halved',
                'label' => 'School Admin',
                'sub' => 'Principal / School Lead',
                'color' => '#2563eb'
            ],
            'teacher' => [
                'role' => 'teacher',
                'name' => 'Senior Teacher',
                'email' => 'teacher@school.com',
                'password' => '123456',
                'icon' => 'fa-chalkboard-user',
                'label' => 'Teacher',
                'sub' => 'Academics & Attendance',
                'color' => '#059669'
            ],
            'student' => [
                'role' => 'student',
                'name' => 'Hamza Ali (Student)',
                'email' => 'student@school.com',
                'password' => '123456',
                'icon' => 'fa-user-graduate',
                'label' => 'Student',
                'sub' => 'Classroom & Homework',
                'color' => '#0284c7'
            ],
            'parent' => [
                'role' => 'parent',
                'name' => 'Tariq Mehmood (Parent)',
                'email' => 'parent@school.com',
                'password' => '123456',
                'icon' => 'fa-people-roof',
                'label' => 'Parent',
                'sub' => 'Children Progress & Dues',
                'color' => '#d97706'
            ],
            'accountant' => [
                'role' => 'accountant',
                'name' => 'Chief Accountant',
                'email' => 'accountant@school.com',
                'password' => '123456',
                'icon' => 'fa-file-invoice-dollar',
                'label' => 'Accountant',
                'sub' => 'Fee Collection & Expenses',
                'color' => '#0f766e'
            ],
            'receptionist' => [
                'role' => 'receptionist',
                'name' => 'Front Office Desk',
                'email' => 'receptionist@school.com',
                'password' => '123456',
                'icon' => 'fa-headset',
                'label' => 'Receptionist',
                'sub' => 'Visitor Book & Inquiries',
                'color' => '#e11d48'
            ],
            'librarian' => [
                'role' => 'librarian',
                'name' => 'Lead Librarian',
                'email' => 'librarian@school.com',
                'password' => '123456',
                'icon' => 'fa-book-bookmark',
                'label' => 'Librarian',
                'sub' => 'Book Catalog & Issues',
                'color' => '#7c3aed'
            ]
        ];

        // Ensure user accounts exist in DB
        foreach ($rolesConfig as $rKey => $info) {
            try {
                $this->db->query("SELECT id FROM users WHERE email = :email LIMIT 1");
                $this->db->bind(':email', $info['email']);
                $existing = $this->db->single();

                if (!$existing) {
                    $hashed = password_hash($info['password'], PASSWORD_DEFAULT);
                    $this->db->query("INSERT INTO users (school_id, name, email, password, role) VALUES (:school_id, :name, :email, :password, :role)");
                    $this->db->bind(':school_id', $schoolId);
                    $this->db->bind(':name', $info['name']);
                    $this->db->bind(':email', $info['email']);
                    $this->db->bind(':password', $hashed);
                    $this->db->bind(':role', $rKey);
                    $this->db->execute();
                    $targetUserId = $this->db->lastInsertId();
                } else {
                    $hashed = password_hash($info['password'], PASSWORD_DEFAULT);
                    $this->db->query("UPDATE users SET password = :password, role = :role, school_id = COALESCE(school_id, :school_id) WHERE id = :id");
                    $this->db->bind(':password', $hashed);
                    $this->db->bind(':role', $rKey);
                    $this->db->bind(':school_id', $schoolId);
                    $this->db->bind(':id', $existing->id);
                    $this->db->execute();
                    $targetUserId = $existing->id;
                }

                if ($targetUserId) {
                    // Sync user_roles table
                    $this->db->query("SELECT id FROM roles WHERE name = :rname LIMIT 1");
                    $this->db->bind(':rname', $rKey);
                    $rObj = $this->db->single();
                    if ($rObj) {
                        $this->db->query("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
                        $this->db->bind(':user_id', $targetUserId);
                        $this->db->bind(':role_id', $rObj->id);
                        $this->db->execute();
                    }
                }
            } catch (Throwable $e) {
                error_log('[ensureRoleDemoAccounts] ' . $e->getMessage());
            }
        }

        // Ensure all existing user accounts in the database have a valid school_id
        try {
            $this->db->query("UPDATE users SET school_id = 1 WHERE school_id IS NULL OR school_id = 0");
            $this->db->execute();
        } catch (Throwable $e) {}

        return $rolesConfig;
    }
}
