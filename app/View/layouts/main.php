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

    <title>ICLABS</title>
                <style type="text/tailwindcss">
        @layer components {

            /* ============================================================
               TABLE WRAPPER & CARD
               ============================================================ */
            .dt-table-card {
                @apply bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden;
            }

            /* ============================================================
               TOP BAR: Length + Search
               ============================================================ */
            div.dt-top-wrapper {
                @apply flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 bg-white;
            }
            .dataTables_length label {
                @apply flex items-center gap-2 text-sm text-slate-500 font-medium m-0 cursor-pointer;
            }
            .dataTables_length select {
                @apply border border-slate-200 rounded-lg pl-3 pr-8 py-1.5 text-sm text-slate-700 bg-white
                       hover:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:border-blue-500
                       outline-none transition-all cursor-pointer shadow-sm;
            }
            .dataTables_filter {
                @apply m-0;
            }
            .dataTables_filter label {
                @apply flex items-center text-sm text-slate-500 font-medium m-0;
            }
            .dataTables_filter input {
                @apply border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-700 bg-slate-50 w-64
                       hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:bg-white
                       outline-none transition-all shadow-sm;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: left 0.75rem center;
                background-size: 1rem 1rem;
                padding-left: 2.25rem !important;
            }

            /* ============================================================
               TABLE HEAD
               ============================================================ */
            .dt-head-cell {
                @apply text-left text-xs font-semibold text-slate-500 tracking-wider
                       bg-slate-50 px-4 py-3.5 border-b border-slate-200 whitespace-nowrap;
            }
            table.dataTable thead th,
            table.dataTable thead td {
                @apply bg-slate-50 border-b border-slate-200 !important;
            }

            /* ============================================================
               TABLE BODY ROW
               ============================================================ */
            .dt-body-row {
                @apply border-b border-slate-100 transition-colors duration-150 cursor-default;
            }
            .dt-body-row:hover {
                @apply bg-blue-50/40 !important;
            }
            .dt-body-row:last-child {
                @apply border-b-0;
            }
            /* Odd row subtle stripe */
            .dt-body-row:nth-child(odd) {
                @apply bg-slate-50/40;
            }
            .dt-body-row:nth-child(even) {
                @apply bg-white;
            }

            /* ============================================================
               TABLE BODY CELL
               ============================================================ */
            .dt-body-cell {
                @apply px-4 py-3.5 text-sm text-slate-600 align-middle;
            }
            table.dataTable tbody td {
                @apply px-4 py-3.5 text-sm font-medium text-slate-600 align-middle border-b border-slate-100;
            }

            /* ============================================================
               BOTTOM BAR: Info + Pagination
               ============================================================ */
            div.dt-bottom-wrapper {
                @apply flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-slate-100 bg-white;
            }
            .dataTables_info {
                @apply text-sm text-slate-500 font-medium;
            }
            .dataTables_paginate {
                @apply flex items-center gap-1;
            }
            .dataTables_paginate .paginate_button {
                @apply inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-semibold
                       text-slate-600 border border-slate-200 bg-white shadow-sm
                       hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300
                       cursor-pointer transition-all duration-150 select-none;
            }
            .dataTables_paginate .paginate_button.current {
                @apply bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/25
                       hover:bg-blue-700 hover:text-white hover:border-blue-700 !important;
            }
            .dataTables_paginate .paginate_button.disabled {
                @apply opacity-40 cursor-not-allowed hover:bg-white hover:text-slate-600
                       hover:border-slate-200 shadow-none pointer-events-none;
            }
            .dataTables_paginate .paginate_button.previous,
            .dataTables_paginate .paginate_button.next,
            .dataTables_paginate .paginate_button.first,
            .dataTables_paginate .paginate_button.last {
                @apply text-base font-bold;
            }
            .dataTables_paginate .ellipsis {
                @apply px-2 text-slate-400 select-none;
            }

            /* ============================================================
               REMOVE DataTables default ugly border
               ============================================================ */
            table.dataTable.no-footer {
                @apply border-b-0;
            }
            div.dataTables_scrollBody {
                @apply overflow-x-auto;
            }

        }
    </style>

    <style>
        /* ============================================================
           MODAL BACKDROP BLUR
           ============================================================ */
        .modal {
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
        }
        .modal-backdrop {
            background-color: rgba(15, 23, 42, 0.4) !important;
        }
        .modal-backdrop.show {
            opacity: 1 !important;
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





