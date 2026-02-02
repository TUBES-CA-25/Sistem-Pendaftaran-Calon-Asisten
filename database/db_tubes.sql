-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2026 at 01:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tubes`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `absensi_wawancara_II` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_wawancara_III` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_tes_tertulis` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_wawancara_I` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_presentasi` varchar(225) NOT NULL DEFAULT 'Alpha',
  `status_akhir` varchar(20) DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `id_mahasiswa`, `absensi_wawancara_II`, `absensi_wawancara_III`, `absensi_tes_tertulis`, `absensi_wawancara_I`, `absensi_presentasi`, `status_akhir`) VALUES
(22, 91, 'Hadir', '-', 'Hadir', 'Hadir', 'Hadir', 'Lolos'),
(23, 97, '-', '-', '-', '-', '-', '-'),
(24, 98, '-', '-', '-', '-', '-', '-'),
(26, 100, '-', '-', '-', '-', '-', '-'),
(29, 112, '-', '-', '-', '-', '-', '-'),
(30, 113, '-', '-', '-', '-', '-', '-');

-- --------------------------------------------------------

--
-- Table structure for table `bank_soal`
--

CREATE TABLE `bank_soal` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_soal`
--

INSERT INTO `bank_soal` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`, `token`, `is_active`) VALUES
(34, 'Ujian CCA 2025', 'Ujian Pendaftaran Calon Asisten laboratorium 2026', '2026-01-31 11:47:52', '2026-01-31 13:44:31', '12345', 1);

-- --------------------------------------------------------

--
-- Table structure for table `berkas_mahasiswa`
--

CREATE TABLE `berkas_mahasiswa` (
  `id` int(11) NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `foto` text NOT NULL,
  `cv` text NOT NULL,
  `transkrip_nilai` text NOT NULL,
  `surat_pernyataan` text NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berkas_mahasiswa`
--

