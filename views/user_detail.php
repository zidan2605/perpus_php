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

$levelText = $data['level'] == 1 ? 'Administrator' : 'User Biasa';
$levelBadge = $data['level'] == 1 ? '<span class="badge badge-success">Admin</span>' : '<span class="badge badge-info">User</span>';
?>

<div class="content-header">
    <h2><i class="fas fa-user-circle"></i> Detail User</h2>
    <div class="header-actions">
        <a href="?page=user&actions=edit&username=<?= urlencode($data['username']) ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="?page=user&actions=tampil" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="detail-container">
    <div class="detail-card">
        <div class="card-header">
            <div class="profile-section">
                <div class="profile-photo-placeholder">
                    <i class="fas fa-user fa-5x"></i>
                </div>
                
                <div class="profile-info">
                    <h3><?= Security::escapeOutput($data['nama']) ?></h3>
                    <p class="username">@<?= Security::escapeOutput($data['username']) ?></p>
                    <?= $levelBadge ?>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="info-section">
                <h4><i class="fas fa-id-card"></i> Informasi Akun</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Username</label>
                        <p><i class="fas fa-user"></i> <?= Security::escapeOutput($data['username']) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Nama Lengkap</label>
                        <p><?= Security::escapeOutput($data['nama']) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <p><i class="fas fa-envelope"></i> <?= Security::escapeOutput($data['email']) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Level Akses</label>
                        <p><?= $levelText ?> (Level <?= $data['level'] ?>)</p>
                    </div>
                    <div class="info-item full-width">
                        <label>Keterangan / Jabatan</label>
                        <p><?= Security::escapeOutput($data['ket']) ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($username === $_SESSION['username']): ?>
            <div class="info-section">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Ini adalah akun Anda yang sedang aktif
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.header-actions {
    display: flex;
    gap: 0.75rem;
}

.detail-container {
    max-width: 900px;
}

.detail-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #6366F1, #818CF8);
    padding: 2.5rem;
    color: white;
}

.profile-section {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.profile-photo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.7);
    flex-shrink: 0;
}

.profile-info h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.username {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0.75rem;
}

.badge {
    display: inline-block;
    padding: 0.5rem 1.25rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: rgba(255,255,255,0.3);
    color: white;
}

.badge-info {
    background: rgba(59, 130, 246, 0.3);
    color: white;
}

.card-body {
    padding: 2rem;
}

.info-section {
    margin-bottom: 2rem;
}

.info-section:last-child {
    margin-bottom: 0;
}

.info-section h4 {
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.info-item p {
    color: #1e293b;
    font-size: 1rem;
    margin: 0;
}

.info-item i {
    margin-right: 0.5rem;
    color: #6366F1;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-info i {
    font-size: 1.25rem;
}

@media (max-width: 768px) {
    .profile-section {
        flex-direction: column;
        text-align: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
