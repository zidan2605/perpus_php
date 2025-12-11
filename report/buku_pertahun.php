<?php
include '../src/db.php';
include '../config/security.php'; // Manually include for escapeOutput

$selected_year = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');

$report_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_pattern = $selected_year . '%';

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Per Tahun</title>
    <link href="../Assets/css/report.css" rel="stylesheet" type="text/css"/>
    <link href="../Assets/css/modern.css" rel="stylesheet" type="text/css"/>
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
                Pilih Tahun Laporan Buku
            </div>
            <div class="card-body">
                <form action="" method="post" style="display: flex; gap: 1rem; align-items: center;">
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
                            <tr><td colspan="6" style="text-align: center;">Tidak ada data untuk tahun yang dipilih.</td></tr>
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

