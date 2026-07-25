<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- DataTables 2.x CSS (no jQuery required) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

    <!-- Bootstrap 5.3.3 JS Emulation Layer -->
    <script>
        (function() {
            const modalInstances = new Map();
            const toastInstances = new Map();

            class Modal {
                constructor(element, options = {}) {
                    this.element = typeof element === 'string' ? document.querySelector(element) : element;
                    if (!this.element) return;
                    this.options = options;
                    this._backdrop = null;
                    modalInstances.set(this.element, this);

                    const dismissButtons = this.element.querySelectorAll('[data-bs-dismiss="modal"]');
                    dismissButtons.forEach(btn => btn.addEventListener('click', () => this.hide()));
                }
                show() {
                    if (!this.element) return;
                    this.element.dispatchEvent(new Event('show.bs.modal', { bubbles: true }));
                    document.body.classList.add('modal-open');
                    document.body.style.overflow = 'hidden';
                    if (this.options.backdrop !== false) this._createBackdrop();
                    this.element.classList.remove('hidden');
                    this.element.style.display = 'flex';
                    this.element.classList.add('show', 'flex');
                    this.element.setAttribute('aria-modal', 'true');
                    this.element.removeAttribute('aria-hidden');
                    this.element.offsetHeight;
                    this.element.style.opacity = '1';
                    this.element.dispatchEvent(new Event('shown.bs.modal', { bubbles: true }));
                }
                hide() {
                    if (!this.element) return;
                    this.element.dispatchEvent(new Event('hide.bs.modal', { bubbles: true }));
                    this.element.classList.add('hidden');
                    this.element.style.display = 'none';
                    this.element.classList.remove('show', 'flex');
                    this.element.style.opacity = '0';
                    this.element.setAttribute('aria-hidden', 'true');
                    this.element.removeAttribute('aria-modal');
                    this._removeBackdrop();
                    const openModals = document.querySelectorAll('.modal.show');
                    if (openModals.length === 0) {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                    }
                    this.element.dispatchEvent(new Event('hidden.bs.modal', { bubbles: true }));
                }
                _createBackdrop() {
                    let backdrop = document.querySelector('.modal-backdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop';
                        document.body.appendChild(backdrop);
                        backdrop.offsetHeight;
                        backdrop.classList.add('show');
                    }
                    this._backdrop = backdrop;
                }
                _removeBackdrop() {
                    if (this._backdrop) {
                        this._backdrop.classList.remove('show');
                        const currentBackdrop = this._backdrop;
                        setTimeout(() => {
                            if (currentBackdrop && currentBackdrop.parentNode) {
                                currentBackdrop.parentNode.removeChild(currentBackdrop);
                            }
                        }, 300);
                        this._backdrop = null;
                    }
                }
                static getInstance(element) {
                    const el = typeof element === 'string' ? document.querySelector(element) : element;
                    return modalInstances.get(el) || null;
                }
                static getOrCreateInstance(element, options = {}) {
                    const el = typeof element === 'string' ? document.querySelector(element) : element;
                    let instance = modalInstances.get(el);
                    if (!instance) instance = new Modal(el, options);
                    return instance;
                }
            }

            class Toast {
                constructor(element, options = {}) {
                    this.element = typeof element === 'string' ? document.querySelector(element) : element;
                    if (!this.element) return;
                    this.options = options;
                    this._timeoutId = null;
                    toastInstances.set(this.element, this);
                    const dismissButtons = this.element.querySelectorAll('[data-bs-dismiss="toast"]');
                    dismissButtons.forEach(btn => btn.addEventListener('click', () => this.hide()));
                }
                show() {
                    if (!this.element) return;
                    this.element.classList.remove('hidden');
                    this.element.style.display = 'block';
                    this.element.offsetHeight;
                    this.element.classList.add('show');
                    this.element.style.opacity = '1';
                    if (this.options.delay !== 0) {
                        const delay = this.options.delay || 4000;
                        this._timeoutId = setTimeout(() => this.hide(), delay);
                    }
                }
                hide() {
                    if (!this.element) return;
                    if (this._timeoutId) clearTimeout(this._timeoutId);
                    this.element.style.opacity = '0';
                    this.element.classList.remove('show');
                    setTimeout(() => {
                        this.element.classList.add('hidden');
                        this.element.style.display = 'none';
                    }, 300);
                }
                static getInstance(element) {
                    const el = typeof element === 'string' ? document.querySelector(element) : element;
                    return toastInstances.get(el) || null;
                }
                static getOrCreateInstance(element, options = {}) {
                    const el = typeof element === 'string' ? document.querySelector(element) : element;
                    let instance = toastInstances.get(el);
                    if (!instance) instance = new Toast(el, options);
                    return instance;
                }
            }

            window.bootstrap = window.bootstrap || {};
            window.bootstrap.Modal = Modal;
            window.bootstrap.Toast = Toast;

            // Global event delegation for data-bs triggers (capture phase to bypass stopPropagation on child elements)
            document.addEventListener('click', function(e) {
                const dropdownToggle = e.target.closest('[data-bs-toggle="dropdown"]');
                const modalToggle    = e.target.closest('[data-bs-toggle="modal"]');
                const modalDismiss   = e.target.closest('[data-bs-dismiss="modal"]');
                const toastToggle    = e.target.closest('[data-bs-toggle="toast"]');
                const toastDismiss   = e.target.closest('[data-bs-dismiss="toast"]');

                if (dropdownToggle) {
                    e.preventDefault();
                    // Close all open dropdowns first, then toggle the clicked one
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        const parent = menu.closest('.dropdown');
                        if (parent !== dropdownToggle.closest('.dropdown')) {
                            menu.classList.remove('show');
                        }
                    });
                    const parent = dropdownToggle.closest('.dropdown');
                    if (parent) {
                        const menu = parent.querySelector('.dropdown-menu');
                        if (menu) menu.classList.toggle('show');
                    }
                } else if (modalToggle) {
                    e.preventDefault();
                    const sel = modalToggle.getAttribute('data-bs-target') || modalToggle.getAttribute('href');
                    if (sel) {
                        const el = document.querySelector(sel);
                        if (el) Modal.getOrCreateInstance(el).show();
                    }
                } else if (modalDismiss) {
                    e.preventDefault();
                    const el = modalDismiss.closest('.modal');
                    if (el) Modal.getOrCreateInstance(el).hide();
                } else if (toastToggle) {
                    e.preventDefault();
                    const sel = toastToggle.getAttribute('data-bs-target');
                    if (sel) {
                        const el = document.querySelector(sel);
                        if (el) Toast.getOrCreateInstance(el).show();
                    }
                } else if (toastDismiss) {
                    e.preventDefault();
                    const el = toastDismiss.closest('.toast');
                    if (el) Toast.getOrCreateInstance(el).hide();
                } else {
                    // Click outside: close all open dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            }, true);
        })();
    </script>

    <script>
        const APP_URL = <?= json_encode(APP_URL) ?>;
        window.INITIAL_PAGE = <?= json_encode($initialPage ?? 'dashboard') ?>;
    </script>

    <title>ICLABS</title>
                <style type="text/tailwindcss">
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
            .dataTables_wrapper {
                @apply flex flex-col gap-4;
            }
            .dataTables_wrapper .row {
                @apply flex flex-col sm:flex-row justify-between items-center gap-4 w-full m-0 p-0;
            }
            .dataTables_wrapper .row > div {
                @apply w-full sm:w-auto p-0 flex items-center justify-start sm:justify-end;
            }
            .dataTables_wrapper .row > div:first-child {
                @apply justify-start;
            }
            .dataTables_wrapper .row > div:last-child {
                @apply justify-end;
            }

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
           DATATABLES LAYOUT BRIDGES
           ============================================================ */
        .dataTables_wrapper .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            gap: 1rem !important;
        }
        .dataTables_wrapper .row > div {
            flex: 0 0 auto !important;
            width: auto !important;
            max-width: 100% !important;
            padding: 0 !important;
        }
        /* Mobile fallback: allow stacking under 640px */
        @media (max-width: 640px) {
            .dataTables_wrapper .row {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .dataTables_wrapper .row > div {
                width: 100% !important;
                justify-content: center !important;
            }
        }

        /* ============================================================
           VANILLA MODAL & TOAST EMULATION STYLES
           ============================================================ */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1050;
            display: none;
            overflow-y: auto;
            outline: 0;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
        }
        .modal.show {
            opacity: 1;
        }
        .modal.hidden {
            display: none !important;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1040;
            background-color: rgba(15, 23, 42, 0.4);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-backdrop.show {
            opacity: 1;
        }
        .modal-dialog {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 1.75rem auto;
            pointer-events: none;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .modal.show .modal-dialog {
            transform: scale(1);
        }
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
        }
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            outline: 0;
        }
        .toast {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .toast.show {
            display: block !important;
            opacity: 1;
        }
        .toast.hidden {
            display: none !important;
        }
        .bg-success {
            background-color: #10b981 !important;
        }
        .bg-danger {
            background-color: #ef4444 !important;
        }
        
        /* Custom close button styling to replace Bootstrap btn-close */
        .btn-close {
            box-sizing: content-box;
            width: 0.75em;
            height: 0.75em;
            padding: 0.25em 0.25em;
            color: #000;
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
            border: 0;
            border-radius: 0.375rem;
            opacity: 0.5;
            transition: opacity 0.15s ease-in-out;
            cursor: pointer;
        }
        .btn-close:hover {
            opacity: 0.75;
        }
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Form styling fallback for pages with bootstrap classes */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.5rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #334155;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus {
            border-color: #3b82f6;
            outline: 0;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }
        .form-select {
            display: block;
            width: 100%;
            padding: 0.5rem 2rem 0.5rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #334155;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23334155' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .form-select:focus {
            border-color: #3b82f6;
            outline: 0;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }
        @keyframes page-fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-page-fade {
            animation: page-fade-in 0.6s cubic-bezier(0.65, 0, 0.35, 1) forwards;
        }
    </style>
