<?php
Security::requireAuth();

$id = Security::sanitizeInput($_GET['id'] ?? '');
if (empty($id)) {
    echo "<script>alert('ID tidak valid'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM peminjaman WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.assign('?page=peminjaman&actions=tampil');</script>";
    exit;
}
?>
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit"></i> Update Data Peminjaman Buku
    </div>
    <div class="card-body">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Judul Buku</label>
                <input type="text" name="judulbuku" value="<?= Security::escapeOutput($data['judul_buku']) ?>" class="form-control" readonly style="background: var(--light-color);">
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nama Peminjam</label>
                <input type="text" name="peminjam" value="<?= Security::escapeOutput($data['peminjam']) ?>" class="form-control" placeholder="Nama Peminjam" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="<?= Security::escapeOutput($data['tgl_pinjam']) ?>" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" value="<?= Security::escapeOutput($data['tgl_kembali']) ?>" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Lama Pinjam (hari)</label>
                <input type="number" name="lama" value="<?= Security::escapeOutput($data['lama_pinjam']) ?>" class="form-control" placeholder="Lama pinjam dalam hari" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Status</label>
                <select name="status" class="form-control" required>
                    <option value="Belum Kembali" <?= $data['status'] == 'Belum Kembali' ? 'selected' : '' ?>>Belum Kembali</option>
                    <option value="Sudah Kembali" <?= $data['status'] == 'Sudah Kembali' ? 'selected' : '' ?>>Sudah Kembali</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Keterangan</label>
                <textarea name="ket" class="form-control" rows="3" placeholder="Keterangan tambahan"><?= Security::escapeOutput($data['ket']) ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Data
                </button>
                <a href="?page=peminjaman&actions=tampil" class="btn btn-danger">
                    <i class="fas fa-arrow-left"></i> Kembali
            </div>
        </form>
    </div>
</div>

<?php 
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>alert('Token keamanan tidak valid!');</script>";
        exit;
    }
    
    try {
        $judulbuku = Security::sanitizeInput($_POST['judulbuku']);
        $peminjam = Security::sanitizeInput($_POST['peminjam']);
        $tglPinjam = Security::sanitizeInput($_POST['tgl_pinjam']);
        $tglKembali = Security::sanitizeInput($_POST['tgl_kembali']);
        $lama = (int)Security::sanitizeInput($_POST['lama']);
        $status = Security::sanitizeInput($_POST['status']);
        $ket = Security::sanitizeInput($_POST['ket']);
        
        $stmt = $koneksi->prepare("UPDATE peminjaman SET judul_buku=?, peminjam=?, tgl_pinjam=?, tgl_kembali=?, lama_pinjam=?, status=?, ket=? WHERE id=?");
        $stmt->bind_param("ssssssss", $judulbuku, $peminjam, $tglPinjam, $tglKembali, $lama, $status, $ket, $id);
        
        if($stmt->execute()) {
            Security::logActivity("Updated borrowing ID: $id");
            echo "<script>window.location.assign('?page=peminjaman&actions=tampil');</script>";
        } else {
            throw new Exception("Failed to update borrowing");
        }
    } catch (Exception $e) {
        error_log("Edit borrowing error: " . $e->getMessage());
        echo "<script>alert('Edit Data Gagal');</script>";
    }
}
?>





