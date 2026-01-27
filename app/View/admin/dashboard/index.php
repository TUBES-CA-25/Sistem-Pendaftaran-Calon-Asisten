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
<div class="container-fluid px-4">

    <!-- Stats Cards Grid -->
    <div class="row g-4 mb-4">
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
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-4 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 60px; height: 60px; font-size: 2rem; background-color: <?= $stat['icon_bg'] ?>;">
                        <i class='<?= $stat['icon'] ?>'></i>
                    </div>
                    <div class="flex-grow-1 text-center">
                        <p class="text-muted mb-0 small"><?= $stat['label'] ?></p>
                        <h2 class="mb-0 fw-bold fs-3 text-dark"><?= $stat['value'] ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Presentation Progress & Upcoming Schedule -->
    <div class="row g-4 mb-4">
    </div>

    <div class="row g-4">
        <!-- Calendar -->
        <div class="col-lg-8">
            <!-- Header with Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class='bx bx-calendar me-2'></i>Kalender Kegiatan Pendaftaran</h6>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" type="button" id="btnAddActivity" style="background-color: #2563EB; border-color: #2563EB;">
                    <i class='bx bx-plus'></i> Tambah Kegiatan
                </button>
            </div>

            <!-- Calendar Card -->
            <div class="card border shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <!-- Navigation -->
                    <div class="d-flex align-items-center justify-content-center mb-4 gap-4">
                        <button id="prevMonth" class="btn btn-sm btn-light rounded-circle p-2 border-0">
                            <i class='bx bx-chevron-left fs-5'></i>
                        </button>
                        <h6 class="mb-0 fw-bold text-dark" id="currentMonth"><?= $currentMonthName ?></h6>
                        <button id="nextMonth" class="btn btn-sm btn-light rounded-circle p-2 border-0">
                            <i class='bx bx-chevron-right fs-5'></i>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table text-center align-middle mb-0" id="calendarTable" style="table-layout: fixed; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">MO</th>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">TU</th>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">WE</th>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">TH</th>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">FR</th>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">SA</th>
                                    <th class="border-0 text-muted small fw-normal text-uppercase py-3">SU</th>
                                </tr>
                            </thead>
                            <tbody id="calendarBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Status Activities -->
        <div class="col-lg-4">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-bottom-0 py-3 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class='bx bx-list-check me-2'></i>Status Kegiatan</h6>
                </div>
                <div class="card-body p-4 pt-0 d-flex flex-column gap-3">
                <?php
                // Status metadata for calendar legend
                use App\Services\Admin\ActivityStatusService;
                $statusMeta = ActivityStatusService::getStatusMetadata();

                foreach ($statusKegiatan as $key => $status):
                    // Badge class already provided by Service via Controller
                    $badgeClass = $status['badgeClass'] ?? 'bg-light text-secondary border';
                ?>
                    <div class="p-3 border rounded-4 mb-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($status['label']) ?></h6>
                                <div class="small text-muted">
                                    <div class="mb-1">Jumlah Diterima : <?= $status['jumlah'] ?></div>
                                    <?php if(!empty($status['deadline'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span>Deadline: <?= date('d M Y', strtotime($status['deadline'])) ?></span>
                                            <button class="btn btn-link p-0 text-primary edit-deadline-btn"
                                                    data-jenis="<?= $key ?>"
                                                    data-label="<?= htmlspecialchars($status['label']) ?>"
                                                    data-date="<?= $status['deadline'] ?>">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 fw-semibold text-nowrap <?= $badgeClass ?>" style="font-size: 0.75rem;">
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
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addActivityForm">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Judul Kegiatan</label>
                        <input type="text" class="form-control rounded-3" name="judul" id="judulKegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal</label>
                        <input type="date" class="form-control rounded-3" name="tanggal" id="tanggalKegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea class="form-control rounded-3" name="deskripsi" id="deskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Deadline Modal -->
<div class="modal fade" id="editDeadlineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Deadline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editDeadlineForm">
                    <input type="hidden" id="editDeadlineJenis" name="jenis">
                    <small class="text-muted d-block mb-2" id="editDeadlineLabelName"></small>
                    <input type="date" class="form-control form-control-lg rounded-3 mb-4" id="editDeadlineDate" name="tanggal" required>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-3">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activity Detail/Action Modal -->
<div class="modal fade" id="activityActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="actionModalTitle">Detail Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div id="actionModalContent" class="mb-4">
                    <h6 class="fw-bold text-primary mb-1" id="displayJudul"></h6>
                    <p class="text-muted small mb-2"><i class="bx bx-calendar me-1"></i><span id="displayTanggal"></span></p>
                    <p class="text-dark small mb-0" id="displayDeskripsi"></p>
                </div>
                <!-- Only show actions for type 'Kegiatan' (kegiatan_admin table) -->
                <div id="calendarActions" style="display: none;">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary rounded-3" id="btnEditActivity">
                            <i class="bx bx-edit-alt me-1"></i> Edit Kegiatan
                        </button>
                        <button type="button" class="btn btn-outline-danger rounded-3" id="btnDeleteActivity">
                            <i class="bx bx-trash me-1"></i> Hapus Kegiatan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Activity Modal -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editActivityForm">
                    <input type="hidden" name="id" id="editIdKegiatan">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Judul Kegiatan</label>
                        <input type="text" class="form-control rounded-3" name="judul" id="editJudulKegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal</label>
                        <input type="date" class="form-control rounded-3" name="tanggal" id="editTanggalKegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea class="form-control rounded-3" name="deskripsi" id="editDeskripsiKegiatan" rows="3"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Bootstrap Modal instance for deadline editing
    // Edit Deadline Logic - Use event delegation for dynamically loaded content
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.edit-deadline-btn')) {
            const btn = e.target.closest('.edit-deadline-btn');
            const jenis = btn.getAttribute('data-jenis');
            const label = btn.getAttribute('data-label');
            const date = btn.getAttribute('data-date');

            const jenisInput = document.getElementById('editDeadlineJenis');
            const labelEl = document.getElementById('editDeadlineLabelName');
            const dateInput = document.getElementById('editDeadlineDate');

            if (jenisInput && labelEl && dateInput) {
                jenisInput.value = jenis;
                labelEl.textContent = label;
                dateInput.value = date;

                // Trigger modal using data attribute
                const modalTrigger = document.createElement('button');
                modalTrigger.setAttribute('data-bs-toggle', 'modal');
                modalTrigger.setAttribute('data-bs-target', '#editDeadlineModal');
                modalTrigger.style.display = 'none';
                document.body.appendChild(modalTrigger);
                modalTrigger.click();
                document.body.removeChild(modalTrigger);
            }
        }
    });

    // Handle Edit Deadline Submit
    const editDeadlineForm = document.getElementById('editDeadlineForm');
    if (editDeadlineForm) {
        editDeadlineForm.onsubmit = function(e) {
            e.preventDefault();

            const formData = {
                jenis: document.getElementById('editDeadlineJenis').value,
                tanggal: document.getElementById('editDeadlineDate').value
            };

            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }

            fetch(`${baseUrl}/updatedeadline`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Deadline berhasil diperbarui!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        };
    }

    // Real-time Stats Polling
    function updateDashboardStats() {
        if (typeof baseUrl === 'undefined') {
            var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
        }

        const statTotal = document.getElementById('stat-total');
        const statLulus = document.getElementById('stat-lulus');
        const statPending = document.getElementById('stat-pending');
        const statGagal = document.getElementById('stat-gagal');

        if (!statTotal || !statLulus || !statPending || !statGagal) {
            // Console warn suppressed to avoid noise
            return;
        }

        fetch(`${baseUrl}/dashboard/stats`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                statTotal.innerText = data.total;
                statLulus.innerText = data.lulus;
                statPending.innerText = data.pending;
                statGagal.innerText = data.gagal;
            }
        })
        .catch(console.error);
    }

    // Only start interval if stats elements exist
    if (document.getElementById('stat-total')) {
        setInterval(updateDashboardStats, 5000);
    }

    // --- CALENDAR LOGIC ---

    // Add Activity Modal - Use data-bs-toggle instead of Bootstrap object
    const btnAddActivity = document.getElementById('btnAddActivity');
    if (btnAddActivity) {
        btnAddActivity.setAttribute('data-bs-toggle', 'modal');
        btnAddActivity.setAttribute('data-bs-target', '#addActivityModal');
    }

    // Handle Add Activity Submit
    const addActivityForm = document.getElementById('addActivityForm');
    if (addActivityForm) {
        addActivityForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = {
                judul: document.getElementById('judulKegiatan').value,
                tanggal: document.getElementById('tanggalKegiatan').value,
                deskripsi: document.getElementById('deskripsiKegiatan').value
            };
            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }
            fetch(`${baseUrl}/addkegiatan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Kegiatan berhasil ditambahkan!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        };
    }

    // Calendar Data & Functions
    const eventsData = <?= json_encode($kegiatanBulanIni ?? []) ?>;
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();

    // Click tracker for activities
    let selectedEvent = null;

    function generateCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();
        const adjustedStart = startDay === 0 ? 6 : startDay - 1; // Mon=0, Sun=6

        const calendarBody = document.getElementById('calendarBody');
        if (!calendarBody) return;

        calendarBody.innerHTML = '';

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const currentMonthEl = document.getElementById('currentMonth');
        if (currentMonthEl) {
            currentMonthEl.textContent = `${monthNames[month]} ${year}`;
        }

        let date = 1;
        // 6 rows max to cover all weeks
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            let hasDateInRow = false;

            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                // Apply inline styling for clean look with borders
                cell.style.height = '70px'; 
                cell.style.verticalAlign = 'middle';
                cell.style.border = '1px solid #E5E7EB';
                cell.style.position = 'relative';
                cell.style.padding = '12px';
                cell.className = 'text-dark';
                
                if (i === 0 && j < adjustedStart) {
                    cell.textContent = '';
                } else if (date > daysInMonth) {
                    cell.textContent = '';
                } else {
                    // Create date number
                    const dateSpan = document.createElement('div');
                    dateSpan.textContent = date;
                    dateSpan.style.fontSize = '14px';
                    dateSpan.style.fontWeight = '500';
                    dateSpan.style.color = '#1F2937'; // Dark gray/black color
                    
                    hasDateInRow = true;
                    
                    const monthPlus1 = String(month + 1).padStart(2, '0');
                    const datePad = String(date).padStart(2, '0');
                    const dateStr = `${year}-${monthPlus1}-${datePad}`;

                    const daysEvents = eventsData.filter(e => e.tanggal === dateStr);

                    // Check if this is today
                    const today = new Date();
                    const isToday = date === today.getDate() && month === today.getMonth() && year === today.getFullYear();

                    if (daysEvents.length > 0) {
                        cell.style.cursor = 'pointer';
                        cell.onclick = function() {
                            showActivityActions(daysEvents);
                        };

                        // Event date styling - light blue background
                        cell.style.backgroundColor = '#E0E7FF';
                        cell.style.borderRadius = '8px';
                        dateSpan.style.fontWeight = '600';
                        
                        // Add red dot below the date
                        const dot = document.createElement('div');
                        dot.style.width = '6px';
                        dot.style.height = '6px';
                        dot.style.backgroundColor = '#DC2626';
                        dot.style.borderRadius = '50%';
                        dot.style.margin = '4px auto 0';
                        
                        cell.appendChild(dateSpan);
                        cell.appendChild(dot);
                    } else if (isToday) {
                        // Today's date - blue border
                        cell.style.border = '2px solid #2563EB';
                        cell.style.borderRadius = '8px';
                        dateSpan.style.fontWeight = '700';
                        cell.appendChild(dateSpan);
                    } else {
                        // Regular date
                        cell.appendChild(dateSpan);
                    }

                    date++;
                }
                row.appendChild(cell);
            }
            if (hasDateInRow || i === 0) { 
                calendarBody.appendChild(row);
            }
            if (date > daysInMonth) break;
        }
    }

    function showActivityActions(events) {
        // For simplicity, we handle the first event of the day if multiple exist
        const event = events[0];
        selectedEvent = event;

        document.getElementById('displayJudul').textContent = event.judul;
        document.getElementById('displayTanggal').textContent = new Date(event.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('displayDeskripsi').textContent = event.deskripsi || 'Tidak ada deskripsi';
        
        const actionsDiv = document.getElementById('calendarActions');
        if (event.jenis === 'Kegiatan') {
            actionsDiv.style.display = 'block';
        } else {
            actionsDiv.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('activityActionModal'));
        modal.show();
    }

    // Edit Button Handler
    document.getElementById('btnEditActivity').onclick = function() {
        if (!selectedEvent) return;
        
        // Hide action modal
        bootstrap.Modal.getInstance(document.getElementById('activityActionModal')).hide();

        document.getElementById('editIdKegiatan').value = selectedEvent.id;
        document.getElementById('editJudulKegiatan').value = selectedEvent.judul;
        document.getElementById('editTanggalKegiatan').value = selectedEvent.tanggal;
        document.getElementById('editDeskripsiKegiatan').value = selectedEvent.deskripsi || '';

        const modal = new bootstrap.Modal(document.getElementById('editActivityModal'));
        modal.show();
    };

    // Delete Button Handler
    document.getElementById('btnDeleteActivity').onclick = function() {
        if (!selectedEvent) return;

        showConfirmDelete(() => {
            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }
            fetch(`${baseUrl}/deletekegiatan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: selectedEvent.id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('activityActionModal')).hide();
                    showAlert('Kegiatan berhasil dihapus!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        }, 'Hapus kegiatan ini?');
    };

    // Handle Edit Activity Submit
    const editActivityForm = document.getElementById('editActivityForm');
    if (editActivityForm) {
        editActivityForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = {
                id: document.getElementById('editIdKegiatan').value,
                judul: document.getElementById('editJudulKegiatan').value,
                tanggal: document.getElementById('editTanggalKegiatan').value,
                deskripsi: document.getElementById('editDeskripsiKegiatan').value
            };
            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }
            fetch(`${baseUrl}/updatekegiatan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Kegiatan berhasil diperbarui!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        };
    }

    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    if (prevMonthBtn) {
        prevMonthBtn.onclick = function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentYear, currentMonth);
        };
    }

    if (nextMonthBtn) {
        nextMonthBtn.onclick = function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentYear, currentMonth);
        };
    }

    // Init
    generateCalendar(currentYear, currentMonth);

})();
</script>
