<?php
$today = date('Y-m-d');
$upcoming = array_filter($activities, function($act) use ($today) {
    return $act['tanggal'] >= $today;
});

usort($upcoming, function($a, $b) {
    return strcmp($a['tanggal'], $b['tanggal']);
});

$upcomingFuture = array_slice($upcoming, 0, 1);

if (empty($upcomingFuture)) {
    return;
}

$html = '';
foreach ($upcomingFuture as $act) {
    $icon = $act['jenis'] === 'Wawancara' ? 'bi-people' : ($act['jenis'] === 'Presentasi' ? 'bi-display' : 'bi-calendar-event');
    $colorClass = $act['jenis'] === 'Wawancara' ? 'bg-blue-50 text-blue-600' : ($act['jenis'] === 'Presentasi' ? 'bg-cyan-50 text-cyan-600' : 'bg-amber-50 text-amber-600');
    
    // format date logic manually since JS used a specific format
    // If you need exact formatting, we can use date_create
    $tanggal = date_create($act['tanggal']);
    $formattedDateStr = date_format($tanggal, 'd M Y');
    
    $html .= '
        <div class="flex gap-3">
            <div class="w-10 h-10 rounded-full ' . $colorClass . ' flex items-center justify-center shrink-0">
                <i class="bi ' . $icon . ' text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-slate-800 text-sm mb-0.5">' . htmlspecialchars($act['judul']) . '</p>
                <span class="text-slate-400 text-xs block mb-0.5">
                    <i class="bi bi-calendar3 me-1"></i>' . $formattedDateStr . '
                </span>
                <span class="text-slate-400 text-xs block">
                    <i class="bi bi-folder me-1"></i>' . htmlspecialchars($act['jenis']) . '
                </span>
            </div>
        </div>';
}
echo $html;
