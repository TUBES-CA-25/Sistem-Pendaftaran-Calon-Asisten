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
.bank-soal-wrapper {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
    min-height: calc(100vh - 60px);
    margin: 0;
    padding: 0;
}

/* Force full width by removing parent padding */
.main-content {
    padding: 0 !important;
}

/* ==================== HEADER STYLES ==================== */
.page-header {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
    padding: 0 2rem 3.5rem 2rem;
    margin: 0;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.page-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 5%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.header-content {
    position: relative;
    z-index: 1;
    padding-top: 3.5rem;
    max-width: 100%;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.25rem;
}

.header-subtitle {
    color: rgba(255,255,255,0.8);
    font-size: 0.95rem;
    margin: 0;
}

.header-stats {
    display: flex;
    gap: 1rem;
}

.stat-badge {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    text-align: center;
    min-width: 100px;
}

.stat-badge .stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    display: block;
}

.stat-badge .stat-label {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ==================== NAVIGATION TABS ==================== */
.nav-tabs-custom {
    background: white;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-tabs-custom .nav-link {
    color: #64748b;
    font-weight: 500;
    padding: 0.875rem 1.5rem;
    border-radius: 10px;
    margin-right: 0.5rem;
    transition: all 0.2s;
    border: none;
}

.nav-tabs-custom .nav-link:hover {
    background: #f1f5f9;
    color: #2563eb;
}

.nav-tabs-custom .nav-link.active {
    background: #2563eb;
    color: white;
}

.nav-tabs-custom .nav-link i {
    margin-right: 0.5rem;
}

/* ==================== BANK SOAL CARDS ==================== */
.bank-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.bank-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: 2px solid transparent;
}

.bank-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.2);
    border-color: #2563eb;
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
    background: linear-gradient(90deg, #2563eb, #7c3aed);
}

.bank-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: #2563eb;
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
    line-height: 1.5;
    margin-bottom: 1rem;
}

