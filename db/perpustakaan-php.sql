-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Dec 11, 2025 at 10:00 AM
-- Server version: 8.0.43
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan-php`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `no_anggota` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempat_lahir` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_daftar` date NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `no_anggota`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `email`, `pekerjaan`, `foto`, `tanggal_daftar`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'AGT001', 'Ahmad Fauzi', 'L', 'Jakarta', '1995-05-15', 'Jl. Merdeka No. 123, Jakarta Pusat', '081234567890', 'ahmad.fauzi@email.com', 'Mahasiswa', NULL, '2024-01-10', 'Aktif', 'Anggota aktif', '2025-12-11 09:16:35', '2025-12-11 09:16:35'),
(3, 'AGT003', 'Budi Santoso', 'L', 'Surabaya', '1990-12-10', 'Jl. Pahlawan No. 78, Surabaya', '083456789012', 'budi.santoso@email.com', 'Pegawai Swasta', '', '2024-03-20', 'Nonaktif', '', '2025-12-11 09:16:35', '2025-12-11 09:18:52');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int NOT NULL,
  `loker_buku` varchar(25) NOT NULL,
  `no_rak` int NOT NULL,
  `no_laci` int NOT NULL,
  `no_boks` int NOT NULL,
  `judul_buku` varchar(100) NOT NULL,
  `nama_pengarang` varchar(100) NOT NULL,
  `tahun_terbit` date NOT NULL,
  `penerima` varchar(50) NOT NULL,
  `penerbit` varchar(50) NOT NULL,
  `status` varchar(25) NOT NULL,
  `keterangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `loker_buku`, `no_rak`, `no_laci`, `no_boks`, `judul_buku`, `nama_pengarang`, `tahun_terbit`, `penerima`, `penerbit`, `status`, `keterangan`) VALUES
