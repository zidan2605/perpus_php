<?php
/**
 * Main Index - Landing Page
 * perpustakaan-php/public/index.php
 */

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

Auth::startSession();

$pageTitle = 'Perpustakaan Digital - Modern Library System';
$activePage = 'home';

// Get statistics
$db = getDB();
$total_books = 0;
$available_books = 0;
$members = 0;

try {
    $result = $db->query("SELECT COUNT(*) as total FROM buku");
    if ($result) $total_books = $result->fetch_assoc()['total'];
    
    $result = $db->query("SELECT COUNT(*) as total FROM buku WHERE status='Ada'");
    if ($result) $available_books = $result->fetch_assoc()['total'];
    
    // Check if anggota table exists
    $checkTable = $db->query("SHOW TABLES LIKE 'anggota'");
    if ($checkTable && $checkTable->num_rows > 0) {
        $result = $db->query("SELECT COUNT(*) as total FROM anggota WHERE status='Aktif'");
        if ($result) $members = $result->fetch_assoc()['total'];
    } else {
        $result = $db->query("SELECT COUNT(*) as total FROM user WHERE level=0");
        if ($result) $members = $result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
}

include __DIR__ . '/../templates/header.php';
?>

<section class="hero">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Selamat Datang di<br><span>Perpustakaan Digital</span></h1>
            <p class="hero-subtitle">Sistem informasi perpustakaan modern yang memudahkan pengelolaan koleksi buku dan transaksi peminjaman dengan teknologi terkini</p>
            <div class="hero-buttons">
                <a href="<?= url('public/books.php') ?>" class="btn-hero btn-primary">
                    <i class="fas fa-book"></i> Jelajahi Koleksi
                </a>
                <a href="#about" class="btn-hero btn-secondary">
                    <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
        </svg>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-books"></i></div>
                <div class="stat-number"><?= $total_books ?></div>
                <div class="stat-label">Total Koleksi Buku</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-number"><?= $available_books ?></div>
                <div class="stat-label">Buku Tersedia</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?= $members ?></div>
                <div class="stat-label">Anggota Aktif</div>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Fitur Unggulan</span>
            <h2 class="section-title">Kenapa Memilih Kami?</h2>
            <p class="section-subtitle">Sistem perpustakaan lengkap dengan berbagai fitur canggih</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-book"></i></div>
                <h3>Manajemen Buku</h3>
                <p>Kelola koleksi buku dengan mudah, lengkap dengan pencarian dan kategorisasi yang efisien</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-exchange-alt"></i></div>
                <h3>Transaksi Peminjaman</h3>
                <p>Proses peminjaman dan pengembalian buku yang cepat dan terintegrasi</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Laporan & Statistik</h3>
                <p>Laporan lengkap dan statistik real-time untuk monitoring perpustakaan</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Keamanan Tinggi</h3>
                <p>Sistem keamanan berlapis dengan enkripsi data dan proteksi terhadap serangan</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3>Responsive Design</h3>
                <p>Tampilan yang optimal di semua perangkat, dari desktop hingga smartphone</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users-cog"></i></div>
                <h3>User Friendly</h3>
                <p>Interface yang intuitif dan mudah digunakan untuk semua kalangan</p>
            </div>
        </div>
    </div>
</section>

<section class="about" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <div class="about-icon-wrapper">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
            <div class="about-content">
                <span class="section-badge">Tentang Kami</span>
                <h2>Perpustakaan Digital Modern</h2>
                <p>Perpustakaan Digital adalah sistem informasi perpustakaan modern yang dirancang untuk memudahkan pengelolaan koleksi buku, transaksi peminjaman, dan administrasi perpustakaan secara keseluruhan.</p>
                <p>Dengan antarmuka yang user-friendly dan fitur-fitur canggih, kami berkomitmen untuk memberikan pengalaman terbaik dalam pengelolaan perpustakaan digital.</p>
                <ul class="about-features">
                    <li><i class="fas fa-check-circle"></i> Sistem terintegrasi dan mudah digunakan</li>
                    <li><i class="fas fa-check-circle"></i> Keamanan data terjamin</li>
                    <li><i class="fas fa-check-circle"></i> Laporan dan statistik real-time</li>
                    <li><i class="fas fa-check-circle"></i> Support dan maintenance berkala</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Siap Menggunakan Sistem Kami?</h2>
            <p>Hubungi kami untuk informasi lebih lanjut</p>
            <a href="<?= url('public/contact.php') ?>" class="btn-cta">
                <i class="fas fa-envelope"></i> Hubungi Kami
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>


