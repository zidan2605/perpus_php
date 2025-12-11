<?php
/**
 * Public Announcements Page
 */

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

Auth::startSession();

$pageTitle = 'Pengumuman - Perpustakaan Digital';
$activePage = 'announcements';

// Pagination settings
$perPage = 5;
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page_num - 1) * $perPage;

// Search functionality
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

$db = getDB();

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
$stmt = $db->prepare($countQuery);
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

$stmt = $db->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

include __DIR__ . '/../templates/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-bullhorn"></i> Pengumuman</h1>
        <p>Informasi dan pengumuman terbaru dari perpustakaan</p>
    </div>
</section>

<section class="announcements-section">
    <div class="container">
        
        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Cari pengumuman..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
                <?php if (!empty($search)): ?>
                    <a href="announcements.php" class="btn-reset"><i class="fas fa-times"></i> Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Announcements List -->
        <div class="announcements-list">
            <?php 
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
            ?>
                <article class="announcement-card">
                    <div class="announcement-header">
                        <h2><?= htmlspecialchars($row['title']) ?></h2>
                        <div class="announcement-meta">
                            <span class="author">
                                <i class="fas fa-user"></i> 
                                <?= htmlspecialchars($row['creator_name'] ?? $row['created_by']) ?>
                            </span>
                            <span class="date">
                                <i class="fas fa-calendar"></i> 
                                <?= date('d F Y', strtotime($row['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <div class="announcement-content">
                        <?= nl2br(htmlspecialchars($row['content'])) ?>
                    </div>
                </article>
            <?php 
                endwhile;
            else:
            ?>
                <div class="empty-state">
                    <i class="fas fa-bullhorn fa-4x"></i>
                    <h3><?= !empty($search) ? 'Tidak ada pengumuman yang sesuai' : 'Belum ada pengumuman' ?></h3>
                    <p><?= !empty($search) ? 'Coba kata kunci lain' : 'Pengumuman akan muncul di sini' ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page_num > 1): ?>
                <a href="?p=<?= $page_num - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-btn">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </a>
            <?php endif; ?>
            
            <div class="page-numbers">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page_num): ?>
                        <span class="page-num active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?p=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-num"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            
            <?php if ($page_num < $totalPages): ?>
                <a href="?p=<?= $page_num + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-btn">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8rem 0 3rem;
    text-align: center;
    margin-bottom: 3rem;
}

.page-header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.announcements-section {
    padding: 0 0 4rem;
    min-height: 60vh;
}

.search-bar {
    margin-bottom: 2rem;
}

.search-bar form {
    display: flex;
    gap: 1rem;
    max-width: 700px;
    margin: 0 auto;
    align-items: center;
}

.search-bar input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
}

.search-bar button, .search-bar .btn-reset {
    padding: 0.75rem 1.5rem;
    background: #6366F1;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.search-bar .btn-reset {
    background: #64748b;
}

.search-bar button:hover {
    background: #4F46E5;
    transform: translateY(-2px);
}

.search-bar .btn-reset:hover {
    background: #475569;
}

.announcements-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.announcement-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.announcement-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    transform: translateY(-4px);
}

.announcement-header h2 {
    color: #1e293b;
    font-size: 1.75rem;
    margin-bottom: 1rem;
}

.announcement-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    color: #64748b;
    font-size: 0.95rem;
}

.announcement-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.announcement-content {
    color: #475569;
    line-height: 1.8;
    font-size: 1.05rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.empty-state i {
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 3rem;
}

.page-btn {
    padding: 0.75rem 1.25rem;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.page-btn:hover {
    background: #f8fafc;
    border-color: #6366F1;
    color: #6366F1;
}

.page-numbers {
    display: flex;
    gap: 0.5rem;
}

.page-num {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.page-num:hover, .page-num.active {
    background: #6366F1;
    border-color: #6366F1;
    color: white;
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .search-bar form {
        flex-direction: column;
    }
    
    .search-bar button, .search-bar .btn-reset {
        width: 100%;
    }
    
    .announcement-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>
