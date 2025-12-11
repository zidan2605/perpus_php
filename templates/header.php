<?php
// Load config for BASE_URL
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Perpustakaan Digital' ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?= url('public/index.php') ?>" class="navbar-brand">
                <i class="fas fa-book-reader"></i>
                <span>Perpustakaan Digital</span>
            </a>
            <button class="navbar-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="navbar-nav" id="navMenu">
                <li><a href="<?= url('public/index.php') ?>" class="<?= $activePage == 'home' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= url('public/books.php') ?>" class="<?= $activePage == 'books' ? 'active' : '' ?>">Daftar Buku</a></li>
                <li><a href="<?= url('public/announcements.php') ?>" class="<?= $activePage == 'announcements' ? 'active' : '' ?>">Pengumuman</a></li>
                <li><a href="<?= url('public/index.php') ?>#about" class="<?= $activePage == 'about' ? 'active' : '' ?>">Tentang</a></li>
                <li><a href="<?= url('public/contact.php') ?>" class="<?= $activePage == 'contact' ? 'active' : '' ?>">Kontak</a></li>
                <?php if (Auth::isLoggedIn()): ?>
                    <?php if ($_SESSION['level'] == 1): ?>
                        <li><a href="<?= url('admin/index_admin.php') ?>" class="btn-login"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?= url('public/logout.php') ?>" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= url('public/login.php') ?>" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>


