<?php
/**
 * Daftar Hadir Peserta Admin View - Presentasi Style
 */
$absensiList = $absensiList ?? [];
$mahasiswaList = $mahasiswaList ?? [];
?>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Rekap Peserta';
        $subtitle = 'Rekapitulasi lengkap tahapan seleksi dan status akhir peserta';
        $icon = 'bi bi-clipboard-check';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>
    <div class="max-w-7xl mx-auto px-4 pt-0 pb-6">


        <?php if (empty($absensiList)): ?>
            <div class="flex flex-col items-center justify-center py-16 text-slate-400 bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                <i class="bi bi-inbox text-6xl mb-4 text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Data Rekap</h3>
                <p class="text-sm max-w-sm text-slate-500">Data rekap akan muncul setelah Anda menambahkan peserta</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full align-middle text-sm text-left no-datatable" id="monitoringTable" data-paginator="true" data-paginator-perpage="10">
                        <thead class="">
                            <tr>
                                <th class="dt-head-cell text-center" style="width: 50px;">No</th>
                                <th class="dt-head-cell">Nama Lengkap</th>
                                <th class="dt-head-cell">Stambuk</th>
                                <th class="dt-head-cell text-center">Tes Tertulis</th>
                                <th class="dt-head-cell text-center">Presentasi</th>
                                <th class="dt-head-cell text-center">Wawancara I</th>
                                <th class="dt-head-cell text-center">Wawancara II</th>
                                <th class="dt-head-cell text-center">Status Akhir</th>
                                <th class="dt-head-cell text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="dt-tbody">
                            <?php $no = 1; foreach ($absensiList as $row): ?>
                            <tr class="dt-body-row">
                                <td class="text-center py-4 px-4"><?= $no++ ?></td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <?php
                                            // Use only foto from berkas_mahasiswa upload (res/imageUser)
                                            $photoUrl = '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';
                                            if (!empty($row['berkas_foto'])) {
                                                $photoUrl = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . htmlspecialchars($row['berkas_foto']) . '?v=' . time();
                                            }
                                        ?>
                                        <img src="<?= $photoUrl ?>" alt="Foto" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                        <div>
                                            <div class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                            <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Calon Asisten</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-slate-500 py-4 px-4 font-semibold"><?= htmlspecialchars($row['stambuk']) ?></td>
                                <td class="text-center py-4 px-4"><?= renderStatusBadge($row['absensi_tes_tertulis']) ?></td>
                                <td class="text-center py-4 px-4"><?= renderStatusBadge($row['absensi_presentasi']) ?></td>
                                <td class="text-center py-4 px-4"><?= renderStatusBadge($row['absensi_wawancara_I']) ?></td>
                                <td class="text-center py-4 px-4"><?= renderStatusBadge($row['absensi_wawancara_II']) ?></td>
                                <td class="text-center py-4 px-4">
                                    <?php
                                        $statusAkhir = $row['status_akhir'] ?? 'Pending';
                                        $badgeClass = 'text-amber-700 bg-amber-50 border border-amber-100';
                                        if($statusAkhir === 'Lulus') $badgeClass = 'text-emerald-700 bg-emerald-50 border border-emerald-100';
                                        elseif($statusAkhir === 'Tidak Lulus') $badgeClass = 'text-red-700 bg-red-50 border border-red-100';
                                    ?>
                                    <span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg status-akhir-badge <?= $badgeClass ?>">
                                        <?= strtoupper($statusAkhir) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-cyan-50 hover:bg-cyan-100 text-cyan-600 open-rekap"
                                                title="Detail Rekap"
                                                data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                data-stambuk="<?= $row['stambuk'] ?>"
                                                data-foto="<?= !empty($row['berkas_foto']) ? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . htmlspecialchars($row['berkas_foto']) : '' ?>"
                                                data-berkas="<?= $row['berkas_status'] ?? '0' ?>"
                                                data-tes="<?= $row['absensi_tes_tertulis'] ?>"
                                                data-nilai="<?= $row['nilai_akhir'] ?? '' ?>"
                                                data-presentasi="<?= $row['absensi_presentasi'] ?>"
                                                data-wawancara1="<?= $row['absensi_wawancara_I'] ?>"
                                                data-wawancara2="<?= $row['absensi_wawancara_II'] ?>"
                                                data-statusakhir="<?= $row['status_akhir'] ?? 'Pending' ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 open-detail"
                                                title="Edit"
                                                data-id="<?= $row['id'] ?>"
                                                data-mhsid="<?= $row['id_mahasiswa'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                data-stambuk="<?= $row['stambuk'] ?>"
                                                data-absensiwawancarai="<?= $row['absensi_wawancara_I'] ?? '' ?>"
                                                data-absensiwawancaraii="<?= $row['absensi_wawancara_II'] ?? '' ?>"
                                                data-absensitestertulis="<?= $row['absensi_tes_tertulis'] ?? '' ?>"
                                                data-absensipresentasi="<?= $row['absensi_presentasi'] ?? '' ?>"
                                                data-statusakhir="<?= $row['status_akhir'] ?? 'Pending' ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
