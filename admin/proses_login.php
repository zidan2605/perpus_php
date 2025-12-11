<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../src/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/login.php");
    exit;
}

if (!isset($_POST['login'], $_POST['user'], $_POST['pwd'], $_POST['csrf_token'])) {
    header("Location: ../public/login.php?error=missing_fields");
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'])) {
    Security::logActivity("Failed login attempt - Invalid CSRF token");
    header("Location: ../public/login.php?error=invalid_token");
    exit;
}

$user = Security::sanitizeInput($_POST['user']);
$pwd = $_POST['pwd'];

if (empty($user) || empty($pwd)) {
    header("Location: ../public/login.php?error=empty_fields");
    exit;
}

try {
    $stmt = $koneksi->prepare("SELECT username, paswd, level, nama, ket, email FROM user WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        if (password_verify($pwd, $data['paswd']) || md5($pwd) === $data['paswd']) {
            session_regenerate_id(true);
            
            $_SESSION['username'] = $data['username'];
            $_SESSION['idsesi'] = session_id();
            $_SESSION['level'] = $data['level'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['ket'] = $data['ket'];
            $_SESSION['email'] = $data['email'];
            
            unset($_SESSION['csrf_token']);
            
            if (md5($pwd) === $data['paswd']) {
                $newHash = password_hash($pwd, PASSWORD_DEFAULT);
                $updateStmt = $koneksi->prepare("UPDATE user SET paswd = ? WHERE username = ?");
                $updateStmt->bind_param("ss", $newHash, $data['username']);
                $updateStmt->execute();
            }
            
            // Successful login
            
            header("Location: index_admin.php");
            exit;
        }
    }
    
    // Failed login
    header("Location: ../public/login.php?error=invalid_credentials");
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: ../public/login.php?error=system_error");
    exit;
}


