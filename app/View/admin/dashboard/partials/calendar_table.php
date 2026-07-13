<?php
$year = $year ?? (int)date('Y');
$month = $month ?? (int)date('n');
$eventsData = $eventsData ?? [];

$firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
$daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
$adjustedStart = $firstDay == 0 ? 6 : $firstDay - 1; // Mon=0, Sun=6

$todayYear = (int)date('Y');
$todayMonth = (int)date('n');
$todayDate = (int)date('j');

$html = '';
$date = 1;

for ($i = 0; $i < 6; $i++) {
    $rowHtml = '<tr>';
    $hasDateInRow = false;
    
    for ($j = 0; $j < 7; $j++) {
        $cellHtml = '<td class="text-dark" style="height: 70px; vertical-align: middle; border: 1px solid #E5E7EB; position: relative; padding: 12px;">';
        
        if ($i === 0 && $j < $adjustedStart) {
            // Empty
        } else if ($date > $daysInMonth) {
            // Empty
        } else {
            $hasDateInRow = true;
            $isToday = ($date === $todayDate && $month === $todayMonth && $year === $todayYear);
            $formattedDate = sprintf('%04d-%02d-%02d', $year, $month, $date);
            
            $daysEvents = array_filter($eventsData, function($e) use ($formattedDate) {
                return $e['tanggal'] === $formattedDate;
            });
            
            // Create date span string
            $fontWeight = '500';
            $dateColor = '#1F2937';
            $cellBg = 'transparent';
            $cellBorder = '1px solid #E5E7EB';
            $cellRadius = '0';
            $cursor = 'default';
            $onclick = '';
            $dotHtml = '';
            
            if (count($daysEvents) > 0) {
                $cursor = 'pointer';
                $escapedEvents = htmlspecialchars(json_encode(array_values($daysEvents)), ENT_QUOTES, 'UTF-8');
                $onclick = " onclick='showActivityActions(this.getAttribute(\"data-events\"))' data-events=\"$escapedEvents\"";
                $cellBg = '#E0E7FF';
                $cellRadius = '8px';
                $fontWeight = '600';
                $dotHtml = '<div style="width: 6px; height: 6px; background-color: #DC2626; border-radius: 50%; margin: 4px auto 0;"></div>';
            } else if ($isToday) {
                // Inline border because we need to override the 1px td border
                // We'll wrap the inner content instead if we want round borders
                // Actually the JS applied it directly to td
                $cellBorder = '2px solid #2563EB';
                $cellRadius = '8px';
                $fontWeight = '700';
            }
            
            // Reconstruct the cell styling with overrides if needed
            $cellStyle = "height: 70px; vertical-align: middle; border: $cellBorder; position: relative; padding: 12px; cursor: $cursor; background-color: $cellBg; border-radius: $cellRadius;";
            $cellHtml = "<td class=\"text-dark\" style=\"$cellStyle\"$onclick>";
            $cellHtml .= "<div style=\"font-size: 14px; font-weight: $fontWeight; color: $dateColor;\">$date</div>";
            $cellHtml .= $dotHtml;
            
            $date++;
        }
        
        $cellHtml .= '</td>';
        $rowHtml .= $cellHtml;
    }
    $rowHtml .= '</tr>';
    
    if ($hasDateInRow || $i === 0) {
        $html .= $rowHtml;
    }
    if ($date > $daysInMonth) break;
}

echo $html;
