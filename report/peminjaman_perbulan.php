<?php
include '../src/db.php';
include '../config/security.php'; // Manually include for escapeOutput

$selected_month = isset($_POST['bulan']) ? (int)$_POST['bulan'] : date('m');
$selected_year = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');

$report_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_prefix = $selected_year . '-' . str_pad((string)$selected_month, 2, '0', STR_PAD_LEFT);
    $search_pattern = $date_prefix . '%';

    $stmt = $koneksi->prepare("SELECT * FROM peminjaman WHERE tgl_pinjam LIKE ? ORDER BY tgl_pinjam ASC");
    $stmt->bind_param("s", $search_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report_data[] = $row;
        }
    }
}

$months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Per Bulan</title>
    <link href="../Assets/css/report.css" rel="stylesheet" type="text/css"/>
    <link href="../Assets/css/modern.css" rel="stylesheet" type="text/css"/> <!-- For form styles -->
    <style>
        @media print {
            .form-container { display: none; }
        }
    </style>
</head>
<body>

    <div class="form-container" style="padding: 2rem; max-width: 800px; margin: 2rem auto;">
        <div class="card">
            <div class="card-header">
                Pilih Bulan dan Tahun Laporan Peminjaman
            </div>
            <div class="card-body">
                <form action="" method="post" style="display: flex; gap: 1rem; align-items: center;">
                    <div>
                        <label for="bulan">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            <?php foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= ($selected_month == $num) ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="tahun">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control">
                            <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                                <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div style="margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="report-container">
            <header class="report-header">
                <h2>Perpustakaan Digital</h2>
                <h4>Indonesia</h4>
                <hr>
                <h3>LAPORAN DATA BUKU - TAHUN <?= $selected_year ?></h3>
            </header>

            <main>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul Buku</th>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report_data)): ?>
                            <tr><td colspan="6" style="text-align: center;">Tidak ada data untuk periode yang dipilih.</td></tr>
                        <?php else: ?>
                            <?php $nomor = 0; foreach ($report_data as $data): $nomor++; ?>
                                <?php
                                    $is_returned = !empty($data['tgl_kembali']) && $data['tgl_kembali'] !== '0000-00-00';
                                    $status_text = $is_returned ? 'Sudah Kembali' : 'Belum Kembali';
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?= $nomor ?></td>
                                    <td><?= Security::escapeOutput($data['judul_buku']) ?></td>
                                    <td><?= Security::escapeOutput($data['peminjam']) ?></td>
                                    <td style="text-align: center;"><?= date('d-m-Y', strtotime($data['tgl_pinjam'])) ?></td>
                                    <td style="text-align: center;"><?= $is_returned ? date('d-m-Y', strtotime($data['tgl_kembali'])) : '-' ?></td>
                                    <td style="text-align: center;"><?= Security::escapeOutput($status_text) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>

            <footer class="report-footer">
                <p>Kisaran, <?= date("d F Y") ?></p>
                <div class="signature">
                    <p>Kepala Perpustakaan</p>
                    <br><br><br>
                    <p><strong>(___________________)</strong></p>
                </div>
            </footer>
        </div>
        <div class="form-container" style="text-align: center; margin-top: 2rem;">
            <button class="btn" onclick="window.print()">Cetak Laporan</button>
        </div>
    <?php endif; ?>

</body>
</html>

