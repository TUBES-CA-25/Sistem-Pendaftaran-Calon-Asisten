<?php
use App\Controllers\User\PresentasiUserController;
use App\Controllers\User\DashboardUserController;

$results = PresentasiUserController::viewAll();

// Cek Prasyarat
$biodataDone = DashboardUserController::getBiodataStatus();
$berkasDone = DashboardUserController::getBerkasStatus();
$tesDone = DashboardUserController::getAbsensiTesTertulis();
$pptDone = DashboardUserController::getPptStatus();

$allPrerequisitesMet = $biodataDone && $berkasDone && $tesDone;
?>

<link rel="stylesheet" href="<?= APP_URL ?>/Style/presentasi.css">

<main class="presentasi-container">
    
    <div class="page-header">
        <h1>Presentasi & Makalah</h1>
        <p>Ajukan judul presentasi dan upload materi Anda di sini.</p>
    </div>

    <div class="presentasi-grid">
        
        <div class="left-section">
            
            <?php if(!$biodataDone): ?>
                <div class="alert-warning"><i class='bx bx-error'></i> Harap lengkapi <b>Biodata</b> terlebih dahulu.</div>
            <?php elseif(!$berkasDone): ?>
                <div class="alert-warning"><i class='bx bx-error'></i> Harap lengkapi <b>Upload Berkas</b> terlebih dahulu.</div>
            <?php elseif(!$tesDone): ?>
                <div class="alert-warning"><i class='bx bx-error'></i> Anda belum mengikuti <b>Tes Tertulis</b>.</div>
            <?php endif; ?>

            <?php if (empty($results) || (isset($results['is_accepted']) && $results['is_accepted'] == 0) || (isset($results['is_revisi']) && $results['is_revisi'] == 1)) : ?>
                
                <div class="action-card">
                    <div class="card-title">
                        <span>Ajukan Judul Presentasi</span>
                        <i class='bx bx-edit-alt'></i>
                    </div>
                    <form id="berkasPresentasiForm">
                        <div class="form-group">
                            <label for="judul" class="form-label">Judul Materi</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="judul" 
                                name="judul" 
                                placeholder="Masukkan judul materi yang akan dibawakan..." 
                                required 
                                <?= (!$allPrerequisitesMet) ? 'disabled' : '' ?>
                            >
                        </div>
                        <button type="submit" class="btn-submit" <?= (!$allPrerequisitesMet) ? 'disabled' : '' ?>>
                            Kirim Pengajuan Judul
                        </button>
                    </form>
                </div>

            <?php elseif (isset($results['is_accepted']) && $results['is_accepted'] == 1) : ?>
                
                <div class="action-card">
                    <div class="card-title">
                        <span>Upload Materi Presentasi</span>
                        <i class='bx bx-upload'></i>
                    </div>
                    
                    <form id="presentasiFormAccepted" enctype="multipart/form-data">
                        
                        <a id="downloadFile1" href="#" download class="download-link">
                            <i class='bx bxs-file-pdf' style="font-size: 20px;"></i>
                            <span>Download Template Makalah Resmi</span>
                        </a>

                        <div class="form-group">
                            <label class="form-label">File Presentasi (PPT/PPTX)</label>
                            <div class="upload-box">
                                <input class="form-control" type="file" id="ppt" name="ppt" accept=".ppt,.pptx" required 
                                <?= (!$pptDone && !$allPrerequisitesMet) ? 'disabled' : '' ?>>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">File Makalah (PDF)</label>
                            <div class="upload-box">
                                <input class="form-control" type="file" id="makalah" name="makalah" accept="application/pdf" required
                                <?= (!$pptDone && !$allPrerequisitesMet) ? 'disabled' : '' ?>>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            Simpan & Upload Berkas
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="action-card" style="text-align: center; padding: 40px;">
                    <i class='bx bx-time-five' style="font-size: 48px; color: #FFC107;"></i>
                    <h3 style="margin: 10px 0;">Menunggu Verifikasi</h3>
                    <p style="color: #777; font-size: 14px;">Judul Anda sedang ditinjau oleh Asisten. Silakan cek berkala.</p>
                </div>
            <?php endif; ?>

        </div>

        <div class="right-section">
            <div class="status-card">
                <div class="card-title">Riwayat Pengajuan</div>
                
                <?php if (!empty($results)) : ?>
                    <?php
                        // Normalisasi array agar bisa di-loop (jika cuma 1 data)
                        $history = isset($results['judul']) ? [$results] : $results;
                        
                        foreach ($history as $row) :
                            $isAcc = $row['is_accepted'] == 1;
                            $isRej = $row['is_accepted'] == 0 && empty($row['revisi']); // Asumsi logika tolak
                            $isRev = !empty($row['revisi']) && $row['is_accepted'] == 0;
                            
                            $statusClass = 'status-wait';
                            $badgeClass = 'bg-wait';
                            $statusText = 'Menunggu';

                            if ($isAcc) {
                                $statusClass = 'status-acc'; $badgeClass = 'bg-acc'; $statusText = 'Diterima';
                            } elseif ($isRev) {
                                $statusClass = 'status-wait'; $badgeClass = 'bg-wait'; $statusText = 'Revisi';
                            } elseif ($row['is_accepted'] == 0) { // Ditolak
                                $statusClass = 'status-rej'; $badgeClass = 'bg-rej'; $statusText = 'Ditolak';
                            }

                            // Keterangan
                            $keterangan = $isAcc ? "Silahkan lanjut upload PPT & Makalah." : 
                                         ($isRev ? "Catatan: " . $row['revisi'] : 
                                         ($row['keterangan'] ?? "Menunggu review asisten."));
                    ?>
                        <div class="history-item <?= $statusClass ?>">
                            <div class="h-header">
                                <span class="status-badge <?= $badgeClass ?>"><?= $statusText ?></span>
                                <span class="h-date"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                            </div>
                            <div class="h-title"><?= htmlspecialchars($row['judul']) ?></div>
                            <div class="h-footer">
                                <i class='bx bx-info-circle'></i> <?= htmlspecialchars($keterangan) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                
                <?php else : ?>
                    <div style="text-align: center; color: #999; padding: 20px;">
                        <i class='bx bx-folder-open' style="font-size: 32px;"></i>
                        <p style="font-size: 12px;">Belum ada pengajuan judul.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</main>

<script src="/tubes_web/public/Assets/Script/user/presentasi.js"></script>