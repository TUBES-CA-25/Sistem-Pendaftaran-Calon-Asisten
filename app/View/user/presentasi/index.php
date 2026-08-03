<?php
/**
 * Presentasi View
 *
 * Data yang diterima dari controller:
 * @var array $results - Data presentasi user
 * @var bool $biodataStatus - Status biodata
 * @var bool $berkasStatus - Status berkas
 * @var bool $absensiTesTertulis - Status absensi tes tertulis
 * @var bool $pptStatus - Status PPT
 */
$results = $results ?? [];
$biodataStatus = $biodataStatus ?? false;
$berkasStatus = $berkasStatus ?? false;
$absensiTesTertulis = $absensiTesTertulis ?? false;
$pptStatus = $pptStatus ?? false;
$canSubmitJudul = $biodataStatus && $absensiTesTertulis;
$canSubmitPpt = $biodataStatus && $absensiTesTertulis && $pptStatus;

/* --------------------------------------------------------------------- *
 * $results kini berisi SELURUH riwayat pengajuan (terbaru di atas), bukan
 * lagi satu baris. Kondisi form ditentukan oleh pengajuan TERBARU saja.
 * Bentuk lama (satu baris asosiatif) tetap ditangani agar view ini aman
 * bila dipanggil dengan data lama.
 * --------------------------------------------------------------------- */
$riwayatJudul = [];
if (!empty($results)) {
    $riwayatJudul = isset($results['judul']) ? [$results] : $results;
}
$pengajuanAktif = $riwayatJudul[0] ?? null;

/**
 * Status pengajuan judul:
 *   diterima -> is_accepted = 1
 *   ditolak  -> is_revisi   = 1
 *   menunggu -> selain itu (baru diajukan, belum dinilai admin)
 */
function statusJudul(?array $row): string {
    if (!$row) return 'kosong';
    if ((int) ($row['is_accepted'] ?? 0) === 1) return 'diterima';
    if ((int) ($row['is_revisi'] ?? 0) === 1)  return 'ditolak';
    return 'menunggu';
}

$statusAktif = statusJudul($pengajuanAktif);
?>

