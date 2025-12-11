<?php
/**
 * Contact Page
 */

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

Auth::startSession();

$pageTitle = 'Hubungi Kami - Perpustakaan Digital';
$activePage = 'contact';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = Auth::sanitizeInput($_POST['nama']);
    $email = Auth::sanitizeInput($_POST['email']);
    $pesan = Auth::sanitizeInput($_POST['pesan']);
    
    if (empty($nama) || empty($email) || empty($pesan)) {
        $error_message = "Semua field wajib diisi!";
    } else {
        $db = getDB();
        
        try {
            $checkTable = $db->query("SHOW TABLES LIKE 'contact_messages'");
            
            if ($checkTable->num_rows == 0) {
                $db->query("CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nama VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    pesan TEXT NOT NULL,
                    status VARCHAR(20) DEFAULT 'unread',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
            }
            
            $stmt = $db->prepare("INSERT INTO contact_messages (nama, email, pesan) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nama, $email, $pesan);
            
            if ($stmt->execute()) {
                $success_message = "Pesan Anda berhasil dikirim!";
            } else {
                $error_message = "Gagal mengirim pesan.";
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Hubungi Kami</h1>
        <p>Ada pertanyaan? Kirim pesan kepada kami</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Informasi Kontak</h2>
                <p>Jangan ragu untuk menghubungi kami. Tim kami siap membantu Anda.</p>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-content">
                        <h4>Alamat</h4>
                        <p>Jl. Perpustakaan No. 123<br>Jakarta Pusat, DKI Jakarta 10110</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-phone"></i></div>
                    <div class="info-content">
                        <h4>Telepon</h4>
                        <p>(021) 1234-5678<br>0812-3456-7890</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-content">
                        <h4>Email</h4>
                        <p>info@perpustakaan.com<br>admin@perpustakaan.com</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-wrapper">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $success_message ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nama Lengkap</label>
                        <input type="text" name="nama" required placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" required placeholder="contoh@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Pesan</label>
                        <textarea name="pesan" rows="6" required placeholder="Tuliskan pesan Anda..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8rem 0 3rem;
    text-align: center;
}
.contact-section { padding: 3rem 0; }
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}
.contact-info h2 { margin-bottom: 1rem; }
.info-item {
    display: flex;
    gap: 1rem;
    margin: 2rem 0;
}
.info-icon {
    width: 50px;
    height: 50px;
    background: #6366F1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}
.contact-form-wrapper {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
}
.btn-submit {
    width: 100%;
    padding: 1rem;
    background: #6366F1;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
}
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}
.alert-success {
    background: #dcfce7;
    color: #166534;
}
.alert-error {
    background: #fee2e2;
    color: #991b1b;
}
@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>


