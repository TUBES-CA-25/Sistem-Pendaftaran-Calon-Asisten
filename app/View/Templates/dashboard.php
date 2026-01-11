<?php
/**
 * Dashboard View
 *
 * Data yang diterima dari controller:
 * @var array $notifikasi - Daftar notifikasi user
 * @var int $tahapanSelesai - Jumlah tahapan yang sudah selesai
 * @var int $percentage - Persentase progress
 * @var array $tahapan - Daftar tahapan pendaftaran
 * @var array $jadwalPresentasiUser - Jadwal presentasi user
 */

// Gunakan data dari controller, jika tidak ada gunakan default
$notifikasi = $notifikasi ?? [];
$tahapanSelesai = $tahapanSelesai ?? 0;
$percentage = $percentage ?? 0;
$tahapan = $tahapan ?? [];
$jadwalPresentasiUser = $jadwalPresentasiUser ?? null;
?>

<style>
/* Jadwal Presentasi Card for User Dashboard */
.my-schedule-card {
    background: linear-gradient(135deg, #3dc2ec 0%, #2563eb 100%);
    border-radius: 16px;
    padding: 1.5rem;
    margin-top: 1rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.my-schedule-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.my-schedule-card .card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
}

.my-schedule-card .card-header i {
    font-size: 1.5rem;
}

.my-schedule-card .card-header h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.my-schedule-card .schedule-content {
    position: relative;
    z-index: 1;
}

.my-schedule-card .schedule-info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.my-schedule-card .schedule-info-row:last-child {
    border-bottom: none;
}

.my-schedule-card .schedule-info-row i {
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
}

.my-schedule-card .schedule-info-row .label {
    font-size: 0.8rem;
    opacity: 0.9;
    min-width: 70px;
}

.my-schedule-card .schedule-info-row .value {
    font-weight: 600;
    font-size: 0.9rem;
}

.my-schedule-card .no-schedule {
    text-align: center;
    padding: 1rem 0;
}

.my-schedule-card .no-schedule i {
    font-size: 2.5rem;
    opacity: 0.7;
    display: block;
    margin-bottom: 0.5rem;
}

.my-schedule-card .no-schedule p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
    color: white;
}
</style>

<main>
  <h1 class="dashboard">Dashboard</h1>
  <div class="insights">
    <div class="tahap">
      <span class="material-symbols-outlined">browse_activity</span>
      <div class="middle">
        <div class="left">
          <h3>Tahap yang telah diselesaikan</h3>
          <h1><?= $tahapanSelesai ?></h1>
        </div>
        <div class="progress" data-percentage="<?= $percentage ?>">

          <div class="number">
            <?= $percentage ?>%
          </div>
        </div>

      </div>
      <small class="text-muted">Last 24 Hours</small>
    </div>

    <!-- Jadwal Presentasi User Card -->
    <div class="my-schedule-card">
      <div class="card-header">
        <i class='bx bx-calendar-event'></i>
        <h3>Jadwal Presentasi Anda</h3>
      </div>
      <div class="schedule-content">
        <?php if ($jadwalPresentasiUser): ?>
          <?php
            $tanggal = new DateTime($jadwalPresentasiUser['tanggal']);
            $formattedDate = $tanggal->format('d F Y');
            $waktu = date('H:i', strtotime($jadwalPresentasiUser['waktu']));
          ?>
          <div class="schedule-info-row">
            <i class='bx bx-calendar'></i>
            <span class="label">Tanggal</span>
            <span class="value"><?= $formattedDate ?></span>
          </div>
          <div class="schedule-info-row">
            <i class='bx bx-time'></i>
            <span class="label">Waktu</span>
            <span class="value"><?= $waktu ?> WIB</span>
          </div>
          <div class="schedule-info-row">
            <i class='bx bx-building'></i>
            <span class="label">Ruangan</span>
            <span class="value"><?= htmlspecialchars($jadwalPresentasiUser['ruangan']) ?></span>
          </div>
          <div class="schedule-info-row">
            <i class='bx bx-book'></i>
            <span class="label">Judul</span>
            <span class="value"><?= htmlspecialchars(mb_strimwidth($jadwalPresentasiUser['judul'], 0, 40, '...')) ?></span>
          </div>
        <?php else: ?>
          <div class="no-schedule">
            <i class='bx bx-calendar-x'></i>
            <p>Jadwal presentasi belum ditentukan</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="recent-tahapan">
    <h2 class="tahapan-pendaftaran">
      Tahapan-Tahapan Pendaftaran Calon Asisten ICLABS 2024
    </h2>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Tahapan</th>
              <th>Status</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($tahapan as $tahap) {
              $status = $tahap[2];
              ?>
              <tr>
                <td><?= $tahap[0] ?></td>
                <td><?= $tahap[1] ?></td>
                <td class="<?= $status ? 'status-acc' : 'status' ?>">
                  <?= $status ? 'Selesai' : 'Belum' ?>
                </td>
                <td>Anda <?= $status ? "telah menyelesaikan" : "belum menyelesaikan" ?>   <?= $tahap[3] ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
  </div>
</main>
<div class="right">
  <div class="top">
    <div class="notification">
      <h2>Notification</h2>
      <span class="material-symbols-outlined">inbox</span>
      <div class="updates">
        <div class="update">
          <div class="message">
            <p><b>Tim Iclabs</b> selamat kamu telah berhasil mendaftar di web IC-ASSIST</p>
          </div>
        </div>
        <div class="dashboard" id="dashboard">
          <button type="button" class="btn btn-primary" id="viewMessageButton">
            Lihat Pesan
          </button>
        </div>
      </div>
      <div id="content" style="margin-top: 20px;"></div>
    </div>
  </div>
</div>

<div id="customMessageModal" class="custom-modal"
  style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
  <div class="custom-modal-content"
    style="background: white; padding: 20px; border-radius: 8px; width: 600px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
    <div class="custom-modal-header"
      style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
      <h5 class="custom-modal-title">Pesan</h5>
      <button id="closeModalButton"
        style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    <div class="custom-modal-body" style="margin-top: 10px; display: flex; flex-direction: column; gap: 10px;">
      <?php
      if ($notifikasi == null) {
        echo "<p>Tidak ada pesan</p>";
      } else {

        foreach ($notifikasi as $notif) { ?>
          <div
            style="background: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 5px; text-transform: uppercase;">
            <b>Tim Iclabs</b>
            <p><?= $notif['pesan'] ?></p>
            <p><?= $notif['created_at'] ?></p>
          </div>
          <?php
        }
      }
      ?>
    </div>
    <div class="custom-modal-footer" style="margin-top: 20px; text-align: right;">
      <button id="closeModalFooterButton" class="btn btn-secondary">Tutup</button>
    </div>
  </div>
</div>

<script>
 


  //  let customMessageModal = document.getElementById("customMessageModal");
  //  let closeModalButton = document.getElementById("closeModalButton");
  //  let closeModalFooterButton = document.getElementById("closeModalFooterButton");

  document.getElementById("viewMessageButton").addEventListener("click", function () {
    customMessageModal.style.display = "flex";
  });

  document.getElementById("closeModalButton").addEventListener("click", function () {
    customMessageModal.style.display = "none";
  });

  document.getElementById("closeModalFooterButton").addEventListener("click", function () {
    customMessageModal.style.display = "none";
  });

  document.getElementById("customMessageModal").addEventListener("click", function (event) {
    if (event.target === customMessageModal) {
      customMessageModal.style.display = "none";
    }
  });
</script>