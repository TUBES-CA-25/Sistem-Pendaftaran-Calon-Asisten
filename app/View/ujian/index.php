<?php
/**
 * Exam View
 * 
 * Data yang diterima dari controller:
 * @var string $stambuk - Stambuk mahasiswa
 * @var array $profile - Data profile
 * @var string $nama - Nama mahasiswa
 * @var string $photo - Path foto
 * @var array $results - Soal-soal ujian
 */
$stambuk = $stambuk ?? '';
$profile = $profile ?? [];
$nama = $nama ?? 'Nama Lengkap';
$photo = $photo ?? '/Sistem-Pendaftaran-Calon-Asisten/res/profile/default.png';
$results = $results ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICLabs - Tes Tertulis</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?=APP_URL?>/Assets/css/exam.css" />
    <link rel="icon" href="<?=APP_URL?>/Assets/Img/iclabs.png">
    <script>
        const APP_URL = <?= json_encode(APP_URL) ?>;
        // Helper untuk akses storage yang aman
        window.storage = {
            get: function(key) {
                try { return localStorage.getItem(key); } catch(e) { return null; }
            },
            set: function(key, val) {
                try { localStorage.setItem(key, val); } catch(e) {}
            },
            remove: function(key) {
                try { localStorage.removeItem(key); } catch(e) {}
            }
        };
    </script>
</head>

