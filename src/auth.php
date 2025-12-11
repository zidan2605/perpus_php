<?php
/**
 * Authentication Helper
 * perpustakaan-php
 */

// Load config if not already loaded
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

class Auth {
    
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['username']) && isset($_SESSION['level']);
    }
    
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header("Location: " . url('public/login.php'));
            exit;
        }
    }
    
    public static function requireAdmin() {
        self::requireAuth();
        if ($_SESSION['level'] != 1) {
            header("Location: " . url('public/index.php'));
            exit;
        }
    }
    
    public static function login($username, $password) {
        $db = getDB();
        
        $stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                self::startSession();
                $_SESSION['username'] = $user['username'];
                $_SESSION['level'] = $user['level'];
                $_SESSION['ket'] = $user['level'] == 1 ? 'Administrator' : 'User';
                
                self::logActivity("User logged in: " . $username);
                return true;
            }
        }
        
        return false;
    }
    
    public static function logout() {
        self::startSession();
        $username = $_SESSION['username'] ?? 'unknown';
        
        session_unset();
        session_destroy();
        
        self::logActivity("User logged out: " . $username);
    }
    
    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'username' => $_SESSION['username'],
            'level' => $_SESSION['level'],
            'ket' => $_SESSION['ket']
        ];
    }
    
    // CSRF Protection
    public static function generateCSRFToken() {
        self::startSession();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        self::startSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Security helpers
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    public static function escapeOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    // Activity logging
    public static function logActivity($message) {
        $logFile = __DIR__ . '/../logs/activity.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user = $_SESSION['username'] ?? 'guest';
        
        $logMessage = "[$timestamp] [$ip] [$user] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}


