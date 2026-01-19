<?php
/**
 * Tes Tulis Admin - Bank Soal Management
 * Modern Bootstrap 5 Design with Bank Soal System
 */
use App\Controllers\exam\ExamController;

// Get all bank soal from database
$bankSoalList = ExamController::getAllBankSoal();

// Get all soal data
$allSoal = ExamController::viewAllSoal();
$pgCount = 0;
$essayCount = 0;
$totalSoal = count($allSoal);
foreach ($allSoal as $soal) {
    if (($soal['status_soal'] ?? '') === 'pilihan_ganda') {
        $pgCount++;
    } else {
        $essayCount++;
    }
}
?>

<style>
/* ==================== GLOBAL STYLES ==================== */
body {
    background: var(--bs-body-bg);
    min-height: 100vh;
}

main {
    padding: 0;
    margin: -20px -20px -20px -20px;
    width: calc(100% + 40px);
}

/* ==================== HEADER STYLES ==================== */
/* Header styles moved to components/PageHeader.php */

/* ==================== NAVIGATION TABS - Bootstrap Enhanced ==================== */
.nav-tabs-custom {
    background: white;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
    margin-bottom: 0;
}

.nav-tabs-custom .nav-link {
    color: #64748b;
    font-weight: 500;
    padding: 0.875rem 1.5rem;
    border-radius: 0.625rem;
    margin-right: 0.5rem;
    transition: all 0.2s ease;
    border: none;
}

.nav-tabs-custom .nav-link:hover {
    background: #f1f5f9;
    color: var(--bs-primary-dark);
}

.nav-tabs-custom .nav-link.active {
    background: var(--gradient-header);
    color: white !important;
}

.nav-tabs-custom .nav-link i {
    margin-right: 0.5rem;
}

/* ==================== BANK SOAL CARDS - Bootstrap Grid ==================== */
.bank-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.bank-card {
    background: white;
    border-radius: var(--border-radius-card);
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: 2px solid transparent;
}

.bank-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.2);
    border-color: var(--bs-primary-dark);
}

.bank-card-header {
    padding: 1.5rem;
    position: relative;
}

.bank-card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-header);
}

.bank-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border-radius: var(--bs-border-radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: var(--bs-primary-dark);
    margin-bottom: 1rem;
}

.bank-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.bank-desc {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    min-height: 2.4em;
    display: block;
    word-break: break-word;
    overflow-wrap: break-word;
}

