<?php

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll() {
        $sql = "SELECT id, username, email, full_name, role, is_active, created_at, updated_at 
                FROM users 
                ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        $users = [];
        
        if ($result && $result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $users[] = $row;
            }
        }
        
        return $users;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function authenticate($username, $password) {
        $user = $this->getByUsername($username);
        
        if (!empty($user) && isset($user['is_active']) && $user['is_active'] == 1) {
            if (password_verify($password, $user['password'])) {
                // Remove password from returned user data
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }

    public function create($username, $email, $password, $fullName = '', $role = 'editor') {
        // Check if username or email already exists
        if (!empty($this->getByUsername($username))) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        if (!empty($this->getByEmail($email))) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, full_name, role) 
                VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$username, $email, $hashedPassword, $fullName, $role])) {
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Failed to create user'];
    }

    public function update($id, $username, $email, $fullName = '', $role = 'editor', $password = null) {
        // Check if username or email already exists for other users
        $existingUser = $this->getByUsername($username);
        if (!empty($existingUser) && isset($existingUser['id']) && $existingUser['id'] != $id) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        $existingEmail = $this->getByEmail($email);
        if (!empty($existingEmail) && isset($existingEmail['id']) && $existingEmail['id'] != $id) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        if ($password !== null && !empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ?, 
                    full_name = ?, role = ?, password = ? 
                    WHERE id = ?");
            $result = $stmt->execute([$username, $email, $fullName, $role, $hashedPassword, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ?, 
                    full_name = ?, role = ? 
                    WHERE id = ?");
            $result = $stmt->execute([$username, $email, $fullName, $role, $id]);
        }
        
        if ($result) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to update user'];
    }

    public function updateStatus($id, $isActive) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        return $stmt->execute([(int)$isActive, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
}

