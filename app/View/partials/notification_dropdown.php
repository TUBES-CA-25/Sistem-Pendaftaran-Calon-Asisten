<?php
$notifications = $notifications ?? [];
$count = $count ?? 0;

$html = '
<li class="flex justify-between items-center bg-gray-50/85 px-4 py-3 border-b border-gray-100">
    <span class="font-bold text-gray-800">Notifikasi</span>';
if ($count> 0) {
    $html .= '<span class="bg-primary-dark text-white rounded-full px-2 py-1 text-xs">' . $count . '</span>';
}
$html .= '
</li>';

if (count($notifications)> 0) {
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
            <a class="notification-item block px-4 py-3 hover:bg-blue-50/40 transition-colors border-b border-gray-100 last:border-0 no-underline" href="#" data-page="notification" style="white-space: normal;">
                <div class="flex gap-3 items-start">
                    <div class="notification-icon w-8 h-8 rounded-full bg-primary-light flex items-center justify-center text-primary-dark shrink-0 mt-0.5">
                        <i class=\'bx bx-info-circle text-lg\'></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="mb-1 text-xs text-gray-700 font-medium leading-normal break-words">' . htmlspecialchars($notif['pesan'], ENT_QUOTES, 'UTF-8') . '</p>
                        <small class="text-gray-400 block text-[10px]">' . $dateStr . '</small>
                    </div>
                </div>
            </a>
        </li>';
    }
    $html .= '
    <li>
        <a class="block text-center text-xs text-primary-dark hover:text-primary hover:bg-blue-50/50 font-semibold py-2.5 bg-gray-50/80 border-t border-gray-100 no-underline" href="#" data-page="notification">
            Lihat Semua Notifikasi
        </a>
    </li>';
} else {
    $html .= '
    <li>
        <div class="text-center text-gray-400 py-6 px-4">
            <i class=\'bx bx-bell-off text-3xl block mb-2 text-gray-300\'></i>
            <small class="text-xs">Tidak ada notifikasi</small>
        </div>
    </li>';
}

echo $html;
