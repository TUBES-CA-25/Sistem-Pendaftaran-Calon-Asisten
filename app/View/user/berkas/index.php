<?php
/**
 * Upload Berkas View
 *
 * Data yang diterima dari controller:
 * @var array $res - Data berkas user
 * @var string $nama - Nama lengkap
 * @var bool $biodataStatus - Status biodata sudah lengkap atau belum
 * @var bool $isBerkasEmpty - Status berkas kosong atau tidak
 */
$res = $res ?? [];
$nama = $nama ?? 'Nama Lengkap';
$biodataStatus = $biodataStatus ?? false;
$isBerkasEmpty = $isBerkasEmpty ?? true;
?>

    <?php 
    // 1. Cek Status Penerimaan dari data $res (ambil data terbaru/pertama)
    $isAccepted = false;
    if (!empty($res) && isset($res[0]['accepted']) && $res[0]['accepted'] == 1) {
        $isAccepted = true;
    }
    ?>


<!-- Page Header -->
<?php
    $title = 'Upload Berkas';
    $subtitle = 'Upload dokumen pendaftaran Anda';
    $icon = 'bx bx-file';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Form Card -->
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-cloud-upload text-blue-600"></i>Upload Dokumen
                    </h5>
                </div>
                
                <div class="flex-1">
                    <?php if (!$biodataStatus): ?>
                        <div class="flex items-center gap-2.5 p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div class="font-semibold">Lengkapi biodata terlebih dahulu</div>
                        </div>

                    <?php elseif ($isAccepted): ?>
                        <div class="flex items-start gap-3 p-5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800" role="alert">
                            <i class="bi bi-check-circle-fill text-xl shrink-0"></i>
                            <div>
                                <h6 class="font-bold mb-1">Berkas Telah Diterima!</h6>
                                <p class="text-xs opacity-90 leading-relaxed">Seluruh berkas persyaratan Anda telah diverifikasi dan disetujui. Anda tidak perlu mengunggah ulang.</p>
                            </div>
                        </div>

                    <?php else: ?>
                        <form id="berkasForm" enctype="multipart/form-data" class="space-y-5">
                            <div>
                                <label for="foto" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-image text-blue-600"></i>Foto 3x4
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PNG, JPG, JPEG</span>
                            </div>

                            <div>
                                <label for="cv" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-text text-blue-600"></i>CV
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="cv" name="cv" accept="application/pdf" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF</span>
                            </div>

                            <div>
                                <label for="transkrip" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-bar-graph text-blue-600"></i>Transkrip Nilai
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="transkrip" name="transkrip" accept="application/pdf" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF</span>
                            </div>

                            <div>
                                <label for="suratpernyataan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-check text-blue-600"></i>Surat Pernyataan
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="suratpernyataan" name="suratpernyataan" accept="application/pdf" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF</span>
                            </div>

                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100/50 hover:bg-blue-100/70 transition duration-200 mb-6">
                                <a id="downloadFile1" href="#" download class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                        <i class="bx bx-file text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-blue-800 text-sm block">Download Template CV</span>
                                        <span class="text-[10px] text-slate-400 block font-medium">Gunakan template yang disediakan</span>
                                    </div>
                                </a>
                            </div>

                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2">
                                <i class="bi bi-upload"></i>Submit Berkas
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- History Table Card -->
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-clock-history text-blue-600"></i>Riwayat Submit Berkas
                    </h5>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead>
                            <tr> 
                                <th class="dt-head-cell">No</th>                                   
                                <th class="dt-head-cell">Tanggal</th>
                                <th class="dt-head-cell">Nama</th>
                                <th class="dt-head-cell">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (!$isBerkasEmpty && !empty($res)): $nomor = 0; ?>
                                <?php foreach ($res as $result): $nomor++; ?>                                    
                                    <tr>                                    
                                        <td class="px-4 py-3 text-slate-500 font-medium text-xs"><?= $nomor ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-slate-500 font-medium text-xs"><?= $result['created_at'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-slate-700 font-semibold text-xs"><?= htmlspecialchars($nama) ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <?php if ($result['accepted'] == 1): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                    <i class="bi bi-check-circle-fill"></i>Terverifikasi
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                                    <i class="bi bi-clock-fill"></i>Menunggu
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                        <i class="bi bi-inbox text-4xl mb-2 block opacity-50"></i>
                                        <span class="text-xs font-medium">Belum ada data berkas</span>
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

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/berkas.js"></script>


