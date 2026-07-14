-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 09:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

DROP TABLE IF EXISTS `absensi`;
CREATE TABLE `absensi` (
  `id_absensi` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status_kehadiran` enum('Hadir','Telat','Izin','Absen') DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `user_id`, `tanggal`, `jam_masuk`, `jam_pulang`, `status_kehadiran`, `catatan`) VALUES
(1, 'NP-202401', '2026-06-29', '07:45:00', '17:00:00', 'Hadir', NULL),
(2, 'NP-202402', '2026-06-29', '08:00:00', '17:00:00', 'Hadir', NULL),
(3, 'NP-202403', '2026-06-29', '08:10:00', '17:00:00', 'Telat', NULL),
(4, 'NP-202404', '2026-06-29', '07:55:00', '17:00:00', 'Hadir', NULL),
(5, 'NP-202405', '2026-06-29', NULL, NULL, 'Izin', NULL),
(6, 'NP-202406', '2026-06-29', '07:50:00', '17:00:00', 'Hadir', NULL),
(7, 'NP-202407', '2026-06-29', '08:05:00', '17:00:00', 'Hadir', NULL),
(8, 'NP-202408', '2026-06-29', '07:55:00', '17:00:00', 'Hadir', NULL),
(9, 'NP-202409', '2026-06-29', '08:20:00', '17:00:00', 'Telat', NULL),
(10, 'NP-202410', '2026-06-29', '07:40:00', '17:00:00', 'Hadir', NULL),
(11, 'NP-202411', '2026-06-29', '07:55:00', '17:00:00', 'Hadir', NULL),
(12, 'NP-202412', '2026-06-29', NULL, NULL, 'Absen', NULL),
(13, 'NP-202413', '2026-06-29', '07:50:00', '17:00:00', 'Hadir', NULL),
(14, 'NP-202414', '2026-06-29', '07:45:00', '17:00:00', 'Hadir', NULL),
(15, 'NP-202415', '2026-06-29', '08:00:00', '17:00:00', 'Hadir', NULL),
(16, 'NP-202416', '2026-06-29', '08:15:00', '17:00:00', 'Telat', NULL),
(17, 'NP-202417', '2026-06-29', '07:55:00', '17:00:00', 'Hadir', NULL),
(18, 'NP-202418', '2026-06-29', '07:50:00', '17:00:00', 'Hadir', NULL),
(19, 'NP-202419', '2026-06-29', '08:00:00', '17:00:00', 'Hadir', NULL),
(20, 'NP-202420', '2026-06-29', '07:45:00', '17:00:00', 'Hadir', NULL),
(21, 'NP-202401', '2026-07-13', '00:00:08', NULL, 'Hadir', NULL),
(22, 'NP-202409', '2026-07-13', '00:00:08', '13:45:00', 'Hadir', NULL),
(23, 'NP-202402', '2026-07-13', '15:43:00', '15:44:00', 'Telat', NULL),
(24, 'NP-202407', '2026-07-13', '19:46:00', '19:46:00', 'Telat', NULL),
(25, 'NP-202409', '2026-07-14', '06:41:00', NULL, 'Hadir', NULL),
(26, 'NP-202401', '2026-07-14', '06:43:00', NULL, 'Hadir', NULL),
(27, 'NP-202406', '2026-07-14', '06:47:00', NULL, 'Hadir', NULL),
(28, 'NP-202402', '2026-07-14', '06:52:00', NULL, 'Hadir', NULL),
(29, 'NP-202404', '2026-07-14', '06:55:00', NULL, 'Hadir', NULL),
(30, 'NP-202408', '2026-07-14', '06:57:00', NULL, 'Hadir', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

DROP TABLE IF EXISTS `kegiatan`;
CREATE TABLE `kegiatan` (
  `id_kegiatan` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu_kegiatan` varchar(30) NOT NULL DEFAULT '',
  `nama_kegiatan` varchar(255) DEFAULT NULL,
  `status_kegiatan` enum('Selesai','On Progress') DEFAULT NULL,
  `departemen` enum('Subbag Tata Usaha','Subbag Kepegawaian dan Tata Usaha','Subbag Keuangan','Subbag Humas','Divisi Pelayanan Hukum','Divisi Pemasyarakatan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `user_id`, `tanggal`, `waktu_kegiatan`, `nama_kegiatan`, `status_kegiatan`, `departemen`) VALUES
(1, 'NP-202401', '2026-06-29', '', 'Registrasi Surat Masuk', 'Selesai', 'Subbag Tata Usaha'),
(2, 'NP-202401', '2026-06-29', '', 'Input Data Kenaikan Pangkat', 'Selesai', 'Subbag Kepegawaian dan Tata Usaha'),
(3, 'NP-202401', '2026-06-29', '', 'Rekonsiliasi Anggaran', 'On Progress', 'Subbag Keuangan'),
(4, 'NP-202401', '2026-06-29', '', 'Update Berita Website', 'Selesai', 'Subbag Humas'),
(5, 'NP-202401', '2026-06-29', '', 'Konsultasi Hukum Publik', 'Selesai', 'Divisi Pelayanan Hukum'),
(6, 'NP-202401', '2026-06-29', '', 'Evaluasi Keamanan Blok', 'On Progress', 'Divisi Pemasyarakatan'),
(9, 'NP-202409', '2026-07-13', '07.30 - 08.15', 'Briefing pagi dan pengecekan agenda kerja harian', 'Selesai', 'Subbag Tata Usaha'),
(10, 'NP-202409', '2026-07-13', '08.30 - 09.45', 'Registrasi serta verifikasi surat masuk', 'Selesai', 'Subbag Tata Usaha'),
(11, 'NP-202409', '2026-07-13', '10.00 - 11.15', 'Pengarsipan dokumen administrasi ke dalam sistem', 'Selesai', 'Subbag Tata Usaha'),
(12, 'NP-202409', '2026-07-13', '11.15 - 12.00', 'Penyusunan konsep nota dinas dan disposisi surat', 'Selesai', 'Subbag Tata Usaha'),
(14, 'NP-202409', '2026-07-13', '13.00 - 14.15', 'Rekapitulasi dokumen administrasi harian', 'Selesai', 'Subbag Tata Usaha'),
(15, 'NP-202409', '2026-07-13', '14.30 - 15.30', 'Pembaruan data administrasi dan distribusi surat keluar', 'On Progress', 'Subbag Tata Usaha'),
(16, 'NP-202409', '2026-07-13', '15.30 - 16.00', 'Penyusunan laporan kegiatan harian dan penutupan administrasi', 'Selesai', 'Subbag Tata Usaha');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `role` enum('admin','karyawan') DEFAULT 'karyawan',
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `status_karyawan` varchar(30) DEFAULT 'Tetap',
  `nip` varchar(30) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_id`, `password`, `nama`, `role`, `email`, `telepon`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `nik`, `departemen`, `status_karyawan`, `nip`, `foto`, `jabatan`) VALUES
(1, 'admin', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Administrator', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tetap', NULL, NULL, NULL),
(2, 'NP-202401', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Sabina Panjaitan', 'karyawan', 'sabina.panjaitan@siap.go.id', '081234567819', 'Jl. Ayahanda No. 31, Medan Timur', 'Medan', '2004-02-24', 'Perempuan', '1271132402040019', 'Subbag Teknologi Informasi', 'Tetap', '200402242026102001', 'uploads/profile/profile_NP-202401_a3a183c224c7f86d.jpg', 'Analis Sistem Informasi'),
(3, 'NP-202402', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Budi Santoso', 'karyawan', 'budi.santoso@siap.go.id', '081234567802', 'Jl. Setia Budi No. 8, Medan', 'Binjai', '1996-08-20', 'Laki-laki', '1275022008960002', 'Subbag Kepegawaian', 'Tetap', '199608202021041002', 'uploads/profile/profile_NP-202402_d21bd0927713c459.jpg', 'Analis Kepegawaian'),
(4, 'NP-202403', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Siti Aminah', 'karyawan', 'siti.aminah@siap.go.id', '081234567803', 'Jl. Karya Wisata No. 5, Medan', 'Medan', '1998-01-10', 'Perempuan', '1271131001980003', 'Subbag Program dan Pelaporan', 'Tetap', '199801102022031003', NULL, 'Analis Program'),
(5, 'NP-202404', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Dedi Kurniawan', 'karyawan', 'dedi.kurniawan@siap.go.id', '081234567804', 'Jl. Asia No. 25, Medan', 'Pematangsiantar', '1995-11-02', 'Laki-laki', '1277030211950004', 'Subbag Keuangan', 'Tetap', '199511022020031004', 'uploads/profile/profile_NP-202404_d056fce08aed0bbe.jpg', 'Verifikator Keuangan'),
(6, 'NP-202405', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Rina Wijaya', 'karyawan', 'rina.wijaya@siap.go.id', '081234567805', 'Jl. Marelan No. 18, Medan', 'Tebing Tinggi', '1999-04-18', 'Perempuan', '1272031804990005', 'Subbag Umum', 'Tetap', '199904182023031005', NULL, 'Pengelola Persuratan'),
(7, 'NP-202406', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Andi Pratama', 'karyawan', 'andi.pratama@siap.go.id', '081234567801', 'Jl. Gatot Subroto No. 12, Medan', 'Medan', '1997-03-15', 'Laki-laki', '1271131503970001', 'Subbag Tata Usaha', 'Tetap', '199703152022031001', 'uploads/profile/profile_NP-202406_44576ce580f45357.webp', 'Pengelola Administrasi'),
(8, 'NP-202407', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Dewi Lestari', 'karyawan', 'dewi.lestari@siap.go.id', '081234567808', 'Jl. Ring Road No. 11, Medan', 'Medan', '1996-07-19', 'Perempuan', '1271131907960008', 'Subbag Keuangan', 'Tetap', '199607192021031008', NULL, 'Verifikator Keuangan'),
(9, 'NP-202408', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Fajar Ramadhan', 'karyawan', 'fajar.ramadhan@siap.go.id', '081234567807', 'Jl. Cemara No. 15, Medan', 'Medan', '1998-05-27', 'Laki-laki', '1271132705980007', 'Subbag Umum', 'Tetap', '199805272022031007', 'uploads/profile/profile_NP-202408_8e1b36430a12b120.jpg', 'Pengelola Persuratan'),
(10, 'NP-202409', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Gita Permata', 'karyawan', 'gita.permata@siap.go.id', '081930300928', 'Jl. Ayahanda No. 31, Medan Timur', 'Medan', '1999-12-03', 'Perempuan', '127113546789002', 'Subbag Tata Usaha', 'Tetap', '199912032023031009', 'uploads/profile/profile_NP-202409_423b5d514e680a1d.jpg', 'Pengelola Administrasi'),
(11, 'NP-202410', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Hendra Setiawan', 'karyawan', 'hendra.setiawan@siap.go.id', '081234567808', 'Jl. Iskandar Muda No. 22, Medan', 'Medan', '1996-07-19', 'Laki-laki', '1271131907960008', 'Bagian Kepegawaian', 'Tetap', '199607192021031008', NULL, 'Pengelola Data ASN'),
(12, 'NP-202411', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Indah Permatasari', 'karyawan', 'indah.permatasari@siap.go.id', '081234567809', 'Jl. Sunggal No. 14, Medan', 'Binjai', '1998-10-08', 'Perempuan', '1275020810980009', 'Bagian Umum', 'Tetap', '199810082022031009', NULL, 'Arsiparis'),
(13, 'NP-202412', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Joko Susilo', 'karyawan', 'joko.susilo@siap.go.id', '081234567810', 'Jl. Krakatau No. 10, Medan', 'Tebing Tinggi', '1995-02-11', 'Laki-laki', '1272031102950010', 'Bagian Pengelolaan BMN', 'Tetap', '199502112020031010', NULL, 'Pengelola Barang Milik Negara'),
(14, 'NP-202413', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Kartika Sari', 'karyawan', 'kartika.sari@siap.go.id', '081234567811', 'Jl. Sisingamangaraja No. 30, Medan', 'Pematangsiantar', '1997-06-25', 'Perempuan', '1277032506970011', 'Bidang Kekayaan Intelektual', 'Tetap', '199706252021031011', NULL, 'Analis Kekayaan Intelektual'),
(15, 'NP-202414', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Lutfi Hakim', 'karyawan', 'lutfi.hakim@siap.go.id', '081234567812', 'Jl. Ayahanda No. 18, Medan', 'Medan', '1996-09-14', 'Laki-laki', '1271131409960012', 'Bagian Pengadaan Barang dan Jasa', 'Kontrak', '199609142022031012', NULL, 'Analis Pengadaan'),
(16, 'NP-202415', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Maya Indah', 'karyawan', 'maya.indah@siap.go.id', '081234567813', 'Jl. Ring Road No. 5, Medan', 'Medan', '1999-01-30', 'Perempuan', '1271133001990013', 'Pusat Data dan Informasi', 'Tetap', '199901302023031013', NULL, 'Administrator Sistem'),
(17, 'NP-202416', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Niko Saputra', 'karyawan', 'niko.saputra@siap.go.id', '081234567814', 'Jl. Brigjen Katamso No. 17, Medan', 'Binjai', '1997-12-05', 'Laki-laki', '1275020512970014', 'Bagian Hubungan Masyarakat', 'Tetap', '199712052022031014', NULL, 'Pranata Hubungan Masyarakat'),
(18, 'NP-202417', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Olivia Putri', 'karyawan', 'olivia.putri@siap.go.id', '081234567815', 'Jl. SM Raja No. 45, Medan', 'Medan', '1998-03-22', 'Perempuan', '1271132203980015', 'Bagian Program dan Pelaporan', 'Tetap', '199803222023031015', NULL, 'Penyusun Laporan Kinerja'),
(19, 'NP-202418', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Putra Utama', 'karyawan', 'putra.utama@siap.go.id', '081234567816', 'Jl. Gatot Subroto No. 45, Medan', 'Medan', '1998-02-14', 'Laki-laki', '1271131402980016', 'Subbag Keuangan', 'Tetap', '199802142023031016', NULL, 'Pengelola Verifikasi Keuangan'),
(20, 'NP-202419', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Qori Amalia', 'karyawan', 'qori.amalia@siap.go.id', '081234567817', 'Jl. Setia Budi No. 27, Medan', 'Binjai', '1999-06-08', 'Perempuan', '1275020806990017', 'Subbag Kepegawaian', 'Tetap', '199906082023031017', NULL, 'Pengelola Administrasi Kepegawaian'),
(21, 'NP-202420', '$2y$10$Cb2IJWw0Zq62kERlaGX0YeKCbhHvJzbUcXIifl4fgTf3RGMN6uKKO', 'Rian Hidayat', 'karyawan', 'rian.hidayat@siap.go.id', '081234567818', 'Jl. Kapten Muslim No. 18, Medan', 'Tebing Tinggi', '1997-11-25', 'Laki-laki', '1272032511970018', 'Subbag Tata Usaha', 'Tetap', '199711252022031018', NULL, 'Pengadministrasi Umum');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