.bank-stats {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.bank-stat-item {
    background: #f8fafc;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
}

.bank-stat-item i {
    margin-right: 0.375rem;
}

.bank-stat-item.pg {
    color: #2563eb;
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
    background: #2563eb;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
}

.bank-action-btn:hover {
    background: #1d4ed8;
    transform: scale(1.05);
}

/* ==================== CREATE BANK CARD ==================== */
.create-bank-card {
    background: white;
    border: 2px dashed #cbd5e1;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    cursor: pointer;
    transition: all 0.3s;
    min-height: 280px;
}

.create-bank-card:hover {
    border-color: #2563eb;
    background: #f8fafc;
}

.create-bank-card i {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.create-bank-card:hover i {
    color: #2563eb;
}

.create-bank-card span {
    font-weight: 600;
    color: #64748b;
}

/* ==================== DETAIL VIEW ==================== */
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
    border-radius: 8px;
    transition: all 0.2s;
}

.back-btn:hover {
    background: #f1f5f9;
    color: #2563eb;
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

/* ==================== SOAL LIST ==================== */
.soal-container {
    padding: 1.5rem;
}

.soal-filter-bar {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.search-input-group input:focus {
    outline: none;
    border-color: #2563eb;
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
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn:hover, .filter-btn.active {
    border-color: #2563eb;
    color: #2563eb;
    background: #eff6ff;
}

/* ==================== SOAL CARD ==================== */
.soal-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.soal-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border-left: 4px solid #2563eb;
    transition: all 0.2s;
}

.soal-card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
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
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    border-radius: 12px;
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
    color: #2563eb;
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
    border-radius: 10px;
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
    border-radius: 10px;
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
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.soal-action-btn.edit {
    background: #eff6ff;
    color: #2563eb;
}

.soal-action-btn.edit:hover {
    background: #2563eb;
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

/* ==================== EMPTY STATE ==================== */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
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

/* ==================== MODAL STYLES ==================== */
.modal-custom .modal-content {
    border: none;
    border-radius: 20px;
    overflow: hidden;
}

.modal-custom .modal-header {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
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
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.2s;
}

.modal-custom .form-control:focus {
    border-color: #2563eb;
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
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

.type-option:hover {
    border-color: #2563eb;
}

.type-option.selected {
    border-color: #2563eb;
    background: #eff6ff;
}

.type-option i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

.type-option.pg i { color: #2563eb; }
.type-option.essay i { color: #d97706; }

.type-option span {
    font-weight: 600;
    color: #374151;
}

/* ==================== BUTTONS ==================== */
.btn-primary-custom {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    color: white;
    transition: all 0.2s;
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
    border-radius: 10px;
    font-weight: 600;
    color: white;
}

.btn-outline-custom {
    border: 2px solid #e2e8f0;
    background: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    color: #64748b;
}

.btn-outline-custom:hover {
    border-color: #2563eb;
    color: #2563eb;
}

/* ==================== PILIHAN A-E STYLES ==================== */
.pilihan-fields .input-group {
    border-radius: 10px;
    overflow: hidden;
}

.pilihan-fields .option-label {
    width: 45px;
    justify-content: center;
    font-weight: 700;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
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
    border-color: #2563eb;
    box-shadow: none;
}

.jawaban-selector {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
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
    background-color: #2563eb;
    border-color: #2563eb;
}

.jawaban-selector .form-check-label {
    font-weight: 600;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid #e5e7eb;
}

.jawaban-selector .form-check-input:checked + .form-check-label {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}
</style>

<div class="bank-soal-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content d-flex justify-content-between align-items-center flex-wrap gap-4" style="padding-left: 2rem; padding-right: 2rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon">
                        <i class='bx bx-library'></i>
                    </div>
                    <div>
                        <h1 class="header-title">Bank Soal Ujian</h1>
                        <p class="header-subtitle">Kelola bank soal untuk tes tertulis calon asisten laboratorium</p>
                    </div>
                </div>
                <div class="header-stats">
                    <div class="stat-badge">
                        <span class="stat-number"><?= count($bankSoalList) ?></span>
                        <span class="stat-label">Bank Soal</span>
                    </div>
                    <div class="stat-badge">
                        <span class="stat-number"><?= count($allSoal) ?></span>
                        <span class="stat-label">Total Soal</span>
                    </div>
                    <div class="stat-badge">
                        <span class="stat-number"><?= $pgCount ?></span>
                        <span class="stat-label">Pilihan Ganda</span>
                    </div>
                    <div class="stat-badge">
                        <span class="stat-number"><?= $essayCount ?></span>
                        <span class="stat-label">Essay</span>
                    </div>
                </div>
        </div>
    </div>

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
                    <div class="bank-card">
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
                                    <i class='bx bx-list-check'></i> <?= $bank['pg_count'] ?? 0 ?> PG
                                </span>
                                <span class="bank-stat-item essay">
                                    <i class='bx bx-edit-alt'></i> <?= $bank['essay_count'] ?? 0 ?> Essay
                                </span>
                            </div>
                        </div>
                        <div class="bank-card-footer" onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama']) ?>')">
                            <span class="bank-date">
                                <i class='bx bx-calendar'></i> <?= date('d M Y', strtotime($bank['created_at'])) ?>
                            </span>
                            <span class="bank-action-btn">
                                Lihat Soal <i class='bx bx-chevron-right'></i>
                            </span>
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
                <!-- Pilih Bank Soal -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card" style="border:none; border-radius:16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class='bx bx-folder-open me-2 text-primary'></i>Pilih Bank Soal</h5>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <select class="form-select form-select-lg" id="selectedBankSoal" style="border-radius:10px; border:2px solid #e2e8f0;">
                                            <option value="" disabled selected>-- Pilih Bank Soal --</option>
                                            <?php foreach ($bankSoalList as $bank): ?>
                                            <option value="<?= $bank['id'] ?>" data-name="<?= htmlspecialchars($bank['nama']) ?>" data-count="<?= $bank['jumlah_soal'] ?>">
                                                <?= htmlspecialchars($bank['nama']) ?> (<?= $bank['jumlah_soal'] ?> soal)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="selected-bank-info mt-3 mt-md-0" id="selectedBankInfo" style="display:none;">
                                            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                                <i class='bx bx-check-circle text-success fs-4'></i>
                                                <div>
                                                    <small class="text-muted d-block">Bank Terpilih</small>
                                                    <strong id="selectedBankName">-</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100" style="border:none; border-radius:20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-body text-center p-5">
                                <div class="mb-4">
                                    <i class='bx bx-upload' style="font-size:4rem; color:#2563eb;"></i>
                                </div>
                                <h4 class="fw-bold mb-2">Import Soal</h4>
                                <p class="text-muted mb-3">Upload file Excel atau CSV untuk menambahkan soal ke bank yang dipilih</p>
                                <div class="alert alert-info small text-start mb-3" style="border-radius:10px;">
                                    <i class='bx bx-info-circle me-1'></i>
                                    <strong>Format file:</strong> Kolom harus berisi: Deskripsi, Tipe (PG/Essay), Pilihan A, B, C, D, E, Jawaban
                                </div>
                                <input type="file" class="form-control mb-3" id="importFile" accept=".xlsx,.xls,.csv" style="border-radius:10px;">
                                <button class="btn btn-primary-custom w-100" onclick="importSoal()" id="btnImport" disabled>
                                    <i class='bx bx-upload me-1'></i> Import Soal ke Bank Terpilih
                                </button>
                                <div class="mt-3">
                                    <a href="#" class="text-decoration-none small text-primary" onclick="downloadTemplate()">
                                        <i class='bx bx-download'></i> Download Template Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100" style="border:none; border-radius:20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-body text-center p-5">
                                <div class="mb-4">
                                    <i class='bx bx-download' style="font-size:4rem; color:#059669;"></i>
                                </div>
                                <h4 class="fw-bold mb-2">Export Soal</h4>
                                <p class="text-muted mb-3">Download soal dari bank yang dipilih dalam format Excel</p>
                                <div class="export-info mb-4 p-3 bg-light rounded-3" id="exportInfo">
                                    <div class="d-flex justify-content-center gap-4">
                                        <div class="text-center">
                                            <div class="fs-3 fw-bold text-primary" id="exportTotalCount">0</div>
                                            <small class="text-muted">Total Soal</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="fs-3 fw-bold text-info" id="exportPGCount">0</div>
                                            <small class="text-muted">Pilihan Ganda</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="fs-3 fw-bold text-warning" id="exportEssayCount">0</div>
                                            <small class="text-muted">Essay</small>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-success-custom w-100" onclick="exportSoal()" id="btnExport" disabled>
                                    <i class='bx bx-download me-1'></i> Export dari Bank Terpilih
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

// Close Bank Detail
window.closeBankDetail = function() {
    window.currentBankId = null;
    window.currentBankSoal = [];
    document.getElementById('bankDetailView').classList.remove('active');
    document.getElementById('bankListView').classList.remove('hidden');
}

// Global Base URL for JS
const baseUrl = '<?= APP_URL ?>';

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
    if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
        fetch(baseUrl + '/deletesoal', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                alert('Soal berhasil dihapus!');
                window.loadBankQuestions(window.currentBankId);
            } else {
                alert(data.message || 'Gagal menghapus soal');
            }
        })
        .catch(() => alert('Terjadi kesalahan'));
    }
}

// Form Submit - Add Soal
document.getElementById('addSoalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!window.currentBankId) {
        alert('Pilih bank soal terlebih dahulu!');
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
            alert('Soal berhasil ditambahkan!');
            bootstrap.Modal.getInstance(document.getElementById('addSoalModal')).hide();
            this.reset();
            window.loadBankQuestions(window.currentBankId);
        } else {
            alert(data.message || 'Gagal menambahkan soal');
        }
    })
    .catch(() => alert('Terjadi kesalahan'));
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
            alert('Soal berhasil diupdate!');
            bootstrap.Modal.getInstance(document.getElementById('editSoalModal')).hide();
            window.loadBankQuestions(window.currentBankId);
        } else {
            alert(data.message || 'Gagal mengupdate soal');
        }
    })
    .catch(() => alert('Terjadi kesalahan'));
});

