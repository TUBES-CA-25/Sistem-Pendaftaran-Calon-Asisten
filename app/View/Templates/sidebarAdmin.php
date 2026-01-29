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
            <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="ICLABS Logo" class="icon">
            <span>ICLABS</span>
        </div>
    </div>
    <ul>
        <li>
            <a href="<?= APP_URL ?>/dashboard" data-page="dashboard">
                <i class='bx bxs-dashboard'></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>
        <li class="menu-label">MENU UTAMA</li>
        <li>
            <a href="<?= APP_URL ?>/lihatPeserta" data-page="lihatPeserta">
                <i class='bx bxs-user-check'></i>
                <span class="nav-item">Lihat Peserta</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/pengajuanJudul" data-page="pengajuanJudul">
                <i class='bx bxs-file-doc'></i>
                <span class="nav-item">Pengajuan Judul</span>
            </a>
        </li>
        <li>
            <a href="#" class="dropdown-toggle">
                <i class="bi bi-journal-text"></i>
                <span class="nav-item">Bank Soal</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="<?= APP_URL ?>/bankSoal" data-page="bankSoal">
                        <i class='bx bx-list-ul'></i>
                        <span class="nav-item">Daftar Soal</span>
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/importSoal" data-page="importSoal">
                        <i class='bx bx-transfer'></i>
                        <span class="nav-item">Import/Export</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle">
                <i class="bi bi-calendar-event"></i>
                <span class="nav-item">Penjadwalan</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="<?= APP_URL ?>/jadwaltes" data-page="jadwaltes">
                        <i class='bx bx-edit'></i>
                        <span class="nav-item">Tes Tertulis</span>
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/jadwalPresentasi" data-page="jadwalPresentasi">
                        <i class='bx bx-slideshow'></i>
                        <span class="nav-item">Presentasi</span>
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/wawancara" data-page="wawancara">
                        <i class='bx bx-user-voice'></i>
                        <span class="nav-item">Wawancara</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="<?= APP_URL ?>/ruangan" data-page="ruangan">
                <i class='bx bx-home-alt'></i>
                <span class="nav-item">Ruangan</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/lihatnilai" data-page="lihatnilai">
                <i class='bx bx-bar-chart-alt-2'></i>
                <span class="nav-item">Nilai</span>
            </a>
        </li>


        <li>
            <a href="<?= APP_URL ?>/daftarKehadiran" data-page="daftarKehadiran">
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