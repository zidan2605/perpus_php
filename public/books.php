<?php
/**
 * Books Listing Page
 */

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

Auth::startSession();

$pageTitle = 'Daftar Buku - Perpustakaan Digital';
$activePage = 'books';

// Get books from database
$db = getDB();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$query = "SELECT id, judul_buku, nama_pengarang as pengarang, penerbit, YEAR(tahun_terbit) as th_terbit, status FROM buku WHERE 1=1";

if (!empty($search)) {
    $search = $db->real_escape_string($search);
    $query .= " AND (judul_buku LIKE '%$search%' OR nama_pengarang LIKE '%$search%' OR penerbit LIKE '%$search%')";
}

if (!empty($kategori)) {
    $kategori = $db->real_escape_string($kategori);
    $query .= " AND kategori = '$kategori'";
}

$query .= " ORDER BY id DESC";
$result = $db->query($query);

include __DIR__ . '/../templates/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-books"></i> Daftar Buku</h1>
        <p>Koleksi buku perpustakaan digital</p>
    </div>
</section>

<section class="books-section">
    <div class="container">
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Cari judul, pengarang, penerbit..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="books-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if ($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['judul_buku'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['pengarang'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['penerbit'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['th_terbit'] ?? '') ?></td>
                        <td>
                            <?php 
                            $status = $row['status'] ?? 'Tidak Ada';
                            if($status == 'Ada'): 
                            ?>
                                <span class="status-badge available">Tersedia</span>
                            <?php else: ?>
                                <span class="status-badge borrowed">Dipinjam</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data buku</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
.books-section { 
    padding: 0 0 3rem;
    min-height: 60vh;
}
.search-bar {
    margin-bottom: 2rem;
}
.search-bar form {
    display: flex;
    gap: 1rem;
    max-width: 600px;
    margin: 0 auto;
}
.search-bar input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
}
.search-bar button {
    padding: 0.75rem 2rem;
    background: #6366F1;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}
.search-bar button:hover {
    background: #4F46E5;
    transform: translateY(-2px);
}
.table-responsive {
    overflow-x: auto;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.books-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 0.95rem;
}
.books-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.books-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.books-table tbody tr {
    transition: all 0.2s;
}
.books-table tbody tr:hover {
    background: #f8fafc;
    transform: scale(1.01);
}
.books-table th:nth-child(1),
.books-table td:nth-child(1) {
    width: 50px;
    text-align: center;
    font-weight: 600;
}
.books-table th:nth-child(2),
.books-table td:nth-child(2) {
    width: 25%;
    font-weight: 600;
    color: #1e293b;
}
.books-table th:nth-child(3),
.books-table td:nth-child(3) {
    width: 18%;
}
.books-table th:nth-child(4),
.books-table td:nth-child(4) {
    width: 15%;
}
.books-table th:nth-child(5),
.books-table td:nth-child(5) {
    width: 10%;
    text-align: center;
}
.books-table th:nth-child(6),
.books-table td:nth-child(6) {
    width: 15%;
    text-align: center;
}
.status-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.status-badge.available {
    background: #dcfce7;
    color: #166534;
}
.status-badge.borrowed {
    background: #fee2e2;
    color: #991b1b;
}
.badge {
    background: #e0e7ff;
    color: #3730a3;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 1.8rem;
    }
    .books-table {
        font-size: 0.85rem;
    }
    .books-table th,
    .books-table td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>


