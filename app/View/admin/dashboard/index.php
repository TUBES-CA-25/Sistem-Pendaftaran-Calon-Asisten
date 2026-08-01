<?php
/**
 * Dashboard Admin View
 */
$currentYear = date('Y');
$currentMonth = date('m');
$currentMonthName = date('F Y');

// Null Coalescing for optional variables
$totalPendaftar = $totalPendaftar ?? 0;
$pendaftarLulus = $pendaftarLulus ?? 0;
$pendaftarPending = $pendaftarPending ?? 0;
$pendaftarGagal = $pendaftarGagal ?? 0;
$statusKegiatan = $statusKegiatan ?? [];
$kegiatanBulanIni = $kegiatanBulanIni ?? [];
$jadwalPresentasiMendatang = $jadwalPresentasiMendatang ?? [];

?>

<!-- Page Header -->
<?php
    $title = 'Dashboard';
    $subtitle = 'Monitoring dan kelola kegiatan pendaftaran asisten';
    $icon = 'bx bxs-dashboard';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <?php 
        $stats = [
            [
                'label' => 'Total Pendaftar', 
                'value' => $totalPendaftar, 
                'icon' => 'bx bxs-group', 
                'icon_bg' => '#2563EB' // Bright Blue
            ],
            [
                'label' => 'Pendaftar Lulus', 
                'value' => $pendaftarLulus, 
                'icon' => 'bx bxs-check-shield', 
                'icon_bg' => '#16A34A' // Green
            ],
            [
                'label' => 'Pendaftar Pending', 
                'value' => $pendaftarPending, 
                'icon' => 'bx bxs-time-five', 
                'icon_bg' => '#FACC15' // Yellow
            ],
            [
                'label' => 'Pendaftar Gagal', 
                'value' => $pendaftarGagal, 
                'icon' => 'bx bxs-x-circle', 
                'icon_bg' => '#DC2626' // Red
            ],
        ];
        
        foreach($stats as $stat): 
        ?>
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-4 hover:shadow-md transition duration-200">
                <div class="rounded-xl flex items-center justify-center text-white shrink-0" 
                     style="width: 52px; height: 52px; font-size: 1.75rem; background-color: <?= $stat['icon_bg'] ?>;">
                    <i class='<?= $stat['icon'] ?>'></i>
                </div>
                <div class="flex-grow">
                    <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]"><?= $stat['label'] ?></p>
                    <h2 class="text-2xl font-black text-slate-800"><?= $stat['value'] ?></h2>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Presentation Progress & Upcoming Schedule -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Header with Button -->
            <div class="flex justify-between items-center gap-4">
                <h6 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class='bx bx-calendar text-blue-600'></i>Kalender Kegiatan Pendaftaran
                </h6>
                <button class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5" type="button" id="btnAddActivity">
                    <i class='bx bx-plus'></i> Tambah Kegiatan
                </button>
            </div>

            <!-- Calendar Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <!-- Navigation -->
                <div class="flex items-center justify-center gap-4 mb-6">
                    <button id="prevMonth" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center border-0 text-slate-600 transition" aria-label="Bulan sebelumnya">
                        <i class='bx bx-chevron-left text-lg'></i>
                    </button>
                    <h6 class="text-sm font-bold text-slate-800 mb-0" id="currentMonth"><?= $currentMonthName ?></h6>
                    <button id="nextMonth" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center border-0 text-slate-600 transition" aria-label="Bulan berikutnya">
                        <i class='bx bx-chevron-right text-lg'></i>
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse" id="calendarTable" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">MO</th>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">TU</th>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">WE</th>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">TH</th>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">FR</th>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">SA</th>
                                <th class="px-2 py-3 text-slate-400 text-xs font-bold text-center tracking-wider">SU</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            <?php 
                                $year = (int)date('Y');
                                $month = (int)date('n');
                                $eventsData = $kegiatanBulanIni ?? [];
                                include __DIR__ . '/partials/calendar_table.php'; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status Activities -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-4">
                    <h6 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class='bx bx-list-check text-blue-600'></i>Status Kegiatan
                    </h6>
                </div>
                <div class="flex-grow flex flex-col gap-4">
                <?php
                // Status metadata for calendar legend
                $statusMeta = $statusMeta ?? [];

                foreach ($statusKegiatan as $key => $status):
                    // Badge class already provided by Service via Controller
                    $badgeClass = $status['badgeClass'] ?? 'bg-light text-secondary border';
                ?>
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition duration-150">
                        <div class="flex justify-between items-center">
                            <div>
                                <h6 class="font-bold text-slate-700 text-sm mb-1"><?= htmlspecialchars($status['label']) ?></h6>
                                <div class="text-[10px] text-slate-400 font-medium">
                                    <?php if(!empty($status['deadline'])): ?>
                                        <div class="flex items-center gap-2">
                                            <span>Deadline: <?= date('d M Y', strtotime($status['deadline'])) ?></span>
                                            <button class="text-blue-600 hover:text-blue-700 edit-deadline-btn"
                                                    data-jenis="<?= $key ?>"
                                                    data-label="<?= htmlspecialchars($status['label']) ?>"
                                                    data-date="<?= $status['deadline'] ?>">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass ?>" style="font-size: 0.75rem;">
                                <?= htmlspecialchars($status['status']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Activity Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="addActivityModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-calendar-plus text-blue-600"></i>Tambah Kegiatan
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Tutup">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="addActivityForm" class="space-y-4">
                    <div>
                        <label for="judulKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Judul Kegiatan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="judul" id="judulKegiatan" required>
                    </div>
                    <div>
                        <label for="tanggalKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="tanggal" id="tanggalKegiatan" required>
                    </div>
                    <div>
                        <label for="deskripsiKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="deskripsi" id="deskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition cursor-pointer" data-modal-close>Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition border-0 cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Deadline Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="editDeadlineModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-sm scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit text-blue-600"></i>Edit Deadline
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Tutup">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="editDeadlineForm" class="space-y-4">
                    <input type="hidden" id="editDeadlineJenis" name="jenis">
                    <small class="text-slate-400 font-medium block" id="editDeadlineLabelName"></small>
                    <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" id="editDeadlineDate" name="tanggal" required>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition border-0 cursor-pointer">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Activity Detail/Action Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="activityActionModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100" style="max-width: 480px;">
        <div class="relative bg-white w-full bg-white rounded-[24px] shadow-2xl overflow-hidden relative">
            
            <!-- Header section (Gradient Blue to Cyan) -->
            <div class="bg-gradient-to-br from-primary to-secondary p-6 pb-5 relative">
                <button type="button" class="absolute top-3 right-3 w-8 h-8 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition-colors shadow-sm cursor-pointer border-0" data-modal-close aria-label="Tutup">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
                <h5 class="text-[17px] font-extrabold text-white leading-snug pr-8 tracking-tight uppercase" id="displayJudul"></h5>
            </div>

            <!-- Content section (White) -->
            <div class="p-6">
                <!-- Informasi Umum -->
                <div class="mb-5">
                    <div class="flex items-center gap-1.5 mb-2.5">
                        <i class="bx bx-info-circle text-slate-400 text-sm"></i>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Informasi Umum</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#E6F0FF] rounded-lg">
                        <i class="bx bx-calendar text-blue-600 text-sm"></i>
                        <span class="text-xs font-bold text-blue-700" id="displayTanggal"></span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-2">
                    <div class="flex items-center gap-1.5 mb-2.5">
                        <i class="bx bx-file text-slate-400 text-sm"></i>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi</span>
                    </div>
                    <div class="bg-[#F8FAFC] rounded-xl p-4 border border-slate-100">
                        <p class="text-[13px] text-slate-600 leading-relaxed whitespace-pre-wrap m-0" id="displayDeskripsi"></p>
                    </div>
                </div>
            </div>

            <!-- Actions — Kegiatan -->
            <div id="calendarActions" style="display: none;" class="px-6 pb-6 pt-0">
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold text-[13px] rounded-xl border border-slate-200 transition-all flex items-center justify-center gap-2 cursor-pointer" id="btnEditActivity">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button type="button" class="py-2.5 bg-[#C81E1E] hover:bg-red-700 text-white font-bold text-[13px] rounded-xl transition-all flex items-center justify-center gap-2 border-0 cursor-pointer" id="btnDeleteActivity">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>

            <!-- Actions — Wawancara/Presentasi -->
            <div id="calendarManageAction" style="display: none;" class="px-6 pb-6 pt-0">
                <button type="button" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2 border-0 cursor-pointer" id="btnManageSchedule">
                    <i class="bx bx-calendar-event text-lg"></i> Kelola Penjadwalan
                </button>
            </div>

        </div>
    </div>
</div>


<!-- Edit Activity Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="editActivityModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit-alt text-blue-600"></i>Edit Kegiatan
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Tutup">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="editActivityForm" class="space-y-4">
                    <input type="hidden" name="id" id="editIdKegiatan">
                    <div>
                        <label for="editJudulKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Judul Kegiatan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="judul" id="editJudulKegiatan" required>
                    </div>
                    <div>
                        <label for="editTanggalKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="tanggal" id="editTanggalKegiatan" required>
                    </div>
                    <div>
                        <label for="editDeskripsiKegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="deskripsi" id="editDeskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition" data-modal-close>Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.eventsData = <?= json_encode($kegiatanBulanIni ?? []) ?>;
    window.baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
</script>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/admin/dashboard.js"></script>
