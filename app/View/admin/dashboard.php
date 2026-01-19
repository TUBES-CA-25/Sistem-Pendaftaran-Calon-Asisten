<?php
/**
 * Dashboard Admin View
 *
 * Data yang diterima dari controller:
 * @var int $totalPendaftar - Total pendaftar
 * @var int $pendaftarLulus - Jumlah lulus
 * @var int $pendaftarPending - Jumlah pending
 * @var int $pendaftarGagal - Jumlah gagal
 * @var array $statusKegiatan - Status kegiatan
 * @var array $kegiatanBulanIni - Kegiatan bulan ini
 * @var array $jadwalPresentasiMendatang - Jadwal presentasi mendatang
 */
$currentYear = date('Y');
$currentMonth = date('m');
$currentMonthName = date('F Y');

$totalPendaftar = $totalPendaftar ?? 0;
$pendaftarLulus = $pendaftarLulus ?? 0;
$pendaftarPending = $pendaftarPending ?? 0;
$pendaftarGagal = $pendaftarGagal ?? 0;
$statusKegiatan = $statusKegiatan ?? [];
$kegiatanBulanIni = $kegiatanBulanIni ?? [];
$jadwalPresentasiMendatang = $jadwalPresentasiMendatang ?? [];

$jumlahKelengkapanBerkas = $statusKegiatan['kelengkapan_berkas']['jumlah'] ?? 0;
$jumlahTesTertulis = $statusKegiatan['tes_tertulis']['jumlah'] ?? 0;
$jumlahTahapWawancara = $statusKegiatan['tahap_wawancara']['jumlah'] ?? 0;
$jumlahPengumuman = $statusKegiatan['pengumuman']['jumlah'] ?? 0;
?>

<!-- Page Header -->
<?php
    $title = 'Hello Admin 👋';
    $subtitle = "Let's learn something new today!";
    $icon = 'bx bx-home-circle';
    require_once __DIR__ . '/../templates/components/PageHeader.php';
?>

