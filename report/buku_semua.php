<?php
require '../config/security.php';
require '../src/db.php';
$koneksi = getDB();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Buku</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #333; }
        .header h2 { font-size: 24px; color: #333; margin-bottom: 5px; text-transform: uppercase; }
        .header p { font-size: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table thead { background: #f5f5f5; }
        table th { padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold; color: #333; border: 1px solid #ddd; }
        table td { padding: 10px 8px; font-size: 11px; border: 1px solid #ddd; color: #333; }
        table tbody tr:nth-child(even) { background: #fafafa; }
        .no-col { width: 40px; text-align: center; }
        .status-col { width: 80px; text-align: center; }
        @media print { body { padding: 10px; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Data Buku Perpustakaan Digital</h2>
        <p>Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Loker</th>
                <th class="status-col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM buku ORDER BY judul_buku ASC";
            $query = mysqli_query($koneksi, $sql);
            $nomor = 0;
            if (mysqli_num_rows($query) === 0) {
                echo '<tr><td colspan="7" style="text-align: center;">Belum ada data buku</td></tr>';
            } else {
                while ($data = mysqli_fetch_array($query)) {
                    $nomor++;
                    $status = $data['status'] === 'Ada' ? 'Tersedia' : 'Dipinjam';
            ?>
                <tr>
                    <td class="no-col"><?= $nomor ?></td>
                    <td><?= Security::escapeOutput($data['judul_buku']) ?></td>
                    <td><?= Security::escapeOutput($data['nama_pengarang']) ?></td>
                    <td><?= Security::escapeOutput($data['penerbit']) ?></td>
                    <td><?= Security::escapeOutput($data['tahun_terbit']) ?></td>
                    <td><?= Security::escapeOutput($data['loker_buku']) ?></td>
                    <td class="status-col"><?= $status ?></td>
                </tr>
            <?php } } ?>
        </tbody>
    </table>
</body>
</html>
