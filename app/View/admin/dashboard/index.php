<?php
/**
 * Dashboard Admin View
 */
$currentYear = date('Y');
$currentMonth = date('m');
$currentMonthName = date('F Y');

// Null Coalescing for optional variables
$totalPendaftar = $totalPendaftar ?? 0;
$pendaftarLulus = $pendaftarLulus ?? 0;
$pendaftarPending = $pendaftarPending ?? 0;
$pendaftarGagal = $pendaftarGagal ?? 0;
$statusKegiatan = $statusKegiatan ?? [];
$kegiatanBulanIni = $kegiatanBulanIni ?? [];
$jadwalPresentasiMendatang = $jadwalPresentasiMendatang ?? [];
$kegiatanMendatang = $kegiatanMendatang ?? [];

// Grafik pendaftar per angkatan. Angkatan = digit ke-4..7 stambuk (130|2023|0306).
$pendaftarPerAngkatan = $pendaftarPerAngkatan ?? [];

// Acuan 100% tinggi batang; max(...,1) mencegah pembagian nol.
$puncakAngkatan = 1;
foreach ($pendaftarPerAngkatan as $baris) {
    $puncakAngkatan = max($puncakAngkatan, (int) $baris['jumlah']);
}
?>

<!-- Page Header -->
<?php
    $title = 'Dashboard';
    $subtitle = 'Monitoring dan kelola kegiatan pendaftaran asisten';
    $icon = 'bx bxs-dashboard';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<!-- Main Content -->
<?php /* Wrapper disamakan dengan 9 halaman admin lain; dashboard satu-satunya
         yang tidak punya pt-0 sehingga ada celah ekstra di bawah header. */ ?>
