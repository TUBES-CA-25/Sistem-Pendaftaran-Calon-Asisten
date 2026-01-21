<?php
/**
 * Dashboard View - Refactored with Bootstrap 5
 *
 * Data yang diterima dari controller:
 * @var array $notifikasi - Daftar notifikasi user
 * @var int $tahapanSelesai - Jumlah tahapan yang sudah selesai
 * @var int $percentage - Persentase progress
 * @var array $tahapan - Daftar tahapan pendaftaran
 * @var array $jadwalPresentasiUser - Jadwal presentasi user
 * @var array $biodata - Data biodata lengkap
 * @var array $user - Data user (stambuk, username)
 * @var string $photo - Nama file foto user
 * @var array $dokumen - Status dokumen/berkas
 */

$notifikasi = $notifikasi ?? [];
$tahapanSelesai = $tahapanSelesai ?? 0;
$percentage = $percentage ?? 0;
$tahapan = $tahapan ?? [];
$jadwalPresentasiUser = $jadwalPresentasiUser ?? null;
$biodata = $biodata ?? [];
$user = $user ?? [];
$photo = $photo ?? 'default.png';
$dokumen = $dokumen ?? [];
?>

<!-- Page Header -->
<?php
    $title = 'Dashboard';
    $subtitle = 'Selamat datang di IC-ASSIST';
    $icon = 'bx bx-home-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="pb-4 page-content">

    <!-- Greeting Header -->
    <div class="mb-4">
        <h1 class="display-6 fw-bold text-dark mb-1">
            Hello <?= htmlspecialchars($biodata['namaLengkap'] ?? $user['username'] ?? 'User') ?> 👋
        </h1>
        <p class="text-muted mb-0">Let's learn something new today!</p>
    </div>

    <div class="row g-4">
        <!-- Main Content (Left Column) - 8 col -->
        <div class="col-lg-8">

            <!-- Progress Circular Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4">Progress Pendaftaran</h5>

                    <div class="d-flex align-items-center justify-content-center position-relative mb-4" style="height: 220px;">
                        <!-- SVG Circular Progress -->
                        <svg width="200" height="200" class="progress-ring">
                            <circle class="progress-ring-circle-bg"
                                stroke="#e5e7eb"
                                stroke-width="12"
                                fill="transparent"
                                r="85"
                                cx="100"
                                cy="100"/>
                            <circle class="progress-ring-circle"
                                stroke="url(#gradient)"
                                stroke-width="12"
                                fill="transparent"
                                r="85"
                                cx="100"
                                cy="100"
                                style="stroke-dasharray: 534.07; stroke-dashoffset: <?= 534.07 * (1 - $percentage/100) ?>; transform: rotate(-90deg); transform-origin: center;"/>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3dc2ec"/>
                                    <stop offset="100%" style="stop-color:#2563eb"/>
                                </linearGradient>
                            </defs>
                        </svg>

                        <!-- Text di tengah -->
                        <div class="position-absolute text-center">
                            <div class="display-4 fw-bold text-primary"><?= $percentage ?>%</div>
                            <small class="text-muted">Complete</small>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex justify-content-center gap-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary" style="width:12px;height:12px"></div>
                            <small class="text-muted">Terisi</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-light border" style="width:12px;height:12px"></div>
                            <small class="text-muted">Kosong</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Stepper Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">
                        Anda telah menyelesaikan <?= $tahapanSelesai ?> dari 9 tahapan pendaftaran
                    </h5>

                    <!-- Stepper -->
                    <div class="d-flex align-items-center justify-content-between position-relative mb-4">
                        <!-- Progress Line Background -->
                        <div class="position-absolute w-100 bg-light" style="height:4px; top:12px; left:0; z-index:0"></div>
                        <!-- Progress Line Active (calculated dynamically) -->
                        <?php $stepProgress = min(($tahapanSelesai / 9) * 100, 100); ?>
                        <div class="position-absolute bg-primary stepper-line" style="height:4px; top:12px; left:0; width:<?= $stepProgress ?>%; z-index:1"></div>

                        <?php
                        // Define 4 main steps for stepper with corresponding tahapan thresholds
                        $stepperStages = [
                            ['number' => 1, 'color' => 'danger', 'label' => 'Lengkapi Berkas', 'threshold' => 2],  // Tahap 1-2
                            ['number' => 2, 'color' => 'warning', 'label' => 'Tes Tertulis', 'threshold' => 4],    // Tahap 3-4
                            ['number' => 3, 'color' => 'success', 'label' => 'Tahap Wawancara', 'threshold' => 7], // Tahap 5-7
                            ['number' => 4, 'color' => 'primary', 'label' => 'Pengumuman', 'threshold' => 9]       // Tahap 8-9
                        ];

                        foreach ($stepperStages as $step):
                            $isActive = $tahapanSelesai >= $step['threshold'];
                        ?>
                            <div class="text-center position-relative" style="z-index:2">
                                <div class="rounded-circle bg-<?= $step['color'] ?> d-flex align-items-center justify-content-center mx-auto mb-2"
                                     style="width:28px; height:28px">
                                    <?php if ($isActive): ?>
                                        <i class="bi bi-check text-white fw-bold"></i>
                                    <?php else: ?>
                                        <span class="text-white fw-bold small"><?= $step['number'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-<?= $step['color'] ?> fw-semibold d-block"><?= $step['label'] ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex flex-wrap gap-3 small">
                        <?php foreach ($stepperStages as $step): ?>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-<?= $step['color'] ?>" style="width:8px;height:8px"></div>
                                <span class="text-muted"><?= $step['label'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Biodata Diri Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Biodata Diri</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="navigateTo('biodata')">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Nama Lengkap -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-person-fill text-primary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Nama Lengkap</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['namaLengkap'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- NIM -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-success bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-123 text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">NIM</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['stambuk'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-info bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-envelope-fill text-info fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Email</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['email'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Tempat, Tanggal Lahir -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-warning bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-calendar-event text-warning fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Tempat, Tanggal Lahir</small>
                                    <p class="mb-0 fw-semibold">
                                        <?= htmlspecialchars($biodata['tempatLahir'] ?? '-') ?>,
                                        <?= htmlspecialchars($biodata['tanggalLahir'] ?? '-') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-secondary bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-gender-ambiguous text-secondary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Jenis Kelamin</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['jenisKelamin'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Nomor HP -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-danger bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-telephone-fill text-danger fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Nomor HP</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['noHp'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- IPK -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-star-fill text-primary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">IPK</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['ipk'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Program Studi -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-success bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-book-fill text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Program Studi</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['jurusan'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat (Full Width) -->
                        <div class="col-12">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-info bg-opacity-10 p-2 flex-shrink-0">
                                    <i class="bi bi-geo-alt-fill text-info fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Alamat</small>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($biodata['alamat'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar (Right Column) - 4 col -->
        <div class="col-lg-4">

            <!-- Profile Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <!-- Profile Photo -->
                    <div class="mb-3">
                        <?php if (!empty($photo) && $photo !== 'default.png'): ?>
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/<?= htmlspecialchars($photo) ?>"
                                 alt="Profile"
                                 class="rounded-circle border border-3 border-primary"
                                 style="width: 100px; height: 100px; object-fit: cover;"
                                 onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/dummy.jpeg'">
                        <?php else: ?>
                            <!-- Default Avatar with Initials -->
                            <div class="rounded-circle border border-3 border-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width: 100px; height: 100px; background: linear-gradient(135deg, #3dc2ec 0%, #2563eb 100%); font-size: 2.5rem;">
                                <?php
                                $nama = $biodata['namaLengkap'] ?? $user['username'] ?? 'U';
                                $words = explode(' ', $nama);
                                $initials = '';
                                if (count($words) >= 2) {
                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($nama, 0, 2));
                                }
                                echo $initials;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Name & Title -->
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($biodata['namaLengkap'] ?? $user['username'] ?? 'User') ?></h5>
                    <p class="text-muted mb-3 small">Calon Asisten Lab</p>

                    <!-- Edit Button -->
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="navigateTo('biodata')">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </button>
                </div>
            </div>

            <!-- Calendar Widget -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">Calendar</h6>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="card-body p-4">
                    <!-- Calendar Header -->
                    <div class="text-center mb-3">
                        <p class="fw-semibold mb-0" id="calendar-month-year">
                            <?= date('F Y') ?>
                        </p>
                    </div>

                    <!-- Day headers -->
                    <div class="d-flex justify-content-between mb-2">
                        <?php
                        $days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                        foreach($days as $day):
                        ?>
                            <div class="text-center text-muted small fw-semibold" style="width:14.28%">
                                <?= $day ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Calendar dates (will be populated by JavaScript) -->
                    <div class="calendar-grid" id="calendar-dates">
                        <!-- Dates will be generated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">Upcoming</h6>
                    <a href="javascript:void(0)" onclick="navigateTo('dashboard')" class="text-primary text-decoration-none small fw-semibold">View All</a>
                </div>
                <div class="card-body p-4">
                    <?php if ($jadwalPresentasiUser): ?>
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:48px; height:48px">
                                <i class="bi bi-calendar-event text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-semibold"><?= htmlspecialchars($jadwalPresentasiUser['judul'] ?? 'Presentasi') ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?php
                                    if (isset($jadwalPresentasiUser['tanggal'])) {
                                        $timestamp = strtotime($jadwalPresentasiUser['tanggal']);
                                        echo $timestamp ? date('d F Y', $timestamp) : '-';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php
                                    if (isset($jadwalPresentasiUser['waktu'])) {
                                        $timestamp = strtotime($jadwalPresentasiUser['waktu']);
                                        echo $timestamp ? date('H:i', $timestamp) : '-';
                                    } else {
                                        echo '-';
                                    }
                                    ?> WIB
                                </small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted small mb-0">No upcoming events</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Bootstrap Message Modal -->
<div class="modal fade" id="customMessageModal" tabindex="-1" aria-labelledby="customMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-gradient-primary text-white border-0 py-3">
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 32px; height: 32px; background: var(--bs-gradient);">
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

<!-- Load Dashboard JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/dashboard.js"></script>
