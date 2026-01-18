<?php
use app\Controllers\notifications\NotificationControllers;
use app\Controllers\user\DashboardUserController;
use App\Controllers\Profile\ProfileController;
$role = ProfileController::viewUser()["role"];


// percobaan 
 use app\Controllers\user\WawancaraController;
  $wawancara = WawancaraController::getAllById() ;




// 1. FIX ERROR COUNT(): Pastikan data selalu array, meskipun return dari controller false/null
$rawNotif = NotificationControllers::getMessageById();
$notifikasi = (is_array($rawNotif)) ? $rawNotif : [];

// 2. AMBIL DATA REAL
$bio = DashboardUserController::getBiodataDetail();
$persen = DashboardUserController::getPercentage();
$totalSelesai = DashboardUserController::getNumberTahapanSelesai();
$jurusan = ProfileController::viewBiodata() == null ? "Jurusan" : ProfileController::viewBiodata()["jurusan"];
$noHp = ProfileController::viewBiodata() == null ? "No Telephone" : ProfileController::viewBiodata()["noHp"];


// 3. Jadwal Mendatang (Real dari Database via Model)
$jadwal = DashboardUserController::getUpcomingSchedule();


// 3. DEFINISI 9 TAHAPAN (LOGIKA LAMA DIKEMBALIKAN)
// Format: [Nama Tahap, Status Boolean/Value, Icon Material]
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
?>

<link rel="stylesheet" href="<?= APP_URL ?>/Style/dashboardStyle.css">

