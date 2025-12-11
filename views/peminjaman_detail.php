<div class="card">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Informasi Detail Peminjaman Buku
    </div>
    <div class="card-body">
        <?php
        $id = $_GET['id'];
        $sql = "SELECT * FROM peminjaman WHERE id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        ?>   

        <div class="table-responsive">
            <table class="table"> 
                        <tr>
                            <td width="200">Judul Buku</td> <td><?= $data['judul_buku'] ?></td>
                        </tr>
                        <tr>
                            <td>Nama Peminjam</td> <td><?= $data['peminjam'] ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Pinjam</td> <td><?= $data['tgl_pinjam'] ?></td>
                        </tr>
						<tr>
                            <td>Tanggal Kembali</td> <td><?= $data['tgl_kembali'] ?></td>
                        </tr>
                        <tr>
                            <td>Lama Pinjam</td> <td><?= $data['lama_pinjam'] ?></td>
                        </tr>
						<tr>
                            <td>Keterangan</td> <td><?= $data['keterangan'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <a href="?page=peminjaman&actions=tampil" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>



