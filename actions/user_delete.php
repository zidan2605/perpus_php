<?php
declare(strict_types=1);

require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

// Only admin can delete users
if ($_SESSION['level'] != 1) {
    header("Location: ../admin/index_admin.php?page=user&actions=tampil&error=access_denied");
    exit;
}

if (!isset($_GET['username']) || empty($_GET['username'])) {
    header("Location: ../admin/index_admin.php?page=user&actions=tampil&error=invalid_username");
    exit;
}

$username = Security::sanitizeInput($_GET['username']);

// Prevent deleting own account
if ($username === $_SESSION['username']) {
    header("Location: ../admin/index_admin.php?page=user&actions=tampil&error=cannot_delete_self");
    exit;
}

try {
    $koneksi = getDB();
    
    // Get user data first
    $stmt = $koneksi->prepare("SELECT username, nama FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: ../admin/index_admin.php?page=user&actions=tampil&error=not_found");
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Delete user
    $stmt = $koneksi->prepare("DELETE FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    
    if ($stmt->execute()) {
        Security::logActivity("Deleted user: {$user['nama']} ({$user['username']})");
        header("Location: ../admin/index_admin.php?page=user&actions=tampil&success=deleted");
        exit;
    } else {
        throw new Exception("Gagal menghapus user");
    }
} catch (Exception $e) {
    error_log("Delete user error: " . $e->getMessage());
    header("Location: ../admin/index_admin.php?page=user&actions=tampil&error=delete_failed");
    exit;
}
