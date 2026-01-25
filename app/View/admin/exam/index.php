<?php
/**
 * Tes Tulis Admin - Bank Soal Management
 * Modern Bootstrap 5 Design with Bank Soal System
 * 
 * MVC Pattern: This View only displays data. 
 * Business logic is handled by Model, data prepared by Controller.
 */
use App\Controllers\exam\ExamController;

// Get all data from Controller (proper MVC pattern)
$pageData = ExamController::getAdminExamPageData();
$bankSoalList = $pageData['bankSoalList'];
$allSoal = $pageData['allSoal'];
$stats = $pageData['stats'];

// Use statistics from Model (no counting logic in View)
$bankCount = $stats['bank_count'];
$totalSoal = $stats['total_soal'];
$pgCount = $stats['pg_count'];
$essayCount = $stats['essay_count'];
?>




    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <style>
        .editor-toolbar { border-color: #dee2e6; border-radius: 0.5rem 0.5rem 0 0; }
        .CodeMirror { border-color: #dee2e6; border-radius: 0 0 0.5rem 0.5rem; }
        .editor-statusbar { display: none; }
        
        /* Limit rendered markdown image size */
        .condition-render-markdown img {
            max-width: 100%;
            max-height: 400px; /* Reasonable limit */
            object-fit: contain;
            border-radius: 8px;
            margin-top: 10px;
            margin-bottom: 10px; /* Space between image and text */
            border: 1px solid #dee2e6;
            display: block; /* Force new line */
        }
    </style>
<main>
    <!-- Page Header -->
    <?php
        $title = 'Bank Soal Ujian';
        $subtitle = 'Kelola bank soal untuk tes tertulis calon asisten laboratorium';
        $icon = 'bx bx-library';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <!-- Stats Badges -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center mb-0 pb-2">
                    <div class="rounded-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: #2563eb;">
                        <i class='bx bx-folder text-white fs-3'></i>
                    </div>
                    <div>
                        <h3 class="fs-4 fw-bold mb-0 text-dark" id="stat-count-bank"><?= $bankCount ?></h3>
                        <div class="text-secondary small">Bank Soal</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center mb-0 pb-2">
                    <div class="rounded-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: #059669;">
                        <i class='bx bx-file text-white fs-3'></i>
                    </div>
                    <div>
                        <h3 class="fs-4 fw-bold mb-0 text-dark" id="stat-count-total"><?= $totalSoal ?></h3>
                        <div class="text-secondary small">Total Soal</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center mb-0 pb-2">
                    <div class="rounded-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: #448aff;">
                        <i class='bx bx-list-check text-white fs-3'></i>
                    </div>
                    <div>
                        <h3 class="fs-4 fw-bold mb-0 text-dark" id="stat-count-pg"><?= $pgCount ?></h3>
                        <div class="text-secondary small">Pilihan Ganda</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center mb-0 pb-2">
                    <div class="rounded-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: #ffd740;">
                        <i class='bx bx-edit text-dark fs-3'></i>
                    </div>
                    <div>
                        <h3 class="fs-4 fw-bold mb-0 text-dark" id="stat-count-essay"><?= $essayCount ?></h3>
                        <div class="text-secondary small">Essay</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-2">
            <div class="d-flex justify-content-between align-items-center">
                <ul class="nav nav-pills nav-fill gap-2" id="bankSoalTabs">
                <li class="nav-item">
                    <a class="nav-link active px-4 py-2" data-bs-toggle="pill" href="#tabBankSoal">
                        <i class='bx bx-folder me-2'></i> Bank Soal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-2" data-bs-toggle="pill" href="#tabImportExport">
                        <i class='bx bx-transfer me-2'></i> Import/Export
                    </a>
                </li>
            </ul>
            <button class="btn btn-primary" id="btnCreateBank" data-bs-toggle="modal" data-bs-target="#createBankModal" style="background-color: #2563eb; border-color: #2563eb;">
                <i class='bx bx-plus me-1'></i> Bank Soal Baru
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab: Bank Soal -->
        <div class="tab-pane fade show active" id="tabBankSoal">
            <!-- Bank Soal List View -->
            <div class="bank-list-view" id="bankListView">
                <?php if (empty($bankSoalList)): ?>
                <div class="text-center py-5">
                    <i class='bx bx-folder-open text-muted' style="font-size: 5rem;"></i>
                    <h4 class="mt-3 text-muted">Belum Ada Bank Soal</h4>
                    <p class="text-muted">Klik tombol "Buat Bank Soal Baru" untuk membuat bank soal pertama</p>
                </div>
                <?php endif; ?>
                <div class="row g-4" id="bankGrid">
                    <!-- Existing Banks -->
                    <?php foreach ($bankSoalList as $bank): ?>
                    <!-- Bank Card Item -->
                    <div class="col-md-6 col-lg-4 col-xl-3" id="bank-card-<?= $bank['id'] ?>">
                        <div class="card h-100 border-0 rounded-4 hover-card overflow-hidden">
                            <!-- Card Cover Image -->
                            <div class="card-cover position-relative" style="height: 120px; background-color: #2563eb; background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.1) 0px, rgba(255,255,255,0.1) 2px, transparent 2px, transparent 10px);">
                                <div class="position-absolute top-0 end-0 p-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-white p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bx-dots-horizontal-rounded fs-4'></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:void(0)" onclick="window.editBankModal(<?= $bank['id'] ?>)">
                                                    <i class='bx bx-edit text-primary'></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="javascript:void(0)" onclick="deleteBank(<?= $bank['id'] ?>)">
                                                    <i class='bx bx-trash'></i> Hapus
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body pt-0 px-4 pb-4 position-relative">
                                
                                <div class="mt-4 pt-2 cursor-pointer" onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama'] ?? '') ?>')">
                                    <h5 class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($bank['nama'] ?? '') ?>">
                                        <?= htmlspecialchars($bank['nama'] ?? '') ?>
                                    </h5>
                                    <p class="text-secondary small mb-3 text-truncate-2" style="min-height: 40px;">
                                        <?= htmlspecialchars($bank['deskripsi'] ?? 'Tidak ada deskripsi') ?>
                                    </p>
                                    
                                    <div class="d-flex gap-2 flex-wrap mb-2">
                                        <span class="badge rounded-pill text-white px-3 py-2 border-0" style="background-color: #ff5252;">
                                            <i class='bx bx-file me-1'></i> <?= $bank['jumlah_soal'] ?> Soal
                                        </span>
                                        <span class="badge rounded-pill text-white px-3 py-2 border-0" style="background-color: #448aff;" title="Pilihan Ganda">
                                            PG: <?= $bank['pg_count'] ?? 0 ?>
                                        </span>
                                        <span class="badge rounded-pill text-dark px-3 py-2 border-0" style="background-color: #ffd740;" title="Essay">
                                            Essay: <?= $bank['essay_count'] ?? 0 ?>
                                        </span>
                                    </div>
                                    <div class="mb-0">
                                        <span class="badge rounded-pill text-dark px-3 py-2 border-0" style="background-color: #69f0ae;">
                                            <i class='bx bx-key me-1'></i> <?= htmlspecialchars($bank['token'] ?? '') ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Active Switch -->
                                    <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center" onclick="event.stopPropagation()">
                                        <span class="small fw-bold text-secondary">Status: 
                                            <span id="statusText_<?= $bank['id'] ?>" class="<?= ($bank['is_active'] ?? 0) == 1 ? 'text-success' : 'text-danger' ?>">
                                                <?= ($bank['is_active'] ?? 0) == 1 ? 'Aktif' : 'Tidak Aktif' ?>
                                            </span>
                                        </span>
                                        <div class="form-check form-switch cursor-pointer">
                                            <input class="form-check-input bank-active-switch cursor-pointer" type="checkbox" id="activeSwitch_<?= $bank['id'] ?>" 
                                            <?= ($bank['is_active'] ?? 0) == 1 ? 'checked' : '' ?>
                                            onchange="window.activateBank(<?= $bank['id'] ?>)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Create New Bank Card -->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 border-0 rounded-4 cursor-pointer hover-card bg-white" 
                             data-bs-toggle="modal" data-bs-target="#createBankModal">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                <div class="rounded-3 mb-3 transition-transform hover-scale d-flex justify-content-center align-items-center" style="width: 80px; height: 80px; background-color: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                    <i class='bx bx-plus fs-1'></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Buat Bank Baru</h6>
                                <p class="text-secondary small mb-0">Klik untuk menambahkan bank soal baru</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Detail View -->
            <div class="bank-detail-view d-none" id="bankDetailView">
                <div class="d-flex align-items-center mb-4 gap-3">
                    <button class="btn btn-light rounded-circle p-2" onclick="closeBankDetail()">
                        <i class='bx bx-arrow-back fs-4'></i>
                    </button>
                    <div>
                        <h4 class="fw-bold mb-0" id="detailBankTitle">Nama Bank Soal</h4>
                        <div class="text-secondary small">Kelola daftar soal dalam bank ini</div>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSoalModal" style="background-color: #2563eb; border-color: #2563eb;">
                            <i class='bx bx-plus me-1'></i> Tambah Soal
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class='bx bx-search'></i></span>
                                    <input type="text" class="form-control border-0 bg-light" id="searchSoal" placeholder="Cari soal...">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">Semua</button>
                                <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="pilihan_ganda">Pilihan Ganda</button>
                                <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="essay">Essay</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-light bg-opacity-50" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                        <div id="soalList">
                            <!-- Soal items rendered by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'import_export.php'; ?>
    </div>
</main>

<!-- Create Bank Modal -->
<div class="modal fade" id="createBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-0 rounded-top-4 p-4" style="background-color: #2563eb;">
                <h5 class="modal-title fw-bold"><i class='bx bx-folder-plus me-2'></i>Buat Bank Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBankForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Nama Bank Soal</label>
                        <input type="text" class="form-control form-control-lg" name="nama_bank" placeholder="Contoh: Ujian Masuk 2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi_bank" rows="3" placeholder="Deskripsi singkat bank soal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Token Ujian</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-key'></i></span>
                            <input type="text" class="form-control" name="token_bank" placeholder="Kode Token" required>
                        </div>
                        <div class="form-text">Kode unik untuk peserta mengakses ujian ini.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill" style="background-color: #2563eb; border-color: #2563eb;">Buat Bank Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade" id="editBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-0 rounded-top-4 p-4" style="background-color: #2563eb;">
                <h5 class="modal-title fw-bold"><i class='bx bx-edit me-2'></i>Edit Bank Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBankForm">
                <input type="hidden" name="id" id="editBankId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Nama Bank Soal</label>
                        <input type="text" class="form-control form-control-lg" name="nama" id="editBankName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="editBankDesc" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Token Ujian</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-key'></i></span>
                            <input type="text" class="form-control" name="token" id="editBankToken" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill" style="background-color: #2563eb; border-color: #2563eb;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Soal Modal -->
<div class="modal fade" id="addSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-0 rounded-top-4 p-4" style="background-color: #2563eb;">
                <h5 class="modal-title fw-bold"><i class='bx bx-plus-circle me-2'></i>Tambah Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSoalForm">
                <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto;">
                    <!-- Tipe Soal Selection -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-dark d-block mb-3">Pilih Tipe Soal</label>
                        <div class="d-flex justify-content-center gap-3">
                            <div class="type-option selected p-3 rounded-4 border border-2 cursor-pointer position-relative" data-type="pilihan_ganda" style="width: 160px;">
                                <div class="check-icon position-absolute top-0 end-0 mt-2 me-2 text-primary">
                                    <i class='bx bxs-check-circle fs-4'></i>
                                </div>
                                <i class='bx bx-list-ul fs-1 text-primary mb-2'></i>
                                <div class="fw-bold">Pilihan Ganda</div>
                            </div>
                            <div class="type-option p-3 rounded-4 border border-2 cursor-pointer position-relative" data-type="essay" style="width: 160px;">
                                <div class="check-icon position-absolute top-0 end-0 mt-2 me-2 text-primary d-none">
                                    <i class='bx bxs-check-circle fs-4'></i>
                                </div>
                                <i class='bx bx-align-left fs-1 text-warning mb-2'></i>
                                <div class="fw-bold">Essay</div>
                            </div>
                        </div>
                        <input type="hidden" name="status_soal" id="soalType" value="pilihan_ganda">
                    </div>

                    <!-- Pertanyaan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Pertanyaan</label>
                        <textarea class="form-control p-3 mb-3" name="deskripsi" rows="4" placeholder="Tulis pertanyaan disini..." style="resize: vertical;"></textarea>
                        
                        <label class="form-label fw-bold text-dark small">Gambar Soal (Opsional)</label>
                        <input type="file" class="form-control" name="soal_image" accept="image/*">
                        <div class="form-text small">Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                    </div>

                    <!-- Pilihan Ganda Container -->
                    <div id="pilihanContainer" class="mb-4">
                        <label class="form-label fw-bold text-dark mb-3">Pilihan Jawaban</label>
                        <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold text-primary"><?= $opt ?></span>
                            <input type="text" class="form-control" name="pilihan_<?= strtolower($opt) ?>" 
                                   placeholder="Pilihan <?= $opt ?><?= $opt == 'E' ? ' (Opsional)' : '' ?>" 
                                   <?= $opt != 'E' ? 'required' : '' ?>>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Jawaban Benar Container -->
                    <div id="jawabanPGContainer" class="mb-3">
                        <label class="form-label fw-bold text-dark">Kunci Jawaban</label>
                        <div class="d-flex gap-3">
                            <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jawaban" id="jawab<?= $opt ?>" value="<?= $opt ?>" required>
                                <label class="form-check-label fw-bold" for="jawab<?= $opt ?>"><?= $opt ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div id="jawabanEssayContainer" class="mb-3" style="display: none;">
                        <label class="form-label fw-bold text-dark">Kunci Jawaban (Essay)</label>
                        <textarea class="form-control" name="jawaban_essay" rows="3" placeholder="Jawaban referensi untuk essay..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill" style="background-color: #2563eb; border-color: #2563eb;">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Soal Modal -->
<div class="modal fade" id="editSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-0 rounded-top-4 p-4" style="background-color: #2563eb;">
                <h5 class="modal-title fw-bold"><i class='bx bx-edit me-2'></i>Edit Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSoalForm">
                <input type="hidden" name="id" id="editSoalId">
                <div class="modal-body p-4">
                    <!-- Tipe Soal Selection -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-dark d-block mb-3">Tipe Soal</label>
                        <div class="d-flex justify-content-center gap-3">
                            <div class="type-option p-3 rounded-4 border border-2 cursor-pointer position-relative" data-type="pilihan_ganda" id="editTypePG" style="width: 160px;">
                                <div class="check-icon position-absolute top-0 end-0 mt-2 me-2 text-primary">
                                    <i class='bx bxs-check-circle fs-4'></i>
                                </div>
                                <i class='bx bx-list-ul fs-1 text-primary mb-2'></i>
                                <div class="fw-bold">Pilihan Ganda</div>
                            </div>
                            <div class="type-option p-3 rounded-4 border border-2 cursor-pointer position-relative" data-type="essay" id="editTypeEssay" style="width: 160px;">
                                <div class="check-icon position-absolute top-0 end-0 mt-2 me-2 text-primary d-none">
                                    <i class='bx bxs-check-circle fs-4'></i>
                                </div>
                                <i class='bx bx-align-left fs-1 text-warning mb-2'></i>
                                <div class="fw-bold">Essay</div>
                            </div>
                        </div>
                        <input type="hidden" name="status_soal" id="editSoalType">
                    </div>

                    <!-- Pertanyaan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Pertanyaan</label>
                        <textarea class="form-control p-3" name="deskripsi" id="editDeskripsi" rows="4"></textarea>
                    </div>

                    <!-- Pilihan Ganda -->
                    <div id="editPilihanContainer" class="mb-4">
                        <label class="form-label fw-bold text-dark mb-3">Pilihan Jawaban</label>
                        <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold text-primary"><?= $opt ?></span>
                            <input type="text" class="form-control" id="editPilihan<?= $opt ?>" name="pilihan_<?= strtolower($opt) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Jawaban -->
                    <div id="editJawabanPGContainer" class="mb-3">
                        <label class="form-label fw-bold text-dark">Kunci Jawaban</label>
                        <div class="d-flex gap-3">
                            <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jawaban" id="editJawab<?= $opt ?>" value="<?= $opt ?>">
                                <label class="form-check-label fw-bold" for="editJawab<?= $opt ?>"><?= $opt ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div id="editJawabanEssayContainer" class="mb-3" style="display: none;">
                        <label class="form-label fw-bold text-dark">Kunci Jawaban (Essay)</label>
                        <textarea class="form-control" name="jawaban_essay" id="editJawabanEssay" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill" style="background-color: #2563eb; border-color: #2563eb;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Utilities specifically for this page */
.hover-card { transition: transform 0.2s, box-shadow 0.2s; }
.hover-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.cursor-pointer { cursor: pointer; }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

/* Type Option Selection */
.type-option.selected { background-color: rgba(13, 110, 253, 0.05); border-color: #0d6efd !important; }
.type-option.selected .check-icon { display: block !important; }

/* Modal Customization (Bootstrap overrides) */
/* Modal Customization (Bootstrap overrides) */
.modal-backdrop.show { opacity: 0.5; }

/* Allow scrolling in modals with EasyMDE */
.modal-dialog-scrollable .modal-body {
    overflow-y: auto !important;
    max-height: 70vh !important; /* Force height limit to trigger scroll */
    scrollbar-width: thin; /* Firefox */
}
/* Webkit scrollbar styling */
.modal-dialog-scrollable .modal-body::-webkit-scrollbar {
    width: 6px; /* Slightly thinner */
}
.modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Fix EasyMDE z-index issues in modal */
.EasyMDEContainer {
    z-index: 1055; 
}
.CodeMirror {
    min-height: 200px;
    max-height: 400px;
}
</style>

<!-- Pass PHP Data to JavaScript -->
<script>
    window.serverData = {
        allSoal: <?= json_encode(array_map(function($soal) {
            return [
                'id' => $soal['id'] ?? 0,
                'bank_soal_id' => $soal['bank_soal_id'] ?? null,
                'deskripsi' => $soal['deskripsi'] ?? '',
                'status_soal' => $soal['status_soal'] ?? 'essay',
                'pilihan' => $soal['pilihan'] ?? '',
                'jawaban' => $soal['jawaban'] ?? ''
            ];
        }, $allSoal)) ?>,
        bankSoalList: <?= json_encode($bankSoalList) ?>
    };
    
    // Global Base URL
    var baseUrl = '<?= APP_URL ?>';
    
    // Legacy support for inline scripts that might expect these globals immediately
    window.allSoal = window.serverData.allSoal;
    window.bankSoalList = window.serverData.bankSoalList;
</script>

<!-- Load External JavaScript -->
<script src="<?= APP_URL ?>/assets/Script/admin/exam_import_export.js"></script>
<script src="<?= APP_URL ?>/assets/Script/admin/exam_import_export.js"></script>
<script src="<?= APP_URL ?>/assets/Script/admin/exam.js"></script>
<script>
    // Initialize EasyMDE
    document.addEventListener('DOMContentLoaded', function() {
        // Options matching user screenshot
        const mdeOptions = {
             element: null, // to be set
             autoDownloadFontAwesome: false,
             spellChecker: false,
             status: false,
             uploadImage: true,
             imageUploadEndpoint: baseUrl + '/uploadImage',
             imagePathAbsolute: true,
             imageAccept: "image/png, image/jpeg, image/gif, image/webp",
             imageTexts: {
                 sbInit: 'Drag & drop image here',
                 sbOnDragEnter: 'Drop image to upload',
                 sbOnDrop: 'Uploading...',
                 sbProgress: 'Uploading... (#progress#)',
                 sbOnUploaded: 'Uploaded',
                 sizeUnits: 'b,kb,mb'
             },
             errorMessages: {
                 noFileGiven: 'Please select a file.',
                 typeNotAllowed: 'This file type is not allowed.',
                 fileTooLarge: 'Image is too big detected.',
                 importError: 'Something went wrong during image upload.'
             },
             toolbar: [
                 "bold", "italic", "heading", "|", 
                 "quote", "unordered-list", "ordered-list", "|",
                 "link", "image", "upload-image", "|",
                 "preview", "side-by-side", "fullscreen", "|",
                 "guide"
             ]
        };

        // Create Editor for Add Question
        window.easyMDE_add = new EasyMDE({
            ...mdeOptions,
            element: document.querySelector('#addSoalForm textarea[name="deskripsi"]')
        });

        // Create Editor for Edit Question
        window.easyMDE_edit = new EasyMDE({
            ...mdeOptions,
            element: document.querySelector('#editSoalForm textarea[name="deskripsi"]') 
        });

        // Refresh on Modal Open to fix rendering issues
        const addModal = document.getElementById('addSoalModal');
        addModal.addEventListener('shown.bs.modal', function () {
            window.easyMDE_add.codemirror.refresh();
        });

        const editModal = document.getElementById('editSoalModal');
        editModal.addEventListener('shown.bs.modal', function () {
            window.easyMDE_edit.codemirror.refresh();
        });

        // Sync before submit
        document.getElementById('addSoalForm').addEventListener('submit', function(e) {
             const val = window.easyMDE_add.value();
             if (!val.trim()) {
                 e.preventDefault();
                 alert('Pertanyaan tidak boleh kosong');
                 return;
             }
        });

        document.getElementById('editSoalForm').addEventListener('submit', function(e) {
             const val = window.easyMDE_edit.value();
             if (!val.trim()) {
                 e.preventDefault();
                 alert('Pertanyaan tidak boleh kosong');
                 return;
             }
        });
    });
</script>

<!-- Error Handling & Suppression Scripts -->
<script>
    // 1. Suppress external extension errors (Visual cleanup for console)
    const originalConsoleError = console.error;
    console.error = function(...args) {
        if (args[0] && typeof args[0] === 'string' && 
           (args[0].includes('chrome-extension://') || args[0].includes('quillbot'))) {
            return; // Suppress extension noise
        }
        originalConsoleError.apply(console, args);
    };

    // 2. Global Image Error Handler (Handle 404s gracefully in UI)
    document.addEventListener('error', function(e) {
        if (e.target && e.target.tagName === 'IMG') {
            // Stop if specific suppression class is present
            if (e.target.classList.contains('suppress-error')) return;

            // Check if src is current page (often happens with src="" or src="#")
            if (e.target.src === window.location.href || e.target.getAttribute('src') === '') {
                e.target.style.display = 'none'; // Just hide empty images
                return;
            }

            // Check if it's already a placeholder to prevent loops
            if (!e.target.src.includes('placehold.co')) {
                console.warn('Image failed to load, swapping with placeholder:', e.target.src);
                e.target.src = 'https://placehold.co/600x400?text=Image+Not+Found';
                e.target.alt = 'Broken Image';
                e.target.style.border = '1px dashed #ff0000';
            }
        }
    }, true); // Capture phase to catch load errors
</script>
