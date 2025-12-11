<?php
/**
 * Configuration File
 * Auto-detect BASE_URL untuk portability
 * Compatible dengan: Laragon, XAMPP, WAMP, Linux, Production Server
 */

// Detect protocol (http or https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) 
            ? 'https://' : 'http://';

// Detect host
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Auto-detect base path
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);

// Remove /public or /admin from path if exists
$basePath = preg_replace('#/(public|admin|actions|views|templates)(/.*)?$#', '', $scriptDir);

// Normalize path (remove duplicate slashes)
$basePath = '/' . trim($basePath, '/');
if ($basePath === '/') $basePath = '';

// Define BASE_URL constant
if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . $host . $basePath);
}

// Define BASE_PATH for internal use
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $basePath);
}


// Application Settings
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Perpustakaan Digital');
}

if (!defined('APP_VERSION')) {
    define('APP_VERSION', '3.0');
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (Development mode)
// Set to 0 for production
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Helper function untuk generate URL
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Helper function untuk generate asset URL
function asset($path = '') {
    return BASE_URL . '/public/' . ltrim($path, '/');
}
