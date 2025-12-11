<?php
declare(strict_types=1);

// Session Security Configuration
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '0'); // Set to '1' if using HTTPS
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.gc_maxlifetime', '3600'); // 1 hour
    
    // Hide PHP version
    ini_set('expose_php', '0');
    
    // Error handling - hide errors in production
    if (getenv('APP_ENV') === 'production') {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        error_reporting(0);
    } else {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }
    
    // Set error log location
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
    ini_set('log_errors', '1');
    
    session_start();
    
    // Validate session
    if (isset($_SESSION['CREATED'])) {
        if (time() - $_SESSION['CREATED'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }
    } else {
        $_SESSION['CREATED'] = time();
    }
}


