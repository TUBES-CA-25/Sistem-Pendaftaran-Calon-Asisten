<?php
/**
 * Reusable Page Navbar Component
 *
 * Variables yang diperlukan:
 * @var string $title - Judul halaman (wajib)
 * @var string $subtitle - Subjudul halaman (opsional)
 * @var string $icon - Class icon (opsional, contoh: 'bx bx-library' atau 'bi bi-people')
 * @var string $userName - Nama user (opsional, default dari session)
 * @var string $photo - Path foto user (opsional, default dari session)
 * @var array $breadcrumb - Array breadcrumb (opsional, contoh: ['Home' => 'dashboard', 'Current' => ''])
 */

// Priority: Latest Session or static variable
$role = $role ?? ($_SESSION['user']['role'] ?? ($_SESSION['role'] ?? 'User'));
$userName = $_SESSION['user']['username'] ?? ($userName ?? 'User');

// Dynamic Photo Logic
if ($role === 'Admin') {
    // Use admin photo or fallback to Rectangle.png
    if (!isset($photo) || empty($photo)) {
        $photo = \App\Controllers\HomeController::getAdminPhoto($_SESSION['user']['id'] ?? 1);
    }
    $userName = 'Admin';
} else {
    // Student/User photo logic - trust the photo from controller if provided
    if (!isset($photo) || empty($photo)) {
        // Fallback for Users if nothing provided
        $photo = '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';
    }
}

// Generate breadcrumb if not provided
if (!isset($breadcrumb)) {
    $breadcrumb = [
        'Home' => 'dashboard',
        $title => ''
    ];
}

// Determine navbar style based on role
$role = $role ?? ($_SESSION['user']['role'] ?? ($_SESSION['role'] ?? 'User'));
// User requested white navbar for all roles
$navbarClass = 'page-navbar page-navbar-user';

// Count notifications for badge (only for user)
if (!isset($notificationCount) && isset($notifikasi) && is_array($notifikasi)) {
    // Filter only unread notifications
    $unreadNotifs = array_filter($notifikasi, function($n) {
        // If is_read not set, assume unread (0). If set, check if 0.
        return !isset($n['is_read']) || $n['is_read'] == 0;
    });
    $notificationCount = count($unreadNotifs);
}
?>

<!-- CATATAN: nav ini TIDAK boleh memakai overflow-hidden.
     Dropdown notifikasi menggantung ke bawah melewati batas nav; dengan
     overflow-hidden di sini, dropdown-nya terpotong (tertimbun konten
     halaman). Lingkaran dekorasi yang sengaja meluber tetap dipotong,
     tetapi oleh pembungkusnya sendiri di bawah ini. -->
