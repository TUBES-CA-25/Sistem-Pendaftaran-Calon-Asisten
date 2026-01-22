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

<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }
    .calendar-date {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        border-radius: 10px;
        position: relative;
    }
    .calendar-date.other-month {
        opacity: 0.3;
    }
    .calendar-date.today {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-weight: 700;
    }
    .calendar-date.has-activity {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .calendar-date.has-activity:hover {
        background: rgba(0,0,0,0.02);
        transform: scale(1.05);
    }
    .activity-dots {
        display: flex;
        justify-content: center;
        gap: 2px;
        margin-top: 2px;
    }
    .dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
    }
    .last-child-no-border:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>

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

            <?php if ($isPengumumanOpen): ?>
                <!-- Graduation Announcement Card (Visible when open) -->
                <div class="card border-0 shadow rounded-4 mb-4 overflow-hidden position-relative" 
                     style="background: <?= $graduationStatus === 'Lulus' ? 'linear-gradient(135deg, #22c55e, #16a34a)' : 'linear-gradient(135deg, #ef4444, #dc2626)' ?>; color: white;">
                    <div class="card-body p-4 p-md-5 text-center position-relative" style="z-index: 2;">
                        <div class="mb-3">
                            <i class="bi bi-patch-check-fill display-1" style="opacity: 0.9;"></i>
                        </div>
                        <h2 class="fw-bold mb-2">
                            <?= $graduationStatus === 'Lulus' ? 'Selamat, Anda LULUS!' : 'Mohon Maaf, Anda Belum Lulus.' ?>
                        </h2>
                        <p class="lead mb-4" style="opacity: 0.9;">
                            <?= $graduationStatus === 'Lulus' 
                                ? 'Anda telah berhasil melewati seluruh tahapan seleksi calon asisten laboratorium. Silakan cek informasi selanjutnya.' 
                                : 'Terima kasih telah berpartisipasi dalam proses seleksi. Tetap semangat dan coba lagi di kesempatan berikutnya.' ?>
                        </p>
                        <?php if ($graduationStatus === 'Lulus'): ?>
                            <button class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
                                <i class="bi bi-info-circle me-2"></i>Informasi Lanjutan
                            </button>
                        <?php endif; ?>
                    </div>
                    <!-- Decorative Circles (Bubbles) -->
                    <div class="position-absolute rounded-circle bg-white opacity-10" style="width: 200px; height: 200px; top: -50px; right: -50px;"></div>
                    <div class="position-absolute rounded-circle bg-white opacity-10" style="width: 100px; height: 100px; bottom: -20px; left: 10%;"></div>
                </div>
            <?php else: ?>
                <!-- Announcement Coming Soon Card (Visible when closed) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                            <i class="bi bi-bell-fill text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-primary">Hasil Seleksi Sedang Diproses</h6>
                            <p class="small text-muted mb-0">Pengumuman kelulusan akan ditampilkan di sini setelah seluruh tahapan seleksi berakhir. Tetap pantau!</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Progress & Stepper Row -->
            <div class="row g-4 mb-4">
                <!-- Progress Circular Card -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <h6 class="fw-semibold mb-3 text-center">Progress Pendaftaran</h6>

                            <div class="d-flex align-items-center justify-content-center position-relative mb-3" style="height: 160px;">
                                <!-- SVG Circular Progress (Scaled down for tighter layout) -->
                                <svg width="150" height="150" class="progress-ring">
                                    <defs>
                                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#3dc2ec" />
                                            <stop offset="100%" stop-color="#2563eb" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-ring-circle-bg"
                                        stroke="#e5e7eb"
                                        stroke-width="10"
                                        fill="transparent"
                                        r="65"
                                        cx="75"
                                        cy="75"/>
                                    <circle class="progress-ring-circle"
                                        stroke="url(#gradient)"
                                        stroke-width="10"
                                        fill="transparent"
                                        r="65"
                                        cx="75"
                                        cy="75"
                                        style="stroke-dasharray: 408.41; stroke-dashoffset: <?= 408.41 * (1 - $percentage/100) ?>; transform: rotate(-90deg); transform-origin: center;"/>
                                </svg>

                                <!-- Text di tengah -->
                                <div class="position-absolute text-center">
                                    <div class="h3 fw-bold text-primary mb-0"><?= $percentage ?>%</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">Complete</small>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="d-flex justify-content-center gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary" style="width:8px;height:8px"></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Terisi</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light border" style="width:8px;height:8px"></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Kosong</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Stepper Card -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold mb-4">Status Pendaftaran</h6>
                            
                            <p class="small text-muted mb-4 lh-sm">
                                Anda telah menyelesaikan <strong><?= $tahapanSelesai ?></strong> dari 9 tahapan pendaftaran.
                            </p>

                            <!-- Stepper Vertical layout or cramped horizontal? 
                                 Let's stick to horizontal but with better spacing for side-by-side -->
                            <div class="d-flex align-items-center justify-content-between position-relative mb-4 mt-2 px-1">
                                <!-- Progress Line Background -->
                                <div class="position-absolute w-100 bg-light" style="height:3px; top:10px; left:0; z-index:0"></div>
                                <!-- Progress Line Active -->
                                <?php $stepProgress = min(($tahapanSelesai / 9) * 100, 100); ?>
                                <div class="position-absolute bg-primary stepper-line" style="height:3px; top:10px; left:0; width:<?= $stepProgress ?>%; z-index:1; transition: width 1s ease;"></div>

                                <?php
                                $stepperStages = [
                                    ['number' => 1, 'color' => 'danger', 'label' => 'Berkas', 'threshold' => 2],
                                    ['number' => 2, 'color' => 'warning', 'label' => 'Tes', 'threshold' => 4],
                                    ['number' => 3, 'color' => 'success', 'label' => 'Wawancara', 'threshold' => 7],
                                    ['number' => 4, 'color' => 'primary', 'label' => 'Final', 'threshold' => 9]
                                ];

                                foreach ($stepperStages as $step):
                                    $isActive = $tahapanSelesai >= $step['threshold'];
                                ?>
                                    <div class="text-center position-relative" style="z-index:2">
                                        <div class="rounded-circle bg-<?= $isActive ? $step['color'] : 'light' ?> <?= $isActive ? '' : 'border' ?> d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm"
                                             style="width:22px; height:22px">
                                            <?php if ($isActive): ?>
                                                <i class="bi bi-check text-white fw-bold" style="font-size: 0.7rem;"></i>
                                            <?php else: ?>
                                                <span class="text-muted fw-bold" style="font-size: 0.6rem;"><?= $step['number'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="fw-bold d-block text-<?= $isActive ? $step['color'] : 'muted' ?>" style="font-size: 0.6rem;"><?= $step['label'] ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- New Legend/Info section -->
                            <div class="mt-4 pt-2 border-top">
                                <small class="text-muted d-block mb-1" style="font-size: 0.65rem;">Sistem Seleksi:</small>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($stepperStages as $step): ?>
                                        <div class="badge bg-<?= $step['color'] ?> bg-opacity-10 text-<?= $step['color'] ?> border border-<?= $step['color'] ?> border-opacity-25" style="font-size: 0.55rem;">
                                            <?= $step['label'] ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
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
                            <?php 
                                $imagePath = $photo;
                                // If photo already contains a path, use it as is, otherwise prepend our standard path
                                if (strpos($photo, '/') === false) {
                                    $imagePath = "/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/" . $photo;
                                }
                            ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>"
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
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="loadPage('profile')">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </button>
                </div>
            </div>

            <!-- Calendar Widget -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">Calendar</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light rounded-circle" id="prev-month">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-light rounded-circle" id="next-month">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
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

            <!-- Pass initial data to JS -->
            <script>
                window.initialActivities = <?= json_encode($currentActivities) ?>;
            </script>

            <!-- Upcoming Events -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">Upcoming</h6>
                    <a href="javascript:void(0)" onclick="navigateTo('wawancara')" class="text-primary text-decoration-none small fw-semibold">View All</a>
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

<!-- Upcoming Activities Modal -->
<div class="modal fade" id="upcomingActivitiesModal" tabindex="-1" aria-labelledby="upcomingActivitiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="upcomingActivitiesModalLabel">Upcoming Activities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0" id="upcomingActivitiesBody">
                <!-- Content populated by JS -->
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Load Dashboard JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/dashboard.js"></script>
