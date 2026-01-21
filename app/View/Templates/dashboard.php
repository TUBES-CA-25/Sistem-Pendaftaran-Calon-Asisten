<?php
use app\Controllers\notifications\NotificationControllers;
use app\Controllers\user\DashboardUserController;
use App\Controllers\Profile\ProfileController;
use app\Controllers\user\WawancaraController;

// 1. DATA PHP & PROGRESS
$role = ProfileController::viewUser()["role"];
$bio = DashboardUserController::getBiodataDetail();
$persen = DashboardUserController::getPercentage();
$totalSelesai = DashboardUserController::getNumberTahapanSelesai();
$jurusan = ProfileController::viewBiodata() == null ? "Jurusan" : ProfileController::viewBiodata()["jurusan"];
$noHp = ProfileController::viewBiodata() == null ? "No Telephone" : ProfileController::viewBiodata()["noHp"];

// 2. JADWAL (Filter 3 terdekat untuk Preview Dashboard)
$wawancara = WawancaraController::getAllById();
$upcomingJadwal = array_slice(array_filter($wawancara ?? [], function($j) {
    return isset($j['tanggal']) && $j['tanggal'] >= date('Y-m-d');
}), 0, 3);

// 3. NOTIFIKASI
$notifikasi = NotificationControllers::getMessageById() ?: [];

// 4. TAHAPAN SELEKSI
$tahapan = [
    ["Lengkapi Biodata", DashboardUserController::getBiodataStatus(), "person"],
    ["Lengkapi Berkas", DashboardUserController::getBerkasStatus(), "folder_shared"],
    ["Tes Tertulis", DashboardUserController::getAbsensiTesTertulis(), "quiz"],
    ["Submit Judul & PPT", DashboardUserController::getPptJudulAccStatus(), "upload_file"],
    ["Submit Makalah", DashboardUserController::getPptStatus(), "article"],
    ["Presentasi", DashboardUserController::getAbsensiPresentasi(), "co_present"],
    ["Wawancara Asisten", DashboardUserController::getAbsensiWawancaraI(), "group"],
    ["Wawancara Kepala Lab 1", DashboardUserController::getAbsensiWawancaraII(), "supervisor_account"],
    ["Wawancara Kepala Lab 2", DashboardUserController::getAbsensiWawancaraIII(), "manage_accounts"],
];

// 5. KALENDER PHP
$bulanSekarang = date('n'); $tahunSekarang = date('Y');
$jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulanSekarang, $tahunSekarang);
$hariPertama = date('w', strtotime("$tahunSekarang-$bulanSekarang-01"));
$namaBulan = date('F Y');
?>

<link rel="stylesheet" href="<?= APP_URL ?>/public/Assets/Style/dashboardStyle.css">