.bank-stats {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.bank-stat-item {
    background: #f8fafc;
    padding: 0.5rem 1rem;
    border-radius: var(--bs-border-radius);
    font-size: 0.8rem;
}

.bank-stat-item i {
    margin-right: 0.375rem;
}

.bank-stat-item.pg {
    color: var(--bs-primary-dark);
    background: #eff6ff;
}

.bank-stat-item.essay {
    color: #d97706;
    background: #fef3c7;
}

.bank-stat-item.total {
    color: #059669;
    background: #d1fae5;
}

.bank-card-footer {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bank-date {
    font-size: 0.8rem;
    color: #94a3b8;
}

.bank-action-btn {
    background: var(--gradient-header);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--bs-border-radius);
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.bank-action-btn:hover {
    background: var(--bs-primary-dark);
    transform: scale(1.05);
}

/* ==================== CREATE BANK CARD - Bootstrap Enhanced ==================== */
.create-bank-card {
    background: white;
    border: 2px dashed #cbd5e1;
    border-radius: var(--border-radius-card);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 280px;
}

.create-bank-card:hover {
    border-color: var(--bs-primary-dark);
    background: #f8fafc;
}

.create-bank-card i {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.create-bank-card:hover i {
    color: var(--bs-primary-dark);
}

.create-bank-card span {
    font-weight: 600;
    color: #64748b;
}

/* ==================== DETAIL VIEW - Bootstrap Enhanced ==================== */
.detail-view {
    display: none;
    animation: fadeIn 0.3s ease;
}

.detail-view.active {
    display: block;
}

.bank-list-view {
    display: block;
}

.bank-list-view.hidden {
    display: none;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.detail-header {
    background: white;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.back-btn {
    background: none;
    border: none;
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    border-radius: var(--bs-border-radius);
    transition: all 0.2s ease;
}

.back-btn:hover {
    background: #f1f5f9;
    color: var(--bs-primary-dark);
}

.detail-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

.detail-actions {
    display: flex;
    gap: 0.75rem;
}

/* ==================== SOAL LIST - Bootstrap Enhanced ==================== */
.soal-container {
    padding: 1.5rem;
}

.soal-filter-bar {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: var(--bs-border-radius-lg);
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
}

.search-input-group {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-input-group i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-input-group input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.625rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.search-input-group input:focus {
    outline: none;
    border-color: var(--bs-primary-dark);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: var(--bs-border-radius);
    font-size: 0.85rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-btn:hover, .filter-btn.active {
    border-color: var(--bs-primary-dark);
    color: var(--bs-primary-dark);
    background: #eff6ff;
}

/* ==================== SOAL CARD - Bootstrap Enhanced ==================== */
.soal-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.soal-card {
    background: white;
    border-radius: var(--bs-border-radius-xl);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border-left: 4px solid var(--bs-primary-dark);
    transition: all 0.2s ease;
}

.soal-card:hover {
    box-shadow: var(--shadow-md);
}

.soal-card.essay {
    border-left-color: #d97706;
}

.soal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.soal-number {
    width: 44px;
    height: 44px;
    background: var(--gradient-header);
    color: white;
    border-radius: var(--bs-border-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
}

.soal-card.essay .soal-number {
    background: linear-gradient(135deg, #d97706, #b45309);
}

.soal-type-badge {
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.soal-type-badge.pg {
    background: #dbeafe;
    color: var(--bs-primary-dark);
}

.soal-type-badge.essay {
    background: #fef3c7;
    color: #d97706;
}

.soal-content {
    margin-left: 56px;
}

.soal-text {
    font-size: 1rem;
    color: #1e293b;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.soal-options {
    background: #f8fafc;
    border-radius: 0.625rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.soal-options-title {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.option-item {
    padding: 0.5rem 0;
    color: #475569;
    font-size: 0.9rem;
}

.soal-answer {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-radius: 0.625rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.soal-answer i {
    color: #059669;
    font-size: 1.25rem;
}

.soal-answer-label {
    font-size: 0.8rem;
    color: #047857;
    font-weight: 600;
    text-transform: uppercase;
}

.soal-answer-text {
    color: #065f46;
    font-weight: 600;
}

.soal-actions {
    display: flex;
    gap: 0.5rem;
    margin-left: auto;
}

.soal-action-btn {
    width: 36px;
    height: 36px;
    border-radius: var(--bs-border-radius);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.soal-action-btn.edit {
    background: #eff6ff;
    color: var(--bs-primary-dark);
}

.soal-action-btn.edit:hover {
    background: var(--bs-primary-dark);
    color: white;
}

.soal-action-btn.delete {
    background: #fef2f2;
    color: #dc2626;
}

.soal-action-btn.delete:hover {
    background: #dc2626;
    color: white;
}

/* ==================== EMPTY STATE - Bootstrap Enhanced ==================== */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--bs-border-radius-xl);
}

.empty-state i {
    font-size: 5rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #475569;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #94a3b8;
}

/* ==================== MODAL STYLES - Bootstrap 5.3.3 Structure ==================== */
.modal-custom .modal-content {
    border: none;
    border-radius: var(--border-radius-card);
    overflow: hidden;
}

.modal-custom .modal-header {
    background: var(--gradient-header);
    color: white;
    border: none;
    padding: 1.5rem;
}

.modal-custom .modal-title {
    font-weight: 700;
}

.modal-custom .modal-body {
    padding: 2rem;
}

.modal-custom .form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.modal-custom .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 0.625rem;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.modal-custom .form-control:focus {
    border-color: var(--bs-primary-dark);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.type-selector {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.type-option {
    flex: 1;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: var(--bs-border-radius-lg);
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.type-option:hover {
    border-color: var(--bs-primary-dark);
}

.type-option.selected {
    border-color: var(--bs-primary-dark);
    background: #eff6ff;
}

.type-option i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

.type-option.pg i { color: var(--bs-primary-dark); }
.type-option.essay i { color: #d97706; }

.type-option span {
    font-weight: 600;
    color: #374151;
}

/* ==================== BUTTONS - Bootstrap Enhanced ==================== */
.btn-primary-custom {
    background: var(--gradient-header);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.625rem;
    font-weight: 600;
    color: white;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-success-custom {
    background: linear-gradient(135deg, #059669, #047857);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.625rem;
    font-weight: 600;
    color: white;
}

.btn-outline-custom {
    border: 2px solid #e2e8f0;
    background: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.625rem;
    font-weight: 600;
    color: #64748b;
    transition: all 0.2s ease;
}

.btn-outline-custom:hover {
    border-color: var(--bs-primary-dark);
    color: var(--bs-primary-dark);
}

/* ==================== PILIHAN A-E STYLES - Bootstrap Forms ==================== */
.pilihan-fields .input-group {
    border-radius: 0.625rem;
    overflow: hidden;
}

.pilihan-fields .option-label {
    width: 45px;
    justify-content: center;
    font-weight: 700;
    background: var(--gradient-header);
    color: white;
    border: none;
}

.pilihan-fields .option-label.option-optional {
    background: linear-gradient(135deg, #94a3b8, #64748b);
}

.pilihan-fields .form-control {
    border: 2px solid #e5e7eb;
    border-left: none;
}

.pilihan-fields .form-control:focus {
    border-color: var(--bs-primary-dark);
    box-shadow: none;
}

.jawaban-selector {
    background: #f8fafc;
    padding: 1rem;
    border-radius: var(--bs-border-radius-lg);
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.jawaban-selector .form-check {
    margin: 0;
}

.jawaban-selector .form-check-input {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.5rem;
    cursor: pointer;
}

.jawaban-selector .form-check-input:checked {
    background-color: var(--bs-primary-dark);
    border-color: var(--bs-primary-dark);
}

.jawaban-selector .form-check-label {
    font-weight: 600;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    background: white;
    border-radius: var(--bs-border-radius);
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid #e5e7eb;
}

.jawaban-selector .form-check-input:checked + .form-check-label {
    background: var(--bs-primary-dark);
    color: white;
    border-color: var(--bs-primary-dark);
}
</style>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Bank Soal Ujian';
        $subtitle = 'Kelola bank soal untuk tes tertulis calon asisten laboratorium';
        $icon = 'bx bx-library';
        
        // Stats badges untuk header
        ob_start();
    ?>
    <div class="header-stats">
        <div class="stat-badge">
            <span class="stat-number" id="stat-count-bank"><?= count($bankSoalList) ?></span>
            <span class="stat-label">Bank Soal</span>
        </div>
        <div class="stat-badge">
            <span class="stat-number" id="stat-count-total"><?= count($allSoal) ?></span>
            <span class="stat-label">Total Soal</span>
        </div>
        <div class="stat-badge">
            <span class="stat-number" id="stat-count-pg"><?= $pgCount ?></span>
            <span class="stat-label">Pilihan Ganda</span>
        </div>
        <div class="stat-badge">
            <span class="stat-number" id="stat-count-essay"><?= $essayCount ?></span>
            <span class="stat-label">Essay</span>
        </div>
    </div>
    <?php
        $headerRightContent = ob_get_clean();
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <!-- Navigation Tabs -->
    <div class="nav-tabs-custom">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="nav nav-pills" id="bankSoalTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="pill" href="#tabBankSoal">
                        <i class='bx bx-folder'></i> Bank Soal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="pill" href="#tabImportExport">
                        <i class='bx bx-transfer'></i> Import/Export
                    </a>
                </li>
            </ul>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createBankModal">
                <i class='bx bx-plus me-1'></i> Buat Bank Soal Baru
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
                    <i class='bx bx-folder-open' style="font-size: 5rem; color: #cbd5e1;"></i>
                    <h4 class="mt-3 text-muted">Belum Ada Bank Soal</h4>
                    <p class="text-muted">Klik tombol "Buat Bank Soal Baru" untuk membuat bank soal pertama</p>
                </div>
                <?php endif; ?>
                <div class="bank-grid">
                    <!-- Existing Banks -->
                    <?php foreach ($bankSoalList as $bank): ?>
                    <div class="bank-card" id="bank-card-<?= $bank['id'] ?>">
                        <div class="bank-card-header" onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama']) ?>')">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="bank-icon">
                                    <i class='bx bx-book-content'></i>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteBank(<?= $bank['id'] ?>)" title="Hapus Bank">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                            <h3 class="bank-title mt-2"><?= htmlspecialchars($bank['nama']) ?></h3>
                            <p class="bank-desc"><?= htmlspecialchars($bank['deskripsi'] ?? '') ?></p>
                            <div class="bank-stats">
                                <span class="bank-stat-item total">
                                    <i class='bx bx-file'></i> <?= $bank['jumlah_soal'] ?? 0 ?> Soal
                                </span>
                                <span class="bank-stat-item pg">
                                    <i class='bx bx-key'></i> Token: <strong><?= htmlspecialchars($bank['token'] ?? '-') ?></strong>
                                </span>
                            </div>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div class="form-check form-switch cursor-pointer" onclick="event.stopPropagation()">
                                    <input class="form-check-input" type="checkbox" id="activeSwitch_<?= $bank['id'] ?>" 
                                        <?= ($bank['is_active'] ?? 0) == 1 ? 'checked' : '' ?>
                                        onchange="window.activateBank(<?= $bank['id'] ?>)">
                                    <label class="form-check-label small <?= ($bank['is_active'] ?? 0) == 1 ? 'text-primary fw-bold' : 'text-muted' ?>" for="activeSwitch_<?= $bank['id'] ?>">
                                        <?= ($bank['is_active'] ?? 0) == 1 ? 'Aktif' : 'Tidak Aktif' ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bank-card-footer">
                             <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); window.editBankModal(<?= $bank['id'] ?>)" title="Edit Bank">
                                    <i class='bx bx-edit'></i> Edit
                                </button>
                                <span class="bank-action-btn" onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama']) ?>')">
                                    Lihat Soal <i class='bx bx-chevron-right'></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Create New Bank Card -->
                    <div class="create-bank-card" data-bs-toggle="modal" data-bs-target="#createBankModal">
                        <i class='bx bx-plus-circle'></i>
                        <span>Buat Bank Soal Baru</span>
                    </div>
                </div>
            </div>

            <!-- Bank Detail View -->
            <div class="detail-view" id="bankDetailView">
                <div class="detail-header">
                    <div class="d-flex align-items-center gap-3">
                        <button class="back-btn" onclick="closeBankDetail()">
                            <i class='bx bx-arrow-back'></i> Kembali
                        </button>
                        <h2 class="detail-title" id="detailBankTitle">Ujian Tes Tertulis 2024</h2>
                    </div>
                    <div class="detail-actions">
                        <button class="btn btn-outline-custom" onclick="editBank()">
                            <i class='bx bx-edit'></i> Edit Bank
                        </button>
                        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addSoalModal">
                            <i class='bx bx-plus'></i> Tambah Soal
                        </button>
                    </div>
                </div>

                <div class="soal-container">
                    <!-- Filter Bar -->
                    <div class="soal-filter-bar">
                        <div class="search-input-group">
                            <i class='bx bx-search'></i>
                            <input type="text" id="searchSoal" placeholder="Cari soal...">
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">Semua</button>
                            <button class="filter-btn" data-filter="pilihan_ganda">Pilihan Ganda</button>
                            <button class="filter-btn" data-filter="essay">Essay</button>
                        </div>
                    </div>

                    <!-- Soal List - Rendered by JavaScript -->
                    <div class="soal-list" id="soalList">
                        <div class="empty-state">
                            <i class='bx bx-file-blank'></i>
                            <h4>Pilih Bank Soal</h4>
                            <p>Klik salah satu bank soal untuk melihat daftar pertanyaan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Import/Export -->
        <div class="tab-pane fade" id="tabImportExport">
            <div class="container-fluid py-4">
                <div class="row g-4">
                    <!-- Import Card -->
                    <div class="col-md-6">
                        <div class="card h-100" style="border:none; border-radius:20px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1); transition: all 0.3s ease; overflow: hidden;">
                            <div class="card-body p-5" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                                <!-- Bank Selection Section -->
                                <div class="mb-4 p-4 rounded-3" style="background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                            <i class='bx bx-folder-open text-white' style="font-size: 1.3rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0" style="color: #1f2937; font-size: 0.95rem;">Pilih Bank Soal</h6>
                                            <small class="text-muted" style="font-size: 0.8rem;">Pilih bank untuk import</small>
                                        </div>
                                    </div>
                                    <select class="form-select" id="selectedBankSoalImport" style="border-radius:10px; border:2px solid #e2e8f0; padding: 0.65rem 1rem; font-size: 0.95rem;">
                                        <option value="" disabled selected>-- Pilih Bank --</option>
                                        <?php foreach ($bankSoalList as $bank): ?>
                                        <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama']) ?>" data-count="<?= $bank['jumlah_soal'] ?>">
                                            <?= htmlspecialchars($bank['nama']) ?> (<?= $bank['jumlah_soal'] ?> soal)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Icon -->
                                <div class="mb-4 position-relative text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 90px; height: 90px; background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);">
                                        <i class='bx bx-upload' style="font-size:2.5rem; color: white;"></i>
                                    </div>
                                    <div class="position-absolute" style="top: -10px; right: calc(50% - 55px); width: 35px; height: 35px; background: rgba(37, 99, 235, 0.1); border-radius: 50%;"></div>
                                </div>
                                
                                <!-- Title & Description -->
                                <div class="text-center">
                                    <h4 class="fw-bold mb-2" style="color: #1f2937;">Import Soal</h4>
                                    <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">Upload file Excel atau CSV untuk menambahkan soal ke bank yang dipilih</p>
                                </div>
                                
                                <!-- Info Alert -->
                                <div class="alert mb-4 text-start" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 1px solid #93c5fd; border-radius:12px; border-left: 4px solid #2563eb;">
                                    <div class="d-flex align-items-start">
                                        <i class='bx bx-info-circle me-2 mt-1' style="color: #1d4ed8; font-size: 1.2rem;"></i>
                                        <div style="font-size: 0.85rem;">
                                            <strong style="color: #1e40af;">Persyaratan Import:</strong>
                                            <ul style="color: #1e3a8a; margin: 8px 0 0 0; padding-left: 20px;">
                                                <li>Format file: <strong>CSV (.csv)</strong> atau <strong>Excel (.xls, .xlsx)</strong></li>
                                                <li>File harus sesuai dengan template yang disediakan</li>
                                                <li>Kolom: Deskripsi, Tipe (PG/Essay), Pilihan A-E, Jawaban</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- File Input -->
                                <div class="mb-3">
                                    <label class="form-label text-start d-block fw-semibold mb-2" style="color: #374151; font-size: 0.9rem;">
                                        <i class='bx bx-file me-1'></i>Pilih File
                                    </label>
                                    <input type="file" class="form-control" id="importFile" accept=".xlsx,.xls,.csv" style="border-radius:12px; border: 2px dashed #cbd5e1; padding: 0.75rem; transition: all 0.2s;">
                                </div>
                                
                                <!-- Import Button -->
                                <button class="btn w-100 mb-3" onclick="importSoal()" id="btnImport" disabled style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; border-radius: 12px; padding: 0.875rem 1.5rem; font-weight: 600; font-size: 1rem; transition: all 0.2s;">
                                    <i class='bx bx-upload me-2'></i>Import Soal ke Bank Terpilih
                                </button>
                                
                                <!-- Download Template Link -->
                                <div class="mt-3 text-center">
                                    <a href="<?= APP_URL ?>/soal/download-template" class="text-decoration-none d-inline-flex align-items-center" style="color: #2563eb; font-weight: 500; font-size: 0.9rem; transition: all 0.2s;">
                                        <i class='bx bx-download me-1' style="font-size: 1.1rem;"></i>
                                        Download Template Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Card with Bank Selection -->
                    <div class="col-md-6">
                        <div class="card h-100" style="border:none; border-radius:20px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.1); transition: all 0.3s ease; overflow: hidden;">
                            <div class="card-body p-5" style="background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%);">
                                <!-- Bank Selection Section -->
                                <div class="mb-4 p-4 rounded-3" style="background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #059669, #047857);">
                                            <i class='bx bx-folder-open text-white' style="font-size: 1.3rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0" style="color: #1f2937; font-size: 0.95rem;">Pilih Bank Soal</h6>
                                            <small class="text-muted" style="font-size: 0.8rem;">Pilih bank untuk export</small>
                                        </div>
                                    </div>
                                    <select class="form-select" id="selectedBankSoal" style="border-radius:10px; border:2px solid #e2e8f0; padding: 0.65rem 1rem; font-size: 0.95rem;">
                                        <option value="" disabled selected>-- Pilih Bank --</option>
                                        <?php foreach ($bankSoalList as $bank): ?>
                                        <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama']) ?>" data-count="<?= $bank['jumlah_soal'] ?>">
                                            <?= htmlspecialchars($bank['nama']) ?> (<?= $bank['jumlah_soal'] ?> soal)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Icon -->
                                <div class="mb-4 position-relative text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 90px; height: 90px; background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);">
                                        <i class='bx bx-download' style="font-size:2.5rem; color: white;"></i>
                                    </div>
                                    <div class="position-absolute" style="top: -10px; right: calc(50% - 55px); width: 35px; height: 35px; background: rgba(5, 150, 105, 0.1); border-radius: 50%;"></div>
                                </div>
                                
                                <!-- Title & Description -->
                                <div class="text-center">
                                    <h4 class="fw-bold mb-2" style="color: #1f2937;">Export Soal</h4>
                                    <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">Download soal dari bank yang dipilih dalam format Excel</p>
                                </div>
                                
                                <!-- Export Stats -->
                                <div class="export-info mb-4 p-4 rounded-3" id="exportInfo" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac;">
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <div class="fs-2 fw-bold mb-1" id="exportTotalCount" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Soal</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <div class="fs-2 fw-bold mb-1" id="exportPGCount" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Pilihan Ganda</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <div class="fs-2 fw-bold mb-1" id="exportEssayCount" style="background: linear-gradient(135deg, #f59e0b, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Essay</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Export Button -->
                                <button class="btn w-100" onclick="exportSoal()" id="btnExport" disabled style="background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; padding: 0.875rem 1.5rem; font-weight: 600; font-size: 1rem; transition: all 0.2s;">
                                    <i class='bx bx-download me-2'></i>Export dari Bank Terpilih
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Bank Modal -->
<div class="modal fade modal-custom" id="createBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='bx bx-folder-plus me-2'></i>Buat Bank Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBankForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank Soal</label>
                        <input type="text" class="form-control" name="nama_bank" required placeholder="Contoh: Ujian Tes Tertulis 2024">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi_bank" rows="3" placeholder="Deskripsi singkat tentang bank soal ini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Token Ujian</label>
                        <input type="text" class="form-control" name="token_bank" placeholder="Contoh: UJIAN2024" required>
                        <div class="form-text">Token yang harus dimasukkan peserta untuk memulai ujian.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class='bx bx-check me-1'></i> Buat Bank Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade modal-custom" id="editBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='bx bx-edit me-2'></i>Edit Bank Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBankForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editBankId">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank Soal</label>
                        <input type="text" class="form-control" name="nama" id="editBankName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="editBankDesc" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Token Ujian</label>
                        <input type="text" class="form-control" name="token" id="editBankToken" required>
                        <div class="form-text">Update token untuk ujian ini.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class='bx bx-check me-1'></i> Update Bank Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Soal Modal -->
<div class="modal fade modal-custom" id="addSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='bx bx-plus-circle me-2'></i>Tambah Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSoalForm">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Tipe Soal</label>
                        <div class="type-selector">
                            <div class="type-option pg selected" data-type="pilihan_ganda">
                                <i class='bx bx-list-check'></i>
                                <span>Pilihan Ganda</span>
                            </div>
                            <div class="type-option essay" data-type="essay">
                                <i class='bx bx-edit-alt'></i>
                                <span>Essay</span>
                            </div>
                        </div>
                        <input type="hidden" name="status_soal" id="soalType" value="pilihan_ganda">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Soal <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="4" required placeholder="Tuliskan pertanyaan soal..."></textarea>
                    </div>
                    <div class="mb-3" id="pilihanContainer">
                        <label class="form-label">Pilihan Jawaban</label>
                        <div class="pilihan-fields">
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">A</span>
                                <input type="text" class="form-control" name="pilihan_a" placeholder="Isi pilihan A" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">B</span>
                                <input type="text" class="form-control" name="pilihan_b" placeholder="Isi pilihan B" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">C</span>
                                <input type="text" class="form-control" name="pilihan_c" placeholder="Isi pilihan C" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">D</span>
                                <input type="text" class="form-control" name="pilihan_d" placeholder="Isi pilihan D" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label option-optional">E</span>
                                <input type="text" class="form-control" name="pilihan_e" placeholder="Isi pilihan E (opsional)">
                                <span class="input-group-text text-muted small">Opsional</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="jawabanPGContainer">
                        <label class="form-label">Jawaban Benar</label>
                        <div class="jawaban-selector">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="jawabA" value="A" required>
                                <label class="form-check-label" for="jawabA">A</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="jawabB" value="B">
                                <label class="form-check-label" for="jawabB">B</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="jawabC" value="C">
                                <label class="form-check-label" for="jawabC">C</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="jawabD" value="D">
                                <label class="form-check-label" for="jawabD">D</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="jawabE" value="E">
                                <label class="form-check-label" for="jawabE">E</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="jawabanEssayContainer" style="display:none;">
                        <label class="form-label">Jawaban / Kunci Jawaban</label>
                        <textarea class="form-control" name="jawaban_essay" rows="3" placeholder="Tuliskan kunci jawaban essay..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class='bx bx-check me-1'></i> Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Soal Modal -->
<div class="modal fade modal-custom" id="editSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='bx bx-edit me-2'></i>Edit Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSoalForm">
                <div class="modal-body">
                    <input type="hidden" id="editSoalId" name="id">
                    <div class="mb-4">
                        <label class="form-label">Tipe Soal</label>
                        <div class="type-selector">
                            <div class="type-option pg" data-type="pilihan_ganda" id="editTypePG">
                                <i class='bx bx-list-check'></i>
                                <span>Pilihan Ganda</span>
                            </div>
                            <div class="type-option essay" data-type="essay" id="editTypeEssay">
                                <i class='bx bx-edit-alt'></i>
                                <span>Essay</span>
                            </div>
                        </div>
                        <input type="hidden" name="status_soal" id="editSoalType" value="pilihan_ganda">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Soal</label>
                        <textarea class="form-control" name="deskripsi" id="editDeskripsi" rows="4" required></textarea>
                    </div>
                    <div class="mb-3" id="editPilihanContainer">
                        <label class="form-label">Pilihan Jawaban</label>
                        <div class="pilihan-fields">
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">A</span>
                                <input type="text" class="form-control" name="pilihan_a" id="editPilihanA" placeholder="Isi pilihan A">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">B</span>
                                <input type="text" class="form-control" name="pilihan_b" id="editPilihanB" placeholder="Isi pilihan B">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">C</span>
                                <input type="text" class="form-control" name="pilihan_c" id="editPilihanC" placeholder="Isi pilihan C">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label">D</span>
                                <input type="text" class="form-control" name="pilihan_d" id="editPilihanD" placeholder="Isi pilihan D">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text option-label option-optional">E</span>
                                <input type="text" class="form-control" name="pilihan_e" id="editPilihanE" placeholder="Isi pilihan E (opsional)">
                                <span class="input-group-text text-muted small">Opsional</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="editJawabanPGContainer">
                        <label class="form-label">Jawaban Benar</label>
                        <div class="jawaban-selector">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="editJawabA" value="A">
                                <label class="form-check-label" for="editJawabA">A</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="editJawabB" value="B">
                                <label class="form-check-label" for="editJawabB">B</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="editJawabC" value="C">
                                <label class="form-check-label" for="editJawabC">C</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="editJawabD" value="D">
                                <label class="form-check-label" for="editJawabD">D</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jawaban" id="editJawabE" value="E">
                                <label class="form-check-label" for="editJawabE">E</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="editJawabanEssayContainer" style="display:none;">
                        <label class="form-label">Jawaban / Kunci Jawaban</label>
                        <textarea class="form-control" name="jawaban_essay" id="editJawabanEssay" rows="3" placeholder="Tuliskan kunci jawaban essay..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class='bx bx-check me-1'></i> Update Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Data soal dan bank - Use window to avoid redeclaration errors
window.allSoal = <?= json_encode(array_map(function($soal) {
    return [
        'id' => $soal['id'] ?? 0,
        'bank_soal_id' => $soal['bank_soal_id'] ?? null,
        'deskripsi' => $soal['deskripsi'] ?? '',
        'status_soal' => $soal['status_soal'] ?? 'essay',
        'pilihan' => $soal['pilihan'] ?? '',
        'jawaban' => $soal['jawaban'] ?? ''
    ];
}, $allSoal)) ?>;

window.bankSoalList = <?= json_encode($bankSoalList) ?>;
window.currentBankId = null;
window.currentBankSoal = [];

// Open Bank Detail
window.openBankDetail = function(bankId, bankName) {
    window.currentBankId = bankId;
    document.getElementById('bankListView').classList.add('hidden');
    document.getElementById('bankDetailView').classList.add('active');
    document.getElementById('detailBankTitle').textContent = bankName;
    
    // Load questions for this bank
    window.loadBankQuestions(bankId);
}

// Load questions for specific bank
window.loadBankQuestions = function(bankId) {
    const soalList = document.getElementById('soalList');
    soalList.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Memuat soal...</p></div>';
    
    // Use global baseUrl
    // const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/');
    
    fetch(baseUrl + '/getBankQuestions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'bank_id=' + bankId
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.currentBankSoal = data.data || [];
            renderSoalList(window.currentBankSoal);
        } else {
            soalList.innerHTML = '<div class="empty-state"><i class="bx bx-error-circle"></i><h4>Gagal memuat soal</h4></div>';
        }
    })
    .catch((err) => {
        console.error('Error loading questions:', err);
        soalList.innerHTML = '<div class="empty-state"><i class="bx bx-error-circle"></i><h4>Terjadi kesalahan</h4></div>';
    });
}

// Render soal list
window.renderSoalList = function(soalArray) {
    const soalList = document.getElementById('soalList');
    
    if (!soalArray || soalArray.length === 0) {
        soalList.innerHTML = `
            <div class="empty-state">
                <i class='bx bx-file-blank'></i>
                <h4>Belum Ada Soal</h4>
                <p>Klik tombol "Tambah Soal" untuk menambahkan soal baru ke bank ini</p>
            </div>`;
        return;
    }
    
    let html = '';
    soalArray.forEach((soal, index) => {
        const isPG = (soal.status_soal || '') === 'pilihan_ganda';
        const optionsHtml = isPG && soal.pilihan ? window.renderOptions(soal.pilihan) : '';
        
        html += `
        <div class="soal-card ${isPG ? '' : 'essay'}" data-id="${soal.id}" data-type="${soal.status_soal || 'essay'}">
            <div class="soal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="soal-number">${index + 1}</div>
                    <span class="soal-type-badge ${isPG ? 'pg' : 'essay'}">
                        ${isPG ? 'Pilihan Ganda' : 'Essay'}
                    </span>
                </div>
                <div class="soal-actions">
                    <button class="soal-action-btn edit" onclick="window.editSoal(${soal.id})" title="Edit">
                        <i class='bx bx-edit'></i>
                    </button>
                    <button class="soal-action-btn delete" onclick="window.deleteSoal(${soal.id})" title="Hapus">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            </div>
            <div class="soal-content">
                <p class="soal-text">${window.escapeHtml(soal.deskripsi || '')}</p>
                ${optionsHtml}
                ${soal.jawaban ? `
                <div class="soal-answer">
                    <i class='bx bx-check-circle'></i>
                    <div>
                        <div class="soal-answer-label">Jawaban Benar</div>
                        <div class="soal-answer-text">${window.escapeHtml(soal.jawaban)}</div>
                    </div>
                </div>` : ''}
            </div>
        </div>`;
    });
    
    soalList.innerHTML = html;
}

window.renderOptions = function(pilihan) {
    if (!pilihan) return '';
    const options = pilihan.split(',').map(p => p.trim());
    let html = '<div class="soal-options"><div class="soal-options-title">Pilihan Jawaban</div>';
    options.forEach(opt => {
        html += `<div class="option-item">${escapeHtml(opt)}</div>`;
    });
    html += '</div>';
    return html;
}

window.escapeHtml = function(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Refresh Bank Dropdowns in Import/Export Tab
window.refreshBankDropdowns = function(newBankId, newBankName, soalCount) {
    // Update Import Dropdown
    const importSelect = document.getElementById('selectedBankSoalImport');
    if (importSelect) {
        const newOptionImport = document.createElement('option');
        newOptionImport.value = newBankId;
        newOptionImport.setAttribute('data-name', newBankName);
        newOptionImport.setAttribute('data-count', soalCount || 0);
        newOptionImport.textContent = `${newBankName} (${soalCount || 0} soal)`;
        
        // Insert after the first option (placeholder)
        if (importSelect.options.length > 1) {
            importSelect.insertBefore(newOptionImport, importSelect.options[1]);
        } else {
            importSelect.appendChild(newOptionImport);
        }
    }
    
    // Update Export Dropdown
    const exportSelect = document.getElementById('selectedBankSoal');
    if (exportSelect) {
        const newOptionExport = document.createElement('option');
        newOptionExport.value = newBankId;
        newOptionExport.setAttribute('data-name', newBankName);
        newOptionExport.setAttribute('data-count', soalCount || 0);
        newOptionExport.textContent = `${newBankName} (${soalCount || 0} soal)`;
        
        // Insert after the first option (placeholder)
        if (exportSelect.options.length > 1) {
            exportSelect.insertBefore(newOptionExport, exportSelect.options[1]);
        } else {
            exportSelect.appendChild(newOptionExport);
        }
    }
    
    // Update window.bankSoalList for consistency
    if (!window.bankSoalList) {
        window.bankSoalList = [];
    }
    window.bankSoalList.unshift({
        id: newBankId,
        nama: newBankName,
        jumlah_soal: soalCount || 0
    });
    
    console.log('Bank dropdowns refreshed with new bank:', newBankName);
}

// Remove Bank from Dropdowns when deleted
window.removeBankFromDropdowns = function(bankId) {
    console.log('=== Starting bank removal from dropdowns ===');
    console.log('Bank ID to remove:', bankId);
    
    let removedCount = 0;
    
    // Remove from Import Dropdown
    const importSelect = document.getElementById('selectedBankSoalImport');
    console.log('Import select found:', !!importSelect);
    if (importSelect) {
        // Find all options in this select
        const options = importSelect.querySelectorAll('option');
        console.log('Total options in import dropdown:', options.length);
        
        options.forEach(option => {
            if (option.value == bankId) {
                console.log('Found matching option in import dropdown:', option.textContent);
                option.remove();
                removedCount++;
            }
        });
    }
    
    // Remove from Export Dropdown
    const exportSelect = document.getElementById('selectedBankSoal');
    console.log('Export select found:', !!exportSelect);
    if (exportSelect) {
        // Find all options in this select
        const options = exportSelect.querySelectorAll('option');
        console.log('Total options in export dropdown:', options.length);
        
        options.forEach(option => {
            if (option.value == bankId) {
                console.log('Found matching option in export dropdown:', option.textContent);
                option.remove();
                removedCount++;
            }
        });
    }
    
    console.log('Total options removed:', removedCount);
    
    // Update window.bankSoalList
    if (window.bankSoalList) {
        const beforeLength = window.bankSoalList.length;
        window.bankSoalList = window.bankSoalList.filter(bank => bank.id != bankId);
        console.log(`Updated bankSoalList: ${beforeLength} → ${window.bankSoalList.length}`);
    }
    
    console.log('=== Bank removal completed ===');
}

// Update Dashboard Statistics Real-time
window.updateDashboardStats = function(type, change) {
    const ids = {
        'bank': 'stat-count-bank',
        'total': 'stat-count-total',
        'pg': 'stat-count-pg',
        'essay': 'stat-count-essay'
    };
    
    const element = document.getElementById(ids[type]);
    if (element) {
        let currentVal = parseInt(element.textContent) || 0;
        let newVal = currentVal + change;
        
        // Ensure non-negative
        if (newVal < 0) newVal = 0;
        
        // Animate change
        element.style.transform = 'scale(1.2)';
        element.style.color = '#3b82f6';
        element.style.transition = 'all 0.2s ease';
        
        setTimeout(() => {
            element.textContent = newVal;
            element.style.transform = 'scale(1)';
            element.style.color = '';
        }, 200);
        
        console.log(`Updated stat [${type}]: ${currentVal} -> ${newVal}`);
    }
}

// Delete Bank with Real-time Card Removal
window.deleteBank = function(bankId) {
    console.log('DELETE BANK CALLED with ID:', bankId, 'Type:', typeof bankId);
    
    showConfirmDelete(function() {
        console.log('User confirmed deletion, proceeding...');
        
        fetch(baseUrl + '/deleteBank', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + bankId
        })
        .then(res => res.json())
        .then(data => {
            console.log('Server response:', data);
            
            if (data.status === 'success') {
                showAlert('Bank soal berhasil dihapus!');
                
                // IMMEDIATELY remove from dropdowns FIRST
                console.log('Calling removeBankFromDropdowns with ID:', bankId);
                removeBankFromDropdowns(bankId);
                
                // Then remove card from DOM with animation
                const cardToRemove = document.getElementById('bank-card-' + bankId);
                if (cardToRemove) {
                    // Update stats before removing
                    updateDashboardStats('bank', -1);
                    
                    // Try to update total soal count
                    try {
                        const totalText = cardToRemove.querySelector('.bank-stat-item.total').textContent;
                        const totalSoal = parseInt(totalText.replace(/\D/g, '')) || 0;
                        if (totalSoal > 0) {
                            updateDashboardStats('total', -totalSoal);
                        }
                    } catch(e) { console.error('Error updating total stats:', e); }

                    // Add fade-out animation
                    cardToRemove.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    cardToRemove.style.opacity = '0';
                    cardToRemove.style.transform = 'scale(0.95)';
                    
                    // Remove after animation
                    setTimeout(() => {
                        cardToRemove.remove();
                        
                        // Check if grid is empty, show empty state
                        const grid = document.querySelector('.bank-grid');
                        if (grid && grid.children.length === 0) {
                            const emptyState = document.createElement('div');
                            emptyState.className = 'text-center py-5';
                            emptyState.innerHTML = `
                                <i class='bx bx-folder-open' style="font-size: 5rem; color: #cbd5e1;"></i>
                                <h4 class="mt-3 text-muted">Belum Ada Bank Soal</h4>
                                <p class="text-muted">Klik tombol "Buat Bank Soal Baru" untuk membuat bank soal pertama</p>
                            `;
                            grid.parentElement.insertBefore(emptyState, grid);
                        }
                    }, 300);
                }
                
            } else {
                showAlert(data.message || 'Gagal menghapus bank soal', false);
            }
        })
        .catch((err) => {
            console.error('Error deleting bank:', err);
            showAlert('Terjadi kesalahan', false);
        });
    }, 'Apakah Anda yakin ingin menghapus bank soal ini? Semua soal di dalamnya akan ikut terhapus.');
}

// Close Bank Detail
window.closeBankDetail = function() {
    window.currentBankId = null;
    window.currentBankSoal = [];
    document.getElementById('bankDetailView').classList.remove('active');
    document.getElementById('bankListView').classList.remove('hidden');
}

// Global Base URL for JS (avoid redeclaration if already defined)
if (typeof baseUrl === 'undefined') {
    var baseUrl = '<?= APP_URL ?>';
}

// Type Selector for Add Modal
document.querySelectorAll('#addSoalModal .type-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('#addSoalModal .type-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        const type = this.dataset.type;
        document.getElementById('soalType').value = type;
        
        // Show/hide pilihan ganda fields
        const isPG = type === 'pilihan_ganda';
        document.getElementById('pilihanContainer').style.display = isPG ? 'block' : 'none';
        document.getElementById('jawabanPGContainer').style.display = isPG ? 'block' : 'none';
        document.getElementById('jawabanEssayContainer').style.display = isPG ? 'none' : 'block';
        
        // Toggle required attributes
        document.querySelectorAll('#pilihanContainer input[name^="pilihan_"]').forEach((input, idx) => {
            if (idx < 4) input.required = isPG; // A, B, C, D required
        });
        document.querySelector('#jawabanPGContainer input[name="jawaban"]').required = isPG;
    });
});

// Type Selector for Edit Modal
document.querySelectorAll('#editSoalModal .type-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('#editSoalModal .type-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        const type = this.dataset.type;
        document.getElementById('editSoalType').value = type;
        
        // Show/hide pilihan ganda fields
        const isPG = type === 'pilihan_ganda';
        document.getElementById('editPilihanContainer').style.display = isPG ? 'block' : 'none';
        document.getElementById('editJawabanPGContainer').style.display = isPG ? 'block' : 'none';
        document.getElementById('editJawabanEssayContainer').style.display = isPG ? 'none' : 'block';
    });
});

// Filter Buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        window.filterSoal();
    });
});

// Search
document.getElementById('searchSoal').addEventListener('input', window.filterSoal);

window.filterSoal = function() {
    const search = document.getElementById('searchSoal').value.toLowerCase();
    const filter = document.querySelector('.filter-btn.active').dataset.filter;
    
    // Filter window.currentBankSoal and re-render
    const filtered = window.currentBankSoal.filter(soal => {
        const text = (soal.deskripsi || '').toLowerCase();
        const type = soal.status_soal || 'essay';
        const matchSearch = text.includes(search);
        const matchFilter = filter === 'all' || type === filter;
        return matchSearch && matchFilter;
    });
    
    window.renderSoalList(filtered);
}

// Edit Soal
window.editSoal = function(id) {
    const soal = window.currentBankSoal.find(s => s.id == id);
    if (soal) {
        document.getElementById('editSoalId').value = soal.id;
        document.getElementById('editDeskripsi').value = soal.deskripsi;
        document.getElementById('editSoalType').value = soal.status_soal;
        
        // Parse pilihan A, B, C, D, E dari string
        const pilihanArr = (soal.pilihan || '').split(',').map(p => p.trim());
        const pilihanMap = {};
        pilihanArr.forEach(p => {
            const match = p.match(/^([A-E])\.?\s*(.*)$/i);
            if (match) {
                pilihanMap[match[1].toUpperCase()] = match[2];
            }
        });
        
        document.getElementById('editPilihanA').value = pilihanMap['A'] || '';
        document.getElementById('editPilihanB').value = pilihanMap['B'] || '';
        document.getElementById('editPilihanC').value = pilihanMap['C'] || '';
        document.getElementById('editPilihanD').value = pilihanMap['D'] || '';
        document.getElementById('editPilihanE').value = pilihanMap['E'] || '';
        
        // Update type selector UI
        document.querySelectorAll('#editSoalModal .type-option').forEach(o => o.classList.remove('selected'));
        const isPG = soal.status_soal === 'pilihan_ganda';
        
        if (isPG) {
            document.getElementById('editTypePG').classList.add('selected');
            document.getElementById('editPilihanContainer').style.display = 'block';
            document.getElementById('editJawabanPGContainer').style.display = 'block';
            document.getElementById('editJawabanEssayContainer').style.display = 'none';
            
            // Set jawaban radio
            const jawaban = (soal.jawaban || '').toUpperCase().charAt(0);
            const radioBtn = document.getElementById('editJawab' + jawaban);
            if (radioBtn) radioBtn.checked = true;
        } else {
            document.getElementById('editTypeEssay').classList.add('selected');
            document.getElementById('editPilihanContainer').style.display = 'none';
            document.getElementById('editJawabanPGContainer').style.display = 'none';
            document.getElementById('editJawabanEssayContainer').style.display = 'block';
            document.getElementById('editJawabanEssay').value = soal.jawaban || '';
        }
        
        new bootstrap.Modal(document.getElementById('editSoalModal')).show();
    }
}

// Delete Soal
window.deleteSoal = function(id) {
    showConfirmDelete(function() {
        fetch(baseUrl + '/deletesoal', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                showAlert('Soal berhasil dihapus!');
                window.loadBankQuestions(window.currentBankId);
            } else {
                showAlert(data.message || 'Gagal menghapus soal', false);
            }
        })
        .catch(() => showAlert('Terjadi kesalahan', false));
    }, 'Apakah Anda yakin ingin menghapus soal ini?');
}

// Helper to robustly close modals
window.closeModal = function(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        // Try Bootstrap instance
        const instance = bootstrap.Modal.getInstance(modalEl);
        if (instance) {
            instance.hide();
        } else {
            // Fallback: create new instance and hide, or manually remove class
            new bootstrap.Modal(modalEl).hide();
        }
        
        // Force cleanup if backdrop persists
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 500); // Wait for animation
    }
}

// Form Submit - Add Soal
document.getElementById('addSoalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!window.currentBankId) {
        showAlert('Pilih bank soal terlebih dahulu!', false);
        return;
    }
    
    const formData = new FormData(this);
    formData.set('bank_id', window.currentBankId);
    
    // Combine pilihan A-E into single field
    const type = formData.get('status_soal');
    if (type === 'pilihan_ganda') {
        const pilihanA = formData.get('pilihan_a') || '';
        const pilihanB = formData.get('pilihan_b') || '';
        const pilihanC = formData.get('pilihan_c') || '';
        const pilihanD = formData.get('pilihan_d') || '';
        const pilihanE = formData.get('pilihan_e') || '';
        
        let pilihan = `A. ${pilihanA}, B. ${pilihanB}, C. ${pilihanC}, D. ${pilihanD}`;
        if (pilihanE) pilihan += `, E. ${pilihanE}`;
        
        formData.set('pilihan', pilihan);
    } else {
        formData.set('pilihan', '');
        formData.set('jawaban', formData.get('jawaban_essay') || '');
    }
    
    fetch(baseUrl + '/addingsoal', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            showAlert('Soal berhasil ditambahkan!');
            window.closeModal('addSoalModal');
            this.reset();
            window.loadBankQuestions(window.currentBankId);
            
            // Try updating stats if elements exist
            try {
                const totalEl = document.getElementById('stat-count-total');
                if (totalEl) totalEl.textContent = parseInt(totalEl.textContent || 0) + 1;
                
                if (type === 'pilihan_ganda') {
                     const pgEl = document.getElementById('stat-count-pg');
                     if (pgEl) pgEl.textContent = parseInt(pgEl.textContent || 0) + 1;
                } else {
                     const essayEl = document.getElementById('stat-count-essay');
                     if (essayEl) essayEl.textContent = parseInt(essayEl.textContent || 0) + 1;
                }
            } catch(e) { console.warn('Stats update failed:', e); }

        } else {
            showAlert(data.message || 'Gagal menambahkan soal', false);
        }
    })
    .catch(() => showAlert('Terjadi kesalahan', false));
});

