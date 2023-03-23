-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 23, 2023 at 07:08 AM
-- Server version: 8.0.29
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apotek`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int NOT NULL,
  `username` varchar(25) NOT NULL,
  `sip_dokter` varchar(100) NOT NULL,
  `kategori_dokter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `jadwal_praktek` text NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `konsul`
--

CREATE TABLE `konsul` (
  `id_konsul` int NOT NULL,
  `id_dokter` int NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `nik` varchar(16) NOT NULL,
  `status_selesai` tinyint(1) NOT NULL DEFAULT '0',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int NOT NULL,
  `nama_obat` varchar(150) NOT NULL,
  `satuan_obat` varchar(50) NOT NULL,
  `stok_obat` int NOT NULL DEFAULT '0',
  `deskripsi_obat` varchar(300) NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `nik` varchar(16) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jenis_kelamin` enum('l','p') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text NOT NULL,
  `norm` varchar(50) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `key_pengaturan` varchar(25) NOT NULL,
  `label_pengaturan` varchar(50) NOT NULL,
  `value_pengaturan` varchar(150) NOT NULL,
  `priority_pengaturan` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`key_pengaturan`, `label_pengaturan`, `value_pengaturan`, `priority_pengaturan`) VALUES
('BRAND_ADDRESS', 'Alamat', 'Jl. Pengantin Ali No.12 RT/RW: 009/006 Ciracas, Ciracas Kota Jakarta Timur DKI Jakarta, Kode Pos 13740', 2),
('BRAND_CITY', 'Kota', 'Jakarta', 3),
('BRAND_NAME', 'Nama Apotek', 'Apotek', 0),
('BRAND_OWNER', 'Nama Pemilik', 'Joel', 1);

-- --------------------------------------------------------

--
-- Table structure for table `resep`
--

CREATE TABLE `resep` (
  `id_resep` int NOT NULL,
  `id_konsul` int NOT NULL,
  `data_resep` text NOT NULL,
  `status_dicetak` tinyint(1) NOT NULL DEFAULT '0',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_stok`
--

CREATE TABLE `riwayat_stok` (
  `id_riwayat_stok` int NOT NULL,
  `id_obat` int NOT NULL,
  `stok_akhir` int NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diperbarui` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_keluar`
--

CREATE TABLE `stok_keluar` (
  `id_stok_keluar` int NOT NULL,
  `id_stok_keluar_kategori` int NOT NULL,
  `id_obat` int NOT NULL,
  `kuantitas_stok_keluar` int NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_keluar_kategori`
--

CREATE TABLE `stok_keluar_kategori` (
  `id_stok_keluar_kategori` int NOT NULL,
  `nama_stok_keluar_kategori` varchar(150) NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stok_keluar_kategori`
--

INSERT INTO `stok_keluar_kategori` (`id_stok_keluar_kategori`, `nama_stok_keluar_kategori`, `tanggal_dibuat`, `tanggal_diubah`) VALUES
(1, 'Resep', '2023-03-23 06:40:03', '2023-03-23 06:40:03');

-- --------------------------------------------------------

--
-- Table structure for table `stok_masuk`
--

CREATE TABLE `stok_masuk` (
  `id_stok_masuk` int NOT NULL,
  `id_stok_masuk_kategori` int NOT NULL,
  `id_obat` int NOT NULL,
  `kuantitas_stok_masuk` int NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_masuk_kategori`
--

CREATE TABLE `stok_masuk_kategori` (
  `id_stok_masuk_kategori` int NOT NULL,
  `nama_stok_masuk_kategori` varchar(150) NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(25) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(50) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diubah` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `name`, `email`, `password`, `role`, `last_login`, `tanggal_dibuat`, `tanggal_diubah`) VALUES
(1, 'konsul', 'Konsul', 'user@email.com', '$2y$10$0b8sYXhtJLKK1sYQSzrf/uivOP2C.q422iJllsDvXAGufh9TZ1TS6', 'konsul', '2023-03-23 11:46:40', '2023-03-11 02:33:10', '2023-03-23 04:46:40'),
(3, 'farma', 'Farma', 'user2@email.com', '$2y$10$uDagAdHnUXhCJeVAna0O9ur1Cy/yHAMU47m3TOqsI6ZzS2PjwtGrO', 'farma', '2023-03-23 13:13:58', '2023-03-11 02:33:10', '2023-03-23 06:13:58'),
(4, 'ellie_williams', 'Ellie Williams', 'ellie@email.com', '$2y$10$Q0y27uHeVp0dfwyCdAqOD.Nn4sckHDtwzYNhFb3LsEJ2SZjfQkou.', 'dokter', '2023-03-21 23:49:26', '2023-03-11 05:26:17', '2023-03-21 16:49:26'),
(6, 'bambang', 'Bambang', 'bambang@email.com', '$2y$10$JuwXIwYhKPPI4vMu3dTj/uPhCE/AvsIGIo7IKjTYUcR..pAmSzY9S', 'dokter', '2023-03-23 13:12:12', '2023-03-23 04:07:43', '2023-03-23 06:12:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `konsul`
--
ALTER TABLE `konsul`
  ADD PRIMARY KEY (`id_konsul`),
  ADD KEY `nik` (`nik`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`nik`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD UNIQUE KEY `key_pengaturan` (`key_pengaturan`);

--
-- Indexes for table `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_konsul` (`id_konsul`);

--
-- Indexes for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD PRIMARY KEY (`id_riwayat_stok`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `stok_keluar`
--
ALTER TABLE `stok_keluar`
  ADD PRIMARY KEY (`id_stok_keluar`),
  ADD KEY `id_stok_keluar_kategori` (`id_stok_keluar_kategori`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `stok_keluar_kategori`
--
ALTER TABLE `stok_keluar_kategori`
  ADD PRIMARY KEY (`id_stok_keluar_kategori`);

--
-- Indexes for table `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD PRIMARY KEY (`id_stok_masuk`),
  ADD KEY `id_stok_masuk_kategori` (`id_stok_masuk_kategori`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `stok_masuk_kategori`
--
ALTER TABLE `stok_masuk_kategori`
  ADD PRIMARY KEY (`id_stok_masuk_kategori`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `konsul`
--
ALTER TABLE `konsul`
  MODIFY `id_konsul` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resep`
--
ALTER TABLE `resep`
  MODIFY `id_resep` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  MODIFY `id_riwayat_stok` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_keluar`
--
ALTER TABLE `stok_keluar`
  MODIFY `id_stok_keluar` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_keluar_kategori`
--
ALTER TABLE `stok_keluar_kategori`
  MODIFY `id_stok_keluar_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stok_masuk`
--
ALTER TABLE `stok_masuk`
  MODIFY `id_stok_masuk` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_masuk_kategori`
--
ALTER TABLE `stok_masuk_kategori`
  MODIFY `id_stok_masuk_kategori` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
