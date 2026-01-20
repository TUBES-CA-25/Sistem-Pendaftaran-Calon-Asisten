
<!-- Tab: Import/Export -->
<div class="tab-pane fade" id="tabImportExport">
    <div class="row g-4">
        <!-- Import Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2 me-3" style="background-color: rgba(37, 99, 235, 0.1);">
                            <i class='bx bx-import fs-4' style="color: #2563EB;"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Import Soal</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="text-secondary small mb-4">Import soal dari file Excel/CSV ke dalam bank soal yang dipilih.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark text-uppercase">1. Pilih Bank Soal</label>
                        <select class="form-select" id="selectedBankSoalImport">
                            <option value="" selected disabled>Pilih Bank Tujuan...</option>
                            <?php foreach ($bankSoalList as $bank): ?>
                            <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>"><?= htmlspecialchars($bank['nama'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark text-uppercase">2. Upload File</label>
                        <input type="file" class="form-control" id="importFile" accept=".csv, .xls, .xlsx">
                        <div class="form-text small mt-2">
                            <i class='bx bx-info-circle me-1'></i> Gunakan template yang disediakan agar format sesuai.
                            <a href="<?= APP_URL ?>/downloadTemplatesoal" class="text-decoration-none fw-bold ms-1" style="color: #2563EB;">Download Template</a>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100 py-2" onclick="importSoal()" id="btnImport" disabled style="--bs-btn-bg: #2563EB; --bs-btn-border-color: #2563EB; --bs-btn-hover-bg: #1d4ed8; --bs-btn-hover-border-color: #1d4ed8;">
                        <i class='bx bx-upload me-2'></i> Import Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Export Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2 me-3" style="background-color: rgba(37, 99, 235, 0.1);">
                            <i class='bx bx-export fs-4' style="color: #2563EB;"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Export Soal</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="text-secondary small mb-4">Export soal dari bank soal ke format CSV untuk backup atau diedit.</p>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark text-uppercase">Pilih Bank Soal</label>
                        <select class="form-select" id="selectedBankSoal" onchange="updateExportButtonState()">
                            <option value="" selected disabled>Pilih Bank Sumber...</option>
                            <?php foreach ($bankSoalList as $bank): ?>
                            <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>" data-count="<?= $bank['jumlah_soal'] ?>">
                                <?= htmlspecialchars($bank['nama'] ?? '') ?> (<?= $bank['jumlah_soal'] ?> soal)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="alert border-0 rounded-3 mb-4" style="background-color: rgba(37, 99, 235, 0.1);">
                        <div class="d-flex">
                            <i class='bx bx-stats mt-1 me-2' style="color: #2563EB;"></i>
                            <div>
                                <div class="fw-bold" style="color: #2563EB;">Ringkasan Bank</div>
                                <div class="small text-secondary mt-1">
                                    Total: <span class="fw-bold text-dark" id="exportTotalCount">-</span> soal<br>
                                    PG: <span class="fw-bold text-dark" id="exportPGCount">-</span> | Essay: <span class="fw-bold text-dark" id="exportEssayCount">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100 py-2" onclick="exportSoal()" id="btnExport" disabled style="--bs-btn-bg: #2563EB; --bs-btn-border-color: #2563EB; --bs-btn-hover-bg: #1d4ed8; --bs-btn-hover-border-color: #1d4ed8;">
                        <i class='bx bx-download me-2'></i> Export ke CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
