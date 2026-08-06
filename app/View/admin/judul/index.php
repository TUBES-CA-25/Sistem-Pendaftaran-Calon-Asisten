<?php
/**
 * Pengajuan Judul Admin View
 *
 * @var array $mahasiswaList
 * @var array $mahasiswaAccStatus
 */
$mahasiswaList = $mahasiswaList ?? [];
?>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Pengajuan Judul';
        $subtitle = 'Validasi judul presentasi mahasiswa';
        $icon = 'bi bi-file-text';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto px-4 pt-0 pb-6">


        <?php if (empty($mahasiswaList)): ?>
            <div class="flex flex-col items-center justify-center py-16 text-slate-400 bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                <i class="bi bi-inbox text-6xl mb-4 text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Pengajuan</h3>
                <p class="text-sm max-w-sm text-slate-500">Data pengajuan judul akan muncul setelah mahasiswa mengajukan judul presentasi</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full align-middle text-sm text-left no-datatable" id="tablePengajuan" data-paginator="true" data-paginator-perpage="10">
                        <thead class="">
                            <tr>
                                <th class="dt-head-cell text-center" style="width: 5%;">No</th>
                                <th class="dt-head-cell" style="width: 25%;">Nama Lengkap</th>
                                <th class="dt-head-cell" style="width: 15%;">Stambuk</th>
                                <th class="dt-head-cell" style="width: 35%;">Judul Presentasi</th>
                                <th class="dt-head-cell text-center" style="width: 10%;">Status</th>
                                <th class="dt-head-cell text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="dt-tbody">
                            <?php $i = 1; foreach ($mahasiswaList as $row): ?>
                                <?php
                                    $statusValue = $row['is_accepted'] ?? 0;
                                    $isAccepted = ($statusValue == 1);
                                    $isRejected = ($statusValue == 2);
                                    $isPending = ($statusValue == 0);
                                    $hasSchedule = isset($row['has_schedule']) && $row['has_schedule'];
                                    
                                    if ($isRejected) {
                                        $badgeClass = 'bg-red-50 text-red-700 border border-red-100';
                                        $badgeText = 'Ditolak';
                                    } elseif ($isAccepted) {
                                        $badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                                        $badgeText = 'Diterima';
                                    } elseif ($hasSchedule) {
                                        $badgeClass = 'bg-blue-50 text-blue-700 border border-blue-100';
                                        $badgeText = 'Terjadwal';
                                    } else {
                                        $badgeClass = 'bg-slate-50 text-slate-600 border border-slate-200';
                                        $badgeText = 'Menunggu';
                                    }
                                ?>
                                <tr class="dt-body-row" data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                                    <td class="text-center py-4 px-4"><?= $i ?></td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= \App\Controllers\HomeController::getUserPhotoPath($row['foto'] ?? 'default.png') ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                            <div>
                                                <div class="font-bold text-slate-800"><?= htmlspecialchars($row['nama'] ?? '-') ?></div>
                                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                    <td class="text-center py-4 px-4">
                                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-lg <?= $badgeClass ?>"><?= $badgeText ?></span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-cyan-50 hover:bg-cyan-100 text-cyan-600 btn-detail-pengajuan"
                                                    data-nama="<?= htmlspecialchars($row['nama'] ?? '') ?>"
                                                    data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '-') ?>"
                                                    data-judul="<?= htmlspecialchars($row['judul'] ?? '-') ?>"
                                                    data-ppt="<?= htmlspecialchars($row['berkas']['ppt'] ?? '') ?>"
                                                    data-makalah="<?= htmlspecialchars($row['berkas']['makalah'] ?? '') ?>"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>"
                                                    data-status="<?= $statusValue ?>"
                                                    title="Lihat Detail"><i class="bi bi-eye"></i></button>

                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 btn-send-message"
                                                    data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>" title="Kirim Pesan"><i class="bi bi-chat-dots"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Detail -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="detailPengajuanModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-person-badge text-lg"></i>Detail Presentasi</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div><strong class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Nama:</strong> <p class="text-slate-800 font-semibold" id="detailNama">-</p></div>
                    <div><strong class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Stambuk:</strong> <p class="text-slate-800 font-semibold" id="detailStambuk">-</p></div>
                    <div><strong class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Judul:</strong> <p class="text-slate-700 font-medium leading-relaxed" id="detailJudul">-</p></div>
                    
                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-blue-600 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 shadow-sm" id="btnDownloadPpt"><i class="bi bi-file-earmark-ppt"></i> Download PPT</button>
                        <button class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-blue-600 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 shadow-sm" id="btnDownloadMakalah"><i class="bi bi-file-earmark-pdf"></i> Download Makalah</button>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-center gap-3">
                <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-emerald-500/10 flex items-center gap-2" id="btnModalAccept">
                    <i class="bi bi-check-circle"></i> Terima Judul
                </button>
                <button class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-red-500/10 flex items-center gap-2" id="btnModalReject">
                    <i class="bi bi-x-circle"></i> Tolak Judul
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Send Message -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="sendMessageModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-chat-dots text-lg"></i>Kirim Pesan Revisi</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6">
                <form id="formSendMessage">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pesan:</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="messageContent" rows="4" required placeholder="Tulis pesan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="submit" form="formSendMessage" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-send"></i> Kirim</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Assets/js/admin/judul.js?v=<?= time() ?>"></script>

