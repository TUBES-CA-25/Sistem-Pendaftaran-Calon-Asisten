<?php
use app\Controllers\Profile\ProfileController;
use App\Controllers\user\BerkasUserController;
$role = ProfileController::viewUser()["role"];
$userName = ProfileController::viewUser()["username"];
$photo = RES_PATH . "/imageUser/" . (BerkasUserController::viewPhoto()["foto"] ?? "default.png");
?>
<aside class="sidebar d-flex flex-column flex-shrink-0 bg-white" id="sidebar">
    <div class="top p-3 d-flex align-items-center justify-content-between">
        <div class="logo d-flex align-items-center gap-2 overflow-hidden">
            <img src="<?= BASE_URL ?>/Assets/Img/iclabs.png" alt="IC-Assist Logo" class="icon" style="width: 32px; height: 32px;">
            <span class="fs-5 fw-bold text-primary logo-text">IC-ASSIST</span>
        </div>
        <i class="bx bx-chevron-left fs-4 cursor-pointer" id="btn"></i>
    </div>

    <div class="user p-3 d-flex align-items-center gap-3 border-bottom mb-3 overflow-hidden">
        <a href="#" data-page="profile" class="flex-shrink-0">
            <img src="<?= $photo ?>" alt="foto" name="userphoto" id="userphoto" class="rounded-circle border border-2 border-primary" style="width: 40px; height: 40px; object-fit: cover;">
        </a>
        <div class="user-info">
            <p class="mb-0 fw-bold text-dark text-truncate" id="username"><?= $userName ?></p>
            <small class="text-muted text-xs"><?= $role ?></small>
        </div>
    </div>

    <ul class="nav nav-pills flex-column mb-auto px-2">
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/dashboard" data-page="dashboard" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class="bx bx-home fs-4"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/biodata" data-page="biodata" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class="bx bxs-id-card fs-4"></i>
                <span class="nav-text">Lengkapi Biodata</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/uploadBerkas" data-page="uploadBerkas" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class="bx bx-file fs-4"></i>
                <span class="nav-text">Upload Berkas</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/tesTulis" data-page="tesTulis" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class="bx bx-task fs-4"></i>
                <span class="nav-text">Tes Tulis</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/presentasi" data-page="presentasi" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class="bx bx-chalkboard fs-4"></i>
                <span class="nav-text">Presentasi</span>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>/wawancara" data-page="wawancara" class="nav-link d-flex align-items-center gap-3 text-dark">
                <i class="bx bx-user-voice fs-4"></i>
                <span class="nav-text">Jadwal</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer p-3 mt-auto border-top">
        <a href="<?= BASE_URL ?>/logout" data-page="logout" class="nav-link d-flex align-items-center gap-3 text-danger hover-danger">
            <i class='bx bx-log-out fs-4'></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</aside>