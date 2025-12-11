<?php Security::requireAuth(); ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-book-medical"></i> Form Tambah Data Buku
    </div>
    <div class="card-body">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Loker Buku</label>
                        <select name="loker_buku" class="form-control" required>
                            <option value="">-- Pilih Loker --</option>
                            <option value="Buku Anak Anak">Buku Anak Anak</option>
                            <option value="Buku Dongeng">Buku Dongeng</option>
                            <option value="Buku Majalah">Buku Majalah</option>
                            <option value="Buku Novel">Buku Novel</option>
                            <option value="Buku Pembelajaran">Buku Pembelajaran</option>
                            <option value="Buku Resep Masakan">Buku Resep Masakan</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Ada">Ada</option>
                            <option value="Dipinjam">Dipinjam</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nomor Rak</label>
                        <input type="text" name="no_rak" class="form-control" placeholder="Contoh: A1" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nomor Laci</label>
                        <input type="text" name="no_laci" class="form-control" placeholder="Contoh: 2" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nomor Boks</label>
                        <input type="text" name="no_boks" class="form-control" placeholder="Contoh: B3" required>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Judul Buku</label>
                <input type="text" name="judul_buku" class="form-control" placeholder="Masukkan judul buku" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nama Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" placeholder="Nama pengarang" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Penerbit Buku</label>
                        <input type="text" name="penerbit" class="form-control" placeholder="Nama penerbit" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tahun Terbit</label>
                        <input type="date" name="tahun_terbit" class="form-control" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Penerima Buku</label>
                        <input type="text" name="penerima" class="form-control" placeholder="Nama penerima" required>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Keterangan</label>
                <textarea name="ket" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)" style="resize: vertical;"></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Data Buku
                </button>
                <a href="?page=buku&actions=tampil" class="btn btn-danger">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.row { display: flex; flex-wrap: wrap; margin: 0 -0.75rem; }
.col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; padding: 0 0.75rem; }
.col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 0 0.75rem; }
@media (max-width: 768px) {
    .col-md-4, .col-md-6 { flex: 0 0 100%; max-width: 100%; }
}
</style>

<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>alert('Token keamanan tidak valid!');</script>";
        exit;
    }
    
    try {
        $loker_buku = Security::sanitizeInput($_POST['loker_buku']);
        $rak = Security::sanitizeInput($_POST['no_rak']);
        $laci = Security::sanitizeInput($_POST['no_laci']);
        $boks = Security::sanitizeInput($_POST['no_boks']);
        $judulbuku = Security::sanitizeInput($_POST['judul_buku']);
        $pengarang = Security::sanitizeInput($_POST['pengarang']);
        $tahun_terbit = Security::sanitizeInput($_POST['tahun_terbit']);
        $penerbit = Security::sanitizeInput($_POST['penerbit']);
        $penerima = Security::sanitizeInput($_POST['penerima']);
        $status = Security::sanitizeInput($_POST['status']);
        $ket = Security::sanitizeInput($_POST['ket']);
        
        $stmt = $koneksi->prepare("INSERT INTO buku (loker_buku, no_rak, no_laci, no_boks, judul_buku, nama_pengarang, tahun_terbit, penerima, penerbit, status, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $loker_buku, $rak, $laci, $boks, $judulbuku, $pengarang, $tahun_terbit, $penerima, $penerbit, $status, $ket);
        
        if($stmt->execute()) {
            Security::logActivity("Added new book: $judulbuku");
            echo "<script>window.location.assign('?page=buku&actions=tampil');</script>";
        } else {
            throw new Exception("Failed to save book");
        }
    } catch (Exception $e) {
        error_log("Add book error: " . $e->getMessage());
        echo "<script>alert('Simpan Data Gagal');</script>";
    }
}
?>