// Form Submit - Create Bank
document.getElementById('createBankForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(baseUrl + '/createBank', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nama=' + encodeURIComponent(formData.get('nama_bank')) + '&deskripsi=' + encodeURIComponent(formData.get('deskripsi_bank'))
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Bank soal berhasil dibuat!');
            bootstrap.Modal.getInstance(document.getElementById('createBankModal')).hide();
            location.reload();
        } else {
            alert(data.message || 'Gagal membuat bank soal');
        }
    })
    .catch(() => alert('Terjadi kesalahan'));
});

// Bank Soal Selection for Import/Export
if (typeof window.selectedBankId === 'undefined') window.selectedBankId = null;
if (typeof window.selectedBankName === 'undefined') window.selectedBankName = '';

document.getElementById('selectedBankSoal').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    window.selectedBankId = this.value;
    window.selectedBankName = option.dataset.name || '';
    const soalCount = parseInt(option.dataset.count) || 0;
    
    // Show selected bank info
    document.getElementById('selectedBankInfo').style.display = 'block';
    document.getElementById('selectedBankName').textContent = window.selectedBankName;
    
    // Enable import/export buttons
    document.getElementById('btnImport').disabled = false;
    document.getElementById('btnExport').disabled = false;
    
    // Update export counts (simulasi - nanti bisa dari data real)
    document.getElementById('exportTotalCount').textContent = soalCount;
    document.getElementById('exportPGCount').textContent = <?= $pgCount ?>;
    document.getElementById('exportEssayCount').textContent = <?= $essayCount ?>;
});

