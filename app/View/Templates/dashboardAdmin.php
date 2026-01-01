<?php

use App\Controllers\admin\DashboardAdminController;

$currentYear = date('Y');
$currentMonth = date('m');
$currentMonthName = date('F Y');

// Get statistics
$totalPendaftar = DashboardAdminController::getTotalPendaftar();
$pendaftarLulus = DashboardAdminController::getPendaftarLulus();
$pendaftarPending = DashboardAdminController::getPendaftarPending();
$pendaftarGagal = DashboardAdminController::getPendaftarGagal();

// Get activity status
$statusKegiatan = DashboardAdminController::getStatusKegiatan();

// Get calendar events
$kegiatanBulanIni = DashboardAdminController::getKegiatanByMonth($currentYear, $currentMonth) ?? [];

$jumlahKelengkapanBerkas = $statusKegiatan['kelengkapan_berkas']['jumlah'] ?? 0;
$jumlahTesTertulis = $statusKegiatan['tes_tertulis']['jumlah'] ?? 0;
$jumlahTahapWawancara = $statusKegiatan['tahap_wawancara']['jumlah'] ?? 0;
$jumlahPengumuman = $statusKegiatan['pengumuman']['jumlah'] ?? 0;
?>

<div class="dashboard-wrapper">
    <div class="dashboard-header-banner">
        <h1>Dashboard</h1>
        <h2>Hello Admin 👋</h2>
        <p>Let's learn something new today!</p>
    </div>

    <div class="stats-cards">
        <div class="stat-card">
            <div class="icon-box blue"><i class='bx bxs-group'></i></div>
            <div class="stat-info">
                <div class="label">Total Pendaftar</div>
                <div class="value"><?= $totalPendaftar ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon-box green"><i class='bx bxs-group'></i></div>
            <div class="stat-info">
                <div class="label">Pendaftar Lulus</div>
                <div class="value"><?= $pendaftarLulus ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon-box yellow"><i class='bx bxs-group'></i></div>
            <div class="stat-info">
                <div class="label">Pendaftar Pending</div>
                <div class="value"><?= $pendaftarPending ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon-box red"><i class='bx bxs-group'></i></div>
            <div class="stat-info">
                <div class="label">Pendaftar Gagal</div>
                <div class="value"><?= $pendaftarGagal ?></div>
            </div>
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
