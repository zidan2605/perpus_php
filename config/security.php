<?php
declare(strict_types=1);

class Security {
    
    public static function sanitizeInput(string $data): string {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    public static function escapeOutput(?string $data): string {
        if ($data === null) return '';
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    public static function generateCSRFToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken(?string $token): bool {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public static function checkAuth(): bool {
        if (!isset($_SESSION['username']) || !isset($_SESSION['idsesi'])) {
            return false;
        }
        
        if ($_SESSION['idsesi'] !== session_id()) {
            return false;
        }
        
        return true;
    }
    
    public static function requireAuth(): void {
        if (!self::checkAuth()) {
            header("Location: index.php");
            exit;
        }
    }
    
    public static function checkLevel(string $requiredLevel): bool {
        return isset($_SESSION['level']) && $_SESSION['level'] === $requiredLevel;
    }
    
    public static function logActivity(string $activity): void {
        $logFile = __DIR__ . '/../logs/activity.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $user = $_SESSION['username'] ?? 'Guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $logEntry = "[$timestamp] User: $user | IP: $ip | Activity: $activity\n";
        
        error_log($logEntry, 3, $logFile);
    }
}


