<?php
// Menerima variabel $year, $month, $activities
$firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
$daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
$todayYear = (int)date('Y');
$todayMonth = (int)date('n'); // 1-12
$todayDate = (int)date('j');

/* Warna titik penanda per jenis kegiatan. Ditulis sebagai pasangan kelas
   LITERAL UTUH (bukan dirakit) karena Tailwind Play CDN memindai nama kelas
   sebagai teks di sumber - kelas hasil rakitan tidak akan dikompilasi. */
$warnaTitik = [
    'Wawancara'    => 'bg-blue-500',
    'Presentasi'   => 'bg-cyan-500',
    'Tes Tertulis' => 'bg-indigo-500',
];
$warnaTitikDefault = 'bg-amber-500';

$html = '';

// Sel kosong sebelum tanggal 1
for ($i = 0; $i < $firstDay; $i++) {
    $html .= '<div class="aspect-square"></div>';
}

// Tanggal dalam bulan
for ($day = 1; $day <= $daysInMonth; $day++) {
    $isToday = ($day === $todayDate && $month === $todayMonth && $year === $todayYear);
    $formattedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

    $dayActivities = array_filter($activities, function($act) use ($formattedDate) {
        return $act['tanggal'] === $formattedDate;
    });

    $hasActivity = count($dayActivities) > 0;

    $tooltiptxt = '';
    if ($hasActivity) {
        $tooltipLines = [];
        foreach ($dayActivities as $act) {
            $tooltipLines[] = $act['jenis'] . ': ' . $act['judul'];
        }
        $tooltiptxt = implode("\n", $tooltipLines);
    }

    /* Tiga keadaan sel ditulis sebagai string kelas literal utuh:
       - hari ini      : lingkaran gradasi biru penuh (seragam dengan navbar)
       - ada kegiatan  : latar biru lembut + cincin, menandakan bisa diklik
       - biasa         : polos, hanya berubah saat hover */
    if ($isToday) {
        $stateClasses = 'bg-gradient-to-br from-primary to-secondary text-white font-bold shadow-md shadow-blue-500/30';
    } elseif ($hasActivity) {
        $stateClasses = 'bg-blue-50/70 text-slate-700 font-bold ring-1 ring-blue-100 hover:ring-blue-300 hover:bg-blue-50';
    } else {
        $stateClasses = 'text-slate-500 font-medium hover:bg-slate-100';
    }

    $interactiveClasses = $hasActivity || $isToday ? 'cursor-pointer hover:-translate-y-0.5' : '';

    $classes = trim(
        'group relative flex flex-col items-center justify-center aspect-square rounded-xl '
      . 'text-[13px] leading-none transition-all duration-200 '
      . $stateClasses . ' ' . $interactiveClasses
    );

    /* Titik penanda kegiatan. Pada sel "hari ini" latarnya sudah biru penuh,
       jadi titiknya dibuat putih agar tetap terlihat. */
    $activityDots = '';
    if ($hasActivity) {
        $activityDots = '<div class="flex justify-center gap-[3px] mt-1 absolute bottom-1.5 left-0 right-0">';
        $limitedActivities = array_slice(array_values($dayActivities), 0, 3);
        foreach ($limitedActivities as $act) {
            $colorClass = $isToday
                ? 'bg-white'
                : ($warnaTitik[$act['jenis']] ?? $warnaTitikDefault);
            $activityDots .= '<span class="w-1.5 h-1.5 rounded-full ' . $colorClass . '"></span>';
        }
        $activityDots .= '</div>';
    }

    // Tooltip murni CSS (group-hover). Sebelumnya memakai data-bs-toggle="tooltip"
    // yang tidak pernah ada implementasinya, sehingga tooltip tidak pernah muncul.
    $tooltipHtml = '';
    if (trim($tooltiptxt) !== '') {
        $tooltipHtml =
            '<span role="tooltip" class="pointer-events-none absolute bottom-full left-1/2 z-30 mb-2 '
          . '-translate-x-1/2 whitespace-nowrap rounded-lg bg-slate-800 px-2.5 py-1.5 text-[11px] '
          . 'font-medium leading-snug text-white opacity-0 shadow-lg transition-opacity duration-150 '
          . 'group-hover:opacity-100">'
          . nl2br(htmlspecialchars($tooltiptxt, ENT_QUOTES))
          . '</span>';
    }

    $html .= '
        <div class="' . $classes . '"
             data-date="' . $formattedDate . '"
             onclick="showDayDetails(\'' . $formattedDate . '\')">
            <span class="date-num">' . $day . '</span>
            ' . $activityDots . $tooltipHtml . '
        </div>';
}

/* Isi sampai GENAP 6 BARIS (42 sel), bukan hanya sampai akhir baris terakhir.

   Jumlah baris sebuah bulan bervariasi 4-6 tergantung tanggal 1 jatuh di hari
   apa (mis. Februari 2026 = 4 baris, Agustus 2026 = 6 baris). Kalau hanya
   diisi sampai baris terakhir, tinggi kalender ikut berubah tiap ganti bulan
   sehingga frame kartunya "melompat". Dengan selalu 42 sel, tingginya tetap. */
$totalCells = $firstDay + $daysInMonth;
$targetCells = 42; // 6 baris x 7 hari
for ($i = $totalCells; $i < $targetCells; $i++) {
    $html .= '<div class="aspect-square"></div>';
}

echo $html;