INSERT INTO `berkas_mahasiswa` (`id`, `id_mahasiswa`, `foto`, `cv`, `transkrip_nilai`, `surat_pernyataan`, `accepted`, `created_at`, `modified`) VALUES
(75, 91, '697e5b91bae9e.png', '697e5b91bb1d5.pdf', '697e5b91bb439.pdf', '697e5b91bb6e0.pdf', 1, '2026-01-31 19:39:02', NULL),
(76, 113, '698061359da32.png', '698061359ddd7.pdf', '698061359df1e.pdf', '698061359e02b.pdf', 1, '2026-02-02 08:32:53', NULL),
(77, 114, '69806a92290ce.jpg', '69806a922956a.pdf', '69806a922979d.pdf', '69806a92299bc.pdf', 0, '2026-02-02 09:12:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dashboard`
--

CREATE TABLE `dashboard` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `deskripsi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deadline_kegiatan`
--

CREATE TABLE `deadline_kegiatan` (
  `id` int(11) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deadline_kegiatan`
--

INSERT INTO `deadline_kegiatan` (`id`, `jenis`, `tanggal`, `updated_at`) VALUES
(1, 'kelengkapan_berkas', '2026-02-10', '2026-01-28 11:01:28'),
(2, 'tes_tertulis', '2026-01-02', '2026-01-28 11:01:16'),
(3, 'tahap_wawancara', '2026-01-06', '2026-01-20 05:18:51'),
(4, 'pengumuman', '2026-01-21', '2026-01-28 11:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_presentasi`
--

CREATE TABLE `jadwal_presentasi` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_presentasi` int(10) UNSIGNED NOT NULL,
  `id_ruangan` int(10) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jawaban`
--

CREATE TABLE `jawaban` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_soal` int(10) UNSIGNED NOT NULL,
  `id_mahasiswa` int(11) UNSIGNED NOT NULL,
  `jawaban` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jawaban`
--

INSERT INTO `jawaban` (`id`, `id_soal`, `id_mahasiswa`, `jawaban`, `created_at`, `modified`) VALUES
(505, 200, 91, '3', '2026-02-02 05:19:57', NULL),
(506, 202, 91, '', '2026-02-02 05:19:57', NULL),
(507, 200, 113, '1', '2026-02-02 08:39:49', NULL),
(508, 202, 113, 'ads', '2026-02-02 08:39:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id`, `nama`, `created_at`, `modified`) VALUES
(1, 'Teknik Informatika', '2024-10-10 11:27:57', NULL),
(2, 'Sistem Informasi', '2024-10-10 11:28:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_admin`
--

CREATE TABLE `kegiatan_admin` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kegiatan_admin`
--

INSERT INTO `kegiatan_admin` (`id`, `judul`, `tanggal`, `deskripsi`, `created_at`) VALUES
(1, 'makan makan', '2026-01-18', '123213246535', '2026-01-16 22:40:21'),
(2, 'technical meeting', '2026-01-27', 'sad', '2026-01-24 00:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama`, `created_at`, `modified`) VALUES
(1, 'A1', '2024-10-10 11:29:26', NULL),
(2, 'A2', '2024-10-10 11:29:34', NULL),
(3, 'A3', '2024-10-10 11:29:41', NULL),
(4, 'A4', '2024-10-10 11:29:46', NULL),
(5, 'A5', '2024-10-10 11:29:51', NULL),
(6, 'A6', '2024-10-10 11:29:56', NULL),
(7, 'A7', '2024-10-10 11:30:01', NULL),
(8, 'A8', '2024-10-10 11:30:06', NULL),
(9, 'A9', '2024-10-10 11:30:12', NULL),
(10, 'B1', '2024-10-10 11:30:17', NULL),
(11, 'B2', '2024-10-10 11:30:21', NULL),
(12, 'B3', '2024-10-10 11:30:25', NULL),
(13, 'B4', '2024-10-10 11:30:29', NULL),
(14, 'B5', '2024-10-10 11:30:33', NULL),
(15, 'B6', '2024-10-10 11:30:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_jurusan` int(11) DEFAULT NULL,
  `stambuk` varchar(11) NOT NULL,
  `id_kelas` int(10) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('Wanita','Pria') DEFAULT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `status_akhir` enum('Pending','Lulus','Tidak Lulus') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `id_user`, `id_jurusan`, `stambuk`, `id_kelas`, `nama_lengkap`, `alamat`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `no_telp`, `created_at`, `modified`, `foto_profil`, `status_akhir`) VALUES
(91, 57, 1, '13020230309', 10, 'Raihan Nur Rizqillah', 'Antang', 'Wanita', 'Kota Makassar', '2026-01-30', '08219503918', '2025-12-17 02:16:52', NULL, '697e59763710d.png', 'Tidak Lulus'),
(97, 65, 1, '13020230308', 2, 'Raihan Nur', 'jalan sunu', 'Pria', 'Makassar', '2026-01-29', '0811423743', '2026-01-28 08:52:35', NULL, NULL, 'Pending'),
(98, 66, NULL, '13020230311', NULL, '13020230311@umi.ac.id', NULL, NULL, NULL, NULL, NULL, '2026-02-01 08:22:23', NULL, NULL, 'Pending'),
(100, 68, 1, '13020230232', 7, 'La Ode Dhaifan', 'Jalan Jati', 'Pria', 'Raha', '2025-12-18', '082214958313', '2026-02-02 05:54:41', NULL, NULL, 'Pending'),
(112, 70, 1, '13020230245', 10, 'Sitti Aisyah', 'Bonto Bila 13', 'Wanita', 'Pangkajenne', '2005-01-13', '087863391808', '2026-02-02 06:55:06', NULL, '69804b8e6b03b.png', 'Pending'),
(113, 67, 2, '13020230306', 5, 'Nahwa Kaka', 'Jalan antang', 'Pria', 'Bandung', '2026-02-02', '082195039180', '2026-02-02 08:18:33', NULL, '69805f5fc7706.png', 'Pending'),
(114, 71, 1, '13020230300', 11, 'Rifki', 'Nipa', 'Wanita', 'Nipa', '2026-02-02', '082195031980', '2026-02-02 08:59:02', NULL, '6980675641dee.jpeg', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `nilai_akhir`
--

CREATE TABLE `nilai_akhir` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `nilai` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `total_nilai` int(3) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_akhir`
--

INSERT INTO `nilai_akhir` (`id`, `id_mahasiswa`, `nilai`, `total_nilai`, `created_at`, `modified`) VALUES
(69, 91, 0, 0, '2026-02-02 05:19:57', NULL),
(70, 113, 0, 90, '2026-02-02 08:39:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_mahasiswa` int(11) UNSIGNED NOT NULL,
  `pesan` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `id_mahasiswa`, `pesan`, `is_read`, `created_at`, `modified`) VALUES
(17, 91, 'halo', 1, '2025-12-26 05:45:00', NULL),
(20, 91, 'selamat pagi', 1, '2025-12-26 05:55:42', NULL),
(22, 91, 'selamat pagi', 1, '2025-12-26 05:55:44', NULL),
(24, 91, 'selamat pagi', 1, '2025-12-26 05:55:48', NULL),
(31, 91, 'selamat pagi', 1, '2025-12-26 05:55:53', NULL),
(33, 91, 'selamat pagi', 1, '2025-12-26 05:55:58', NULL),
(34, 91, 'selamat malam', 1, '2025-12-26 05:56:29', NULL),
(36, 91, 'selamat malam', 1, '2025-12-26 05:56:33', NULL),
(37, 91, 'assalamualaikum', 1, '2025-12-26 08:37:43', NULL),
(39, 91, 'assalamualaikum', 1, '2025-12-26 08:37:44', NULL),
(41, 91, 'assalamualaikum', 1, '2025-12-26 08:37:53', NULL),
(42, 91, 'asd', 1, '2025-12-26 08:38:43', NULL),
(44, 91, 'asd', 1, '2025-12-26 08:38:45', NULL),
(50, 91, '123', 1, '2026-01-21 10:17:36', NULL),
(61, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 0. Status: TIDAK LULUS. Jangan berkecil hati, tetap semangat!', 1, '2026-01-22 20:59:45', NULL),
(62, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 30. Status: TIDAK LULUS. Jangan berkecil hati, tetap semangat!', 1, '2026-01-22 21:00:29', NULL),
(63, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 90. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 1, '2026-01-22 21:24:32', NULL),
(64, 91, 'hello kawan', 1, '2026-01-23 09:20:54', NULL),
(65, 91, 'hello kawan', 1, '2026-01-23 09:20:54', NULL),
(66, 91, 'hello kawan', 1, '2026-01-23 09:20:54', NULL),
(67, 91, 'hello kawan', 1, '2026-01-23 09:20:54', NULL),
(68, 91, 'hello kawan', 1, '2026-01-23 09:20:54', NULL),
(69, 91, 'kamu sehat?', 1, '2026-01-23 09:26:40', NULL),
(70, 91, 'kamu sehat?', 1, '2026-01-23 09:26:40', NULL),
(71, 91, 'kamu sehat?', 1, '2026-01-23 09:26:40', NULL),
(72, 91, 'kamu sehat?', 1, '2026-01-23 09:26:40', NULL),
(73, 91, 'kamu sehat?', 1, '2026-01-23 09:26:40', NULL),
(74, 91, 'kamu sehat?', 1, '2026-01-23 09:26:40', NULL),
(75, 91, 'aku sehat?', 1, '2026-01-23 09:28:27', NULL),
(76, 91, 'Pesan Revisi/Keterangan: selamat ulang tahun', 1, '2026-01-23 09:44:39', NULL),
(77, 91, 'Pesan Revisi/Keterangan: perbaiki judulmu', 1, '2026-01-23 09:48:20', NULL),
(78, 91, 'Pesan Revisi/Keterangan: selamat soree 123\n', 1, '2026-01-23 10:01:38', NULL),
(79, 91, 'Pesan Revisi/Keterangan: selamat pagi', 1, '2026-01-23 10:12:51', NULL),
(80, 91, 'Pesan Revisi/Keterangan: pesan terakhir', 1, '2026-01-23 10:14:26', NULL),
(81, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-23 10:24:40', NULL),
(84, 91, 'Pesan Revisi/Keterangan: selamat siang malam', 1, '2026-01-23 11:31:09', NULL),
(85, 91, 'Pesan Revisi/Keterangan: hello kawan rai', 1, '2026-01-23 11:33:59', NULL),
(86, 91, 'Pesan Revisi/Keterangan: bubu baba', 1, '2026-01-23 11:37:05', NULL),
(87, 91, 'baba bubu', 1, '2026-01-23 11:37:51', NULL),
(88, 91, 'baba bubu', 1, '2026-01-23 11:37:51', NULL),
(89, 91, 'selolo\n', 1, '2026-01-23 11:40:14', NULL),
(90, 91, 'Pesan Revisi/Keterangan: ololo', 1, '2026-01-23 11:40:30', NULL),
(91, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-24 00:38:40', NULL),
(92, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-26 13:08:31', NULL),
(93, 91, 'Judul presentasi Anda DITOLAK. Silakan cek revisi.', 1, '2026-01-26 13:13:28', NULL),
(94, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-26 13:13:44', NULL),
(95, 91, 'Judul presentasi Anda DITOLAK. Silakan cek revisi.', 1, '2026-01-26 13:13:53', NULL),
(96, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-26 13:16:47', NULL),
(97, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 100. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 1, '2026-01-27 21:47:24', NULL),
(98, 97, 'jelek cv mu', 1, '2026-01-28 10:10:18', NULL),
(99, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 90. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 1, '2026-01-28 11:25:30', NULL),
(100, 91, 'Pesan Revisi/Keterangan: judulmu bagus', 1, '2026-01-28 22:54:20', NULL),
(101, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-29 20:20:56', NULL),
(102, 91, 'Judul presentasi Anda DITOLAK. Silakan cek revisi.', 1, '2026-01-29 20:23:27', NULL),
(103, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-29 20:25:58', NULL),
(104, 91, 'Judul presentasi Anda DITOLAK. Silakan cek revisi.', 1, '2026-01-29 20:29:07', NULL),
(105, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-01-29 20:29:12', NULL),
(106, 91, 'Judul presentasi Anda DITOLAK. Silakan cek revisi.', 1, '2026-01-29 20:33:19', NULL),
(107, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 90. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 1, '2026-01-29 20:38:41', NULL),
(108, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 100. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 1, '2026-01-31 03:49:18', NULL),
(109, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 100. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 1, '2026-01-31 14:54:27', NULL),
(110, 91, 'Nilai Tes Tertulis Anda telah keluar. Skor: 20. Status: TIDAK LULUS. Jangan berkecil hati, tetap semangat!', 1, '2026-01-31 23:48:53', NULL),
(111, 113, 'Nilai Tes Tertulis Anda telah keluar. Skor: 90. Status: LULUS. Selamat! Silahkan pantau jadwal interview selanjutnya.', 0, '2026-02-02 09:14:51', NULL),
(112, 91, 'Judul presentasi Anda telah DITERIMA.', 1, '2026-02-02 09:18:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `pengumuman` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presentasi`
--

CREATE TABLE `presentasi` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `ppt` text NOT NULL DEFAULT '',
  `makalah` text NOT NULL DEFAULT '',
  `is_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `is_revisi` tinyint(1) NOT NULL DEFAULT 0,
  `keterangan` varchar(255) NOT NULL DEFAULT 'Sedang menunggu verifikasi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presentasi`
--

INSERT INTO `presentasi` (`id`, `id_mahasiswa`, `judul`, `ppt`, `makalah`, `is_accepted`, `is_revisi`, `keterangan`, `created_at`, `modified`) VALUES
(19, 91, 'technical meeting', '698074fc48584.pptx', '698074fc48a89.pdf', 1, 0, 'Sedang menunggu verifikasi', '2026-02-02 09:18:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `nama`, `created_at`, `modified`) VALUES
(5, 'LAB MULTIMEDIA', '2025-01-03 10:14:07', '2026-02-01 01:36:45'),
(10, 'RUANGAN KEPALA LAB II', '2025-02-01 07:00:06', NULL),
(18, 'LAB STARTUP', '2026-01-08 06:11:37', '2026-01-31 07:26:08'),
(19, 'LAB INTERNET OF THINGS', '2026-01-14 06:15:30', '2026-02-01 01:38:29'),
(23, 'RUANGAN KEPALA LAB I', '2026-01-31 14:27:35', NULL),
(25, 'LAB COMPUTER VISION', '2026-01-31 14:29:28', NULL),
(26, 'LAB DATA SCIENCE', '2026-01-31 14:31:44', NULL),
(27, 'LAB COMPUTER NETWORK', '2026-02-01 08:37:24', '2026-02-01 01:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `soal`
--

CREATE TABLE `soal` (
  `id` int(11) UNSIGNED NOT NULL,
  `bank_soal_id` int(11) DEFAULT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `pilihan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '\'Bukan soal pilihan ganda\'',
  `jawaban` varchar(255) DEFAULT '''soal tidak punya jawaban''',
  `status_soal` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal`
--

INSERT INTO `soal` (`id`, `bank_soal_id`, `deskripsi`, `image_url`, `pilihan`, `jawaban`, `status_soal`, `created_at`, `modified`) VALUES
(200, 34, 'asd', 'res/uploads/soal/soal_697df0128aaf3.png', 'A. a, B. as, C. as, D. s', 'A', 'pilihan_ganda', '2026-01-31 12:05:38', NULL),
(202, 34, 'jalan?', 'res/uploads/soal/soal_697e159505499.png', 'bukan soal pilihan', '\'soal tidak punya jawaban\'', 'essay', '2026-01-31 14:45:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `test_tertulis`
--

CREATE TABLE `test_tertulis` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `id_ruangan` int(10) UNSIGNED NOT NULL,
  `no_meja` int(2) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '" "',
  `stambuk` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `role` enum('Admin','User') NOT NULL DEFAULT 'User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL,
  `id_ruang_presentasi` int(11) DEFAULT NULL,
  `id_ruang_tes_tulis` int(11) DEFAULT NULL,
  `id_ruang_wawancara` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `stambuk`, `password`, `reset_token_hash`, `reset_token_expires_at`, `role`, `created_at`, `modified`, `id_ruang_presentasi`, `id_ruang_tes_tulis`, `id_ruang_wawancara`) VALUES
(10, 'admin', '111111111', '12345678', NULL, NULL, 'Admin', '2025-12-17 01:44:54', '0000-00-00 00:00:00', NULL, NULL, NULL),
(32, 'admin', 'adminiclabs', '$2y$10$AfzljbcP/jV9pJk.dU1hcOHe1yfCFO1vA7YOTEbHQ7/AI4oLkyYLu', NULL, NULL, 'Admin', '2024-12-18 09:58:31', '0000-00-00 00:00:00', NULL, NULL, NULL),
(33, 'admin12', 'admin', '$2y$10$7wE5Q2S1paQm/EtBFKuuZeb4RLzUjt4sEny98T/anNlqCQVzGyD5q', NULL, NULL, 'Admin', '2025-01-03 08:52:28', '0000-00-00 00:00:00', NULL, NULL, NULL),
(55, '\"admin4 \"', 'admin4', 'admin4', NULL, NULL, 'Admin', '2025-12-16 23:52:35', '0000-00-00 00:00:00', NULL, NULL, NULL),
(57, 'umi@umi.ac.id', '13020230309', '$2y$10$8xILIVdyUCCcqBPyYl0WleMtmWycfgtR5x.SyD.BOLgc5dgJnzXBy', NULL, NULL, 'User', '2025-12-17 02:16:03', '0000-00-00 00:00:00', NULL, 4, 5),
(60, '12345@umi.ac.id', '13020230301', '$2y$10$c0ZFC3HYRUCYhl2mSATb5OqB4uNzzEHM0FDN1bShYD3dMvlHjft3O', NULL, NULL, 'User', '2026-01-28 08:31:27', NULL, NULL, NULL, NULL),
(61, '12345@umi.ac.id', '13020230100', '$2y$10$6PO19YP3Zhmr2TApXw9ioega4OOzctlAM6lkVEhWGrglYI0QyWik.', NULL, NULL, 'User', '2026-01-28 08:31:46', NULL, NULL, NULL, NULL),
(62, '12345@umi.ac.id', '13020230101', '$2y$10$PtxVwvwy68qvQCAMDO/uteTlJM9woW44bv/iTzxyCprDOgvk0RMrq', NULL, NULL, 'User', '2026-01-28 08:32:04', NULL, NULL, NULL, NULL),
(63, '12345@umi.ac.id', '13020230303', '$2y$10$3gHrqRfV9FCzBMZEYbk19.OWw8LTTSeXlOe4WDb2kG3CBO0qeaQOm', NULL, NULL, 'User', '2026-01-28 08:32:22', NULL, NULL, NULL, NULL),
(64, '12345@umi.ac.id', '13020230304', '$2y$10$0TkfgS5JJhiw9/bujtmE6u7CeoP1QkdUvs6dImg/ZxVbv7X2bYdPy', NULL, NULL, 'User', '2026-01-28 08:32:28', NULL, NULL, NULL, NULL),
(65, '12345@umi.ac.id', '13020230308', '$2y$10$XE0ZP6CThJv.VrNBZ37IR.Ahlc8Dqcag0m12YYUdZiDQZs9IZaisS', NULL, NULL, 'User', '2026-01-28 08:52:35', NULL, NULL, NULL, NULL),
(66, '13020230311@umi.ac.id', '13020230311', '$2y$10$ryCwJJFOQ79dbQ3rh67o1uovjh.XdZxBi6OOkVR8W2FG9IAe5PzQi', NULL, NULL, 'User', '2026-02-01 08:22:23', NULL, NULL, NULL, NULL),
(67, '13020230306@student.umi.ac.id', '13020230306', '$2y$10$PRaAtUAsX3RijJTq02FTw.BThRHlUgax8131KWObgjqA4uB764VZG', '2c79b5fde20a7ac5e66deafbbbdf41a232c164e88a6081776a180e4c40dd6784', '2026-02-02 14:50:28', 'User', '2026-02-02 05:28:11', NULL, NULL, NULL, NULL),
(68, '13020230232@student.umi.ac.id', '13020230232', '$2y$10$AV.5VhFuGmFGF5i2Km5Dbe8/VHr3XM7/lAVbRBElZHIut0NZWRrUm', NULL, NULL, 'User', '2026-02-02 05:54:41', NULL, NULL, NULL, NULL),
(70, '13020230245@student.umi.ac.id', '13020230245', '$2y$10$.12mmolA7wVC1bJKIXZlK.c/ueaDzeOfnvn75UJLhRDtxJF3WV78G', NULL, NULL, 'User', '2026-02-02 06:54:27', NULL, NULL, NULL, NULL),
(71, '13020230300@umi.ac.id', '13020230300', '$2y$10$u7erK99iJjzJFg6./zcZDet9kv9WvEQCxozvqKUUO5vHebLfKgT2a', NULL, NULL, 'User', '2026-02-02 08:56:16', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wawancara`
--

CREATE TABLE `wawancara` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_mahasiswa` int(11) UNSIGNED NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `jenis_wawancara` varchar(255) NOT NULL,
  `waktu` time NOT NULL,
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wawancara`
--

INSERT INTO `wawancara` (`id`, `id_mahasiswa`, `id_ruangan`, `jenis_wawancara`, `waktu`, `tanggal`, `created_at`, `modified`) VALUES
(15, 91, 6, 'wawancara kepala lab II', '00:28:00', '2026-01-27', '2026-01-26 00:28:47', NULL),
(19, 97, 6, 'Tes Tertulis', '19:26:00', '2026-02-06', '2026-01-28 11:21:28', NULL),
(20, 91, 5, 'wawancara kepala lab I', '04:53:00', '2026-01-02', '2026-01-29 20:50:43', NULL),
(21, 97, 10, 'wawancara kepala lab II', '00:41:00', '2026-02-12', '2026-01-31 16:39:39', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `berkas_mahasiswa`
--
ALTER TABLE `berkas_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_berkas_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `dashboard`
--
ALTER TABLE `dashboard`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `deadline_kegiatan`
--
ALTER TABLE `deadline_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jenis` (`jenis`);

--
-- Indexes for table `jadwal_presentasi`
--
ALTER TABLE `jadwal_presentasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_presentasi` (`id_presentasi`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indexes for table `jawaban`
--
ALTER TABLE `jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_jawaban1` (`id_mahasiswa`),
  ADD KEY `fk_mahasiswa_jawaban2` (`id_soal`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan_admin`
--
ALTER TABLE `kegiatan_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`),
  ADD UNIQUE KEY `stambuk` (`stambuk`),
  ADD UNIQUE KEY `no_telp` (`no_telp`),
  ADD KEY `fk_mahasiswa_user1` (`id_kelas`),
  ADD KEY `fk_mahasiswa_user2` (`id_jurusan`);

--
-- Indexes for table `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_notifikasi` (`id_mahasiswa`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presentasi`
--
ALTER TABLE `presentasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bank_soal_id` (`bank_soal_id`);

--
-- Indexes for table `test_tertulis`
--
ALTER TABLE `test_tertulis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`stambuk`);

--
-- Indexes for table `wawancara`
--
ALTER TABLE `wawancara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_wawancara` (`id_mahasiswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `bank_soal`
--
ALTER TABLE `bank_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `berkas_mahasiswa`
--
ALTER TABLE `berkas_mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `dashboard`
--
ALTER TABLE `dashboard`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deadline_kegiatan`
--
ALTER TABLE `deadline_kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jadwal_presentasi`
--
ALTER TABLE `jadwal_presentasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `jawaban`
--
ALTER TABLE `jawaban`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kegiatan_admin`
--
ALTER TABLE `kegiatan_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presentasi`
--
ALTER TABLE `presentasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT for table `test_tertulis`
--
ALTER TABLE `test_tertulis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `wawancara`
--
ALTER TABLE `wawancara`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `berkas_mahasiswa`
--
ALTER TABLE `berkas_mahasiswa`
  ADD CONSTRAINT `fk_mahasiswa_berkas_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dashboard`
--
ALTER TABLE `dashboard`
  ADD CONSTRAINT `fk_mahasiswa_dashboard` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_presentasi`
--
ALTER TABLE `jadwal_presentasi`
  ADD CONSTRAINT `jadwal_presentasi_ibfk_3` FOREIGN KEY (`id_presentasi`) REFERENCES `presentasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_presentasi_ibfk_4` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jawaban`
--
ALTER TABLE `jawaban`
  ADD CONSTRAINT `fk_mahasiswa_jawaban1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mahasiswa_jawaban2` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `fk_mahasiswa_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mahasiswa_user1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mahasiswa_user2` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  ADD CONSTRAINT `fk_mahasiswa_nilai_akhir1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_mahasiswa_notifikasi` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presentasi`
--
ALTER TABLE `presentasi`
  ADD CONSTRAINT `presentasi_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `soal`
--
ALTER TABLE `soal`
  ADD CONSTRAINT `fk_soal_bank_soal` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `test_tertulis`
--
ALTER TABLE `test_tertulis`
  ADD CONSTRAINT `test_tertulis_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_tertulis_ibfk_2` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wawancara`
--
ALTER TABLE `wawancara`
  ADD CONSTRAINT `fk_mahasiswa_wawancara` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