// Inline helper with XSS protection
function renderStatusBadge($val) {
    // Handle empty/null values
    if (!$val || trim($val) === '' || $val === '-') {
        return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400 border border-slate-200">Belum Ada</span>';
    }

    // Sanitize input first
    $sanitized = htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
    $v = strtolower($sanitized);

    // Case-insensitive status matching
    $statusMap = [
        'hadir' => ['class' => 'bg-emerald-50 text-emerald-700 border border-emerald-100', 'label' => 'Hadir'],
        'alpha' => ['class' => 'bg-red-50 text-red-700 border border-red-100', 'label' => 'Alpha'],
        'tidak hadir' => ['class' => 'bg-red-50 text-red-700 border border-red-100', 'label' => 'Tidak Hadir'],
        'izin' => ['class' => 'bg-amber-50 text-amber-700 border border-amber-100', 'label' => 'Izin'],
        'sakit' => ['class' => 'bg-amber-50 text-amber-700 border border-amber-100', 'label' => 'Sakit'],
        'process' => ['class' => 'bg-blue-50 text-blue-700 border border-blue-100', 'label' => 'Process']
    ];

    // Find matching status
    if (isset($statusMap[$v])) {
        $status = $statusMap[$v];
        return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg ' . $status['class'] . '">' . $status['label'] . '</span>';
    }

    // Unknown status - show as info
    return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-100">' . ucfirst($sanitized) . '</span>';
}
?>

