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
    $isTes = $act['jenis'] === 'Tes Tertulis';
    $isWawancara = $act['jenis'] === 'Wawancara';
    $isPresentasi = $act['jenis'] === 'Presentasi';
    
    $icon = $isWawancara ? 'bi-people' : ($isPresentasi ? 'bi-display' : ($isTes ? 'bi-pencil-square' : 'bi-calendar-event'));
    $colorClass = $isWawancara ? 'bg-blue-50 text-blue-600' : ($isPresentasi ? 'bg-cyan-50 text-cyan-600' : ($isTes ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600'));
    
    // format date logic manually since JS used a specific format
    // If you need exact formatting, we can use date_create
    $tanggal = date_create($act['tanggal']);
    $formattedDateStr = date_format($tanggal, 'd M Y');
    
    // Tanggal & jenis berdampingan (bukan bertumpuk) karena kartunya lebar.
    $html .= '
        <div class="group flex items-center gap-3 p-3 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl ' . $colorClass . ' flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-105">
                <i class="bi ' . $icon . ' text-lg"></i>
            </div>
            <div class="min-w-0 flex-grow">
                <p class="font-bold text-slate-800 text-[13px] mb-1 truncate transition-colors group-hover:text-blue-600" title="' . htmlspecialchars($act['judul']) . '">' . htmlspecialchars($act['judul']) . '</p>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5">
                    <span class="inline-flex items-center gap-1 text-slate-500 text-[11px] font-medium">
                        <i class="bi bi-calendar3"></i>' . $formattedDateStr . '
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded ' . $colorClass . '">
                        ' . htmlspecialchars($act['jenis']) . '
                    </span>
                </div>
            </div>
        </div>';
}
// space-y-2: jarak antar item bila ada lebih dari satu kegiatan.
echo '<div class="space-y-2.5">' . $html . '</div>';