</head>

<body class="animate-page-fade">
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

    <!-- Tailwind Redesigned Custom Modal -->
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

    <!-- Tailwind Redesigned Generic Action Confirmation Modal -->
    <div class="modal fade" id="actionConfirmModal" tabindex="-1" aria-labelledby="actionConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white/95 backdrop-blur-md rounded-[24px] shadow-2xl overflow-hidden">
                <!-- Header with Icon & Title -->
                <div id="actionConfirmHeader" class="px-6 py-8 flex flex-col items-center relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600">
                    <!-- Background sparkles/glow -->
                    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-white/10 blur-xl"></div>
                    <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full bg-white/10 blur-xl"></div>
                    
                    <div class="w-full text-center relative z-10 flex flex-col items-center">
                        <div class="mb-3">
                            <div class="inline-flex items-center justify-center rounded-full w-20 h-20 bg-white/15 border border-white/30 shadow-inner">
                                <i id="actionConfirmIcon" class="bi bi-question-lg text-4xl text-white"></i>
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
                    <button type="button" id="actionConfirmButton" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition min-w-[120px] shadow-sm flex items-center justify-center gap-2 border-0 cursor-pointer">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Toast Container (Redesigned) -->
    <div class="fixed top-4 right-4 z-[1100] flex flex-col gap-2 pointer-events-none" id="toast-container">
        <div id="liveToast" class="toast border-0 border-l-[4px] rounded-r-lg shadow-lg transition-all duration-200 pointer-events-auto" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="200" style="min-width: 280px; max-width: 400px;">
            <div class="flex items-center gap-2.5 p-3">
                <!-- Icon -->
                <i id="toastIcon" class="bi bi-check-circle-fill text-xl"></i>
                
                <!-- Content -->
                <div class="flex-1">
                    <h6 id="toastTitle" class="font-bold text-[13px] mb-0.5 tracking-wide">Success!</h6>
                    <p id="toastMessage" class="text-[11px] font-medium m-0 leading-tight">Operasi berhasil!</p>
                </div>
                
                <!-- Action -->
                <button type="button" id="toastBtn" class="shrink-0 px-3 py-1 border rounded-md text-[11px] font-bold transition-colors" data-bs-dismiss="toast">
                    Close
                </button>
            </div>
        </div>
    </div>
   
    <script src="<?= APP_URL ?>/Assets/js/app.js?v=<?= time() ?>"></script>
    <script src="<?= APP_URL ?>/Assets/js/ScriptSidebar.js?v=<?= time() ?>"></script>

    <!-- DataTables 2.x (Vanilla JS — no jQuery required) -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

</body>
</html>





