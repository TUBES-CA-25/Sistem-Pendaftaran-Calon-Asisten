<?php
/**
 * Written Test Schedule View - Student Based
 * @var array $data
 */
$jadwalTesList = $data['jadwalTesList'] ?? [];
$mahasiswaList = $data['mahasiswaList'] ?? [];
$ruanganList = $data['ruanganList'] ?? [];
$bankSoalList = $data['bankSoalList'] ?? [];
?>

<main>
    <?php
        $title = 'Jadwal Tes Tertulis';
        $subtitle = 'Manajemen jadwal ujian tertulis mahasiswa per individu';
        $icon = 'bi bi-calendar-event';
        require_once __DIR__ . '/../../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto pt-0 pb-6">
        <!-- Student Schedule Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">

            <?php /* Tombol dipindah MASUK ke dalam kartu tabel dan diberi class
                     vp-custom-button. VanillaPaginator (app.js) memindahkannya ke
                     baris kontrol di samping kotak "Cari data..." saat membangun
                     toolbar, lalu melepas class "hidden"-nya.

                     Posisi elemen ini penting: paginator hanya memindai tombol di
                     dalam table.closest('div').parentElement - yaitu kartu ini.
                     Kalau tombol ditaruh di luar kartu, ia tidak akan ditemukan
                     dan tetap tersembunyi. */ ?>
            <button class="vp-custom-button hidden px-5 py-2.5 bg-gradient-to-r from-primary to-secondary hover:from-primary-hover hover:to-secondary-hover text-white font-semibold text-sm rounded-xl transition items-center gap-2 shadow-md shadow-blue-500/10 whitespace-nowrap" type="button" data-modal-open="#addJadwalModal">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Jadwal</span>
            </button>

            <div class="overflow-x-auto">
                <table class="min-w-full align-middle text-sm text-left no-datatable" id="jadwalTable" data-paginator="true" data-paginator-perpage="10">
                    <thead>
                        <tr class="">
                            <th class="dt-head-cell text-center" style="width: 5%;">No</th>
                            <th class="dt-head-cell text-left" style="width: 25%;">Nama Lengkap</th>
                            <th class="dt-head-cell text-left" style="width: 15%;">Stambuk</th>
                            <th class="dt-head-cell text-left" style="width: 20%;">Kegiatan</th>
                            <th class="dt-head-cell text-left" style="width: 15%;">Ruangan</th>
                            <th class="dt-head-cell text-left" style="width: 10%;">Tanggal</th>
                            <th class="dt-head-cell text-left" style="width: 10%;">Waktu</th>
                            <th class="dt-head-cell text-center" style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="dt-tbody">
                        <?php if (empty($jadwalTesList)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-3">
                                        <i class="bi bi-calendar-x"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Jadwal Tes</h4>
                                    <p class="text-slate-500 text-xs">Klik <span class="font-semibold text-blue-600">Tambah Jadwal</span> untuk menjadwalkan tes tertulis peserta.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($jadwalTesList as $row): ?>
                                <tr class="dt-body-row" data-id="<?= $row['id'] ?>">
                                    <td class="text-center py-4 px-4"><?= $i++ ?></td>
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
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['kegiatan']) ?></td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['ruangan']) ?></td>
                                    <td class="py-4 px-4"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="py-4 px-4"><?= htmlspecialchars($row['waktu']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 open-edit" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                    data-stambuk="<?= htmlspecialchars($row['stambuk']) ?>"
                                                    data-ruangan="<?= $row['ruangan'] ?>"
                                                    data-kegiatan="<?= htmlspecialchars($row['kegiatan']) ?>"
                                                    data-tanggal="<?= $row['tanggal'] ?>"
                                                    data-waktu="<?= $row['waktu'] ?>"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 delete-schedule" data-id="<?= $row['id'] ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-cyan-50 hover:bg-cyan-100 text-cyan-600 reset-exam" 
                                                    data-id="<?= $row['id_mahasiswa'] ?>" 
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                    title="Reset Pengerjaan">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-2xl">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-calendar-plus text-lg"></i>Tambah Jadwal Tes</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6">
                <form id="addJadwalForm" class="space-y-4">
                    <div>
                        <label for="mahasiswaSelect" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa:</label>
                        <div class="flex gap-2">
                            <select class="flex-1 min-w-0 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="mahasiswaSelect">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['stambuk'] ?> - <?= $m['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="shrink-0 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition flex items-center gap-1.5" id="addMhsToList"><i class="bi bi-plus-lg"></i> Tambah</button>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Tambahkan satu per satu; semua peserta terpilih dijadwalkan pada waktu yang sama.</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700">Daftar Peserta:</label>
                            <span id="jumlahMhsTerpilih" class="text-xs font-bold text-slate-500">0 peserta</span>
                        </div>
                        <ul class="list-none p-0 m-0 space-y-2 max-h-40 overflow-y-auto" id="selectedMhsList">
                            <li id="daftarMhsKosong" class="text-center text-xs text-slate-400 py-4 border border-dashed border-slate-200 rounded-xl">
                                Belum ada peserta dipilih
                            </li>
                        </ul>
                    </div>
                    <?php /* Field "Kegiatan" dihapus: tab ini khusus tes tertulis dan tidak
                            ada pilihan lain. Nilainya ditetapkan di server lewat konstanta
                            JadwalTesController::JENIS_TES_TERTULIS. */ ?>
                    <div>
                        <label for="ruanganSelect" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="ruanganSelect" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tanggalInput" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                            <input type="date" min="<?= date('Y-m-d') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="tanggalInput" required>
                        </div>
                        <div>
                            <label for="waktuInput" class="block text-sm font-semibold text-slate-700 mb-2">Waktu</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="waktuInput" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="submit" form="addJadwalForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Jadwal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="updateJadwalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-2xl">
                <h5 class="font-bold flex items-center gap-2"><i class="bi bi-pencil-square text-lg"></i>Update Jadwal Tes</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6">
                <form id="updateJadwalForm" class="space-y-4">
                    <input type="hidden" id="editId">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Mahasiswa</label>
                        <p id="editMhsInfo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-semibold mb-0"></p>
                    </div>
                    <?php /* Field "Kegiatan" dihapus - lihat catatan di modal tambah. */ ?>
                    <div>
                        <label for="editRuangan" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editRuangan" required>
                            <?php foreach ($ruanganList as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="editTanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                            <input type="date" min="<?= date('Y-m-d') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editTanggal" required>
                        </div>
                        <div>
                            <label for="editWaktu" class="block text-sm font-semibold text-slate-700 mb-2">Waktu</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="submit" form="updateJadwalForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10">Update Jadwal</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Assets/js/admin/penjadwalan/tes.js?v=<?= time() ?>"></script>

