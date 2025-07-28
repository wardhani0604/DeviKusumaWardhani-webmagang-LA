-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 04:49 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pertanian_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `peserta_id` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `datang` time DEFAULT NULL,
  `pulang` time DEFAULT NULL,
  `status` enum('Hadir','Izin','Sakit') DEFAULT 'Hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `peserta_id`, `tanggal`, `datang`, `pulang`, `status`) VALUES
(1, '062240512660', '2025-06-02', '06:33:50', NULL, 'Hadir'),
(2, '062240512660', '2025-05-02', '06:34:28', '06:34:45', 'Hadir');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'mia', '$2y$10$Ay5wSyXJHyEyDew9zKIv3utG7/WF7TjmqsnHaI1DHIbhTWncJB0Gq', '2025-05-19 11:58:56');

-- --------------------------------------------------------

--
-- Table structure for table `hrd`
--

CREATE TABLE `hrd` (
  `hrd_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hrd`
--

INSERT INTO `hrd` (`hrd_id`, `username`, `password`) VALUES
(1, 'Lilik', '123');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `peserta_id` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `aktivitas` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `bukti_kegiatan` varchar(255) DEFAULT NULL,
  `validasi` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `peserta_id`, `tanggal`, `aktivitas`, `status`, `bukti_kegiatan`, `validasi`, `catatan`) VALUES
(3, '062240512660', '2025-05-02', 'Mengelola data di word', 'Selesai', '1753054689_ingris.docx', 'Revisi', 'rapikan paragrafnya');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id` int(11) NOT NULL,
  `peserta_id` varchar(20) NOT NULL,
  `persiapan_pekerjaan` int(11) NOT NULL,
  `penggunaan_alat` int(11) NOT NULL,
  `penyelesaian_pekerjaan` int(11) NOT NULL,
  `kualitas_pekerjaan` int(11) NOT NULL,
  `rata_teknis` int(11) NOT NULL,
  `disiplin_kerja` int(11) NOT NULL,
  `kerjasama` int(11) NOT NULL,
  `inisiatif` int(11) NOT NULL,
  `tanggung_jawab` int(11) NOT NULL,
  `kebersihan` int(11) NOT NULL,
  `kerapihan` int(11) NOT NULL,
  `rata_non_teknis` int(11) NOT NULL,
  `rata_rata_akhir` int(11) NOT NULL,
  `predikat` varchar(20) DEFAULT NULL,
  `tanggal_penilaian` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id`, `peserta_id`, `persiapan_pekerjaan`, `penggunaan_alat`, `penyelesaian_pekerjaan`, `kualitas_pekerjaan`, `rata_teknis`, `disiplin_kerja`, `kerjasama`, `inisiatif`, `tanggung_jawab`, `kebersihan`, `kerapihan`, `rata_non_teknis`, `rata_rata_akhir`, `predikat`, `tanggal_penilaian`) VALUES
(2, '062240512660', 80, 78, 77, 79, 79, 80, 77, 70, 80, 80, 80, 78, 79, 'Baik', '2025-07-21');

-- --------------------------------------------------------

--
-- Table structure for table `peserta`
--