<!-- Main Content Container -->
<div class="container-fluid px-4 py-4" style="margin-top: -30px; position: relative; z-index: 10;">

    <!-- Stats Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Total Pendaftar -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 text-white" style="width: 60px; height: 60px; background: #2563eb; font-size: 2rem;">
                        <i class='bx bxs-group'></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium">Total Pendaftar</span>
                        <div class="fs-3 fw-bold text-dark" id="stat-total"><?= $totalPendaftar ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lulus -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 text-white" style="width: 60px; height: 60px; background: #16a34a; font-size: 2rem;">
                        <i class='bx bxs-check-shield'></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium">Pendaftar Lulus</span>
                        <div class="fs-3 fw-bold text-dark" id="stat-lulus"><?= $pendaftarLulus ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 text-white" style="width: 60px; height: 60px; background: #ca8a04; font-size: 2rem;">
                        <i class='bx bxs-time-five'></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium">Pendaftar Pending</span>
                        <div class="fs-3 fw-bold text-dark" id="stat-pending"><?= $pendaftarPending ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gagal -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 text-white" style="width: 60px; height: 60px; background: #dc2626; font-size: 2rem;">
                        <i class='bx bxs-x-circle'></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium">Pendaftar Gagal</span>
                        <div class="fs-3 fw-bold text-dark" id="stat-gagal"><?= $pendaftarGagal ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Presentasi Mendatang -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header border-0 d-flex align-items-center justify-content-between py-3 px-4" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
            <div class="d-flex align-items-center gap-2">
                <i class='bx bx-calendar-event text-white fs-4'></i>
                <span class="fw-semibold text-white">Jadwal Presentasi Mendatang</span>
            </div>
            <a href="#" data-page="presentasi" class="text-white text-decoration-none small fw-medium" style="opacity: 0.9;">
                Lihat Semua <i class='bx bx-chevron-right'></i>
            </a>
        </div>
        <div class="card-body p-4">
            <?php if (empty($jadwalPresentasiMendatang)): ?>
                <div class="text-center text-muted py-4">
                    <i class='bx bx-calendar-x fs-1 d-block mb-2'></i>
                    <p class="mb-0">Belum ada jadwal presentasi mendatang</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($jadwalPresentasiMendatang as $jadwal):
                        $tanggal = new DateTime($jadwal['tanggal']);
                        $day = $tanggal->format('d');
                        $month = $tanggal->format('F');
                        $year = $tanggal->format('Y');
                        $waktu = date('H:i', strtotime($jadwal['waktu']));
                    ?>
                    <div class="jadwal-card p-3 rounded-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white;">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class='bx bx-calendar fs-4'></i>
                                <div>
                                    <div class="fw-semibold">Jadwal Presentasi Anda</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-white" style="border-opacity: 0.3 !important;">
                            <i class='bx bx-calendar-event'></i>
                            <span>Tanggal</span>
                            <span class="ms-auto fw-semibold"><?= $day ?> <?= $month ?> <?= $year ?></span>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-white" style="border-opacity: 0.3 !important;">
                            <i class='bx bx-time'></i>
                            <span>Waktu</span>
                            <span class="ms-auto fw-semibold"><?= $waktu ?> WIB</span>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-white" style="border-opacity: 0.3 !important;">
                            <i class='bx bx-building'></i>
                            <span>Ruangan</span>
                            <span class="ms-auto fw-semibold"><?= htmlspecialchars($jadwal['ruangan']) ?></span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <i class='bx bx-book-open'></i>
                            <span>Judul</span>
                            <span class="ms-auto fw-semibold text-end"><?= htmlspecialchars($jadwal['judul']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Calendar & Status Row -->
    <div class="row g-4">
        <!-- Calendar Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header border-0 d-flex align-items-center justify-content-between py-3 px-4" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <i class='bx bx-calendar text-white fs-4'></i>
                        <span class="fw-semibold text-white">Kalender Kegiatan Pendaftaran</span>
                    </div>
                    <button class="btn btn-light rounded-3" type="button" id="btnAddActivity">
                        <i class='bx bx-plus'></i> Tambah Kegiatan
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                        <button id="prevMonth" type="button" class="btn btn-outline-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class='bx bx-chevron-left fs-5'></i>
                        </button>
                        <span class="fw-bold fs-5 text-primary" id="currentMonth" style="min-width: 200px; text-align: center;"><?= $currentMonthName ?></span>
                        <button id="nextMonth" type="button" class="btn btn-outline-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class='bx bx-chevron-right fs-5'></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0 calendar-table" id="calendarTable">
                            <thead>
                                <tr>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Sen</th>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Sel</th>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Rab</th>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Kam</th>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Jum</th>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Sab</th>
                                    <th class="py-3 bg-light fw-semibold" style="color: #64748b;">Min</th>
                                </tr>
                            </thead>
                            <tbody id="calendarBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Kegiatan Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <i class='bx bx-list-check text-white fs-4'></i>
                        <span class="fw-semibold text-white">Status Kegiatan</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($statusKegiatan as $key => $status): ?>
                        <div class="status-kegiatan-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-left: 4px solid #2563eb !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($status['label']) ?></h6>
                                <span class="badge rounded-pill px-3 py-2 <?= $status['css_class'] ?>" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($status['status']) ?>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class='bx bx-user-check text-primary fs-5'></i>
                                <span class="text-muted small">Jumlah Diterima: <strong class="text-dark"><?= $status['jumlah'] ?></strong></span>
                            </div>
                            <?php if(!empty($status['deadline'])): ?>
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class='bx bx-calendar-event text-danger'></i>
                                        <small class="text-muted">
                                            Deadline: <strong class="text-dark" id="deadline-display-<?= $key ?>"><?= date('d M Y', strtotime($status['deadline'])) ?></strong>
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary rounded-3 edit-deadline-btn"
                                            data-jenis="<?= $key ?>"
                                            data-label="<?= htmlspecialchars($status['label']) ?>"
                                            data-date="<?= $status['deadline'] ?>">
                                        <i class='bx bx-edit-alt'></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kegiatan (Bootstrap) -->
<div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-semibold" id="addActivityModalLabel">Tambah Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addActivityForm">
                    <div class="mb-3">
                        <label for="judulKegiatan" class="form-label fw-medium">Judul Kegiatan</label>
                        <input type="text" class="form-control form-control-lg rounded-3" id="judulKegiatan" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggalKegiatan" class="form-label fw-medium">Tanggal</label>
                        <input type="date" class="form-control form-control-lg rounded-3" id="tanggalKegiatan" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsiKegiatan" class="form-label fw-medium">Deskripsi</label>
                        <textarea class="form-control rounded-3" id="deskripsiKegiatan" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Deadline (Bootstrap) -->
<div class="modal fade" id="editDeadlineModal" tabindex="-1" aria-labelledby="editDeadlineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-semibold" id="editDeadlineModalLabel">Edit Deadline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editDeadlineForm">
                    <input type="hidden" id="editDeadlineJenis" name="jenis">
                    <div class="mb-3">
                        <label id="editDeadlineLabelName" class="form-label fw-semibold">Nama Kegiatan</label>
                    </div>
                    <div class="mb-3">
                        <label for="editDeadlineDate" class="form-label fw-medium">Tanggal Deadline Baru</label>
                        <input type="date" class="form-control form-control-lg rounded-3" id="editDeadlineDate" name="tanggal" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Calendar Table Styling */
.calendar-table {
    border: none !important;
}

.calendar-table thead th {
    border: none !important;
    font-size: 0.85rem;
    padding: 0.75rem !important;
}

.calendar-table tbody td {
    border: 1px solid #e5e7eb !important;
    height: 90px;
    vertical-align: top;
    padding: 0.75rem;
    position: relative;
    font-weight: 500;
    color: #334155;
    transition: all 0.2s ease;
    cursor: default;
}

.calendar-table tbody td:hover {
    background-color: #f8fafc;
}

.calendar-table tbody td.event {
    cursor: pointer;
    background-color: #fef3f2;
    border-color: #fecaca !important;
}

.calendar-table tbody td.event:hover {
    background-color: #fee2e2;
}

.calendar-table tbody td.event::after {
    content: '';
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}

.calendar-table tbody td.today {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
    border: 2px solid #3b82f6 !important;
    font-weight: 700;
    color: #2563eb;
}

.calendar-table tbody td.today.event {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.15) 100%);
    border: 2px solid #ef4444 !important;
}

