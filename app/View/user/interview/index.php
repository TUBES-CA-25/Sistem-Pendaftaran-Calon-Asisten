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

<main class="container-fluid px-4 pb-4">

    <!-- Schedule Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-calendar-event me-2 text-primary"></i>Jadwal Kegiatan
            </h5>
            <div class="position-relative" style="width: 250px; max-width: 100%;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchSchedule" class="form-control ps-5 rounded-3 bg-light border-0" placeholder="Cari nama peserta...">
            </div>
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
                                        <div class="d-flex flex-column gap-1">
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
                                                    } elseif ($value['jenis'] === 'Ujian Tertulis') {
                                                        $icon = 'bi-clipboard-check';
                                                        $color = 'text-danger';
                                                    }
                                                }
                                                ?>
                                                <i class="bi <?= $icon ?> <?= $color ?>"></i>
                                                <span class="fw-bold"><?= htmlspecialchars($value['judul'] ?? '-') ?></span>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary small ms-1" style="font-size: 0.65rem;">
                                                    <?= htmlspecialchars($value['jenis'] ?? 'Kegiatan') ?>
                                                </span>
                                                <?php if (isset($value['is_mine']) && $value['is_mine']): ?>
                                                    <span class="badge bg-primary rounded-pill px-2 py-1 ms-2" style="font-size: 0.65rem;">
                                                        <i class="bi bi-person-check-fill me-1"></i>Milik Anda
                                                    </span>

                                                    <?php if (isset($value['status_kehadiran'])): ?>
                                                        <?php if ($value['status_kehadiran'] === 'Hadir'): ?>
                                                            <span class="badge bg-success rounded-pill px-2 py-1 ms-1" style="font-size: 0.65rem;">
                                                                <i class="bi bi-check-circle-fill me-1"></i>Selesai
                                                            </span>
                                                        <?php elseif ($value['status_kehadiran'] === 'Alpha'): ?>
                                                            <span class="badge bg-danger rounded-pill px-2 py-1 ms-1" style="font-size: 0.65rem;">
                                                                <i class="bi bi-x-circle-fill me-1"></i>Alpha
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted ms-4">
                                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?>
                                            </div>
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

<script>
$(document).ready(function() {
    // Search functionality
    $('#searchSchedule').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
        
        // Handle "No results found" if all rows are hidden
        var visibleRows = $("table tbody tr:not(#noResultsRow):visible").length;
        if (visibleRows === 0) {
            if ($('#noResultsRow').length === 0) {
                $("table tbody").append(`
                    <tr id="noResultsRow">
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                            <span class="text-muted">Data yang Anda cari tidak ditemukan</span>
                        </td>
                    </tr>
                `);
            }
        } else {
            $('#noResultsRow').remove();
        }
    });
});
</script>
