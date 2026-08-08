<?php
$calendarWeeks = $calendarWeeks ?? [];

/* Penghitung sel untuk animasi "timbul" bertingkat. Nilainya di-reset setiap
   partial ini dirender - termasuk saat ganti bulan lewat AJAX - sehingga
   animasinya ikut berjalan lagi pada tiap navigasi bulan, bukan hanya saat
   halaman pertama dimuat. */
$nSel = 0;

/* Gaya mengikuti referensi "TimeFrame": tanpa garis kotak, tiap sel berupa
   permukaan membulat, hari ini ditandai lingkaran biru penuh, dan acara
   tampil sebagai kartu berwarna.

   Warna kartu acara dipetakan per jenis dan ditulis sebagai string kelas
   LITERAL UTUH (bukan dirakit dari potongan) - syarat Tailwind Play CDN,
   yang memindai nama kelas sebagai teks di sumber. Kelas hasil rakitan
   tidak akan pernah dikompilasi. */
$gayaAcara = [
    'Kegiatan'   => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',
    'Wawancara'  => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
    'Presentasi' => 'bg-lime-100 text-lime-800 ring-1 ring-lime-200',
];
$gayaDefault = 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';

foreach ($calendarWeeks as $week):
?>
<tr>
    <?php
    /* $kolom dipakai untuk menandai akhir pekan. Minggu dimulai Senin,
       jadi indeks 5 = Sabtu dan 6 = Minggu. */
    $kolom = -1;
    foreach ($week as $day):
        $kolom++;
        $akhirPekan = ($kolom >= 5);
    ?>
        <?php if ($day === null): ?>
            <?php $nSel++; ?>
            <td class="h-[66px] align-top p-1">
                <div class="h-full rounded-2xl bg-slate-50/60"></div>
            </td>
        <?php else: ?>
            <?php
                $isToday  = $day['isToday'];
                $adaAcara = count($day['events']) > 0;
                $delaySel = $nSel * 14;
                $nSel++;

                $onclick = '';
                if ($adaAcara) {
                    $escapedEvents = htmlspecialchars(json_encode($day['events']), ENT_QUOTES, 'UTF-8');
                    $onclick = " onclick='showActivityActions(this.getAttribute(\"data-events\"))' data-events=\"$escapedEvents\"";
                }

                /* Permukaan sel: hari ini berlatar biru lembut, hari berkegiatan
                   berlatar putih dengan cincin tipis, sisanya polos. */
                if ($isToday) {
                    $gayaSel = 'bg-blue-50/80 ring-2 ring-blue-400 shadow-sm shadow-blue-500/20';
                } elseif ($adaAcara) {
                    $gayaSel = 'bg-white ring-1 ring-slate-200 shadow-sm hover:ring-blue-300 hover:shadow-md';
                } elseif ($akhirPekan) {
                    $gayaSel = 'bg-slate-50/60 hover:bg-slate-100';
                } else {
                    $gayaSel = 'bg-slate-50/60 hover:bg-slate-100';
                }

                /* Angka tanggal: hari ini jadi lingkaran gradasi biru penuh. */
                if ($isToday) {
                    $gayaAngka = 'w-7 h-7 rounded-full bg-gradient-to-br from-primary to-secondary text-white font-bold shadow-md shadow-blue-500/40';
                } elseif ($adaAcara) {
                    $gayaAngka = 'w-6 h-6 text-slate-700 font-bold';
                } elseif ($akhirPekan) {
                    $gayaAngka = 'w-6 h-6 text-slate-300 font-semibold';
                } else {
                    $gayaAngka = 'w-6 h-6 text-slate-400 font-semibold';
                }
            ?>
            <td class="calendar-cell h-[66px] align-top p-1<?= $adaAcara ? ' cursor-pointer' : '' ?>"<?= $onclick ?>>
              <div class="animate-cell-rise h-full" style="animation-delay: <?= $delaySel ?>ms;">
                <div class="h-full rounded-2xl p-1 flex flex-col items-center gap-0.5 transition-all duration-200 <?= $gayaSel ?>">
                    <span class="flex items-center justify-center text-[11px] leading-none shrink-0 transition-transform duration-200 <?= $gayaAngka ?>">
                        <?= $day['date'] ?>
                    </span>

                    <?php if ($adaAcara): ?>
                        <div class="w-full flex flex-col gap-[2px] min-w-0 overflow-hidden">
                            <?php
                            // Tampilkan maksimal 2 acara supaya sel tidak meluber
                            foreach (array_slice($day['events'], 0, 2) as $event):
                                $jenis = $event['jenis'] ?? '';
                                $gaya  = $gayaAcara[$jenis] ?? $gayaDefault;
                            ?>
                                <div class="text-[9px] leading-none w-full px-1.5 py-[3px] rounded font-bold truncate <?= $gaya ?>"
                                     title="<?= htmlspecialchars($event['judul']) ?>">
                                    <?= htmlspecialchars($event['judul']) ?>
                                </div>
                            <?php endforeach; ?>

                            <?php if (count($day['events']) > 2): ?>
                                <div class="text-[9px] font-bold text-slate-400 text-center leading-tight">
                                    +<?= count($day['events']) - 2 ?> lagi
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
              </div>
            </td>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
