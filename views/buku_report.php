<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-alt"></i>
            <span>Laporan Data Buku</span>
        </div>
        <a href="../report/buku_semua.php" target="_blank" class="btn btn-primary btn-sm">
            <i class="fas fa-print"></i> Cetak Semua
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM buku ORDER BY id DESC";
                    $query = mysqli_query($koneksi, $sql) or die("SQL Error: " . mysqli_error($koneksi));
                    $nomor = 0;
                    if (mysqli_num_rows($query) === 0) {
                        echo '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray-600);">Belum ada data buku.</td></tr>';
                    } else {
                        while ($data = mysqli_fetch_array($query)) {
                            $nomor++;
                            $statusClass = $data['status'] === 'Ada' ? 'badge-success' : 'badge-warning';
                            $statusText = $data['status'] === 'Ada' ? 'Tersedia' : 'Dipinjam';
                    ?>
                            <tr>
                                <td style="text-align: center;"><?= $nomor ?></td>
                                <td><?= Security::escapeOutput($data['judul_buku']) ?></td>
                                <td><?= Security::escapeOutput($data['nama_pengarang']) ?></td>
                                <td><?= Security::escapeOutput($data['penerbit']) ?></td>
                                <td style="text-align: center;"><?= Security::escapeOutput($data['tahun_terbit']) ?></td>
                                <td style="text-align: center;">
                                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="../report/buku_satu.php?id=<?= $data['id'] ?>" target="_blank" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;" title="Cetak Detail">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
