<?php
declare(strict_types=1);

// Include necessary files to run standalone
require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin/index_admin.php?page=buku&actions=tampil&error=invalid_id");
    exit;
}

$id = (int)$_GET['id'];

try {
    $koneksi = getDB();
    $stmt = $koneksi->prepare("DELETE FROM buku WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        Security::logActivity("Deleted book with ID: $id");
        header("Location: ../admin/index_admin.php?page=buku&actions=tampil&success=deleted");
        exit;
    } else {
        throw new Exception("Gagal menghapus buku");
    }
} catch (Exception $e) {
    error_log("Delete book error: " . $e->getMessage());
    header("Location: ../admin/index_admin.php?page=buku&actions=tampil&error=delete_failed");
    exit;
}


