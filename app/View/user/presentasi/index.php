<?php
/**
 * Presentasi View
 *
 * Data yang diterima dari controller:
 * @var array $results - Data presentasi user
 * @var bool $biodataStatus - Status biodata
 * @var bool $berkasStatus - Status berkas
 * @var bool $absensiTesTertulis - Status absensi tes tertulis
 * @var bool $pptStatus - Status PPT
 */
$results = $results ?? [];
$biodataStatus = $biodataStatus ?? false;
$berkasStatus = $berkasStatus ?? false;
$absensiTesTertulis = $absensiTesTertulis ?? false;
$pptStatus = $pptStatus ?? false;
$canSubmitJudul = $biodataStatus && $absensiTesTertulis;
$canSubmitPpt = $biodataStatus && $absensiTesTertulis && $pptStatus;
?>

<!-- Page Header -->
<?php
    $title = 'Presentasi';
    $subtitle = 'Submit judul dan file presentasi';
    $icon = 'bx bx-chalkboard';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">

    <div class="row g-4">
        <!-- Form Card -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-file-earmark-slides me-2 text-primary"></i>Submit Presentasi
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <?php if (!$biodataStatus): ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>Lengkapi biodata terlebih dahulu!</div>
                        </div>
                    <?php elseif (!$berkasStatus): ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>Lengkapi berkas terlebih dahulu!</div>
                        </div>
                    <?php elseif (!$absensiTesTertulis): ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>Anda belum mengikuti tes tertulis!</div>
                        </div>
                    <?php elseif (empty($results) || (isset($results['is_accepted']) && $results['is_accepted'] == 0) || (isset($results['is_revisi']) && $results['is_revisi'] == 1)): ?>
                        <!-- Form Submit Judul -->
                        <form id="berkasPresentasiForm">
                            <div class="mb-4">
                                <label for="judul" class="form-label fw-semibold">
                                    <i class="bi bi-pencil-square me-1"></i>Judul Presentasi
                                </label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="judul" name="judul" placeholder="Masukkan judul presentasi Anda" required <?php if (!$canSubmitJudul) echo 'disabled'; ?>>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3" <?php if (!$canSubmitJudul) echo 'disabled'; ?>>
                                <i class="bi bi-send me-2"></i>Submit Judul
                            </button>
                        </form>
                    <?php elseif (isset($results['is_accepted']) && $results['is_accepted'] == 1): ?>
                        <!-- Form Submit PPT & Makalah -->
                        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>Judul Anda telah disetujui! Silahkan upload file.</div>
                        </div>

                        <form id="presentasiFormAccepted" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="ppt" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-ppt me-1"></i>File PPT
                                </label>
                                <input class="form-control form-control-lg rounded-3" type="file" id="ppt" name="ppt" accept=".ppt,.pptx" required <?php if (!$canSubmitPpt) echo 'disabled'; ?>>
                                <small class="text-muted">Format: PPT, PPTX (Max 10MB)</small>
                            </div>

                            <div class="mb-4">
                                <label for="makalah" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Makalah
                                </label>
                                <input class="form-control form-control-lg rounded-3" type="file" id="makalah" name="makalah" accept="application/pdf" required <?php if (!$canSubmitPpt) echo 'disabled'; ?>>
                                <small class="text-muted">Format: PDF (Max 2MB)</small>
                            </div>

                            <!-- Download Template -->
                            <div class="p-3 rounded-3 mb-4" style="background: #f0f9ff;">
                                <a id="downloadFile1" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/Template-Laporan-Makalah.docx" download class="d-flex align-items-center gap-3 text-decoration-none">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: var(--gradient-primary);">
                                        <i class="bx bx-file text-white fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-primary d-block">Download Template Makalah</span>
                                        <small class="text-muted">Gunakan template yang disediakan</small>
                                    </div>
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3" <?php if (!$canSubmitPpt) echo 'disabled'; ?>>
                                <i class="bi bi-upload me-2"></i>Submit File
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- History Table Card -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Hasil Submit Judul Presentasi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($results)): ?>
                                    <?php
                                    $i = 1;
                                    if (isset($results['judul'])) {
                                        $results = [$results];
                                    }
                                    foreach ($results as $row):
                                        $revisi = !$row['is_accepted']
                                            ? (!empty($row['revisi']) ? 'Revisi: ' . $row['revisi'] : 'Ditolak')
                                            : 'Diterima';
                                        $keterangan = !$row['is_accepted']
                                            ? (!empty($row['is_revisi'] && (!empty($row['keterangan']) || !$row['keterangan'])) ? $row['keterangan'] : 'Belum Diterima')
                                            : 'Silahkan submit PPT dan makalah!';
                                        $isAccepted = $row['is_accepted'];
                                    ?>
                                        <tr>
                                            <td class="ps-4"><?= $i ?></td>
                                            <td>
                                                <span class="fw-medium"><?= htmlspecialchars($row['judul']) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($isAccepted): ?>
                                                    <span class="badge badge-success-subtle rounded-pill px-3 py-2">
                                                        <i class="bi bi-check-circle-fill me-1"></i><?= htmlspecialchars($revisi) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning-subtle rounded-pill px-3 py-2">
                                                        <i class="bi bi-clock-fill me-1"></i><?= htmlspecialchars($revisi) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-muted"><?= htmlspecialchars($row['created_at']) ?></td>
                                            <td class="small"><?= htmlspecialchars($keterangan) ?></td>
                                        </tr>
                                    <?php $i++;
                                    endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted">Tidak ada data untuk ditampilkan</span>
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

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/presentasi.js"></script>
