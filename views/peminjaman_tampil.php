<?php
Security::requireAuth();
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fas fa-handshake"></i> Riwayat Peminjaman</span>
        <a href="?page=peminjaman&actions=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Peminjaman</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Judul Buku</th>
                        <th>Peminjam</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM peminjaman ORDER BY id DESC";
                    $query = mysqli_query($koneksi, $sql) or die("SQL Error: " . mysqli_error($koneksi));
                    $nomor = 0;
                    if (mysqli_num_rows($query) === 0) {
                        echo '<tr><td colspan="7" style="text-align: center;">Belum ada data peminjaman.</td></tr>';
                    } else {
                        while ($data = mysqli_fetch_array($query)) {
                            $nomor++;
                            
                            // Check if there's a status column, otherwise determine by date
                            if (isset($data['status'])) {
                                $is_returned = ($data['status'] === 'Sudah Kembali');
                                $status_text = $data['status'];
                            } else {
                                // Status ditentukan dari apakah buku sudah dikembalikan (lewat form kembalikan)
                                // Bukan dari tgl_kembali karena itu adalah tanggal yang direncanakan
                                $is_returned = false; // Default belum kembali untuk data baru
                                $status_text = 'Belum Kembali';
                                
                                // Cek apakah ada penanda buku sudah dikembalikan
                                // Jika ket mengandung "DIKEMBALIKAN" atau field tertentu
                                if (isset($data['tanggal_dikembalikan']) && !empty($data['tanggal_dikembalikan'])) {
                                    $is_returned = true;
                                    $status_text = 'Sudah Kembali';
                                }
                            }
                            
                            // Cek jika sudah lewat tanggal kembali
                            $tgl_kembali_plan = $data['tgl_kembali'];
                            $is_overdue = false;
                            if (!$is_returned && !empty($tgl_kembali_plan) && $tgl_kembali_plan !== '0000-00-00') {
                                $today = date('Y-m-d');
                                if ($today > $tgl_kembali_plan) {
                                    $is_overdue = true;
                                }
                            }
                    ?>
                            <tr>
                                <td><?= $nomor ?></td>
                                <td><?= Security::escapeOutput($data['judul_buku']) ?></td>
                                <td><?= Security::escapeOutput($data['peminjam']) ?></td>
                                <td><?= date('d-m-Y', strtotime($data['tgl_pinjam'])) ?></td>
                                <td>
                                    <?php if (!empty($data['tgl_kembali']) && $data['tgl_kembali'] !== '0000-00-00'): ?>
                                        <?= date('d-m-Y', strtotime($data['tgl_kembali'])) ?>
                                        <?php if ($is_overdue && !$is_returned): ?>
                                            <span class="badge badge-danger" style="margin-left: 0.5rem;">Terlambat</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $is_returned ? 'badge-success' : ($is_overdue ? 'badge-danger' : 'badge-warning') ?>">
                                        <?= Security::escapeOutput($status_text) ?>
                                    </span>
                                </td>
                                <td style="display: flex; gap: 0.5rem;">
                                    <?php if (!$is_returned): ?>
                                        <a href="?page=peminjaman&actions=kembaliBuku&id=<?= $data['id'] ?>" class="btn btn-success" title="Proses Pengembalian" onclick="return confirm('Anda yakin buku ini sudah dikembalikan?')">
                                            <i class="fas fa-undo-alt"></i> Kembalikan
                                        </a>
                                    <?php endif; ?>
                                    <a href="?page=peminjaman&actions=detail&id=<?= $data['id'] ?>" class="btn" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="../actions/peminjaman_delete.php?id=<?= $data['id'] ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
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

