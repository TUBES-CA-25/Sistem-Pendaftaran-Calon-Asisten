<?php
/**
 * Admin Sidebar View
 *
 * Data yang diterima dari controller:
 * @var string $role - Role user (Admin)
 * @var string $userName - Username admin
 * @var string $photo - Path foto admin
 */
$role = $role ?? 'Admin';
$userName = $userName ?? 'Admin';

// Handle photo - could be array, string, or null
$role = $role ?? 'Admin';
$userName = $userName ?? 'Admin';
?>
<div class="sidebar" id="sidebar">
    <div class="top">
        <div class="logo">
            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="ICLABS Logo" class="icon">
            <span>ICLABS</span>
        </div>
    </div>
    <ul>
        <li>
            <a href="#" data-page="dashboard">
                <i class='bx bxs-dashboard'></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>
        <li class="menu-label">MENU UTAMA</li>
        <li>
            <a href="#" data-page="lihatPeserta">
                <i class='bx bxs-user-check'></i>
                <span class="nav-item">Lihat Peserta</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="bankSoal">
                <i class='bx bx-edit'></i>
                <span class="nav-item">Bank Soal</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="ruangan">
                <i class='bx bx-home-alt'></i>
                <span class="nav-item">Ruangan</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="lihatnilai">
                <i class='bx bx-bar-chart-alt-2'></i>
                <span class="nav-item">Nilai</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="presentasi">
                <i class='bx bx-slideshow'></i>
                <span class="nav-item">Presentasi</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="wawancara">
                <i class='bx bx-user-voice'></i>
                <span class="nav-item">Kelola Wawancara</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="daftarKehadiran">
                <i class='bx bx-calendar-check'></i>
                <span class="nav-item">Rekap</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="#" data-page="logout">
            <i class='bx bx-log-out'></i>
            <span class="nav-item">Logout</span>
        </a>
    </div>
</div>