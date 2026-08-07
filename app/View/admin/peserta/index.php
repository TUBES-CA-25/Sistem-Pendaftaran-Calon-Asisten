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
<!-- Page -->
<!-- Page Header -->
<?php
    $title = 'Daftar Peserta';
    $subtitle = 'Kelola data peserta pendaftaran calon asisten';
    $icon = 'bi bi-people-fill';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>
<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 pt-0 pb-6">

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

        <!-- Tombol aksi: dipindahkan VanillaPaginator ke toolbar atas, bersebelahan
             dengan kotak "Cari data". Sengaja `hidden` supaya tidak berkedip di
             posisi lama sebelum paginator memindahkannya. -->
        <button id="btnKirimNotifDt" class="vp-custom-button hidden px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition shadow-sm shadow-blue-500/20 items-center gap-2 whitespace-nowrap" data-modal-open="#addNotification">
            <i class="bi bi-send-fill text-xs"></i>
            <span>Kirim Notifikasi</span>
        </button>

        <div class="overflow-x-auto">
            <table id="daftarPesertaTable" class="min-w-full align-middle text-sm text-left no-datatable" data-paginator="true" data-paginator-perpage="10">

                <thead>
                    <tr class="">
                        <th class="dt-head-cell text-center">No</th>
                        <th class="dt-head-cell">Nama Lengkap</th>
                        <th class="dt-head-cell text-center">Stambuk</th>
                        <th class="dt-head-cell">Jurusan</th>
                        <th class="dt-head-cell text-center">Status</th>
                        <th class="dt-head-cell text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="dt-tbody">
                    <?php $i = 1; ?>
                    <?php foreach ($result as $row): ?>
                        <?php if (empty($row['id'])) continue; // Show ONLY Mahasiswa ?>
                        <tr class="dt-body-row" data-id="<?= $row['id'] ?>" data-userid="<?= $row['idUser'] ?>">
                            <td class="text-center py-4 px-4"><?= $i ?></td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= $row['photoPath'] ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                    <div>
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></div>
                                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-4 px-4">
                                <?= htmlspecialchars($row['stambuk'] ?? '-') ?>
                            </td>
                            <td class="py-4 px-4">
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="addNotification" aria-labelledby="addNotificationLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2" id="addNotificationLabel">
                    <i class="bi bi-send text-lg"></i>Kirim Notifikasi ke Peserta
                </h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6">
                <form id="addNotificationForm" class="space-y-4">
                    <!-- Hidden Select for Logic Compatibility -->
                    <div class="hidden">
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="mahasiswa">
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
                        <i class="bi bi-info-circle-fill text-xl shrink-0 mt-0.5"></i>
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
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2" data-modal-close>
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="detailModal" aria-labelledby="detailModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <!-- Tinggi frame dikunci (h-[90vh]) supaya modal TIDAK berubah ukuran saat
         pindah tab. Header, kartu profil, navigasi tab, dan footer memakai
         shrink-0; hanya area isi tab yang memanjang dan menggulir sendiri. -->
    <div class="relative w-full max-w-4xl mx-auto h-[85vh] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full h-full flex flex-col rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header dengan Background Gradient -->
            <div class="relative bg-gradient-to-br from-primary to-secondary px-6 py-4 text-white overflow-hidden shrink-0">
                <!-- Decorative Elements -->
                <div class="absolute -top-10 -right-10 w-28 h-28 bg-white/10 rounded-full"></div>
                <div class="absolute bottom-5 -left-10 w-20 h-20 bg-white/5 rounded-full"></div>
                
                <button type="button" data-modal-close aria-label="Tutup" class="absolute top-3.5 right-3.5 z-20 shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>

                <!-- Title -->
                <h5 class="text-white font-bold text-base flex items-center gap-1.5 relative z-10">
                    <i class="bi bi-person-badge text-lg"></i>Detail Peserta
                </h5>
            </div>
            
            <!-- Profile Card yang Overlap -->
            <div class="px-5 -mt-4 relative z-10 shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <!-- Photo Column -->
                        <div class="relative shrink-0">
                            <img id="modalFoto" src="" alt="Foto Peserta" 
                                 class="rounded-full w-20 h-20 object-cover border-2 border-white shadow-md bg-slate-100">
                            <span id="modalStatusIcon" class="absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold border-2 border-white">
                                <i id="modalStatusIconInner" class="bi bi-dash"></i>
                            </span>
                        </div>
                        
                        <!-- Info Column -->
                        <div class="flex-grow w-full">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2.5">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800 mb-0.5" id="modalNamaHeader">Nama Peserta</h3>
                                    <p class="text-slate-400 text-xs font-semibold flex items-center gap-1 mb-1.5">
                                        <i class="bi bi-credit-card-2-front text-sm"></i>
                                        <span id="modalStambukHeader">-</span>
                                    </p>
                                    <span id="modalStatusBadge" class="inline-block rounded-full px-3 py-1 text-[10px] font-semibold">
                                        <i id="modalStatusBadgeIcon" class="me-1"></i><span id="modalStatusBadgeText">Status</span>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Navigation -->
            <div class="px-5 mt-3 shrink-0 bg-white">
                <div class="flex border-b border-slate-200 gap-1.5">
                    <button type="button" class="tab-btn active px-3 py-2 text-xs font-semibold text-blue-600 border-b-2 border-blue-600 transition-all flex items-center gap-1.5" data-tab="tab-profil">
                        <i class="bi bi-person-vcard"></i> Profil & Kontak
                    </button>
                    <button type="button" class="tab-btn px-3 py-2 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 transition-all flex items-center gap-1.5" data-tab="tab-berkas">
                        <i class="bi bi-folder2-open"></i> Berkas Pendaftaran
                    </button>
                    <button type="button" class="tab-btn px-3 py-2 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700 transition-all flex items-center gap-1.5" data-tab="tab-presentasi">
                        <i class="bi bi-presentation-play"></i> Tugas & Presentasi
                    </button>
                </div>
            </div>
            
            <!-- Body Content: satu-satunya area yang boleh memanjang/menggulir -->
            <div class="px-5 pb-5 pt-3 flex-1 min-h-0 overflow-y-auto">
                <!-- Tab overlay: semua panel ditumpuk di sel grid yang sama lalu
                     di-cross-fade.

                     PENTING: jangan pakai <style type="text/tailwindcss"> di sini.
                     Play CDN hanya mengompilasi blok itu saat load awal; pada
                     navigasi SPA markup masuk lewat innerHTML sehingga blok tidak
                     pernah dikompilasi, .tab-panel kehilangan SELURUH gayanya, dan
                     ketiga panel tampil sekaligus bertumpuk ke bawah.
                     Karena itu utility-nya ditulis literal di elemen.

                     Nama class .tab-panel/.active-tab tetap dipertahankan karena
                     di-toggle oleh JS (admin/peserta.js:298-300). -->
                <div class="tabs-container grid">
                    <!-- Panel 1: Profil & Kontak -->
                    <div id="tab-profil" class="tab-panel active-tab [grid-area:1/1] transition-opacity duration-200 ease-in-out space-y-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <h6 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-1.5">
                            <span class="w-1 h-3.5 bg-blue-600 rounded-full"></span>
                            Biodata Lengkap
                        </h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                            <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100 hover:bg-blue-50 transition-colors shadow-sm shadow-blue-100/50">
                                <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider block mb-0.5">Nama Lengkap</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalNama">-</span>
                            </div>
                            <div class="p-3 bg-sky-50/50 rounded-xl border border-sky-100 hover:bg-sky-50 transition-colors shadow-sm shadow-sky-100/50">
                                <span class="text-[9px] font-bold text-sky-500 uppercase tracking-wider block mb-0.5">Stambuk / NIM</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalStambuk">-</span>
                            </div>
                            <div class="p-3 bg-cyan-50/50 rounded-xl border border-cyan-100 hover:bg-cyan-50 transition-colors shadow-sm shadow-cyan-100/50">
                                <span class="text-[9px] font-bold text-cyan-600 uppercase tracking-wider block mb-0.5">Jurusan</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalJurusanTab">-</span>
                            </div>
                            <div class="p-3 bg-pink-50/50 rounded-xl border border-pink-100 hover:bg-pink-50 transition-colors shadow-sm shadow-pink-100/50">
                                <span class="text-[9px] font-bold text-pink-500 uppercase tracking-wider block mb-0.5">Kelas</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalKelasTab">-</span>
                            </div>
                            <div class="p-3 bg-rose-50/50 rounded-xl border border-rose-100 hover:bg-rose-50 transition-colors shadow-sm shadow-rose-100/50">
                                <span class="text-[9px] font-bold text-rose-500 uppercase tracking-wider block mb-0.5">Tempat Lahir</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalTempat_lahir">-</span>
                            </div>
                            <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-100 hover:bg-amber-50 transition-colors shadow-sm shadow-amber-100/50">
                                <span class="text-[9px] font-bold text-amber-600 uppercase tracking-wider block mb-0.5">Tanggal Lahir</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalTanggal_lahir">-</span>
                            </div>
                            <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 hover:bg-emerald-50 transition-colors shadow-sm shadow-emerald-100/50">
                                <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider block mb-0.5">Jenis Kelamin</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalJenisKelaminDetail">-</span>
                            </div>
                            <div class="p-3 bg-teal-50/50 rounded-xl border border-teal-100 hover:bg-teal-50 transition-colors shadow-sm shadow-teal-100/50">
                                <span class="text-[9px] font-bold text-teal-500 uppercase tracking-wider block mb-0.5">No. HP / WhatsApp</span>
                                <span class="font-bold text-slate-800 text-xs" id="modalNoTelpTab">-</span>
                            </div>
                            <div class="sm:col-span-2 lg:col-span-3 p-3 bg-cyan-50/50 rounded-xl border border-cyan-100 hover:bg-cyan-50 transition-colors shadow-sm shadow-cyan-100/50">
                                <span class="text-[9px] font-bold text-cyan-500 uppercase tracking-wider block mb-0.5">Alamat Tinggal</span>
                                <span class="font-bold text-slate-800 text-xs leading-relaxed block" id="modalAlamat">-</span>
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Panel 2: Berkas Pendaftaran -->
                <div id="tab-berkas" class="tab-panel hidden [grid-area:1/1] transition-opacity duration-200 ease-in-out space-y-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <h6 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-1.5">
                            <span class="w-1.5 h-3.5 bg-amber-500 rounded-full"></span>
                            Dokumen yang Diunggah
                        </h6>
                        <div class="grid grid-cols-1 gap-3.5">
                            <!-- Foto -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm hover:scale-[1.01] transition-all duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-lg flex items-center justify-center w-10 h-10 bg-blue-50 text-blue-600 shrink-0">
                                        <i class="bi bi-image text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-xs font-bold text-slate-800 truncate">Pas Foto</p>
                                        <small class="text-slate-400 text-[10px] block truncate mt-0.5">Format JPG/PNG</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-8 h-8 rounded-lg flex items-center justify-center transition bg-blue-50 hover:bg-blue-100 text-blue-600 shadow-sm" id="downloadFotoButton" data-download-url="" aria-label="Unduh foto">
                                    <i class="bi bi-download text-xs"></i>
                                </button>
                            </div>
                            
                            <!-- CV -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm hover:scale-[1.01] transition-all duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-lg flex items-center justify-center w-10 h-10 bg-sky-50 text-sky-600 shrink-0">
                                        <i class="bi bi-file-person text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-xs font-bold text-slate-800 truncate">CV (Curriculum Vitae)</p>
                                        <small class="text-slate-400 text-[10px] block truncate mt-0.5">Format PDF</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-8 h-8 rounded-lg flex items-center justify-center transition bg-sky-50 hover:bg-sky-100 text-sky-600 shadow-sm" id="downloadCVButton" data-download-url="" aria-label="Unduh CV">
                                    <i class="bi bi-download text-xs"></i>
                                </button>
                            </div>
 
                            <!-- Transkrip -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm hover:scale-[1.01] transition-all duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-lg flex items-center justify-center w-10 h-10 bg-emerald-50 text-emerald-600 shrink-0">
                                        <i class="bi bi-file-earmark-text text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-xs font-bold text-slate-800 truncate">Transkrip Nilai</p>
                                        <small class="text-slate-400 text-[10px] block truncate mt-0.5">KHS / Transkrip Terakhir</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-8 h-8 rounded-lg flex items-center justify-center transition bg-emerald-50 hover:bg-emerald-100 text-emerald-600 shadow-sm" id="downloadTranskripButton" data-download-url="" aria-label="Unduh transkrip nilai">
                                    <i class="bi bi-download text-xs"></i>
                                </button>
                            </div>
 
                            <!-- Surat Pernyataan -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm hover:scale-[1.01] transition-all duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-lg flex items-center justify-center w-10 h-10 bg-rose-50 text-rose-600 shrink-0">
                                        <i class="bi bi-file-earmark-check text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-xs font-bold text-slate-800 truncate">Surat Pernyataan</p>
                                        <small class="text-slate-400 text-[10px] block truncate mt-0.5">Format PDF bermaterai</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-download-berkas w-8 h-8 rounded-lg flex items-center justify-center transition bg-rose-50 hover:bg-rose-100 text-rose-600 shadow-sm" id="downloadSuratButton" data-download-url="" aria-label="Unduh surat pernyataan">
                                    <i class="bi bi-download text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Panel 3: Tugas & Presentasi -->
                <div id="tab-presentasi" class="tab-panel hidden [grid-area:1/1] transition-opacity duration-200 ease-in-out space-y-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <h6 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-1.5">
                            <span class="w-1.5 h-3.5 bg-blue-600 rounded-full"></span>
                            Informasi Presentasi & Judul
                        </h6>
                        
                        <!-- Judul Presentasi -->
                        <div class="p-3.5 bg-blue-50/45 rounded-xl border border-blue-100/60 mb-4">
                            <span class="text-[9px] font-bold text-blue-400 uppercase tracking-wider block mb-0.5">Judul Presentasi</span>
                            <h5 class="text-slate-800 font-bold text-xs leading-relaxed mb-0" id="modalJudulPresentasi">Belum diisi oleh peserta</h5>
                        </div>
 
                        <!-- Dokumen Pendukung -->
                        <div id="presentasiFiles" class="grid grid-cols-1 gap-3.5">
                            <!-- Makalah -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm hover:scale-[1.01] transition-all duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-lg flex items-center justify-center w-10 h-10 bg-red-50 text-red-600 shrink-0">
                                        <i class="bi bi-file-earmark-pdf text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-xs font-bold text-slate-800 truncate">Makalah</p>
                                        <small class="text-slate-400 text-[10px] block truncate mt-0.5">Format PDF</small>
                                    </div>
                                </div>
                                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 shadow-sm" id="downloadMakalahButton" data-download-url="" aria-label="Unduh makalah">
                                    <i class="bi bi-download text-xs"></i>
                                </button>
                            </div>
 
                            <!-- PPT -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm hover:scale-[1.01] transition-all duration-200">
                                <div class="flex items-center gap-3 flex-grow min-w-0">
                                    <div class="rounded-lg flex items-center justify-center w-10 h-10 bg-orange-50 text-orange-600 shrink-0">
                                        <i class="bi bi-file-earmark-slides text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="mb-0 text-xs font-bold text-slate-800 truncate">Slide PPT</p>
                                        <small class="text-slate-400 text-[10px] block truncate mt-0.5">Format PPTX</small>
                                    </div>
                                </div>
                                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-orange-50 hover:bg-orange-100 text-orange-600 shadow-sm" id="downloadPptButton" data-download-url="" aria-label="Unduh PPT">
                                    <i class="bi bi-download text-xs"></i>
                                </button>
                            </div>
                        </div>
 
                        <!-- Empty state if no presentasi files -->
                        <div id="noPresentasiFiles" class="text-center py-6 text-slate-400" style="display: none;">
                            <i class="bi bi-journal-x text-3xl block mb-1 text-slate-300"></i>
                            <span class="text-[11px] font-medium">Belum ada file presentasi (Makalah / PPT) yang diunggah.</span>
                        </div>
                    </div>
                </div>
                </div> <!-- End tabs-container -->
            </div>
            
            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-200/60 shrink-0">
                <input type="hidden" id="modalMahasiswaId" value="">
                <input type="hidden" id="modalUserId" value="">
                
                <div class="flex flex-col sm:flex-row justify-between items-center w-full gap-3">
                    <button type="button" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5" data-modal-close>
                        <i class="bi bi-x-lg"></i>Tutup
                    </button>
                    <div class="flex flex-wrap gap-2.5 w-full sm:w-auto justify-end">
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm btn-send-message" id="btnSendMessageToUser">
                            <i class="bi bi-envelope-fill"></i>Kirim Pesan
                        </button>
                        
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/10" id="btnTerimaModal" onclick="acceptParticipant()" style="display: none;">
                            <i class="bi bi-check-circle"></i>Verifikasi Berkas
                        </button>
                        
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-red-500/10" id="btnTolakModal" onclick="rejectParticipant()" style="display: none;">
                            <i class="bi bi-x-circle"></i>Tolak Berkas
                        </button>
                        
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/10" id="btnVerifikasiModal" onclick="triggerVerificationFromModal()">
                            <i class="bi bi-check-circle"></i>Verifikasi Berkas
                        </button>
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-red-500/10" id="btnBatalkanModal" onclick="cancelVerification()" style="display: none;">
                            <i class="bi bi-x-circle"></i>Batalkan Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kirim Pesan Individual -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="sendMessageModal" aria-labelledby="sendMessageModalLabel" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative isolate w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2" id="sendMessageModalLabel">
                    <i class="bi bi-chat-dots text-lg"></i>Kirim Pesan
                </h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="bg-white p-6 space-y-4">
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
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="button" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2" id="sendIndividualMessage">
                    <i class="bi bi-send"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Load Custom JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/admin/peserta.js?v=<?= time() ?>"></script>


