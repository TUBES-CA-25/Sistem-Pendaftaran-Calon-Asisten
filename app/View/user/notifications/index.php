<?php
/**
 * Notification View
 */
?>

<!-- Page Header -->
<?php
    $title = 'Notifikasi';
    $subtitle = 'Daftar pesan dan pemberitahuan';
    $icon = 'bx bx-bell';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class='bx bx-message-square-dots'></i> Pesan Notifikasi
            </h5>
        </div>
        <div class="card-body p-4">
    <?php
    use App\Controllers\notifications\NotificationControllers;
    $notifications = NotificationControllers::getMessageById() ?? [];
    ?>
    <style>
        .notification-item {
            border-left: 4px solid #e9ecef;
            transition: all 0.2s;
        }
        .notification-item:hover {
            border-left-color: var(--bs-primary);
            background-color: #f8f9fa;
        }
    </style>
    <ul id="messageList" class="list-group list-group-flush">
        <?php if (empty($notifications)): ?>
            <div id="emptyState" class="text-center py-5 text-muted">
                <i class='bx bx-inbox display-1 opacity-50'></i>
                <p class="mt-3">Tidak ada pesan notifikasi</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <?php 
                    $date = isset($notif['created_at']) ? date('d M Y, H:i', strtotime($notif['created_at'])) : '-';
                ?>
                <li class="list-group-item notification-item py-3 px-4">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                        <strong class="text-primary-emphasis">
                            <i class="bx bx-envelope me-1"></i> Admin
                        </strong>
                        <small class="text-muted bg-light px-2 py-1 rounded-pill border">
                            <i class="bx bx-time-five me-1"></i><?= $date ?>
                        </small>
                    </div>
                    <p class="mb-1 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                        <?= htmlspecialchars($notif['pesan']) ?>
                    </p>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
