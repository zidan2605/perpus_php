<?php
Security::requireAuth();

// Only admin should be able to see this page
if ($_SESSION['level'] != 1) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.assign('index_admin.php');</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = Security::sanitizeInput($_POST['username']);
        $password = $_POST['password']; // Don't sanitize password
        $nama = Security::sanitizeInput($_POST['nama']);
        $email = Security::sanitizeInput($_POST['email']);
        $level = (int)$_POST['level'];
        $ket = Security::sanitizeInput($_POST['ket']);

        if (empty($username) || empty($password) || empty($nama) || empty($email) || empty($ket)) {
            echo "<script>alert('Semua field wajib diisi.');</script>";
        } else {
            // Check if username already exists
            $checkStmt = $koneksi->prepare("SELECT username FROM user WHERE username = ?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                echo "<script>alert('Username sudah digunakan!'); window.location.assign('?page=user&actions=tambah&error=username_exists');</script>";
                exit;
            }
            
            // Hash password with Security class
            $hashedPassword = Security::hashPassword($password);
            
            $insertStmt = $koneksi->prepare("INSERT INTO user (username, paswd, nama, email, level, ket) VALUES (?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("ssssis", $username, $hashedPassword, $nama, $email, $level, $ket);
            
            if ($insertStmt->execute()) {
                Security::logActivity("Added new user: $username ($nama)");
                echo "<script>alert('User berhasil ditambahkan!'); window.location.assign('?page=user&actions=tampil&success=added');</script>";
            } else {
                throw new Exception("Gagal menambahkan user.");
            }
        }
    } catch (Exception $e) {
        error_log("Add user error: " . $e->getMessage());
        echo "<script>alert('Tambah User Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="content-header">
    <h2><i class="fas fa-user-plus"></i> Tambah User Baru</h2>
    <a href="?page=user&actions=tampil" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="form-container">
    <form method="POST" action="?page=user&actions=tambah" class="form-modern">
        
        <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Informasi Akun</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" 
                           required placeholder="Masukkan username" autocomplete="off">
                    <small class="form-text">Username untuk login, tidak dapat diubah setelah dibuat</small>
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" 
                           required placeholder="Masukkan password" autocomplete="new-password" minlength="6">
                    <small class="form-text">Minimal 6 karakter</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nama">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control" 
                           required placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           required placeholder="contoh@email.com">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-shield-alt"></i> Hak Akses</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="level">Level User <span class="required">*</span></label>
                    <select id="level" name="level" class="form-control" required>
                        <option value="">-- Pilih Level --</option>
                        <option value="1">Admin (Level 1)</option>
                        <option value="0">User Biasa (Level 0)</option>
                    </select>
                    <small class="form-text">Admin memiliki akses penuh sistem</small>
                </div>
                
                <div class="form-group">
                    <label for="ket">Keterangan / Jabatan <span class="required">*</span></label>
                    <input type="text" id="ket" name="ket" class="form-control" 
                           required placeholder="Contoh: Staff Perpustakaan, Admin">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan User
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
// Show password toggle
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const levelSelect = document.getElementById('level');
    const ketInput = document.getElementById('ket');
    
    // Auto-fill keterangan based on level
    levelSelect.addEventListener('change', function() {
        if (this.value == '1' && ketInput.value === '') {
            ketInput.value = 'Administrator';
        } else if (this.value == '0' && ketInput.value === '') {
            ketInput.value = 'Staff Perpustakaan';
        }
    });
});
</script>
