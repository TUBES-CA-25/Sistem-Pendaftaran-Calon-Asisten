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
            <!-- Controls Toolbar -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div class="relative w-full sm:w-72">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Cari ruangan...">
                </div>
                <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2 shadow-md shadow-blue-500/10 btn-add-room" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Tambah Ruangan</span>
                </button>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="ruanganGrid">
                <?php if (empty($ruanganList)) { ?>
                    <div class="col-span-full max-w-md mx-auto py-12 text-center">
                         <div class="w-20 h-20 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mx-auto mb-4">
                            <i class="bi bi-buildings"></i>
                         </div>
                         <h4 class="text-lg font-bold text-slate-800 mb-1">Belum ada Ruangan</h4>
                         <p class="text-slate-500 text-sm mb-6">Mulai dengan menambahkan ruangan baru untuk seleksi.</p>
                         <div>
                             <button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Sekarang
                             </button>
                         </div>
                    </div>
                <?php } else { ?>
                    <?php foreach ($ruanganList as $ruangan) { ?>
                        <div class="room-item" data-id="<?= $ruangan['id'] ?>" data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full p-6 relative flex flex-col justify-between hover:shadow-md transition duration-200">
                                <!-- Top Accent Bar -->
                                <div class="absolute top-0 left-0 w-full h-[5px] bg-gradient-to-r from-blue-500 to-indigo-600"></div>

                                <!-- Room Info -->
                                <div class="text-center flex flex-col items-center">
                                    <!-- Icon -->
                                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 text-3xl mt-2">
                                        <i class="bi bi-buildings-fill"></i>
                                    </div>
                                    <h5 class="text-base font-bold text-slate-800 mb-1 truncate max-w-full">
                                        <?= htmlspecialchars($ruangan['nama']) ?>
                                    </h5>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider mb-4">
                                        <i class="bi bi-geo-alt-fill text-[9px]"></i> Ruangan Seleksi
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 mt-4 shrink-0">
                                    <button class="flex-grow py-2 px-3 border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 btn-edit-room"
                                            data-id="<?= $ruangan['id'] ?>"
                                            data-name="<?= htmlspecialchars($ruangan['nama']) ?>"
                                            title="Ubah Nama">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button class="flex-grow py-2 px-3 border border-red-500 text-red-500 hover:bg-red-50 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 btn-delete-room"
                                            data-id="<?= $ruangan['id'] ?>"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
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
