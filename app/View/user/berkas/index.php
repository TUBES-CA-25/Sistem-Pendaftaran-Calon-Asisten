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

<main class="max-w-7xl mx-auto pb-8">
    <!-- 5 kolom: Upload Dokumen 2/5 (dipersempit), Riwayat 3/5 (diperlebar)
         supaya tabel riwayat punya ruang lebih lega. -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Upload Form Card -->
        <div class="lg:col-span-2">
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
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PNG, JPG, JPEG &middot; maks 5 MB</span>
                                <p class="hidden mt-1.5 text-[11px] font-semibold text-red-600 items-center gap-1" data-error-for="foto"><i class="bi bi-exclamation-circle-fill"></i><span></span></p>
                            </div>

                            <div>
                                <label for="cv" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-text text-blue-600"></i>CV
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="cv" name="cv" accept="application/pdf" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF &middot; maks 5 MB</span>
                                <p class="hidden mt-1.5 text-[11px] font-semibold text-red-600 items-center gap-1" data-error-for="cv"><i class="bi bi-exclamation-circle-fill"></i><span></span></p>
                            </div>

                            <div>
                                <label for="transkrip" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-bar-graph text-blue-600"></i>Transkrip Nilai
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="transkrip" name="transkrip" accept="application/pdf" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF &middot; maks 5 MB</span>
                                <p class="hidden mt-1.5 text-[11px] font-semibold text-red-600 items-center gap-1" data-error-for="transkrip"><i class="bi bi-exclamation-circle-fill"></i><span></span></p>
                            </div>

                            <div>
                                <label for="suratpernyataan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-file-earmark-check text-blue-600"></i>Surat Pernyataan
                                </label>
                                <input class="w-full px-3 py-2 text-sm text-slate-500 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition duration-150" type="file" id="suratpernyataan" name="suratpernyataan" accept="application/pdf" required>
                                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Format: PDF &middot; maks 5 MB</span>
                                <p class="hidden mt-1.5 text-[11px] font-semibold text-red-600 items-center gap-1" data-error-for="suratpernyataan"><i class="bi bi-exclamation-circle-fill"></i><span></span></p>
                            </div>

                            <?php /* Berkas template yang bisa diunduh peserta.
                                    href ditulis langsung di markup - dulu dikosongkan ('#')
                                    lalu diisi berkas.js, sehingga saat nama berkasnya
                                    berganti tautannya diam-diam mati tanpa terlihat di
                                    view. Nama berkas dilewatkan rawurlencode karena
                                    mengandung spasi. */ ?>
                            <?php
                                $templateBerkas = [
                                    [
                                        'berkas' => 'Template CV.docx',
                                        'judul'  => 'Download Template CV',
                                        'ket'    => 'Gunakan template yang disediakan',
                                        'ikon'   => 'bx bx-file',
                                    ],
                                    [
                                        'berkas' => 'Surat Pernyataan Orang Tua.docx',
                                        'judul'  => 'Download Surat Pernyataan Orang Tua',
                                        'ket'    => 'Isi, tanda tangani, lalu unggah dalam bentuk PDF',
                                        'ikon'   => 'bx bx-file-blank',
                                    ],
                                ];
                            ?>
                            <div class="space-y-2 mb-6">
                                <?php foreach ($templateBerkas as $tpl): ?>
                                <a href="<?= APP_URL ?>/Assets/Downloads/<?= rawurlencode($tpl['berkas']) ?>" download
                                   class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100/50 hover:bg-blue-100/70 transition duration-200">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                        <i class="<?= $tpl['ikon'] ?> text-white text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-bold text-blue-800 text-sm block"><?= htmlspecialchars($tpl['judul']) ?></span>
                                        <span class="text-[10px] text-slate-400 block font-medium"><?= htmlspecialchars($tpl['ket']) ?></span>
                                    </div>
                                    <i class="bi bi-download text-blue-500 ml-auto shrink-0"></i>
                                </a>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2">
                                <i class="bi bi-upload"></i>Submit Berkas
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Riwayat Submit Berkas -->
        <div class="lg:col-span-3">

            <!-- h-full: samakan tinggi frame dengan kartu Upload di sebelah kiri.
                 Tanpa ini kartu hanya setinggi isinya sehingga terlihat pendek. -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-clock-history text-blue-600"></i>Riwayat Submit Berkas
                    </h5>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-100 text-sm no-datatable" data-paginator="true" data-paginator-perpage="5">
                        <thead>
                            <tr>
                                <th class="dt-head-cell">No</th>
                                <th class="dt-head-cell">Tanggal</th>
                                <th class="dt-head-cell">Nama</th>
                                <th class="dt-head-cell">Status</th>
                                <th class="dt-head-cell text-center">Aksi</th>
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
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center justify-center">
                                                <?php if ($result['accepted'] == 1): ?>
                                                    <!-- Sudah diverifikasi: hapus dikunci. Tombol tetap
                                                         ditampilkan (disabled) agar peserta paham kenapa
                                                         tidak bisa menghapus, bukan sekadar hilang. -->
                                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-50 text-slate-300 cursor-not-allowed"
                                                          title="Berkas sudah diverifikasi, tidak dapat dihapus">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </span>
                                                <?php else: ?>
                                                    <button type="button"
                                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 btn-hapus-submit"
                                                            title="Hapus data berkas ini">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
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

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/user/berkas.js"></script>


