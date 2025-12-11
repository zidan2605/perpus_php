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
$jk = $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan';
$ttl = $data['tempat_lahir'] . ', ' . date('d F Y', strtotime($data['tanggal_lahir']));
$umur = date_diff(date_create($data['tanggal_lahir']), date_create('today'))->y;
?>

<div class="content-header">
    <h2><i class="fas fa-user-circle"></i> Detail Anggota</h2>
    <div class="header-actions">
        <a href="index_admin.php?page=anggota&actions=edit&id=<?= $data['id'] ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="index_admin.php?page=anggota&actions=tampil" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="detail-container">
    <div class="detail-card">
        <div class="card-header">
            <div class="profile-section">
                <?php if (!empty($data['foto']) && file_exists(__DIR__ . '/../uploads/anggota/' . $data['foto'])): ?>
                    <img src="../uploads/anggota/<?= Security::escapeOutput($data['foto']) ?>" 
                         alt="Foto <?= Security::escapeOutput($data['nama_lengkap']) ?>" 
                         class="profile-photo">
                <?php else: ?>
                    <div class="profile-photo-placeholder">
                        <i class="fas fa-user fa-5x"></i>
                    </div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <h3><?= Security::escapeOutput($data['nama_lengkap']) ?></h3>
                    <p class="member-id"><?= Security::escapeOutput($data['no_anggota']) ?></p>
                    <span class="badge <?= $data['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>">
                        <?= Security::escapeOutput($data['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="info-section">
                <h4><i class="fas fa-id-card"></i> Informasi Pribadi</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Jenis Kelamin</label>
                        <p><?= $jk ?></p>
                    </div>
                    <div class="info-item">
                        <label>Tempat, Tanggal Lahir</label>
                        <p><?= Security::escapeOutput($ttl) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Umur</label>
                        <p><?= $umur ?> tahun</p>
                    </div>
                    <div class="info-item">
                        <label>Pekerjaan</label>
                        <p><?= Security::escapeOutput($data['pekerjaan']) ?: '-' ?></p>
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h4><i class="fas fa-map-marker-alt"></i> Informasi Kontak</h4>
                <div class="info-grid">
                    <div class="info-item full-width">
                        <label>Alamat</label>
                        <p><?= nl2br(Security::escapeOutput($data['alamat'])) ?></p>
                    </div>
                    <div class="info-item">
                        <label>No Telepon</label>
                        <p><i class="fas fa-phone"></i> <?= Security::escapeOutput($data['no_telepon']) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <p>
                            <?php if (!empty($data['email'])): ?>
                                <i class="fas fa-envelope"></i> <?= Security::escapeOutput($data['email']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h4><i class="fas fa-calendar-check"></i> Informasi Keanggotaan</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Tanggal Daftar</label>
                        <p><?= date('d F Y', strtotime($data['tanggal_daftar'])) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Lama Menjadi Anggota</label>
                        <p>
                            <?php
                            $diff = date_diff(date_create($data['tanggal_daftar']), date_create('today'));
                            $years = $diff->y;
                            $months = $diff->m;
                            $days = $diff->d;
                            
                            $durasi = [];
                            if ($years > 0) $durasi[] = "$years tahun";
                            if ($months > 0) $durasi[] = "$months bulan";
                            if ($days > 0) $durasi[] = "$days hari";
                            
                            echo !empty($durasi) ? implode(', ', $durasi) : 'Baru bergabung';
                            ?>
                        </p>
                    </div>
                    <div class="info-item full-width">
                        <label>Keterangan</label>
                        <p><?= nl2br(Security::escapeOutput($data['keterangan'])) ?: '-' ?></p>
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h4><i class="fas fa-clock"></i> Informasi Sistem</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Dibuat Pada</label>
                        <p><?= date('d F Y H:i', strtotime($data['created_at'])) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Terakhir Diupdate</label>
                        <p><?= date('d F Y H:i', strtotime($data['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.header-actions {
    display: flex;
    gap: 0.75rem;
}

.detail-container {
    max-width: 1000px;
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

.profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 12px;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.profile-photo-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.5);
}

.profile-info h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.member-id {
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

.badge-danger {
    background: rgba(239, 68, 68, 0.3);
    color: white;
}

.card-body {
    padding: 2rem;
}

.info-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.info-section:last-child {
    border-bottom: none;
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