<main class="dashboard-container">
    
    <div class="welcome-banner">
        <div class="banner-text">
            <h1>Hello, <?= htmlspecialchars($bio['nama_lengkap'] ?? $_SESSION['user']['username']) ?> 👋</h1>
            <p>Pantau progres Kamu agar tidak ketinggal Info terbaru.</p>
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
        
        <!-- Kolom kiri -->
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
                        <p>Anda telah menyelesaikan <b><?= $totalSelesai ?></b> dari <b>9</b> tahapan wajib.</p>
                        <div class="status-tags">
                            <span class="tag <?= ($persen > 0) ? 'active' : '' ?>">
                                <?= ($persen == 100) ? 'Lengkap' : 'Berproses' ?>
                            </span>
                        </div>
                        <br> <br>
                        <small class="text-muted">
                          Terakhir diupdate: <?= DashboardUserController::getLastActivityString() ?>
                        </small>
                    </div>
                </div>
            </div>


            
            <!-- Tahapan Seleksi -->
            <div class="card doc-card">
                <div class="card-header">
                    <h3>Tahapan Seleksi</h3>
                    <small>Real-time Update</small>
                </div>
                
                <div class="doc-list-scroll"> <?php foreach($tahapan as $t): ?>
                        <?php 
                            $nama = $t[0];
                            $status = $t[1]; // Bisa boolean true/false, bisa string 'revisi', bisa angka 1/0
                            $icon = $t[2];
                            
                            // Logika Penentuan Badge Warna & Text
                            $badgeClass = 'secondary';
                            $badgeText = 'Belum';

                            if ($status === true || $status == 1 || $status === 'diterima' || $status === 'Hadir') {
                                $badgeClass = 'success';
                                $badgeText = 'Selesai';
                            } elseif ($status === 'revisi') {
                                $badgeClass = 'warning';
                                $badgeText = 'Revisi';
                            } elseif ($status === '0' || $status === 0) { // String '0' kadang return dari DB
                                $badgeClass = 'danger';
                                $badgeText = 'Ditolak';
                            } else {
                                $badgeClass = 'secondary'; // Default Belum
                            }
                        ?>

                        <div class="doc-item">
                            <div class="doc-icon <?= ($badgeClass == 'success') ? 'primary' : 'info' ?>">
                                <span class="material-symbols-outlined"><?= $icon ?></span>
                            </div>
                            
                            <div class="doc-info">
                                <span><?= $nama ?></span>
                                <small><?= ($badgeClass == 'success') ? 'Telah diselesaikan' : 'Menunggu penyelesaian' ?></small>
                            </div>

                            <span class="status-pill <?= $badgeClass ?>">
                                <?= $badgeText ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div> <!-- Tutup Tahapan Seleksi -->



        </div> <!-- Tutup Kolom Kiri -->

         
        <!-- Kolom kanan -->
        <div class="right-column">
                  <!-- kalender Jadwal -->
             <div class="card calendar-card">
                <div class="calendar-header">
                    <h3 id="currentMonth">Tanggal saat ini</h3>
                    <div class="calendar-nav">
                        <span id="prevMonth" class="material-symbols-outlined">chevron_left</span>
                        <span id="nextMonth" class="material-symbols-outlined">chevron_right</span>
                    </div>
                </div>

                <div id="calendarBody" class="calendar-grid"></div>

                <div class="upcoming-section">
                    <h4>Jadwal saat ini</h4>
                    <div class="upcoming-list">
                      <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Kegiatan</th>
                                <th>Lokasi</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>

                        <tbody>
                          <?php if (empty($wawancara)) : ?>
                            <tr>
                                <td colspan="5">Belum ada Jadwal</td>
                            </tr>                            
                              <?php endif; $i = 0;?>
                              <?php foreach ($wawancara as $value) : $i++?>
                              <tr>
                                  <td><?= $i?></td>
                                  <td><?= $value['jenis_wawancara'] ?? "" ?></td>
                                  <td><?= $value['ruangan'] ?? "" ?></td>
                                  <td><?= $value['tanggal']?? "" ?></td>
                                  <td><?= $value['waktu'] ?? "" ?></td>
                              </tr>
                                              
                          <?php endforeach; ?>
                        </tbody>

                      </table>


                    </div>
                </div>
            </div>     


             <!-- Biodata -->
            <div class="card biodata-card">
                <div class="card-header">
                    <h3>Biodata Diri</h3>
                    <a href="<?= APP_URL ?>/biodata" class="btn-icon"><span class="material-symbols-outlined">edit</span></a>
                </div>
                <div class="card-body">
                    <div class="bio-row">
                        <span class="label">Nama :</span>
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
                    <div class="bio-row">
                        <span class="label">No. HP</span>
                        <span class="value"><?= htmlspecialchars($noHp ?? '-') ?></span>
                    </div>
                </div>
            </div> <!-- Tutup Biodata -->
            
            

        </div> <!-- Tutup Kolom Kanan -->

    </div>
    
</main>

<div id="customMessageModal" class="custom-modal" style="display: none;">
  <div class="custom-modal-content">
    <div class="custom-modal-header">
      <h5>Pesan Masuk</h5>
      <button id="closeModalButton">&times;</button>
    </div>
    <div class="custom-modal-body">
      <?php if (empty($notifikasi)) { echo "<p class='text-center text-muted'>Tidak ada pesan baru.</p>"; } else { 
        foreach ($notifikasi as $notif) { ?>
          <div class="notif-item">
            <b>Tim Iclabs</b>
            <p><?= htmlspecialchars($notif['pesan']) ?></p>
            <small><?= $notif['created_at'] ?></small>
          </div>
      <?php } } ?>
    </div>
  </div>
</div>

<script src="<?= APP_URL ?>/Script/user/dashboardScript.js"></script>

<script>
  // Menggunakan Optional Chaining (?.) untuk mencegah error jika elemen tidak ditemukan
  const modal = document.getElementById("customMessageModal");
  const btnOpen = document.getElementById("viewMessageButton");
  const btnClose = document.getElementById("closeModalButton");

  if(btnOpen) btnOpen.addEventListener("click", () => modal.style.display = "flex");
  if(btnClose) btnClose.addEventListener("click", () => modal.style.display = "none");
  
  window.addEventListener("click", (e) => { 
      if(e.target === modal) modal.style.display = "none"; 
  });
</script>