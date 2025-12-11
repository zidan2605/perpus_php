<?php
Security::requireAuth();
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fas fa-book"></i> Data Buku</span>
        <a href="?page=buku&actions=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Buku</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Loker</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM buku ORDER BY id DESC";
                    $query = mysqli_query($koneksi, $sql) or die("SQL Error: " . mysqli_error($koneksi));
                    $nomor = 0;
                    while ($data = mysqli_fetch_array($query)) {
                        $nomor++;
                    ?>
                        <tr>
                            <td><?= $nomor ?></td>
                            <td><?= Security::escapeOutput($data['judul_buku']) ?></td>
                            <td><?= Security::escapeOutput($data['nama_pengarang']) ?></td>
                            <td><?= Security::escapeOutput($data['loker_buku']) ?></td>
                            <td><?= Security::escapeOutput($data['tahun_terbit']) ?></td>
                            <td>
                                <span class="badge <?= $data['status'] == 'Ada' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= Security::escapeOutput($data['status']) ?>
                                </span>
                            </td>
                            <td style="display: flex; gap: 0.5rem;">
                                <a href="?page=buku&actions=detail&id=<?= $data['id'] ?>" class="btn" title="Detail"><i class="fas fa-eye"></i></a>
                                <a href="?page=buku&actions=edit&id=<?= $data['id'] ?>" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <?php if ($data['status'] == 'Ada'): ?>
                                    <a href="?page=peminjaman&actions=tambah&judulbuku=<?= urlencode($data['judul_buku']) ?>" class="btn btn-success" title="Pinjam">
                                        <i class="fas fa-hand-holding-hand"></i> Pinjam
                                    </a>
                                <?php endif; ?>
                                <a href="../actions/buku_delete.php?id=<?= $data['id'] ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

