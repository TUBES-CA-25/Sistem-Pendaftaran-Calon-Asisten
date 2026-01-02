<aside class="sidebar d-flex flex-column flex-shrink-0 bg-white" id="sidebar">
    <div class="top p-3 d-flex align-items-center justify-content-between">
        <div class="logo d-flex align-items-center gap-2 overflow-hidden">
            <img src="<?= BASE_URL ?>/Assets/Img/iclabs.png" alt="ICLABS Logo" class="icon" style="width: 32px; height: 32px;">
            <span class="fs-5 fw-bold text-primary logo-text">ICLABS</span>
        </div>
        <i class="bx bx-chevron-left fs-4 cursor-pointer" id="btn"></i>
    </div>

    <ul class="nav nav-pills flex-column mb-auto px-2 mt-3">
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/dashboard" data-page="dashboard" class="nav-link d-flex align-items-center gap-3 text-dark active">
                <i class='bx bxs-dashboard fs-4'></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item mt-3 mb-2">
            <small class="text-muted text-uppercase fw-bold px-3" style="font-size: 0.7rem; letter-spacing: 0.5px;">Menu Utama</small>
        </li>
        
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/peserta" data-page="lihatPeserta" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class='bx bxs-user-check fs-4'></i>
                <span class="nav-text">Lihat Peserta</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/tesTulis" data-page="tesTulis" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class='bx bx-edit fs-4'></i>
                <span class="nav-text">Ujian</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/ruangan" data-page="ruangan" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class='bx bx-home-alt fs-4'></i>
                <span class="nav-text">Ruangan</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" data-page="lihatnilai" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class='bx bx-bar-chart-alt-2 fs-4'></i>
                <span class="nav-text">Nilai</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" data-page="presentasi" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class='bx bx-slideshow fs-4'></i>
                <span class="nav-text">Presentasi</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" data-page="daftarKehadiran" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class='bx bx-calendar-check fs-4'></i>
                <span class="nav-text">Absensi</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer p-3 mt-auto border-top">
        <a href="<?= BASE_URL ?>/logout" data-page="logout" class="nav-link d-flex align-items-center gap-3 text-danger">
            <i class='bx bx-log-out fs-4'></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</aside>