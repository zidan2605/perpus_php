<?php
// Not including session check as this seems to be a public-accessible report link
include '../src/db.php';
include '../config/security.php'; // Manually include for escapeOutput

$selected_month = isset($_POST['bulan']) ? (int)$_POST['bulan'] : date('m');
$selected_year = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');

$report_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming 'tahun_terbit' is in 'YYYY-MM-DD' or 'YYYY_MM_DD' format.
    // The query needs to handle this. Let's use LIKE for simplicity, but YEAR() and MONTH() on a DATE type is better.
    // A prepared statement is crucial here.
    $date_prefix = $selected_year . '-' . str_pad((string)$selected_month, 2, '0', STR_PAD_LEFT);
    $search_pattern = $date_prefix . '%';

    $stmt = $koneksi->prepare("SELECT * FROM buku WHERE tahun_terbit LIKE ? ORDER BY tahun_terbit ASC");
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
    <title>Laporan Buku Per Bulan</title>
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
                Pilih Bulan dan Tahun Laporan Buku
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
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun Terbit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report_data)): ?>
                            <tr><td colspan="6" style="text-align: center;">Tidak ada data untuk periode yang dipilih.</td></tr>
                        <?php else: ?>
                            <?php $nomor = 0; foreach ($report_data as $data): $nomor++; ?>
                                <tr>
                                    <td style="text-align: center;"><?= $nomor ?></td>
                                    <td><?= Security::escapeOutput($data['judul_buku']) ?></td>
                                    <td><?= Security::escapeOutput($data['nama_pengarang']) ?></td>
                                    <td><?= Security::escapeOutput($data['penerbit']) ?></td>
                                    <td style="text-align: center;"><?= Security::escapeOutput(str_replace('_', '-', $data['tahun_terbit'])) ?></td>
                                    <td style="text-align: center;"><?= Security::escapeOutput($data['status']) ?></td>
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

