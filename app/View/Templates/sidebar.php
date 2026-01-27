<?php
/**
 * Sidebar View
 *
 * Data yang diterima dari controller:
 * @var string $role - Role user (User/Admin)
 * @var string $userName - Username user
 * @var string $photo - Path foto user
 */
$role = $role ?? 'User';
$userName = $userName ?? 'Guest';

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
            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="IC-Assist Logo" class="icon">
            <span>IC-ASSIST</span>
        </div>

    </div>

    <ul>
        <li>
            <a href="#" data-page="dashboard">
                <i class="bx bx-home"></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>
        <li class="menu-label">MENU UTAMA</li>
        <li>
            <a href="#" data-page="biodata">
                <i class="bx bxs-id-card"></i>
                <span class="nav-item">Lengkapi Biodata</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="uploadBerkas">
                <i class="bx bx-file"></i>
                <span class="nav-item">Upload Berkas</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="tesTulis">
                <i class="bx bx-task"></i>
                <span class="nav-item">Tes Tulis</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="presentasi">
                <i class="bx bx-chalkboard"></i>
                <span class="nav-item">Presentasi</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="wawancara">
                <i class="bx bx-user-voice"></i>
                <span class="nav-item">Jadwal</span>
            </a>
        </li>
        <li>
            <a href="#" data-page="pengumuman">
                <i class="bx bx-notepad"></i>
                <span class="nav-item">Pengumuman</span>
            </a>
            <span class="tooltip">Pengumuman</span>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="#" data-page="logout">
            <i class="bx bx-log-out"></i>
            <span class="nav-item">Logout</span>
        </a>
    </div>
</div>