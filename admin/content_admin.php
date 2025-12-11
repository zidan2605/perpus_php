<?php
if (!isset($_SESSION['username'])) {
    header("Location: ../public/login.php");
    exit();
}

$page = isset($_GET['page']) ? trim($_GET['page']) : '';
$aksi = isset($_GET['actions']) ? trim($_GET['actions']) : '';

if(empty($page)){
    require 'beranda_adm.php';
} else {
    // New module system
    $newModules = ['settings', 'pengaturan'];
    
    if (in_array($page, $newModules)) {
        if ($page === 'pengaturan') {
            $file = __DIR__ . "/../views/pengaturan_tampil.php";
        } else {
            $file = __DIR__ . "/../views/" . $page . ".php";
        }
        
        if (file_exists($file)){
            require $file;
        } else {
            echo "<div class='alert alert-danger'>File tidak ditemukan: " . htmlspecialchars($file) . "</div>";
            require 'beranda_adm.php';
        }
    } else {
        // Old system
        $allowedPages = ['buku', 'peminjaman', 'user', 'pesan', 'anggota', 'pengumuman'];
        $allowedActions = ['tampil', 'tambah', 'edit', 'detail', 'delete', 'report', 'kembaliBuku', 'mark_read'];
        
        if (in_array($page, $allowedPages) && in_array($aksi, $allowedActions)) {
            $file = __DIR__ . "/../views/" . $page . "_" . $aksi . ".php";
            
            if (file_exists($file)){
                require $file;
            } else {
                echo "<div class='alert alert-danger'>File tidak ditemukan: " . htmlspecialchars($file) . "</div>";
                require 'beranda_adm.php';
            }
        } else {
            // Invalid page/action combination
            if (!empty($page)) {
                echo "<div class='alert alert-warning'>Halaman tidak valid: " . htmlspecialchars($page) . " / " . htmlspecialchars($aksi) . "</div>";
            }
            require 'beranda_adm.php';
        }
    }
}
?>