<body class="bg-light">
    <!-- Navbar Header -->
    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container-fluid px-3">
            <span class="navbar-brand mb-0 h5">
                <img src="<?=APP_URL?>/Assets/Img/iclabs.png" alt="Logo" height="30" class="d-inline-block align-text-top me-2">
                ICLabs - Tes Tertulis
            </span>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid p-0" style="height: calc(100vh - 56px);">
        <div class="row g-0 h-100">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 bg-white border-end shadow-sm overflow-auto">
                <div class="p-3">
                    <!-- Profile Section -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <img src="<?= htmlspecialchars($photo) ?>"
                             alt="User Photo"
                             class="rounded-circle border border-3 border-primary mb-3 profile-photo"
                             width="80" height="80">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($nama) ?></h6>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($stambuk) ?></p>
                    </div>

                    <!-- Question Navigation -->
                    <h6 class="fw-bold mb-3 small text-muted">NAVIGASI SOAL</h6>
                    <div class="d-grid gap-2" style="grid-template-columns: repeat(5, 1fr);">
                        <?php for ($i = 1; $i <= count($results); $i++): ?>
                            <button class="btn btn-outline-secondary btn-sm nav-btn-soal"
                                    data-index="<?= $i - 1 ?>">
                                <?= $i ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8 overflow-auto">
                <div class="p-4">
                    <!-- Timer Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <h5 class="mb-0 fw-bold">Soal <span id="current-question-number">1</span></h5>
                        <div class="badge bg-info text-dark fs-6 py-2 px-3">
                            <i class="bi bi-clock-fill me-2"></i>
                            <span id="timer">30:00</span>
                        </div>
                    </div>

                    <!-- Questions Container -->
                    <div class="questions-container">
                        <?php foreach ($results as $index => $result): ?>
                            <div class="question card border-0 shadow-sm"
                                 data-id-soal="<?= htmlspecialchars($result['id']) ?>"
                                 style="display: none;">
                                <div class="card-body p-4">
                                    <!-- Question Text -->
                                    <div class="mb-4">
                                        <?php if (!empty($result['img_soal'])): ?>
                                            <img src="<?= htmlspecialchars($result['img_soal']) ?>"
                                                 class="img-fluid rounded mb-3 question-image"
                                                 alt="Question Image">
                                        <?php endif; ?>
                                        <p class="lead"><?= nl2br(htmlspecialchars($result['deskripsi'])) ?></p>
                                    </div>

                                    <!-- Answer Section -->
                                    <?php if ($result['status_soal'] === 'pilihan_ganda'): ?>
                                        <!-- Multiple Choice Options -->
                                        <div class="list-group mb-4">
                                            <?php
                                            // Try JSON decode first (new format)
                                            $options = json_decode($result['pilihan'], true);

                                            // If JSON decode fails, try CSV format (old format): "A. option1, B. option2, C. option3"
                                            if (!is_array($options) || empty($options)) {
                                                $pilihanString = trim($result['pilihan']);
                                                if (!empty($pilihanString) && $pilihanString !== 'Bukan soal pilihan ganda') {
                                                    // Split by comma to get individual options
                                                    $optionsArray = explode(',', $pilihanString);
                                                    $options = [];

                                                    foreach ($optionsArray as $opt) {
                                                        $opt = trim($opt);
                                                        // Remove "A. ", "B. ", etc. prefix if exists
                                                        $cleanOption = preg_replace('/^[A-Z]\.\s*/', '', $opt);
                                                        if (!empty($cleanOption)) {
                                                            $options[] = $cleanOption;
                                                        }
                                                    }
                                                }
                                            }

                                            $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];

                                            if (is_array($options) && !empty($options)):
                                                foreach ($options as $optionIndex => $option):
                                                    $label = $optionLabels[$optionIndex] ?? ($optionIndex + 1);
                                            ?>
                                                    <label class="list-group-item list-group-item-action d-flex align-items-center py-3 cursor-pointer">
                                                        <input class="form-check-input me-3 flex-shrink-0"
                                                               type="radio"
                                                               name="answer[<?= htmlspecialchars($result['id']) ?>]"
                                                               value="<?= htmlspecialchars($optionIndex) ?>">
                                                        <span class="fw-semibold text-primary me-2"><?= $label ?>.</span>
                                                        <span><?= htmlspecialchars($option) ?></span>
                                                    </label>
                                            <?php
                                                endforeach;
                                            else:
                                            ?>
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    Pilihan jawaban tidak tersedia untuk soal ini.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <!-- Essay Answer -->
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Jawaban Anda:</label>
                                            <textarea class="form-control"
                                                      name="answer[<?= htmlspecialchars($result['id']) ?>]"
                                                      rows="8"
                                                      placeholder="Tulis jawaban Anda di sini..."
                                                      style="resize: vertical;"></textarea>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Navigation Buttons -->
                                    <div class="d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                                        <button class="btn btn-outline-primary btn-lg back-btn">
                                            <i class="bi bi-arrow-left me-2"></i>
                                            Sebelumnya
                                        </button>
                                        <button class="btn btn-primary btn-lg next-btn">
                                            Selanjutnya
                                            <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                        <button class="btn btn-success btn-lg finish-btn" style="display: none;">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Selesai
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div class="modal fade" id="customModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <img id="modalGif" src="" alt="Animation" class="mb-3" style="width: 100px; display: none;">
                    <p id="modalMessage" class="mb-0">Pesan akan ditampilkan di sini.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-primary" id="closeModal" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-question-circle text-warning fs-1 mb-3 d-block"></i>
                    <p id="confirmModalMessage" class="mb-0">Apakah Anda yakin?</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary" id="confirmModalCancel" data-bs-dismiss="modal">
                        Tidak
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmModalConfirm">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Bootstrap Modal Instances
        let customModalInstance = null;
        let confirmModalInstance = null;

        // Initialize modals when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const customModalEl = document.getElementById('customModal');
            const confirmModalEl = document.getElementById('confirmModal');

            if (customModalEl) {
                customModalInstance = new bootstrap.Modal(customModalEl);
            }
            if (confirmModalEl) {
                confirmModalInstance = new bootstrap.Modal(confirmModalEl);
            }
        });

        // Shim for showActionConfirmation using Bootstrap Modal
        window.showActionConfirmation = function(options) {
            const msg = document.getElementById('confirmModalMessage');
            const btnConfirm = document.getElementById('confirmModalConfirm');
            const btnCancel = document.getElementById('confirmModalCancel');

            if (msg && btnConfirm && btnCancel && confirmModalInstance) {
                msg.innerHTML = options.message || 'Apakah Anda yakin?';

                // Remove old listeners
                const newBtnConfirm = btnConfirm.cloneNode(true);
                btnConfirm.parentNode.replaceChild(newBtnConfirm, btnConfirm);

                const newBtnCancel = btnCancel.cloneNode(true);
                btnCancel.parentNode.replaceChild(newBtnCancel, btnCancel);

                newBtnConfirm.addEventListener('click', function() {
                    confirmModalInstance.hide();
                    if (options.onConfirm) options.onConfirm();
                });

                newBtnCancel.addEventListener('click', function() {
                    confirmModalInstance.hide();
                });

                confirmModalInstance.show();
            } else {
                if (confirm(options.message || 'Apakah Anda yakin?')) {
                    if (options.onConfirm) options.onConfirm();
                }
            }
        };

        // Shim for showModal using Bootstrap Modal
        window.showModal = function(message, gifUrl) {
            const msgEl = document.getElementById('modalMessage');
            const gifEl = document.getElementById('modalGif');
            const closeBtn = document.getElementById('closeModal');

            if (msgEl && customModalInstance) {
                msgEl.innerText = message;

                if (gifEl) {
                    if (gifUrl) {
                        gifEl.src = gifUrl;
                        gifEl.style.display = 'block';
                    } else {
                        gifEl.style.display = 'none';
                    }
                }

                if (closeBtn) {
                    closeBtn.onclick = function() {
                        customModalInstance.hide();
                    };
                }

                customModalInstance.show();
            } else {
                alert(message);
            }
        };
    </script>
    <script src="<?=APP_URL?>/Assets/js/examScript.js"></script>
</body>

</html>