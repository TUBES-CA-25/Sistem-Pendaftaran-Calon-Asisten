<?php
// Menerima variabel $year, $month, $activities
$firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
$daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
$todayYear = (int)date('Y');
$todayMonth = (int)date('n'); // 1-12
$todayDate = (int)date('j');

$html = '';

// Empty cells before first day of month
for ($i = 0; $i < $firstDay; $i++) {
    $html .= '<div class="flex flex-col items-center justify-center text-sm rounded-lg relative aspect-square transition-all duration-200 opacity-30"></div>';
}

// Days of the month
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
    
    $baseClasses = 'flex flex-col items-center justify-center text-sm rounded-lg relative aspect-square transition-all duration-200';
    $stateClasses = $isToday ? 'bg-blue-100 text-blue-600 font-bold' : '';
    $interactiveClasses = $hasActivity ? 'cursor-pointer hover:bg-slate-100 hover:scale-105' : '';
    
    $classes = trim("$baseClasses $stateClasses $interactiveClasses");
    
    $activityDots = '';
    if ($hasActivity) {
        $activityDots = '<div class="flex justify-center gap-[2px] mt-[2px]">';
        $limitedActivities = array_slice(array_values($dayActivities), 0, 3);
        foreach ($limitedActivities as $act) {
            $colorClass = $act['jenis'] === 'Wawancara' ? 'bg-blue-600' : ($act['jenis'] === 'Presentasi' ? 'bg-cyan-600' : 'bg-amber-500');
            $activityDots .= '<span class="w-1 h-1 rounded-full ' . $colorClass . '"></span>';
        }
        $activityDots .= '</div>';
    }
    
    $html .= '
        <div class="' . $classes . '" 
             data-date="' . $formattedDate . '" 
             onclick="showDayDetails(\'' . $formattedDate . '\')"
             data-bs-toggle="tooltip"
             data-bs-html="true"
             data-bs-title="' . htmlspecialchars(str_replace("\n", "<br>", $tooltiptxt), ENT_QUOTES) . '">
            <span class="date-num">' . $day . '</span>
            ' . $activityDots . '
        </div>';
}

// Fill remaining cells
$totalCells = $firstDay + $daysInMonth;
$remainingCells = 7 - ($totalCells % 7);
if ($remainingCells < 7) {
    for ($i = 0; $i < $remainingCells; $i++) {
        $html .= '<div class="flex flex-col items-center justify-center text-sm rounded-lg relative aspect-square transition-all duration-200 opacity-30"></div>';
    }
}

echo $html;
