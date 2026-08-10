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
// Tiga nilai di bawah dipakai view tetapi sebelumnya tidak punya default,
// sehingga view fatal bila dirender tanpa data dari controller.
$graduationStatus = $graduationStatus ?? 'Pending';
$currentActivities = $currentActivities ?? [];
$profileDisplay = $profileDisplay ?? ['hasValidPhoto' => false, 'photoPath' => ''];
?>



<!-- Page Header -->
<?php
    $title = 'Dashboard';
    $subtitle = 'Selamat datang di IC-ASSIST';
    $icon = 'bx bx-home-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-7xl mx-auto pb-3">

    <!-- Tanpa items-start: kolom dibiarkan meregang (default `stretch`) supaya
         tinggi frame kiri dan kanan sejajar. Dengan items-start, tiap kolom
         hanya setinggi isinya sehingga kartu kanan berhenti di tengah. -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        <!-- BARIS 1 KIRI (2/3): pengumuman + Progress & Status.
             Wajib 2 span: di dalamnya ada sub-grid md:grid-cols-12
             (kartu Progress 5 + Stepper 7 bersebelahan). Kalau dipaksa
             1/3, kedua kartu itu terjepit dan layout berantakan. -->
        <div class="lg:col-span-2 space-y-3">

            <?php if ($graduationStatus === 'Lulus' || $graduationStatus === 'Tidak Lulus'): ?>
                <!-- Graduation Announcement Card (Visible when finalized or announcement open) -->
                <?php /* Dua atribut style pada satu elemen membuat yang kedua DIABAIKAN
                         browser - latar gradasi hilang dan kartu jadi putih dengan
                         teks putih (tak terbaca). Keduanya digabung jadi satu. */ ?>
                <div class="relative overflow-hidden rounded-2xl shadow-sm p-6 text-center text-white animate-fade-up"
                     style="animation-delay: 0ms; background: <?= $graduationStatus === 'Lulus' ? 'linear-gradient(135deg, #2563eb, #1d4ed8)' : 'linear-gradient(135deg, #ef4444, #dc2626)' ?>;">
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
                <div class="relative overflow-hidden flex items-center gap-3 p-3.5 rounded-2xl bg-blue-50 border border-blue-100/50 shadow-[0_2px_12px_-2px_rgba(37,99,235,0.12)] animate-fade-up" style="animation-delay: 0ms;">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center shrink-0 animate-dot-pulse">
                        <i class="bi bi-bell-fill text-white text-lg"></i>
                    </div>
                    <div class="relative z-10 pr-24 sm:pr-28">
                        <h6 class="font-bold text-blue-800 mb-1">Hasil Seleksi Sedang Diproses</h6>
                        <p class="text-sm text-slate-600">Pengumuman kelulusan akan ditampilkan di sini setelah seluruh tahapan seleksi berakhir. Tetap pantau!</p>
                    </div>

                    <?php /* Ilustrasi papan-klip bercentang di sisi kanan.

                             Digambar sebagai SVG inline, BUKAN <img> ke berkas atau
                             CDN: Content-Security-Policy aplikasi ini membatasi
                             sumber gambar, dan SVG inline juga bebas dari permintaan
                             jaringan tambahan.

                             pointer-events-none + aria-hidden supaya murni dekoratif
                             dan tidak mengganggu klik maupun pembaca layar. */ ?>
                    <span class="pointer-events-none absolute right-2 -bottom-1 w-24 sm:w-28 opacity-90 hidden sm:block" aria-hidden="true">
                        <svg viewBox="0 0 120 96" fill="none" class="w-full h-auto">
                            <!-- lingkaran latar lembut -->
                            <circle cx="66" cy="46" r="40" fill="#dbeafe" opacity="0.85"/>
                            <!-- papan klip -->
                            <rect x="40" y="16" width="52" height="66" rx="7" fill="#ffffff" stroke="#bfdbfe" stroke-width="2"/>
                            <rect x="54" y="10" width="24" height="12" rx="4" fill="#93c5fd"/>
                            <!-- baris teks -->
                            <rect x="50" y="36" width="32" height="4" rx="2" fill="#dbeafe"/>
                            <rect x="50" y="46" width="24" height="4" rx="2" fill="#dbeafe"/>
                            <!-- lencana centang -->
                            <circle cx="76" cy="64" r="14" fill="#3b82f6"/>
                            <path d="M69.5 64.5l4.5 4.5 8.5-9" stroke="#ffffff" stroke-width="3.2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                            <!-- pena -->
                            <rect x="18" y="52" width="26" height="7" rx="3.5" transform="rotate(-28 18 52)" fill="#bfdbfe"/>
                            <path d="M14 72l2-7 5 3z" fill="#93c5fd"/>
                        </svg>
                    </span>
                </div>
            <?php endif; ?>

            <!-- Progress & Stepper Row -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Progress Circular Card -->
                <div class="md:col-span-5 bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.16)] hover:-translate-y-1 transition-all duration-300 p-4 flex flex-col justify-center animate-pop-in" style="animation-delay: 90ms;">
                    <h6 class="font-bold text-slate-700 mb-4 text-center">Progress Pendaftaran</h6>

                    <div class="relative mx-auto mb-2 w-28 h-28">
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
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-2xl font-black text-blue-600 animate-count-in" style="animation-delay: 380ms;"><?= $percentage ?>%</span>
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
                <div class="md:col-span-7 bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.16)] hover:-translate-y-1 transition-all duration-300 p-4 flex flex-col justify-between animate-pop-in" style="animation-delay: 170ms;">
                    <div>
                        <h6 class="font-bold text-slate-700 mb-2">Status Pendaftaran</h6>
                        
                        <p class="text-sm text-slate-500 mb-6">
                            Anda telah menyelesaikan <strong class="text-slate-800"><?= $tahapanSelesai ?></strong> dari 5 tahapan pendaftaran.
                        </p>

                        <div class="w-full overflow-x-auto overflow-y-hidden pb-2 mb-4">
                            <div class="flex items-start justify-between relative mt-2 px-1 mx-auto min-w-[340px]">
                                <!-- Progress Line Background -->
                                <div class="absolute h-0.5 bg-slate-100 top-2.5 left-0 right-0 z-0"></div>
                                <!-- Progress Line Active -->
                                <?php $stepProgress = min(($tahapanSelesai / 5) * 100, 100); ?>
                                <div class="absolute h-0.5 bg-gradient-to-r from-primary to-secondary top-2.5 left-0 stepper-line transition-all duration-1000" style="width:<?= $stepProgress ?>%; z-index:1;"></div>

                                <?php
                                $stepperStages = [
                                    ['number' => 1, 'bgActive' => 'bg-red-500', 'textActive' => 'text-red-500', 'label' => 'Berkas', 'threshold' => 1],
                                    ['number' => 2, 'bgActive' => 'bg-amber-500', 'textActive' => 'text-amber-500', 'label' => 'Tes Tertulis', 'threshold' => 2],
                                    ['number' => 3, 'bgActive' => 'bg-cyan-500', 'textActive' => 'text-cyan-500', 'label' => 'Presentasi', 'threshold' => 3],
                                    ['number' => 4, 'bgActive' => 'bg-emerald-500', 'textActive' => 'text-emerald-500', 'label' => 'Wawancara', 'threshold' => 4],
                                    ['number' => 5, 'bgActive' => 'bg-blue-600', 'textActive' => 'text-blue-600', 'label' => 'Pengumuman', 'threshold' => 5]
                                ];

                                $iStep = 0;
                                foreach ($stepperStages as $step):
                                    $isActive = $tahapanSelesai>= $step['threshold'];
                                    $delayStep = 260 + ($iStep * 90);   // stagger antar langkah
                                    $iStep++;
                                ?>
                                    <div class="group/step text-center relative px-1 z-10 flex-1 animate-pop-in" style="animation-delay: <?= $delayStep ?>ms;">
                                        <div class="relative w-6 h-6 rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm transition-transform duration-300 group-hover/step:scale-125 <?= $isActive ? $step['bgActive'] : 'bg-slate-100 border border-slate-200' ?>">
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
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-red-50 text-red-600 border-red-100 animate-pop-in transition-transform duration-200 hover:scale-110 cursor-default" style="animation-delay: 480ms;">Berkas</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-amber-50 text-amber-600 border-amber-100 animate-pop-in transition-transform duration-200 hover:scale-110 cursor-default" style="animation-delay: 540ms;">Tes Tertulis</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-cyan-50 text-cyan-600 border-cyan-100 animate-pop-in transition-transform duration-200 hover:scale-110 cursor-default" style="animation-delay: 600ms;">Presentasi</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-emerald-50 text-emerald-600 border-emerald-100 animate-pop-in transition-transform duration-200 hover:scale-110 cursor-default" style="animation-delay: 660ms;">Wawancara</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border bg-blue-50 text-blue-600 border-blue-100 animate-pop-in transition-transform duration-200 hover:scale-110 cursor-default" style="animation-delay: 720ms;">Pengumuman</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (1/3, setinggi 2 baris): Profil DI ATAS, lalu Biodata Diri.
             row-span-2 supaya kolom ini sejajar dengan dua baris di kiri
             (pengumuman+progress, lalu upcoming+kalender). -->
        <!-- flex flex-col: agar kartu Biodata di bawah bisa flex-grow mengisi
             sisa tinggi kolom, sehingga ujung bawahnya sejajar dengan kartu
             Kalender Kegiatan di sebelah kiri. -->
        <div class="lg:col-span-1 lg:row-span-2 flex flex-col gap-3">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.16)] hover:-translate-y-1 transition-all duration-300 p-4 text-center animate-slide-in-right" style="animation-delay: 120ms;">
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
                <!-- break-words + text-sm: untuk akun yang biodatanya belum diisi,
                     nilai ini jatuh ke username yang berupa email panjang
                     (13020230306@student.umi.ac.id) dan tanpa ini teksnya meluber
                     keluar kartu. -->
                <h5 class="font-bold text-slate-800 mb-0.5 text-sm break-words leading-snug"><?= htmlspecialchars($biodata['namaLengkap'] ?? $user['username'] ?? 'User') ?></h5>
                <p class="text-xs text-slate-400 mb-4">Calon Asisten Laboratorium</p>

                <!-- Edit Button -->
                <button class="w-full py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 font-semibold text-sm rounded-xl transition duration-200" onclick="loadPage('biodata')">
                    <i class="bi bi-pencil me-2"></i>Lihat Biodata
                </button>
            </div>

            <!-- Biodata Diri Card. flex-grow: mengisi sisa tinggi kolom kanan
                 agar bagian bawahnya rata dengan kartu Kalender Kegiatan. -->
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.16)] hover:-translate-y-1 transition-all duration-300 p-4 flex-grow animate-slide-in-right" style="animation-delay: 220ms;">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="font-bold text-slate-800 text-lg">Biodata Diri</h5>
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50 transition" onclick="navigateTo('biodata')">
                        <i class="bi bi-pencil me-1"></i>Lihat Biodata
                    </button>
                </div>
                
                <!-- SATU kolom: kartu ini kini berada di kolom sempit (1/3),
                     jadi md:grid-cols-2 akan membuat isinya terjepit. -->
                <div class="grid grid-cols-1 gap-1.5">
                    <!-- Nama Lengkap -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 300ms;">
                        <div class="rounded-xl bg-blue-50 p-2.5 shrink-0 text-blue-600 transition-transform duration-200 group-hover/bio:scale-110">
                            <i class="bi bi-person-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Nama Lengkap</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['namaLengkap'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- NIM -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 370ms;">
                        <div class="rounded-xl bg-emerald-50 p-2.5 shrink-0 text-emerald-600 transition-transform duration-200 group-hover/bio:scale-110">
                            <i class="bi bi-123 text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">NIM</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($user['stambuk'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 440ms;">
                        <div class="rounded-xl bg-cyan-50 p-2.5 shrink-0 text-cyan-600 transition-transform duration-200 group-hover/bio:scale-110">
                            <i class="bi bi-envelope-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Email</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($userName ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Tempat, Tanggal Lahir -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 510ms;">
                        <div class="rounded-xl bg-amber-50 p-2.5 shrink-0 text-amber-600 transition-transform duration-200 group-hover/bio:scale-110">
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
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 580ms;">
                        <div class="rounded-xl bg-slate-50 p-2.5 shrink-0 text-slate-600 transition-transform duration-200 group-hover/bio:scale-110">
                            <i class="bi bi-gender-ambiguous text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Jenis Kelamin</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['jenisKelamin'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 650ms;">
                        <div class="rounded-xl bg-red-50 p-2.5 shrink-0 text-red-600 transition-transform duration-200 group-hover/bio:scale-110">
                            <i class="bi bi-telephone-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Nomor HP</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['noHp'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Program Studi -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 720ms;">
                        <div class="rounded-xl bg-emerald-50 p-2.5 shrink-0 text-emerald-600 transition-transform duration-200 group-hover/bio:scale-110">
                            <i class="bi bi-book-fill text-lg"></i>
                        </div>
                        <div>
                            <small class="text-xs text-slate-400 block mb-0.5">Program Studi</small>
                            <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($biodata['jurusan'] ?? '-') ?></p>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="group/bio flex items-start gap-3 p-2 rounded-xl bg-slate-50/60 border border-slate-100 transition-all duration-200 hover:bg-white hover:border-blue-200 hover:shadow-md animate-fade-up" style="animation-delay: 790ms;">
                        <div class="rounded-xl bg-cyan-50 p-2.5 shrink-0 text-cyan-600 transition-transform duration-200 group-hover/bio:scale-110">
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

        <!-- BARIS 2 KIRI (2/3): kartu gabungan Kalender Kegiatan. -->
        <div class="lg:col-span-2 space-y-3">
            <!-- Upcoming: dipindah ke kolom 2/3 ini agar memanjang dan
                 berada tepat di atas kartu Biodata Diri. -->
            <!-- Kalender Kegiatan: gabungan Kalender + Upcoming.
                 Keduanya memakai data yang sama ($activities dari
                 getKegiatanByMonth), jadi disatukan agar hubungannya jelas:
                 kiri menandai tanggal, kanan merinci kegiatannya. -->
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.16)] hover:-translate-y-1 transition-all duration-300 p-4 animate-fade-up" style="animation-delay: 280ms;">
                <div class="flex justify-between items-center mb-5 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                            <i class="bi bi-calendar3"></i>
                        </span>
                        <div class="min-w-0">
                            <h6 class="font-bold text-slate-800 text-[15px] leading-tight">Kalender Kegiatan</h6>
                            <p class="text-[11px] font-medium text-slate-400 leading-tight">Klik tanggal untuk melihat detail</p>
                        </div>
                    </div>
                    <a href="javascript:void(0)" onclick="navigateTo('wawancara')" class="text-xs text-blue-600 hover:text-blue-700 font-bold transition shrink-0">Lihat Semua</a>
                </div>

                <div class="flex flex-col md:flex-row gap-6 min-w-0">

                    <!-- KIRI: kalender -->
                    <div class="md:flex-1 min-w-0">
                        <?php /* Nama bulan + navigasi disatukan jadi satu pil,
                                 seragam dengan kalender di dashboard admin. */ ?>
                        <div class="flex justify-between items-center mb-4">
                            <p class="font-bold text-sm text-slate-800 tabular-nums" id="calendar-month-year">
                                <?= date('F Y') ?>
                            </p>
                            <div class="flex items-center gap-1 bg-slate-100 rounded-full p-1">
                                <button class="w-7 h-7 rounded-full hover:bg-white hover:text-blue-600 hover:shadow-sm flex items-center justify-center text-slate-500 transition-all duration-200 active:scale-90 border-0" id="prev-month" aria-label="Bulan sebelumnya">
                                    <i class="bi bi-chevron-left text-xs"></i>
                                </button>
                                <button class="w-7 h-7 rounded-full hover:bg-white hover:text-blue-600 hover:shadow-sm flex items-center justify-center text-slate-500 transition-all duration-200 active:scale-90 border-0" id="next-month" aria-label="Bulan berikutnya">
                                    <i class="bi bi-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Day headers -->
                        <?php /* Baris hari dibungkus permukaan biru lembut supaya
                                 terbaca sebagai kepala tabel, bukan teks mengambang. */ ?>
                        <div class="grid grid-cols-7 gap-1 mb-2 text-center text-blue-600 text-[10px] font-bold tracking-wider bg-blue-50/70 rounded-lg py-2">
                            <div>SUN</div>
                            <div>MON</div>
                            <div>TUE</div>
                            <div>WED</div>
                            <div>THU</div>
                            <div>FRI</div>
                            <div>SAT</div>
                        </div>

                        <!-- Calendar dates -->
                        <div class="grid grid-cols-7 gap-1.5" id="calendar-dates">
                            <!-- Dates will be generated by JavaScript -->
                        </div>

                        <?php /* Keterangan warna titik. Tanpa ini, titik berwarna di
                                 bawah tanggal tidak punya arti yang bisa dibaca. */ ?>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-4 pt-3 border-t border-slate-100">
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Wawancara
                            </span>
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>Presentasi
                            </span>
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Tes Tertulis
                            </span>
                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Lainnya
                            </span>
                        </div>
                    </div>

                    <!-- KANAN: daftar kegiatan (diisi ulang oleh dashboard.js) -->
                    <div class="md:flex-1 min-w-0 md:border-l md:border-slate-100 md:pl-6">
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-3">Kegiatan Terdekat</p>
                        <div id="upcomingEventsList">
                            <?php if ($jadwalPresentasiUser): ?>
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/60 border border-slate-100">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <i class="bi bi-calendar-event text-lg"></i>
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="font-semibold text-slate-800 text-sm mb-0.5 truncate"><?= htmlspecialchars($jadwalPresentasiUser['judul'] ?? 'Presentasi') ?></p>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-0.5">
                                            <span class="text-slate-400 text-xs">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= $jadwalPresentasiUser['formattedDate'] ?? '-' ?>
                                            </span>
                                            <span class="text-slate-400 text-xs">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= $jadwalPresentasiUser['formattedTime'] ?? '-' ?> WIB
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8 text-slate-400">
                                    <i class="bi bi-calendar-x text-3xl mb-2 block opacity-65"></i>
                                    <span class="text-xs">Tidak ada kegiatan di bulan ini</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Pass initial data to JS -->
            <script>
                window.initialActivities = <?= json_encode($currentActivities) ?>;
            </script>
        </div>

    </div>
</main>

<!-- Custom Message Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="customMessageModal" aria-labelledby="customMessageModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary text-white border-0 py-4 px-6 flex justify-between items-center rounded-t-2xl">
                <h5 class="font-bold text-base" id="customMessageModalLabel">
                    <i class="bi bi-envelope-fill me-2"></i>Pesan
                </h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6">
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
                                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
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
            <div class="border-0 p-4 bg-slate-50 flex justify-end">
                <button type="button" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition" data-modal-close>Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Activities Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="upcomingActivitiesModal" aria-labelledby="upcomingActivitiesModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="border-0 py-4 px-6 flex justify-between items-center bg-slate-50 border-b border-slate-100 rounded-t-2xl">
                <h5 class="font-bold text-slate-800 text-base" id="upcomingActivitiesModalLabel">Upcoming Activities</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6" id="upcomingActivitiesBody">
                <!-- Content populated by JS -->
            </div>
            <div class="border-0 p-4 bg-slate-50 flex justify-end">
                <button type="button" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition" data-modal-close>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Load Dashboard JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/user/dashboard.js"></script>

