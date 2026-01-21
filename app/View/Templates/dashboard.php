<?php
use app\Controllers\notifications\NotificationControllers;
use app\Controllers\user\DashboardUserController;
use App\Controllers\Profile\ProfileController;
use app\Controllers\user\WawancaraController;

// 1. DATA PENGGUNA & PROGRESS
$bio = DashboardUserController::getBiodataDetail();
$persen = DashboardUserController::getPercentage();
$totalSelesai = DashboardUserController::getNumberTahapanSelesai();
$jurusan = ProfileController::viewBiodata() == null ? "Jurusan" : ProfileController::viewBiodata()["jurusan"];
$noHp = ProfileController::viewBiodata() == null ? "No Telephone" : ProfileController::viewBiodata()["noHp"];

// 2. JADWAL
$wawancara = WawancaraController::getAllById();

// 3. NOTIFIKASI
$rawNotif = NotificationControllers::getMessageById();
$notifikasi = (is_array($rawNotif)) ? $rawNotif : [];

// 4. DEFINISI TAHAPAN
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

// 5. LOGIKA KALENDER PHP
$bulanSekarang = date('n');
$tahunSekarang = date('Y');
$jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulanSekarang, $tahunSekarang);
$hariPertama = date('w', strtotime("$tahunSekarang-$bulanSekarang-01")); // 0 (Minggu) - 6 (Sabtu)
$namaBulan = date('F Y'); 
?>

<link rel="stylesheet" href="<?= APP_URL ?>/Style/dashboardStyle.css">

