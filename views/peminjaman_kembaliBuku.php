<?php
Security::requireAuth();

// Use the unique loan ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID Peminjaman tidak valid'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
    exit;
}

$loan_id = (int)$_GET['id'];

// Query lebih flexible - cek berbagai kondisi belum kembali
$stmt = $koneksi->prepare("SELECT * FROM peminjaman WHERE id = ?");
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data peminjaman tidak ditemukan.'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
    exit;
}

// Cek apakah sudah dikembalikan
$sudah_kembali = false;
if (isset($data['status'])) {
    $sudah_kembali = ($data['status'] === 'Sudah Kembali');
} else {
    // Jika tidak ada kolom status, cek dari tgl_kembali
    $sudah_kembali = (!empty($data['tgl_kembali']) && $data['tgl_kembali'] !== '0000-00-00');
}

if ($sudah_kembali) {
    echo "<script>alert('Buku ini sudah dikembalikan.'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
    exit;
}

$judul_buku_to_update = $data['judul_buku'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tglKembali = Security::sanitizeInput($_POST['tgl_kembali']);
    
    if (empty($tglKembali)) {
        echo "<script>alert('Tanggal kembali wajib diisi.');</script>";
    } else {
        try {
            $tglPinjamDate = new DateTime($data['tgl_pinjam']);
            $tglKembaliDate = new DateTime($tglKembali);
            $lama_pinjam = $tglPinjamDate->diff($tglKembaliDate)->days;
            
            $koneksi->begin_transaction();
            
            // Update the loan record dengan status
            $updateLoanStmt = $koneksi->prepare("UPDATE peminjaman SET tgl_kembali = ?, lama_pinjam = ?, status = 'Sudah Kembali' WHERE id = ?");
            $updateLoanStmt->bind_param("sii", $tglKembali, $lama_pinjam, $loan_id);
            $updateLoanStmt->execute();

            // Update the book status (to 'Ada')
            $updateBookStmt = $koneksi->prepare("UPDATE buku SET status = 'Ada' WHERE judul_buku = ?");
            $updateBookStmt->bind_param("s", $judul_buku_to_update);
            $updateBookStmt->execute();
            
            $koneksi->commit();
            
            Security::logActivity("Processed return for loan ID: $loan_id");
            echo "<script>alert('Buku berhasil dikembalikan.'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
        } catch (Exception $e) {
            $koneksi->rollback();
            error_log("Return book error: " . $e->getMessage());
            echo "<script>alert('Proses pengembalian gagal. Silakan coba lagi.');</script>";
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-undo-alt"></i> Proses Pengembalian Buku
    </div>
    <div class="card-body">
        <form action="?page=peminjaman&actions=kembaliBuku&id=<?= $loan_id ?>" method="post" style="display: flex; flex-direction: column; gap: 1rem;">
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label>Judul Buku</label>
                <input type="text" value="<?= Security::escapeOutput($data['judul_buku']) ?>" class="form-control" readonly>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label>Nama Peminjam</label>
                <input type="text" value="<?= Security::escapeOutput($data['peminjam']) ?>" class="form-control" readonly>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label>Tanggal Pinjam</label>
                <input type="date" value="<?= Security::escapeOutput($data['tgl_pinjam']) ?>" class="form-control" readonly>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="tgl_kembali">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" id="tgl_kembali" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="?page=peminjaman&actions=tampil" class="btn">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

