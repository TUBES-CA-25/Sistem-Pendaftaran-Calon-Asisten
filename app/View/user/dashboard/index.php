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
$userName = $userName ?? 'Guest';
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
    /* Inline helper classes for dynamically generated items in JavaScript */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .calendar-date {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        border-radius: 8px;
        position: relative;
    }
    .calendar-date.other-month {
        opacity: 0.3;
    }
    .calendar-date.today {
        background-color: rgb(219 234 254);
        color: rgb(37 99 235);
        font-weight: 700;
    }
    .calendar-date.has-activity {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .calendar-date.has-activity:hover {
        background-color: rgb(241 245 249);
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
        border-radius: 9999px;
    }
</style>

<!-- Page Header -->
<?php
    $title = 'Dashboard';
    $subtitle = 'Selamat datang di IC-ASSIST';
    $icon = 'bx bx-home-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">

    <!-- Greeting Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
            Hello <?= htmlspecialchars($biodata['namaLengkap'] ?? $user['username'] ?? 'User') ?> 👋
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content (Left Column) - 2/3 width -->
        <div class="lg:col-span-2 space-y-6">

            <?php if ($graduationStatus === 'Lulus' || $graduationStatus === 'Tidak Lulus'): ?>
                <!-- Graduation Announcement Card (Visible when finalized or announcement open) -->
                <div class="relative overflow-hidden rounded-2xl shadow-sm p-6 text-center text-white" 
                     style="background: <?= $graduationStatus === 'Lulus' ? 'linear-gradient(135deg, #2563eb, #1d4ed8)' : 'linear-gradient(135deg, #ef4444, #dc2626)' ?>;">
                    <div class="relative z-10">
                        <div class="mb-3">
                            <i class="bi bi-patch-check-fill text-5xl opacity-90"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">
                            <?= $graduationStatus === 'Lulus' ? 'Selamat, Anda telah lulus!' : 'Mohon Maaf, Anda Belum Lulus.' ?>
                        </h4>
                        <p class="text-sm opacity-90">
                            <?= $graduationStatus === 'Lulus' 
                                ? 'Anda telah berhasil melewati seluruh tahapan seleksi calon asisten laboratorium.' 
                                : 'Terima kasih telah berpartisipasi dalam proses seleksi. Tetap semangat dan coba lagi di kesempatan berikutnya.' ?>
                        </p>
                    </div>
                    <!-- Decorative Circles (Bubbles) -->
                    <div class="absolute rounded-full bg-white w-[150px] h-[150px] -top-10 -right-10 opacity-10"></div>
                    <div class="absolute rounded-full bg-white w-[80px] h-[80px] -bottom-4 -left-5 opacity-10"></div>
                </div>
            <?php else: ?>
                <!-- Announcement Coming Soon Card (Visible when closed and pending) -->
                <div class="flex items-center gap-4 p-5 rounded-2xl bg-blue-50 border border-blue-100/50">
                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-content-center shrink-0">
                        <i class="bi bi-bell-fill text-white text-lg"></i>
                    </div>
                    <div>
                        <h6 class="font-bold text-blue-800 mb-1">Hasil Seleksi Sedang Diproses</h6>
                        <p class="text-sm text-slate-600">Pengumuman kelulusan akan ditampilkan di sini setelah seluruh tahapan seleksi berakhir. Tetap pantau!</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Progress & Stepper Row -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Progress Circular Card -->
                <div class="md:col-span-5 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-center">
                    <h6 class="font-bold text-slate-700 mb-4 text-center">Progress Pendaftaran</h6>

                    <div class="relative mx-auto mb-4 w-36 h-36">
                        <!-- SVG Circular Progress (Responsive) -->
                        <svg viewBox="0 0 150 150" class="w-full h-full">
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#3dc2ec" />
                                    <stop offset="100%" stop-color="#2563eb" />
                                </linearGradient>
                            </defs>
                            <circle stroke="#f1f5f9" stroke-width="10" fill="transparent" r="65" cx="75" cy="75"/>
                            <circle stroke="url(#gradient)" stroke-width="10" fill="transparent" r="65" cx="75" cy="75"
                                    style="stroke-dasharray: 408.41; stroke-dashoffset: <?= 408.41 * (1 - $percentage/100) ?>; transform: rotate(-90deg); transform-origin: center; transition: stroke-dashoffset 1s ease-in-out;"/>
                        </svg>

                        <!-- Text di tengah -->
                        <div class="absolute inset-0 flex flex-col items-center justify-content-center text-center">
                            <span class="text-2xl font-black text-blue-600"><?= $percentage ?>%</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Complete</span>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="flex justify-center gap-4 mt-2">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                            <span class="text-xs text-slate-500">Terisi</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-slate-200"></div>
                            <span class="text-xs text-slate-500">Kosong</span>
                        </div>
                    </div>
                </div>

                <!-- Status Stepper Card -->
                <div class="md:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                    <div>
                        <h6 class="font-bold text-slate-700 mb-2">Status Pendaftaran</h6>
                        
                        <p class="text-sm text-slate-500 mb-6">
                            Anda telah menyelesaikan <strong class="text-slate-800"><?= $tahapanSelesai ?></strong> dari 5 tahapan pendaftaran.
                        </p>

                        <div class="w-full overflow-x-auto pb-2 mb-4">
                            <div class="flex items-start justify-between relative mt-2 px-1 mx-auto min-w-[340px]">
                                <!-- Progress Line Background -->
                                <div class="absolute h-0.5 bg-slate-100 top-2.5 left-0 right-0 z-0"></div>
                                <!-- Progress Line Active -->
                                <?php $stepProgress = min(($tahapanSelesai / 5) * 100, 100); ?>
                                <div class="absolute h-0.5 bg-blue-600 top-2.5 left-0 stepper-line transition-all duration-1000" style="width:<?= $stepProgress ?>%; z-index:1;"></div>

                                <?php
                                $stepperStages = [
                                    ['number' => 1, 'bgActive' => 'bg-red-500', 'textActive' => 'text-red-500', 'label' => 'Berkas', 'threshold' => 1],
                                    ['number' => 2, 'bgActive' => 'bg-amber-500', 'textActive' => 'text-amber-500', 'label' => 'Tes Tertulis', 'threshold' => 2],
                                    ['number' => 3, 'bgActive' => 'bg-cyan-500', 'textActive' => 'text-cyan-500', 'label' => 'Presentasi', 'threshold' => 3],
                                    ['number' => 4, 'bgActive' => 'bg-emerald-500', 'textActive' => 'text-emerald-500', 'label' => 'Wawancara', 'threshold' => 4],
                                    ['number' => 5, 'bgActive' => 'bg-blue-600', 'textActive' => 'text-blue-600', 'label' => 'Pengumuman', 'threshold' => 5]
                                ];

                                foreach ($stepperStages as $step):
                                    $isActive = $tahapanSelesai >= $step['threshold'];
                                ?>
                                    <div class="text-center relative px-1 z-10 flex-1">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-content-center mx-auto mb-2 shadow-sm transition-all duration-300 <?= $isActive ? $step['bgActive'] : 'bg-slate-100 border border-slate-200' ?>">
                                            <?php if ($isActive): ?>
                                                <i class="bi bi-check text-white text-xs font-bold"></i>
                                            <?php else: ?>
                                                <span class="text-slate-400 font-bold text-[10px]"><?= $step['number'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="font-bold block text-[10px] leading-tight transition-colors <?= $isActive ? $step['textActive'] : 'text-slate-400' ?>"><?= $step['label'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- New Legend/Info section -->
                    <div class="border-t border-slate-100 pt-4 mt-2">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-2">Sistem Seleksi:</span>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-red-50 text-red-600 border-red-100">Berkas</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-amber-50 text-amber-600 border-amber-100">Tes Tertulis</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-cyan-50 text-cyan-600 border-cyan-100">Presentasi</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-emerald-50 text-emerald-600 border-emerald-100">Wawancara</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-blue-50 text-blue-600 border-blue-100">Pengumuman</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biodata Diri Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="font-bold text-slate-800 text-lg">Biodata Diri</h5>
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50 transition" onclick="navigateTo('biodata')">
                        <i class="bi bi-pencil me-1"></i>Lihat Biodata
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-blue-50 p-2.5 shrink-0 text-blue-600">
                            <i class="bi bi-person-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Nama Lengkap</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['namaLengkap'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- NIM -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-emerald-50 p-2.5 shrink-0 text-emerald-600">
                            <i class="bi bi-123 text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">NIM</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($user['stambuk'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-cyan-50 p-2.5 shrink-0 text-cyan-600">
                            <i class="bi bi-envelope-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Email</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($userName ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Tempat, Tanggal Lahir -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-amber-50 p-2.5 shrink-0 text-amber-600">
                            <i class="bi bi-calendar-event text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Tempat, Tanggal Lahir</small>
                            <p class="font-semibold text-slate-700 text-sm">
                                <?= htmlspecialchars($biodata['tempatLahir'] ?? '-') ?>,
                                <?= htmlspecialchars($biodata['tanggalLahir'] ?? '-') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-slate-50 p-2.5 shrink-0 text-slate-600">
                            <i class="bi bi-gender-ambiguous text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Jenis Kelamin</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['jenisKelamin'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-red-50 p-2.5 shrink-0 text-red-600">
                            <i class="bi bi-telephone-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Nomor HP</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['noHp'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Program Studi -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-emerald-50 p-2.5 shrink-0 text-emerald-600">
                            <i class="bi bi-book-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Program Studi</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['jurusan'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-cyan-50 p-2.5 shrink-0 text-cyan-600">
                            <i class="bi bi-geo-alt-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Alamat</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['alamat'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar (Right Column) - 1/3 width -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center">
                <!-- Profile Photo -->
                <div class="mb-4 flex justify-center">
                    <?php if ($profileDisplay['hasValidPhoto']): ?>
                        <img src="<?= htmlspecialchars($profileDisplay['photoPath']) ?>"
                             alt="Profile"
                             class="rounded-full border-4 border-blue-50 w-24 h-24 object-cover"
                             onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/dummy.jpeg'">
                    <?php else: ?>
                        <!-- Default Avatar (Fallback) -->
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png"
                             alt="Profile"
                             class="rounded-full border-4 border-blue-50 w-24 h-24 object-cover">
                    <?php endif; ?>
                </div>

                <!-- Name & Title -->
                <h5 class="font-bold text-slate-800 mb-0.5 text-base"><?= htmlspecialchars($biodata['namaLengkap'] ?? $user['username'] ?? 'User') ?></h5>
                <p class="text-xs text-slate-400 mb-4">Calon Asisten Lab</p>

                <!-- Edit Button -->
                <button class="w-full py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 font-semibold text-sm rounded-xl transition duration-200" onclick="loadPage('biodata')">
                    <i class="bi bi-pencil me-2"></i>Lihat Biodata
                </button>
            </div>

            <!-- Calendar Widget -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h6 class="font-bold text-slate-700">Calendar</h6>
                    <div class="flex gap-1">
                        <button class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-content-center text-slate-600 transition" id="prev-month">
                            <i class="bi bi-chevron-left text-xs"></i>
                        </button>
                        <button class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-content-center text-slate-600 transition" id="next-month">
                            <i class="bi bi-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Calendar Header -->
                <div class="text-center mb-3">
                    <p class="font-bold text-sm text-slate-800" id="calendar-month-year">
                        <?= date('F Y') ?>
                    </p>
                </div>

                <!-- Day headers -->
                <div class="grid grid-cols-7 gap-1 mb-2 text-center text-slate-400 text-[10px] font-bold tracking-wider">
                    <div>SUN</div>
                    <div>MON</div>
                    <div>TUE</div>
                    <div>WED</div>
                    <div>THU</div>
                    <div>FRI</div>
                    <div>SAT</div>
                </div>

                <!-- Calendar dates -->
                <div class="grid grid-cols-7 gap-1" id="calendar-dates">
                    <!-- Dates will be generated by JavaScript -->
                </div>
            </div>

            <!-- Pass initial data to JS -->
            <script>
                window.initialActivities = <?= json_encode($currentActivities) ?>;
            </script>

            <!-- Upcoming Events -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h6 class="font-bold text-slate-700">Upcoming</h6>
                    <a href="javascript:void(0)" onclick="navigateTo('wawancara')" class="text-xs text-blue-600 hover:text-blue-700 font-bold transition">View All</a>
                </div>
                
                <div>
                    <?php if ($jadwalPresentasiUser): ?>
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-content-center shrink-0">
                                <i class="bi bi-calendar-event text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800 text-sm mb-0.5"><?= htmlspecialchars($jadwalPresentasiUser['judul'] ?? 'Presentasi') ?></p>
                                <span class="text-slate-400 text-xs block mb-0.5">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= $jadwalPresentasiUser['formattedDate'] ?? '-' ?>
                                </span>
                                <span class="text-slate-400 text-xs block">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= $jadwalPresentasiUser['formattedTime'] ?? '-' ?> WIB
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-6 text-slate-400">
                            <i class="bi bi-calendar-x text-3xl mb-2 block opacity-65"></i>
                            <span class="text-xs">No upcoming events</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Custom Message Modal -->
<div class="modal fade" id="customMessageModal" tabindex="-1" aria-labelledby="customMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-blue-500 to-blue-600 text-white border-0 py-4 px-6 flex justify-between items-center">
                <h5 class="modal-title font-bold text-base" id="customMessageModalLabel">
                    <i class="bi bi-envelope-fill me-2"></i>Pesan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6">
                <div class="space-y-4">
                    <?php if (empty($notifikasi)): ?>
                        <div class="text-center py-8 text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3 block opacity-60"></i>
                            <p class="text-sm">Tidak ada pesan</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifikasi as $notif): ?>
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-content-center font-bold text-xs">
                                        T
                                    </div>
                                    <strong class="text-slate-700 text-sm">Tim Iclabs</strong>
                                </div>
                                <p class="text-slate-600 text-sm mb-2 leading-relaxed"><?= htmlspecialchars($notif['pesan']) ?></p>
                                <span class="text-slate-400 text-xs block">
                                    <i class="bi bi-clock me-1"></i><?= $notif['created_at'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-slate-50 flex justify-end">
                <button type="button" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Activities Modal -->
<div class="modal fade" id="upcomingActivitiesModal" tabindex="-1" aria-labelledby="upcomingActivitiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="modal-header border-0 py-4 px-6 flex justify-between items-center bg-slate-50 border-b border-slate-100">
                <h5 class="modal-title font-bold text-slate-800 text-base" id="upcomingActivitiesModalLabel">Upcoming Activities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6" id="upcomingActivitiesBody">
                <!-- Content populated by JS -->
            </div>
            <div class="modal-footer border-0 p-4 bg-slate-50 flex justify-end">
                <button type="button" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Load Dashboard JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/dashboard.js"></script>
