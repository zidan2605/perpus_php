<?php
if (!defined('SECURE_ACCESS')) die('Direct access not permitted');
?>

<div class="content-header">
    <h2><i class="fas fa-users"></i> Data Anggota Perpustakaan</h2>
    <a href="index_admin.php?page=anggota&actions=tambah" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Anggota
    </a>
</div>

<?php
// Display success/error messages
if (isset($_GET['success'])) {
    $messages = [
        'added' => 'Data anggota berhasil ditambahkan!',
        'updated' => 'Data anggota berhasil diperbarui!',
        'deleted' => 'Data anggota berhasil dihapus!'
    ];
    $msg = $messages[$_GET['success']] ?? 'Operasi berhasil!';
    echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> $msg</div>";
}

if (isset($_GET['error'])) {
    $errors = [
        'invalid_id' => 'ID anggota tidak valid!',
        'delete_failed' => 'Gagal menghapus data anggota!',
        'not_found' => 'Data anggota tidak ditemukan!'
    ];
    $err = $errors[$_GET['error']] ?? 'Terjadi kesalahan!';
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> $err</div>";
}

// Search functionality
$search = isset($_GET['search']) ? Security::sanitizeInput($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? Security::sanitizeInput($_GET['status']) : '';

$query = "SELECT * FROM anggota WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (no_anggota LIKE ? OR nama_lengkap LIKE ? OR no_telepon LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
    $types .= "ssss";
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = $koneksi->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="table-controls">
    <form method="GET" action="" class="search-form">
        <input type="hidden" name="page" value="anggota">
        <input type="hidden" name="actions" value="tampil">
        <div class="search-group">
            <input type="text" name="search" placeholder="Cari no anggota, nama, telepon, email..." 
                   value="<?= htmlspecialchars($search) ?>" class="form-control">
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="Aktif" <?= $status_filter === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Nonaktif" <?= $status_filter === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-search">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if (!empty($search) || !empty($status_filter)): ?>
                <a href="index_admin.php?page=anggota&actions=tampil" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">No Anggota</th>
                <th width="15%">Nama Lengkap</th>
                <th width="8%">JK</th>
                <th width="12%">Tempat, Tgl Lahir</th>
                <th width="12%">No Telepon</th>
                <th width="12%">Email</th>
                <th width="10%">Tgl Daftar</th>
                <th width="8%">Status</th>
                <th width="8%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
                    $jk = $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan';
                    $ttl = $row['tempat_lahir'] . ', ' . date('d-m-Y', strtotime($row['tanggal_lahir']));
                    $statusClass = $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger';
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><strong><?= Security::escapeOutput($row['no_anggota']) ?></strong></td>
                <td><?= Security::escapeOutput($row['nama_lengkap']) ?></td>
                <td><?= $jk ?></td>
                <td><?= Security::escapeOutput($ttl) ?></td>
                <td><?= Security::escapeOutput($row['no_telepon']) ?></td>
                <td><?= Security::escapeOutput($row['email']) ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_daftar'])) ?></td>
                <td>
                    <span class="badge <?= $statusClass ?>">
                        <?= Security::escapeOutput($row['status']) ?>
                    </span>
                </td>
                <td class="action-buttons">
                    <a href="index_admin.php?page=anggota&actions=detail&id=<?= $row['id'] ?>" 
                       class="btn-action btn-info" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="index_admin.php?page=anggota&actions=edit&id=<?= $row['id'] ?>" 
                       class="btn-action btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="../actions/anggota_delete.php?id=<?= $row['id'] ?>" 
                       class="btn-action btn-danger" 
                       onclick="return confirm('Yakin ingin menghapus anggota ini?')" 
                       title="Hapus">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="10" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-users fa-3x"></i>
                        <p>Belum ada data anggota</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.content-header h2 {
    color: #1e293b;
    font-size: 1.75rem;
    font-weight: 700;
}

.table-controls {
    margin-bottom: 1.5rem;
}

.search-form {
    display: flex;
    gap: 0.75rem;
}

.search-group {
    display: flex;
    gap: 0.75rem;
    flex: 1;
}

.search-group .form-control {
    flex: 1;
}

.search-group select.form-control {
    flex: 0 0 180px;
}

.btn-search {
    background: linear-gradient(135deg, #6366F1, #818CF8);
    white-space: nowrap;
}

.badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    padding: 3rem;
    text-align: center;
    color: #94a3b8;
}

.empty-state i {
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1.1rem;
    margin: 0;
}
</style>
