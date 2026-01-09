<?php
/**
 * Dashboard View
 * 
 * Data yang diterima dari controller:
 * @var array $notifikasi - Daftar notifikasi user
 * @var int $tahapanSelesai - Jumlah tahapan yang sudah selesai
 * @var int $percentage - Persentase progress
 * @var array $tahapan - Daftar tahapan pendaftaran
 */

// Gunakan data dari controller, jika tidak ada gunakan default
$notifikasi = $notifikasi ?? [];
$tahapanSelesai = $tahapanSelesai ?? 0;
$percentage = $percentage ?? 0;
$tahapan = $tahapan ?? [];
?>

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