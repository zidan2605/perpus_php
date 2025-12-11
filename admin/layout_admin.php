<?php
// Load config for BASE_URL
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Admin</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= asset('css/admin-modern.css') ?>">
</head>
<body>
<?php
// Include database connection
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../src/db.php';
$koneksi = getDB();
?>

<div class="app-container">
    <!-- Main Sidebar Container -->
    <aside class="app-sidebar">
        <!-- Brand Logo -->
        <a href="index_admin.php" class="brand-link">
            <i class="fas fa-book-reader"></i>
            <span>Perpustakaan</span>
        </a>

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
                <span class="user-role"><?= htmlspecialchars($_SESSION['ket']) ?></span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="sidebar-nav">
            <ul>
                <?php
                // Function to check if a menu item is active
                function is_active($page, $action = null) {
                    $is_page_active = isset($_GET['page']) && $_GET['page'] == $page;
                    $is_action_active = true;
                    if ($action !== null) {
                        $is_action_active = isset($_GET['actions']) && $_GET['actions'] == $action;
                    }
                    return $is_page_active && $is_action_active;
                }

                function is_menu_open($page) {
                    return isset($_GET['page']) && $_GET['page'] == $page;
                }
                ?>

                <li>
                    <a href="index_admin.php" class="<?= empty($_GET['page']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN</li>

                <li class="has-submenu <?= is_menu_open('buku') ? 'open' : '' ?>">
                    <a href="#">
                        <i class="nav-icon fas fa-book"></i>
                        <span>Buku</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="?page=buku&actions=tampil" class="<?= is_active('buku', 'tampil') ? 'active' : '' ?>"><i class="fas fa-list"></i> Daftar Buku</a></li>
                        <li><a href="?page=buku&actions=tambah" class="<?= is_active('buku', 'tambah') ? 'active' : '' ?>"><i class="fas fa-plus"></i> Tambah Buku</a></li>
                    </ul>
                </li>

                <li class="has-submenu <?= is_menu_open('peminjaman') ? 'open' : '' ?>">
                    <a href="#">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <span>Peminjaman</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="?page=peminjaman&actions=tampil" class="<?= is_active('peminjaman', 'tampil') ? 'active' : '' ?>"><i class="fas fa-list"></i> Data Peminjaman</a></li>
                        <li><a href="?page=peminjaman&actions=tambah" class="<?= is_active('peminjaman', 'tambah') ? 'active' : '' ?>"><i class="fas fa-plus"></i> Tambah Peminjaman</a></li>
                    </ul>
                </li>

                <li class="has-submenu <?= is_menu_open('anggota') ? 'open' : '' ?>">
                    <a href="#">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <span>Anggota</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="?page=anggota&actions=tampil" class="<?= is_active('anggota', 'tampil') ? 'active' : '' ?>"><i class="fas fa-list"></i> Data Anggota</a></li>
                        <li><a href="?page=anggota&actions=tambah" class="<?= is_active('anggota', 'tambah') ? 'active' : '' ?>"><i class="fas fa-user-plus"></i> Tambah Anggota</a></li>
                    </ul>
                </li>

                <li class="has-submenu <?= is_menu_open('user') ? 'open' : '' ?>">
                    <a href="#">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <span>User</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="?page=user&actions=tampil" class="<?= is_active('user', 'tampil') ? 'active' : '' ?>"><i class="fas fa-list"></i> Data User</a></li>
                        <li><a href="?page=user&actions=tambah" class="<?= is_active('user', 'tambah') ? 'active' : '' ?>"><i class="fas fa-user-plus"></i> Tambah User</a></li>
                    </ul>
                </li>

                <li class="nav-header">KOMUNIKASI</li>

                <li class="has-submenu <?= is_menu_open('pengumuman') ? 'open' : '' ?>">
                    <a href="#">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <span>Pengumuman</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="?page=pengumuman&actions=tampil" class="<?= is_active('pengumuman', 'tampil') ? 'active' : '' ?>"><i class="fas fa-list"></i> Data Pengumuman</a></li>
                        <li><a href="?page=pengumuman&actions=tambah" class="<?= is_active('pengumuman', 'tambah') ? 'active' : '' ?>"><i class="fas fa-plus"></i> Tambah Pengumuman</a></li>
                    </ul>
                </li>

                <li>
                    <a href="?page=pesan&actions=tampil" class="<?= is_active('pesan', 'tampil') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-envelope"></i>
                        <span>Pesan</span>
                        <?php
                        // Count unread messages
                        try {
                            $checkTable = $koneksi->query("SHOW TABLES LIKE 'contact_messages'");
                            if ($checkTable->num_rows > 0) {
                                $checkColumn = $koneksi->query("SHOW COLUMNS FROM contact_messages LIKE 'status'");
                                if ($checkColumn->num_rows > 0) {
                                    $unreadCount = $koneksi->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'")->fetch_assoc()['count'];
                                } else {
                                    $unreadCount = $koneksi->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")->fetch_assoc()['count'];
                                }
                                if ($unreadCount > 0) {
                                    echo '<span class="badge badge-danger">' . $unreadCount . '</span>';
                                }
                            }
                        } catch (Exception $e) {}
                        ?>
                    </a>
                </li>

                <li class="nav-header">LAPORAN</li>

                <li>
                    <a href="?page=buku&actions=report" class="<?= is_active('buku', 'report') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <span>Laporan Buku</span>
                    </a>
                </li>
                
                <li>
                    <a href="?page=peminjaman&actions=report" class="<?= is_active('peminjaman', 'report') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <span>Laporan Peminjaman</span>
                    </a>
                </li>

                <li class="nav-header">AKUN</li>
                
                <li>
                    <a href="../public/logout.php" class="logout-link" onclick="return confirm('Yakin ingin logout?')">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <?php
                        // A simple way to set page title based on 'page' GET parameter
                        if (isset($_GET['page'])) {
                            echo ucwords(str_replace('_', ' ', $_GET['page']));
                        } else {
                            echo 'Dashboard';
                        }
                    ?>
                </h1>
            </div>
            <div class="header-right">
                <div class="user-profile">
                    <i class="fas fa-user"></i>
                    <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                </div>
                <a href="../public/logout.php" class="logout-button" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <!-- Main content -->
        <main class="app-content">
            <?php require 'content_admin.php'; ?>
        </main>
        
        <!-- Footer -->
        <footer class="app-footer">
            <p>© <?= date('Y') ?> <a href="landing.php" target="_blank">Perpustakaan Digital</a>. All rights reserved.</p>
        </footer>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
// Toggle sidebar for mobile
const sidebarToggle = document.querySelector('.sidebar-toggle');
const sidebar = document.querySelector('.app-sidebar');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Submenu toggle
document.querySelectorAll('.has-submenu > a').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        const parent = item.parentElement;
        parent.classList.toggle('open');
    });
});

// Auto dismiss alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

</body>
</html>

