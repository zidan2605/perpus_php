<?php
if (!defined('SECURE_ACCESS')) die('Direct access not permitted');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index_admin.php?page=anggota&actions=tampil&error=invalid_id");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM anggota WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index_admin.php?page=anggota&actions=tampil&error=not_found");
    exit;
}

$data = $result->fetch_assoc();
?>

<div class="content-header">
    <h2><i class="fas fa-user-edit"></i> Edit Data Anggota</h2>
    <a href="index_admin.php?page=anggota&actions=tampil" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="form-container">
    <form method="POST" action="../actions/anggota_proses.php" enctype="multipart/form-data" class="form-modern">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="old_foto" value="<?= $data['foto'] ?>">
        
        <div class="form-section">
            <h3><i class="fas fa-id-card"></i> Informasi Identitas</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="no_anggota">No Anggota <span class="required">*</span></label>
                    <input type="text" id="no_anggota" name="no_anggota" class="form-control" 
                           value="<?= Security::escapeOutput($data['no_anggota']) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" 
                           value="<?= Security::escapeOutput($data['nama_lengkap']) ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                        <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pekerjaan">Pekerjaan</label>
                    <input type="text" id="pekerjaan" name="pekerjaan" class="form-control" 
                           value="<?= Security::escapeOutput($data['pekerjaan']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tempat_lahir">Tempat Lahir <span class="required">*</span></label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control" 
                           value="<?= Security::escapeOutput($data['tempat_lahir']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir <span class="required">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" 
                           value="<?= $data['tanggal_lahir'] ?>" required>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-address-book"></i> Informasi Kontak</h3>
            
            <div class="form-group">
                <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= Security::escapeOutput($data['alamat']) ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="no_telepon">No Telepon <span class="required">*</span></label>
                    <input type="text" id="no_telepon" name="no_telepon" class="form-control" 
                           value="<?= Security::escapeOutput($data['no_telepon']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= Security::escapeOutput($data['email']) ?>">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-cog"></i> Pengaturan Anggota</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal_daftar">Tanggal Daftar <span class="required">*</span></label>
                    <input type="date" id="tanggal_daftar" name="tanggal_daftar" class="form-control" 
                           value="<?= $data['tanggal_daftar'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Aktif" <?= $data['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Nonaktif" <?= $data['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="foto">Foto Anggota</label>
                <?php if (!empty($data['foto']) && file_exists(__DIR__ . '/../uploads/anggota/' . $data['foto'])): ?>
                    <div class="current-photo">
                        <img src="../uploads/anggota/<?= Security::escapeOutput($data['foto']) ?>" alt="Foto Anggota">
                        <p>Foto saat ini</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                <small class="form-text">Format: JPG, PNG, GIF (Max: 2MB). Kosongkan jika tidak ingin mengubah foto.</small>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="3"><?= Security::escapeOutput($data['keterangan']) ?></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Data
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

.current-photo {
    margin-bottom: 1rem;
    text-align: center;
}

.current-photo img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 0.5rem;
}

.current-photo p {
    color: #64748b;
    font-size: 0.9rem;
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
