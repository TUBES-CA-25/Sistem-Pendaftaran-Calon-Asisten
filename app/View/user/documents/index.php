<?php
/**
 * Upload Berkas View
 *
 * Data yang diterima dari controller:
 * @var array $res - Data berkas user
 * @var string $nama - Nama lengkap
 * @var bool $biodataStatus - Status biodata sudah lengkap atau belum
 * @var bool $isBerkasEmpty - Status berkas kosong atau tidak
 */
$res = $res ?? [];
$nama = $nama ?? 'Nama Lengkap';
$biodataStatus = $biodataStatus ?? false;
$isBerkasEmpty = $isBerkasEmpty ?? true;
?>

<!-- Page Header -->
<?php
    $title = 'Upload Berkas';
    $subtitle = 'Upload dokumen pendaftaran Anda';
    $icon = 'bx bx-file';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="pb-4" style="margin-top: -30px; position: relative; z-index: 10;">

    <div class="row g-4">
        <!-- Upload Form Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-cloud-upload me-2 text-primary"></i>Upload Dokumen
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <?php if (!$biodataStatus): ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>Lengkapi biodata terlebih dahulu</div>
                        </div>
                    <?php else: ?>
                        <form id="berkasForm" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="foto" class="form-label fw-semibold">
                                    <i class="bi bi-image me-1"></i>Foto 3x4
                                </label>
                                <input class="form-control form-control-lg rounded-3" type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg" required>
                                <small class="text-muted">Format: PNG, JPG, JPEG</small>
                            </div>

                            <div class="mb-4">
                                <label for="cv" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-text me-1"></i>CV
                                </label>
                                <input class="form-control form-control-lg rounded-3" type="file" id="cv" name="cv" accept="application/pdf" required>
                                <small class="text-muted">Format: PDF</small>
                            </div>

                            <div class="mb-4">
                                <label for="transkrip" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-bar-graph me-1"></i>Transkrip Nilai
                                </label>
                                <input class="form-control form-control-lg rounded-3" type="file" id="transkrip" name="transkrip" accept="application/pdf" required>
                                <small class="text-muted">Format: PDF</small>
                            </div>

                            <div class="mb-4">
                                <label for="suratpernyataan" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-check me-1"></i>Surat Pernyataan
                                </label>
                                <input class="form-control form-control-lg rounded-3" type="file" id="suratpernyataan" name="suratpernyataan" accept="application/pdf" required>
                                <small class="text-muted">Format: PDF</small>
                            </div>

                            <!-- Download Template -->
                            <div class="p-3 rounded-3 mb-4" style="background: #f0f9ff;">
                                <a id="downloadFile1" href="#" download class="d-flex align-items-center gap-3 text-decoration-none">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: var(--gradient-primary);">
                                        <i class="bx bx-file text-white fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-primary d-block">Download Template CV</span>
                                        <small class="text-muted">Gunakan template yang disediakan</small>
                                    </div>
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                                <i class="bi bi-upload me-2"></i>Submit Berkas
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- History Table Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Submit Berkas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$isBerkasEmpty && !empty($res)): ?>
                                    <?php foreach ($res as $result): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-light text-dark">CCA00<?= $result['id_mahasiswa'] ?></span>
                                            </td>
                                            <td class="small text-muted"><?= $result['created_at'] ?></td>
                                            <td><?= htmlspecialchars($nama) ?></td>
                                            <td>
                                                <?php if ($result['accepted'] == 1): ?>
                                                    <span class="badge badge-success-subtle rounded-pill px-3 py-2">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Terverifikasi
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning-subtle rounded-pill px-3 py-2">
                                                        <i class="bi bi-clock-fill me-1"></i>Menunggu
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted">Belum ada data berkas</span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/berkas.js"></script>
