<?php
declare(strict_types=1);

require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $koneksi = getDB();
        $stmt = $koneksi->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        Security::logActivity("Deleted contact message - ID: $id");
    } catch (Exception $e) {
        error_log("Error deleting message: " . $e->getMessage());
    }
}

header("Location: ../admin/index_admin.php?page=pesan&actions=tampil");
exit;


