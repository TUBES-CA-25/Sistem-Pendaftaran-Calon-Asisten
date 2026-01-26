<?php
/**
 * Import/Export Soal Page
 * @var array $data
 */
$bankSoalList = $data['bankSoalList'] ?? [];
?>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Import & Export Soal';
        $subtitle = 'Kelola pemindahan data soal via Excel/CSV';
        $icon = 'bx bx-transfer';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="container-fluid px-4 py-3">
        <!-- View: Import/Export -->
        <div id="contentImportExport" class="pb-5">
            <div class="row g-4 justify-content-center">
                <!-- Import Section -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-7">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px; background-color: #2563eb;">
                                            <i class='bx bx-import fs-2 text-white'></i>
                                        </div>
                                        <div>
                                            <h4 class="fw-bold mb-1" style="color: #1e40af;">Import Soal Baru</h4>
                                            <p class="text-secondary mb-0">Tambahkan soal masal ke bank soal pilihan Anda</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark mb-2">1. Pilih Bank Soal Tujuan</label>
                                        <select class="form-select form-select-lg shadow-sm" id="selectedBankSoalImport" onchange="updateImportButtonState()" style="border-radius: 12px; border: 2px solid #e0f2fe;">
                                            <option value="" selected disabled>-- Pilih Bank Soal --</option>
                                            <?php foreach ($bankSoalList as $bank): ?>
                                            <option value="<?= $bank['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>"
                                                    data-count="<?= $bank['jumlah_soal'] ?>"
                                                    data-pg="<?= $bank['pg_count'] ?>"
                                                    data-essay="<?= $bank['essay_count'] ?>">
                                                <?= htmlspecialchars($bank['nama'] ?? '') ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark mb-2">2. Upload File Data Soal</label>
                                        <div class="upload-zone p-4 border-2 border-dashed border-primary rounded-4 text-center bg-light bg-opacity-50 position-relative" style="border-style: dashed !important; transition: all 0.3s ease;">
                                            <input type="file" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" id="importFile" accept=".csv, .xls, .xlsx" onchange="updateImportButtonState(); updateFileLabel()">
                                            <div id="uploadContent">
                                                <i class='bx bx-cloud-upload text-primary mb-2' style="font-size: 3.5rem;"></i>
                                                <h6 class="fw-bold text-dark mb-1" id="fileLabel">Klik atau drag file ke sini</h6>
                                                <p class="text-secondary small mb-0">Mendukung .csv, .xls, .xlsx (Maksimal 5MB)</p>
                                            </div>
                                        </div>
                                        <div id="fileInfo" class="mt-2 text-center small fw-medium text-success d-none"></div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="bg-white p-4 rounded-4 shadow-sm border border-light h-100">
                                        <h6 class="fw-bold text-dark mb-3"><i class='bx bx-info-circle me-1 text-primary'></i> Panduan Import</h6>
                                        <ul class="list-unstyled small mb-4">
                                            <li class="mb-2 d-flex gap-2"><i class='bx bx-check text-success fs-5'></i> <span>Gunakan format kolom template yang disediakan.</span></li>
                                            <li class="mb-2 d-flex gap-2"><i class='bx bx-check text-success fs-5'></i> <span>Kolom wajib: Deskripsi, Tipe (PG/Essay), Jawaban.</span></li>
                                            <li class="mb-2 d-flex gap-2"><i class='bx bx-check text-success fs-5'></i> <span>Untuk PG, isi kolom Pilihan A sampai E.</span></li>
                                        </ul>
                                        
                                        <div class="d-grid gap-3">
                                            <button class="btn btn-primary btn-lg rounded-3 fw-bold py-3 shadow-sm" onclick="importSoal()" id="btnImport" disabled style="background-color: #2563eb; border: none;">
                                                <i class='bx bx-download me-2'></i> Mulai Proses Import
                                            </button>
                                            <a href="javascript:void(0)" onclick="downloadTemplate()" class="btn btn-outline-primary rounded-3 py-2">
                                                <i class='bx bx-file-blank me-2'></i> Download Template Excel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Section -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-7">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px; background-color: #059669;">
                                            <i class='bx bx-export fs-2 text-white'></i>
                                        </div>
                                        <div>
                                            <h4 class="fw-bold mb-1" style="color: #065f46;">Export Data Soal</h4>
                                            <p class="text-secondary mb-0">Unduh seluruh soal dari database ke format Excel</p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark mb-2">Pilih Bank Soal Sumber</label>
                                        <select class="form-select form-select-lg shadow-sm" id="selectedBankSoal" onchange="updateExportButtonState()" style="border-radius: 12px; border: 2px solid #d1fae5;">
                                            <option value="" selected disabled>-- Pilih Bank Soal --</option>
                                            <?php foreach ($bankSoalList as $bank): ?>
                                            <option value="<?= $bank['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>" 
                                                    data-count="<?= $bank['jumlah_soal'] ?>"
                                                    data-pg="<?= $bank['pg_count'] ?>"
                                                    data-essay="<?= $bank['essay_count'] ?>">
                                                <?= htmlspecialchars($bank['nama'] ?? '') ?> (<?= $bank['jumlah_soal'] ?> soal)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button class="btn btn-success btn-lg px-5 rounded-3 fw-bold py-3 shadow-sm w-100 w-md-auto" onclick="exportSoal()" id="btnExport" disabled style="background-color: #059669; border: none;">
                                        <i class='bx bx-download me-2'></i> Unduh File Excel (.xlsx)
                                    </button>
                                </div>

                                <div class="col-lg-5">
                                    <div id="exportSummary" class="bg-white p-4 rounded-4 shadow-sm border border-light">
                                        <h6 class="fw-bold text-dark mb-3">Ringkasan Data Terpilih</h6>
                                        <div class="row g-3">
                                            <div class="col-4 text-center">
                                                <h3 class="fw-bold mb-0 text-success" id="exportTotalCount">-</h3>
                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Total Soal</small>
                                            </div>
                                            <div class="col-4 text-center border-start border-end">
                                                <h3 class="fw-bold mb-0 text-primary" id="exportPGCount">-</h3>
                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Pilihan Ganda</small>
                                            </div>
                                            <div class="col-4 text-center">
                                                <h3 class="fw-bold mb-0 text-warning" id="exportEssayCount">-</h3>
                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Essay</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Global Base URL and data for external scripts
    var baseUrl = '<?= APP_URL ?>';
    window.bankSoalList = <?= json_encode($bankSoalList) ?>;
</script>

<!-- Load External JavaScript -->
<script src="<?= APP_URL ?>/assets/Script/admin/exam_import_export.js"></script>
