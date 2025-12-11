<?php
/**
 * Database Connection
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaan-php');
define('DB_PORT', 3306);

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            
            if ($this->connection->connect_error) {
                die("Database connection failed. Please start MySQL.");
            }
            
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Get database instance
function getDB() {
    return Database::getInstance()->getConnection();
}

// Backward compatibility - buat koneksi global untuk file lama
$koneksi = getDB();


