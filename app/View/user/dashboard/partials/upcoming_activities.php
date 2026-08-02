<?php
$today = date('Y-m-d');
$upcoming = array_filter($activities, function($act) use ($today) {
    return $act['tanggal']>= $today;
});

usort($upcoming, function($a, $b) {
    return strcmp($a['tanggal'], $b['tanggal']);
});

// Daftar ini menempati panel kanan kartu "Kalender Kegiatan" (setengah dari
// kolom 2/3), jadi muat beberapa kegiatan sekaligus.
$upcomingFuture = array_slice($upcoming, 0, 3);

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
    
    // Tanggal & jenis berdampingan (bukan bertumpuk) karena kartunya lebar.
    $html .= '
        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/60 border border-slate-100">
            <div class="w-10 h-10 rounded-full ' . $colorClass . ' flex items-center justify-center shrink-0">
                <i class="bi ' . $icon . ' text-lg"></i>
            </div>
            <div class="min-w-0 flex-grow">
                <p class="font-semibold text-slate-800 text-sm mb-0.5 truncate">' . htmlspecialchars($act['judul']) . '</p>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-0.5">
                    <span class="text-slate-400 text-xs">
                        <i class="bi bi-calendar3 me-1"></i>' . $formattedDateStr . '
                    </span>
                    <span class="text-slate-400 text-xs">
                        <i class="bi bi-folder me-1"></i>' . htmlspecialchars($act['jenis']) . '
                    </span>
                </div>
            </div>
        </div>';
}
// space-y-2: jarak antar item bila ada lebih dari satu kegiatan.
echo '<div class="space-y-2">' . $html . '</div>';