/* Calendar Tooltip */
.calendar-tooltip {
    position: absolute;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    z-index: 1100;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    max-width: 300px;
}

.calendar-tooltip.show {
    opacity: 1;
}

.calendar-tooltip strong {
    color: #3dc2ec;
    display: block;
    margin-bottom: 0.25rem;
}

/* Status Kegiatan Card */
.status-kegiatan-card {
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.status-kegiatan-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
}

.status-kegiatan-card .badge {
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* Button Hover Effects */
.btn-outline-primary:hover {
    transform: scale(1.05);
    transition: all 0.2s ease;
}

/* Navigation Buttons */
.btn-outline-primary.rounded-circle:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    border-color: #2563eb;
    color: white;
    transform: scale(1.1);
}

.btn-light:hover {
    background: #f1f5f9;
}

/* Updated Primary Color for Gradient */
#currentMonth {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Jadwal Presentasi Card */
.jadwal-card {
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.jadwal-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.jadwal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.jadwal-card i {
    font-size: 1.25rem;
}
</style>

<script>
(function() {
    // Bootstrap Modal instances
    const addActivityModal = new bootstrap.Modal(document.getElementById('addActivityModal'));
    const editDeadlineModal = new bootstrap.Modal(document.getElementById('editDeadlineModal'));

    // --- Add Activity Button ---
    const btnAddActivity = document.getElementById('btnAddActivity');
    if (btnAddActivity) {
        btnAddActivity.addEventListener('click', () => addActivityModal.show());
    }

    // --- Edit Deadline Logic ---
    document.querySelectorAll('.edit-deadline-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const jenis = this.getAttribute('data-jenis');
            const label = this.getAttribute('data-label');
            const date = this.getAttribute('data-date');

            document.getElementById('editDeadlineJenis').value = jenis;
            document.getElementById('editDeadlineLabelName').textContent = label;
            document.getElementById('editDeadlineDate').value = date;

            editDeadlineModal.show();
        });
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
                showAlert('Simulasi: Kegiatan ditambahkan (Backend route needs setup)', true);
                location.reload();
            });
        };
    }

    // --- Real-time Stats Polling ---
    function updateDashboardStats() {
        if (typeof baseUrl === 'undefined') {
            var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
        }

        fetch(`${baseUrl}/dashboard/stats`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                document.getElementById('stat-total').innerText = data.total;
                document.getElementById('stat-lulus').innerText = data.lulus;
                document.getElementById('stat-pending').innerText = data.pending;
                document.getElementById('stat-gagal').innerText = data.gagal;
            }
        })
        .catch(console.error);
    }

    setInterval(updateDashboardStats, 5000);

    // --- Calendar Logic ---
    const eventsData = <?= json_encode($kegiatanBulanIni) ?>;

    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();

    // Tooltip Element
    const tooltip = document.createElement('div');
    tooltip.className = 'calendar-tooltip';
    document.body.appendChild(tooltip);

    function hideTooltip() {
        tooltip.classList.remove('show');
    }

    function generateCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();
        const adjustedStart = startDay === 0 ? 6 : startDay - 1;

        const calendarBody = document.getElementById('calendarBody');
        if (!calendarBody) return;

        calendarBody.innerHTML = '';

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const currentMonthEl = document.getElementById('currentMonth');
        if (currentMonthEl) {
            currentMonthEl.textContent = `${monthNames[month]} ${year}`;
        }

        let date = 1;
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                if (i === 0 && j < adjustedStart) {
                    cell.textContent = '';
                } else if (date > daysInMonth) {
                    cell.textContent = '';
                } else {
                    cell.textContent = date;
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                    const daysEvents = eventsData.filter(e => e.tanggal === dateStr);

                    if (daysEvents.length > 0) {
                        cell.classList.add('event');

                        cell.addEventListener('mouseenter', (e) => {
                            const tooltipContent = daysEvents.map(ev => {
                                let content = `<strong>${ev.judul}</strong>`;
                                if (ev.deskripsi) {
                                    content += `<br><span style="font-weight:normal; opacity:0.9">${ev.deskripsi}</span>`;
                                }
                                return content;
                            }).join('<br><br>');

                            tooltip.innerHTML = tooltipContent;
                            tooltip.classList.add('show');
                            tooltip.style.left = `${e.pageX}px`;
                            tooltip.style.top = `${e.pageY - 40}px`;
                        });

                        cell.addEventListener('mousemove', (e) => {
                            tooltip.style.left = `${e.pageX + 10}px`;
                            tooltip.style.top = `${e.pageY - 30}px`;
                        });

                        cell.addEventListener('mouseleave', () => hideTooltip());
                    }

                    const today = new Date();
                    if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        if (!cell.classList.contains('event')) {
                            cell.classList.add('today');
                        }
                    }

                    date++;
                }
                row.appendChild(cell);
            }
            calendarBody.appendChild(row);
        }
    }

    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentYear, currentMonth);
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentYear, currentMonth);
        });
    }

    generateCalendar(currentYear, currentMonth);
})();
</script>
