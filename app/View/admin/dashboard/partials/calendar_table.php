<?php
$calendarWeeks = $calendarWeeks ?? [];

/* Penghitung sel untuk animasi "timbul" bertingkat. Nilainya di-reset setiap
   partial ini dirender - termasuk saat ganti bulan lewat AJAX - sehingga
   animasinya ikut berjalan lagi pada tiap navigasi bulan, bukan hanya saat
   halaman pertama dimuat. */
$nSel = 0;

/* Gaya mengikuti referensi "TimeFrame": tanpa garis kotak, tiap sel berupa
   permukaan membulat, hari ini ditandai lingkaran biru penuh.

   Nama kegiatan ditulis apa adanya sebagai teks - tanpa latar berwarna. */

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
            <td class="h-[54px] align-top p-1">
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
            <td class="calendar-cell h-[54px] align-top p-1<?= $adaAcara ? ' cursor-pointer' : '' ?>"<?= $onclick ?>>
              <div class="animate-cell-rise h-full" style="animation-delay: <?= $delaySel ?>ms;">
                <div class="h-full rounded-2xl p-1 flex flex-col items-center gap-0.5 transition-all duration-200 <?= $gayaSel ?>">
                    <span class="flex items-center justify-center text-[11px] leading-none shrink-0 transition-transform duration-200 <?= $gayaAngka ?>">
                        <?= $day['date'] ?>
                    </span>

                    <?php if ($adaAcara): ?>
                        <?php /* Nama kegiatan ditulis sebagai TEKS POLOS - tanpa
                                 latar atau kotak berwarna.

                                 Yang ditulis adalah JENIS kegiatannya, bukan judul
                                 mentahnya: judul bisa sepanjang 84 karakter (mis.
                                 judul skripsi) sehingga di sel selebar ~100px hanya
                                 tampil potongan tak bermakna. Judul lengkap tetap
                                 ada di tooltip dan di modal saat sel diklik. */ ?>
                        <div class="w-full flex flex-col gap-0.5 min-w-0 mt-0.5">
                            <?php
                            // Maksimal 2 baris supaya sel tidak meluber
                            foreach (array_slice($day['events'], 0, 2) as $event):
                                $jenis = $event['jenis'] ?? '';
                                $judulAcara = trim((string) ($event['judul'] ?? ''));
                                $labelSel = $jenis !== '' ? $jenis : ($judulAcara !== '' ? $judulAcara : 'Kegiatan');
                                /* Judul sering sama persis dengan jenisnya
                                   (mis. "Tes Tertulis"); jangan diulang di tooltip. */
                                $tip = $labelSel;
                                if ($judulAcara !== '' && $judulAcara !== $jenis) {
                                    $tip .= ' - ' . $judulAcara;
                                }
                            ?>
                                <span class="block w-full text-[9px] font-semibold text-slate-500 leading-tight text-center truncate"
                                      title="<?= htmlspecialchars($tip) ?>"><?= htmlspecialchars($labelSel) ?></span>
                            <?php endforeach; ?>

                            <?php if (count($day['events']) > 2): ?>
                                <span class="block text-[9px] font-semibold text-slate-400 leading-tight text-center">+<?= count($day['events']) - 2 ?> lagi</span>
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
