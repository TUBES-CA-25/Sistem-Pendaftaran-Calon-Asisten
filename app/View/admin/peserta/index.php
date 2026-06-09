<?php
/**
 * Daftar Peserta View
 * 
 * Data yang diterima dari controller:
 * @var array $mahasiswaList - Daftar mahasiswa
 * @var array $result - Result mahasiswa
 */
$mahasiswaList = $mahasiswaList ?? [];
$result = $result ?? [];
?>

<!-- Page Header -->
<?php
    $title = 'Daftar Peserta';
    $subtitle = 'Kelola data peserta pendaftaran calon asisten';
    $icon = 'bi bi-people-fill';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div class="relative w-full sm:w-72">
            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" id="searchPeserta" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Cari peserta...">
        </div>
        <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2 shadow-md shadow-blue-500/10" data-bs-toggle="modal" data-bs-target="#addNotification">
            <i class="bi bi-send-fill"></i> Kirim Notifikasi
        </button>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table id="daftarPesertaTable" class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-center w-[60px]">NO</th>
                        <th class="font-bold text-xs uppercase tracking-wider py-4 px-4">NAMA LENGKAP</th>
                        <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-center w-[140px]">STAMBUK</th>
                        <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 w-[180px]">JURUSAN</th>
                        <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-center w-[130px]">STATUS</th>
                        <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-center w-[120px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $i = 1; ?>
                    <?php foreach ($result as $row): ?>
                        <?php if (empty($row['id'])) continue; // Show ONLY Mahasiswa ?>
                        <tr class="hover:bg-slate-50/85 transition" data-id="<?= $row['id'] ?>" data-userid="<?= $row['idUser'] ?>">
                            <td class="text-center font-semibold text-slate-400 py-4 px-4"><?= $i ?></td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= $row['photoPath'] ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 flex-shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></span>
                                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">Mahasiswa</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-slate-600 py-4 px-4 font-semibold">
                                <?= htmlspecialchars($row['stambuk'] ?? '-') ?>
                            </td>
                            <td class="text-slate-500 py-4 px-4 font-medium">
                                <?= htmlspecialchars($row['jurusan'] ?? '-') ?>
                            </td>
                            <td class="text-center py-4 px-4">
                                <span class="<?= $row['statusBadge']['class'] ?> inline-block px-3 py-1.5 text-xs font-semibold rounded-lg">
                                    <?= $row['statusBadge']['text'] ?>
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-blue-50 hover:bg-blue-100 text-blue-600 btn-view" 
                                            title="Lihat Detail"
                                            data-id="<?= $row['id'] ?>"
                                            data-userid="<?= $row['idUser'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? '') ?>"
                                            data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '') ?>"
                                            data-jurusan="<?= htmlspecialchars($row['jurusan'] ?? '') ?>"
                                            data-kelas="<?= htmlspecialchars($row['kelas'] ?? '') ?>"
                                            data-alamat="<?= htmlspecialchars($row['alamat'] ?? '') ?>"
                                            data-tempat_lahir="<?= htmlspecialchars($row['tempat_lahir'] ?? '') ?>"
                                            data-notelp="<?= htmlspecialchars($row['notelp'] ?? '') ?>"
                                            data-tanggal_lahir="<?= htmlspecialchars($row['tanggal_lahir'] ?? '') ?>"
                                            data-jenis_kelamin="<?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?>"
                                            data-judul_presentasi="<?= htmlspecialchars($row['judul_presentasi'] ?? '') ?>"
                                            data-foto="<?= $row['berkas']['foto'] ?? '' ?>"
                                            data-cv="<?= $row['berkas']['cv'] ?? '' ?>"
                                            data-transkrip="<?= $row['berkas']['transkrip_nilai'] ?? '' ?>"
                                            data-surat="<?= $row['berkas']['surat_pernyataan'] ?? '' ?>"
                                            data-berkas_accepted="<?= $row['berkas']['accepted'] ?? '' ?>"
                                            data-makalah="<?= $row['presentasi']['makalah'] ?? '' ?>"
                                            data-ppt="<?= $row['presentasi']['ppt'] ?? ''?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 btn-delete" 
                                            title="Hapus"
                                            data-id="<?= $row['id'] ?>"
                                            data-userid="<?= $row['idUser'] ?>">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Kirim Notifikasi -->
