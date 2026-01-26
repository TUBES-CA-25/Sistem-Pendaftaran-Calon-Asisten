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
    if (class_exists('App\Controllers\Admin\AdminProfileController')) {
        $photo = \App\Controllers\Admin\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
    } else {
        $photo = '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png';
    }
} else {
    // Student/User photo logic
    if (isset($photo)) {
        if (is_array($photo) && !empty($photo)) {
            $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photo[0];
        } elseif (is_string($photo) && !empty($photo)) {
            // If it's already a full path or data URL, leave it
            if (strpos($photo, '/Sistem-Pendaftaran-Calon-Asisten') === false && strpos($photo, 'data:image') === false) {
                 $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photo;
            }
        }
    } else {
        // Fallback for Users if nothing provided
        $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/default.png';
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

<nav class="navbar <?= $navbarClass ?>">
    <!-- Background Decoration Container -->
    <div class="navbar-decoration"></div>
    
    <div class="container-fluid px-4">
        <!-- Left: Icon + Title -->
        <div class="navbar-brand d-flex align-items-center gap-3">
            <?php if (!empty($icon)): ?>
                <div class="navbar-icon">
                    <i class='<?= $icon ?>'></i>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="navbar-title"><?= $title ?? 'Judul Halaman' ?></h1>
                <?php if (!empty($subtitle)): ?>
                    <p class="navbar-subtitle"><?= $subtitle ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Spacer -->
        <div class="flex-grow-1"></div>

        <!-- Right: Actions -->
        <div class="d-flex align-items-center gap-2">
            <?php if ($role !== 'Admin'): ?>
                <!-- Notification Icon (User only) -->
                <div class="dropdown">
                    <button class="btn navbar-action-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-bell fs-5'></i>
                        <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;">
                            <?= $notificationCount ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end navbar-notification-dropdown border-0 shadow-lg rounded-4 p-0 overflow-hidden" style="width: 320px; max-width: 90vw;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center bg-light px-3 py-2 border-bottom">
                            <span class="fw-bold text-dark">Notifikasi</span>
                            <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                                <span class="badge bg-primary rounded-pill"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group list-group-flush">
                        <?php if (isset($notifikasi) && !empty($notifikasi)): ?>
                            <?php foreach (array_slice($notifikasi, 0, 5) as $notif): ?>
                                <a class="list-group-item list-group-item-action p-3 notification-item border-bottom-0 border-top-0" href="#" data-page="notification">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="notification-icon flex-shrink-0 mt-1">
                                            <i class='bx bx-info-circle text-primary fs-5'></i>
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <p class="mb-1 small text-dark fw-medium lh-sm text-wrap text-break">
                                                <?= htmlspecialchars($notif['pesan'] ?? 'Notifikasi baru') ?>
                                            </p>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                <?= isset($notif['created_at']) ? date('d M, H:i', strtotime($notif['created_at'])) : '' ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            <a class="list-group-item list-group-item-action text-center small text-primary fw-semibold py-2 bg-light border-top" href="#" data-page="notification">
                                Lihat Semua Notifikasi
                            </a>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class='bx bx-bell-off fs-3 d-block mb-2 text-secondary'></i>
                                <small>Tidak ada notifikasi</small>
                            </div>
                        <?php endif; ?>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <button class="btn navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $photo ?>" alt="Profile" class="navbar-profile-img">
                    <span class="navbar-profile-name d-none d-sm-inline"><?= htmlspecialchars($userName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end navbar-dropdown">
                    <li>
                        <a class="dropdown-item" href="#" data-page="profile">
                            <i class='bx bx-user me-2'></i>Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" data-page="logout">
                            <i class='bx bx-log-out me-2'></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
