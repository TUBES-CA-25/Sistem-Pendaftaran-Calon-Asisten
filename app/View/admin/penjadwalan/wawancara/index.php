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
    require_once __DIR__ . '/../../../templates/components/PageHeader.php';
?>

<main>
<div class="max-w-7xl mx-auto pt-0 pb-6">

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">

        <?php /* Tombol dipindah MASUK ke dalam kartu tabel + class vp-custom-button.
                 VanillaPaginator (app.js) memindahkannya ke baris kontrol di samping
                 kotak "Cari data..." lalu melepas class "hidden"-nya.

                 Letak elemen ini penting: paginator hanya memindai tombol di dalam
                 table.closest('div').parentElement - yaitu kartu ini. Kalau ditaruh
                 di luar kartu, tombol tidak ditemukan dan tetap tersembunyi. */ ?>
        <button class="vp-custom-button hidden px-5 py-2.5 bg-gradient-to-r from-primary to-secondary hover:from-primary-hover hover:to-secondary-hover text-white font-semibold text-sm rounded-xl transition items-center gap-2 shadow-md shadow-blue-500/10 whitespace-nowrap" type="button" data-modal-open="#addJadwalModal">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Jadwal</span>
        </button>

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
</main>

<!-- Add Jadwal Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 modal-wawancara" id="addJadwalModal" aria-labelledby="addJadwalModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-2xl gap-3">
                <h5 class="font-bold flex items-center gap-2" id="addJadwalModalLabel"><i class="bi bi-calendar-plus text-lg"></i>Tambah Jadwal Wawancara</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="addJadwalForm" method="POST" action="javascript:void(0);">
                <div class="p-6 space-y-4">
                    <?php /* Jenis Kegiatan sengaja diletakkan PALING ATAS.
                            Pilihan ini menentukan siapa yang layak muncul di daftar
                            mahasiswa - peserta yang sudah dijadwalkan Lab I tetap
                            harus terlihat saat menjadwalkan Lab II. Kalau field ini
                            di bawah, admin memilih peserta dari daftar yang belum
                            tersaring lalu harus mengulang. */ ?>
                    <div>
                        <label for="wawancara" class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kegiatan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="wawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
                            <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                        </select>
                        <p class="text-xs text-slate-400 mt-1.5">Pilih tahap wawancara terlebih dahulu - daftar mahasiswa menyesuaikan otomatis.</p>
                    </div>

                    <div>
                        <label for="mahasiswa" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa:</label>
                        <div class="flex gap-2">
                            <select class="flex-1 min-w-0 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="mahasiswa">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php /* data-sudah-lab1/2 dipakai wawancara.js untuk menyembunyikan
                                        peserta yang jenis wawancaranya sudah dijadwalkan. Satu
                                        peserta menjalani Lab I lalu Lab II, jadi penyaringannya
                                        bergantung pada jenis yang sedang dipilih admin. */ ?>
                                <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                    <option value="<?= $mahasiswa['id'] ?>"
                                            data-sudah-lab1="<?= (int) ($mahasiswa['sudah_lab1'] ?? 0) ?>"
                                            data-sudah-lab2="<?= (int) ($mahasiswa['sudah_lab2'] ?? 0) ?>">
                                        <?= $mahasiswa['stambuk'] ?> - <?= $mahasiswa['nama_lengkap'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="shrink-0 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition flex items-center gap-1.5" id="addMahasiswaButton"><i class="bi bi-plus-lg"></i> Tambah</button>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Tambahkan satu per satu; semua peserta terpilih dijadwalkan pada waktu yang sama.</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700">Daftar Peserta:</label>
                            <span id="jumlahMahasiswaTerpilih" class="text-xs font-bold text-slate-500">0 peserta</span>
                        </div>
                        <ul class="list-none p-0 m-0 space-y-2 max-h-40 overflow-y-auto" id="selectedMahasiswaList">
                            <li id="daftarMahasiswaKosong" class="text-center text-xs text-slate-400 py-4 border border-dashed border-slate-200 rounded-xl">
                                Belum ada peserta dipilih
                            </li>
                        </ul>
                    </div>

                    <div>
                        <label for="ruangan" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="ruangan" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $ruangan): ?>
                                <option value="<?= $ruangan['id'] ?>">
                                    <?= $ruangan['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                            <input type="date" min="<?= date('Y-m-d') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="tanggal" required>
                        </div>
                        <div>
                            <label for="waktu" class="block text-sm font-semibold text-slate-700 mb-2">Waktu:</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="waktu" required>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                    <button type="submit" form="addJadwalForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-check-lg"></i> Simpan</button>
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
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white rounded-t-2xl gap-3">
                <h5 class="font-bold flex items-center gap-2" id="updateWawancaraModalLabel"><i class="bi bi-pencil-square text-lg"></i>Update Jadwal Wawancara</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="updateWawancaraForm" method="POST" action="javascript:void(0);">
                <div class="p-6 space-y-4">
                    <input type="hidden" id="updateWawancaraId">
                    <div>
                        <label for="updateRuangan" class="block text-sm font-semibold text-slate-700 mb-2">Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="updateRuangan" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $ruangan): ?>
                                <option value="<?= $ruangan['id'] ?>">
                                    <?= $ruangan['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="updateTanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                        <input type="date" min="<?= date('Y-m-d') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="updateTanggal" required>
                    </div>
                    <div>
                        <label for="updateWaktu" class="block text-sm font-semibold text-slate-700 mb-2">Waktu:</label>
                        <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="updateWaktu" required>
                    </div>
                    <div>
                        <label for="updateJenisWawancara" class="block text-sm font-semibold text-slate-700 mb-2">Jenis Wawancara:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="updateJenisWawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
                            <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                        </select>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-check-lg"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Assets/js/admin/wawancara.js?v=<?= time() ?>"></script>

