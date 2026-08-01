<?php
/**
 * Wawancara Admin View
 *
 * Data yang diterima dari controller:
 * @var array $wawancara - Data wawancara
 * @var array $mahasiswaList - Daftar mahasiswa
 * @var array $ruanganList - Daftar ruangan
 */
$wawancara = $wawancara ?? [];
$mahasiswaList = $mahasiswaList ?? [];
$ruanganList = $ruanganList ?? [];
$colors = ['#2f66f6'];
?>

<?php
    $title = 'Kelola Wawancara';
    $subtitle = 'Kelola jadwal wawancara peserta';
    $icon = 'bi bi-calendar-event';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<div class="max-w-7xl mx-auto px-4 pt-0 pb-6">

    <!-- Tombol aksi (dulu tersembunyi menunggu initComplete DataTables) -->
    <div class="flex justify-end mb-4">
        <button class="inline-flex px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl items-center gap-2 transition shadow-sm border-0" type="button" data-modal-open="#addJadwalModal">
            <i class="bi bi-plus-circle"></i> Tambah Jadwal
        </button>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table id="wawancaraMahasiswa" class="min-w-full align-middle text-sm text-left no-datatable" data-paginator="true" data-paginator-perpage="10">
                <thead>
                    <tr class="">
                        <th class="dt-head-cell text-center" style="width: 5%;">No</th>
                        <th class="dt-head-cell" style="width: 25%;">Nama Lengkap</th>
                        <th class="dt-head-cell" style="width: 15%;">Stambuk</th>
                        <th class="dt-head-cell" style="width: 20%;">Kegiatan</th>
                        <th class="dt-head-cell" style="width: 10%;">Ruangan</th>
                        <th class="dt-head-cell" style="width: 10%;">Tanggal</th>
                        <th class="dt-head-cell" style="width: 10%;">Waktu</th>
                        <th class="dt-head-cell text-center" style="width: 5%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="bg-white divide-y divide-slate-100">
                    <?php if (empty($wawancara)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <i class="bi bi-inbox text-5xl block mb-3 opacity-55"></i>
                                <span class="font-semibold text-sm">Belum ada data jadwal wawancara</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($wawancara as $row): ?>
                            <tr class="dt-body-row border-b border-slate-100 hover:bg-slate-50 transition" data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                                <td class="text-center py-4 px-4"><?= $i ?></td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= \App\Controllers\HomeController::getUserPhotoPath($row['foto'] ?? 'default.png') ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                        <div>
                                            <div class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['stambuk']) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['jenis_wawancara']) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['ruangan']) ?></td>
                                <td class="py-4 px-4"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['waktu']) ?></td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors border-0 open-update" 
                                                data-modal-open="#updateWawancaraModal"
                                                data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                data-stambuk="<?= htmlspecialchars($row['stambuk']) ?>"
                                                data-ruangan="<?= htmlspecialchars($row['ruangan']) ?>"
                                                data-ruangan_id="<?= $row['id_ruangan'] ?>"
                                                data-jeniswawancara="<?= htmlspecialchars($row['jenis_wawancara']) ?>"
                                                data-waktu="<?= htmlspecialchars($row['waktu']) ?>"
                                                data-tanggal="<?= htmlspecialchars($row['tanggal']) ?>"
                                                data-id="<?= $row['id'] ?>"
                                                title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors border-0 btn-delete-wawancara" 
                                                data-id="<?= $row['id'] ?>"
                                                title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Jadwal Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 modal-wawancara" id="addJadwalModal" aria-labelledby="addJadwalModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg" id="addJadwalModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="addJadwalForm" method="POST" action="javascript:void(0);">
                <div class="p-6 space-y-4">
                    <div>
                        <label for="mahasiswa" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pilih Mahasiswa</label>
                        <div class="flex gap-2 mb-3">
                             <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="mahasiswa">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                    <option value="<?= $mahasiswa['id'] ?>">
                                        <?= $mahasiswa['stambuk'] ?> - <?= $mahasiswa['nama_lengkap'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm transition shrink-0 border-0" id="addMahasiswaButton">Tambah</button>
                        </div>
                        <ul class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden shadow-sm" id="selectedMahasiswaList" style="max-height: 150px; overflow-y: auto;">
                        </ul>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="ruangan" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ruangan</label>
                            <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="ruangan" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php foreach ($ruanganList as $ruangan): ?>
                                    <option value="<?= $ruangan['id'] ?>">
                                        <?= $ruangan['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="wawancara" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Kegiatan</label>
                            <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="wawancara" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                                <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal</label>
                            <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="tanggal" required>
                        </div>
                        <div>
                            <label for="waktu" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu</label>
                            <input type="time" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="waktu" required>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-modal-close>Batal</button>
                    <button type="submit" form="addJadwalForm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Tambah Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Wawancara Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 modal-wawancara" id="updateWawancaraModal" aria-labelledby="updateWawancaraModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg" id="updateWawancaraModalLabel"><i class="bi bi-pencil-square me-2"></i>Update Wawancara</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="updateWawancaraForm" method="POST" action="javascript:void(0);">
                <div class="p-6 space-y-4">
                    <input type="hidden" id="updateWawancaraId">
                    <div>
                        <label for="updateRuangan" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ruangan</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateRuangan" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $ruangan): ?>
                                <option value="<?= $ruangan['id'] ?>">
                                    <?= $ruangan['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="updateTanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateTanggal" required>
                    </div>
                    <div>
                        <label for="updateWaktu" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu</label>
                        <input type="time" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateWaktu" required>
                    </div>
                    <div>
                        <label for="updateJenisWawancara" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Wawancara</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateJenisWawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
                            <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                        </select>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-modal-close>Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Update Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Assets/js/admin/wawancara.js?v=<?= time() ?>"></script>

