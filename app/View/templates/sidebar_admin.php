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
            <span class="text-lg font-bold tracking-wider text-primary">IC-ASSIST</span>
        </div>
    </div>
    
    <ul class="flex-grow min-h-0 py-4 list-none m-0 overflow-y-auto">
        <li class="relative mx-3 my-0.5">
            <a href="<?= APP_URL ?>/dashboard" data-page="dashboard" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bxs-dashboard text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="whitespace-nowrap">Dashboard</span>
            </a>
        </li>
        <li class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase px-4 pt-3 pb-1">DATA PESERTA</li>
        <li class="relative mx-3 my-0.5">
            <a href="<?= APP_URL ?>/lihatPeserta" data-page="lihatPeserta" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bxs-user-check text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="whitespace-nowrap">Lihat Peserta</span>
            </a>
        </li>
        <li class="relative mx-3 my-0.5">
            <a href="<?= APP_URL ?>/pengajuanJudul" data-page="pengajuanJudul" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bxs-file-doc text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="whitespace-nowrap">Pengajuan Judul</span>
            </a>
        </li>
        
        <li class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase px-4 pt-3 pb-1">PERSIAPAN</li>
        <li class="relative mx-3 my-0.5">
            <!--
                Dulu ini dropdown berisi "Daftar Soal" dan "Import/Export".
                Import/Export kini menjadi tab di dalam halaman Bank Soal,
                menyisakan satu anak saja - dropdown untuk satu tujuan hanya
                menambah klik, jadi diratakan menjadi menu langsung.
                Rute /importSoal tetap hidup dan membuka tab tersebut.
            -->
            <a href="<?= APP_URL ?>/bankSoal" data-page="bankSoal" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bi bi-journal-text text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Bank Soal</span>
            </a>
        </li>

        <li class="relative mx-3 my-0.5">
            <a href="<?= APP_URL ?>/ruangan" data-page="ruangan" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bx-home-alt text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="whitespace-nowrap">Ruangan</span>
            </a>
        </li>
        
        <li class="relative mx-3 my-0.5">
            <!--
                Dulu dropdown berisi Tes Tertulis, Presentasi, dan Wawancara.
                Ketiganya kini menjadi tab di dalam satu halaman Penjadwalan,
                diurutkan sesuai tahapan seleksi. Rute lama masing-masing tetap
                hidup dan mendarat di tabnya sendiri.
            -->
            <a href="<?= APP_URL ?>/penjadwalan" data-page="penjadwalan" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bi bi-calendar-event text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Penjadwalan</span>
            </a>
        </li>
        
        <li class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase px-4 pt-3 pb-1">HASIL SELEKSI</li>
        <li class="relative mx-3 my-0.5">
            <a href="<?= APP_URL ?>/lihatnilai" data-page="lihatnilai" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bx-bar-chart-alt-2 text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="whitespace-nowrap">Nilai</span>
            </a>
        </li>
        <li class="relative mx-3 my-0.5">
            <a href="<?= APP_URL ?>/daftarKehadiran" data-page="daftarKehadiran" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class='bx bx-calendar-check text-lg shrink-0 w-6 flex justify-center'></i>
                <span class="whitespace-nowrap">Rekap</span>
            </a>
        </li>
    </ul>
    
    <div class="p-4 border-t border-gray-100 mt-auto">
        <a href="#" data-page="logout" class="flex items-center gap-3 px-4 py-2 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all text-sm font-medium no-underline">
            <i class='bx bx-log-out text-lg shrink-0 w-6 flex justify-center'></i>
            <span >Logout</span>
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
        @apply bg-gradient-to-br from-primary to-secondary text-white shadow-md shadow-blue-500/10 !important;
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
        @apply bg-gradient-to-br from-primary to-secondary text-white shadow-sm !important;
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

