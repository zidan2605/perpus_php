<?php
Security::requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index_admin.php?page=pengumuman&actions=tampil&error=invalid_id");
    exit;
}

// Fetch announcement data
$stmt = $koneksi->prepare("SELECT * FROM pengumuman WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index_admin.php?page=pengumuman&actions=tampil&error=not_found");
    exit;
}

$data = $result->fetch_assoc();

// Check if user can edit (owner or admin)
$canEdit = ($data['created_by'] === $_SESSION['username']) || ($_SESSION['level'] == 1);
if (!$canEdit) {
    header("Location: index_admin.php?page=pengumuman&actions=tampil&error=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>alert('Token keamanan tidak valid!');</script>";
        exit;
    }

    try {
        $title = Security::sanitizeInput($_POST['title']);
        $content = Security::sanitizeInput($_POST['content']);

        if (empty($title) || empty($content)) {
            echo "<script>alert('Judul dan Konten wajib diisi.');</script>";
        } else {
            $stmt = $koneksi->prepare("UPDATE pengumuman SET title = ?, content = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $content, $id);
            
            if ($stmt->execute()) {
                Security::logActivity("Updated announcement: $title (ID: $id)");
                echo "<script>alert('Pengumuman berhasil diperbarui!'); window.location.assign('?page=pengumuman&actions=tampil&success=updated');</script>";
            } else {
                throw new Exception("Gagal memperbarui pengumuman.");
            }
        }
    } catch (Exception $e) {
        error_log("Edit announcement error: " . $e->getMessage());
        echo "<script>alert('Edit Pengumuman Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="content-header">
    <h2><i class="fas fa-edit"></i> Edit Pengumuman</h2>
    <a href="?page=pengumuman&actions=tampil" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="form-container">
    <form method="POST" action="?page=pengumuman&actions=edit&id=<?= $id ?>" class="form-modern">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        
        <div class="form-group">
            <label for="title">Judul Pengumuman <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="<?= Security::escapeOutput($data['title']) ?>"
                   required placeholder="Masukkan judul pengumuman" maxlength="255">
        </div>
        
        <div class="form-group">
            <label for="content">Konten Pengumuman <span class="required">*</span></label>
            <textarea id="content" name="content" class="form-control" rows="10" 
                      required placeholder="Masukkan isi pengumuman..."><?= Security::escapeOutput($data['content']) ?></textarea>
            <small class="form-text">Anda dapat menggunakan Enter untuk membuat paragraf baru</small>
        </div>
        
        <div class="form-meta">
            <small>
                <i class="fas fa-user"></i> Dibuat oleh: <?= Security::escapeOutput($data['created_by']) ?><br>
                <i class="fas fa-clock"></i> Dibuat pada: <?= date('d F Y, H:i', strtotime($data['created_at'])) ?>
                <?php if ($data['updated_at'] != $data['created_at']): ?>
                    <br><i class="fas fa-history"></i> Terakhir diupdate: <?= date('d F Y, H:i', strtotime($data['updated_at'])) ?>
                <?php endif; ?>
            </small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="?page=pengumuman&actions=tampil" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<style>
.form-container {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 900px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #334155;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s;
    font-family: 'Inter', sans-serif;
}

.form-control:focus {
    outline: none;
    border-color: #6366F1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 200px;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.85rem;
    color: #64748b;
}

.form-meta {
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    color: #64748b;
}

.form-meta i {
    margin-right: 0.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1rem;
}

.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.content-header h2 {
    color: #1e293b;
    font-size: 1.75rem;
    font-weight: 700;
}
</style>