<nav class="w-[96%] lg:w-[98%] max-w-[1400px] mx-auto my-4 bg-gradient-to-br from-primary to-secondary rounded-2xl shadow-lg shadow-blue-500/10 relative transition-all duration-300 py-5 px-4 md:py-7 md:px-6">
    <!-- Background Decoration Container: overflow-hidden dipindah ke sini -->
    <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none">
        <div class="absolute -top-16 -right-16 w-80 h-80 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-20 left-8 w-52 h-52 bg-white/5 rounded-full"></div>
    </div>

    <!-- z-10 (bukan `z-1` yang bukan class Tailwind valid) agar isi header
         berada di atas lingkaran dekorasi. -->
    <div class="w-full relative z-10 px-4 flex items-center justify-between">
        <!-- Left: Burger Menu + Icon + Title -->
        <div class="flex items-center gap-3">
            <button class="flex flex-col justify-between w-6 h-[18px] bg-transparent border-0 p-0 cursor-pointer z-[1030] lg:hidden" id="burgerMenuBtn" type="button" aria-label="Toggle Sidebar">
                <span class="block w-full h-[2px] bg-white rounded-full transition-all duration-300 origin-left [.sidebar-open_&]:rotate-45 [.sidebar-open_&]:translate-x-[2px] [.sidebar-open_&]:-translate-y-[1px]"></span>
                <span class="block w-full h-[2px] bg-white rounded-full transition-all duration-300 [.sidebar-open_&]:opacity-0 [.sidebar-open_&]:translate-x-3"></span>
                <span class="block w-full h-[2px] bg-white rounded-full transition-all duration-300 origin-left [.sidebar-open_&]:-rotate-45 [.sidebar-open_&]:translate-x-[2px] [.sidebar-open_&]:translate-y-[1px]"></span>
            </button>

            <?php if (!empty($icon)): ?>
                <div class="w-10 h-10 md:w-11 md:h-11 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-lg md:text-xl text-white shrink-0 shadow-inner">
                    <i class='<?= $icon ?>'></i>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="text-base md:text-xl font-bold text-white m-0 leading-tight tracking-wide truncate max-w-[180px] md:max-w-xs"><?= $title ?? 'Judul Halaman' ?></h1>
                <?php if (!empty($subtitle)): ?>
                    <p class="text-[10px] md:text-xs text-blue-100 m-0 mt-0.5 opacity-90 leading-none"><?= $subtitle ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-3">
            <?php if ($role !== 'Admin'): ?>
                <!-- Notification Icon (User only) -->
                <div data-dropdown class="relative">
                    <button class="w-10 h-10 md:w-11 md:h-11 bg-white/10 hover:bg-white/20 active:scale-95 border border-white/10 rounded-full text-white flex items-center justify-center relative transition-all duration-200" type="button" data-dropdown-toggle aria-expanded="false" aria-label="Notifikasi">
                        <i class='bx bx-bell text-lg md:text-xl'></i>
                        <?php if (isset($notificationCount) && $notificationCount> 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 border-2 border-primary-dark text-white font-bold rounded-full text-[9px] w-5 h-5 flex items-center justify-center">
                            <?= $notificationCount ?>
                        </span>
                        <?php endif; ?>
                    </button>
                    <!-- z-[1045]: harus di atas konten halaman DAN sidebar (z-[1040]),
                         tetapi tetap di bawah modal (z-[1050]). -->
                    <ul data-dropdown-menu class="hidden absolute right-0 top-full z-[1045] border-0 shadow-2xl rounded-2xl p-0 overflow-hidden bg-white w-[340px] max-w-[90vw] mt-2 list-none">
                        <li class="flex justify-between items-center bg-gray-50/85 backdrop-blur-md px-4 py-3 border-b border-gray-100">
                            <span class="font-bold text-gray-800">Notifikasi</span>
                            <?php if (isset($notificationCount) && $notificationCount> 0): ?>
                                <span class="bg-primary-dark text-white rounded-full px-2 py-1 text-xs"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </li>
                        <li class="block">
                        <?php if (isset($notifikasi) && !empty($notifikasi)): ?>
                            <?php foreach (array_slice($notifikasi, 0, 5) as $notif): ?>
                                <a class="block px-4 py-3 hover:bg-blue-50/40 transition-colors border-b border-gray-100 last:border-0 notification-item no-underline" href="#" data-page="notification">
                                    <div class="flex gap-3 items-start">
                                        <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center text-primary-dark shrink-0 mt-0.5">
                                            <i class='bx bx-info-circle text-lg'></i>
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <p class="mb-1 text-xs text-gray-700 font-medium leading-normal text-wrap break-all">
                                                <?= htmlspecialchars($notif['pesan'] ?? 'Notifikasi baru') ?>
                                            </p>
                                            <small class="text-gray-400 block text-[10px]">
                                                <?= isset($notif['created_at']) ? date('d M, H:i', strtotime($notif['created_at'])) : '' ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            <a class="block text-center text-xs text-primary-dark hover:text-primary hover:bg-blue-50/50 font-semibold py-2.5 bg-gray-50/80 border-t border-gray-100 no-underline" href="#" data-page="notification">
                                Lihat Semua Notifikasi
                            </a>
                        <?php else: ?>
                            <div class="text-center text-gray-400 py-6 px-4">
                                <i class='bx bx-bell-off text-3xl block mb-2 text-gray-300'></i>
                                <small class="text-xs">Tidak ada notifikasi</small>
                            </div>
                        <?php endif; ?>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Profile Section -->
            <?php if ($role === 'Admin'): ?>
                <!-- Admin: Simple static display without dropdown -->
                <div class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-full py-1.5 px-3 md:px-4 transition-all duration-200">
                    <img src="<?= $photo ?>" alt="Profile" class="w-8 h-8 md:w-9 md:h-9 rounded-full object-cover border-2 border-white/60 bg-white" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/Rectangle.png'">
                    <span class="text-white text-xs md:text-sm font-semibold tracking-wide truncate max-w-[100px] md:max-w-[150px] hidden sm:inline"><?= htmlspecialchars($userName) ?></span>
                </div>
            <?php else: ?>
                <!-- User: Simple static display without dropdown -->
                <div class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-full py-1.5 px-3 md:px-4 transition-all duration-200">
                    <img src="<?= $photo ?>" alt="Profile" class="w-8 h-8 md:w-9 md:h-9 rounded-full object-cover border-2 border-white/60 bg-white" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                    <span class="text-white text-xs md:text-sm font-semibold tracking-wide truncate max-w-[100px] md:max-w-[150px] hidden sm:inline"><?= htmlspecialchars($userName) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!--
  Toast container SENGAJA DIHAPUS dari sini.
  Toast global sudah dirender oleh layout (layouts/main.php & layouts/main_admin.php)
  dengan gaya Tailwind. Karena PageHeader di-include di dalam #content, duplikat
  #liveToast di sini muncul LEBIH DULU di DOM, sehingga
  document.getElementById('liveToast') di app.js justru mengambil elemen ini
  dan toast tampil tanpa gaya. Jangan tambahkan kembali di sini.
-->

