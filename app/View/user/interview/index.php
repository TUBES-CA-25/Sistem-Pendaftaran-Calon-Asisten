<?php
/**
 * Wawancara View
 *
 * Data yang diterima dari controller:
 * @var array $wawancara - Data jadwal wawancara
 */
$wawancara = $wawancara ?? [];
?>

<!-- Page Header -->
<?php
    $title = 'Jadwal Kegiatan';
    $subtitle = 'Informasi jadwal wawancara dan kegiatan';
    $icon = 'bx bx-user-voice';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="pb-4" style="margin-top: -30px; position: relative; z-index: 10;">

    <!-- Schedule Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 p-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-calendar-event me-2 text-primary"></i>Jadwal Kegiatan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Jenis Kegiatan</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($wawancara)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                                    <span class="text-muted">Belum ada jadwal kegiatan</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; ?>
                            <?php foreach ($wawancara as $value): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark"><?= $i ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php 
                                            $icon = 'bi-calendar-event';
                                            $color = 'text-primary';
                                            if (isset($value['jenis'])) {
                                                if ($value['jenis'] === 'Wawancara') {
                                                    $icon = 'bi-people';
                                                    $color = 'text-success';
                                                } elseif ($value['jenis'] === 'Presentasi') {
                                                    $icon = 'bi-display';
                                                    $color = 'text-info';
                                                }
                                            }
                                            ?>
                                            <i class="bi <?= $icon ?> <?= $color ?>"></i>
                                            <span class="fw-medium"><?= htmlspecialchars($value['judul'] ?? '-') ?></span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary small ms-1" style="font-size: 0.65rem;">
                                                <?= htmlspecialchars($value['jenis'] ?? 'Kegiatan') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="bi bi-geo-alt text-primary"></i>
                                            <?= htmlspecialchars($value['ruangan'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar3 text-muted"></i>
                                            <?= htmlspecialchars($value['tanggal'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-clock me-1"></i><?= $value['waktu'] !== '00:00:00' ? htmlspecialchars($value['waktu']) : 'Full Day' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
