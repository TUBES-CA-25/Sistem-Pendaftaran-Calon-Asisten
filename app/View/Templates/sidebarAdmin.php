<?php
/**
 * Admin Sidebar View
 *
 * Data yang diterima dari controller:
 * @var string $role - Role user (Admin)
 * @var string $userName - Username admin
 * @var string $photo - Path foto admin
 */
$role = $role ?? 'Admin';
$userName = $userName ?? 'Admin';

// Handle photo - could be array, string, or null
$role = $role ?? 'Admin';
$userName = $userName ?? 'Admin';
?>
<div class="sidebar fixed top-0 left-0 h-screen w-[240px] z-[1040] flex flex-col p-0 transition-transform duration-300 lg:translate-x-0 -translate-x-full shadow-md border-r border-gray-100 bg-white" id="sidebar">
    <div class="flex flex-col justify-center items-center py-6 px-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="ICLABS Logo" class="w-10 h-10 shrink-0 object-contain">
            <span class="text-lg font-bold tracking-wider text-[#3dc2ec]">ICLABS</span>
        </div>
    </div>
    
    <ul class="flex-grow py-4 list-none m-0 overflow-y-auto">
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/dashboard" data-page="dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bxs-dashboard text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="nav-item whitespace-nowrap">Dashboard</span>
            </a>
        </li>
        <li class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase px-4 pt-4 pb-2">MENU UTAMA</li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/lihatPeserta" data-page="lihatPeserta" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bxs-user-check text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="nav-item whitespace-nowrap">Lihat Peserta</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/pengajuanJudul" data-page="pengajuanJudul" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bxs-file-doc text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="nav-item whitespace-nowrap">Pengajuan Judul</span>
            </a>
        </li>
        
        <li class="relative mx-3 my-1">
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline dropdown-toggle">
                <i class="bi bi-journal-text text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="nav-item whitespace-nowrap">Bank Soal</span>
            </a>
            <ul class="submenu list-none p-0 m-0 bg-gray-50/50">
                <li class="relative my-0.5">
                    <a href="<?= APP_URL ?>/bankSoal" data-page="bankSoal" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#1d4ed8] transition-all text-xs font-medium no-underline">
                        <i class='bx bx-list-ul text-sm shrink-0 w-4 flex justify-center'></i>
                        <span class="nav-item whitespace-nowrap">Daftar Soal</span>
                    </a>
                </li>
                <li class="relative my-0.5">
                    <a href="<?= APP_URL ?>/importSoal" data-page="importSoal" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#1d4ed8] transition-all text-xs font-medium no-underline">
                        <i class='bx bx-transfer text-sm shrink-0 w-4 flex justify-center'></i>
                        <span class="nav-item whitespace-nowrap">Import/Export</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="relative mx-3 my-1">
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline dropdown-toggle">
                <i class="bi bi-calendar-event text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="nav-item whitespace-nowrap">Penjadwalan</span>
            </a>
            <ul class="submenu list-none p-0 m-0 bg-gray-50/50">
                <li class="relative my-0.5">
                    <a href="<?= APP_URL ?>/jadwaltes" data-page="jadwaltes" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#1d4ed8] transition-all text-xs font-medium no-underline">
                        <i class='bx bx-edit text-sm shrink-0 w-4 flex justify-center'></i>
                        <span class="nav-item whitespace-nowrap">Tes Tertulis</span>
                    </a>
                </li>
                <li class="relative my-0.5">
                    <a href="<?= APP_URL ?>/jadwalPresentasi" data-page="jadwalPresentasi" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#1d4ed8] transition-all text-xs font-medium no-underline">
                        <i class='bx bx-slideshow text-sm shrink-0 w-4 flex justify-center'></i>
                        <span class="nav-item whitespace-nowrap">Presentasi</span>
                    </a>
                </li>
                <li class="relative my-0.5">
                    <a href="<?= APP_URL ?>/wawancara" data-page="wawancara" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#1d4ed8] transition-all text-xs font-medium no-underline">
                        <i class='bx bx-user-voice text-sm shrink-0 w-4 flex justify-center'></i>
                        <span class="nav-item whitespace-nowrap">Wawancara</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/ruangan" data-page="ruangan" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bx-home-alt text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="nav-item whitespace-nowrap">Ruangan</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/lihatnilai" data-page="lihatnilai" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bx-bar-chart-alt-2 text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="nav-item whitespace-nowrap">Nilai</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/daftarKehadiran" data-page="daftarKehadiran" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bx-calendar-check text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="nav-item whitespace-nowrap">Rekap</span>
            </a>
        </li>
    </ul>
    
    <div class="p-4 border-t border-gray-100 mt-auto">
        <a href="#" data-page="logout" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all text-sm font-medium no-underline">
            <i class='bx bx-log-out text-lg shrink-0 w-6 flex justify-center'></i>
            <span class="nav-item">Logout</span>
        </a>
    </div>
</div>

<style type="text/tailwindcss">
    /* Custom scrollbar for sidebar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Active link style with blue gradient matching the navbar */
    .sidebar a.active {
        @apply bg-gradient-to-r from-[#1d4ed8] via-[#2563eb] to-[#3dc2ec] text-white shadow-md shadow-blue-500/10 !important;
    }
    .sidebar a.active i {
        @apply text-white !important;
    }

    /* Submenu animation and display */
    .sidebar .submenu {
        @apply max-h-0 overflow-hidden opacity-0 pl-6 bg-transparent transition-all duration-300;
    }
    .sidebar .submenu.show {
        @apply max-h-96 opacity-100 py-1.5;
    }
    
    /* Submenu active item styled with blue gradient */
    .sidebar .submenu a.active {
        @apply bg-gradient-to-r from-[#1d4ed8] via-[#2563eb] to-[#3dc2ec] text-white shadow-sm !important;
    }
    .sidebar .submenu a.active i {
        @apply text-white !important;
    }

    /* Dropdown Toggle Caret Positioning */
    .sidebar .dropdown-toggle::after {
        @apply transition-transform duration-300 ml-auto;
    }
    .sidebar .dropdown-toggle.show::after {
        @apply rotate-180;
    }
</style>

