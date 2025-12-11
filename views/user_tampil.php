<?php
Security::requireAuth();

// Only admin should be able to see this page
if ($_SESSION['level'] != 1) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.assign('index_admin.php');</script>";
    exit;
}
?>

<div class="content-header">
    <h2><i class="fas fa-users"></i> Manajemen Pengguna</h2>
    <a href="?page=user&actions=tambah" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah User
    </a>
</div>

<?php
// Display success/error messages
if (isset($_GET['success'])) {
    $messages = [
        'added' => 'User berhasil ditambahkan!',
        'updated' => 'Data user berhasil diperbarui!',
        'deleted' => 'User berhasil dihapus!'
    ];
    $msg = $messages[$_GET['success']] ?? 'Operasi berhasil!';
    echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> $msg</div>";
}

if (isset($_GET['error'])) {
    $errors = [
        'invalid_username' => 'Username tidak valid!',
        'delete_failed' => 'Gagal menghapus user!',
        'not_found' => 'User tidak ditemukan!',
        'username_exists' => 'Username sudah digunakan!',
        'cannot_delete_self' => 'Tidak dapat menghapus akun sendiri!'
    ];
    $err = $errors[$_GET['error']] ?? 'Terjadi kesalahan!';
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> $err</div>";
}

$sql = "SELECT * FROM user ORDER BY username ASC";
$query = mysqli_query($koneksi, $sql) or die("SQL Error: " . mysqli_error($koneksi));
?>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama</th>
                <th width="15%">Username</th>
                <th width="20%">Email</th>
                <th width="15%">Level</th>
                <th width="15%">Keterangan</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $nomor = 0;
            if (mysqli_num_rows($query) === 0) {
                echo '<tr><td colspan="7" class="text-center"><div class="empty-state"><i class="fas fa-users fa-3x"></i><p>Belum ada data pengguna</p></div></td></tr>';
            } else {
                while ($data = mysqli_fetch_array($query)) {
                    $nomor++;
                    $levelBadge = $data['level'] == 1 ? '<span class="badge badge-success">Admin</span>' : '<span class="badge badge-info">User</span>';
            ?>
                    <tr>
                        <td class="text-center"><?= $nomor ?></td>
                        <td><?= Security::escapeOutput($data['nama']) ?></td>
                        <td><strong><?= Security::escapeOutput($data['username']) ?></strong></td>
                        <td><?= Security::escapeOutput($data['email']) ?></td>
                        <td><?= $levelBadge ?></td>
                        <td><?= Security::escapeOutput($data['ket']) ?></td>
                        <td class="action-buttons">
                            <a href="?page=user&actions=detail&username=<?= urlencode($data['username']) ?>" 
                               class="btn-action btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="?page=user&actions=edit&username=<?= urlencode($data['username']) ?>" 
                               class="btn-action btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($data['username'] !== $_SESSION['username']): ?>
                            <a href="../actions/user_delete.php?username=<?= urlencode($data['username']) ?>" 
                               class="btn-action btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus user ini?')" 
                               title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
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

.badge-info {
    background: #dbeafe;
    color: #1e40af;
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

