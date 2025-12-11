<?php
declare(strict_types=1);

require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin/index_admin.php?page=peminjaman&actions=tampil&error=invalid_id");
    exit;
}

$id = (int)$_GET['id'];

try {
    $koneksi = getDB();
    // Note: Deleting a loan record does not automatically update the book's status to 'Ada'.
    // This might be a desired feature to add later, but for now, we just delete the record.
    $stmt = $koneksi->prepare("DELETE FROM peminjaman WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        Security::logActivity("Deleted loan with ID: $id");
        header("Location: ../admin/index_admin.php?page=peminjaman&actions=tampil&success=deleted");
        exit;
    } else {
        throw new Exception("Gagal menghapus data peminjaman");
    }
} catch (Exception $e) {
    error_log("Delete loan error: " . $e->getMessage());
    header("Location: ../admin/index_admin.php?page=peminjaman&actions=tampil&error=delete_failed");
    exit;
}


