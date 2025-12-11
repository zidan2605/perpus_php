<?php
Security::requireAuth();
?>

<div class="content-header">
    <h2><i class="fas fa-bullhorn"></i> Manajemen Pengumuman</h2>
    <a href="?page=pengumuman&actions=tambah" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Pengumuman
    </a>
</div>

<?php
// Display success/error messages
if (isset($_GET['success'])) {
    $messages = [
        'added' => 'Pengumuman berhasil ditambahkan!',
        'updated' => 'Pengumuman berhasil diperbarui!',
        'deleted' => 'Pengumuman berhasil dihapus!'
    ];
    $msg = $messages[$_GET['success']] ?? 'Operasi berhasil!';
    echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> $msg</div>";
}

if (isset($_GET['error'])) {
    $errors = [
        'invalid_id' => 'ID pengumuman tidak valid!',
        'delete_failed' => 'Gagal menghapus pengumuman!',
        'not_found' => 'Pengumuman tidak ditemukan!',
        'unauthorized' => 'Anda tidak memiliki akses untuk mengedit/menghapus pengumuman ini!'
    ];
    $err = $errors[$_GET['error']] ?? 'Terjadi kesalahan!';
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> $err</div>";
}

// Pagination settings
$perPage = 5;
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page_num - 1) * $perPage;

// Search functionality
$search = isset($_GET['search']) ? Security::sanitizeInput($_GET['search']) : '';

// Build query
$whereClause = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $whereClause .= " AND (title LIKE ? OR content LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
    $types = "ss";
}

// Count total records
$countQuery = "SELECT COUNT(*) as total FROM pengumuman $whereClause";
$stmt = $koneksi->prepare($countQuery);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalRecords = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $perPage);

// Fetch data with pagination
$query = "SELECT p.*, u.nama as creator_name 
          FROM pengumuman p 
          LEFT JOIN user u ON p.created_by = u.username 
          $whereClause 
          ORDER BY p.created_at DESC 
          LIMIT ? OFFSET ?";

$params[] = $perPage;
$params[] = $offset;
$types .= "ii";

$stmt = $koneksi->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="table-controls">
    <form method="GET" action="" class="search-form">
        <input type="hidden" name="page" value="pengumuman">
        <input type="hidden" name="actions" value="tampil">
        <div class="search-group">
            <input type="text" name="search" placeholder="Cari judul atau konten pengumuman..." 
                   value="<?= htmlspecialchars($search) ?>" class="form-control">
            <button type="submit" class="btn btn-search">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if (!empty($search)): ?>
                <a href="?page=pengumuman&actions=tampil" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="announcements-grid">
    <?php 
    if ($result->num_rows > 0):
        while($row = $result->fetch_assoc()): 
            $canModify = ($row['created_by'] === $_SESSION['username']) || ($_SESSION['level'] == 1);
            
            // Debug info - AKTIFKAN untuk troubleshooting
            echo "<!-- DEBUG: created_by='{$row['created_by']}', session_username='{$_SESSION['username']}', level={$_SESSION['level']}, canModify=" . ($canModify ? 'YES' : 'NO') . " -->";
    ?>
        <div class="announcement-card">
            <div class="announcement-header">
                <h3><?= Security::escapeOutput($row['title']) ?></h3>
                <div class="announcement-actions">
                    <?php if ($canModify): ?>
                    <a href="?page=pengumuman&actions=edit&id=<?= $row['id'] ?>" 
                       class="btn-action btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="../actions/pengumuman_delete.php?id=<?= $row['id'] ?>" 
                       class="btn-action btn-danger" 
                       onclick="return confirm('Yakin ingin menghapus pengumuman ini?')" 
                       title="Hapus">
                        <i class="fas fa-trash"></i>
                    </a>
                    <?php else: ?>
                    <span class="badge badge-info">Dibuat oleh: <?= Security::escapeOutput($row['created_by']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="announcement-meta">
                <span><i class="fas fa-user"></i> <?= Security::escapeOutput($row['creator_name'] ?? $row['created_by']) ?></span>
                <span><i class="fas fa-clock"></i> <?= date('d F Y, H:i', strtotime($row['created_at'])) ?></span>
            </div>
            <div class="announcement-content">
                <?= nl2br(Security::escapeOutput($row['content'])) ?>
            </div>
        </div>
    <?php 
        endwhile;
    else:
    ?>
        <div class="empty-state">
            <i class="fas fa-bullhorn fa-3x"></i>
            <p><?= !empty($search) ? 'Tidak ada pengumuman yang sesuai dengan pencarian' : 'Belum ada pengumuman' ?></p>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($page_num > 1): ?>
        <a href="?page=pengumuman&actions=tampil&p=<?= $page_num - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-link">
            <i class="fas fa-chevron-left"></i> Prev
        </a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page_num): ?>
            <span class="page-link active"><?= $i ?></span>
        <?php else: ?>
            <a href="?page=pengumuman&actions=tampil&p=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-link"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
    
    <?php if ($page_num < $totalPages): ?>
        <a href="?page=pengumuman&actions=tampil&p=<?= $page_num + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-link">
            Next <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>

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
    margin-bottom: 2rem;
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

.btn-search {
    background: linear-gradient(135deg, #6366F1, #818CF8);
    white-space: nowrap;
}

.announcements-grid {
    display: grid;
    gap: 1.5rem;
}

.announcement-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.announcement-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.announcement-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
    gap: 1rem;
}

.announcement-header h3 {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.announcement-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.announcement-actions .badge {
    padding: 0.5rem 1rem;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 6px;
    font-size: 0.85rem;
    white-space: nowrap;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: white;
    border: 2px solid #e2e8f0;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s;
    cursor: pointer;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-action.btn-warning {
    border-color: #fbbf24;
    color: #f59e0b;
}

.btn-action.btn-warning:hover {
    background: #fef3c7;
    border-color: #f59e0b;
}

.btn-action.btn-danger {
    border-color: #f87171;
    color: #ef4444;
}

.btn-action.btn-danger:hover {
    background: #fee2e2;
    border-color: #ef4444;
}

.announcement-meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #64748b;
}

.announcement-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.announcement-content {
    color: #475569;
    line-height: 1.7;
    font-size: 0.95rem;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.page-link {
    padding: 0.5rem 1rem;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 500;
}

.page-link:hover {
    background: #f8fafc;
    border-color: #6366F1;
    color: #6366F1;
}

.page-link.active {
    background: linear-gradient(135deg, #6366F1, #818CF8);
    color: white;
    border-color: #6366F1;
}

.empty-state {
    padding: 4rem 2rem;
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