CREATE TABLE `peserta` (
  `id` int(11) NOT NULL,
  `peserta_id` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `lembaga_pendidikan` varchar(75) NOT NULL,
  `jurusan` varchar(65) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `durasi_magang` int(11) DEFAULT NULL,
  `status_validasi` enum('pending','diterima','ditolak') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `peserta`
--

INSERT INTO `peserta` (`id`, `peserta_id`, `nama`, `no_hp`, `lembaga_pendidikan`, `jurusan`, `username`, `password`, `tanggal_masuk`, `tanggal_keluar`, `durasi_magang`, `status_validasi`) VALUES
(20, '062240512674', 'M.Ikbar Rafif Al-Fawwaz', '6289636254653', 'Politeknik Negeri Sriwijaya Palembang', 'Akuntansi', 'Ikbar', '$2y$10$XKZnhWuMzziQAzusM6qwO.mViSuSN9Yfn0RsCmxSHzevRceakJ4Z.', '2025-02-10', '2025-06-30', 4, ''),
(21, '062240512664', 'Susana Nunaki', '6289636254653', 'Politeknik Negeri Sriwijaya Palembang', 'Manajemen Informatika', 'Susana', '$2y$10$IyAlOuEEt3Hp0tGYepiDHO7UGPqLkWQxY4iliGwtqJkU.CEx27VRu', '2025-02-10', '2025-06-30', 4, 'diterima'),
(22, '062240512660', 'Tio Diman Saputra', '6289636254653', 'Politeknik Negeri Sriwijaya Palembang', 'Akuntansi', 'Tio', '$2y$10$IIcN/QKxi39Q9OiiznKckOa/HQdgqLBQcsglRAkCSXjiZIHyZJshW', '2025-02-10', '2025-06-30', 4, 'diterima'),
(23, 'SMKPGRI2P1', 'Rizki Alpiansyah', '6289636254653', 'SMK PGRI 2 Palembang', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Rizki', '$2y$10$DaUX9jw3Y8CmAHuYJ9qQ6OYBG5OHjyNjoe5Ie9IqxQ1onq961qQ86', '2023-09-01', '2024-03-01', 5, ''),
(24, 'SMKPGRI2P2', 'Gusti Pranata', '6289636254653', 'SMK PGRI 2 Palembang', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Gusti', '$2y$10$uKVg5MjaYSwo2FPgAamMn.WQUAbDxcumtob1yyCq4ZL2uOjzoZtni', '2023-09-01', '2024-03-01', 5, 'diterima'),
(25, 'SMKPGRI2P3', 'Richard Lamshegar Sipahutar', '6289636254653', 'SMK PGRI 2 Palembang', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Richard', '$2y$10$yUXNj3Iy.brM/dcCPb6I6eo33dOpVbSMqJnq880KEgYiScsUwdfKq', '2023-09-01', '2024-03-01', 5, 'ditolak'),
(26, 'SMKN2P1', 'M. Marelky Alghifari', '6289636254653', 'SMK Negeri 2 Palembang', 'Teknik Komputer dan Jaringan', 'Marelky', '$2y$10$VZF1FL9xy/LzbfD2ra6r7ue7gvEQI9XF7uRL5D5nS6wN6QRmi4olK', '2024-08-01', '2024-12-13', 4, 'diterima'),
(27, 'SMKN2P2', 'M. Fadhil Febriliano', '6289636254653', 'SMK Negeri 2 Palembang', 'Teknik Komputer dan Jaringan', 'Fadhil', '$2y$10$RVlmtX0S6heBGDpQoI.Bx.lzP0Xr8k0gyyCFXNq/ddoBGPemlNXqK', '2024-08-01', '2024-12-13', 4, 'diterima'),
(28, 'SMKXP1', 'Yohana Aprilia Sibarani', '6289636254653', 'SMK Xaverius Palembang', 'OTKP', 'Yohana', '$2y$10$CIdSWfWt72fzoWmbPlPJhOClhaQ8iR.Yxr9u6xMPiCbRXhpfNBGXe', '2024-02-01', '2024-04-30', 2, ''),
(29, '01020582226007', 'Siti Salsabilah', '6289636254653', 'Universitas Negeri Sriwijaya', 'Kesekretarisan', 'Siti', '$2y$10$3RHwr87Wk96HaTip40KMpuG1NSBhq2uJNLNMIqTZNC3nSbKtejOm2', '2024-07-03', '2024-08-03', 1, ''),
(30, '01020582226020', 'Anggi Maharani', '6289636254653', 'Universitas Negeri Sriwijaya', 'Kesekretarisan', 'Anggi', '$2y$10$PxJCjTW31BcIM3.9jY66A.4W1XSX4.IB.pjRVcwACi8ovZyM.ZdDa', '2024-07-03', '2024-08-03', 1, 'diterima');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peserta_id` (`peserta_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `hrd`
--
ALTER TABLE `hrd`
  ADD PRIMARY KEY (`hrd_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peserta_id` (`peserta_id`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peserta_id` (`peserta_id`);

--
-- Indexes for table `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `peserta_id` (`peserta_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hrd`
--
ALTER TABLE `hrd`
  MODIFY `hrd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peserta`
--
ALTER TABLE `peserta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`peserta_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`peserta_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`peserta_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