// Import Soal
window.importSoal = function() {
    if (!window.selectedBankId) {
        alert('Pilih bank soal terlebih dahulu!');
        return;
    }
    
    const file = document.getElementById('importFile').files[0];
    if (!file) {
        alert('Pilih file terlebih dahulu');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('bank_id', window.selectedBankId);
    formData.append('bank_name', window.selectedBankName);
    
    alert(`Import soal ke bank "${window.selectedBankName}" sedang dalam pengembangan.\nFile: ${file.name}`);
    
    // TODO: Implement actual import
    // fetch(baseUrl + '/importsoal', {
    //     method: 'POST',
    //     body: formData
    // })
    // .then(res => res.json())
    // .then(data => {
    //     if (data.success) {
    //         alert('Import berhasil! ' + data.count + ' soal ditambahkan.');
    //         location.reload();
    //     }
    // });
}

// Export Soal
window.exportSoal = function() {
    if (!window.selectedBankId) {
        alert('Pilih bank soal terlebih dahulu!');
        return;
    }
    
    alert(`Export soal dari bank "${window.selectedBankName}" sedang dalam pengembangan.`);
    
    // TODO: Implement actual export
    // window.location.href = baseUrl + `/exportsoal?bank_id=${window.selectedBankId}`;
}

// Download Template
window.downloadTemplate = function() {
    alert('Download template Excel sedang dalam pengembangan.\n\nFormat kolom:\n- Deskripsi (pertanyaan)\n- Tipe (pilihan_ganda / essay)\n- Pilihan A\n- Pilihan B\n- Pilihan C\n- Pilihan D\n- Pilihan E (opsional)\n- Jawaban');
}

// Edit Bank
window.editBank = function() {
    if (!window.currentBankId) {
        alert('Tidak ada bank yang dipilih');
        return;
    }
    
    const bank = window.bankSoalList.find(b => b.id == window.currentBankId);
    if (bank) {
        const newName = prompt('Nama Bank Soal:', bank.nama);
        if (newName && newName.trim()) {
            const newDesc = prompt('Deskripsi:', bank.deskripsi || '');
            
            fetch(baseUrl + '/updateBank', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${window.currentBankId}&nama=${encodeURIComponent(newName)}&deskripsi=${encodeURIComponent(newDesc || '')}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Bank soal berhasil diupdate!');
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengupdate bank soal');
                }
            })
            .catch(() => alert('Terjadi kesalahan'));
        }
    }
}

// Delete Bank
window.deleteBank = function(bankId) {
    if (confirm('Apakah Anda yakin ingin menghapus bank soal ini?\nSemua soal di dalam bank ini juga akan dihapus!')) {
        fetch(baseUrl + '/deleteBank', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + bankId
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Bank soal berhasil dihapus!');
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus bank soal');
            }
        })
        .catch(() => alert('Terjadi kesalahan'));
    }
}
</script>