<!-- Page Header -->
<?php
    $title = 'Presentasi';
    $subtitle = 'Submit judul dan file presentasi';
    $icon = 'bx bx-chalkboard';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Form Card -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-file-earmark-slides text-blue-600"></i>Submit Presentasi
                    </h5>
                </div>
                <div class="flex-1">
                    <?php if (!$biodataStatus): ?>
                        <div class="flex items-center gap-2.5 p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div class="font-semibold">Lengkapi biodata terlebih dahulu!</div>
                        </div>
                    <?php elseif (!$berkasStatus): ?>
                        <div class="flex items-center gap-2.5 p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div class="font-semibold">Lengkapi berkas terlebih dahulu!</div>
                        </div>
                    <?php elseif (!$absensiTesTertulis): ?>
                        <div class="flex items-center gap-2.5 p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div class="font-semibold">Anda belum mengikuti tes tertulis!</div>
                        </div>
                    <?php elseif ($statusAktif === 'menunggu'): ?>
                        <!-- Sudah mengajukan, menunggu penilaian admin. -->
                        <div class="flex items-start gap-3 p-5 rounded-xl bg-amber-50 border border-amber-100 text-amber-800" role="alert">
                            <i class="bi bi-hourglass-split text-xl shrink-0"></i>
                            <div>
                                <h6 class="font-bold mb-1">Menunggu Verifikasi</h6>
                                <p class="text-xs opacity-90 leading-relaxed">
                                    Judul <strong>&ldquo;<?= htmlspecialchars($pengajuanAktif['judul'] ?? '-') ?>&rdquo;</strong>
                                    sudah diajukan dan sedang ditinjau admin. Anda dapat mengajukan judul
                                    baru bila judul ini ditolak.
                                </p>
                            </div>
                        </div>

                    <?php elseif ($statusAktif === 'kosong' || $statusAktif === 'ditolak'): ?>
                        <?php if ($statusAktif === 'ditolak'): ?>
                            <!-- Judul terakhir ditolak: tampilkan alasannya, lalu izinkan ajukan ulang. -->
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 mb-4" role="alert">
                                <i class="bi bi-x-circle-fill text-lg shrink-0"></i>
                                <div>
                                    <h6 class="font-bold mb-0.5 text-sm">Judul Sebelumnya Ditolak</h6>
                                    <p class="text-xs opacity-90 leading-relaxed">
                                        <?= !empty($pengajuanAktif['keterangan'])
                                            ? htmlspecialchars($pengajuanAktif['keterangan'])
                                            : 'Silakan ajukan judul baru.' ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Form Submit Judul -->
                        <form id="berkasPresentasiForm" class="space-y-5">
                            <div>
                                <label for="judul" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-pencil-square text-blue-600"></i>Judul Presentasi
                                </label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed" id="judul" name="judul" placeholder="Masukkan judul presentasi Anda" required <?php if (!$canSubmitJudul) echo 'disabled'; ?>>
                            </div>
                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" <?php if (!$canSubmitJudul) echo 'disabled'; ?>>
                                <i class="bi bi-send"></i>Submit Judul
                            </button>
                        </form>
                    <?php elseif ($statusAktif === 'diterima'): ?>
                        <!-- Form Submit PPT & Makalah -->
                        <div class="flex items-center gap-2.5 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm mb-4" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <div class="font-semibold">Judul Anda telah disetujui! Silahkan upload file.</div>
                        </div>

                        <form id="presentasiFormAccepted" enctype="multipart/form-data" class="space-y-5">
                            <div>
                                <label for="ppt" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-ppt text-blue-600"></i>File PPT
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" type="file" id="ppt" name="ppt" accept=".ppt,.pptx" required <?php if (!$canSubmitPpt) echo 'disabled'; ?>>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PPT, PPTX (Max 10MB)</span>
                            </div>

                            <div>
                                <label for="makalah" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-pdf text-blue-600"></i>Makalah
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" type="file" id="makalah" name="makalah" accept="application/pdf" required <?php if (!$canSubmitPpt) echo 'disabled'; ?>>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF (Max 2MB)</span>
                            </div>

                            <!-- Download Template -->
                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100/50 hover:bg-blue-100/70 transition duration-200 mb-6">
                                <a id="downloadFile1" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/Template-Laporan-Makalah.docx" download class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                        <i class="bx bx-file text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-blue-800 text-sm block">Download Template Makalah</span>
                                        <span class="text-[10px] text-slate-400 block font-medium">Gunakan template yang disediakan</span>
                                    </div>
                                </a>
                            </div>

                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" <?php if (!$canSubmitPpt) echo 'disabled'; ?>>
                                <i class="bi bi-upload"></i>Submit File
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- History Table Card -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-clock-history text-blue-600"></i>Hasil Submit Judul Presentasi
                    </h5>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-100 text-sm no-datatable" data-paginator="true" data-paginator-perpage="5">
                        <thead>
                            <tr>
                                <th class="dt-head-cell">No</th>
                                <th class="dt-head-cell">Judul</th>
                                <th class="dt-head-cell">Status</th>
                                <th class="dt-head-cell">Waktu</th>
                                <th class="dt-head-cell">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (!empty($riwayatJudul)): ?>
                                <?php
                                // $riwayatJudul sudah terurut terbaru di atas (ORDER BY id DESC),
                                // jadi penomoran dimulai dari yang paling baru.
                                $i = 1;
                                foreach ($riwayatJudul as $row):
                                    $status = statusJudul($row);
                                    $keterangan = $row['keterangan'] ?? '';
                                    if ($keterangan === '') {
                                        $keterangan = [
                                            'diterima' => 'Silakan submit PPT dan makalah.',
                                            'ditolak'  => 'Judul ditolak, ajukan judul baru.',
                                            'menunggu' => 'Menunggu peninjauan admin.',
                                        ][$status] ?? '-';
                                    }
                                ?>
                                    <tr>
                                        <td class="px-4 py-3 text-slate-500 font-medium text-xs"><?= $i ?></td>
                                        <td class="px-4 py-3 text-slate-700 font-bold text-xs">
                                            <span class="break-words"><?= htmlspecialchars($row['judul'] ?? '-') ?></span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <?php if ($status === 'diterima'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                    <i class="bi bi-check-circle-fill"></i>Diterima
                                                </span>
                                            <?php elseif ($status === 'ditolak'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                                    <i class="bi bi-x-circle-fill"></i>Ditolak
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                                    <i class="bi bi-clock-fill"></i>Menunggu
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-slate-500 font-medium text-xs"><?= htmlspecialchars($row['created_at'] ?? '-') ?></td>
                                        <td class="px-4 py-3 text-slate-500 text-xs font-medium"><?= htmlspecialchars($keterangan) ?></td>
                                    </tr>
                                <?php $i++;
                                endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        <i class="bi bi-inbox text-4xl mb-2 block opacity-50"></i>
                                        <span class="text-xs font-medium">Tidak ada data untuk ditampilkan</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/user/presentasi.js"></script>