<div class="modal fade" id="addNotification" tabindex="-1" aria-labelledby="addNotificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2" id="addNotificationLabel">
                    <i class="bi bi-send text-lg"></i>Kirim Notifikasi ke Peserta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-6">
                <form id="addNotificationForm" class="space-y-4">
                    <!-- Hidden Select for Logic Compatibility -->
                    <div class="hidden">
                        <select class="form-select" id="mahasiswa">
                            <option value="" disabled selected>-- Pilih Peserta --</option>
                            <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                <option value="<?= $mahasiswa['id'] ?>" data-userid="<?= $mahasiswa['idUser'] ?>">
                                    <?= htmlspecialchars($mahasiswa['stambuk']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="selectedMahasiswaList"></div>
                        <span id="selectedCount"></span>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3 text-blue-700">
                        <i class="bi bi-info-circle-fill text-xl flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-sm block mb-0.5">Broadcast Notifikasi</strong>
                            <span class="text-xs text-blue-600 font-medium">Pesan yang Anda tulis di bawah ini akan dikirimkan kepada <u>seluruh peserta</u> yang terdaftar dalam sistem.</span>
                        </div>
                    </div>

                    <div>
                        <label for="notifMessage" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-chat-text me-1"></i>Pesan Broadcast
                        </label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="notifMessage" rows="5" placeholder="Tulis pesan pengumuman atau informasi penting di sini..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i>Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2" form="addNotificationForm">
                    <i class="bi bi-send"></i>Kirim Notifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Peserta -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <!-- Header dengan Background Gradient -->
            <div class="relative bg-slate-900 px-8 py-6 text-white overflow-hidden flex-shrink-0">
                <!-- Decorative Elements -->
                <div class="absolute -top-10 -right-10 w-36 h-36 bg-white/10 rounded-full"></div>
                <div class="absolute bottom-5 -left-10 w-24 h-24 bg-white/5 rounded-full"></div>
                
                <button type="button" class="btn-close btn-close-white absolute top-4 right-4 opacity-80 z-20" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <!-- Title -->
                <h5 class="text-white font-bold text-lg flex items-center gap-2 relative z-10">
                    <i class="bi bi-person-badge text-xl"></i>Detail Peserta
                </h5>
            </div>
            
            <!-- Profile Card yang Overlap -->
            <div class="px-6 -mt-6 relative z-10 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <!-- Photo Column -->
                        <div class="relative flex-shrink-0">
                            <img id="modalFoto" src="" alt="Foto Peserta" 
                                 class="rounded-full w-32 h-32 object-cover border-4 border-white shadow-md bg-slate-100">
                            <span id="modalStatusIcon" class="absolute bottom-0 right-0 w-8 h-8 rounded-full shadow-md flex items-center justify-center text-sm text-white font-bold">
                            </span>
                        </div>
                        
                        <!-- Info Column -->
                        <div class="flex-grow w-full">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-1" id="modalNamaHeader">Nama Peserta</h3>
                                    <p class="text-slate-400 text-sm font-semibold flex items-center gap-1.5 mb-2">
                                        <i class="bi bi-credit-card-2-front text-base"></i>
                                        <span id="modalStambukHeader">-</span>
                                    </p>
                                    <span id="modalStatusBadge" class="inline-block rounded-full px-4 py-1.5 text-xs font-semibold">
                                        Status
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Quick Stats Row -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                <div class="bg-blue-50/50 border border-blue-100/60 rounded-xl p-3 text-center transition hover:bg-blue-50">
                                    <i class="bi bi-mortarboard-fill text-blue-500 mb-1.5 text-lg block"></i>
                                    <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Jurusan</span>
                                    <span class="font-bold text-xs text-slate-700 truncate block" id="modalJurusan" title="">-</span>
                                </div>
                                <div class="bg-emerald-50/50 border border-emerald-100/60 rounded-xl p-3 text-center transition hover:bg-emerald-50">
                                    <i class="bi bi-door-open-fill text-emerald-500 mb-1.5 text-lg block"></i>
                                    <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Kelas</span>
                                    <span class="font-bold text-xs text-slate-700 block" id="modalKelas">-</span>
                                </div>
                                <div class="bg-purple-50/50 border border-purple-100/60 rounded-xl p-3 text-center transition hover:bg-purple-50">
                                    <i class="bi bi-gender-ambiguous text-purple-500 mb-1.5 text-lg block"></i>
                                    <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Gender</span>
                                    <span class="font-bold text-xs text-slate-700 block" id="modalJenis_kelamin">-</span>
                                </div>
                                <div class="bg-amber-50/50 border border-amber-100/60 rounded-xl p-3 text-center transition hover:bg-amber-50">
                                    <i class="bi bi-telephone-fill text-amber-500 mb-1.5 text-lg block"></i>
                                    <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-0.5">Telepon</span>
                                    <span class="font-bold text-xs text-slate-700 truncate block" id="modalNoTelp">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Body Content -->
            <div class="modal-body px-6 pb-6 pt-3">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center mb-5 mr-3">
                            <div class="rounded-xl flex items-center justify-content-center mr-3 w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex-shrink-0">
                                <i class="bi bi-person-vcard text-base"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-slate-800 mb-0.5">Biodata Peserta</h6>
                                <small class="text-slate-400 text-xs font-semibold">Informasi personal</small>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2 p-3 bg-slate-50 border-l-4 border-blue-500 rounded-xl">
                                <label class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Nama Lengkap</label>
                                <p class="font-bold text-slate-800 mb-0" id="modalNama">-</p>
                            </div>
                            <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <label class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Stambuk/NIM</label>
                                <p class="font-semibold text-slate-700 text-sm mb-0" id="modalStambuk">-</p>
                            </div>
                            <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <label class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Tempat Lahir</label>
                                <p class="font-semibold text-slate-700 text-sm mb-0" id="modalTempat_lahir">-</p>
                            </div>
                            <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <label class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Tanggal Lahir</label>
                                <p class="font-semibold text-slate-700 text-sm mb-0" id="modalTanggal_lahir">-</p>
                            </div>
                            <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <label class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Jenis Kelamin</label>
                                <p class="font-semibold text-slate-700 text-sm mb-0" id="modalJenisKelaminDetail">-</p>
                            </div>
                            <div class="sm:col-span-2 p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <label class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1 flex items-center gap-1">
                                    <i class="bi bi-geo-alt"></i>Alamat
                                </label>
                                <p class="font-semibold text-slate-700 text-sm mb-0 leading-relaxed" id="modalAlamat">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center mb-5 mr-3">
                            <div class="rounded-xl flex items-center justify-content-center mr-3 w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 text-white flex-shrink-0">
                                <i class="bi bi-folder2-open text-base"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-slate-800 mb-0.5">Berkas Pendaftaran</h6>
                                <small class="text-slate-400 text-xs font-semibold">Dokumen yang diunggah</small>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Foto -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-200/80 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm transition duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-xl flex items-center justify-content-center w-10 h-10 bg-blue-100 text-blue-600 flex-shrink-0">
                                        <i class="bi bi-image text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-sm font-semibold text-slate-700 truncate">Foto</p>
                                        <small class="text-slate-400 text-xs block truncate">Pas foto mahasiswa</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-9 h-9 rounded-xl flex items-center justify-center transition bg-sky-50 hover:bg-sky-100 text-sky-600" id="downloadFotoButton" data-download-url="">
                                    <i class="bi bi-download text-sm"></i>
                                </button>
                            </div>

                            <!-- CV -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-200/80 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm transition duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-xl flex items-center justify-content-center w-10 h-10 bg-purple-100 text-purple-600 flex-shrink-0">
                                        <i class="bi bi-file-person text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-sm font-semibold text-slate-700 truncate">CV</p>
                                        <small class="text-slate-400 text-xs block truncate">Curriculum Vitae</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-9 h-9 rounded-xl flex items-center justify-center transition bg-sky-50 hover:bg-sky-100 text-sky-600" id="downloadCVButton" data-download-url="">
                                    <i class="bi bi-download text-sm"></i>
                                </button>
                            </div>

                            <!-- Transkrip -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-200/80 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm transition duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-xl flex items-center justify-content-center w-10 h-10 bg-emerald-100 text-emerald-600 flex-shrink-0">
                                        <i class="bi bi-file-text text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-sm font-semibold text-slate-700 truncate">Transkrip Nilai</p>
                                        <small class="text-slate-400 text-xs block truncate">Transkrip nilai akademik</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-9 h-9 rounded-xl flex items-center justify-center transition bg-sky-50 hover:bg-sky-100 text-sky-600" id="downloadTranskripButton" data-download-url="">
                                    <i class="bi bi-download text-sm"></i>
                                </button>
                            </div>

                            <!-- Surat Pernyataan -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-200/80 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm transition duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-xl flex items-center justify-content-center w-10 h-10 bg-amber-100 text-amber-600 flex-shrink-0">
                                        <i class="bi bi-file-earmark-check text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-sm font-semibold text-slate-700 truncate">Surat Pernyataan</p>
                                        <small class="text-slate-400 text-xs block truncate">Surat pernyataan bermaterai</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-9 h-9 rounded-xl flex items-center justify-center transition bg-sky-50 hover:bg-sky-100 text-sky-600" id="downloadSuratButton" data-download-url="">
                                    <i class="bi bi-download text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-slate-50 px-8 py-5 border-t border-slate-200/60 flex-shrink-0">
                <input type="hidden" id="modalMahasiswaId" value="">
                <input type="hidden" id="modalUserId" value="">
                
                <div class="flex flex-col sm:flex-row justify-between items-center w-full gap-4">
                    <button type="button" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>Tutup
                    </button>
                    <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">
                        <button type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-sm btn-send-message" id="btnSendMessageToUser">
                            <i class="bi bi-envelope-fill"></i>Kirim Pesan
                        </button>
                        
                        <button type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-emerald-500/10" id="btnTerimaModal" onclick="acceptParticipant()" style="display: none;">
                            <i class="bi bi-check-circle"></i>Verifikasi Berkas
                        </button>
                        
                        <button type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-red-500/10" id="btnTolakModal" onclick="rejectParticipant()" style="display: none;">
                            <i class="bi bi-x-circle"></i>Batalkan Verifikasi Berkas
                        </button>
                        
                        <button type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-emerald-500/10" id="btnVerifikasiModal" onclick="triggerVerificationFromModal()">
                            <i class="bi bi-check-circle"></i>Verifikasi Berkas
                        </button>
                        <button type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-red-500/10" id="btnBatalkanModal" onclick="cancelVerification()" style="display: none;">
                            <i class="bi bi-x-circle"></i>Batalkan Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kirim Pesan Individual -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2" id="sendMessageModalLabel">
                    <i class="bi bi-chat-dots text-lg"></i>Kirim Pesan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-6 space-y-4">
                 <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kepada:</label>
                    <p class="text-slate-800 font-semibold mb-0" id="messageRecipient">-</p>
                </div>
                <div>
                    <label for="individualMessage" class="block text-sm font-semibold text-slate-700 mb-2">Pesan untuk Mahasiswa:</label>
                    <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="individualMessage" rows="4" required placeholder="Tuliskan pesan..."></textarea>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <input type="hidden" id="messageUserId" value="">
                <input type="hidden" id="messageMahasiswaId" value="">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2" id="sendIndividualMessage">
                    <i class="bi bi-send"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Load Custom JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/participants.js"></script>