// Form Submit - Edit Soal
document.getElementById('editSoalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    // Combine pilihan A-E into single field
    const type = formData.get('status_soal');
    if (type === 'pilihan_ganda') {
        const pilihanA = formData.get('pilihan_a') || '';
        const pilihanB = formData.get('pilihan_b') || '';
        const pilihanC = formData.get('pilihan_c') || '';
        const pilihanD = formData.get('pilihan_d') || '';
        const pilihanE = formData.get('pilihan_e') || '';
        
        let pilihan = `A. ${pilihanA}, B. ${pilihanB}, C. ${pilihanC}, D. ${pilihanD}`;
        if (pilihanE) pilihan += `, E. ${pilihanE}`;
        
        formData.set('pilihan', pilihan);
    } else {
        formData.set('pilihan', '');
        formData.set('jawaban', formData.get('jawaban_essay') || '');
    }
    
    fetch(baseUrl + '/updatesoal', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            showAlert('Soal berhasil diupdate!');
            window.closeModal('editSoalModal');
            window.loadBankQuestions(window.currentBankId);
        } else {
            showAlert(data.message || 'Gagal mengupdate soal', false);
        }
    })
    .catch(() => showAlert('Terjadi kesalahan', false));
});

// Form Submit - Create Bank
// Form Submit - Create Bank
document.getElementById('createBankForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const nama = formData.get('nama_bank');
    const deskripsi = formData.get('deskripsi_bank');
    const token = formData.get('token_bank');
    
    fetch(baseUrl + '/createBank', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nama=' + encodeURIComponent(nama) + 
              '&deskripsi=' + encodeURIComponent(deskripsi) +
              '&token=' + encodeURIComponent(token)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Bank soal berhasil dibuat!');
            bootstrap.Modal.getInstance(document.getElementById('createBankModal')).hide();
            
            // Remove empty state if exists
            const emptyState = document.querySelector('.bank-grid').previousElementSibling;
            if (emptyState && emptyState.classList.contains('text-center') && emptyState.textContent.includes('Belum Ada Bank Soal')) {
                emptyState.remove();
            }

            // Create new card HTML
            const newId = data.id || Date.now(); // Fallback if ID not returned
            const newCard = document.createElement('div');
            newCard.className = 'bank-card';
            newCard.id = 'bank-card-' + newId;
            newCard.innerHTML = `
                <div class="bank-card-header" onclick="openBankDetail(${newId}, '${escapeHtml(nama)}')">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="bank-icon">
                            <i class='bx bx-book-content'></i>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteBank(${newId})" title="Hapus Bank">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                    <h3 class="bank-title mt-2">${escapeHtml(nama)}</h3>
                    <p class="bank-desc">${escapeHtml(deskripsi)}</p>
                    <div class="bank-stats">
                        <span class="bank-stat-item total">
                            <i class='bx bx-file'></i> 0 Soal
                        </span>
                        <span class="bank-stat-item pg">
                            <i class='bx bx-key'></i> Token: <strong>${escapeHtml(token)}</strong>
                        </span>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div class="form-check form-switch cursor-pointer" onclick="event.stopPropagation()">
                            <input class="form-check-input" type="checkbox" id="activeSwitch_${newId}" 
                                onchange="window.activateBank(${newId})">
                            <label class="form-check-label small text-muted" for="activeSwitch_${newId}">
                                Tidak Aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bank-card-footer">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); window.editBankModal(${newId})" title="Edit Bank">
                            <i class='bx bx-edit'></i> Edit
                        </button>
                    </div>
                </div>
            `;
            
            // Append to grid
            const grid = document.querySelector('.bank-grid');
            if(grid) {
                grid.insertBefore(newCard, grid.firstChild);
            }
            
            // Refresh import/export dropdowns in real-time
            refreshBankDropdowns(newId, nama, 0);
            
            // Update Dashboard Statistics
            updateDashboardStats('bank', 1);
            
            this.reset();
        } else {
            showAlert(data.message || 'Gagal membuat bank soal', false);
        }
    })
    .catch(() => showAlert('Terjadi kesalahan', false));
});

