<?php
Security::requireAuth();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Check if column is is_read or status
        $checkColumn = $koneksi->query("SHOW COLUMNS FROM contact_messages LIKE 'is_read'");
        
        if ($checkColumn->num_rows > 0) {
            $stmt = $koneksi->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        } else {
            $stmt = $koneksi->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        Security::logActivity("Marked message as read - ID: $id");
    } catch (Exception $e) {
        error_log("Error marking message as read: " . $e->getMessage());
    }
}

header("Location: index_admin.php?page=pesan&actions=tampil");
exit;



