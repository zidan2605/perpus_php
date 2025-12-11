<?php
require '../config/security.php';
require '../src/db.php';
$koneksi = getDB();
$id = (int)$_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM peminjaman WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$status = isset($data['status']) && $data['status'] === 'Sudah Kembali' ? 'Sudah Kembali' : 'Belum Kembali';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Peminjaman</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 3px solid #333; }
        .header h2 { font-size: 22px; color: #333; margin-bottom: 5px; }
        .detail-table { width: 100%; max-width: 600px; margin: 0 auto; }
        .detail-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .detail-table td:first-child { font-weight: bold; width: 200px; color: #555; }
        @media print { body { padding: 15px; } }
    </style>
</head>
<body onload="print()">
    <div class="header">
        <h2>Detail Peminjaman Perpustakaan Digital</h2>
        <p style="font-size: 13px; color: #666;">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>
    </div>
    <table class="detail-table">
        <tr><td>Judul Buku</td><td><?= Security::escapeOutput($data['judul_buku']) ?></td></tr>
        <tr><td>Nama Peminjam</td><td><?= Security::escapeOutput($data['peminjam']) ?></td></tr>
        <tr><td>Tanggal Pinjam</td><td><?= date('d F Y', strtotime($data['tgl_pinjam'])) ?></td></tr>
        <tr><td>Tanggal Kembali</td><td><?= $status === 'Sudah Kembali' ? date('d F Y', strtotime($data['tgl_kembali'])) : '-' ?></td></tr>
        <tr><td>Lama Pinjam</td><td><?= Security::escapeOutput($data['lama_pinjam']) ?> hari</td></tr>
        <tr><td>Status</td><td><strong><?= $status ?></strong></td></tr>
    </table>
</body>
</html>
