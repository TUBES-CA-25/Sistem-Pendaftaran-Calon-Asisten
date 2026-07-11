    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ICLABS</title>
    <link rel="icon" href="<?= APP_URL ?>/Assets/Img/iclabs.png">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Tailwind CSS Play CDN & Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= APP_URL ?>/Assets/js/tailwind-config.js"></script>

    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet' crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" crossorigin="anonymous" />

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery (Must be loaded before body for inline scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
                @apply border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 bg-white
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
                @apply text-left text-xs font-semibold text-slate-500 uppercase tracking-wider
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
                @apply px-4 py-3.5 text-sm text-slate-600 align-middle border-b border-slate-100;
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
</head>

<body>
    <script>
        // Suppress tracking prevention warnings in console
        (function() {
            const originalError = console.error;
            console.error = function(...args) {
                const message = args[0]?.toString() || '';
                if (message.includes('Tracking Prevention') ||
                    message.includes('storage') ||
                    message.includes('blocked access')) {
                    return; // Suppress tracking warnings
                }
                originalError.apply(console, args);
            };
        })();
    </script>

    <?php require_once __DIR__ . "/../templates/sidebarAdmin.php" ?>

    <!-- Bootstrap Toast Container (Redesigned with Tailwind) -->
    <div class="fixed top-4 right-4 z-[1100] flex flex-col gap-3 pointer-events-none" id="toast-container">
        <div id="liveToast" class="toast align-items-center text-white border-0 rounded-2xl shadow-2xl transition-all duration-300 hidden pointer-events-auto" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-3">
                <div class="toast-body d-flex align-items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i id="toastIcon" class="bi bi-check-circle-fill text-lg"></i>
                    </div>
                    <span id="toastMessage" class="font-semibold text-sm">Operasi berhasil!</span>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Generic Action Confirmation Modal (Premium Design with Tailwind) -->
    <div class="modal fade" id="actionConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white/95 backdrop-blur-md rounded-[24px] shadow-2xl overflow-hidden">
                <!-- Header with Gradient & Decoration -->
                <div id="actionConfirmHeader" class="modal-header border-0 text-white relative p-6 flex flex-col items-center text-center bg-gradient-to-r from-blue-600 to-indigo-600 border-b border-slate-100/10">
                    <!-- Decorative Circles -->
                    <div class="absolute -top-5 -right-5 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute bottom-2.5 left-2.5 w-12 h-12 bg-white/5 rounded-full pointer-events-none"></div>
                    
                    <div class="w-full text-center relative z-10 flex flex-col items-center">
                        <div class="mb-3">
                            <div class="inline-flex items-center justify-center rounded-full w-20 h-20 bg-white/15 border border-white/30 shadow-inner">
                                <i id="actionConfirmIcon" class="bi bi-question-lg text-4xl"></i>
                            </div>
                        </div>
                        <h5 id="actionConfirmTitle" class="modal-title font-bold text-white text-lg mt-1">Konfirmasi</h5>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="modal-body text-center p-6">
                    <p id="actionConfirmMessage" class="text-slate-500 text-sm leading-relaxed mb-0">
                        Apakah Anda yakin ingin melanjutkan tindakan ini?
                    </p>
                </div>
                
                <!-- Footer -->
                <div class="modal-footer border-0 flex justify-center gap-3 p-6 pt-0">
                    <button type="button" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition min-w-[120px] border-0 cursor-pointer" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Batal
                    </button>
                    <button type="button" id="actionConfirmButton" class="px-5 py-2.5 text-white font-semibold text-sm rounded-xl transition min-w-[120px] shadow-sm flex items-center justify-center gap-2 border-0 cursor-pointer">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Delete Confirmation Modal (Redesigned with Tailwind) -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white/95 backdrop-blur-md rounded-[24px] shadow-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-red-500 to-rose-600 text-white border-b border-red-100/10 px-6 py-4 flex justify-between items-center">
                    <h5 class="modal-title font-bold text-base flex items-center gap-2" id="deleteConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-8 flex flex-col items-center">
                    <div class="inline-flex items-center justify-center rounded-full mb-4 w-20 h-20 bg-rose-50 text-rose-500">
                        <i class="bi bi-trash3 text-3xl"></i>
                    </div>
                    <h5 class="text-lg font-bold text-slate-800 mb-1">Apakah Anda yakin?</h5>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mx-auto mb-0" id="deleteConfirmMessage">Data yang dihapus tidak dapat dikembalikan.</p>
                    <input type="hidden" id="deleteTargetId" value="">
                    <input type="hidden" id="deleteTargetType" value="">
                </div>
                <div class="modal-footer border-0 flex justify-center gap-3 p-6 pt-0">
                    <button type="button" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition min-w-[120px] border-0 cursor-pointer" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="button" class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-650 hover:from-red-650 hover:to-rose-750 text-white font-semibold text-sm rounded-xl transition min-w-[120px] shadow-sm flex items-center justify-center gap-2 border-0 cursor-pointer" id="confirmDeleteButton">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:ml-[240px] lg:w-[calc(100%-240px)] w-full min-h-screen p-3 md:p-4 lg:p-6 bg-[#f8fafc] transition-all duration-300" id="content">
        <?php
        // Load initial page content based on URL or default to dashboard
        $initialPage = $initialPage ?? 'dashboard';
        $pageViewMap = [
            'dashboard' => 'admin/dashboard/index.php',
            'lihatPeserta' => 'admin/peserta/index.php',
            'daftarKehadiran' => 'admin/kehadiran/index.php',
            'lihatnilai' => 'admin/nilai/index.php',
            'tesTulis' => 'admin/ujian/index.php',
            'bankSoal' => 'admin/ujian/index.php',
            'importSoal' => 'admin/impor/index.php',
            'jadwaltes' => 'admin/jadwaltes/index.php',
            'pengajuanJudul' => 'admin/judul/index.php',
            'jadwalPresentasi' => 'admin/presentasi/index.php',
            'wawancara' => 'admin/wawancara/index.php',
            'ruangan' => 'admin/ruangan/index.php',
        ];
        $viewFile = $pageViewMap[$initialPage] ?? 'admin/dashboard/index.php';
        require_once __DIR__ . "/../" . $viewFile;
        ?>
    </div>


    <!-- Bootstrap Custom Modal (Redesigned with Tailwind) -->
    <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white/95 backdrop-blur-md rounded-[24px] shadow-2xl overflow-hidden">
                <div class="modal-body text-center p-6 md:p-8 flex flex-col items-center">
                    <img id="modalGif" src="" alt="Animation" class="mb-4" style="width: 100px; display: none;">
                    <p id="modalMessage" class="text-slate-700 font-semibold text-base md:text-lg mb-6 leading-relaxed">Pesan akan ditampilkan di sini.</p>
                    <button type="button" id="closeModal" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-blue-500/10 cursor-pointer border-0" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Confirm Modal (Redesigned with Tailwind) -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white/95 backdrop-blur-md rounded-[24px] shadow-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white border-b border-blue-100/10">
                    <h5 class="modal-title font-bold text-white text-base" id="confirmModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-6 text-slate-700 text-sm md:text-base leading-relaxed">
                    <p id="confirmModalMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer border-0 flex justify-center gap-3 p-6 pt-0">
                    <button type="button" id="confirmModalCancel" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition min-w-[100px] border-0 cursor-pointer" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" id="confirmModalConfirm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition min-w-[100px] shadow-sm border-0 cursor-pointer">Confirm</button>
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
    <script src="<?= APP_URL ?>/Assets/js/app.js?v=<?= time() ?>"></script>
    <script src="<?= APP_URL ?>/Assets/js/ScriptSidebar.js?v=<?= time() ?>"></script>
</body>
</html>






