<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Variables & Bootstrap Overrides (includes Poppins font) -->
    <!-- Custom Variables & Bootstrap Overrides (includes Poppins font) -->
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/theme.css">
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/style.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <script>
        const APP_URL = '<?php echo APP_URL; ?>';
        window.INITIAL_PAGE = '<?= $initialPage ?? 'dashboard' ?>';
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>IC-ASSIST</title>
</head>

<body>
    <?php require_once __DIR__ . "/../templates/sidebar.php"?>

    <div class="main-content" id="content">
        <?php
        // Load initial page content based on URL or default to dashboard
        $initialPage = $initialPage ?? 'dashboard';
        $pageViewMap = [
            'dashboard' => 'user/dashboard/index.php',
            'biodata' => 'user/biodata/index.php',
            'uploadBerkas' => 'user/documents/index.php',
            'tesTulis' => 'user/exam/index.php',
            'presentasi' => 'user/presentation/index.php',
            'wawancara' => 'user/interview/index.php',
            'profile' => 'user/profile/index.php',
            'editprofile' => 'user/profile/edit.php',
            'pengumuman' => 'user/announcement/index.php',
            'notification' => 'user/notifications/index.php',
        ];
        $viewFile = $pageViewMap[$initialPage] ?? 'user/dashboard/index.php';
        require_once __DIR__ . "/../" . $viewFile;
        ?>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-body text-center p-4 p-lg-5">
                    <!-- GIF Animasi -->
                    <img id="modalGif" src="" alt="Animation" class="mb-3" style="width: 100px; display: none;">

                    <!-- Pesan Custom -->
                    <p id="modalMessage" class="fs-5 fw-medium mb-4">Pesan akan ditampilkan di sini.</p>

                    <!-- Tombol Close -->
                    <button type="button" id="closeModal" class="btn btn-primary px-4 py-2 rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="liveToast" class="toast align-items-center text-white border-0 rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i id="toastIcon" class="bi bi-check-circle-fill fs-5"></i>
                    <span id="toastMessage">Operasi berhasil!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
   
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/shared/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/sidebar/ScriptSidebar.js"></script>
    
</body>
</html>
