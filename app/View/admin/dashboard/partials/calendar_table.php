<?php
$calendarWeeks = $calendarWeeks ?? [];

foreach ($calendarWeeks as $week):
?>
<tr>
    <?php foreach ($week as $day): ?>
        <?php if ($day === null): ?>
            <td class="text-dark" style="height: 70px; vertical-align: middle; border: 1px solid #E5E7EB; position: relative; padding: 12px;"></td>
        <?php else: ?>
            <?php
                $fontWeight = '500';
                $dateColor = '#1F2937';
                $cellBg = 'transparent';
                $cellBorder = '1px solid #E5E7EB';
                $cellRadius = '0';
                $cursor = 'default';
                $onclick = '';
                $dotHtml = '';
                
                if (count($day['events']) > 0) {
                    $cursor = 'pointer';
                    $escapedEvents = htmlspecialchars(json_encode($day['events']), ENT_QUOTES, 'UTF-8');
                    $onclick = " onclick='showActivityActions(this.getAttribute(\"data-events\"))' data-events=\"$escapedEvents\"";
                    $cellBg = '#E0E7FF';
                    $cellRadius = '8px';
                    $fontWeight = '600';
                    $dotHtml = '<div style="width: 6px; height: 6px; background-color: #DC2626; border-radius: 50%; margin: 4px auto 0;"></div>';
                } else if ($day['isToday']) {
                    $cellBorder = '2px solid #2563EB';
                    $cellRadius = '8px';
                    $fontWeight = '700';
                }
                
                $cellStyle = "height: 70px; vertical-align: middle; border: $cellBorder; position: relative; padding: 12px; cursor: $cursor; background-color: $cellBg; border-radius: $cellRadius;";
            ?>
            <td class="text-dark" style="<?= $cellStyle ?>"<?= $onclick ?>>
                <div style="font-size: 14px; font-weight: <?= $fontWeight ?>; color: <?= $dateColor ?>;"><?= $day['date'] ?></div>
                <?= $dotHtml ?>
            </td>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
