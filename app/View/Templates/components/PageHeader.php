<?php
/**
 * Reusable Page Header Component
 * 
 * Variables yang diperlukan:
 * @var string $title - Judul halaman (wajib)
 * @var string $subtitle - Subjudul halaman (opsional)
 * @var string $icon - Class icon (opsional, contoh: 'bx bx-library' atau 'bi bi-people')
 * @var string $headerRightContent - Konten tambahan di kanan header (opsional, untuk stats dll)
 */
?>
<style>
/* ==================== UNIFIED HEADER STYLES ==================== */
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
    flex-shrink: 0;
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
    flex-wrap: wrap;
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
</style>

<div class="page-header">
    <div class="header-content d-flex justify-content-between align-items-center flex-wrap gap-4" style="padding-left: 2rem; padding-right: 2rem;">
        <div class="d-flex align-items-center gap-3">
            <?php if (!empty($icon)): ?>
                <div class="header-icon">
                    <i class='<?= $icon ?>'></i>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="header-title"><?= $title ?? 'Judul Halaman' ?></h1>
                <?php if (!empty($subtitle)): ?>
                    <p class="header-subtitle"><?= $subtitle ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($headerRightContent)): ?>
            <div class="header-right-content">
                <?= $headerRightContent ?>
            </div>
        <?php endif; ?>
    </div>
</div>
