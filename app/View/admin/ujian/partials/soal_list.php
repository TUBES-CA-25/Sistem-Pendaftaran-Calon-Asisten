<?php
/**
 * Daftar soal dalam bentuk TABEL.
 *
 * Kontrak yang WAJIB dipertahankan (dipakai admin/exam.js):
 *  - Tiap baris  : <tr class="card" data-id="<id>" data-type="<status_soal>">
 *                  · data-type              -> window.filterSoal() (toggle 'hidden')
 *                  · class card + data-id   -> window.editSoal() / window.deleteSoal()
 *  - Markdown    : .condition-render-markdown (tersembunyi, teks mentah) yang
 *                  DIIKUTI LANGSUNG .markdown-rendered-content sebagai tujuan
 *                  render — exam.js mencarinya lewat nextElementSibling.
 *
 * Menerima $soalArray dari controller.
 */
if (empty($soalArray)) {
    echo '<div class="text-center py-12 flex flex-col items-center">
        <i class="bx bx-file-blank text-slate-300 text-6xl mb-4"></i>
        <h5 class="text-slate-600 font-bold text-lg mb-1">Belum Ada Soal</h5>
        <p class="text-slate-400 text-sm max-w-sm">Klik tombol "Tambah Soal" untuk menambahkan soal baru ke bank ini</p>
    </div>';
    return;
}
?>
<table class="min-w-full divide-y divide-slate-100 text-sm">
    <thead class="bg-slate-50/80">
        <tr>
            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400 w-12">No</th>
            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">Pertanyaan</th>
            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400 w-36">Tipe</th>
            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400 w-56">Jawaban Benar</th>
            <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-400 w-24">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 bg-white">
<?php
foreach ($soalArray as $index => $soal) {
    $isPG      = (($soal['status_soal'] ?? '') === 'pilihan_ganda');
    $soalId    = $soal['id'];
    $deskripsi = $soal['deskripsi'] ?? '';
    $imageUrl  = $soal['image_url'] ?? '';
    $jawaban   = $soal['jawaban'] ?? '';
    $pilihan   = $soal['pilihan'] ?? '';

    /* ------------------------------------------------------------------ *
     * Uraikan pilihan ganda -> tentukan kunci jawaban benar.
     * Logika parsing dipertahankan apa adanya dari versi kartu.
     * ------------------------------------------------------------------ */
    $options    = [];
    $correctKey = null;
    if ($isPG && !empty($pilihan)) {
        $parsed = json_decode($pilihan, true);
        if (is_array($parsed)) {
            if (array_keys($parsed) !== range(0, count($parsed) - 1)) {
                foreach ($parsed as $k => $v) {
                    $options[] = ['key' => $k, 'value' => $v];
                }
            } else {
                foreach ($parsed as $idx => $v) {
                    $options[] = ['key' => chr(65 + $idx), 'value' => $v];
                }
            }
        } else {
            $decoded = strip_tags($pilihan);
            preg_match_all('/([A-E])\.\s*(.*?)(?=(?:,\s*[A-E]\.)|$)/s', $decoded, $matches, PREG_SET_ORDER);
            if (count($matches) > 0) {
                foreach ($matches as $m) {
                    $options[] = ['key' => $m[1], 'value' => trim($m[2])];
                }
            } else {
                $parts = array_map('trim', explode(',', $decoded));
                foreach ($parts as $idx => $part) {
                    $options[] = ['key' => chr(65 + $idx), 'value' => $part];
                }
            }
        }

        if (!empty($jawaban)) {
            $jwb = strtoupper(trim($jawaban));
            foreach ($options as $opt) {
                if ($jwb === strtoupper($opt['key']) || str_starts_with($jwb, strtoupper($opt['key']) . '.')) {
                    $correctKey = $opt['key'];
                    break;
                }
            }
            if (!$correctKey && preg_match('/^([A-E])/', $jwb, $m)) {
                $correctKey = $m[1];
            }
        }
    }

    // Teks untuk kolom "Jawaban Benar"
    $correctText = '';
    if ($isPG) {
        foreach ($options as $opt) {
            if ($opt['key'] === $correctKey) {
                $correctText = trim(strip_tags((string) $opt['value']));
                break;
            }
        }
        if ($correctText === '' && $correctKey !== null) {
            $correctText = 'Pilihan ' . $correctKey;
        }
    } else {
        $correctText = trim(strip_tags((string) $jawaban));
    }
?>
        <tr class="card hover:bg-slate-50/70 transition-colors align-top"
            data-id="<?= $soalId ?>"
            data-type="<?= htmlspecialchars($soal['status_soal'] ?? 'essay') ?>">

            <td class="px-4 py-4 text-slate-400 font-semibold text-xs"><?= $index + 1 ?></td>

            <td class="px-4 py-4">
                <?php if (!empty($imageUrl)): ?>
                    <img src="<?= htmlspecialchars((str_starts_with($imageUrl, 'http') ? '' : '/Sistem-Pendaftaran-Calon-Asisten/') . $imageUrl) ?>"
                         alt="Gambar Soal"
                         class="h-16 w-24 object-cover rounded-lg border border-slate-200 mb-2 cursor-zoom-in hover:opacity-90 transition-opacity"
                         onerror="this.style.display='none'"
                         onclick="showImageModal(this.src)">
                <?php endif; ?>

                <div class="condition-render-markdown" style="display:none;"><?= htmlspecialchars($deskripsi) ?></div>
                <div class="markdown-rendered-content text-slate-800 font-medium leading-relaxed"></div>

                <?php if ($isPG && !empty($options)): ?>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <?php foreach ($options as $opt): ?>
                            <?php $benar = ($opt['key'] === $correctKey); ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold border <?= $benar ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-50 text-slate-500 border-slate-200' ?>">
                                <?= htmlspecialchars($opt['key']) ?><?php if ($benar): ?><i class="bx bxs-check-circle"></i><?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </td>

            <td class="px-4 py-4">
                <?php if ($isPG): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 whitespace-nowrap">
                        <i class="bx bx-list-check"></i>Pilihan Ganda
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100 whitespace-nowrap">
                        <i class="bx bx-edit-alt"></i>Essay
                    </span>
                <?php endif; ?>
            </td>

            <td class="px-4 py-4">
                <?php if ($correctText !== ''): ?>
                    <div class="flex items-start gap-1.5 text-emerald-700">
                        <i class="bx bxs-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                        <span class="text-xs font-medium" title="<?= htmlspecialchars($correctText) ?>"><?= htmlspecialchars($correctText) ?></span>
                    </div>
                <?php else: ?>
                    <span class="text-xs text-slate-300">&mdash;</span>
                <?php endif; ?>
            </td>

            <td class="px-4 py-4">
                <div class="flex items-center justify-center gap-1.5">
                    <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition"
                            onclick="window.editSoal(<?= $soalId ?>)" title="Edit soal">
                        <i class='bx bx-edit'></i>
                    </button>
                    <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition"
                            onclick="window.deleteSoal(<?= $soalId ?>)" title="Hapus soal">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            </td>
        </tr>
<?php } ?>
    </tbody>
</table>
