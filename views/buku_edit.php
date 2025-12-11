<?php
Security::requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID buku tidak valid'); window.location.assign('?page=buku&actions=tampil');</script>";
    exit;
}

$id = (int)$_GET['id'];

$stmt = $koneksi->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data buku tidak ditemukan'); window.location.assign('?page=buku&actions=tampil');</script>";
    exit;
}

// Convert the custom date format 'YYYY_MM_DD' to 'YYYY-MM-DD' for the date input
$tahun_terbit_parts = explode('_', $data['tahun_terbit']);
$tahun_terbit_formatted = !empty($tahun_terbit_parts[0]) ? implode('-', array_map(function($p) {
    return str_pad($p, 2, '0', STR_PAD_LEFT);
}, $tahun_terbit_parts)) : date('Y-m-d');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>alert('Token keamanan tidak valid!');</script>";
        exit;
    }

    try {
        // Sanitize all inputs
        $judul_buku = Security::sanitizeInput($_POST['judul_buku']);
        $nama_pengarang = Security::sanitizeInput($_POST['nama_pengarang']);
        $tahun_terbit = Security::sanitizeInput($_POST['tahun_terbit']);
        $penerbit = Security::sanitizeInput($_POST['penerbit']);
        $status = Security::sanitizeInput($_POST['status']);
        $loker_buku = Security::sanitizeInput($_POST['loker_buku']);
        $keterangan = Security::sanitizeInput($_POST['keterangan'] ?? '');

        // Prepare update statement
        $updateStmt = $koneksi->prepare(
            "UPDATE buku SET judul_buku=?, nama_pengarang=?, tahun_terbit=?, penerbit=?, status=?, loker_buku=?, keterangan=? WHERE id=?"
        );
        $updateStmt->bind_param(
            "sssssssi",
            $judul_buku,
            $nama_pengarang,
            $tahun_terbit,
            $penerbit,
            $status,
            $loker_buku,
            $keterangan,
            $id
        );

        if ($updateStmt->execute()) {
            Security::logActivity("Updated book ID: $id - $judul_buku");
            echo "<script>alert('Data buku berhasil diperbarui.'); window.location.assign('?page=buku&actions=tampil');</script>";
        } else {
            throw new Exception("Gagal memperbarui data buku.");
        }
    } catch (Exception $e) {
        error_log("Edit book error: " . $e->getMessage());
        echo "<script>alert('Edit Data Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-edit"></i> Edit Data Buku
    </div>
    <div class="card-body">
        <form action="?page=buku&actions=edit&id=<?= $id ?>" method="post" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div style="grid-column: 1 / -1; display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="judul_buku">Judul Buku</label>
                <input type="text" name="judul_buku" id="judul_buku" class="form-control" value="<?= Security::escapeOutput($data['judul_buku']) ?>" required>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="nama_pengarang">Nama Pengarang</label>
                <input type="text" name="nama_pengarang" id="nama_pengarang" class="form-control" value="<?= Security::escapeOutput($data['nama_pengarang']) ?>" required>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="penerbit">Penerbit</label>
                <input type="text" name="penerbit" id="penerbit" class="form-control" value="<?= Security::escapeOutput($data['penerbit']) ?>" required>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="date" name="tahun_terbit" id="tahun_terbit" class="form-control" value="<?= Security::escapeOutput($tahun_terbit_formatted) ?>" required>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="loker_buku">Loker Buku</label>
                <select name="loker_buku" id="loker_buku" class="form-control" required>
                    <?php 
                    $lokers = ["Buku Anak Anak", "Buku Dongeng", "Buku Majalah", "Buku Novel", "Buku Pembelajaran", "Buku Resep Masakan"];
                    foreach($lokers as $loker): 
                    ?>
                        <option value="<?= $loker ?>" <?= ($data['loker_buku'] === $loker) ? 'selected' : '' ?>><?= $loker ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="grid-column: 1 / -1; display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="keterangan">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" class="form-control"><?= Security::escapeOutput($data['keterangan']) ?></textarea>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Ada" <?= ($data['status'] === 'Ada') ? 'selected' : '' ?>>Ada</option>
                    <option value="Dipinjam" <?= ($data['status'] === 'Dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                </select>
            </div>

            <div style="grid-column: 1 / -1; margin-top: 1rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="?page=buku&actions=tampil" class="btn">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

