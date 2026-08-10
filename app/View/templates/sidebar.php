<?php
/**
 * Sidebar View
 *
 * Data yang diterima dari controller:
 * @var string $role - Role user (User/Admin)
 * @var string $userName - Username user
 * @var string $photo - Path foto user
 */
$role = $role ?? 'User';
$userName = $userName ?? 'Guest';

// Handle photo - could be array, string, or null
$defaultPhoto = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjUiIGN5PSIyNSIgcj0iMjUiIGZpbGw9InVybCgjZ3JhZGllbnQwKSIvPgo8ZGVmcz4KPGxpbmVhckdyYWRpZW50IGlkPSJncmFkaWVudDAiIHgxPSIwIiB5MT0iMCIgeDI9IjUwIiB5Mj0iNTAiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzNkYzJlYyIvPgo8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMyNTYzZWIiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8cGF0aCBkPSJNMjUgMjNDMjcuNzYxNCAyMyAzMCAyMC43NjE0IDMwIDE4QzMwIDE1LjIzODYgMjcuNzYxNCAxMyAyNSAxM0MyMi4yMzg2IDEzIDIwIDE1LjIzODYgMjAgMThDMjAgMjAuNzYxNCAyMi4yMzg2IDIzIDI1IDIzWiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTM3IDM3QzM3IDMxLjQ3NzIgMzEuNjI3NCAyNyAyNSAyN0MxOC4zNzI2IDI3IDEzIDMxLjQ3NzIgMTMgMzdIMTVDMTUgMzIuNTgyIDE5LjQ3NzIgMjkgMjUgMjlDMzAuNTIyOCAyOSAzNSAzMi41ODIgMzUgMzdIMzdaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4=';
if (isset($photo) && is_array($photo) && !empty($photo)) {
    $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photo[0];
} elseif (isset($photo) && is_string($photo) && !empty($photo)) {
    // If photo is already a full path, use it
    if (strpos($photo, '/Sistem-Pendaftaran-Calon-Asisten') === false) {
        $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photo;
    }
} else {
    $photo = $defaultPhoto;
}
?>
<div class="sidebar fixed top-0 left-0 h-screen w-[240px] z-[1040] flex flex-col p-0 transition-transform duration-300 lg:translate-x-0 -translate-x-full shadow-md border-r border-gray-100 bg-white" id="sidebar">
    <div class="flex flex-col justify-center items-center py-6 px-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="IC-Assist Logo" class="w-10 h-10 shrink-0 object-contain">
            <span class="text-lg font-bold tracking-wider text-primary">IC-ASSIST</span>
        </div>
    </div>

    <ul class="flex-grow min-h-0 py-4 list-none m-0 overflow-y-auto">
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/dashboard" data-page="dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bx bx-home text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Dashboard</span>
            </a>
        </li>
        <li class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase px-4 pt-4 pb-2">MENU UTAMA</li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/biodata" data-page="biodata" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bx bxs-id-card text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Lengkapi Biodata</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/uploadBerkas" data-page="uploadBerkas" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bx bx-file text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Upload Berkas</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/tesTulis" data-page="tesTulis" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bx bx-task text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Tes Tulis</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/presentasi" data-page="presentasi" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bx bx-chalkboard text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Presentasi</span>
            </a>
        </li>
        <li class="relative mx-3 my-1">
            <a href="<?= APP_URL ?>/wawancara" data-page="wawancara" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#1d4ed8] hover:translate-x-1 transition-all text-sm font-medium no-underline">
                <i class="bx bx-user-voice text-lg shrink-0 w-6 flex justify-center"></i>
                <span class="whitespace-nowrap">Jadwal</span>
            </a>
        </li>
    </ul>
    <div class="p-4 border-t border-gray-100 mt-auto">
        <a href="#" data-page="logout" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all text-sm font-medium no-underline">
            <i class="bx bx-log-out text-lg shrink-0 w-6 flex justify-center"></i>
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

    /* Active link style - gradien brand tunggal, sama dengan navbar & tombol login */
    .sidebar a.active {
        @apply bg-gradient-to-br from-primary to-secondary text-white shadow-md shadow-blue-500/10 !important;
    }
    .sidebar a.active i {
        @apply text-white !important;
    }
</style>
