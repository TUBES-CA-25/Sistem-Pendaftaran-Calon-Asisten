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
$defaultPhoto = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjUiIGN5PSIyNSIgcj0iMjUiIGZpbGw9InVybCgjZ3JhZGllbnQwKSIvPgo8ZGVmcz4KPGxpbmVhckdyYWRpZW50IGlkPSJncmFkaWVudDAiIHgxPSIwIiB5MT0iMCIgeDI9IjUwIiB5Mj0iNTAiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzNkYzJlYyIvPgo8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMyNTYzZWIiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8cGF0aCBkPSJNMjUgMjNDMjcuNzYxNCAyMyAzMCAyMC43NjE0IDMwIDE4QzMwIDE1LjIzODYgMjcuNzYxNCAxMyAyNSAxM0MyMi4yMzg2IDEzIDIwIDE1LjIzODYgMjAgMThDMjAgMjAuNzYxNCAyMi4yMzg2IDIzIDI1IDIzWiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTM3IDM3QzM3IDMxLjQ3NzIgMzEuNjI3NCAyNyAyNSAyN0MxOC4zNzI2IDI3IDEzIDMxLjQ3NzIgMTMgMzdIMTVDMTUgMzIuNTgyIDE5LjQ3NzIgMjkgMjUgMjlDMzAuNTIyOCAyOSAzNSAzMi41ODIgMzUgMzdIMzdaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4=';
if (isset($photo) && is_array($photo) && !empty($photo)) {
    $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . $photo[0];
} elseif (isset($photo) && is_string($photo) && !empty($photo)) {
    // If photo is already a full path, use it
    if (strpos($photo, '/Sistem-Pendaftaran-Calon-Asisten') === false) {
        $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . $photo;
    }
} else {
    $photo = $defaultPhoto;
}
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
            <a href="#" data-page="dashboard" class="active">
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
                <span class="nav-item">Ujian</span>
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
            <a href="#" data-page="daftarKehadiran">
                <i class='bx bx-calendar-check'></i>
                <span class="nav-item">Absensi</span>
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