<!-- MODAL ADD -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="addMahasiswaModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2">
                    <i class="bi bi-person-plus text-lg"></i>
                    Tambah Data Kehadiran
                </h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6">
                <form id="addJadwalForm" class="space-y-4">
                    <div>
                        <label for="mahasiswa" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa</label>
                        <div class="flex gap-2">
                            <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="mahasiswa">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['stambuk'] ?> - <?= htmlspecialchars($m['nama_lengkap']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shrink-0" type="button" id="addMahasiswaButton" aria-label="Tambah mahasiswa ke daftar">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Mahasiswa Terpilih:</label>
                        <div class="max-h-52 overflow-y-auto border border-slate-200 rounded-xl p-3 bg-white space-y-2" id="selectedMahasiswaList">
                            <div class="empty-msg text-center text-slate-400 py-6">
                                <i class="bi bi-inbox text-3xl mb-2 block"></i>
                                <p class="text-xs font-semibold">Belum ada mahasiswa dipilih</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">STATUS KEHADIRAN</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="absensiTesTertulis" class="block text-xs text-slate-500 font-semibold mb-1">Tes Tertulis</label>
                                <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="absensiTesTertulis">
                                    <option value="" selected>Pilih...</option>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Alpha">Alpha</option>
                                    <option value="Izin">Izin</option>
                                </select>
                            </div>
                            <div>
                                <label for="absensiPresentasi" class="block text-xs text-slate-500 font-semibold mb-1">Presentasi</label>
                                <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="absensiPresentasi">
                                    <option value="" selected>Pilih...</option>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Alpha">Alpha</option>
                                    <option value="Izin">Izin</option>
                                </select>
                            </div>
                            <div>
                                <label for="absensiWawancara1" class="block text-xs text-slate-500 font-semibold mb-1">Wawancara I</label>
                                <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="absensiWawancara1">
                                    <option value="" selected>Pilih...</option>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Alpha">Alpha</option>
                                    <option value="Izin">Izin</option>
                                </select>
                            </div>
                            <div>
                                <label for="absensiWawancara2" class="block text-xs text-slate-500 font-semibold mb-1">Wawancara II</label>
                                <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="absensiWawancara2">
                                    <option value="" selected>Pilih...</option>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Alpha">Alpha</option>
                                    <option value="Izin">Izin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="submit" form="addJadwalForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Simpan Data
                  </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REKAP DETAIL -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="rekapDetailModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2 text-base">
                    <i class="bi bi-card-checklist text-lg"></i>
                    Rekap Peserta
                </h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-0">
                <div class="p-4 text-center bg-slate-50 border-b border-slate-100 flex flex-col items-center">
                    <img id="rekapFoto" src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png" alt="Foto Profil" class="rounded-full w-16 h-16 object-cover border-4 border-white shadow-sm mb-2" style="display: none;" onerror="this.style.display='none'; document.getElementById('rekapAvatarContainer').style.display='flex';">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-sm mb-2" id="rekapAvatarContainer">
                        <span id="rekapAvatar">U</span>
                    </div>
                    <h5 class="text-base font-bold text-slate-800 mb-0.5" id="rekapNama">Nama Peserta</h5>
                    <p class="text-slate-400 font-semibold text-[11px] uppercase tracking-wider" id="rekapStambuk">Stambuk</p>
                </div>
                
                <div class="p-5 space-y-2.5">
                    <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tahapan Seleksi</h6>
                    
                    <div class="space-y-2">
                        <!-- Berkas -->
                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                            <div>
                                <h6 class="text-sm font-bold text-slate-800 mb-0.5">1. Kelengkapan Berkas</h6>
                                <small class="text-slate-400 text-xs font-medium">Administrasi Awal</small>
                            </div>
                            <span id="statusBerkas"></span>
                        </div>

                        <!-- Tes Tertulis -->
                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                            <div>
                                <h6 class="text-sm font-bold text-slate-800 mb-0.5">2. Tes Tertulis</h6>
                                <small class="text-slate-400 text-xs font-medium" id="scoreTes">Nilai: -</small>
                            </div>
                            <span id="statusTes"></span>
                        </div>

                        <!-- Presentasi -->
                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                            <div>
                                <h6 class="text-sm font-bold text-slate-800 mb-0.5">3. Presentasi</h6>
                                <small class="text-slate-400 text-xs font-medium">Status Kehadiran</small>
                            </div>
                            <span id="statusPresentasi"></span>
                        </div>

                        <!-- Wawancara -->
                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                            <div>
                                <h6 class="text-sm font-bold text-slate-800 mb-0.5">4. Wawancara</h6>
                                <small class="text-slate-400 text-xs font-medium">Wawancara I & II</small>
                            </div>
                            <div class="flex gap-2">
                                <span id="statusWawancara1"></span>
                                <span id="statusWawancara2"></span>
                            </div>
                        </div>

                        <!-- FINAL RESULT -->
                        <div class="mt-2 p-3 rounded-xl flex justify-between items-center transition" id="finalResultBox">
                            <h6 class="text-sm font-bold text-slate-700">HASIL AKHIR</h6>
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-lg" id="finalStatus">PENDING</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-5 py-3">
                <button type="button" class="w-full py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="detailAbsensiModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="font-bold flex items-center gap-2">
                    <i class="bi bi-pencil-square text-lg"></i>
                    Edit Data Kehadiran
                </h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="text-center flex flex-col items-center pb-4 border-b border-slate-100">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-sm mb-3">
                        <span id="avatarInitial">U</span>
                    </div>
                    <h5 class="text-lg font-bold text-slate-800 mb-1" id="detailNama">Name</h5>
                    <p class="text-slate-400 font-semibold text-xs uppercase tracking-wider mb-0" id="detailStambuk">Stambuk</p>
                </div>
                <input type="hidden" id="detailUserId">
                <input type="hidden" id="detailMhsId">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tesTertulis" class="block text-xs text-slate-500 font-semibold mb-1.5">Tes Tertulis</label>
                        <select id="tesTertulis" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    <div>
                        <label for="presentasi" class="block text-xs text-slate-500 font-semibold mb-1.5">Presentasi</label>
                        <select id="presentasi" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label for="wawancaraI" class="block text-xs text-slate-500 font-semibold mb-1.5">Wawancara I</label>
                        <select id="wawancaraI" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs bg-white transition">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label for="wawancaraII" class="block text-xs text-slate-500 font-semibold mb-1.5">Wawancara II</label>
                        <select id="wawancaraII" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs bg-white transition">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label for="detailStatusAkhir" class="block text-xs text-slate-500 font-semibold mb-1.5">Status Akhir</label>
                        <select id="detailStatusAkhir" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs bg-white transition">
                            <option value="Pending">Pending</option>
                            <option value="Lulus">Lulus</option>
                            <option value="Tidak Lulus">Tidak Lulus</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-modal-close>Batal</button>
                <button type="button" id="saveDetailAbsensi" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Assets/js/admin/kehadiran.js?v=<?= time() ?>"></script>