// Bank Soal Selection for Import/Export
if (typeof window.selectedBankIdImport === 'undefined') window.selectedBankIdImport = null;
if (typeof window.selectedBankNameImport === 'undefined') window.selectedBankNameImport = '';
if (typeof window.selectedBankIdExport === 'undefined') window.selectedBankIdExport = null;
if (typeof window.selectedBankNameExport === 'undefined') window.selectedBankNameExport = '';

// Import Bank Selection
document.getElementById('selectedBankSoalImport').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    window.selectedBankIdImport = this.value;
    window.selectedBankNameImport = option.dataset.name || '';
    
    // Enable import button only if both bank and file are selected
    const fileInput = document.getElementById('importFile');
    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
    document.getElementById('btnImport').disabled = !hasFile;
    
    console.log('Import Bank Selected:', window.selectedBankNameImport, 'ID:', window.selectedBankIdImport);
});

// File Input Change Detection - Real-time update
document.getElementById('importFile').addEventListener('change', function() {
    const hasFile = this.files && this.files.length > 0;
    const hasBankSelected = window.selectedBankIdImport !== null && window.selectedBankIdImport !== '';
    
    // Enable button only if both file and bank are selected
    document.getElementById('btnImport').disabled = !(hasFile && hasBankSelected);
    
    // Log for debugging
    if (hasFile) {
        console.log('File selected:', this.files[0].name);
    } else {
        console.log('File cleared');
    }
});

