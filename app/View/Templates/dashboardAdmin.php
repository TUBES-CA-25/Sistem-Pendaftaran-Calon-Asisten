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

<style>
/* ==================== GLOBAL STYLES ==================== */
.dashboard-full-wrapper {
    background: #f1f5f9;
    min-height: calc(100vh - 60px);
    margin: 0;
    padding: 0;
}

/* Force full width by removing parent padding */
.main-content {
    padding: 0 !important;
}

/* ==================== HEADER STYLES ==================== */
.page-header {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
    padding: 0 2rem 3.5rem 2rem;
    margin: 0;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.page-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 5%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.header-content {
    position: relative;
    z-index: 1;
    padding-top: 3.5rem;
    max-width: 100%;
}

.header-breadcrumb {
    color: rgba(255,255,255,0.9);
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.25rem;
}

.header-subtitle {
    color: rgba(255,255,255,0.8);
    font-size: 0.95rem;
    margin: 0;
}

.header-stats {
    display: flex;
    gap: 1rem;
}

.stat-badge {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    text-align: center;
    min-width: 100px;
}

.stat-badge .stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    display: block;
}

.stat-badge .stat-label {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* NEW STATS CARDS STYLES */
.new-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.new-stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.new-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.stat-icon-large {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    flex-shrink: 0;
}

.stat-icon-large.blue { background: #2563eb; }
.stat-icon-large.green { background: #16a34a; }
.stat-icon-large.yellow { background: #ca8a04; }
.stat-icon-large.red { background: #dc2626; }

.stat-details {
    display: flex;
    flex-direction: column;
}

.stat-details .label {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.stat-details .number {
    color: #0f172a;
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
}

.dashboard-container {
    padding: 1.5rem;
    margin-top: -30px; /* Overlap effect */
    position: relative;
    z-index: 10;
}

/* ==================== JADWAL PRESENTASI CARD ==================== */
.schedule-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 1.5rem;
}

.schedule-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.schedule-card-header .title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
}

.schedule-card-header .title i {
    color: #2563eb;
    font-size: 1.5rem;
}

.schedule-card-header .view-all {
    color: #2563eb;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: color 0.2s;
}

.schedule-card-header .view-all:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.schedule-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.schedule-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #2563eb;
    transition: transform 0.2s, box-shadow 0.2s;
}

.schedule-item:hover {
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.schedule-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    padding: 0.5rem;
    background: #2563eb;
    border-radius: 8px;
    color: white;
}

.schedule-date .day {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}

.schedule-date .month {
    font-size: 0.7rem;
    font-weight: 500;
    text-transform: uppercase;
}

.schedule-info {
    flex: 1;
    min-width: 0;
}

.schedule-info .name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.schedule-info .judul {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.schedule-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.8rem;
    color: #64748b;
}

.schedule-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.schedule-meta i {
    font-size: 1rem;
}

.schedule-empty {
    text-align: center;
    padding: 2rem;
    color: #94a3b8;
}

.schedule-empty i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    display: block;
}

@media (max-width: 768px) {
    .schedule-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .schedule-date {
        flex-direction: row;
        gap: 0.5rem;
        min-width: auto;
        padding: 0.5rem 1rem;
    }

    .schedule-meta {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
}
</style>

<div class="dashboard-full-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content d-flex justify-content-between align-items-center flex-wrap gap-4" style="padding-left: 2rem; padding-right: 2rem;">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <span class="header-breadcrumb">Dashboard</span>
                    <h1 class="header-title">Hello Admin 👋</h1>
                    <p class="header-subtitle">Let's learn something new today!</p>
                </div>
            </div>
            <!-- Header stats removed -->
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="dashboard-container">
        
        <!-- New Stats Cards Grid -->
        <div class="new-stats-grid">
            <!-- Total Pendaftar -->
            <div class="new-stat-card">
                <div class="stat-icon-large blue">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="stat-details">
                    <span class="label">Total Pendaftar</span>
                    <span class="number"><?= $totalPendaftar ?></span>
                </div>
            </div>

            <!-- Lulus -->
            <div class="new-stat-card">
                <div class="stat-icon-large green">
                    <i class='bx bxs-check-shield'></i>
                </div>
                <div class="stat-details">
                    <span class="label">Pendaftar Lulus</span>
                    <span class="number"><?= $pendaftarLulus ?></span>
                </div>
            </div>

            <!-- Pending -->
            <div class="new-stat-card">
                <div class="stat-icon-large yellow">
                    <i class='bx bxs-time-five'></i>
                </div>
                <div class="stat-details">
                    <span class="label">Pendaftar Pending</span>
                    <span class="number"><?= $pendaftarPending ?></span>
                </div>
            </div>

            <!-- Gagal -->
            <div class="new-stat-card">
                <div class="stat-icon-large red">
                    <i class='bx bxs-x-circle'></i>
                </div>
                <div class="stat-details">
                    <span class="label">Pendaftar Gagal</span>
                    <span class="number"><?= $pendaftarGagal ?></span>
                </div>
            </div>
        </div>

        <!-- Jadwal Presentasi Mendatang -->
        <div class="schedule-card">
            <div class="schedule-card-header">
                <div class="title">
                    <i class='bx bx-calendar-event'></i>
                    Jadwal Presentasi Mendatang
                </div>
                <a href="#" data-page="presentasi" class="view-all">Lihat Semua</a>
            </div>
            <div class="schedule-list">
                <?php if (empty($jadwalPresentasiMendatang)): ?>
                    <div class="schedule-empty">
                        <i class='bx bx-calendar-x'></i>
                        <p>Belum ada jadwal presentasi mendatang</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jadwalPresentasiMendatang as $jadwal):
                        $tanggal = new DateTime($jadwal['tanggal']);
                        $day = $tanggal->format('d');
                        $month = $tanggal->format('M');
                        $waktu = date('H:i', strtotime($jadwal['waktu']));
                    ?>
                    <div class="schedule-item">
                        <div class="schedule-date">
                            <span class="day"><?= $day ?></span>
                            <span class="month"><?= $month ?></span>
                        </div>
                        <div class="schedule-info">
                            <div class="name"><?= htmlspecialchars($jadwal['nama_lengkap']) ?></div>
                            <div class="judul"><?= htmlspecialchars($jadwal['judul']) ?></div>
                            <div class="schedule-meta">
                                <span><i class='bx bx-time'></i> <?= $waktu ?> WIB</span>
                                <span><i class='bx bx-building'></i> <?= htmlspecialchars($jadwal['ruangan']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    <div class="content-row">
        <div class="card-box">
            <div class="calendar-header">
                <div class="title">
                    <i class='bx bx-calendar'></i>
                    Kalender Kegiatan Pendaftaran
                </div>
                <button class="btn-primary-custom" type="button">
                    <i class='bx bx-plus'></i> Tambah Kegiatan
                </button>
            </div>
            <div class="calendar-nav">
                <button id="prevMonth" type="button"><i class='bx bx-chevron-left'></i></button>
                <span class="month-name" id="currentMonth"><?= $currentMonthName ?></span>
                <button id="nextMonth" type="button"><i class='bx bx-chevron-right'></i></button>
            </div>
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>MO</th>
                        <th>TU</th>
                        <th>WE</th>
                        <th>TH</th>
                        <th>FR</th>
                        <th>SA</th>
                        <th>SU</th>
                    </tr>
                </thead>
                <tbody id="calendarBody"></tbody>
            </table>
        </div>

        <div class="card-box">
            <div class="progress-steps">
                <div class="step-item">
                    <div class="step-dot red">1</div>
                </div>
                <div class="step-item">
                    <div class="step-dot yellow">2</div>
                </div>
                <div class="step-item">
                    <div class="step-dot green">3</div>
                </div>
                <div class="step-item">
                    <div class="step-dot blue">4</div>
                </div>
            </div>

            <div class="legends">
                <div class="legend">
                    <div class="dot red"></div>
                    <span class="text">Kelengkapan Berkas</span>
                </div>
                <div class="legend">
                    <div class="dot green"></div>
                    <span class="text">Tahap Wawancara</span>
                </div>
                <div class="legend">
                    <div class="dot yellow"></div>
                    <span class="text">Tes Tertulis</span>
                </div>
                <div class="legend">
                    <div class="dot blue"></div>
                    <span class="text">Pengumuman</span>
                </div>
            </div>

            <div class="status-title">Status Kegiatan</div>

            <div class="status-list">
                <div class="status-item">
                    <div>
                        <h6>Kelengkapan Berkas</h6>
                        <p>Jumlah Diterima : <?= $jumlahKelengkapanBerkas ?></p>
                    </div>
                    <span class="badge-status success">Selesai</span>
                </div>
                <div class="status-item">
                    <div>
                        <h6>Tes Tertulis</h6>
                        <p>Jumlah Diterima : <?= $jumlahTesTertulis ?></p>
                    </div>
                    <span class="badge-status warning">Menunggu</span>
                </div>
                <div class="status-item">
                    <div>
                        <h6>Tahap Wawancara</h6>
                        <p>Jumlah Diterima : <?= $jumlahTahapWawancara ?></p>
                    </div>
                    <span class="badge-status warning">Menunggu</span>
                </div>
                <div class="status-item">
                    <div>
                        <h6>Pengumuman</h6>
                        <p>Jumlah Diterima : <?= $jumlahPengumuman ?></p>
                    </div>
                    <span class="badge-status warning">Menunggu</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
(function() {
    const eventDates = <?= json_encode(array_map(function ($item) {
        return date('Y-m-d', strtotime($item['tanggal']));
    }, $kegiatanBulanIni)) ?>;

    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();

    function generateCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();
        const adjustedStart = startDay === 0 ? 6 : startDay - 1;

        const calendarBody = document.getElementById('calendarBody');
        if (!calendarBody) return; // Guard clause if element not found
        
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
                    if (eventDates.includes(dateStr)) {
                        cell.classList.add('event');
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
            if (date > daysInMonth) break;
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
