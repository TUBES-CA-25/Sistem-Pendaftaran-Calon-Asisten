<?php
/**
 * Tes Tulis View
 *
 * Data yang diterima dari controller:
 * @var bool $absensiTesTertulis - Status sudah absen tes tertulis
 * @var bool $berkasStatus - Status berkas sudah lengkap
 * @var bool $biodataStatus - Status biodata sudah lengkap
 */
$absensiTesTertulis = $absensiTesTertulis ?? false;
$berkasStatus = $berkasStatus ?? false;
$biodataStatus = $biodataStatus ?? false;
$isDisabled = !$berkasStatus || !$biodataStatus || $absensiTesTertulis;
?>

<!-- Page Header -->
<?php
    $title = 'Tes Tertulis';
    $subtitle = 'Ujian pilihan ganda online';
    $icon = 'bx bx-task';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">

    <!-- Exam Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <?php if (!$canAccess): ?>
                <?php if ($accessReason === 'completed'): ?>
                    <!-- Already Completed -->
                    <div class="text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px; background: #dcfce7;">
                            <i class="bi bi-check-lg text-success fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Anda sudah mengikuti tes tertulis</h4>
                        <p class="text-muted">Anda tidak bisa mengikuti tes tertulis lebih dari sekali.</p>
                        <p class="text-muted">Terima kasih.</p>
                    </div>
                <?php else: ?>
                    <!-- Access denied alert -->
                    <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div><?= $accessMessage ?></div>
                    </div>
                <?php endif; ?>
            <?php elseif (!isset($activeBank) || !$activeBank): ?>
                <!-- No Active Exam -->
                <div class="text-center py-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px; background: #e0f2fe;">
                        <i class="bx bx-info-circle text-primary fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Belum Ada Ujian Aktif</h4>
                    <p class="text-muted mb-0">Mohon tunggu informasi dari pengawas ujian.</p>
                </div>
            <?php else: ?>
                <!-- Exam Info -->
                <div class="text-center mb-4 pb-4 border-bottom">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3">Ujian Aktif</span>
                    <h4 class="fw-bold text-primary mb-2"><?= htmlspecialchars($activeBank['nama']) ?></h4>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($activeBank['deskripsi'] ?? '') ?></p>
                </div>

                <h5 class="fw-bold mb-3">
                    <i class="bi bi-journal-text me-2 text-primary"></i>Test Exam
                </h5>
                <p class="text-muted">Pada tahap kali ini kalian akan melaksanakan ujian pilihan ganda.</p>

                <div class="card bg-light border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-shield-check me-2 text-primary"></i>Tata Tertib Sebelum Ujian
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <span class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 24px; height: 24px; background: var(--gradient-primary);">
                                    <span class="text-white small">1</span>
                                </span>
                                <span>Dilarang menghadap kiri kanan. Fokus di komputer Anda.</span>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <span class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 24px; height: 24px; background: var(--gradient-primary);">
                                    <span class="text-white small">2</span>
                                </span>
                                <span>Bila membutuhkan sesuatu, angkat tangan dan panggil asisten.</span>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <span class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 24px; height: 24px; background: var(--gradient-primary);">
                                    <span class="text-white small">3</span>
                                </span>
                                <span>Kerjakan dengan jujur.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-info d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                    <i class="bi bi-clock"></i>
                    <div>Durasi ujian: <strong>80 Menit</strong>. Baca doa terlebih dahulu sebelum memulai.</div>
                </div>

                <div class="mb-4">
                    <label for="nomorMeja" class="form-label fw-semibold">
                        <i class="bi bi-geo-alt me-1"></i>Nomor Meja
                    </label>
                    <input type="text" id="nomorMeja" class="form-control form-control-lg rounded-3" placeholder="Masukkan nomor meja Anda" required <?php if ($isDisabled) echo 'disabled'; ?>>
                    <div id="errorMessage" class="text-danger small mt-2" style="display: none;">Silahkan masukkan nomor meja.</div>
                </div>

                <button id="startTestButton" class="btn btn-primary btn-lg w-100 rounded-3" <?php if ($isDisabled) echo 'disabled'; ?>>
                    <i class="bi bi-play-circle me-2"></i>Mulai Ujian
                </button>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Bootstrap Token Modal -->
<div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Verifikasi Token Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: #e0f2fe;">
                        <i class="bx bx-lock-alt text-primary fs-1"></i>
                    </div>
                    <p class="text-muted mb-0">Silahkan masukkan token ujian yang diberikan oleh pengawas.</p>
                </div>
                <div class="mb-4">
                    <input type="text" id="inputToken" class="form-control form-control-lg text-center fw-bold rounded-3" placeholder="TOKEN UJIAN" style="letter-spacing: 2px; text-transform: uppercase;">
                    <div id="tokenError" class="text-danger small mt-2 text-center" style="display: none;">Token yang Anda masukkan salah!</div>
                </div>
                <button type="button" class="btn btn-primary btn-lg w-100 rounded-3" id="btnSubmitToken">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Ujian
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    const APP_URL = '<?= APP_URL ?>';

    $('#startTestButton').on('click', function () {
        const nomorMejaInput = $('#nomorMeja').val().trim();

        if (!nomorMejaInput || isNaN(nomorMejaInput) || parseInt(nomorMejaInput) <= 0) {
            $('#errorMessage').text('Nomor meja tidak valid!').show();
            return;
        }

        $('#errorMessage').hide();
        new bootstrap.Modal(document.getElementById('tokenModal')).show();
    });

    $('#btnSubmitToken').on('click', function() {
        const token = $('#inputToken').val().trim();
        if (!token) {
            $('#tokenError').text('Masukkan token!').show();
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...');

        $.ajax({
            url: APP_URL + '/exam/verifyToken',
            method: 'POST',
            data: { token: token },
            success: function(res) {
                if (res.status === 'success') {
                    const nomorMeja = $('#nomorMeja').val().trim();
                    window.location.href = `${APP_URL}/soal?nomorMeja=${encodeURIComponent(nomorMeja)}`;
                } else {
                    $('#tokenError').text(res.message || 'Token salah!').show();
                    btn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-2"></i>Masuk Ujian');
                }
            },
            error: function() {
                $('#tokenError').text('Terjadi kesalahan server').show();
                btn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-2"></i>Masuk Ujian');
            }
        });
    });

    $('#inputToken').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btnSubmitToken').click();
        }
    });
});
</script>
