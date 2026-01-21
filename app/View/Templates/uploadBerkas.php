<?php
use App\Controllers\User\BerkasUserController;
use App\Controllers\user\DashboardUserController;
use App\Controllers\Profile\ProfileController;

$res = BerkasUserController::viewBerkas() ?? [];
$isBiodataComplete = DashboardUserController::getBiodataStatus();
?>

<link rel="stylesheet" href="<?= APP_URL ?>/Style/uploadberkas.css">

<main class="upload-page-container">
    
    <div class="page-header">
        <h1>Upload Berkas</h1>
        <p>Lengkapi dokumen persyaratan seleksi Anda di bawah ini.</p>
    </div>

    <div class="upload-grid">
        
        <div class="upload-section">
            <div class="card-header">
                <h3>Form Kelengkapan</h3>
                <?php if(!$isBiodataComplete): ?>
                    <span class="badge-warning">Lengkapi Biodata Terlebih Dahulu!</span>
                <?php endif; ?>
            </div>

            <form id="berkasForm" enctype="multipart/form-data">
                
                <div class="file-input-card">
                    <div class="icon-wrapper photo">
                        <span class="material-symbols-outlined">image</span>
                    </div>
                    <div class="input-details">
                        <label for="foto">Pas Foto 3x4 <span class="required">*</span></label>
                        <input type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg" 
                               class="custom-file-input"
                               <?= (!$isBiodataComplete) ? 'disabled' : '' ?> required>
                        <small>Format: JPG/PNG. Maks 2MB.</small>
                    </div>
                </div>

                <div class="file-input-card">
                    <div class="icon-wrapper pdf">
                        <span class="material-symbols-outlined">description</span>
                    </div>
                    <div class="input-details">
                        <label for="cv">Curriculum Vitae (CV) <span class="required">*</span></label>
                        <input type="file" id="cv" name="cv" accept="application/pdf" 
                               class="custom-file-input"
                               <?= (!$isBiodataComplete) ? 'disabled' : '' ?> required>
                        <small>Format: PDF. Maks 2MB.</small>
                    </div>
                </div>

                <div class="file-input-card">
                    <div class="icon-wrapper pdf">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <div class="input-details">
                        <label for="transkrip">Transkrip Nilai <span class="required">*</span></label>
                        <input type="file" id="transkrip" name="transkrip" accept="application/pdf" 
                               class="custom-file-input"
                               <?= (!$isBiodataComplete) ? 'disabled' : '' ?> required>
                        <small>Format: PDF. Maks 2MB.</small>
                    </div>
                </div>

                <div class="file-input-card">
                    <div class="icon-wrapper pdf">
                        <span class="material-symbols-outlined">assignment_turned_in</span>
                    </div>
                    <div class="input-details">
                        <label for="suratpernyataan">Surat Pernyataan <span class="required">*</span></label>
                        <input type="file" id="suratpernyataan" name="suratpernyataan" accept="application/pdf" 
                               class="custom-file-input"
                               <?= (!$isBiodataComplete) ? 'disabled' : '' ?> required>
                        <small>Format: PDF. Maks 2MB.</small>
                    </div>
                </div>

                <a id="downloadFile1" href="#" class="template-download-link">
                    <i class='bx bxs-file-pdf'></i>
                    <span>Download Template CV Resmi</span>
                </a>

                <div class="form-actions">
                    <button type="submit" class="btn-submit" <?= (!$isBiodataComplete) ? 'disabled' : '' ?>>
                        <i class='bx bx-upload'></i> Upload Semua Berkas
                    </button>
                </div>
            </form>
        </div>

        <div class="history-section">
            <div class="card-header">
                <h3>Riwayat Upload</h3>
            </div>
            
            <div class="history-list">
                <?php if (BerkasUserController::isEmptyBerkas()): ?>
                    <div class="empty-state">
                        <span class="material-symbols-outlined">folder_off</span>
                        <p>Belum ada berkas diunggah.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($res as $result): ?>
                        <div class="history-card">
                            <div class="history-icon">
                                <span class="material-symbols-outlined">history</span>
                            </div>
                            <div class="history-info">
                                <span class="hist-date"><?= date('d M Y, H:i', strtotime($result['created_at'])) ?></span>
                                <span class="hist-label">Submit Berkas</span>
                                
                                <?php if ($result['accepted'] == 1): ?>
                                    <span class="status-badge success">Terverifikasi</span>
                                <?php else: ?>
                                    <span class="status-badge warning">Menunggu Verifikasi</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<script src="/tubes_web/public/Assets/Script/user/berkas.js"></script>