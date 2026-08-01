<?php
/**
 * Jadwal Presentasi Admin View
 *
 * @var array $jadwalPresentasi
 * @var array $ruanganList - Passed via JS/AJAX mainly, but useful if preloaded
 */
$jadwalPresentasi = $jadwalPresentasi ?? [];
?>

<main>
    <?php
        $title = 'Jadwal Presentasi';
        $subtitle = 'Kelola jadwal dan ruangan presentasi mahasiswa';
        $icon = 'bi bi-calendar-event';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto px-4 pt-0 pb-6">
        <!-- Tombol aksi (dulu tersembunyi menunggu initComplete DataTables) -->
        <div class="flex justify-end mb-4">
            <button class="inline-flex px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition items-center gap-2 shadow-md shadow-blue-500/10" id="btnAddJadwal">
                <i class="bi bi-plus-circle"></i> Tambah Jadwal
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full align-middle text-sm text-left no-datatable" id="tableJadwal" data-paginator="true" data-paginator-perpage="10">
                    <thead>
                        <tr class="">
                            <th class="dt-head-cell text-center" style="width: 5%;">No</th>
                            <th class="dt-head-cell" style="width: 20%;">Nama Lengkap</th>
                            <th class="dt-head-cell" style="width: 15%;">Stambuk</th>
                            <th class="dt-head-cell" style="width: 25%;">Judul</th>
                            <th class="dt-head-cell" style="width: 15%;">Ruangan</th>
                            <th class="dt-head-cell" style="width: 10%;">Tanggal</th>
                            <th class="dt-head-cell" style="width: 10%;">Waktu</th>
                            <th class="dt-head-cell text-center" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jadwalTableBody" class="dt-tbody">
                        <?php if (empty($jadwalPresentasi)): ?>
                            <tr><td colspan="8" class="text-center text-slate-400 py-10 font-medium">Belum ada jadwal presentasi</td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($jadwalPresentasi as $row): ?>
                                <tr class="dt-body-row">
                                    <td class="text-center py-4 px-4"><?= $i ?></td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= \App\Controllers\HomeController::getUserPhotoPath($row['foto'] ?? 'default.png') ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                            <div>
                                                <div class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '-') ?></div>
                                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                    <td class="py-4 px-4">
                                        <?= htmlspecialchars($row['ruangan'] ?? $row['nama_ruangan'] ?? '-') ?>
                                    </td>
                                    <td class="py-4 px-4"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['waktu']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 btn-edit-jadwal"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?>"
                                                    data-ruangan="<?= $row['id_ruangan'] ?>"
                                                    data-tanggal="<?= $row['tanggal'] ?>"
                                                    data-waktu="<?= $row['waktu'] ?>"
                                                    title="Edit"><i class="bi bi-pencil"></i></button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 btn-delete-jadwal"
                                                    data-id="<?= $row['id'] ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php $i++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Jadwal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="addJadwalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-calendar-plus text-lg"></i>Tambah Jadwal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6">
                <form id="formAddJadwal" method="POST" action="javascript:void(0);" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="selectMahasiswa" required><option value="">-- Loading --</option></select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="selectRuangan" required><option value="">-- Loading --</option></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="inputTanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                            <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputTanggal" required>
                        </div>
                        <div>
                            <label for="inputWaktu" class="block text-sm font-semibold text-slate-700 mb-2">Waktu:</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="submit" form="formAddJadwal" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Jadwal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="updateJadwalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-pencil-square text-lg"></i>Update Jadwal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6">
                <form id="formUpdateJadwal" method="POST" action="javascript:void(0);" class="space-y-4">
                    <input type="hidden" id="editId">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Mahasiswa:</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-semibold" id="editNama" readonly>
                    </div>
                    <div>
                        <label for="editRuangan" class="block text-sm font-semibold text-slate-700 mb-2">Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editRuangan" required></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="editTanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                            <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editTanggal" required>
                        </div>
                        <div>
                            <label for="editWaktu" class="block text-sm font-semibold text-slate-700 mb-2">Waktu:</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="submit" form="formUpdateJadwal" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Assets/js/admin/presentasi.js?v=<?= time() ?>"></script>

