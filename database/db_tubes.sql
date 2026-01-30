-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Jan 2026 pada 02.23
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_mahasiswa` int(10) UNSIGNED NOT NULL,
  `absensi_wawancara_II` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_wawancara_III` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_tes_tertulis` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_wawancara_I` varchar(225) NOT NULL DEFAULT 'Alpha',
  `absensi_presentasi` varchar(225) NOT NULL DEFAULT 'Alpha'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `id_mahasiswa`, `absensi_wawancara_II`, `absensi_wawancara_III`, `absensi_tes_tertulis`, `absensi_wawancara_I`, `absensi_presentasi`) VALUES
(22, 91, 'Hadir', '-', 'Hadir', 'Hadir', 'Hadir'),
(23, 97, '-', '-', '-', '-', '-');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bank_soal`
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
-- Dumping data untuk tabel `bank_soal`
--

INSERT INTO `bank_soal` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`, `token`, `is_active`) VALUES
(26, 'Ujiansda', '1221ee12', '2026-01-20 12:31:51', '2026-01-20 12:31:51', '12e212e', 0),
(32, 'Ujian 2025', '3rwer', '2026-01-20 23:51:15', '2026-01-28 10:12:40', 'ssd', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `berkas_mahasiswa`
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
-- Dumping data untuk tabel `berkas_mahasiswa`
--

INSERT INTO `berkas_mahasiswa` (`id`, `id_mahasiswa`, `foto`, `cv`, `transkrip_nilai`, `surat_pernyataan`, `accepted`, `created_at`, `modified`) VALUES
(30, 91, '6971d16cf313f.png', '694212c67df9b.pdf', '694212c67e1b3.pdf', '694212c67e3b7.pdf', 1, '2025-12-17 02:17:42', NULL),
(31, 91, '6971d16cf313f.png', '69466eb8c34eb.pdf', '69466eb8c36c4.pdf', '69466eb8c37cc.pdf', 1, '2025-12-20 09:39:04', NULL),
(32, 91, '6971d16cf313f.png', '69466eba04f50.pdf', '69466eba04fd4.pdf', '69466eba050b1.pdf', 1, '2025-12-20 09:39:06', NULL),
(33, 91, '6971d16cf313f.png', '69466ebc5c859.pdf', '69466ebc5c909.pdf', '69466ebc5c9b0.pdf', 1, '2025-12-20 09:39:08', NULL),
(34, 91, '6971d16cf313f.png', '69466ebe6896e.pdf', '69466ebe68a00.pdf', '69466ebe68a92.pdf', 1, '2025-12-20 09:39:10', NULL),
(35, 91, '6971d16cf313f.png', '69466ebe8f009.pdf', '69466ebe8f08e.pdf', '69466ebe8f10e.pdf', 1, '2025-12-20 09:39:10', NULL),
(36, 91, '6971d16cf313f.png', '69466ebec3161.pdf', '69466ebec3204.pdf', '69466ebec328d.pdf', 1, '2025-12-20 09:39:10', NULL),
(37, 91, '6971d16cf313f.png', '6947813f0dfa8.pdf', '6947813f0e335.pdf', '6947813f0e6f0.pdf', 1, '2025-12-21 05:10:23', NULL),
(38, 91, '6971d16cf313f.png', '695fc4f2af071.pdf', '695fc4f2af3b2.pdf', '695fc4f2af4cb.pdf', 1, '2026-01-08 14:53:38', NULL),
(40, 91, '6971d16cf313f.png', '696cd1ee62f52.pdf', '696cd1ee6331f.pdf', '696cd1ee6343a.pdf', 1, '2026-01-18 12:28:30', NULL),
(41, 91, '6971d16cf313f.png', '696cd1f1a7ec6.pdf', '696cd1f1a801a.pdf', '696cd1f1a8151.pdf', 1, '2026-01-18 12:28:33', NULL),
(42, 91, '6971d16cf313f.png', '696cd1f1e2109.pdf', '696cd1f1e236c.pdf', '696cd1f1e25e7.pdf', 1, '2026-01-18 12:28:33', NULL),
(43, 91, '6971d16cf313f.png', '6971aeec66dbc.pdf', '6971aeec66ec4.pdf', '6971aeec66fae.pdf', 1, '2026-01-22 05:00:28', NULL),
(44, 91, '6971d16cf313f.png', '6971aeef660b1.pdf', '6971aeef661c5.pdf', '6971aeef66315.pdf', 1, '2026-01-22 05:00:31', NULL),
(45, 91, '6971d16cf313f.png', '6971aeefd8c38.pdf', '6971aeefd8d8e.pdf', '6971aeefd8f46.pdf', 1, '2026-01-22 05:00:31', NULL),
(46, 91, '6971d16cf313f.png', '6971aefb2e93e.pdf', '6971aefb2ea93.pdf', '6971aefb2ebea.pdf', 1, '2026-01-22 05:00:43', NULL),
(47, 91, '6971d16cf313f.png', '6971c5548cd70.pdf', '6971c5548ce87.pdf', '6971c5548cf81.pdf', 1, '2026-01-22 06:36:04', NULL),
(48, 91, '6971d16cf313f.png', '6971cbb174dcf.pdf', '6971cbb174f73.pdf', '6971cbb1750a7.pdf', 1, '2026-01-22 07:03:13', NULL),
(49, 91, '6971d16cf313f.png', '6971cdba163f1.pdf', '6971cdba16512.pdf', '6971cdba16621.pdf', 1, '2026-01-22 07:11:54', NULL),
(50, 91, '6972060f24e79.jpg', '6972060f25518.pdf', '6972060f25afd.pdf', '6972060f25f03.pdf', 1, '2026-01-22 11:12:15', NULL),
(51, 97, '6979df4266448.jpg', '6979df4266aa2.pdf', '6979df4266f98.pdf', '6979df426936a.pdf', 1, '2026-01-28 10:04:50', NULL),
(52, 97, '6979df43dcae5.jpg', '6979df43dcdd0.pdf', '6979df43dd033.pdf', '6979df43dd32e.pdf', 1, '2026-01-28 10:04:51', NULL),
(53, 97, '6979df510c617.jpg', '6979df510c93a.pdf', '6979df510cbfb.pdf', '6979df510ce59.pdf', 1, '2026-01-28 10:05:05', NULL),
(54, 97, '6979df5692747.jpg', '6979df5692a73.pdf', '6979df5692d2a.pdf', '6979df5692faf.pdf', 1, '2026-01-28 10:05:10', NULL),
(55, 97, '6979df717b111.jpg', '6979df717b365.pdf', '6979df717b498.pdf', '6979df717b8e8.pdf', 1, '2026-01-28 10:05:37', NULL),
(56, 97, '6979df7208677.jpg', '6979df720882f.pdf', '6979df7208999.pdf', '6979df7208b14.pdf', 1, '2026-01-28 10:05:38', NULL),
(57, 97, '6979df722deaf.jpg', '6979df722e442.pdf', '6979df722e99d.pdf', '6979df722ee64.pdf', 1, '2026-01-28 10:05:38', NULL),
(58, 97, '6979df725458f.jpg', '6979df72548fa.pdf', '6979df7254b4e.pdf', '6979df7254ddf.pdf', 1, '2026-01-28 10:05:38', NULL),
(59, 97, '6979df726a844.jpg', '6979df726b2f5.pdf', '6979df726b863.pdf', '6979df726bd30.pdf', 1, '2026-01-28 10:05:38', NULL),
(60, 97, '6979df728ef7e.jpg', '6979df728f38c.pdf', '6979df728f4ed.pdf', '6979df728f617.pdf', 1, '2026-01-28 10:05:38', NULL),
(61, 97, '6979dfad2028b.jpg', '6979dfad2059b.pdf', '6979dfad207fc.pdf', '6979dfad20ab2.pdf', 1, '2026-01-28 10:06:37', NULL),
(62, 97, '6979e04bb83bd.jpg', '6979e04bb87cb.pdf', '6979e04bb8a74.pdf', '6979e04bb8d26.pdf', 1, '2026-01-28 10:09:15', NULL),
(63, 97, '6979e04bce30b.jpg', '6979e04bce872.pdf', '6979e04bceb86.pdf', '6979e04bceebf.pdf', 1, '2026-01-28 10:09:15', NULL),
(64, 97, '6979e04bd962f.jpg', '6979e04bd9ba9.pdf', '6979e04bd9f20.pdf', '6979e04bda25e.pdf', 1, '2026-01-28 10:09:15', NULL),
(65, 97, '6979e04be3b46.jpg', '6979e04be3f89.pdf', '6979e04be4b10.pdf', '6979e04be4f5b.pdf', 1, '2026-01-28 10:09:15', NULL),
(66, 97, '6979e04c0278f.jpg', '6979e04c02b85.pdf', '6979e04c02e92.pdf', '6979e04c03185.pdf', 1, '2026-01-28 10:09:16', NULL),
(67, 97, '6979e04c17e6e.jpg', '6979e04c1828d.pdf', '6979e04c185c3.pdf', '6979e04c18ab2.pdf', 1, '2026-01-28 10:09:16', NULL),
(68, 97, '6979e04c1cd86.jpg', '6979e04c1d158.pdf', '6979e04c1d46a.pdf', '6979e04c1d98d.pdf', 1, '2026-01-28 10:09:16', NULL),
(69, 97, '6979e04c24b1c.jpg', '6979e04c24f68.pdf', '6979e04c2527d.pdf', '6979e04c25576.pdf', 1, '2026-01-28 10:09:16', NULL),
(70, 97, '6979e04c286e4.jpg', '6979e04c28a90.pdf', '6979e04c28d36.pdf', '6979e04c29077.pdf', 1, '2026-01-28 10:09:16', NULL),
(71, 97, '6979e0ca3feb2.jpeg', '6979e0ca401cd.pdf', '6979e0ca404b2.pdf', '6979e0ca40752.pdf', 1, '2026-01-28 10:11:22', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `dashboard`
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
-- Struktur dari tabel `deadline_kegiatan`
--

CREATE TABLE `deadline_kegiatan` (
  `id` int(11) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `deadline_kegiatan`
--

INSERT INTO `deadline_kegiatan` (`id`, `jenis`, `tanggal`, `updated_at`) VALUES
(1, 'kelengkapan_berkas', '2026-02-10', '2026-01-28 11:01:28'),
(2, 'tes_tertulis', '2026-01-02', '2026-01-28 11:01:16'),
(3, 'tahap_wawancara', '2026-01-06', '2026-01-20 05:18:51'),
(4, 'pengumuman', '2026-01-21', '2026-01-28 11:00:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_presentasi`
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

--
-- Dumping data untuk tabel `jadwal_presentasi`
--

INSERT INTO `jadwal_presentasi` (`id`, `id_presentasi`, `id_ruangan`, `tanggal`, `waktu`, `created_at`, `modified`, `is_completed`) VALUES
(13, 18, 19, '2026-01-26', '05:53:00', '2026-01-22 21:50:28', NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jawaban`
--

CREATE TABLE `jawaban` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_soal` int(10) UNSIGNED NOT NULL,
  `id_mahasiswa` int(11) UNSIGNED NOT NULL,
  `jawaban` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`id`, `nama`, `created_at`, `modified`) VALUES
(1, 'Teknik Informatika', '2024-10-10 11:27:57', NULL),
(2, 'Sistem Informasi', '2024-10-10 11:28:26', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan_admin`
--

CREATE TABLE `kegiatan_admin` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kegiatan_admin`
--

INSERT INTO `kegiatan_admin` (`id`, `judul`, `tanggal`, `deskripsi`, `created_at`) VALUES
(1, 'makan makan', '2026-01-18', '123213246535', '2026-01-16 22:40:21'),
(2, 'technical meeting', '2026-01-27', 'sad', '2026-01-24 00:59:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
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
-- Struktur dari tabel `mahasiswa`
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
  `foto_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `id_user`, `id_jurusan`, `stambuk`, `id_kelas`, `nama_lengkap`, `alamat`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `no_telp`, `created_at`, `modified`, `foto_profil`) VALUES
(91, 57, 1, '13020230309', 2, 'Raihan Nur Rizqillah', 'Antang', 'Pria', 'Kota Makassar', '2025-12-19', '08219503918', '2025-12-17 02:16:52', NULL, '6972052dabc15.jpeg'),
(97, 65, 1, '13020230308', 2, 'Raihan Nur', 'jalan sunu', 'Pria', 'Makassar', '2026-01-29', '0811423743', '2026-01-28 08:52:35', NULL, '6979dec1247e5.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_akhir`
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
-- Dumping data untuk tabel `nilai_akhir`
--

INSERT INTO `nilai_akhir` (`id`, `id_mahasiswa`, `nilai`, `total_nilai`, `created_at`, `modified`) VALUES
(46, 91, 0, 90, '2026-01-22 20:59:45', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
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
-- Dumping data untuk tabel `notifikasi`
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
(100, 91, 'Pesan Revisi/Keterangan: judulmu bagus', 1, '2026-01-28 22:54:20', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `pengumuman` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `presentasi`
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
-- Dumping data untuk tabel `presentasi`
--

INSERT INTO `presentasi` (`id`, `id_mahasiswa`, `judul`, `ppt`, `makalah`, `is_accepted`, `is_revisi`, `keterangan`, `created_at`, `modified`) VALUES
(18, 91, 'Tipe data C++', '6972ae8752d8e.pptx', '6972ae87530f2.pdf', 0, 1, 'judulmu bagus', '2025-12-17 06:28:00', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ruangan`
--

INSERT INTO `ruangan` (`id`, `nama`, `created_at`, `modified`) VALUES
(5, 'LAB MULTIMEDI', '2025-01-03 10:14:07', '2026-01-27 14:38:36'),
(6, 'LAB MICROCONTROLLERR', '2025-01-03 10:14:07', '2026-01-13 04:07:34'),
(10, 'RUANGAN KEPALA LAB II', '2025-02-01 07:00:06', NULL),
(18, 'STARTUP', '2026-01-08 06:11:37', NULL),
(19, 'LAB IOT', '2026-01-14 06:15:30', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id` int(11) UNSIGNED NOT NULL,
  `bank_soal_id` int(11) DEFAULT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `pilihan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '\'Bukan soal pilihan ganda\'',
  `jawaban` varchar(255) DEFAULT '''soal tidak punya jawaban''',
  `status_soal` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id`, `bank_soal_id`, `deskripsi`, `pilihan`, `jawaban`, `status_soal`, `created_at`, `modified`) VALUES
(171, 26, 'Contoh Soal Pilihan Ganda: Berapa hasil dari 2 + 2?', 'A. 2, B. 3, C. 4, D. 5, E. 6', 'C', 'pilihan_ganda', '2026-01-20 23:48:27', NULL),
(172, 26, 'Contoh Soal Essay: Jelaskan pengertian MVC (Model-View-Controller)', '', 'MVC adalah pola arsitektur software yang memisahkan aplikasi menjadi tiga komponen utama: Model (data), View (tampilan), dan Controller (logika)', 'essay', '2026-01-20 23:48:27', NULL),
(173, 26, 'Contoh Soal: 1 + 1 = ?', 'A. 1, B. 2, C. 3, D. 4, E. 5', 'B', 'pilihan_ganda', '2026-01-20 23:48:52', NULL),
(178, 32, 'Contoh Soal Pilihan Ganda: Berapa hasil dari 2 + 2?', '', '3', 'essay', '2026-01-21 00:06:00', '2026-01-24 23:03:15'),
(179, 32, 'Contoh Soal Essay: Jelaskan pengertian MVC (Model-View-Controller)', '', 'MVC adalah pola arsitektur software yang memisahkan aplikasi menjadi tiga komponen utama: Model (data), View (tampilan), dan Controller (logika)', 'essay', '2026-01-21 00:06:00', NULL),
(180, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769301593_697566593c873.png)\n\ntryt', '', 'xxa', 'essay', '2026-01-25 00:40:30', NULL),
(181, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769301593_697566593c873.png)\r\n\r\ntryt', 'bukan soal pilihan', 'xxa', 'essay', '2026-01-25 00:40:30', NULL),
(182, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769303877_69756f4515942.png)\nqwewq', '', 'qwewq', 'essay', '2026-01-25 01:18:00', NULL),
(183, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769303877_69756f4515942.png)\r\nqwewq', 'bukan soal pilihan', 'qwewq', 'essay', '2026-01-25 01:18:00', NULL),
(184, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769304252_697570bc7db40.jpeg)\n\nmjkn', '', 'mkn', 'essay', '2026-01-25 01:24:40', NULL),
(185, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769304252_697570bc7db40.jpeg)\r\n\r\nmjkn', 'bukan soal pilihan', 'mkn', 'essay', '2026-01-25 01:24:40', NULL),
(186, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769305732_697576840215c.png)\n\nltarar', '', 'asdawewsad', 'essay', '2026-01-25 01:49:19', NULL),
(187, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769305732_697576840215c.png)\r\n\r\nltarar', 'bukan soal pilihan', 'asdawewsad', 'essay', '2026-01-25 01:49:19', NULL),
(188, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769319236_6975ab4416e9b.jpeg)\r\n\r\napa ini', 'bukan soal pilihan', 'iya', 'essay', '2026-01-25 05:34:04', NULL),
(189, 32, '![](/Sistem-Pendaftaran-Calon-Asisten/public/uploads/soal_content/img_1769374798_6976844e7238d.png)\r\n\r\nsda', 'A. wad, B. wda, C. awd, D. adw, E. wad', 'E', 'pilihan_ganda', '2026-01-25 21:00:13', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_tertulis`
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
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '" "',
  `stambuk` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') NOT NULL DEFAULT 'User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL,
  `id_ruang_presentasi` int(11) DEFAULT NULL,
  `id_ruang_tes_tulis` int(11) DEFAULT NULL,
  `id_ruang_wawancara` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `username`, `stambuk`, `password`, `role`, `created_at`, `modified`, `id_ruang_presentasi`, `id_ruang_tes_tulis`, `id_ruang_wawancara`) VALUES
(10, 'admin', '111111111', '12345678', 'Admin', '2025-12-17 01:44:54', '0000-00-00 00:00:00', NULL, NULL, NULL),
(32, 'admin', 'adminiclabs', '$2y$10$AfzljbcP/jV9pJk.dU1hcOHe1yfCFO1vA7YOTEbHQ7/AI4oLkyYLu', 'Admin', '2024-12-18 09:58:31', '0000-00-00 00:00:00', NULL, NULL, NULL),
(33, 'admin12', 'admin', '$2y$10$7wE5Q2S1paQm/EtBFKuuZeb4RLzUjt4sEny98T/anNlqCQVzGyD5q', 'Admin', '2025-01-03 08:52:28', '0000-00-00 00:00:00', NULL, NULL, NULL),
(55, '\"admin4 \"', 'admin4', 'admin4', 'Admin', '2025-12-16 23:52:35', '0000-00-00 00:00:00', NULL, NULL, NULL),
(57, 'umi@umi.ac.id', '13020230309', '$2y$10$8xILIVdyUCCcqBPyYl0WleMtmWycfgtR5x.SyD.BOLgc5dgJnzXBy', 'User', '2025-12-17 02:16:03', '0000-00-00 00:00:00', NULL, 4, 5),
(60, '12345@umi.ac.id', '13020230301', '$2y$10$c0ZFC3HYRUCYhl2mSATb5OqB4uNzzEHM0FDN1bShYD3dMvlHjft3O', 'User', '2026-01-28 08:31:27', NULL, NULL, NULL, NULL),
(61, '12345@umi.ac.id', '13020230100', '$2y$10$6PO19YP3Zhmr2TApXw9ioega4OOzctlAM6lkVEhWGrglYI0QyWik.', 'User', '2026-01-28 08:31:46', NULL, NULL, NULL, NULL),
(62, '12345@umi.ac.id', '13020230101', '$2y$10$PtxVwvwy68qvQCAMDO/uteTlJM9woW44bv/iTzxyCprDOgvk0RMrq', 'User', '2026-01-28 08:32:04', NULL, NULL, NULL, NULL),
(63, '12345@umi.ac.id', '13020230303', '$2y$10$3gHrqRfV9FCzBMZEYbk19.OWw8LTTSeXlOe4WDb2kG3CBO0qeaQOm', 'User', '2026-01-28 08:32:22', NULL, NULL, NULL, NULL),
(64, '12345@umi.ac.id', '13020230304', '$2y$10$0TkfgS5JJhiw9/bujtmE6u7CeoP1QkdUvs6dImg/ZxVbv7X2bYdPy', 'User', '2026-01-28 08:32:28', NULL, NULL, NULL, NULL),
(65, '12345@umi.ac.id', '13020230308', '$2y$10$XE0ZP6CThJv.VrNBZ37IR.Ahlc8Dqcag0m12YYUdZiDQZs9IZaisS', 'User', '2026-01-28 08:52:35', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `wawancara`
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
-- Dumping data untuk tabel `wawancara`
--

INSERT INTO `wawancara` (`id`, `id_mahasiswa`, `id_ruangan`, `jenis_wawancara`, `waktu`, `tanggal`, `created_at`, `modified`) VALUES
(15, 91, 6, 'wawancara kepala lab II', '00:28:00', '2026-01-27', '2026-01-26 00:28:47', NULL),
(16, 91, 5, 'Tes Tertulis', '21:40:00', '2026-01-27', '2026-01-26 11:37:37', NULL),
(19, 97, 6, 'Tes Tertulis', '19:26:00', '2026-02-06', '2026-01-28 11:21:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `berkas_mahasiswa`
--
ALTER TABLE `berkas_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_berkas_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `dashboard`
--
ALTER TABLE `dashboard`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `deadline_kegiatan`
--
ALTER TABLE `deadline_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jenis` (`jenis`);

--
-- Indeks untuk tabel `jadwal_presentasi`
--
ALTER TABLE `jadwal_presentasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_presentasi` (`id_presentasi`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indeks untuk tabel `jawaban`
--
ALTER TABLE `jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_jawaban1` (`id_mahasiswa`),
  ADD KEY `fk_mahasiswa_jawaban2` (`id_soal`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatan_admin`
--
ALTER TABLE `kegiatan_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`),
  ADD UNIQUE KEY `stambuk` (`stambuk`),
  ADD UNIQUE KEY `no_telp` (`no_telp`),
  ADD KEY `fk_mahasiswa_user1` (`id_kelas`),
  ADD KEY `fk_mahasiswa_user2` (`id_jurusan`);

--
-- Indeks untuk tabel `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_notifikasi` (`id_mahasiswa`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `presentasi`
--
ALTER TABLE `presentasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bank_soal_id` (`bank_soal_id`);

--
-- Indeks untuk tabel `test_tertulis`
--
ALTER TABLE `test_tertulis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`stambuk`);

--
-- Indeks untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mahasiswa_wawancara` (`id_mahasiswa`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `bank_soal`
--
ALTER TABLE `bank_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `berkas_mahasiswa`
--
ALTER TABLE `berkas_mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT untuk tabel `dashboard`
--
ALTER TABLE `dashboard`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `deadline_kegiatan`
--
ALTER TABLE `deadline_kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `jadwal_presentasi`
--
ALTER TABLE `jadwal_presentasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `jawaban`
--
ALTER TABLE `jawaban`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=494;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kegiatan_admin`
--
ALTER TABLE `kegiatan_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT untuk tabel `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `presentasi`
--
ALTER TABLE `presentasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT untuk tabel `test_tertulis`
--
ALTER TABLE `test_tertulis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `berkas_mahasiswa`
--
ALTER TABLE `berkas_mahasiswa`
  ADD CONSTRAINT `fk_mahasiswa_berkas_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dashboard`
--
ALTER TABLE `dashboard`
  ADD CONSTRAINT `fk_mahasiswa_dashboard` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal_presentasi`
--
ALTER TABLE `jadwal_presentasi`
  ADD CONSTRAINT `jadwal_presentasi_ibfk_3` FOREIGN KEY (`id_presentasi`) REFERENCES `presentasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_presentasi_ibfk_4` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jawaban`
--
ALTER TABLE `jawaban`
  ADD CONSTRAINT `fk_mahasiswa_jawaban1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mahasiswa_jawaban2` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `fk_mahasiswa_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mahasiswa_user1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mahasiswa_user2` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  ADD CONSTRAINT `fk_mahasiswa_nilai_akhir1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_mahasiswa_notifikasi` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `presentasi`
--
ALTER TABLE `presentasi`
  ADD CONSTRAINT `presentasi_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD CONSTRAINT `fk_soal_bank_soal` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test_tertulis`
--
ALTER TABLE `test_tertulis`
  ADD CONSTRAINT `test_tertulis_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_tertulis_ibfk_2` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  ADD CONSTRAINT `fk_mahasiswa_wawancara` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
