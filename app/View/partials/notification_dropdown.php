<?php
$notifications = $notifications ?? [];
$count = $count ?? 0;

$html = '
<li class="dropdown-header d-flex justify-content-between align-items-center">
    <span class="fw-bold">Notifikasi</span>';
if ($count > 0) {
    $html .= '<span class="badge bg-primary rounded-pill">' . $count . '</span>';
}
$html .= '
</li>
<li><hr class="dropdown-divider my-1"></li>';

if (count($notifications) > 0) {
    $limitedNotifs = array_slice($notifications, 0, 5);
    foreach ($limitedNotifs as $notif) {
        $dateStr = '';
        if (!empty($notif['created_at'])) {
            $timestamp = strtotime($notif['created_at']);
            // e.g. 14 Jul, 10:30
            $dateStr = date('d M, H:i', $timestamp);
            
            // translate months to indonesian format roughly as JS did if needed
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            $dateStr = str_replace($months, $bulan, $dateStr);
            $dateStr = str_replace(':', ':', $dateStr);
        }

        $html .= '
        <li>
            <a class="dropdown-item notification-item p-3" href="#" data-page="notification" style="white-space: normal;">
                <div class="d-flex gap-3 align-items-start">
                    <div class="notification-icon flex-shrink-0 mt-1">
                        <i class=\'bx bx-info-circle text-primary\'></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <p class="mb-1 small text-dark fw-medium lh-sm text-wrap text-break">' . htmlspecialchars($notif['pesan'], ENT_QUOTES, 'UTF-8') . '</p>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">' . $dateStr . '</small>
                    </div>
                </div>
            </a>
        </li>';
    }
    $html .= '
    <li><hr class="dropdown-divider my-1"></li>
    <li>
        <a class="dropdown-item text-center small text-primary fw-semibold py-2" href="#" data-page="notification">
            Lihat Semua Notifikasi
        </a>
    </li>';
} else {
    $html .= '
    <li>
        <div class="dropdown-item text-center text-muted py-3">
            <i class=\'bx bx-bell-off fs-3 d-block mb-2\'></i>
            <small>Tidak ada notifikasi</small>
        </div>
    </li>';
}

echo $html;