<div class="max-w-7xl mx-auto px-4 pt-0 pb-6">

    <!-- Baris atas: statistik 2x2 (kiri) + grafik angkatan (kanan) -->
    <?php /* Grafik dipindah ke baris atas berdampingan dengan statistik. Karena
             kolomnya kini jauh lebih sempit daripada saat ia menempati kolom kanan
             penuh, grafiknya otomatis tidak lagi memanjang. */ ?>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6 items-stretch">

        <!-- Kiri: statistik tersusun 2x2 -->
        <div class="lg:col-span-3 flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-3.5">
        <?php
        /* 'tile' berisi kelas Tailwind LENGKAP (bukan potongan yang dirakit),
           karena Play CDN memindai nama kelas sebagai teks literal di sumber.
           Ini sekaligus menghapus style inline background-color yang dulu perlu
           karena warnanya berupa hex dinamis.

           'id' dipakai oleh dashboard.js untuk polling statistik tiap 5 detik.
           Sebelumnya id ini tidak ada di markup sama sekali, sehingga polling
           tidak pernah menyala dan angka hanya berubah saat halaman dimuat ulang. */
        $stats = [
            [
                'label' => 'Total Pendaftar',
                'value' => $totalPendaftar,
                'icon'  => 'bx bxs-group',
                'tile'  => 'bg-blue-50 text-blue-600',
                'angka' => 'text-slate-800',
                'id'    => 'stat-total',
                'bar'   => 'bg-blue-500',
            ],
            [
                'label' => 'Pendaftar Lulus',
                'value' => $pendaftarLulus,
                'icon'  => 'bx bxs-check-shield',
                'tile'  => 'bg-emerald-50 text-emerald-600',
                'angka' => 'text-emerald-600',
                'id'    => 'stat-lulus',
                'bar'   => 'bg-emerald-500',
            ],
            [
                'label' => 'Pendaftar Pending',
                'value' => $pendaftarPending,
                'icon'  => 'bx bxs-time-five',
                'tile'  => 'bg-amber-50 text-amber-600',
                'angka' => 'text-amber-600',
                'id'    => 'stat-pending',
                'bar'   => 'bg-amber-500',
            ],
            [
                'label' => 'Pendaftar Gagal',
                'value' => $pendaftarGagal,
                'icon'  => 'bx bxs-x-circle',
                'tile'  => 'bg-rose-50 text-rose-600',
                'angka' => 'text-rose-600',
                'id'    => 'stat-gagal',
                'bar'   => 'bg-rose-500',
            ],
        ];

        $iStat = 0;
        foreach ($stats as $stat):
            $delayStat = $iStat * 90;   // stagger 90ms antar kartu
            $iStat++;
            /* Baris bawah memakai porsi terhadap total - angka nyata dari data yang
               ada, bukan tren "vs bulan lalu" yang tidak mungkin dihitung karena
               project ini tidak menyimpan riwayat statistik.
               Catatan: poller hanya menulis ulang angkanya, jadi porsi ini baru
               menyegar saat halaman dimuat ulang. Itu disengaja, bukan bug. */
            $isTotal = ($stat['id'] === 'stat-total');
            $porsi   = ($totalPendaftar > 0) ? round($stat['value'] / $totalPendaftar * 100) : 0;

            /* Kartu "Total" jadi kartu highlight bergradien sebagai jangkar visual,
               tiga sisanya putih bersih. Sebelumnya keempat kartu identik sehingga
               tidak ada yang menuntun mata. Kedua cabang ditulis sebagai string
               kelas literal utuh - wajib untuk Play CDN yang memindai teks sumber. */
        ?>
        <div class="group relative overflow-hidden rounded-[16px] p-3.5 animate-pop-in transition-all duration-500 <?= $isTotal
                ? 'bg-gradient-to-br from-primary via-primary to-primary-dark shadow-[0_10px_30px_-8px_rgba(37,99,235,0.5)] hover:shadow-[0_18px_40px_-10px_rgba(37,99,235,0.6)] hover:-translate-y-1'
                : 'bg-white shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] hover:shadow-[0_14px_32px_-8px_rgba(15,23,42,0.16)] hover:-translate-y-1' ?>"
             style="animation-delay: <?= $delayStat ?>ms;">

            <?php /* Kilau melintas sekali di kartu highlight saat halaman dimuat.
                     pointer-events-none supaya tidak menghalangi hover/klik. */ ?>
            <?php if ($isTotal): ?>
                <span class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/3 bg-white/20 blur-md animate-sheen" aria-hidden="true"></span>
            <?php endif; ?>

            <?php /* Lingkaran dekoratif besar yang membesar saat hover. Memberi
                     kedalaman tanpa menambah elemen konten. */ ?>
            <span class="pointer-events-none absolute -right-4 -top-6 w-16 h-16 rounded-full transition-transform duration-700 group-hover:scale-[1.35] <?= $isTotal ? 'bg-white/10' : 'bg-slate-50' ?>" aria-hidden="true"></span>

            <div class="relative">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider leading-tight <?= $isTotal ? 'text-white/75' : 'text-slate-400' ?>"><?= $stat['label'] ?></p>
                    <div class="relative w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 <?= $isTotal ? 'bg-white/20 text-white' : $stat['tile'] ?>">
                        <i class='<?= $stat['icon'] ?>'></i>
                    </div>
                </div>
                <h2 class="text-[1.35rem] font-extrabold leading-none mb-1 tracking-tight animate-count-in <?= $isTotal ? 'text-white' : $stat['angka'] ?>"
                    id="<?= $stat['id'] ?>" style="animation-delay: <?= $delayStat + 160 ?>ms;"><?= $stat['value'] ?></h2>

                <?php if ($isTotal): ?>
                    <p class="text-[10px] font-semibold text-white/80">Seluruh pendaftar terdaftar</p>
                <?php else: ?>
                    <?php /* Bar porsi mini: membuat persentase terlihat, bukan hanya terbaca.
                             Lebar via style inline karena nilainya hasil hitung. */ ?>
                    <div class="flex items-center gap-2">
                        <div class="h-1 flex-grow rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full origin-left animate-grow-right <?= $stat['bar'] ?>"
                                 style="width: <?= max($porsi, 2) ?>%; animation-delay: <?= $delayStat + 260 ?>ms;"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 shrink-0 tabular-nums"><?= $porsi ?>%</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
            </div>

            <!-- Kegiatan Terdekat: di bawah kartu statistik -->
            <?php /* Memakai data nyata dari tabel kegiatan_admin - kegiatan yang sama
                     dengan yang ditandai di kalender. Sengaja TIDAK dibatasi bulan
                     berjalan supaya kegiatan bulan depan tetap terlihat. */ ?>
            <div class="bg-white rounded-[18px] shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] p-4 animate-fade-up" style="animation-delay: 420ms;">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3 mb-3">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                        <i class='bx bx-calendar-star'></i>
                    </span>
                    <div class="min-w-0">
                        <h6 class="text-[13px] font-bold text-slate-800 leading-tight truncate">Kegiatan Terdekat</h6>
                        <p class="text-[10px] font-medium text-slate-400 leading-tight">Agenda yang akan berlangsung</p>
                    </div>
                </div>

                <?php if (empty($kegiatanMendatang)): ?>
                    <div class="text-center py-5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-slate-400">
                        <i class='bx bx-calendar-x text-2xl block mb-1 opacity-50'></i>
                        <span class="text-[11px] font-medium">Belum ada kegiatan terjadwal</span>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-2">
                        <?php $ik = 0; foreach ($kegiatanMendatang as $keg): ?>
                            <?php
                                $ts       = strtotime($keg['tanggal']);
                                $selisih  = (int) floor((strtotime(date('Y-m-d', $ts)) - strtotime(date('Y-m-d'))) / 86400);
                                $labelHari = $selisih === 0 ? 'Hari ini' : ($selisih === 1 ? 'Besok' : $selisih . ' hari lagi');
                                $delayK   = 520 + ($ik * 90);
                                $ik++;
                            ?>
                            <div class="group/keg flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 transition-all duration-300 hover:bg-white hover:border-slate-200 hover:shadow-md hover:translate-x-1 animate-fade-up"
                                 style="animation-delay: <?= $delayK ?>ms;">
                                <!-- Tanggal sebagai blok kalender mini -->
                                <div class="w-11 shrink-0 rounded-lg bg-white border border-slate-200 overflow-hidden text-center transition-colors duration-300 group-hover/keg:border-blue-200">
                                    <div class="bg-gradient-to-br from-primary to-secondary text-white text-[9px] font-bold uppercase tracking-wide py-0.5"><?= date('M', $ts) ?></div>
                                    <div class="text-sm font-extrabold text-slate-700 tabular-nums py-0.5"><?= date('d', $ts) ?></div>
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <p class="text-[12px] font-bold text-slate-700 truncate" title="<?= htmlspecialchars($keg['judul']) ?>"><?= htmlspecialchars($keg['judul']) ?></p>
                                    <p class="text-[10px] font-medium text-slate-400"><?= $labelHari ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kanan: grafik pendaftar per angkatan -->
        <div class="lg:col-span-2 flex self-start w-full">
            <!-- Grafik pendaftar per angkatan (ringkas) -->
            <?php /* Kartu sengaja dibuat kecil: datanya hanya beberapa angkatan,
                     jadi bentuk lebar memakan ruang tanpa menambah informasi.
                     Ditaruh di kolom kanan agar kolom kiri fokus pada kalender. */ ?>
            <div class="bg-white rounded-[18px] shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] p-4 animate-pop-in w-full" style="animation-delay: 360ms;">
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3 mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                            <i class='bx bx-bar-chart-alt-2'></i>
                        </span>
                        <div class="min-w-0">
                            <h6 class="text-[13px] font-bold text-slate-800 leading-tight truncate">Pendaftar per Angkatan</h6>
                            <p class="text-[10px] font-medium text-slate-400 leading-tight">Dibaca dari stambuk</p>
                        </div>
                    </div>
                </div>

                <?php if (empty($pendaftarPerAngkatan)): ?>
                    <div class="text-center py-6 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-slate-400">
                        <i class='bx bx-bar-chart-alt-2 text-2xl block mb-1 opacity-50'></i>
                        <span class="text-[11px] font-medium">Belum ada data</span>
                    </div>
                <?php else: ?>
                    <?php /* Bar chart vertikal ringkas. Tinggi batang lewat style inline
                             karena nilainya hasil hitung - BUKAN kelas Tailwind yang
                             dirakit, sebab Play CDN memindai nama kelas sebagai teks
                             literal di sumber dan tidak akan mengenali hasil rakitan.

                             Batang dibatasi w-14 dan barisnya justify-center supaya
                             saat datanya cuma satu angkatan, batangnya tetap proporsional
                             dan tidak melebar memenuhi kartu. */ ?>
                    <?php
                        /* Skala sumbu Y dibulatkan ke atas ke kelipatan yang enak dibaca,
                           supaya garis grid jatuh di angka bulat (bukan 1.33, 2.67, ...).
                           Ini yang membuat bentuknya terbaca sebagai grafik, bukan
                           sekadar batang mengambang. */
                        $langkah   = $puncakAngkatan <= 4 ? 1 : (int) ceil($puncakAngkatan / 4);
                        $skalaAtas = (int) (ceil($puncakAngkatan / $langkah) * $langkah);
                        $skalaAtas = max($skalaAtas, 1);
                        // Garis grid dari atas ke bawah: skalaAtas ... 0
                        $tikPembagi = [];
                        for ($v = $skalaAtas; $v >= 0; $v -= $langkah) { $tikPembagi[] = $v; }
                    ?>
                    <?php /* items-start (bukan items-end/flex-grow): grafik setinggi
                             isinya sendiri, tidak ikut meregang mengikuti kolom kiri
                             yang lebih panjang. Itu penyebab kartu ini tampak
                             memanjang dengan ruang kosong besar di atas batang. */ ?>
                    <div class="flex gap-2 items-start">
                        <?php /* Label sumbu Y: tiap label dibungkus kotak setinggi
                                 sama dan diberi -translate-y-1/2, sehingga TEKS-nya
                                 (bukan kotaknya) yang sejajar tepat di garis grid.
                                 Sebelumnya justify-between hanya meratakan kotaknya,
                                 jadi angka tampak melenceng dari garisnya. */ ?>
                        <div class="relative h-[96px] shrink-0 w-4">
                            <?php
                                $nTik = count($tikPembagi);
                                foreach ($tikPembagi as $iTik => $tik):
                                    // 0% = garis teratas, 100% = garis dasar
                                    $posisi = $nTik > 1 ? ($iTik / ($nTik - 1)) * 100 : 0;
                            ?>
                                <span class="absolute right-0 -translate-y-1/2 text-[10px] font-semibold text-slate-400 tabular-nums leading-none"
                                      style="top: <?= $posisi ?>%;"><?= $tik ?></span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Area plot -->
                        <div class="flex-grow min-w-0">
                            <div class="relative h-[96px]">
                                <?php /* Garis grid horizontal. inset-0 + justify-between
                                         menempatkannya persis di posisi label sumbu Y. */ ?>
                                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none" aria-hidden="true">
                                    <?php foreach ($tikPembagi as $i => $tik): ?>
                                        <span class="block w-full border-t <?= $i === count($tikPembagi) - 1 ? 'border-slate-300' : 'border-slate-100' ?>"></span>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Batang berdiri di atas garis dasar -->
                                <div class="absolute inset-0 flex items-end justify-around gap-4 px-2">
                                    <?php $ig = 0; foreach ($pendaftarPerAngkatan as $baris): ?>
                                        <?php
                                            $jumlah = (int) $baris['jumlah'];
                                            // Tinggi relatif terhadap skala sumbu, bukan nilai puncak,
                                            // supaya ujung batang sejajar dengan garis gridnya.
                                            $tinggi = max((int) round($jumlah / $skalaAtas * 100), 2);
                                            $delayG = $ig * 110;
                                            $ig++;
                                        ?>
                                        <div class="group/bar relative flex-1 max-w-[46px] h-full flex items-end animate-fade-up"
                                             style="animation-delay: <?= 460 + $delayG ?>ms;">
                                            <!-- Batang: origin-bottom + animate-grow-up = tumbuh dari dasar -->
                                            <div class="relative w-full rounded-t-md bg-gradient-to-t from-primary to-secondary origin-bottom animate-grow-up transition-all duration-300 group-hover/bar:brightness-110"
                                                 style="height: <?= $tinggi ?>%; animation-delay: <?= 560 + $delayG ?>ms;"
                                                 title="Angkatan <?= htmlspecialchars($baris['angkatan']) ?>: <?= $jumlah ?> pendaftar">
                                                <!-- Nilai melayang di atas ujung batang -->
                                                <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-[11px] font-extrabold text-slate-700 tabular-nums transition-colors duration-200 group-hover/bar:text-primary-dark"><?= $jumlah ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Sumbu X: label angkatan, kolomnya sejajar dengan batang -->
                            <div class="flex items-start justify-around gap-4 px-2 pt-2">
                                <?php foreach ($pendaftarPerAngkatan as $baris): ?>
                                    <span class="flex-1 max-w-[46px] text-center text-[11px] font-bold text-slate-500 tabular-nums"><?= htmlspecialchars($baris['angkatan']) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Presentation Progress & Upcoming Schedule -->
    <?php /* items-start dilepas dan kolom kanan memakai flex + flex-grow supaya
             tinggi kedua kolom seimbang. Sebelumnya kolom kanan berhenti di tengah
             sementara kalender memanjang, menyisakan ruang kosong besar di kanan
             bawah - ini masalah tata letak paling terlihat pada tampilan lama. */ ?>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Calendar -->
        <?php /* 3/5 (bukan 2/3): kalender butuh lebar untuk 7 kolom hari, tapi
                 kolom kanan butuh cukup ruang agar timeline tidak terhimpit. */ ?>
        <?php /* items-start: kartu kalender setinggi isinya sendiri, TIDAK diregangkan
                 mengikuti kolom kanan. Dengan flex-grow sebelumnya, kalender ikut
                 memanjang hingga sejajar kolom kanan dan menyisakan area putih kosong
                 besar di bawah tabelnya. */ ?>
        <div class="lg:col-span-3 flex flex-col gap-6 items-stretch self-start w-full">
            <!-- Calendar Card -->
            <div class="bg-white rounded-[22px] shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] p-6 animate-fade-up" style="animation-delay: 240ms;">
                <!-- Header kartu: judul + aksi, sejajar dengan pola kartu grafik
                     & timeline yang memang sudah menaruh judulnya di dalam kartu. -->
                <?php /* Judul + navigasi bulan + tombol tambah disatukan dalam SATU
                         baris header. Sebelumnya navigasi bulan berdiri sendiri di
                         tengah dengan margin besar, memboroskan tinggi dan membuat
                         kartu terasa longgar. */ ?>
                <?php /* Gaya mengikuti referensi TimeFrame: aksen biru, tombol
                         berbentuk pil, dan nama bulan sebagai judul besar. */ ?>
                <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg shrink-0">
                            <i class='bx bx-calendar'></i>
                        </span>
                        <div class="min-w-0">
                            <h6 class="text-[15px] font-bold text-slate-800 leading-tight truncate">Kalender Kegiatan</h6>
                            <p class="text-[11px] font-medium text-slate-400 leading-tight">Klik tanggal untuk melihat detail</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Navigasi bulan, dikelompokkan jadi satu pil -->
                        <div class="flex items-center gap-1 bg-slate-100 rounded-full p-1">
                            <button id="prevMonth" class="w-7 h-7 rounded-full hover:bg-white hover:text-blue-600 hover:shadow-sm flex items-center justify-center border-0 text-slate-500 transition-all duration-200 active:scale-90" aria-label="Bulan sebelumnya">
                                <i class='bx bx-chevron-left text-base'></i>
                            </button>
                            <h6 class="text-xs font-bold text-slate-700 mb-0 px-1.5 min-w-[92px] text-center tabular-nums" id="currentMonth"><?= $currentMonthName ?></h6>
                            <button id="nextMonth" class="w-7 h-7 rounded-full hover:bg-white hover:text-blue-600 hover:shadow-sm flex items-center justify-center border-0 text-slate-500 transition-all duration-200 active:scale-90" aria-label="Bulan berikutnya">
                                <i class='bx bx-chevron-right text-base'></i>
                            </button>
                        </div>

                        <button class="px-3.5 py-2 bg-gradient-to-r from-primary to-secondary hover:from-primary-hover hover:to-secondary-hover text-white font-semibold text-xs rounded-full transition-all duration-200 flex items-center gap-1.5 shadow-sm shadow-blue-500/30 hover:shadow-md hover:shadow-blue-500/40 active:scale-95 border-0 cursor-pointer" type="button" id="btnAddActivity">
                            <i class='bx bx-plus text-sm'></i> Tambah
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <?php /* Baris hari dibungkus permukaan biru lembut seperti referensi,
                         menggantikan garis pemisah tipis. */ ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-separate border-spacing-0 table-fixed" id="calendarTable">
                        <thead>
                            <tr>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70 rounded-l-xl">MON</th>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70">TUE</th>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70">WED</th>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70">THU</th>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70">FRI</th>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70">SAT</th>
                                <th class="py-2.5 text-blue-600 text-[10px] font-bold text-center tracking-widest bg-blue-50/70 rounded-r-xl">SUN</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            <?php 
                                $year = (int)date('Y');
                                $month = (int)date('n');
                                $eventsData = $kegiatanBulanIni ?? [];
                                include __DIR__ . '/partials/calendar_table.php'; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status Activities -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <?php /* h-full diganti flex-grow: kolom ini kini berisi DUA kartu
                     (timeline + grafik). Dengan h-full, timeline memakan seluruh
                     tinggi kolom dan grafik terdorong keluar. */ ?>

            <div class="bg-white rounded-[18px] shadow-[0_2px_12px_-2px_rgba(15,23,42,0.08)] p-4 flex flex-col flex-grow animate-slide-in-right" style="animation-delay: 300ms;">
                <?php /* Header diberi hitungan tahap selesai supaya kartu ini
                         menyampaikan progres sekilas, bukan cuma judul. */ ?>
                <?php
                    $totalTahap   = count($statusKegiatan);
                    $tahapSelesai = 0;
                    foreach ($statusKegiatan as $s) {
                        if (($s['status'] ?? '') === 'Selesai') { $tahapSelesai++; }
                    }
                    $persenTahap = $totalTahap > 0 ? round($tahapSelesai / $totalTahap * 100) : 0;
                ?>
                <div class="border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center justify-between gap-3 mb-2.5">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                                <i class='bx bx-list-check'></i>
                            </span>
                            <div class="min-w-0">
                                <h6 class="text-[13px] font-bold text-slate-800 leading-tight truncate">Status Kegiatan</h6>
                                <p class="text-[10px] font-medium text-slate-400 leading-tight"><?= $tahapSelesai ?> dari <?= $totalTahap ?> tahap selesai</p>
                            </div>
                        </div>
                        <span class="text-base font-extrabold text-slate-800 tabular-nums shrink-0"><?= $persenTahap ?>%</span>
                    </div>
                    <div class="h-1 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-secondary origin-left animate-grow-right"
                             style="width: <?= max($persenTahap, 2) ?>%; animation-delay: 500ms;"></div>
                    </div>
                </div>
                <!-- Timeline: garis vertikal + titik penanda di kiri, kartu tahapan
                     di kanan. Tahap yang sedang berlangsung di-highlight biru
                     penuh sebagai fokus utama. -->
                <div class="flex-grow relative pl-6">
                    <!-- Garis penghubung. inset-y dibuat menjorok agar garis
                         berhenti di titik pertama & terakhir, bukan menggantung. -->
                    <span class="absolute left-[5px] top-3 bottom-3 w-px bg-gradient-to-b from-emerald-400 via-slate-200 to-slate-200 origin-top animate-grow-down" style="animation-delay: 380ms;" aria-hidden="true"></span>
                    <?php /* Lapis kedua garis: hijau berkedip lembut menimpa garis dasar,
                             menyambungkan titik-titik yang menyala agar seluruh jalur
                             terasa hidup. */ ?>
                    <span class="absolute left-[5px] top-3 bottom-3 w-px bg-gradient-to-b from-emerald-400 to-transparent animate-dot-glow" aria-hidden="true"></span>

                    <div class="flex flex-col gap-2">
                    <?php
                    // Status metadata for calendar legend
                    $statusMeta = $statusMeta ?? [];

                    $urutan = 0;
                    foreach ($statusKegiatan as $key => $status):
                        $statusText = $status['status'] ?? '';
                        $aktif    = ($statusText === 'Sedang Berlangsung');
                        $selesai  = ($statusText === 'Selesai');
                        $delay    = $urutan * 90;   // stagger 90ms antar tahapan
                        $urutan++;
                    ?>
                        <div class="relative group/step animate-slide-in-right" style="animation-delay: <?= 420 + $delay ?>ms;">
                            <!-- Titik penanda pada garis. Kelas ditulis literal per
                                 kondisi (bukan dirakit) supaya Tailwind Play CDN
                                 bisa memindainya. -->
                            <?php if ($aktif): ?>
                                <?php /* Titik tahap berjalan: lingkaran cahaya berdenyut
                                         di belakang titik + titik yang berkedip lembut.
                                         Dua lapis supaya "menyala"-nya terbaca jelas. */ ?>
                                <span class="absolute -left-6 top-3.5 w-3 h-3 rounded-full bg-blue-500/40 animate-ring-pulse" aria-hidden="true"></span>
                                <span class="absolute -left-6 top-3.5 w-3 h-3 rounded-full bg-white border-[3px] border-blue-600 animate-dot-pulse" aria-hidden="true"></span>
                            <?php elseif ($selesai): ?>
                                <?php /* Titik tahap SELESAI ikut menyala: dua lapis seperti
                                         tahap berjalan, tapi hijau. Delay-nya bertingkat
                                         mengikuti urutan tahap sehingga titik-titiknya
                                         berdenyut berurutan dari atas ke bawah, bukan
                                         serentak. */ ?>
                                <span class="absolute -left-6 top-3.5 w-3 h-3 rounded-full bg-emerald-400/50 animate-ring-pulse" style="animation-delay: <?= $delay ?>ms;" aria-hidden="true"></span>
                                <span class="absolute -left-6 top-3.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow ring-2 ring-emerald-100 animate-dot-pulse-green transition-transform duration-300 group-hover/step:scale-125" style="animation-delay: <?= $delay ?>ms;" aria-hidden="true"></span>
                            <?php else: ?>
                                <span class="absolute -left-6 top-3.5 w-3 h-3 rounded-full bg-white border-2 border-slate-300 transition-transform duration-300 group-hover/step:scale-125" aria-hidden="true"></span>
                            <?php endif; ?>

                            <!-- Kartu tahapan -->
                            <?php /* Tahap berjalan memakai animate-glow: bayangan biru
                                     berdenyut terus-menerus sehingga tahap yang sedang
                                     diproses langsung menarik mata. Kelas ditulis
                                     literal utuh per cabang (syarat Play CDN). */ ?>
                            <div class="<?= $aktif
                                    ? 'relative overflow-hidden p-3 rounded-xl bg-gradient-to-br from-primary to-secondary text-white animate-glow transition-all duration-300'
                                    : 'p-3 rounded-xl bg-slate-50 border border-slate-100 transition-all duration-300 hover:bg-white hover:border-slate-200 hover:shadow-md hover:translate-x-1' ?>">

                                <?php if ($aktif): ?>
                                    <?php /* Kilau melintas berulang di kartu yang berjalan. */ ?>
                                    <span class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/3 bg-white/25 blur-md animate-sheen-loop" aria-hidden="true"></span>
                                <?php endif; ?>

                                <div class="relative flex justify-between items-start gap-2 mb-0.5">
                                    <h6 class="font-bold text-[13px] <?= $aktif ? 'text-white' : 'text-slate-700' ?>">
                                        <?= htmlspecialchars($status['label']) ?>
                                    </h6>

                                    <?php if ($selesai): ?>
                                        <i class="bx bxs-check-circle text-emerald-500 text-base shrink-0"></i>
                                    <?php elseif ($aktif): ?>
                                        <span class="text-[9px] font-bold uppercase tracking-wider bg-white/25 px-2 py-0.5 rounded-full shrink-0 animate-shimmer">Berjalan</span>
                                    <?php endif; ?>
                                </div>

                                <p class="relative text-[10px] font-medium mb-1.5 <?= $aktif ? 'text-white/85' : 'text-slate-400' ?>">
                                    <?= (int) ($status['jumlah'] ?? 0) ?> peserta &middot; <?= htmlspecialchars($statusText) ?>
                                </p>

                                <?php if (!empty($status['deadline'])): ?>
                                    <div class="flex items-center gap-2 text-[10px] font-medium <?= $aktif ? 'text-white/80' : 'text-slate-400' ?>">
                                        <i class="bx bx-calendar-event"></i>
                                        <?php /* .deadline-date + data-jenis: penanda stabil untuk
                                                 dashboard.js. Dulu JS memakai previousElementSibling
                                                 sehingga markup ini tidak boleh digeser sedikit pun.
                                                 Awalan "Deadline: " disamakan dengan yang ditulis JS
                                                 agar bentuk teks tidak berubah setelah diedit. */ ?>
                                        <span class="deadline-date" data-jenis="<?= $key ?>">Deadline: <?= date('d M Y', strtotime($status['deadline'])) ?></span>
                                        <button class="edit-deadline-btn <?= $aktif ? 'text-white hover:text-white/80' : 'text-blue-600 hover:text-blue-700' ?>"
                                                data-jenis="<?= $key ?>"
                                                data-label="<?= htmlspecialchars($status['label']) ?>"
                                                data-date="<?= $status['deadline'] ?>"
                                                title="Ubah deadline">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>



        </div>

    </div>
