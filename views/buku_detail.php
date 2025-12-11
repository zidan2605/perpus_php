<div class="card">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Informasi Detail Buku
    </div>
    <div class="card-body">
        <?php
        $id = $_GET['id'];
        $sql = "SELECT * FROM buku WHERE id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        ?>   

        <div class="table-responsive">
            <table class="table"> 
                        <tr>
                            <td width="200">Loker Buku</td> <td><?= $data['loker_buku'] ?></td>
                        </tr>
                        <tr>
                            <td>Nomor Rak/Lemari</td> <td><?= $data['no_rak'] ?></td>
                        </tr>
                        <tr>
                            <td>Nomor Tingkat/Laci</td> <td><?= $data['no_laci'] ?></td>
                        </tr>
						<tr>
                            <td>Nomor Boks</td> <td><?= $data['no_boks'] ?></td>
                        </tr>
                        <tr>
                            <td>Judul Buku</td> <td><?= $data['judul_buku'] ?></td>
                        </tr>
                        <tr>
                            <td>Nama Pengarang</td> <td><?= $data['nama_pengarang'] ?></td>
                        </tr>
						<tr>
                            <td>Tahun Terbit</td> <td><?= $data['tahun_terbit'] ?></td>
                        </tr>
						<tr>
                            <td>Penerbit Buku</td> <td><?= $data['penerbit'] ?></td>
                        </tr>
						<tr>
                            <td>Penerima Buku</td> <td><?= $data['penerima'] ?></td>
                        </tr>
						<tr>
                            <td>Status</td> <td><?= $data['status'] ?></td>
                        </tr>
						<tr>
                            <td>Keterangan</td> <td><?= $data['keterangan'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <a href="?page=buku&actions=tampil" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>



