<?php
// Get statistics (PHP logic remains the same)
$stats = [
    'total_books' => 0,
    'available_books' => 0,
    'borrowed_books' => 0,
    'total_members' => 0,
    'active_loans' => 0,
    'overdue_loans' => 0,
    'total_announcements' => 0
];
$recent_books = null;
$recent_loans = null;
try {
    $result = $koneksi->query("SELECT COUNT(*) as total FROM buku");
    if ($result) $stats['total_books'] = $result->fetch_assoc()['total'];
    $result = $koneksi->query("SELECT COUNT(*) as total FROM buku WHERE status='Ada'");
    if ($result) $stats['available_books'] = $result->fetch_assoc()['total'];
    $result = $koneksi->query("SELECT COUNT(*) as total FROM buku WHERE status='Dipinjam'");
    if ($result) $stats['borrowed_books'] = $result->fetch_assoc()['total'];
    
    // Check if anggota table exists
    $checkTable = $koneksi->query("SHOW TABLES LIKE 'anggota'");
    if ($checkTable && $checkTable->num_rows > 0) {
        $result = $koneksi->query("SELECT COUNT(*) as total FROM anggota WHERE status='Aktif'");
        if ($result) $stats['total_members'] = $result->fetch_assoc()['total'];
    } else {
        $result = $koneksi->query("SELECT COUNT(*) as total FROM user WHERE level=0");
        if ($result) $stats['total_members'] = $result->fetch_assoc()['total'];
    }
    
    $result = $koneksi->query("SELECT COUNT(*) as total FROM peminjaman WHERE status='Belum Kembali'");
    if ($result) $stats['active_loans'] = $result->fetch_assoc()['total'];
    $result = $koneksi->query("SELECT COUNT(*) as total FROM peminjaman WHERE status='Belum Kembali' AND DATEDIFF(CURDATE(), tgl_pinjam) > 7");
    if ($result) $stats['overdue_loans'] = $result->fetch_assoc()['total'];
    
    // Check if pengumuman table exists
    $checkPengumumanTable = $koneksi->query("SHOW TABLES LIKE 'pengumuman'");
    if ($checkPengumumanTable && $checkPengumumanTable->num_rows > 0) {
        $result = $koneksi->query("SELECT COUNT(*) as total FROM pengumuman");
        if ($result) $stats['total_announcements'] = $result->fetch_assoc()['total'];
    }
    
    $recent_books = $koneksi->query("SELECT judul_buku, nama_pengarang, tahun_terbit, status FROM buku ORDER BY id DESC LIMIT 5");
    $recent_loans = $koneksi->query("SELECT judul_buku, peminjam, tgl_pinjam, status FROM peminjaman ORDER BY id DESC LIMIT 5");
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>

<!-- No separate content-header needed, it's part of the new layout -->

<!-- Welcome Alert -->
<div class="alert alert-info">
    <strong>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</strong>
    Anda login sebagai <?= htmlspecialchars($_SESSION['ket']) ?>.
</div>

<!-- Main Dashboard Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
    
    <!-- Total Books -->
    <div class="stat-box bg-info">
        <div>
            <div class="stat-box-value"><?= $stats['total_books'] ?></div>
            <div class="stat-box-label">Total Judul Buku</div>
        </div>
        <i class="stat-box-icon fas fa-book-spells"></i>
        <a href="?page=buku&actions=tampil" class="stat-box-footer">Lihat Detail</a>
    </div>

    <!-- Available Books -->
    <div class="stat-box bg-success">
        <div>
            <div class="stat-box-value"><?= $stats['available_books'] ?></div>
            <div class="stat-box-label">Buku Tersedia</div>
        </div>
        <i class="stat-box-icon fas fa-check-circle"></i>
        <a href="?page=buku&actions=tampil" class="stat-box-footer">Lihat Detail</a>
    </div>

    <!-- Borrowed Books -->
    <div class="stat-box bg-warning">
        <div>
            <div class="stat-box-value"><?= $stats['borrowed_books'] ?></div>
            <div class="stat-box-label">Buku Dipinjam</div>
        </div>
        <i class="stat-box-icon fas fa-book-reader"></i>
        <a href="?page=peminjaman&actions=tampil" class="stat-box-footer">Lihat Detail</a>
    </div>

    <!-- Total Members -->
    <div class="stat-box bg-primary">
        <div>
            <div class="stat-box-value"><?= $stats['total_members'] ?></div>
            <div class="stat-box-label">Anggota Aktif</div>
        </div>
        <i class="stat-box-icon fas fa-user-friends"></i>
        <a href="?page=anggota&actions=tampil" class="stat-box-footer">Lihat Detail</a>
    </div>

    <!-- Active Loans -->
    <div class="stat-box bg-danger">
        <div>
            <div class="stat-box-value"><?= $stats['active_loans'] ?></div>
            <div class="stat-box-label">Peminjaman Aktif</div>
        </div>
        <i class="stat-box-icon fas fa-hand-holding-box"></i>
        <a href="?page=peminjaman&actions=tampil" class="stat-box-footer">Lihat Detail</a>
    </div>
    
    <!-- Total Announcements -->
    <div class="stat-box bg-purple">
        <div>
            <div class="stat-box-value"><?= $stats['total_announcements'] ?></div>
            <div class="stat-box-label">Total Pengumuman</div>
        </div>
        <i class="stat-box-icon fas fa-bullhorn"></i>
        <a href="?page=pengumuman&actions=tampil" class="stat-box-footer">Lihat Detail</a>
    </div>
</div>

<!-- Glance and Recent Activity Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">

    <!-- Left Column -->
    <div>
        <!-- At a Glance -->
        <div class="card">
            <div class="card-header">Sekilas Pandang</div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 0.8rem;">
                <div class="glance">
                    <i class="glance-icon fas fa-users"></i>
                    <div class="glance-details">
                        <div class="glance-value"><?= $stats['total_members'] ?></div>
                        <div class="glance-label">Total Anggota Terdaftar</div>
                    </div>
                </div>
                <div class="glance">
                    <i class="glance-icon fas fa-exclamation-triangle"></i>
                    <div class="glance-details">
                        <div class="glance-value"><?= $stats['overdue_loans'] ?></div>
                        <div class="glance-label">Pinjaman Lewat Batas</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Actions -->
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">Aksi Cepat</div>
            <div class="card-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                <a href="?page=buku&actions=tambah" class="btn-app"><i class="fas fa-book"></i> Tambah Buku</a>
                <a href="?page=peminjaman&actions=tambah" class="btn-app"><i class="fas fa-handshake"></i> Pinjam Buku</a>
                <a href="?page=buku&actions=report" class="btn-app"><i class="fas fa-file-pdf"></i> Laporan Buku</a>
                <a href="?page=peminjaman&actions=report" class="btn-app"><i class="fas fa-file-pdf"></i> Laporan Pinjam</a>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Books -->
    <div class="card">
        <div class="card-header">Buku Terbaru</div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Judul Buku</th><th>Pengarang</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_books && $recent_books->num_rows > 0): ?>
                            <?php while($book = $recent_books->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['judul_buku']) ?></td>
                                <td><?= htmlspecialchars($book['nama_pengarang']) ?></td>
                                <td><span class="badge <?= $book['status'] == 'Ada' ? 'badge-success' : 'badge-warning' ?>"><?= $book['status'] == 'Ada' ? 'Tersedia' : 'Dipinjam' ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center;">Belum ada data buku</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer" style="text-align: center;">
             <a href="?page=buku&actions=tampil" class="btn btn-primary">Lihat Semua Buku</a>
        </div>
    </div>
</div>

<?php if($stats['overdue_loans'] > 0): ?>
<!-- Alert for Overdue Loans -->
<div class="alert alert-danger" style="margin-top: 1.5rem;">
    <strong>Peringatan!</strong> Ada <strong><?= $stats['overdue_loans'] ?></strong> peminjaman yang sudah lebih dari 7 hari!
    <a href="?page=peminjaman&actions=tampil">Lihat Detail</a>
</div>
<?php endif; ?>