(27, 'Buku Anak Anak', 3, 3, 3, 'Sopan santun', 'Dwi hartono', '2019-01-01', 'TK ZAHIRA', 'Andi publisher', 'Ada', '12345'),
(28, 'Buku Anak Anak', 4, 4, 123, 'Bermain dengan alat tradisional', 'Aan sutejo', '2019-01-03', 'Tk zahira', 'Gramedia', 'Ada', '12345'),
(29, 'Buku Anak Anak', 5, 5, 5, 'Belajar membaca', 'Mizan', '2019-01-17', 'Tk zahira', 'Elexmedia', 'Ada', '12345'),
(30, 'Buku Anak Anak', 6, 6, 6, 'Belajar berhitung', 'Tri Azhari', '2019-01-05', 'Tk zahira', 'Agro media', 'Ada', '12345'),
(31, 'Buku Anak Anak', 7, 7, 7, 'Cara mudah membaca tanpa mengeja', 'irma yani', '2019-01-06', 'TK ZAHIRA', 'Gramdia', 'Ada', '12345'),
(32, 'Buku Anak Anak', 8, 8, 8, 'belajar menebalkan', 'tri hawanda', '2018-12-30', 'Tk zahira', 'Andalas', 'Ada', '12345'),
(33, 'Buku Anak Anak', 8, 8, 8, 'Belajar menulis huruf', 'joko susilo', '2019-01-13', 'Tk zahira', 'pustaka', 'Ada', '12345'),
(34, 'Buku Anak Anak', 9, 9, 9, 'Mari bernyanyi bersama', 'Darwin', '2019-01-10', 'Tk zahira', 'Erlangga', 'Ada', '12345'),
(35, 'Buku Anak Anak', 10, 10, 10, 'Makanan sehat', 'endang Mr', '2019-01-17', 'TK ZAHIRA', 'Swedia', 'Ada', '12345'),
(36, 'Buku Anak Anak', 11, 111, 1, 'Huruf hijaiyah', 'reza hardian', '2019-01-09', 'Tk zahira', 'Elex media', 'Ada', '12346'),
(37, 'Buku Anak Anak', 12, 12, 12, 'Mewarnai gambar', 'Irma yani', '2019-01-14', 'Tk zahira', 'Gria husada', 'Ada', '1234567'),
(38, 'Buku Anak Anak', 13, 13, 13, 'Menggunting dan menempel', 'nuraini', '2019-01-12', 'Tk zahira', 'Gagas media', 'Ada', '1234567'),
(39, 'Buku Anak Anak', 14, 14, 14, 'Mengenal Hewan', 'Astuti', '2019-01-06', 'Tk zahira', 'Aksara', 'Ada', '12345678'),
(40, 'Buku Anak Anak', 15, 15, 15, '4 sehat 5 sempurna', 'tri aksara', '2019-01-02', 'Tk zahira', 'Elfata Andi', 'Ada', '12345678'),
(41, 'Buku Anak Anak', 16, 16, 16, 'Lingkungan sehat', 'roro tri muji', '2019-01-09', 'Tk zahira', 'Andalas', 'Ada', '12345678'),
(44, 'Buku Novel', 1, 1, 1, 'Naruto', 'Zuddy', '2025-11-07', 'Kampus', 'AirLangga', 'Ada', ''),
(45, 'Buku Resep Masakan', 1, 1, 2, 'Doraemon', 'Zuddy', '2025-11-07', 'Kampus', 'AirLangga', 'Ada', 'keterangan');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `nama`, `email`, `pesan`, `status`, `created_at`) VALUES
(3, 'Administrator', 'admin@gmail.com', 'halow', 'read', '2025-12-11 05:59:57'),
(4, 'Admin', 'admin@gmail.com', 'tesssss', 'read', '2025-12-11 07:34:02');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `judul_buku` varchar(50) NOT NULL,
  `peminjam` varchar(50) NOT NULL,
  `tgl_pinjam` varchar(25) NOT NULL,
  `tgl_kembali` varchar(25) NOT NULL,
  `lama_pinjam` int NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Belum Kembali',
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`judul_buku`, `peminjam`, `tgl_pinjam`, `tgl_kembali`, `lama_pinjam`, `keterangan`, `status`, `id`) VALUES
('belajar menebalkan', 'AGT001 - Ahmad Fauzi', '2025-12-11', '2025-12-11', 0, '', 'Sudah Kembali', 16);

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `title`, `content`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Jadwal Perpustakaan Bulan Ini', 'Perpustakaan buka setiap hari Senin - Jumat pukul 08.00 - 17.00 WIB. Sabtu 08.00 - 12.00 WIB. Minggu dan hari libur nasional tutup. Terima kasih.', 'admin', '2025-12-11 09:41:10', '2025-12-11 09:41:10'),
(3, 'Koleksi Buku Baru Telah Tiba', 'Kami telah menambahkan 50 judul buku baru ke dalam koleksi perpustakaan. Silakan kunjungi halaman daftar buku untuk melihat koleksi terbaru kami.', 'admin', '2025-12-11 09:41:10', '2025-12-11 09:41:10'),
(5, 'Selamat Datang di Perpustakaan Digital', 'Perpustakaan Digital telah resmi diluncurkan! Nikmati kemudahan akses koleksi buku dan layanan peminjaman secara online. Kunjungi website kami untuk informasi lebih lanjut.', 'admin', '2025-12-11 09:46:31', '2025-12-11 09:46:31'),
(6, 'Tes 1', 'Halo', 'admin', '2025-12-11 09:50:56', '2025-12-11 09:50:56'),
(7, 'Tes 2', 'Halooow', 'admin', '2025-12-11 09:51:04', '2025-12-11 09:51:04'),
(8, 'Tes 3', 'Cek cek', 'admin', '2025-12-11 09:51:13', '2025-12-11 09:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'Perpustakaan Digital sir', '2025-12-10 17:37:55'),
(2, 'about_title', 'Tentang Perpustakaan Digital', '2025-12-10 17:18:43'),
(3, 'about_content', 'Perpustakaan Digital adalah sistem informasi perpustakaan modern yang memudahkan pengelolaan koleksi buku dan transaksi peminjaman. Dengan teknologi terkini, kami menghadirkan pengalaman membaca yang lebih baik bagi semua.', '2025-12-10 17:18:43'),
(4, 'contact_address', 'Jl. Pendidikan No. 123, Jakarta', '2025-12-10 17:18:43'),
(5, 'contact_phone', '021-1234567', '2025-12-10 17:18:43'),
(6, 'contact_email', 'info@perpustakaandigital.id', '2025-12-10 17:18:43'),
(7, 'copyright_text', '© 2025 Perpustakaan Digital. All rights reserved.', '2025-12-10 17:37:21'),
(8, 'footer_text', 'Sistem Informasi Perpustakaan Modern', '2025-12-10 17:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(25) NOT NULL,
  `paswd` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `level` int NOT NULL,
  `ket` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `paswd`, `email`, `nama`, `level`, `ket`) VALUES
('admin', '$argon2id$v=19$m=65536,t=4,p=1$T3FiZ2RQcFdUQUw2UnBDSQ$r6AIQBDV0nuE1cgksC8uPjTCdaeHUKRiZxzhsYADr1w', 'admin@gmail.com', 'Administrator', 1, 'Admin'),
('user', '$argon2id$v=19$m=65536,t=4,p=1$eWVDOEFGNTZKV0JQcDN0cw$T7VX6+IeLVCVgfgUPHHzs85wyYxPI2UNoPwohLhMjls', 'user@gmail.com', 'user', 0, 'Staff Perpustakaan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_anggota` (`no_anggota`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
