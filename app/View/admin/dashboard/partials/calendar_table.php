<?php
$calendarWeeks = $calendarWeeks ?? [];

foreach ($calendarWeeks as $week):
?>
<tr>
    <?php foreach ($week as $day): ?>
        <?php if ($day === null): ?>
            <td class="text-gray-800 bg-slate-50/50" style="height: 70px; vertical-align: top; border: 1px solid #f1f5f9; position: relative; padding: 6px;"></td>
        <?php else: ?>
            <?php
                $fontWeight = '500';
                $dateColor = '#64748b'; // slate-500
                $cellBg = 'transparent';
                $cellBorder = '1px solid #f1f5f9'; // slate-100
                $cursor = 'default';
                $onclick = '';
                $eventsHtml = '';
                
                $isToday = $day['isToday'];
                if ($isToday) {
                    $dateColor = '#2563eb'; // blue-600
                    $fontWeight = '700';
                }

                if (count($day['events'])> 0) {
                    $cursor = 'pointer';
                    $escapedEvents = htmlspecialchars(json_encode($day['events']), ENT_QUOTES, 'UTF-8');
                    $onclick = " onclick='showActivityActions(this.getAttribute(\"data-events\"))' data-events=\"$escapedEvents\"";
                    $cellBg = '#ffffff';
                    
                    $eventsHtml .= '<div class="mt-1 space-y-1 flex flex-col items-center">';
                    // Show max 2 events to prevent cell from overflowing too much
                    $displayEvents = array_slice($day['events'], 0, 2);
                    foreach ($displayEvents as $event) {
                        $jenis = $event['jenis'];
                        $bgColor = 'bg-slate-100';
                        $textColor = 'text-slate-700';
                        
                        if ($jenis === 'Kegiatan') {
                            $bgColor = 'bg-blue-100';
                            $textColor = 'text-blue-700';
                        } elseif ($jenis === 'Wawancara') {
                            $bgColor = 'bg-amber-100';
                            $textColor = 'text-amber-700';
                        } elseif ($jenis === 'Presentasi') {
                            $bgColor = 'bg-emerald-100';
                            $textColor = 'text-emerald-700';
                        }
                        
                        // Limit title length
                        $title = htmlspecialchars($event['judul']);
                        if (strlen($title)> 12) {
                            $title = substr($title, 0, 10) . '...';
                        }
                        
                        $eventsHtml .= "<div class=\"text-[10px] w-full text-center px-1 py-0.5 rounded-md font-bold truncate $bgColor $textColor\" title=\"" . htmlspecialchars($event['judul']) . "\">";
                        $eventsHtml .= $title;
                        $eventsHtml .= "</div>";
                    }
                    if (count($day['events'])> 2) {
                        $eventsHtml .= "<div class=\"text-[10px] font-bold text-slate-400\">+" . (count($day['events']) - 2) . " lagi</div>";
                    }
                    $eventsHtml .= '</div>';
                }
                
                $cellStyle = "height: 70px; vertical-align: top; border: $cellBorder; position: relative; padding: 6px; cursor: $cursor; background-color: $cellBg; transition: all 0.2s;";
            ?>
            <td class="calendar-cell hover:bg-slate-50" style="<?= $cellStyle ?>"<?= $onclick ?>>
                <div class="flex justify-center">
                    <div style="font-size: 11px; font-weight: <?= $fontWeight ?>; color: <?= $dateColor ?>; <?= $isToday ? 'background-color: #eff6ff; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; border-radius: 50%;' : 'margin-bottom: 2px;' ?>">
                        <?= $day['date'] ?>
                    </div>
                </div>
                <?= $eventsHtml ?>
            </td>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
