<?php
declare(strict_types=1);

require '../config/session.php';
require '../config/security.php';
require '../src/db.php';

Security::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/index_admin.php?page=anggota&actions=tampil");
    exit;
}

// Validate CSRF token
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&error=invalid_token");
    exit;
}

$action = $_POST['action'] ?? '';
$koneksi = getDB();

// Function to handle file upload
function uploadFoto($file, $oldFoto = null) {
    $uploadDir = __DIR__ . '/../uploads/anggota/';
    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Check if file is uploaded
    if (empty($file['name'])) {
        return $oldFoto;
    }
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Format file tidak valid. Hanya JPG, PNG, GIF yang diperbolehkan.");
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception("Ukuran file terlalu besar. Maksimal 2MB.");
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'anggota_' . time() . '_' . uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Gagal mengupload file.");
    }
    
    // Delete old photo if exists
    if ($oldFoto && file_exists($uploadDir . $oldFoto)) {
        unlink($uploadDir . $oldFoto);
    }
    
    return $filename;
}

try {
    if ($action === 'add') {
        // Add new member
        $no_anggota = Security::sanitizeInput($_POST['no_anggota']);
        $nama_lengkap = Security::sanitizeInput($_POST['nama_lengkap']);
        $jenis_kelamin = Security::sanitizeInput($_POST['jenis_kelamin']);
        $tempat_lahir = Security::sanitizeInput($_POST['tempat_lahir']);
        $tanggal_lahir = Security::sanitizeInput($_POST['tanggal_lahir']);
        $alamat = Security::sanitizeInput($_POST['alamat']);
        $no_telepon = Security::sanitizeInput($_POST['no_telepon']);
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        $pekerjaan = Security::sanitizeInput($_POST['pekerjaan'] ?? '');
        $tanggal_daftar = Security::sanitizeInput($_POST['tanggal_daftar']);
        $status = Security::sanitizeInput($_POST['status']);
        $keterangan = Security::sanitizeInput($_POST['keterangan'] ?? '');
        
        // Handle photo upload
        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $foto = uploadFoto($_FILES['foto']);
        }
        
        // Insert to database
        $stmt = $koneksi->prepare(
            "INSERT INTO anggota (no_anggota, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, 
             alamat, no_telepon, email, pekerjaan, foto, tanggal_daftar, status, keterangan) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            "sssssssssssss",
            $no_anggota, $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir,
            $alamat, $no_telepon, $email, $pekerjaan, $foto, $tanggal_daftar, $status, $keterangan
        );
        
        if ($stmt->execute()) {
            Security::logActivity("Added new member: $nama_lengkap ($no_anggota)");
            header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&success=added");
        } else {
            throw new Exception("Gagal menambahkan data anggota");
        }
        
    } elseif ($action === 'edit') {
        // Edit existing member
        $id = (int)$_POST['id'];
        $nama_lengkap = Security::sanitizeInput($_POST['nama_lengkap']);
        $jenis_kelamin = Security::sanitizeInput($_POST['jenis_kelamin']);
        $tempat_lahir = Security::sanitizeInput($_POST['tempat_lahir']);
        $tanggal_lahir = Security::sanitizeInput($_POST['tanggal_lahir']);
        $alamat = Security::sanitizeInput($_POST['alamat']);
        $no_telepon = Security::sanitizeInput($_POST['no_telepon']);
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        $pekerjaan = Security::sanitizeInput($_POST['pekerjaan'] ?? '');
        $tanggal_daftar = Security::sanitizeInput($_POST['tanggal_daftar']);
        $status = Security::sanitizeInput($_POST['status']);
        $keterangan = Security::sanitizeInput($_POST['keterangan'] ?? '');
        $old_foto = $_POST['old_foto'] ?? null;
        
        // Handle photo upload
        $foto = uploadFoto($_FILES['foto'], $old_foto);
        
        // Update database
        $stmt = $koneksi->prepare(
            "UPDATE anggota SET nama_lengkap = ?, jenis_kelamin = ?, tempat_lahir = ?, 
             tanggal_lahir = ?, alamat = ?, no_telepon = ?, email = ?, pekerjaan = ?, 
             foto = ?, tanggal_daftar = ?, status = ?, keterangan = ? 
             WHERE id = ?"
        );
        
        $stmt->bind_param(
            "ssssssssssssi",
            $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir,
            $alamat, $no_telepon, $email, $pekerjaan, $foto, $tanggal_daftar, $status, $keterangan, $id
        );
        
        if ($stmt->execute()) {
            Security::logActivity("Updated member data: $nama_lengkap (ID: $id)");
            header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&success=updated");
        } else {
            throw new Exception("Gagal mengupdate data anggota");
        }
        
    } else {
        header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&error=invalid_action");
    }
    
} catch (Exception $e) {
    error_log("Anggota process error: " . $e->getMessage());
    header("Location: ../admin/index_admin.php?page=anggota&actions=tampil&error=process_failed");
}

exit;
