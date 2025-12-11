<?php
declare(strict_types=1);

require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin/index_admin.php?page=pengumuman&actions=tampil&error=invalid_id");
    exit;
}

$id = (int)$_GET['id'];

try {
    $koneksi = getDB();
    
    // Get announcement data
    $stmt = $koneksi->prepare("SELECT id, title, created_by FROM pengumuman WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: ../admin/index_admin.php?page=pengumuman&actions=tampil&error=not_found");
        exit;
    }
    
    $announcement = $result->fetch_assoc();
    
    // Check if user can delete (owner or admin)
    $canDelete = ($announcement['created_by'] === $_SESSION['username']) || ($_SESSION['level'] == 1);
    
    if (!$canDelete) {
        header("Location: ../admin/index_admin.php?page=pengumuman&actions=tampil&error=unauthorized");
        exit;
    }
    
    // Delete announcement
    $stmt = $koneksi->prepare("DELETE FROM pengumuman WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        Security::logActivity("Deleted announcement: {$announcement['title']} (ID: $id)");
        header("Location: ../admin/index_admin.php?page=pengumuman&actions=tampil&success=deleted");
        exit;
    } else {
        throw new Exception("Gagal menghapus pengumuman");
    }
} catch (Exception $e) {
    error_log("Delete announcement error: " . $e->getMessage());
    header("Location: ../admin/index_admin.php?page=pengumuman&actions=tampil&error=delete_failed");
    exit;
}
