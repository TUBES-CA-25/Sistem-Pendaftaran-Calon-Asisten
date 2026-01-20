
<!-- Tab: Import/Export -->
<div class="tab-pane fade" id="tabImportExport">
    <div class="row g-3">
        <!-- Import Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                <div class="card-body p-3 text-center">
                    <!-- Icon & Title -->
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; background-color: #2563eb;">
                            <i class='bx bx-import fs-5 text-white'></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold mb-0" style="color: #1e40af;">Pilih Bank Soal</h6>
                            <small class="text-secondary" style="font-size: 0.75rem;">Pilih bank untuk import</small>
                        </div>
                    </div>
                    
                    <!-- Dropdown -->
                    <select class="form-select mb-3" id="selectedBankSoalImport" onchange="updateImportButtonState()" style="border-radius: 8px; border: 2px solid #dbeafe;">
                        <option value="" selected disabled>-- Pilih Bank --</option>
                        <?php foreach ($bankSoalList as $bank): ?>
                        <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>">
                            <?= htmlspecialchars($bank['nama'] ?? '') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- Upload Icon -->
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 80px; height: 80px; background-color: #2563eb;">
                            <i class='bx bx-upload fs-2 text-white'></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Import Soal</h6>
                        <p class="text-secondary mb-0" style="font-size: 0.75rem;">Upload file Excel atau CSV untuk menambahkan soal</p>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="alert alert-info border-0 rounded-2 mb-3 text-start py-2 px-3" style="background-color: rgba(59, 130, 246, 0.1);">
                        <div class="d-flex align-items-start">
                            <i class='bx bx-info-circle me-2' style="color: #2563eb; font-size: 1rem;"></i>
                            <div style="font-size: 0.7rem;">
                                <strong>Persyaratan:</strong> CSV/Excel (.csv, .xls, .xlsx)<br>
                                Sesuai template: Deskripsi, Tipe, Pilihan A-E, Jawaban
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Input -->
                    <div class="mb-3">
                        <label class="btn btn-outline-primary w-100 py-2 rounded-2 position-relative" style="cursor: pointer; border: 2px dashed #2563eb; font-size: 0.85rem;">
                            <i class='bx bx-file me-1'></i>
                            <span id="fileLabel">Choose File</span>
                            <input type="file" class="d-none" id="importFile" accept=".csv, .xls, .xlsx" onchange="updateImportButtonState(); updateFileLabel()">
                        </label>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">No file chosen</small>
                    </div>
                    
                    <!-- Import Button -->
                    <button class="btn btn-primary w-100 py-2 rounded-2 fw-bold mb-2" onclick="importSoal()" id="btnImport" disabled style="background-color: #2563eb; border-color: #2563eb; font-size: 0.9rem;">
                        <i class='bx bx-download me-1'></i> Import Soal
                    </button>
                    
                    <!-- Download Template Link -->
                    <a href="<?= APP_URL ?>/downloadTemplatesoal" class="text-decoration-none fw-medium" style="color: #2563eb; font-size: 0.8rem;">
                        <i class='bx bx-download me-1'></i> Download Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Export Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                <div class="card-body p-3 text-center">
                    <!-- Icon & Title -->
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; background-color: #059669;">
                            <i class='bx bx-export fs-5 text-white'></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold mb-0" style="color: #065f46;">Pilih Bank Soal</h6>
                            <small class="text-secondary" style="font-size: 0.75rem;">Pilih bank untuk export</small>
                        </div>
                    </div>
                    
                    <!-- Dropdown -->
                    <select class="form-select mb-3" id="selectedBankSoal" onchange="updateExportButtonState()" style="border-radius: 8px; border: 2px solid #d1fae5;">
                        <option value="" selected disabled>-- Pilih Bank --</option>
                        <?php foreach ($bankSoalList as $bank): ?>
                        <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>" data-count="<?= $bank['jumlah_soal'] ?>">
                            <?= htmlspecialchars($bank['nama'] ?? '') ?> (<?= $bank['jumlah_soal'] ?> soal)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- Download Icon -->
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 80px; height: 80px; background-color: #059669;">
                            <i class='bx bx-download fs-2 text-white'></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Export Soal</h6>
                        <p class="text-secondary mb-0" style="font-size: 0.75rem;">Download soal dalam format Excel</p>
                    </div>
                    
                    <!-- Summary Box -->
                    <div class="alert border-0 rounded-2 mb-3 py-2 px-2" style="background-color: rgba(16, 185, 129, 0.1);">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="p-2 rounded-2" style="background-color: white;">
                                    <h4 class="fw-bold mb-0" id="exportTotalCount" style="color: #059669; font-size: 1.5rem;">-</h4>
                                    <small class="text-secondary text-uppercase fw-medium" style="font-size: 0.65rem;">Total</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-2" style="background-color: white;">
                                    <h4 class="fw-bold mb-0" id="exportPGCount" style="color: #2563eb; font-size: 1.5rem;">-</h4>
                                    <small class="text-secondary text-uppercase fw-medium" style="font-size: 0.65rem;">PG</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-2" style="background-color: white;">
                                    <h4 class="fw-bold mb-0" id="exportEssayCount" style="color: #f59e0b; font-size: 1.5rem;">-</h4>
                                    <small class="text-secondary text-uppercase fw-medium" style="font-size: 0.65rem;">Essay</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Button -->
                    <button class="btn btn-success w-100 py-2 rounded-2 fw-bold" onclick="exportSoal()" id="btnExport" disabled style="background-color: #059669; border-color: #059669; font-size: 0.9rem;">
                        <i class='bx bx-download me-1'></i> Export Soal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileLabel() {
    const fileInput = document.getElementById('importFile');
    const fileLabel = document.getElementById('fileLabel');
    const fileText = fileInput.nextElementSibling;
    
    if (fileInput.files.length > 0) {
        const fileName = fileInput.files[0].name;
        fileLabel.innerHTML = '<i class="bx bx-check-circle me-2"></i>' + fileName;
        fileText.textContent = 'File selected: ' + fileName;
        fileText.classList.remove('text-muted');
        fileText.classList.add('text-success');
    } else {
        fileLabel.innerHTML = '<i class="bx bx-file me-2"></i>Choose File';
        fileText.textContent = 'No file chosen';
        fileText.classList.remove('text-success');
        fileText.classList.add('text-muted');
    }
}
</script>