<main class="dashboard-container">
    
    <div class="welcome-banner">
        <div class="banner-text">
            <h1>Hello, <?= htmlspecialchars($bio['nama_lengkap'] ?? $_SESSION['user']['username']) ?> 👋</h1>
            <p>Pantau progres seleksi dan jadwalmu di sini.</p>
        </div>
        <div class="notification-badge">
            <button type="button" class="btn-notif" id="viewMessageButton">
                <span class="material-symbols-outlined">notifications</span>
                <?php if(count($notifikasi) > 0): ?>
                    <span class="badge-dot"></span>
                <?php endif; ?>
            </button>
        </div>
    </div>

    <div class="dashboard-grid">
        
        <div class="left-column">
            
            <div class="card stats-card">
                <div class="card-body flex-row">
                    <div class="progress-circle" data-percentage="<?= $persen ?>">
                        <svg>
                            <circle class="bg" cx="50" cy="50" r="45"></circle>
                            <circle class="progress-bar" cx="50" cy="50" r="45"></circle>
                        </svg>
                        <div class="percentage-text">
                            <h3><?= $persen ?>%</h3>
                            <small>Selesai</small>
                        </div>
                    </div>
                    <div class="stats-info">
                        <h3>Capaian Seleksi</h3>
                        <p>Anda telah menyelesaikan <b><?= $totalSelesai ?></b> dari <b>9</b> tahapan.</p>
                        <div class="status-tags">
                            <span class="tag <?= ($persen > 0) ? 'active' : '' ?>">
                                <?= ($persen == 100) ? 'Lengkap' : 'Berproses' ?>
                            </span>
                        </div>
                        <br>
                        <small class="text-muted" style="font-size: 10px;">
                          Update: <?= DashboardUserController::getLastActivityString() ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="card doc-card">
                <div class="card-header">
                    <h3>Tahapan Seleksi</h3>
                    <small>Real-time</small>
                </div>
                <div class="doc-list-scroll"> 
                    <?php foreach($tahapan as $t): ?>
                        <?php 
                            $nama = $t[0];
                            $status = $t[1];
                            $icon = $t[2];
                            
                            $badgeClass = ($status === true || $status == 1 || $status === 'diterima' || $status === 'Hadir') ? 'success' : 
                                          (($status === 'revisi') ? 'warning' : 
                                          (($status === '0' || $status === 0) ? 'danger' : 'secondary'));
                            $badgeText = ($badgeClass == 'success') ? 'Selesai' : 
                                         (($badgeClass == 'danger') ? 'Ditolak' : 'Belum');
                        ?>
                        <div class="doc-item">
                            <div class="doc-icon <?= ($badgeClass == 'success') ? 'primary' : 'info' ?>">
                                <span class="material-symbols-outlined"><?= $icon ?></span>
                            </div>
                            <div class="doc-info">
                                <span><?= $nama ?></span>
                            </div>
                            <span class="status-pill <?= $badgeClass ?>"><?= $badgeText ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div> 

        <div class="right-column">
            
            <div class="card calendar-card">
                
                <div class="calendar-header">
                    <h3><?= $namaBulan ?></h3>
                    <div class="calendar-nav">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                </div>

                <div class="calendar-grid-php">
                    <div class="day-name">Min</div>
                    <div class="day-name">Sen</div>
                    <div class="day-name">Sel</div>
                    <div class="day-name">Rab</div>
                    <div class="day-name">Kam</div>
                    <div class="day-name">Jum</div>
                    <div class="day-name">Sab</div>

                    <?php for($k=0; $k < $hariPertama; $k++): ?>
                        <div class="calendar-day empty"></div>
                    <?php endfor; ?>

                    <?php for($hari=1; $hari <= $jumlahHari; $hari++): ?>
                        <?php 
                            $isToday = ($hari == date('j') && $bulanSekarang == date('n') && $tahunSekarang == date('Y'));
                            $classToday = $isToday ? 'today' : '';
                        ?>
                        <div class="calendar-day <?= $classToday ?>"><?= $hari ?></div>
                    <?php endfor; ?>
                </div>

                <div class="upcoming-section">
                    <div class="section-title-row">
                        <h4>Jadwal Kegiatan</h4>
                        <a href="<?= APP_URL ?>/wawancara" class="view-all-link">Lihat Semua</a>
                    </div>
                    
                    <div class="upcoming-list scrollable-list">
                        <?php if (empty($wawancara)) : ?>
                            <div class="empty-state">
                                <span class="material-symbols-outlined">event_busy</span>
                                <p>Belum ada jadwal.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($wawancara as $value) : 
                                $date = date_create($value['tanggal'] ?? 'now');
                                $jam = substr($value['waktu'] ?? '00:00', 0, 5);
                            ?>
                            <div class="schedule-item">
                                <div class="date-box">
                                    <span class="day"><?= date_format($date, "d") ?></span>
                                    <span class="month"><?= date_format($date, "M") ?></span>
                                </div>
                                <div class="schedule-details">
                                    <span class="sched-title"><?= htmlspecialchars($value['jenis_wawancara'] ?? 'Kegiatan') ?></span>
                                    <div class="sched-meta">
                                        <span><i class='bx bx-time'></i> <?= $jam ?></span>
                                        <span><i class='bx bx-map'></i> <?= htmlspecialchars($value['ruangan'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>     

             <div class="card biodata-card">
                <div class="card-header">
                    <h3>Biodata Diri</h3>
                    <a href="<?= APP_URL ?>/biodata" class="btn-icon"><span class="material-symbols-outlined">edit</span></a>
                </div>
                <div class="card-body">
                    <div class="bio-row">
                        <span class="label">Nama</span>
                        <span class="value"><?= htmlspecialchars($bio['nama_lengkap'] ?? '-') ?></span>
                    </div>
                    <div class="bio-row">
                        <span class="label">NIM</span>
                        <span class="value"><?= htmlspecialchars($bio['stambuk'] ?? '-') ?></span>
                    </div>
                    <div class="bio-row">
                        <span class="label">Prodi</span>
                        <span class="value"><?= htmlspecialchars($jurusan ?? '-') ?></span>
                    </div>
                </div>
            </div> 
            
        </div> </div>
</main>

<div id="customMessageModal" class="custom-modal" style="display: none;">
  <div class="custom-modal-content">
    <div class="custom-modal-header" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
      <h5 style="margin: 0; font-size: 16px;">Notifikasi</h5>
      <button id="closeModalButton" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    <div class="custom-modal-body" style="max-height: 300px; overflow-y: auto; padding-top: 10px;">
      <?php if (empty($notifikasi)) { echo "<p class='text-center text-muted' style='font-size:12px;'>Tidak ada pesan baru.</p>"; } else { 
        foreach ($notifikasi as $notif) { ?>
          <div class="notif-item" style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px; border-left: 4px solid #007bff;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <b style="font-size: 13px;">Tim Iclabs</b>
                <small style="color: #999; font-size: 10px;"><?= $notif['created_at'] ?></small>
            </div>
            <p style="margin: 0; font-size: 12px; color: #555;"><?= htmlspecialchars($notif['pesan']) ?></p>
          </div>
      <?php } } ?>
    </div>
  </div>
</div>

<script src="<?= APP_URL ?>/Script/user/dashboardScript.js"></script>

<script>
  // Script Modal Simple
  const modal = document.getElementById("customMessageModal");
  const btnOpen = document.getElementById("viewMessageButton");
  const btnClose = document.getElementById("closeModalButton");

  if(btnOpen) btnOpen.addEventListener("click", () => modal.style.display = "flex");
  if(btnClose) btnClose.addEventListener("click", () => modal.style.display = "none");
  
  // Klik di luar modal untuk menutup
  window.addEventListener("click", (e) => { 
      if(e.target === modal) modal.style.display = "none"; 
  });
</script>