// Export Bank Selection
document.getElementById('selectedBankSoal').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    window.selectedBankIdExport = this.value;
    window.selectedBankNameExport = option.dataset.name || '';
    const soalCount = parseInt(option.dataset.count) || 0;
    
    // Enable export button
    document.getElementById('btnExport').disabled = false;
    
    // Update export counts
    document.getElementById('exportTotalCount').textContent = soalCount;
    const pgCount = Math.floor(soalCount * 0.7);
    const essayCount = soalCount - pgCount;
    document.getElementById('exportPGCount').textContent = pgCount;
    document.getElementById('exportEssayCount').textContent = essayCount;
    
    console.log('Export Bank Selected:', window.selectedBankNameExport, 'ID:', window.selectedBankIdExport);
});

// Import Soal
window.importSoal = function() {
    if (!window.selectedBankIdImport) {
        showAlert('Pilih bank soal terlebih dahulu!', false);
        return;
    }
    
    const file = document.getElementById('importFile').files[0];
    if (!file) {
        showAlert('Pilih file terlebih dahulu', false);
        return;
    }
    
    // Client-side file type validation
    const allowedExtensions = ['csv', 'xls', 'xlsx'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!allowedExtensions.includes(fileExtension)) {
        showAlert(`Format file tidak didukung. Hanya menerima file CSV (.csv) atau Excel (.xls, .xlsx). File Anda: .${fileExtension}`, false);
        return;
    }
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('bank_id', window.selectedBankIdImport);
    formData.append('bank_name', window.selectedBankNameImport);
    
    // Show loading state on button only, no alert
    document.getElementById('btnImport').disabled = true;
    document.getElementById('btnImport').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengimport...';
    
    fetch(baseUrl + '/soal/import', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            showAlert('Import berhasil! ' + (data.count || 0) + ' soal ditambahkan.');
            // Reset form
            document.getElementById('importFile').value = '';
            document.getElementById('selectedBankSoalImport').value = '';
            window.selectedBankIdImport = null;
            document.getElementById('btnImport').disabled = true;
            document.getElementById('btnImport').innerHTML = "<i class='bx bx-upload me-2'></i>Import Soal ke Bank Terpilih";
            
            // Reload if current bank is the one we imported to
            if (window.currentBankId == window.selectedBankIdImport) {
                window.loadBankQuestions(window.currentBankId);
            }
            // Reload page to update counts
            setTimeout(() => location.reload(), 1500);
        } else {
            // Display validation errors if present
            if (data.validation_errors && data.validation_errors.length > 0) {
                let errorMessage = '<div style="text-align: left;"><strong>' + (data.message || 'Terdapat kesalahan pada file:') + '</strong><ul style="margin-top: 10px; padding-left: 20px;">';
                data.validation_errors.forEach(err => {
                    errorMessage += '<li style="margin-bottom: 5px;">' + err + '</li>';
                });
                errorMessage += '</ul><small style="color: #666;">Silakan perbaiki file dan coba lagi, atau download template yang benar.</small></div>';
                showAlert(errorMessage, false);
            } else {
                showAlert(data.message || 'Gagal mengimport soal', false);
            }
            document.getElementById('btnImport').disabled = false;
            document.getElementById('btnImport').innerHTML = "<i class='bx bx-upload me-2'></i>Import Soal ke Bank Terpilih";
        }
    })
    .catch(err => {
        console.error(err);
        showAlert('Terjadi kesalahan saat upload', false);
        document.getElementById('btnImport').disabled = false;
        document.getElementById('btnImport').innerHTML = "<i class='bx bx-upload me-2'></i>Import Soal ke Bank Terpilih";
    });
}

