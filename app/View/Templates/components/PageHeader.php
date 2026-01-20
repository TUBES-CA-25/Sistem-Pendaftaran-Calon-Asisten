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

// Default values
$userName = $userName ?? ($_SESSION['userName'] ?? 'User');
$defaultPhoto = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9InVybCgjZ3JhZGllbnQwKSIvPgo8ZGVmcz4KPGxpbmVhckdyYWRpZW50IGlkPSJncmFkaWVudDAiIHgxPSIwIiB5MT0iMCIgeDI9IjQwIiB5Mj0iNDAiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzNkYzJlYyIvPgo8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMyNTYzZWIiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8cGF0aCBkPSJNMjAgMThDMjIuMjA5MSAxOCAyNCAxNi4yMDkxIDI0IDE0QzI0IDExLjc5MDkgMjIuMjA5MSAxMCAyMCAxMEMxNy43OTA5IDEwIDE2IDExLjc5MDkgMTYgMTRDMTYgMTYuMjA5MSAxNy43OTA5IDE4IDIwIDE4WiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTMwIDMwQzMwIDI1LjU4MTcgMjUuNTIyOCAyMiAyMCAyMkMxNC40NzcyIDIyIDEwIDI1LjU4MTcgMTAgMzBIMTJDMTIgMjYuNjg2MyAxNS41ODE3IDI0IDIwIDI0QzI0LjQxODMgMjQgMjggMjYuNjg2MyAyOCAzMEgzMFoiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPg==';

if (isset($photo) && is_array($photo) && !empty($photo)) {
    $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . $photo[0];
} elseif (isset($photo) && is_string($photo) && !empty($photo)) {
    if (strpos($photo, '/Sistem-Pendaftaran-Calon-Asisten') === false && strpos($photo, 'data:image') === false) {
        $photo = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . $photo;
    }
} else {
    $photo = $defaultPhoto;
}

// Generate breadcrumb if not provided
if (!isset($breadcrumb)) {
    $breadcrumb = [
        'Home' => 'dashboard',
        $title => ''
    ];
}

// Determine navbar style based on role
$role = $role ?? ($_SESSION['role'] ?? 'User');
$navbarClass = ($role === 'Admin') ? 'page-navbar page-navbar-admin' : 'page-navbar page-navbar-user';

// Count notifications for badge (only for user)
if (!isset($notificationCount) && isset($notifikasi) && is_array($notifikasi)) {
    $notificationCount = count($notifikasi);
}
?>

<nav class="navbar <?= $navbarClass ?>">
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

        <!-- Center: Breadcrumb -->
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb navbar-breadcrumb mb-0">
                <?php
                $items = array_keys($breadcrumb);
                $lastIndex = count($items) - 1;
                foreach ($breadcrumb as $label => $page):
                    $isLast = ($label === $items[$lastIndex]);
                ?>
                    <li class="breadcrumb-item <?= $isLast ? 'active' : '' ?>">
                        <?php if (!$isLast && !empty($page)): ?>
                            <a href="#" data-page="<?= $page ?>"><?= htmlspecialchars($label) ?></a>
                        <?php else: ?>
                            <?= htmlspecialchars($label) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>

        <!-- Right: Actions -->
        <div class="d-flex align-items-center gap-2">
            <?php if ($role !== 'Admin'): ?>
                <!-- Notification Icon (User only) -->
                <div class="dropdown">
                    <button class="btn navbar-action-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-bell fs-5'></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;">
                            <?= isset($notificationCount) && $notificationCount > 0 ? $notificationCount : '' ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end navbar-notification-dropdown">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Notifikasi</span>
                            <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                                <span class="badge bg-primary rounded-pill"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <?php if (isset($notifikasi) && !empty($notifikasi)): ?>
                            <?php foreach (array_slice($notifikasi, 0, 5) as $notif): ?>
                                <li>
                                    <a class="dropdown-item notification-item" href="#" data-page="notification">
                                        <div class="d-flex gap-2">
                                            <div class="notification-icon">
                                                <i class='bx bx-info-circle'></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 small"><?= htmlspecialchars(mb_strimwidth($notif['pesan'] ?? 'Notifikasi baru', 0, 60, '...')) ?></p>
                                                <small class="text-muted" style="font-size: 0.75rem;"><?= isset($notif['created_at']) ? date('d M, H:i', strtotime($notif['created_at'])) : '' ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item text-center small text-primary fw-semibold" href="#" data-page="notification">
                                    Lihat Semua Notifikasi
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <div class="dropdown-item text-center text-muted py-3">
                                    <i class='bx bx-bell-off fs-3 d-block mb-2'></i>
                                    <small>Tidak ada notifikasi</small>
                                </div>
                            </li>
                        <?php endif; ?>
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
