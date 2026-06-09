<?php
/**
 * Pengumuman View
 */
?>

<!-- Page Header -->
<?php
    $title = 'Pengumuman';
    $subtitle = 'Informasi penting terkait seleksi calon asisten';
    $icon = 'bx bx-notification';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-4xl mx-auto px-4 pb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden text-center">
        <div class="bg-slate-50 border-b border-slate-100 py-4 px-6">
            <h5 class="text-blue-600 font-bold flex items-center justify-center gap-2">
                <i class='bx bx-bell text-xl'></i> Pengumuman
            </h5>
        </div>
        <div class="p-8 md:p-12">
            <h5 class="text-2xl font-black text-slate-800 mb-4">Special title treatment</h5>
            <p class="text-slate-500 leading-relaxed text-sm mb-6 max-w-2xl mx-auto">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
            <a href="#" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition duration-200" data-page="dashboard">
                <i class='bx bx-arrow-back'></i> Kembali ke Dashboard
            </a>
        </div>
        <div class="bg-blue-600 text-white py-3 text-xs font-semibold flex items-center justify-center gap-2">
            <i class='bx bx-time'></i> 2 days ago
        </div>
    </div>
</main>
