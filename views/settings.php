<?php
// Settings Management

if (!isset($_GET['actions'])) {
    $_GET['actions'] = 'list';
}

switch ($_GET['actions']) {
    case 'edit':
        ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i> Edit Pengaturan Website
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            foreach ($_POST as $key => $value) {
                                if ($key !== 'submit') {
                                    $value = Security::sanitizeInput($value);
                                    $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                                    $stmt = mysqli_prepare($koneksi, $sql);
                                    mysqli_stmt_bind_param($stmt, "ss", $value, $key);
                                    mysqli_stmt_execute($stmt);
                                }
                            }
                            echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pengaturan berhasil diupdate!</div>';
                        }
                        
                        // Get all settings
                        $settings = [];
                        $sql = "SELECT * FROM settings ORDER BY setting_key";
                        $result = mysqli_query($koneksi, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $settings[$row['setting_key']] = $row['setting_value'];
                        }
                        ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Website</label>
                                        <input type="text" name="site_name" class="form-control" value="<?= Security::escapeOutput($settings['site_name'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email Kontak</label>
                                        <input type="email" name="contact_email" class="form-control" value="<?= Security::escapeOutput($settings['contact_email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Telepon</label>
                                        <input type="text" name="contact_phone" class="form-control" value="<?= Security::escapeOutput($settings['contact_phone'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text" name="contact_address" class="form-control" value="<?= Security::escapeOutput($settings['contact_address'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Judul Halaman Tentang</label>
                                <input type="text" name="about_title" class="form-control" value="<?= Security::escapeOutput($settings['about_title'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Konten Halaman Tentang</label>
                                <textarea name="about_content" class="form-control" rows="5" required><?= Security::escapeOutput($settings['about_content'] ?? '') ?></textarea>
                                <small class="text-muted">Konten ini akan ditampilkan di halaman depan</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Teks Footer</label>
                                <input type="text" name="footer_text" class="form-control" value="<?= Security::escapeOutput($settings['footer_text'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Copyright Text</label>
                                <input type="text" name="copyright_text" class="form-control" value="<?= Security::escapeOutput($settings['copyright_text'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Pengaturan
                                </button>
                                <a href="landing.php" target="_blank" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Lihat Landing Page
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
        
    default:
        header('Location: ?page=settings&actions=edit');
        break;
}
?>


