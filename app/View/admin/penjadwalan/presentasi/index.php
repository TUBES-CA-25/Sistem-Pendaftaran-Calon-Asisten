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
        require_once __DIR__ . '/../../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto pt-0 pb-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">

            <?php /* Tombol dipindah MASUK ke dalam kartu tabel + class vp-custom-button.
                     VanillaPaginator (app.js) memindahkannya ke baris kontrol di samping
                     kotak "Cari data..." lalu melepas class "hidden"-nya.

                     Letak elemen ini penting: paginator hanya memindai tombol di dalam
                     table.closest('div').parentElement - yaitu kartu ini. Kalau ditaruh
                     di luar kartu, tombol tidak ditemukan dan tetap tersembunyi.

                     #btnAddJadwal dipertahankan: presentasi.js mengikatnya lewat
                     dom.on (delegasi di document), jadi pemindahan DOM ini aman. */ ?>
            <button class="vp-custom-button hidden px-5 py-2.5 bg-gradient-to-r from-primary to-secondary hover:from-primary-hover hover:to-secondary-hover text-white font-semibold text-sm rounded-xl transition items-center gap-2 shadow-md shadow-blue-500/10 whitespace-nowrap" type="button" id="btnAddJadwal">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Jadwal</span>
            </button>

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
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-3">
                                        <i class="bi bi-easel"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Jadwal Presentasi</h4>
                                    <p class="text-slate-500 text-xs">Klik <span class="font-semibold text-blue-600">Tambah Jadwal</span> untuk menjadwalkan presentasi peserta.</p>
                                </td>
                            </tr>
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="addJadwalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-[560px] scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-2xl">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-calendar-plus text-lg"></i>Tambah Jadwal Presentasi</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6 max-h-[70vh] overflow-y-auto">
                <form id="formAddJadwal" method="POST" action="javascript:void(0);" class="space-y-4">
                    <!--
                        Penjadwalan massal. Beberapa peserta dimasukkan ke satu
                        daftar, lalu tiap peserta mendapat slot berurutan
                        (mulai + n x durasi) - presentasi dinilai satu per satu,
                        tidak serentak seperti wawancara.
                    -->
                    <div>
                        <label for="selectMahasiswa" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa:</label>
                        <div class="flex gap-2">
                            <select class="flex-1 min-w-0 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="selectMahasiswa"><option value="">-- Loading --</option></select>
                            <button type="button" id="btnTambahKeDaftar" class="shrink-0 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition flex items-center gap-1.5">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Tambahkan satu per satu; urutan daftar menentukan urutan waktu presentasi.</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700">Daftar Peserta:</label>
                            <span id="jumlahPesertaTerpilih" class="text-xs font-bold text-slate-500">0 peserta</span>
                        </div>
                        <ul id="daftarPesertaTerpilih" class="list-none p-0 m-0 space-y-2 max-h-52 overflow-y-auto">
                            <li id="daftarPesertaKosong" class="text-center text-xs text-slate-400 py-4 border border-dashed border-slate-200 rounded-xl">
                                Belum ada peserta dipilih
                            </li>
                        </ul>
                    </div>

                    <div>
                        <label for="selectRuangan" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="selectRuangan" required><option value="">-- Loading --</option></select>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label for="inputTanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                            <input type="date" min="<?= date('Y-m-d') ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputTanggal" required>
                        </div>
                        <div>
                            <label for="inputWaktu" class="block text-sm font-semibold text-slate-700 mb-2">Mulai:</label>
                            <input type="time" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputWaktu" required>
                        </div>
                        <div>
                            <label for="inputDurasi" class="block text-sm font-semibold text-slate-700 mb-2">Durasi:</label>
                            <div class="relative">
                                <input type="number" min="1" max="240" value="20" class="w-full pl-3 pr-12 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputDurasi" required>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">mnt</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pratinjau slot supaya admin tahu persis jam tiap peserta sebelum menyimpan -->
                    <div id="pratinjauSlot" class="hidden rounded-xl bg-blue-50/60 border border-blue-100 p-3">
                        <p class="text-xs font-bold text-blue-800 mb-2 flex items-center gap-1.5"><i class="bi bi-clock-history"></i> Pembagian Waktu</p>
                        <ul id="isiPratinjauSlot" class="list-none p-0 m-0 space-y-1 text-xs text-slate-600 max-h-32 overflow-y-auto"></ul>
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="updateJadwalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-2xl">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-pencil-square text-lg"></i>Update Jadwal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6">
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
                            <input type="date" min="<?= date('Y-m-d') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editTanggal" required>
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

<script src="<?= APP_URL ?>/Assets/js/admin/penjadwalan/presentasi.js?v=<?= time() ?>"></script>

