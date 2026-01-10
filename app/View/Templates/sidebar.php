<?php
use app\Controllers\Profile\ProfileController;
use App\Controllers\user\BerkasUserController;
$role = ProfileController::viewUser()["role"];
$userName = ProfileController::viewUser()["username"];
$photo = "/tubes_web/res/imageUser/" . (BerkasUserController::viewPhoto()["foto"] ?? "default.png");
?>






<div class="sidebar" id="sidebar">
    <div class="top">
        <div class="logo">
            <img src="/tubes_web/public/Assets/Img/iclabs.png" alt="IC-Assist Logo" class="icon">
            <span>IC-ASSIST</span>
        </div>
        <i class="bx bx-menu" id="btn"></i>
    </div>



    <div class="user">
        <a href="#" data-page="profile"><img src=<?= $photo ?> alt="foto" name="userphoto" id="userphoto"
                class="user-img"></a>
        <div>
            <p class="bold" id="username"><?= $userName ?></p>
        </div>
    </div>




    <ul>
        <li>
            <a href="#" data-page="dashboard">
                <i class="bx bx-home"></i>
                <span class="nav-item">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>




        <li>
            <a href="#" data-page="biodata">
                <i class="bx bxs-id-card"></i>
                <span class="nav-item">Lengkapi Biodata</span>
            </a>
            <span class="tooltip">Lengkapi Biodata</span>
        </li>




        <li>
            <a href="#" data-page="uploadBerkas">
                <i class="bx bx-file"></i>
                <span class="nav-item">Upload Berkas</span>
            </a>
            <span class="tooltip">Upload Berkas</span>
        </li>




        <li>
            <a href="#" data-page="tesTulis">
                <i class="bx bx-task"></i>
                <span class="nav-item">Tes Tulis</span>
            </a>
            <span class="tooltip">Tes Tulis</span>
        </li>



        <li>
            <a href="#" data-page="presentasi">
                <i class="bx bx-chalkboard"></i>
                <span class="nav-item">Presentasi</span>
            </a>
            <span class="tooltip">Presentasi</span>
        </li>




        <li>
            <a href="#" data-page="wawancara">
                <i class="bx bx-user-voice"></i>
                <span class="nav-item">Jadwal</span>
            </a>
            <span class="tooltip">Jadwal</span>
        </li>



        <li>
            <a href="#" data-page="pengumuman">
                <i class="bx bx-notepad"></i>
                <span class="nav-item">Pengumuman</span>
            </a>
            <span class="tooltip">Pengumuman</span>
        </li>




    </ul>
</div>
