<?php
/**
 * Presentasi User View
 * 
 * Data yang diterima dari Controller:
 * @var array $results - Data presentasi user
 * @var bool $biodataStatus - Status biodata
 * @var bool $berkasStatus - Status berkas
 * @var bool $tesTertulisStatus - Status tes tertulis
 * @var bool $pptStatus - Status PPT
 */
$results = $results ?? [];
$biodataStatus = $biodataStatus ?? false;
$berkasStatus = $berkasStatus ?? false;
$tesTertulisStatus = $tesTertulisStatus ?? false;
$pptStatus = $pptStatus ?? false;
?>
<main>
  <div>
    <h1 class="dashboard">Presentasi</h1>
    <div class="form-container">
    <?php
    if(!$biodataStatus) {
      echo '<div class="alert alert-warning" role="alert">
        Lengkapi biodata terlebih dahulu!
      </div>';
      return;
    }
    if(!$berkasStatus) {
      echo '<div class="alert alert-warning" role="alert">
        Lengkapi berkas terlebih dahulu!
      </div>';
      return;
    }
    if(!$tesTertulisStatus) {
      echo '<div class="alert alert-warning" role="alert">
        Anda belum mengikuti tes tertulis!
      </div>';
      return;
    }
    ?>
      <?php if (empty($results) || (isset($results['is_accepted']) && $results['is_accepted'] == 0) || (isset($results['is_revisi']) && $results['is_revisi'] == 1)) : ?>
        <form id="berkasPresentasiForm">
          <div class="mb-3">
            <label for="judul" class="form-label">Judul</label>
            <input 
              type="text" 
              class="form-control" 
              id="judul" 
              name="judul" 
              placeholder="Masukkan Judul" 
              required 
              <?php if (!($biodataStatus && $tesTertulisStatus)): ?>
                disabled
              <?php endif; ?>
            >
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-submit">Submit</button>
          </div>
        </form>
      <?php elseif (isset($results['is_accepted']) && $results['is_accepted'] == 1) : ?>
        <form id="presentasiFormAccepted" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="ppt" class="form-label">Masukkan PPT</label>
            <input 
              class="form-control" 
              type="file" 
              id="ppt" 
              name="ppt" 
              accept=".ppt,.pptx" 
              required
              <?php if ((!$biodataStatus || !$tesTertulisStatus || !$pptStatus)) echo 'disabled' ?>
                
            >
          </div>
          <div class="mb-3">
            <label for="makalah" class="form-label">Masukkan makalah Anda</label>
            <input 
              class="form-control" 
              type="file" 
              id="makalah" 
              name="makalah" 
              accept="application/pdf" 
              required
              <?php if ((!$biodataStatus || !$tesTertulisStatus || !$pptStatus)) echo 'disabled' ?>
            >
          </div>
          <a 
            id="downloadFile1" 
            style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #007bff; font-size: 16px;" 
            href="#" 
            download
          >
            <div style="display: flex; width: 40px; height: 40px; background-color: #e6f0ff; border-radius: 8px;">
              <i class="bx bx-file" style="font-size: 40px;"></i>
            </div>
            <span>Download Template Makalah</span>
          </a>
          <div class="d-grid">
            <button type="submit" class="btn btn-submit">Submit</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="recent-judul">
    <main>
      <h1 class="dashboard">Hasil Submit Judul Presentasi</h1>
    </main>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Judul</th>
          <th>Status</th>
          <th>Waktu Pengiriman Judul</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($results)) : ?>
          <?php
          $i = 1;
          if (isset($results['judul'])) {
            $results = [$results];
          }
          foreach ($results as $row) :
            $revisi = !$row['is_accepted']
              ? (!empty($row['revisi']) ? 'Revisi: ' . $row['revisi'] : 'Ditolak')
              : 'Diterima';
            $keterangan = !$row['is_accepted']
              ? (!empty($row['is_revisi'] && (!empty($row['keterangan']) || !$row['keterangan'])) ? $row['keterangan'] : 'Belum Diterima')
              : 'Silahkan submit PPT dan makalah Anda!';
            $color = !$row['is_accepted'] ? 'status' : 'status-acc';
            $judul = $row['judul'];
            $created = $row['created_at'];
          ?>
            <tr>
              <td><?= $i ?></td>
              <td><?= htmlspecialchars($judul) ?></td>
              <td class="<?= $color ?>"><?= htmlspecialchars($revisi) ?></td>
              <td><?= htmlspecialchars($created) ?></td>
              <td><?= htmlspecialchars($keterangan) ?></td>
            </tr>
            <?php $i++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="5">Tidak ada data untuk ditampilkan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/presentasi.js"></script>
