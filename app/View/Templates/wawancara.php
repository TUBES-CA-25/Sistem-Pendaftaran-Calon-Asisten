<?php
  use app\Controllers\user\WawancaraController;
  $wawancara = WawancaraController::getAllById() ?? [];
?>

<link rel="stylesheet" href="<?= APP_URL ?>/Style/wawancara.css">

<main class="wawancara-container">
    
    <div class="page-header">
        <h1>Jadwal Kegiatan</h1>
        <p>Informasi jadwal tes, wawancara, dan presentasi Anda.</p>
    </div>

    <div class="schedule-card">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Jenis Kegiatan</th>
                        <th width="30%">Lokasi / Ruangan</th>
                        <th width="25%">Tanggal</th>
                        <th width="15%">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wawancara)) : ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="empty-content">
                                    <i class='bx bx-calendar-x'></i>
                                    <p>Belum ada jadwal kegiatan saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: 
                        $i = 1;
                        foreach ($wawancara as $value) : 
                            // Formatting Data
                            $jenis = htmlspecialchars($value['jenis_wawancara'] ?? '-');
                            $lokasi = htmlspecialchars($value['ruangan'] ?? '-');
                            
                            // Format Tanggal (Contoh: 12 Jan 2026)
                            $rawDate = $value['tanggal'] ?? null;
                            $dateDisplay = $rawDate ? date('d M Y', strtotime($rawDate)) : '-';
                            
                            // Format Waktu (Hapus detik, jadi 09:00)
                            $rawTime = $value['waktu'] ?? null;
                            $timeDisplay = $rawTime ? date('H:i', strtotime($rawTime)) . ' WITA' : '-';

                            // Badge Color Logic
                            $badgeClass = (stripos($jenis, 'Tes') !== false) ? 'badge-test' : 'badge-interview';
                    ?>
                        <tr>
                            <td><span class="row-number"><?= $i++ ?></span></td>
                            <td>
                                <span class="activity-badge <?= $badgeClass ?>">
                                    <?= $jenis ?>
                                </span>
                            </td>
                            <td class="location-cell">
                                <i class='bx bx-map'></i> <?= $lokasi ?>
                            </td>
                            <td class="date-cell">
                                <i class='bx bx-calendar'></i> <?= $dateDisplay ?>
                            </td>
                            <td class="time-cell">
                                <i class='bx bx-time'></i> <?= $timeDisplay ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>