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
                <div class="rounded-xl flex items-center justify-content-center text-white shrink-0" 
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
                <div class="flex items-center justify-content-center gap-4 mb-6">
                    <button id="prevMonth" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-content-center border-0 text-slate-600 transition">
                        <i class='bx bx-chevron-left text-lg'></i>
                    </button>
                    <h6 class="text-sm font-bold text-slate-800 mb-0" id="currentMonth"><?= $currentMonthName ?></h6>
                    <button id="nextMonth" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-content-center border-0 text-slate-600 transition">
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
                use App\Controllers\Admin\DashboardAdminController;
                $statusMeta = DashboardAdminController::getStatusMetadata();

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
<div class="modal fade" id="addActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-calendar-plus text-blue-600"></i>Tambah Kegiatan
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="addActivityForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Judul Kegiatan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="judul" id="judulKegiatan" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="tanggal" id="tanggalKegiatan" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="deskripsi" id="deskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Deadline Modal -->
<div class="modal fade" id="editDeadlineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit text-blue-600"></i>Edit Deadline
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="editDeadlineForm" class="space-y-4">
                    <input type="hidden" id="editDeadlineJenis" name="jenis">
                    <small class="text-slate-400 font-medium block" id="editDeadlineLabelName"></small>
                    <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" id="editDeadlineDate" name="tanggal" required>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activity Detail/Action Modal -->
<div class="modal fade" id="activityActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-sm font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2" id="actionModalTitle">Detail Kegiatan</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg text-base"></i>
                    </button>
                </div>
                <div id="actionModalContent" class="mb-6">
                    <h6 class="font-extrabold text-blue-600 text-base mb-1" id="displayJudul"></h6>
                    <p class="text-slate-400 text-xs flex items-center gap-1 mb-3"><i class="bx bx-calendar"></i><span id="displayTanggal"></span></p>
                    <p class="text-slate-600 text-sm leading-relaxed" id="displayDeskripsi"></p>
                </div>
                <div id="calendarActions" style="display: none;" class="space-y-2 pt-4 border-t border-slate-100">
                    <button type="button" class="w-full py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5" id="btnEditActivity">
                        <i class="bx bx-edit-alt"></i> Edit Kegiatan
                    </button>
                    <button type="button" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5" id="btnDeleteActivity">
                        <i class="bx bx-trash"></i> Hapus Kegiatan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Activity Modal -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit-alt text-blue-600"></i>Edit Kegiatan
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <form id="editActivityForm" class="space-y-4">
                    <input type="hidden" name="id" id="editIdKegiatan">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Judul Kegiatan</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="judul" id="editJudulKegiatan" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="tanggal" id="editTanggalKegiatan" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold bg-white transition" name="deskripsi" id="editDeskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
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
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/admin-dashboard.js"></script>
