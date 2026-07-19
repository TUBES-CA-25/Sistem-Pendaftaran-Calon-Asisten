<?php
/**
 * Ruangan View - Card Layout with Details & Interview
 * 
 * @var array $ruanganList - Daftar ruangan
 */
$ruanganList = $ruanganList ?? [];
?>

<main>
    <!-- SECTION: LIST VIEW -->
    <div id="ruanganListSection">
        <?php
            $title = 'Ruangan Seleksi';
            $subtitle = 'Kelola data ruangan untuk kegiatan seleksi calon asisten';
            $icon = 'bi bi-buildings-fill';
            require_once __DIR__ . '/../../templates/components/PageHeader.php';
        ?>

        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Unified Table Container with Controls -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6 relative">
                
                <!-- Hidden Custom Button for VanillaPaginator -->
                <button class="vp-custom-button hidden px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition items-center gap-2 shadow-md shadow-blue-500/10 btn-add-room" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Tambah Ruangan</span>
                </button>

                <div class="overflow-x-auto">
                    <table class="min-w-full align-middle text-sm text-left no-datatable" id="ruanganTable" data-paginator="true" data-paginator-perpage="10">
                        <thead>
                            <tr>
                                <th class="dt-head-cell text-center" style="width: 10%;">No</th>
                                <th class="dt-head-cell">Nama Ruangan</th>
                                <th class="dt-head-cell text-center" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ruanganTableBody">
                            <?php if (empty($ruanganList)) { ?>
                                <tr>
                                    <td colspan="3" class="text-center py-12">
                                         <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mx-auto mb-3">
                                            <i class="bi bi-buildings"></i>
                                         </div>
                                         <h4 class="text-base font-bold text-slate-800 mb-1">Belum ada Ruangan</h4>
                                         <p class="text-slate-500 text-xs mb-4">Mulai dengan menambahkan ruangan baru untuk seleksi.</p>
                                         <button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-xs rounded-xl transition shadow-sm shadow-blue-500/10 btn-add-room" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Ruangan
                                         </button>
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <?php $i = 1; foreach ($ruanganList as $ruangan) { ?>
                                    <tr class="dt-body-row room-item border-b border-slate-100 hover:bg-slate-50 transition-colors" data-id="<?= $ruangan['id'] ?>" data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                                        <td class="text-center py-4 px-4 font-semibold text-slate-600"><?= $i++ ?></td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0 border border-blue-100">
                                                    <i class="bi bi-buildings-fill text-lg"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800 text-base"><?= htmlspecialchars($ruangan['nama']) ?></div>
                                                    <div class="text-[10px] font-bold text-slate-400 tracking-wider mt-0.5 uppercase"><i class="bi bi-geo-alt-fill text-[9px] me-1"></i>Ruangan Seleksi</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-blue-50 hover:bg-blue-100 text-blue-600 btn-edit-room" 
                                                        title="Ubah Nama"
                                                        data-id="<?= $ruangan['id'] ?>"
                                                        data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 btn-delete-room" 
                                                        title="Hapus"
                                                        data-id="<?= $ruangan['id'] ?>"
                                                        data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modals -->
<div class="modal fade" id="tambahRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2"><i class="bi bi-plus-circle text-lg"></i>Tambah Ruangan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <form id="tambahRuanganForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Ruangan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="namaRuangan" name="namaRuangan" placeholder="Contoh: Lab RPL 1" required>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-blue-500/10">Simpan Ruangan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2"><i class="bi bi-pencil-square text-lg"></i>Ubah Nama Ruangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <form id="updateRuanganForm" class="space-y-4">
                    <input type="hidden" id="updateRuanganId">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Ruangan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="updateNamaRuangan" name="updateNamaRuangan" required>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-blue-500/10">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Load Custom Script -->
<script src="<?= APP_URL ?>/Assets/js/rooms.js?v=2.0"></script>
