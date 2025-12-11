<?php
Security::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>alert('Token keamanan tidak valid!');</script>";
        exit;
    }

    try {
        $title = Security::sanitizeInput($_POST['title']);
        $content = Security::sanitizeInput($_POST['content']);
        $created_by = $_SESSION['username'];

        if (empty($title) || empty($content)) {
            echo "<script>alert('Judul dan Konten wajib diisi.');</script>";
        } else {
            $stmt = $koneksi->prepare("INSERT INTO pengumuman (title, content, created_by) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $content, $created_by);
            
            if ($stmt->execute()) {
                Security::logActivity("Added new announcement: $title");
                echo "<script>alert('Pengumuman berhasil ditambahkan!'); window.location.assign('?page=pengumuman&actions=tampil&success=added');</script>";
            } else {
                throw new Exception("Gagal menambahkan pengumuman.");
            }
        }
    } catch (Exception $e) {
        error_log("Add announcement error: " . $e->getMessage());
        echo "<script>alert('Tambah Pengumuman Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="content-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Pengumuman Baru</h2>
    <a href="?page=pengumuman&actions=tampil" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="form-container">
    <form method="POST" action="?page=pengumuman&actions=tambah" class="form-modern">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        
        <div class="form-group">
            <label for="title">Judul Pengumuman <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" 
                   required placeholder="Masukkan judul pengumuman" maxlength="255">
        </div>
        
        <div class="form-group">
            <label for="content">Konten Pengumuman <span class="required">*</span></label>
            <textarea id="content" name="content" class="form-control" rows="10" 
                      required placeholder="Masukkan isi pengumuman..."></textarea>
            <small class="form-text">Anda dapat menggunakan Enter untuk membuat paragraf baru</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pengumuman
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