</div>

<!-- Add Activity Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="addActivityModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-calendar-plus text-blue-600"></i>Tambah Kegiatan
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Tutup">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="addActivityForm" class="space-y-4">
                    <div>
                        <label for="judulKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Judul Kegiatan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="judul" id="judulKegiatan" required>
                    </div>
                    <div>
                        <label for="tanggalKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="tanggal" id="tanggalKegiatan" required>
                    </div>
                    <div>
                        <label for="deskripsiKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="deskripsi" id="deskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition cursor-pointer" data-modal-close>Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition border-0 cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Deadline Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="editDeadlineModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-sm scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit text-blue-600"></i>Edit Deadline
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Tutup">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="editDeadlineForm" class="space-y-4">
                    <input type="hidden" id="editDeadlineJenis" name="jenis">
                    <small class="text-slate-400 font-medium block" id="editDeadlineLabelName"></small>
                    <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" id="editDeadlineDate" name="tanggal" required>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition border-0 cursor-pointer">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Activity Detail/Action Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="activityActionModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[480px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-[24px] shadow-2xl overflow-hidden">
            
            <!-- Header section (Gradient Blue to Cyan) -->
            <div class="bg-gradient-to-br from-primary to-secondary p-6 pb-5 relative">
                <button type="button" class="absolute top-3 right-3 w-8 h-8 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition-colors shadow-sm cursor-pointer border-0" data-modal-close aria-label="Tutup">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
                <h5 class="text-[17px] font-extrabold text-white leading-snug pr-8 tracking-tight uppercase" id="displayJudul"></h5>
            </div>

            <!-- Content section (White) -->
            <div class="p-6">
                <!-- Informasi Umum -->
                <div class="mb-5">
                    <div class="flex items-center gap-1.5 mb-2.5">
                        <i class="bx bx-info-circle text-slate-400 text-sm"></i>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Informasi Umum</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#E6F0FF] rounded-lg">
                        <i class="bx bx-calendar text-blue-600 text-sm"></i>
                        <span class="text-xs font-bold text-blue-700" id="displayTanggal"></span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-2">
                    <div class="flex items-center gap-1.5 mb-2.5">
                        <i class="bx bx-file text-slate-400 text-sm"></i>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi</span>
                    </div>
                    <div class="bg-[#F8FAFC] rounded-xl p-4 border border-slate-100">
                        <p class="text-[13px] text-slate-600 leading-relaxed whitespace-pre-wrap m-0" id="displayDeskripsi"></p>
                    </div>
                </div>
            </div>

            <!-- Actions — Kegiatan -->
            <div id="calendarActions" style="display: none;" class="px-6 pb-6 pt-0">
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold text-[13px] rounded-xl border border-slate-200 transition-all flex items-center justify-center gap-2 cursor-pointer" id="btnEditActivity">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button type="button" class="py-2.5 bg-[#C81E1E] hover:bg-red-700 text-white font-bold text-[13px] rounded-xl transition-all flex items-center justify-center gap-2 border-0 cursor-pointer" id="btnDeleteActivity">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>

            <!-- Actions — Wawancara/Presentasi -->
            <div id="calendarManageAction" style="display: none;" class="px-6 pb-6 pt-0">
                <button type="button" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2 border-0 cursor-pointer" id="btnManageSchedule">
                    <i class="bx bx-calendar-event text-lg"></i> Kelola Penjadwalan
                </button>
            </div>

        </div>
    </div>
</div>


<!-- Edit Activity Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="editActivityModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit-alt text-blue-600"></i>Edit Kegiatan
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Tutup">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="editActivityForm" class="space-y-4">
                    <input type="hidden" name="id" id="editIdKegiatan">
                    <div>
                        <label for="editJudulKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Judul Kegiatan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="judul" id="editJudulKegiatan" required>
                    </div>
                    <div>
                        <label for="editTanggalKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="tanggal" id="editTanggalKegiatan" required>
                    </div>
                    <div>
                        <label for="editDeskripsiKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="deskripsi" id="editDeskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition" data-modal-close>Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.eventsData = <?= json_encode($kegiatanBulanIni ?? []) ?>;
    window.baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
</script>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/admin/dashboard.js"></script>
