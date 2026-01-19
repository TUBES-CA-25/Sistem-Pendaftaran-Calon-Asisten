<?php
/**
 * Dashboard View
 *
 * Data yang diterima dari controller:
 * @var array $notifikasi - Daftar notifikasi user
 * @var int $tahapanSelesai - Jumlah tahapan yang sudah selesai
 * @var int $percentage - Persentase progress
 * @var array $tahapan - Daftar tahapan pendaftaran
 * @var array $jadwalPresentasiUser - Jadwal presentasi user
 */

$notifikasi = $notifikasi ?? [];
$tahapanSelesai = $tahapanSelesai ?? 0;
$percentage = $percentage ?? 0;
$tahapan = $tahapan ?? [];
$jadwalPresentasiUser = $jadwalPresentasiUser ?? null;
?>

<!-- Page Header -->
<?php
    $title = 'Dashboard';
    $subtitle = 'Selamat datang di IC-ASSIST';
    $icon = 'bx bx-home-circle';
    require_once __DIR__ . '/../templates/components/PageHeader.php';
?>

<main class="pb-4" style="margin-top: -30px; position: relative; z-index: 10;">

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Progress Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="stat-icon stat-icon-primary">
                            <span class="material-symbols-outlined fs-2">browse_activity</span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Tahap yang telah diselesaikan</p>
                            <h2 class="fw-bold mb-2"><?= $tahapanSelesai ?></h2>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted mt-2 d-block"><?= $percentage ?>% selesai</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Presentasi Card -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-4 h-100 text-white overflow-hidden position-relative" style="background: var(--gradient-primary);">
                <!-- Decorative Circle -->
                <div class="position-absolute" style="top: -50%; right: -20%; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bx bx-calendar-event fs-4"></i>
                        <h5 class="fw-semibold mb-0">Jadwal Presentasi Anda</h5>
                    </div>

                    <?php if ($jadwalPresentasiUser): ?>
                        <?php
                            $tanggal = new DateTime($jadwalPresentasiUser['tanggal']);
                            $formattedDate = $tanggal->format('d F Y');
                            $waktu = date('H:i', strtotime($jadwalPresentasiUser['waktu']));
                        ?>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom border-white border-opacity-25">
                                <i class="bx bx-calendar"></i>
                                <span class="small opacity-75" style="min-width: 70px;">Tanggal</span>
                                <span class="fw-semibold"><?= $formattedDate ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom border-white border-opacity-25">
                                <i class="bx bx-time"></i>
                                <span class="small opacity-75" style="min-width: 70px;">Waktu</span>
                                <span class="fw-semibold"><?= $waktu ?> WIB</span>
                            </div>
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom border-white border-opacity-25">
                                <i class="bx bx-building"></i>
                                <span class="small opacity-75" style="min-width: 70px;">Ruangan</span>
                                <span class="fw-semibold"><?= htmlspecialchars($jadwalPresentasiUser['ruangan']) ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-3 py-2">
                                <i class="bx bx-book"></i>
                                <span class="small opacity-75" style="min-width: 70px;">Judul</span>
                                <span class="fw-semibold"><?= htmlspecialchars(mb_strimwidth($jadwalPresentasiUser['judul'], 0, 40, '...')) ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bx bx-calendar-x fs-1 opacity-75 mb-2 d-block"></i>
                            <p class="mb-0 opacity-90">Jadwal presentasi belum ditentukan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <!-- Tahapan Pendaftaran Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">Tahapan Pendaftaran Calon Asisten ICLABS 2024</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-primary-dark">No</th>
                                    <th class="text-primary-dark">Tahapan</th>
                                    <th class="text-primary-dark">Status</th>
                                    <th class="text-primary-dark">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tahapan as $tahap):
                                    $status = $tahap[2];
                                ?>
                                <tr>
                                    <td class="ps-4"><?= $tahap[0] ?></td>
                                    <td><?= $tahap[1] ?></td>
                                    <td>
                                        <?php if ($status): ?>
                                            <span class="badge badge-success-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i>Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-warning-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-clock-fill me-1"></i>Belum
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        Anda <?= $status ? "telah menyelesaikan" : "belum menyelesaikan" ?> <?= $tahap[3] ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Notification</h5>
                    <span class="material-symbols-outlined text-primary">inbox</span>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="p-3 rounded-3 mb-3" style="background: #f8fafc;">
                        <p class="mb-0 small">
                            <strong>Tim Iclabs</strong> selamat kamu telah berhasil mendaftar di web IC-ASSIST
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary w-100 rounded-3" id="viewMessageButton" data-bs-toggle="modal" data-bs-target="#customMessageModal">
                        <i class="bi bi-envelope me-2"></i>Lihat Pesan
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Message Modal -->
<div class="modal fade" id="customMessageModal" tabindex="-1" aria-labelledby="customMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header modal-header-gradient border-0 py-3">
                <h5 class="modal-title" id="customMessageModalLabel">
                    <i class="bi bi-envelope-fill me-2"></i>Pesan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-column gap-3">
                    <?php if (empty($notifikasi)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Tidak ada pesan</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifikasi as $notif): ?>
                            <div class="p-3 rounded-3 border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--gradient-primary);">
                                        <i class="bi bi-person-fill text-white small"></i>
                                    </div>
                                    <strong>Tim Iclabs</strong>
                                </div>
                                <p class="mb-2"><?= htmlspecialchars($notif['pesan']) ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i><?= $notif['created_at'] ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
