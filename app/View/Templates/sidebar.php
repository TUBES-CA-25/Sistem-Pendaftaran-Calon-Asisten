<?php
use app\Controllers\Profile\ProfileController;
use App\Controllers\user\BerkasUserController;

$role = ProfileController::viewUser()["role"];
$userName = ProfileController::viewUser()["username"];
$stambuk = ProfileController::viewUser()["stambuk"];
$photo = "/tubes_web/res/imageUser/" . (BerkasUserController::viewPhoto()["foto"] ?? "default.png");
?>

<div class="sidebar" id="sidebar">
    <!-- Header Section with Logo -->
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="/tubes_web/public/Assets/Img/iclabs.png" alt="IC-ASSIST Logo" class="logo-icon">
            <span class="logo-text">ICLABS</span>
        </div>
    </div>

    <!-- Toggle Button -->
    <button class="sidebar-toggle" id="btn" aria-label="Toggle Sidebar">
        <i class="bx bx-menu"></i>
    </button>

    <!-- User Profile Section -->
    <div class="user-profile">
        <a href="#" data-page="profile" class="user-photo-link">
            <img src="<?= $photo ?>" alt="<?= $userName ?>" class="user-img">
        </a>
        <div class="user-info">
            <p class="user-name"><?= $userName ?></p>
            <small class="user-stambuk"><?= $stambuk ?></small>
        </div>
    </div>

    <!-- Divider -->
    <div class="sidebar-divider"></div>

    <!-- Menu Label -->
    <div class="menu-label">
        <span>MENU UTAMA</span>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <ul>
            <!-- Dashboard -->
            <li>
                <a href="#" data-page="dashboard" class="nav-link active">
                    <i class="bx bx-home-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Dokumen (dengan submenu) -->
            <li class="menu-item-has-children">
                <a href="#" class="nav-link nav-parent">
                    <i class="bx bx-file"></i>
                    <span class="nav-text">Dokumen</span>
                    <i class="bx bx-chevron-down nav-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#" data-page="biodata" class="nav-link submenu-link">
                            <i class="bx bx-user"></i>
                            <span class="nav-text">Lengkapi Biodata</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-page="uploadBerkas" class="nav-link submenu-link">
                            <i class="bx bx-upload"></i>
                            <span class="nav-text">Upload Berkas</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Ujian (dengan submenu) -->
            <li class="menu-item-has-children">
                <a href="#" class="nav-link nav-parent">
                    <i class="bx bx-edit-alt"></i>
                    <span class="nav-text">Ujian</span>
                    <i class="bx bx-chevron-down nav-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#" data-page="tesTulis" class="nav-link submenu-link">
                            <i class="bx bx-pencil"></i>
                            <span class="nav-text">Tes Tulis</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-page="presentasi" class="nav-link submenu-link">
                            <i class="bx bx-chalkboard"></i>
                            <span class="nav-text">Presentasi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Jadwal (dengan submenu) -->
            <li class="menu-item-has-children">
                <a href="#" class="nav-link nav-parent">
                    <i class="bx bx-calendar"></i>
                    <span class="nav-text">Jadwal</span>
                    <i class="bx bx-chevron-down nav-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#" data-page="wawancara" class="nav-link submenu-link">
                            <i class="bx bx-user-voice"></i>
                            <span class="nav-text">Jadwal Wawancara</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-page="jadwalPresentasi" class="nav-link submenu-link">
                            <i class="bx bx-slideshow"></i>
                            <span class="nav-text">Jadwal Presentasi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Informasi (dengan submenu) -->
            <li class="menu-item-has-children">
                <a href="#" class="nav-link nav-parent">
                    <i class="bx bx-info-circle"></i>
                    <span class="nav-text">Informasi</span>
                    <i class="bx bx-chevron-down nav-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#" data-page="notification" class="nav-link submenu-link">
                            <i class="bx bx-bell"></i>
                            <span class="nav-text">Notifikasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-page="pengumuman" class="nav-link submenu-link">
                            <i class="bx bx-notepad"></i>
                            <span class="nav-text">Pengumuman</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Logout Button (Bottom) -->
    <div class="sidebar-footer">
        <a href="#" data-page="logout" class="logout-link">
            <i class="bx bx-log-out"></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>