// Export Soal
// Export Soal
window.exportSoal = function() {
    if (!window.selectedBankIdExport) {
        showAlert('Pilih bank soal terlebih dahulu!', false);
        return;
    }
    
    // Trigger download via new endpoint
    window.location.href = baseUrl + `/soal/export?bank_id=${window.selectedBankIdExport}`;
}



// Edit Bank
// Open Edit Bank Modal
window.editBankModal = function(bankId) {
    const bank = window.bankSoalList.find(b => b.id == bankId);
    if (!bank) return;
    
    document.getElementById('editBankId').value = bank.id;
    document.getElementById('editBankName').value = bank.nama;
    document.getElementById('editBankDesc').value = bank.deskripsi || '';
    document.getElementById('editBankToken').value = bank.token || '';
    
    new bootstrap.Modal(document.getElementById('editBankModal')).show();
}

// Handler Edit Bank Submit
// Handler Edit Bank Submit
document.getElementById('editBankForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('id');
    const nama = formData.get('nama');
    const deskripsi = formData.get('deskripsi');
    const token = formData.get('token');
    
    fetch(baseUrl + '/updateBank', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&nama=${encodeURIComponent(nama)}&deskripsi=${encodeURIComponent(deskripsi)}&token=${encodeURIComponent(token)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Bank soal berhasil diupdate!');
            bootstrap.Modal.getInstance(document.getElementById('editBankModal')).hide();
            
            // Update DOM
            const card = document.getElementById('bank-card-' + id);
            if (card) {
                // Update Title
                const title = card.querySelector('.bank-title');
                if (title) title.textContent = nama;
                
                // Update Desc
                const desc = card.querySelector('.bank-desc');
                if (desc) desc.textContent = deskripsi;
                
                // Update Token
                const tokenEl = card.querySelector('.bank-stat-item.pg strong');
                if (tokenEl) tokenEl.textContent = token;
                
                // Update onclick handlers
                const header = card.querySelector('.bank-card-header');
                if (header) header.setAttribute('onclick', `openBankDetail(${id}, '${escapeHtml(nama)}')`);
            }
        } else {
            showAlert(data.message || 'Gagal mengupdate bank soal', false);
        }
    })
    .catch(() => showAlert('Terjadi kesalahan', false));
});

