<?php Security::requireAuth(); ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-envelope"></i> Pesan dari Pengunjung
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Pesan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        // Check if table exists and has subject column
                        $checkTable = $koneksi->query("SHOW TABLES LIKE 'contact_messages'");
                        if ($checkTable->num_rows > 0) {
                            $checkColumn = $koneksi->query("SHOW COLUMNS FROM contact_messages LIKE 'subject'");
                            $hasSubject = ($checkColumn->num_rows > 0);
                            
                            if ($hasSubject) {
                                $stmt = $koneksi->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
                            } else {
                                // Fallback for old table structure
                                $stmt = $koneksi->prepare("SELECT id, nama, email, pesan, created_at, status FROM contact_messages ORDER BY created_at DESC");
                            }
                            
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            $nomor = 0;
                            while ($data = $result->fetch_assoc()) {
                                $nomor++;
                                $status = $data['status'] ?? 'unread';
                                $isRead = ($status === 'read');
                                ?>
                                <tr style="<?= !$isRead ? 'background-color: #FEF3C7;' : '' ?>">
                                    <td><?= $nomor ?></td>
                                    <td style="font-weight: 600;"><?= Security::escapeOutput($data['nama']) ?></td>
                                    <td><?= Security::escapeOutput($data['email']) ?></td>
                                    <td><?= isset($data['subject']) ? Security::escapeOutput($data['subject']) : '-' ?></td>
                                    <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= Security::escapeOutput($data['pesan']) ?>
                                    </td>
                                    <td><?= date('d M Y, H:i', strtotime($data['created_at'])) ?></td>
                                    <td>
                                        <?php if ($isRead): ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Dibaca
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> Belum Dibaca
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-info btn-xs" onclick="showMessage(<?= $data['id'] ?>, '<?= Security::escapeOutput($data['nama']) ?>', '<?= Security::escapeOutput($data['email']) ?>', '<?= isset($data['subject']) ? Security::escapeOutput($data['subject']) : '' ?>', '<?= htmlspecialchars(addslashes($data['pesan'])) ?>')">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <?php if (!$isRead): ?>
                                            <a href="../actions/pesan_mark_read.php?id=<?= $data['id'] ?>" class="btn btn-success btn-xs" title="Tandai sudah dibaca">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="../actions/pesan_delete.php?id=<?= $data['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus pesan ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                            }
                            
                            if ($nomor == 0) {
                                echo '<tr><td colspan="8" style="text-align: center; padding: 3rem;">Belum ada pesan</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8" style="text-align: center; padding: 3rem;">Tabel pesan belum tersedia</td></tr>';
                        }
                    } catch (Exception $e) {
                        error_log("Error loading messages: " . $e->getMessage());
                        echo '<tr><td colspan="8" style="text-align: center; padding: 3rem; color: red;">Error memuat data</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for viewing message -->
<div id="messageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 20px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #E2E8F0;">
            <h3 style="margin: 0; font-size: 1.5rem; color: #6366F1;"><i class="fas fa-envelope-open"></i> Detail Pesan</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748B;">&times;</button>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <strong style="color: #64748B;">Dari:</strong>
            <p id="modalNama" style="font-size: 1.1rem; font-weight: 600; margin: 0.5rem 0;"></p>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <strong style="color: #64748B;">Email:</strong>
            <p id="modalEmail" style="margin: 0.5rem 0;"></p>
        </div>
        <div id="modalSubjectDiv" style="margin-bottom: 1.5rem; display: none;">
            <strong style="color: #64748B;">Subject:</strong>
            <p id="modalSubject" style="margin: 0.5rem 0; font-weight: 600;"></p>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <strong style="color: #64748B;">Pesan:</strong>
            <p id="modalPesan" style="margin: 0.5rem 0; line-height: 1.8; padding: 1rem; background: #F8FAFC; border-radius: 10px;"></p>
        </div>
    </div>
</div>

<script>
function showMessage(id, nama, email, subject, pesan) {
    document.getElementById('modalNama').textContent = nama;
    document.getElementById('modalEmail').textContent = email;
    document.getElementById('modalPesan').textContent = pesan;
    
    if (subject) {
        document.getElementById('modalSubject').textContent = subject;
        document.getElementById('modalSubjectDiv').style.display = 'block';
    } else {
        document.getElementById('modalSubjectDiv').style.display = 'none';
    }
    
    document.getElementById('messageModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('messageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>


