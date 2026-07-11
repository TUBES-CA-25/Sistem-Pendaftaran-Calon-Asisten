<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= APP_URL ?>/Assets/Img/iclabs.png">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS CDN & Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= APP_URL ?>/Assets/js/tailwind-config.js"></script>

    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Variables & Bootstrap Overrides (includes Poppins font) -->
    <!-- Custom Variables & Bootstrap Overrides (includes Poppins font) -->
    <link rel="stylesheet" href="<?= APP_URL ?>/Assets/css/theme.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/Assets/css/style.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const APP_URL = <?= json_encode(APP_URL) ?>;
        window.INITIAL_PAGE = <?= json_encode($initialPage ?? 'dashboard') ?>;
    </script>

    <title>IC-ASSIST</title>
            <style type="text/tailwindcss">
        @layer components {
            /* DataTables Premium Tailwind Styling */
            .dataTables_wrapper {
                @apply w-full;
            }
            .dataTables_filter label {
                @apply flex items-center gap-2 text-sm font-medium text-slate-600 m-0;
            }
            .dataTables_filter input {
                @apply border border-slate-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all w-full sm:w-64 shadow-sm bg-slate-50 focus:bg-white font-normal;
            }
            .dataTables_length label {
                @apply flex items-center gap-2 text-sm font-medium text-slate-600 m-0;
            }
            .dataTables_length select {
                @apply border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-sm bg-slate-50 cursor-pointer font-normal;
            }
            .dataTables_info {
                @apply text-sm text-slate-500 font-medium;
            }
            .dataTables_paginate {
                @apply flex items-center gap-1;
            }
            .dataTables_paginate .paginate_button {
                @apply px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 cursor-pointer transition-all bg-white shadow-sm ml-1;
            }
            .dataTables_paginate .paginate_button.current {
                @apply bg-blue-600 text-white border-transparent hover:text-white hover:bg-blue-700 shadow-md shadow-blue-500/30 font-bold !important;
            }
            .dataTables_paginate .paginate_button.disabled {
                @apply opacity-50 cursor-not-allowed hover:bg-white hover:text-slate-600 hover:border-slate-200 shadow-none;
            }
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . "/../templates/sidebar.php"?>

    <div class="min-h-screen bg-[#f8fafc] w-full md:w-[calc(100%-240px)] md:ml-[240px] p-2 sm:p-3 md:p-4 transition-all duration-300 ease-in-out" id="content">
        <?php
        // Load initial page content based on URL or default to dashboard
        $initialPage = $initialPage ?? 'dashboard';
        $pageViewMap = [
            'dashboard' => 'user/dashboard/index.php',
            'biodata' => 'user/biodata/index.php',
            'uploadBerkas' => 'user/berkas/index.php',
            'tesTulis' => 'user/ujian/index.php',
            'presentasi' => 'user/presentasi/index.php',
            'wawancara' => 'user/wawancara/index.php',
            'profile' => 'user/biodata/index.php',
            'editprofile' => 'user/biodata/index.php',
            'pengumuman' => 'user/dashboard/pengumuman.php',
            'notification' => 'user/notifikasi/index.php',
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
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <img id="modalGif" src="" alt="Animation" style="width: 100px; display: none;">
                    </div>

                    <!-- Pesan Custom -->
                    <p id="modalMessage" class="fs-5 fw-medium mb-4">Pesan akan ditampilkan di sini.</p>

                    <!-- Tombol Close -->
                    <button type="button" id="closeModal" class="btn btn-primary px-4 py-2 rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Action Confirmation Modal -->
    <div class="modal fade" id="actionConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <!-- Header with Icon & Title -->
                <div id="actionConfirmHeader" class="text-center p-4 bg-primary text-white">
                    <div class="mb-3">
                        <i id="actionConfirmIcon" class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h4 id="actionConfirmTitle" class="fw-bold mb-0">Konfirmasi</h4>
                </div>
                
                <!-- Body -->
                <div class="modal-body text-center p-4 p-lg-5">
                    <p class="text-muted fs-5 mb-4" id="actionConfirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light btn-lg rounded-pill px-5" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-5" id="actionConfirmButton">Ya</button>
                    </div>
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
   
    <script src="<?= APP_URL ?>/Assets/js/app.js?v=<?= time() ?>"></script>
    <script src="<?= APP_URL ?>/Assets/js/ScriptSidebar.js?v=<?= time() ?>"></script>
    
</body>
</html>