// Activate Bank
// Activate Bank
window.activateBank = function(id) {
    // Optimistic UI Update first
    const checkbox = document.getElementById('activeSwitch_' + id);
    const label = checkbox.nextElementSibling;
    const wasChecked = !checkbox.checked; // Before click state
    
    fetch(baseUrl + '/exam/activateBank', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Update UI on success (already toggled by user click, just update label)
            if (checkbox.checked) {
                label.textContent = 'Aktif';
                label.classList.remove('text-muted');
                label.classList.add('text-primary', 'fw-bold');
            } else {
                label.textContent = 'Tidak Aktif';
                label.classList.remove('text-primary', 'fw-bold');
                label.classList.add('text-muted');
            }
        } else {
            showAlert(data.message || 'Gagal mengaktifkan bank soal', false);
            // Revert on failure
            checkbox.checked = wasChecked;
        }
    })
    .catch(() => {
        showAlert('Terjadi kesalahan', false);
        // Revert on failure
        checkbox.checked = wasChecked;
    });
}

// Delete Bank
// Delete Bank


// Reload page when switching to Import/Export tab to refresh dropdown
document.addEventListener('DOMContentLoaded', function() {
    const importExportTab = document.querySelector('a[href="#tabImportExport"]');
    if (importExportTab) {
        importExportTab.addEventListener('shown.bs.tab', function (event) {
            console.log('Import/Export tab shown, reloading page...');
            // Reload page to get fresh data from server
            location.reload();
        });
    }
});
</script>
</main>
