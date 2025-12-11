<?php
/**
 * User Model & Functions
 * perpustakaan-php
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAllUsers() {
        $query = "SELECT user_id, username, level FROM user ORDER BY user_id DESC";
        $result = $this->db->query($query);
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function createUser($username, $password, $level) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO user (username, password, level) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $hashedPassword, $level);
        
        return $stmt->execute();
    }
    
    public function updateUser($id, $username, $level, $newPassword = null) {
        if ($newPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE user SET username = ?, password = ?, level = ? WHERE user_id = ?");
            $stmt->bind_param("ssii", $username, $hashedPassword, $level, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE user SET username = ?, level = ? WHERE user_id = ?");
            $stmt->bind_param("sii", $username, $level, $id);
        }
        
        return $stmt->execute();
    }
    
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function getUserCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM user");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    public function getMemberCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM user WHERE level = 0");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
}


