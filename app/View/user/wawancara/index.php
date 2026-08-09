<?php
/**
 * Wawancara View
 *
 * Data yang diterima dari controller:
 * @var array $wawancara - Data jadwal wawancara
 */
$wawancara = $wawancara ?? [];

// --- LOGIKA BARU: FILTER KHUSUS MILIK SAYA ---
// Kita buat array baru ($mySchedule) yang isinya cuma data yang punya 'is_mine' = true
$mySchedule = array_filter($wawancara, function($item) {
    return isset($item['is_mine']) && $item['is_mine'] == true;
});
// ---------------------------------------------
?>

<?php
    $title = 'Jadwal Kegiatan';
    $subtitle = 'Informasi jadwal wawancara dan kegiatan Anda';
    $icon = 'bx bx-user-voice';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-7xl mx-auto pb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
        <!-- Kotak "Cari jadwal" dihapus: fungsinya duplikat dengan kotak
             "Cari data" yang otomatis disuntik VanillaPaginator tepat di atas
             tabel, dan keduanya menyembunyikan baris dengan cara berbeda
             sehingga bisa saling bentrok. Judul kini menyatu dengan tabel
             (tanpa border pemisah, karena toolbar paginator sudah membawa
             garisnya sendiri). -->
        <div class="px-6 pt-6 pb-1">
            <h5 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-calendar-event text-blue-600"></i>Jadwal Kegiatan Saya
            </h5>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm no-datatable" data-paginator="true" data-paginator-perpage="5">
                <thead>
                    <tr>
                        <th class="dt-head-cell">No</th>
                        <th class="dt-head-cell">Jenis Kegiatan</th>
                        <th class="dt-head-cell">Lokasi</th>
                        <th class="dt-head-cell">Tanggal</th>
                        <th class="dt-head-cell">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($mySchedule)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                <i class="bi bi-calendar-x text-4xl mb-2 block opacity-50"></i>
                                <span class="text-xs font-medium">Anda belum memiliki jadwal kegiatan apapun.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($mySchedule as $value): ?>
                            <tr>
                                <td class="px-4 py-3 text-slate-500 font-medium text-xs">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-50 border border-slate-100 text-slate-500 font-bold"><?= $i ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center flex-wrap gap-2">
                                            <?php 
                                            $icon = 'bi-calendar-event';
                                            $color = 'text-blue-600';
                                            if (isset($value['jenis'])) {
                                                if ($value['jenis'] === 'Wawancara') {
                                                    $icon = 'bi-people';
                                                    $color = 'text-emerald-600';
                                                } elseif ($value['jenis'] === 'Presentasi') {
                                                    $icon = 'bi-display';
                                                    $color = 'text-cyan-600';
                                                } elseif ($value['jenis'] === 'Ujian Tertulis') {
                                                    $icon = 'bi-clipboard-check';
                                                    $color = 'text-rose-600';
                                                }
                                            }
                                            ?>
                                            <i class="bi <?= $icon ?> <?= $color ?> text-sm"></i>
                                            <span class="font-bold text-slate-700 text-xs"><?= htmlspecialchars($value['judul'] ?? '-') ?></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 text-slate-400 border border-slate-100">
                                                <?= htmlspecialchars($value['jenis'] ?? 'Kegiatan') ?>
                                            </span>

                                            <?php if (isset($value['status_kehadiran'])): ?>
                                                <?php if ($value['status_kehadiran'] === 'Hadir'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                        <i class="bi bi-check-circle-fill"></i>Selesai
                                                    </span>
                                                <?php elseif ($value['status_kehadiran'] === 'Alpha'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                                        <i class="bi bi-x-circle-fill"></i>Alpha
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1 ml-5">
                                            <i class="bi bi-person"></i><?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs font-semibold">
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-geo-alt text-blue-600"></i>
                                        <?= htmlspecialchars($value['ruangan'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-500 text-xs font-medium">
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-calendar3"></i>
                                        <?= htmlspecialchars($value['tanggal'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                        <i class="bi bi-clock"></i><?= $value['waktu'] !== '00:00:00' ? htmlspecialchars($value['waktu']) : 'Full Day' ?>
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
</main>


