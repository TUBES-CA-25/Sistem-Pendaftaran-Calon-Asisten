    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ICLABS</title>
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Variables & Bootstrap Overrides (includes Poppins font) -->
    <link rel="stylesheet" href="Assets/Style/custom-variables.css?v=<?= time() ?>">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery (Must be loaded before body for inline scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php require_once __DIR__ . "/../templates/sidebarAdmin.php" ?>

    <!-- Bootstrap Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container" style="z-index: 1100;">
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

    <!-- Bootstrap Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header modal-header-danger border-0 py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px; background: #fee2e2;">
                        <i class="bi bi-trash3 text-danger fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Apakah Anda yakin?</h5>
                    <p class="text-muted mb-0" id="deleteModalMessage">Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-danger px-4 rounded-3" id="btnConfirmDelete">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" id="content">
        <?php
        // Load initial page content based on URL or default to dashboard
        $initialPage = $initialPage ?? 'dashboard';
        $pageViewMap = [
            'dashboard' => 'admin/dashboard/index.php',
            'lihatPeserta' => 'admin/participants/index.php',
            'daftarKehadiran' => 'admin/attendance/index.php',
            'lihatnilai' => 'admin/grades/index.php',
            'tesTulis' => 'admin/exam/index.php',
            'bankSoal' => 'admin/exam/index.php',
            'importSoal' => 'admin/exam/importPage.php',
            'jadwaltes' => 'admin/exam/schedule.php',
            'pengajuanJudul' => 'admin/presentation/titles.php',
            'jadwalPresentasi' => 'admin/presentation/schedule.php',
            'wawancara' => 'admin/interview/index.php',
            'ruangan' => 'admin/rooms/index.php',
        ];
        $viewFile = $pageViewMap[$initialPage] ?? 'admin/dashboard/index.php';
        require_once __DIR__ . "/../" . $viewFile;
        ?>
    </div>


    <!-- Bootstrap Custom Modal -->
    <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-body text-center p-4 p-lg-5">
                    <img id="modalGif" src="" alt="Animation" class="mb-3" style="width: 100px; display: none;">
                    <p id="modalMessage" class="fs-5 fw-medium mb-4">Pesan akan ditampilkan di sini.</p>
                    <button type="button" id="closeModal" class="btn btn-primary px-4 py-2 rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header modal-header-gradient border-0 py-3">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p id="confirmModalMessage" class="fs-5 mb-0"></p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" id="confirmModalCancel" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" id="confirmModalConfirm" class="btn btn-primary px-4 rounded-3">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const APP_URL = '<?php echo APP_URL; ?>';
        window.INITIAL_PAGE = '<?= $initialPage ?? 'dashboard' ?>';
    </script>

    <!-- Core Libs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- App Scripts -->
    <script src="Assets/Script/common.js"></script>
    <script src="Assets/Script/app.js"></script>
    <script src="Assets/Script/sidebar/ScriptSidebar.js"></script>
</body>
</html>