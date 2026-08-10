    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ICLABS</title>
    <link rel="icon" href="<?= APP_URL ?>/Assets/Img/iclabs.png">

    <!-- Tailwind CSS Play CDN & Configuration -->
    <script>
        const originalWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) return;
            originalWarn.apply(console, args);
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= APP_URL ?>/Assets/js/core/tailwind-config.js"></script>

    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet' crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" crossorigin="anonymous" />

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <!-- Konstanta global. WAJIB dideklarasikan di <head>: skrip halaman
         (Assets/js/admin/*.js) dimuat dari dalam #content, sehingga pada
         load penuh ia dieksekusi SEBELUM akhir <body>. -->
    <script>
        const APP_URL = <?= json_encode(APP_URL) ?>;
        window.INITIAL_PAGE = <?= json_encode($initialPage ?? 'dashboard') ?>;
    </script>

    <!-- Komponen UI vanilla (Modal/Toast/Dropdown) - pengganti Bootstrap JS -->
    <script src="<?= APP_URL ?>/Assets/js/core/dom.js?v=<?= time() ?>"></script>
    <script src="<?= APP_URL ?>/Assets/js/core/ui.js?v=<?= time() ?>"></script>

    <!-- Tailwind Config Custom styles -->
    <style type="text/tailwindcss">
        @layer utilities {
            .scrollbar-none::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-none {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        }
        @layer components {
            /* Dropdown custom CSS fallback mapping */
            .dropdown {
                position: relative;
            }
            .dropdown-menu {
                position: absolute;
                z-index: 1000;
                display: none;
                min-width: 10rem;
                padding: 0.5rem 0;
                margin: 0;
                font-size: 0.875rem;
                color: #334155;
                text-align: left;
                list-style: none;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #cbd5e1;
                border-radius: 0.75rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .dropdown-menu.show {
                display: block !important;
            }
            .dropdown-menu-end {
                right: 0 !important;
                left: auto !important;
            }
            .dropdown-item {
                display: block;
                width: 100%;
                padding: 0.375rem 0.75rem;
                clear: both;
                font-weight: 400;
                color: #334155;
                text-align: inherit;
                text-decoration: none;
                white-space: nowrap;
                background-color: transparent;
                border: 0;
                cursor: pointer;
                transition: background-color 0.15s ease, color 0.15s ease;
            }
            .dropdown-item:hover,
            .dropdown-item:focus {
                background-color: #f1f5f9;
                color: #1e40af;
            }
            .dropdown-divider {
                height: 0;
                margin: 0.25rem 0;
                overflow: hidden;
                border-top: 1px solid #e2e8f0;
            }

            /* Custom styling for DataTables with Tailwind classes */
            
            /* ============================================================
               TOP BAR: Length + Search
               ============================================================ */

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

            /* ============================================================
               REMOVE DataTables default ugly border
               ============================================================ */
            table.dataTable.no-footer {
                @apply border-b-0;
            }

        }
    </style>
    
    <style>
    /* Panel notifikasi.

       Posisi & lebar ditulis sebagai CSS biasa, BUKAN kelas Tailwind arbitrer
       (top-[4.5rem], sm:left-auto, dst). Play CDN mengompilasi kelas saat
       runtime dan tidak selalu menyertakan varian arbitrer bersyarat, sehingga
       panel bisa tetap memakai posisi lama lalu keluar layar di HP.

       Di layar kecil panel ditambatkan ke tepi layar (fixed), bukan ke tombol
       lonceng - di kanan lonceng masih ada blok profil sehingga right-0
       mendorong panel melewati tepi kiri. */
    [data-notif-panel] {
        position: fixed;
        left: 1rem;
        right: 1rem;
        top: 4.75rem;
        width: auto;
    }
    [data-notif-panel].hidden { display: none; }

    /* Toast yang sedang TERSEMBUNYI tidak boleh menangkap sentuhan.

       #liveToast selalu ada di DOM dan memakai pointer-events-auto,
       tetapi saat tidak aktif ia hanya opacity-0 - masih menempati
       ruang di pojok kanan atas. Di layar sempit kotak 280px itu
       menutupi tombol notifikasi, sehingga ketukan mengenai toast
       dan tombol terasa mati. */
    #liveToast[class*="opacity-0"] { pointer-events: none; }

    /* Sidebar HP: buka/tutup.

       translate-x-0 ditambahkan oleh sidebar.js saat runtime, sehingga
       Play CDN tidak pernah melihatnya sebagai teks di sumber dan tidak
       mengompilasinya. Akibatnya kelas terpasang tetapi transform tetap
       -240px - sidebar tidak pernah muncul saat burger ditekan.
       CSS biasa di bawah selalu berlaku. */
    @media (max-width: 1023.98px) {
        .sidebar.translate-x-0 { transform: translateX(0) !important; }
        .sidebar.-translate-x-full { transform: translateX(-100%) !important; }
    }

    /* Tinggi sidebar memakai dvh, bukan vh.

       Di peramban HP, 100vh dihitung TANPA memperhitungkan bilah alamat,
       sehingga lebih tinggi dari area yang benar-benar terlihat. Footer
       sidebar (tombol Logout) jadi terdorong ke bawah layar dan tidak
       bisa dijangkau - sidebar sendiri tidak menggulung karena hanya
       <ul> di dalamnya yang overflow-y-auto.

       Baris 100vh dipertahankan sebagai cadangan untuk peramban lama
       yang belum mengenal dvh. */
    .sidebar {
        /* Urutan penting: nilai terakhir yang DIDUKUNG peramban yang dipakai.
           --app-vh diisi JS dari window.innerHeight (paling akurat, jalan di
           semua peramban); dvh sebagai cadangan; vh sebagai cadangan terakhir. */
        height: 100vh;
        height: 100dvh;
        height: var(--app-vh, 100vh);
        overflow-y: auto;
    }
    @media (min-width: 640px) {
        [data-notif-panel] {
            position: absolute;
            left: auto;
            right: 0;
            top: 100%;
            width: 340px;
            max-width: 340px;
            margin-top: 0.5rem;
        }
    }
    </style>
    <script>
    /* Tinggi viewport nyata untuk sidebar.

       100vh di peramban HP tidak memperhitungkan bilah alamat, sehingga
       footer sidebar (tombol Logout) terdorong ke bawah layar. Sebagian
       peramban Android lama juga belum mengenal dvh, jadi tingginya diukur
       langsung dari window.innerHeight.

       Dipasang di <head> supaya nilainya sudah ada sebelum halaman dilukis;
       resize & orientationchange menjaganya tetap benar saat bilah alamat
       muncul/hilang atau layar diputar. */
    (function () {
        function setTinggiViewport() {
            document.documentElement.style.setProperty(
                '--app-vh', window.innerHeight + 'px'
            );
        }
        setTinggiViewport();
        window.addEventListener('resize', setTinggiViewport);
        window.addEventListener('orientationchange', setTinggiViewport);
    })();
    </script>
</head>
<body class="animate-page-fade">
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

    <?php require_once __DIR__ . "/../templates/sidebar_admin.php" ?>

    <!-- Toast global (dikelola UI.toast di Assets/js/core/ui.js) -->
    <div class="fixed top-4 right-4 z-[1100] flex flex-col gap-2 pointer-events-none" id="toast-container">
        <div id="liveToast" class="border-0 border-l-[4px] rounded-r-lg shadow-lg transition-all duration-200 pointer-events-auto min-w-[280px] max-w-[400px] opacity-0 translate-x-2 bg-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="flex items-center gap-2.5 p-3">
                <!-- Icon -->
                <i id="toastIcon" class="bi bi-check-circle-fill text-xl"></i>
                
                <!-- Content -->
                <div class="flex-1">
                    <h6 id="toastTitle" class="font-bold text-[13px] mb-0.5 tracking-wide">Success!</h6>
                    <p id="toastMessage" class="text-[11px] font-medium m-0 leading-tight">Operasi berhasil!</p>
                </div>
                
                <!-- Action -->
                <button type="button" id="toastBtn" class="shrink-0 px-3 py-1 border rounded-md text-[11px] font-bold transition-colors" data-toast-close>
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Generic Action Confirmation Modal (Premium Design with Tailwind) -->
    <div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="actionConfirmModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
        <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
            <div class="relative isolate bg-white w-full rounded-[24px] shadow-2xl overflow-hidden">
                <!-- Header with Gradient & Decoration -->
                <div id="actionConfirmHeader" class="border-0 text-white relative p-6 flex flex-col items-center text-center bg-gradient-to-r from-primary to-secondary rounded-t-[24px]">
                    <!-- Decorative Circles -->
                    <div class="absolute -top-5 -right-5 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute bottom-2.5 left-2.5 w-12 h-12 bg-white/5 rounded-full pointer-events-none"></div>
                    
                    <div class="w-full text-center relative z-10 flex flex-col items-center">
                        <div class="mb-3">
                            <div class="inline-flex items-center justify-center rounded-full w-20 h-20 bg-white/15 border border-white/30 shadow-inner">
                                <i id="actionConfirmIcon" class="bi bi-question-lg text-4xl"></i>
                            </div>
                        </div>
                        <h5 id="actionConfirmTitle" class="font-bold text-white text-lg mt-1">Konfirmasi</h5>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="text-center p-6">
                    <p id="actionConfirmMessage" class="text-slate-500 text-sm leading-relaxed mb-0">
                        Apakah Anda yakin ingin melanjutkan tindakan ini?
                    </p>
                </div>
                
                <!-- Footer -->
                <div class="border-0 flex justify-center gap-3 p-6 pt-0">
                    <button type="button" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition min-w-[120px] border-0 cursor-pointer" data-modal-close>
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
    <div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="deleteConfirmModal" aria-labelledby="deleteConfirmModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
        <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
            <div class="relative isolate bg-white w-full rounded-[24px] shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 flex justify-between items-center rounded-t-[24px]">
                    <h5 class="font-bold text-base flex items-center gap-2" id="deleteConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
                </div>
                <div class="text-center p-8 flex flex-col items-center">
                    <div class="inline-flex items-center justify-center rounded-full mb-4 w-20 h-20 bg-rose-50 text-rose-500">
                        <i class="bi bi-trash3 text-3xl"></i>
                    </div>
                    <h5 class="text-lg font-bold text-slate-800 mb-1">Apakah Anda yakin?</h5>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mx-auto mb-0" id="deleteConfirmMessage">Data yang dihapus tidak dapat dikembalikan.</p>
                    <input type="hidden" id="deleteTargetId" value="">
                    <input type="hidden" id="deleteTargetType" value="">
                </div>
                <div class="border-0 flex justify-center gap-3 p-6 pt-0">
                    <button type="button" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition min-w-[120px] border-0 cursor-pointer" data-modal-close>
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="button" class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold text-sm rounded-xl transition min-w-[120px] shadow-sm flex items-center justify-center gap-2 border-0 cursor-pointer" id="confirmDeleteButton">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:ml-[240px] lg:w-[calc(100%-240px)] w-full min-h-screen p-3 pt-1 md:p-4 md:pt-1.5 lg:p-6 lg:pt-2 bg-[#f8fafc] transition-all duration-300" id="content">
        <?php
        // Load initial page content based on URL or default to dashboard
        $initialPage = $initialPage ?? 'dashboard';
        $pageViewMap = [
            'dashboard' => 'admin/dashboard/index.php',
            'lihatPeserta' => 'admin/peserta/index.php',
            'daftarKehadiran' => 'admin/rekap/index.php',
            'lihatnilai' => 'admin/nilai/index.php',
            'tesTulis' => 'admin/bank-soal/index.php',
            'bankSoal' => 'admin/bank-soal/index.php',
            // Import/Export kini menjadi tab di dalam halaman Bank Soal.
            // Rute lama tetap dipetakan ke view yang sama supaya tautan
            // yang sudah tersebar tidak mati.
            'importSoal' => 'admin/bank-soal/index.php',
            // Halaman induk Penjadwalan (tab: tes tertulis -> presentasi ->
            // wawancara). Rute lama ketiganya tetap dipetakan ke halaman induk
            // dan mendarat di tabnya masing-masing supaya tautan lama hidup.
            'penjadwalan' => 'admin/penjadwalan/index.php',
            'jadwaltes' => 'admin/penjadwalan/index.php',
            'pengajuanJudul' => 'admin/pengajuan-judul/index.php',
            // Keduanya juga mendarat di halaman induk saat dibuka langsung.
            // Aman terhadap rekursi: peta ini hanya dipakai pada pemuatan
            // PENUH, sedangkan tab mengambil isinya dengan header AJAX yang
            // dilayani jalur lain dan tetap mengembalikan markup aslinya.
            'jadwalPresentasi' => 'admin/penjadwalan/index.php',
            'wawancara' => 'admin/penjadwalan/index.php',
            'ruangan' => 'admin/ruangan/index.php',
            // Halaman 404 (dirender saat nama halaman tidak dikenal)
            '404' => 'errors/404.php',
        ];
        $viewFile = $pageViewMap[$initialPage] ?? 'admin/dashboard/index.php';
        require_once __DIR__ . "/../" . $viewFile;
        ?>
    </div>


    <!-- Bootstrap Custom Modal (Redesigned with Tailwind) -->
    <div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="customModal" aria-labelledby="customModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
        <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
            <div class="relative isolate bg-white w-full rounded-[24px] shadow-2xl overflow-hidden">
                <div class="text-center p-6 md:p-8 flex flex-col items-center">
                    <img id="modalGif" src="" alt="Animation" class="mb-4" style="width: 100px; display: none;">
                    <p id="modalMessage" class="text-slate-700 font-semibold text-base md:text-lg mb-6 leading-relaxed">Pesan akan ditampilkan di sini.</p>
                    <button type="button" id="closeModal" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-blue-500/10 cursor-pointer border-0" data-modal-close>Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Confirm Modal (Redesigned with Tailwind) -->
    <div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="confirmModal" aria-labelledby="confirmModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
        <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
            <div class="relative isolate bg-white w-full rounded-[24px] shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-[24px]">
                    <h5 class="font-bold text-white text-base" id="confirmModalLabel">Konfirmasi</h5>
                    <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
                </div>
                <div class="text-center p-6 text-slate-700 text-sm md:text-base leading-relaxed">
                    <p id="confirmModalMessage" class="mb-0"></p>
                </div>
                <div class="border-0 flex justify-center gap-3 p-6 pt-0">
                    <button type="button" id="confirmModalCancel" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition min-w-[100px] border-0 cursor-pointer" data-modal-close>Tidak</button>
                    <button type="button" id="confirmModalConfirm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition min-w-[100px] shadow-sm border-0 cursor-pointer">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!-- App Scripts -->
    <script src="<?= APP_URL ?>/Assets/js/core/app.js?v=<?= time() ?>"></script>
    <script src="<?= APP_URL ?>/Assets/js/core/sidebar.js?v=<?= time() ?>"></script>
</body>
</html>






