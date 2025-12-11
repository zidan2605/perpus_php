<?php
Security::requireAuth();

// Get the book title from URL for pre-selection, but don't require it
$selected_book_title = Security::sanitizeInput($_GET['judulbuku'] ?? '');

// Fetch all available books for the dropdown
$available_books = [];
$result = $koneksi->query("SELECT judul_buku FROM buku WHERE status='Ada' ORDER BY judul_buku ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $available_books[] = $row['judul_buku'];
    }
}

// Fetch active members from anggota table
$active_members = [];
$checkAnggotaTable = $koneksi->query("SHOW TABLES LIKE 'anggota'");
if ($checkAnggotaTable && $checkAnggotaTable->num_rows > 0) {
    $result = $koneksi->query("SELECT id, no_anggota, nama_lengkap FROM anggota WHERE status='Aktif' ORDER BY nama_lengkap ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $active_members[] = $row;
        }
    }
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>alert('Token keamanan tidak valid!');</script>";
        exit;
    }

    try {
        $judulbuku = Security::sanitizeInput($_POST['judul_buku']);
        $anggota_id = (int)$_POST['anggota_id'];
        $tglPinjam = Security::sanitizeInput($_POST['tgl_pinjam']);
        $tglKembali = Security::sanitizeInput($_POST['tgl_kembali'] ?? '');
        $lamaPinjam = (int)Security::sanitizeInput($_POST['lama_pinjam'] ?? 7);
        $ket = Security::sanitizeInput($_POST['keterangan'] ?? '');

        // Validate required fields
        if (empty($judulbuku) || empty($anggota_id) || empty($tglPinjam)) {
            echo "<script>alert('Judul Buku, Anggota, dan Tanggal Pinjam wajib diisi.');</script>";
        } else {
            // Get anggota data
            $stmt_anggota = $koneksi->prepare("SELECT no_anggota, nama_lengkap, status FROM anggota WHERE id = ?");
            $stmt_anggota->bind_param("i", $anggota_id);
            $stmt_anggota->execute();
            $result_anggota = $stmt_anggota->get_result();
            
            if ($result_anggota->num_rows === 0) {
                echo "<script>alert('Data anggota tidak ditemukan!');</script>";
            } else {
                $anggota = $result_anggota->fetch_assoc();
                
                // Check if anggota is active
                if ($anggota['status'] !== 'Aktif') {
                    echo "<script>alert('Anggota tidak aktif! Tidak dapat melakukan peminjaman.');</script>";
                } else {
                    $peminjam = $anggota['no_anggota'] . ' - ' . $anggota['nama_lengkap'];
                    
                    // Calculate return date if not provided (default 7 days)
                    if (empty($tglKembali)) {
                        $tglKembali = date('Y-m-d', strtotime($tglPinjam . ' + ' . $lamaPinjam . ' days'));
                    } else {
                        // Calculate lama_pinjam from dates
                        $date1 = new DateTime($tglPinjam);
                        $date2 = new DateTime($tglKembali);
                        $lamaPinjam = $date2->diff($date1)->days;
                    }
                    
                    // Start transaction
                    $koneksi->begin_transaction();

                    // Check if status column exists
                    $checkColumn = $koneksi->query("SHOW COLUMNS FROM peminjaman LIKE 'status'");
                    $hasStatusColumn = ($checkColumn->num_rows > 0);
                    
                    // Insert peminjaman with all required columns
                    if ($hasStatusColumn) {
                        $stmt = $koneksi->prepare("INSERT INTO peminjaman (judul_buku, peminjam, tgl_pinjam, tgl_kembali, lama_pinjam, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, 'Belum Kembali')");
                        $stmt->bind_param("ssssis", $judulbuku, $peminjam, $tglPinjam, $tglKembali, $lamaPinjam, $ket);
                    } else {
                        $stmt = $koneksi->prepare("INSERT INTO peminjaman (judul_buku, peminjam, tgl_pinjam, tgl_kembali, lama_pinjam, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssis", $judulbuku, $peminjam, $tglPinjam, $tglKembali, $lamaPinjam, $ket);
                    }
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Database insert failed: " . $stmt->error);
                    }

                    // Update buku status
                    $stmt2 = $koneksi->prepare("UPDATE buku SET status='Dipinjam' WHERE judul_buku=?");
                    $stmt2->bind_param("s", $judulbuku);
                    
                    if (!$stmt2->execute()) {
                        throw new Exception("Failed to update book status: " . $stmt2->error);
                    }

                    $koneksi->commit();

                    Security::logActivity("Added new borrowing: $judulbuku by $peminjam");
                    echo "<script>alert('Data peminjaman berhasil disimpan.'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
                }
            }
        }
    } catch (Exception $e) {
        $koneksi->rollback();
        error_log("Add borrowing error: " . $e->getMessage());
        echo "<script>alert('Simpan Data Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-plus-square"></i> Tambah Data Peminjaman
    </div>
    <div class="card-body">
        <form action="?page=peminjaman&actions=tambah" method="post" style="display: flex; flex-direction: column; gap: 1rem;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="judul_buku">Judul Buku</label>
                <select name="judul_buku" id="judul_buku" class="form-control" required>
                    <option value="">--- Pilih Judul Buku ---</option>
                    <?php foreach ($available_books as $book_title): ?>
                        <option value="<?= Security::escapeOutput($book_title) ?>" <?= ($selected_book_title === $book_title) ? 'selected' : '' ?>>
                            <?= Security::escapeOutput($book_title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="anggota_id">Anggota Peminjam</label>
                <?php if (!empty($active_members)): ?>
                    <select name="anggota_id" id="anggota_id" class="form-control" required>
                        <option value="">--- Pilih Anggota ---</option>
                        <?php foreach ($active_members as $member): ?>
                            <option value="<?= $member['id'] ?>">
                                <?= Security::escapeOutput($member['no_anggota']) ?> - <?= Security::escapeOutput($member['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: var(--text-muted-color);">Hanya anggota dengan status Aktif yang ditampilkan</small>
                <?php else: ?>
                    <div style="padding: 1rem; background: #fee2e2; border-left: 4px solid #ef4444; border-radius: 8px; color: #991b1b;">
                        <i class="fas fa-exclamation-circle"></i> Tidak ada anggota aktif. Silakan tambah anggota terlebih dahulu di menu <a href="?page=anggota&actions=tambah" style="color: #991b1b; font-weight: 600;">Anggota</a>.
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="tgl_pinjam">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="lama_pinjam">Lama Pinjam (hari)</label>
                <input type="number" name="lama_pinjam" id="lama_pinjam" class="form-control" value="7" min="1" required>
                <small style="color: var(--text-muted-color);">Default 7 hari, tanggal kembali akan dihitung otomatis</small>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="keterangan">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Contoh: Untuk referensi tugas" rows="3"></textarea>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="?page=peminjaman&actions=tampil" class="btn">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>