<main class="dashboard-container">
    
    <div class="welcome-banner">
        <div class="banner-text">
            <h1>Hello, <?= htmlspecialchars($bio['nama_lengkap'] ?? $_SESSION['user']['username']) ?> 👋</h1>
            <p>Pantau progres seleksi dan jadwalmu di sini.</p>
        </div>
        <div class="notification-badge">
            <button type="button" class="btn-notif" id="viewMessageButton">
                <span class="material-symbols-outlined">notifications</span>
                <?php if(count($notifikasi) > 0): ?> <span class="badge-dot"></span> <?php endif; ?>
            </button>
        </div>
    </div>

    <div class="dashboard-grid">
        
        <div class="left-column">
            <div class="card stats-card">
                <div class="card-body flex-row">
                    <div class="progress-circle" data-percentage="<?= $persen ?>">
                        <svg><circle class="bg" cx="50" cy="50" r="45"></circle><circle class="progress-bar" cx="50" cy="50" r="45"></circle></svg>
                        <div class="percentage-text"><h3><?= $persen ?>%</h3><small>Selesai</small></div>
                    </div>
                    <div class="stats-info">
                        <h3>Capaian Seleksi</h3>
                        <p>Selesai <b><?= $totalSelesai ?></b> dari <b>9</b> tahapan.</p>
                        <div class="status-tags"><span class="tag <?= ($persen > 0) ? 'active' : '' ?>"><?= ($persen == 100) ? 'Lengkap' : 'Berproses' ?></span></div>
                    </div>
                </div>
            </div>

            <div class="card doc-card">
                <div class="card-header"><h3>Tahapan Seleksi</h3><small>Real-time</small></div>
                <div class="doc-list-scroll"> 
                    <?php foreach($tahapan as $t): 
                        $badgeClass = ($t[1] === true || $t[1] == 1 || $t[1] === 'diterima' || $t[1] === 'Hadir') ? 'success' : (($t[1] === 'revisi') ? 'warning' : 'secondary');
                    ?>
                        <div class="doc-item">
                            <div class="doc-icon <?= ($badgeClass == 'success') ? 'primary' : 'info' ?>"><span class="material-symbols-outlined"><?= $t[2] ?></span></div>
                            <div class="doc-info"><span><?= $t[0] ?></span></div>
                            <span class="status-pill <?= $badgeClass ?>"><?= ($badgeClass == 'success') ? 'Selesai' : 'Belum' ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div> 
        
        <div class="right-column">
            <div class="card calendar-card">
                <div class="calendar-header"><h3><?= $namaBulan ?></h3></div>
                <div class="calendar-grid-php">
                    <div class="day-name">M</div><div class="day-name">S</div><div class="day-name">S</div><div class="day-name">R</div><div class="day-name">K</div><div class="day-name">J</div><div class="day-name">S</div>
                    <?php for($k=0; $k < $hariPertama; $k++): ?><div class="calendar-day empty"></div><?php endfor; ?>
                    <?php for($hari=1; $hari <= $jumlahHari; $hari++): 
                        $isToday = ($hari == date('j'));
                    ?><div class="calendar-day <?= $isToday ? 'today' : '' ?>"><?= $hari ?></div><?php endfor; ?>
                </div> 
                
                <div class="upcoming-section">
                    <div class="section-title-row">
                        <h4>Jadwal Terdekat</h4>
                        <a href="javascript:void(0)" id="openScheduleBtn">Lihat Semua</a>
                    </div>
                    <div class="upcoming-list scrollable-list">
                        <?php if (empty($upcomingJadwal)) : ?>
                            <div class="empty-state">
                                <span class="material-symbols-outlined">event_busy</span>
                                <p>Tidak ada jadwal terdekat.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($upcomingJadwal as $val) : 
                                $date = date_create($val['tanggal']);
                                $jam = substr($val['waktu'], 0, 5);
                            ?>
                            <div class="schedule-item">
                                <div class="date-box">
                                    <span class="day"><?= date_format($date, "d") ?></span>
                                    <span class="month"><?= date_format($date, "M") ?></span>
                                </div>
                                <div class="schedule-details">
                                    <span class="sched-title"><?= htmlspecialchars($val['jenis_wawancara']) ?></span>
                                    <div class="sched-meta">
                                        <span><i class='bx bx-time'></i> <?= $jam ?></span>
                                        <span><i class='bx bx-map'></i> <?= htmlspecialchars($val['ruangan']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>     
            
            <div class="card biodata-card">
                <div class="card-header"><h3>Biodata Diri</h3><a href="<?= APP_URL ?>/biodata"><span class="material-symbols-outlined">edit</span></a></div>
                <div class="card-body">
                    <div class="bio-row"><span class="label">Nama</span><span class="value"><?= htmlspecialchars($bio['nama_lengkap'] ?? '-') ?></span></div>
                    <div class="bio-row"><span class="label">NIM</span><span class="value"><?= htmlspecialchars($bio['stambuk'] ?? '-') ?></span></div>
                    <div class="bio-row"><span class="label">Prodi</span><span class="value"><?= htmlspecialchars($jurusan ?? '-') ?></span></div>
                    <div class="bio-row"><span class="label">No. HP</span><span class="value"><?= htmlspecialchars($noHp ?? '-') ?></span></div>
                </div>
            </div> 
        </div> 
    </div>
</main>

<div id="scheduleModal" class="custom-modal" style="display: none;">
  <div class="custom-modal-content modal-lg"> 
    
    <div class="custom-modal-header">
      <h3>
          <i class='bx bx-calendar-event' style="color: #4B8DF8;"></i> 
          Jadwal Lengkap
      </h3>
      <button id="closeScheduleBtn">&times;</button>
    </div>
    
    <div class="custom-modal-body">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Kegiatan</th>
                        <th width="40%">Lokasi</th>
                        <th width="25%">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($wawancara)): ?>
                        <tr><td colspan="4" style="text-align:center; padding: 30px; color:#999;">Belum ada jadwal kegiatan.</td></tr>
                    <?php else: $no=1; ?>
                    <?php foreach ($wawancara as $val) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span style="font-weight:600; color:#333;"><?= htmlspecialchars($val['jenis_wawancara']) ?></span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <i class='bx bx-map' style="color:#999;"></i>
                                    <?= htmlspecialchars($val['ruangan']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column;">
                                    <span style="font-weight:500;"><?= date('d M Y', strtotime($val['tanggal'])) ?></span>
                                    <small style="color:#888;"><?= substr($val['waktu'], 0, 5) ?> WITA</small>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

<div id="customMessageModal" class="custom-modal" style="display: none;">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3><i class='bx bx-bell' style="color: #FFC107;"></i> Notifikasi</h3>
            <button id="closeModalButton">&times;</button>
        </div>
        <div class="custom-modal-body">
            <?php if (empty($notifikasi)): ?>
                <div class="empty-state">
                    <p>Tidak ada pesan baru.</p>
                </div>
            <?php else: ?>
                <?php foreach($notifikasi as $n): 
                    // Format Tanggal: 21 Jan, 14:30
                    $waktu = isset($n['created_at']) ? date('d M, H:i', strtotime($n['created_at'])) : '';
                ?>
                <div class="notif-item">
                    <div class="notif-header">
                        <span class="notif-sender">Admin ICLABS</span>
                        <span class="notif-time"><?= $waktu ?></span>
                    </div>
                    <p class="notif-message"><?= htmlspecialchars($n['pesan']) ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/Script/user/dashboardScript.js"></script>
<script src="<?= APP_URL ?>/Script/sidebar/ScriptSidebar.js"></script> 
<script>
  // Logic Modal Sederhana
  const setupModal = (btnId, modalId, closeId) => {
      const btn = document.getElementById(btnId);
      const modal = document.getElementById(modalId);
      const close = document.getElementById(closeId);
      if(btn) btn.onclick = () => modal.style.display = "flex";
      if(close) close.onclick = () => modal.style.display = "none";
      window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; }
  };
  setupModal("viewMessageButton", "customMessageModal", "closeModalButton");
  setupModal("openScheduleBtn", "scheduleModal", "closeScheduleBtn");
</script>