<div class="sidebar" id="sidebar">
    <div class="top">
        <div class="logo">
            <img src="<?= BASE_URL ?>/Assets/Img/iclabs.png" alt="ICLABS Logo" class="icon">
            <span>ICLABS</span>
        </div>
    </div>
    <ul>
        <li>
            <a href="<?= BASE_URL ?>/dashboard" data-page="dashboard" class="active">
                <i class='bx bxs-dashboard'></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>
        <li class="menu-label">MENU UTAMA</li>
        <li>
            <a href="<?= BASE_URL ?>/peserta" data-page="lihatPeserta">
                <i class='bx bxs-user-check'></i>
                <span class="nav-item">Lihat Peserta</span>
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/tesTulis" data-page="tesTulis">
                <i class='bx bx-edit'></i>
                <span class="nav-item">Ujian</span>
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/ruangan" data-page="ruangan">
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
        <a href="<?= BASE_URL ?>/logout">
            <i class='bx bx-log-out'></i>
            <span class="nav-item">Logout</span>
        </a>
    </div>
</div>