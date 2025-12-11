<?php
declare(strict_types=1);

require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&error=invalid_id");
    exit;
}

$id = (int)$_GET['id'];

try {
    $koneksi = getDB();
    
    // Get member data first to delete photo
    $stmt = $koneksi->prepare("SELECT no_anggota, nama_lengkap, foto FROM anggota WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&error=not_found");
        exit;
    }
    
    $member = $result->fetch_assoc();
    
    // Delete member
    $stmt = $koneksi->prepare("DELETE FROM anggota WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete photo file if exists
        if (!empty($member['foto'])) {
            $photoPath = __DIR__ . '/../uploads/anggota/' . $member['foto'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        
        Security::logActivity("Deleted member: {$member['nama_lengkap']} ({$member['no_anggota']}) - ID: $id");
        header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&success=deleted");
        exit;
    } else {
        throw new Exception("Gagal menghapus data anggota");
    }
} catch (Exception $e) {
    error_log("Delete member error: " . $e->getMessage());
    header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&error=delete_failed");
    exit;
}
