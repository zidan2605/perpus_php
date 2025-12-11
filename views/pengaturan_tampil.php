<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-info-circle"></i> Edit Halaman Tentang</h3>
                </div>
                <div class="panel-body">
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            <strong>Berhasil!</strong> Konten Tentang telah diperbarui.
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    $about_content = '';
                    try {
                        $stmt = $koneksi->prepare("SELECT about_content FROM site_settings WHERE id = 1");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()) {
                            $about_content = $row['about_content'];
                        }
                    } catch (Exception $e) {
                        error_log("Error loading about content: " . $e->getMessage());
                    }
                    ?>
                    
                    <form action="?page=pengaturan&actions=update_about" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        
                        <div class="form-group">
                            <label for="about_content">Konten Halaman Tentang <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="about_content" name="about_content" rows="10" required><?= Security::escapeOutput($about_content) ?></textarea>
                            <p class="help-block">Tuliskan informasi tentang perpustakaan Anda</p>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="index_admin.php" class="btn btn-default">
                                <i class="fa fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-lightbulb-o"></i> Tips</h3>
                </div>
                <div class="panel-body">
                    <p><strong>Konten ini akan ditampilkan di halaman utama.</strong></p>
                    <ul>
                        <li>Gunakan paragraf untuk memudahkan pembaca</li>
                        <li>Jelaskan sejarah perpustakaan</li>
                        <li>Sebutkan visi dan misi</li>
                        <li>Tambahkan informasi kontak jika diperlukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


