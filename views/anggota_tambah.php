<?php
if (!defined('SECURE_ACCESS')) die('Direct access not permitted');
?>

<div class="content-header">
    <h2><i class="fas fa-user-plus"></i> Tambah Anggota Baru</h2>
    <a href="index_admin.php?page=anggota&actions=tampil" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php
// Generate nomor anggota otomatis
$query = "SELECT no_anggota FROM anggota ORDER BY id DESC LIMIT 1";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastNo = $row['no_anggota'];
    $num = (int)substr($lastNo, 3) + 1;
    $newNo = 'AGT' . str_pad($num, 3, '0', STR_PAD_LEFT);
} else {
    $newNo = 'AGT001';
}
?>

<div class="form-container">
    <form method="POST" action="../actions/anggota_proses.php" enctype="multipart/form-data" class="form-modern">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        
        <div class="form-section">
            <h3><i class="fas fa-id-card"></i> Informasi Identitas</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="no_anggota">No Anggota <span class="required">*</span></label>
                    <input type="text" id="no_anggota" name="no_anggota" class="form-control" 
                           value="<?= $newNo ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" 
                           required placeholder="Masukkan nama lengkap">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pekerjaan">Pekerjaan</label>
                    <input type="text" id="pekerjaan" name="pekerjaan" class="form-control" 
                           placeholder="Masukkan pekerjaan">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tempat_lahir">Tempat Lahir <span class="required">*</span></label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control" 
                           required placeholder="Masukkan tempat lahir">
                </div>
                
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir <span class="required">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-address-book"></i> Informasi Kontak</h3>
            
            <div class="form-group">
                <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" 
                          required placeholder="Masukkan alamat lengkap"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="no_telepon">No Telepon <span class="required">*</span></label>
                    <input type="text" id="no_telepon" name="no_telepon" class="form-control" 
                           required placeholder="Contoh: 081234567890">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="contoh@email.com">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-cog"></i> Pengaturan Anggota</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal_daftar">Tanggal Daftar <span class="required">*</span></label>
                    <input type="date" id="tanggal_daftar" name="tanggal_daftar" class="form-control" 
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Aktif" selected>Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="foto">Foto Anggota</label>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                <small class="form-text">Format: JPG, PNG, GIF (Max: 2MB)</small>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="3" 
                          placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Data
            </button>
            <a href="index_admin.php?page=anggota&actions=tampil" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<style>
.form-container {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #334155;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #6366F1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.85rem;
    color: #64748b;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1.5rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Validasi nomor telepon
document.getElementById('no_telepon').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9+]/g, '');
});
</script>
