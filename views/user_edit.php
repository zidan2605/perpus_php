<?php
Security::requireAuth();

// Only admin should be able to see this page
if ($_SESSION['level'] != 1) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.assign('index_admin.php');</script>";
    exit;
}

// Use username as primary key
if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo "<script>alert('Username tidak valid'); window.location.assign('?page=user&actions=tampil');</script>";
    exit;
}

$username = Security::sanitizeInput($_GET['username']);

$stmt = $koneksi->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data pengguna tidak ditemukan'); window.location.assign('?page=user&actions=tampil');</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nama = Security::sanitizeInput($_POST['nama']);
        $email = Security::sanitizeInput($_POST['email']);
        $level = (int)$_POST['level'];
        $ket = Security::sanitizeInput($_POST['ket']);
        $new_password = $_POST['new_password'] ?? '';

        if (empty($nama) || empty($email) || empty($ket)) {
             echo "<script>alert('Semua field wajib diisi.');</script>";
        } else {
            // Update query with or without password
            if (!empty($new_password)) {
                $hashedPassword = Security::hashPassword($new_password);
                $updateStmt = $koneksi->prepare("UPDATE user SET nama = ?, email = ?, level = ?, ket = ?, paswd = ? WHERE username = ?");
                $updateStmt->bind_param("ssisss", $nama, $email, $level, $ket, $hashedPassword, $username);
            } else {
                $updateStmt = $koneksi->prepare("UPDATE user SET nama = ?, email = ?, level = ?, ket = ? WHERE username = ?");
                $updateStmt->bind_param("ssisss", $nama, $email, $level, $ket, $username);
            }
            
            if ($updateStmt->execute()) {
                Security::logActivity("Updated user: $username");
                echo "<script>alert('Data pengguna berhasil diperbarui.'); window.location.assign('?page=user&actions=tampil&success=updated');</script>";
            } else {
                throw new Exception("Gagal memperbarui data.");
            }
        }
    } catch (Exception $e) {
        error_log("Edit user error: " . $e->getMessage());
        echo "<script>alert('Edit Data Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="content-header">
    <h2><i class="fas fa-user-edit"></i> Edit Data User</h2>
    <a href="?page=user&actions=tampil" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="form-container">
    <form method="POST" action="?page=user&actions=edit&username=<?= urlencode($username) ?>" class="form-modern">
        
        <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Informasi Akun</h3>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?= Security::escapeOutput($data['username']) ?>" readonly>
                <small class="form-text">Username tidak dapat diubah</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nama">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control" 
                           value="<?= Security::escapeOutput($data['nama']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= Security::escapeOutput($data['email']) ?>" required>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-key"></i> Ubah Password (Opsional)</h3>
            
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" id="new_password" name="new_password" class="form-control" 
                       placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
                <small class="form-text">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password</small>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-shield-alt"></i> Hak Akses</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="level">Level User <span class="required">*</span></label>
                    <select id="level" name="level" class="form-control" required>
                        <option value="1" <?= $data['level'] == 1 ? 'selected' : '' ?>>Admin (Level 1)</option>
                        <option value="0" <?= $data['level'] == 0 ? 'selected' : '' ?>>User Biasa (Level 0)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="ket">Keterangan / Jabatan <span class="required">*</span></label>
                    <input type="text" id="ket" name="ket" class="form-control" 
                           value="<?= Security::escapeOutput($data['ket']) ?>" required>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="?page=user&actions=tampil" class="btn btn-secondary">
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

.form-control:read-only {
    background: #f1f5f9;
    cursor: not-allowed;
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

