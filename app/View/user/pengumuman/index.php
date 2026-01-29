<?php
/**
 * Pengumuman View
 */
?>

<!-- Page Header -->
<?php
    $title = 'Pengumuman';
    $subtitle = 'Informasi penting terkait seleksi calon asisten';
    $icon = 'bx bx-notification';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">
    <div class="card border-0 shadow-sm rounded-4 text-center">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 text-primary fw-bold">
                <i class='bx bx-bell'></i> Pengumuman
            </h5>
        </div>
        <div class="card-body p-5">
            <h5 class="card-title fs-4 fw-bold mb-3">Special title treatment</h5>
            <p class="card-text text-muted lh-lg">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
            <a href="#" class="btn btn-primary rounded-3 px-4 py-2" data-page="dashboard">
                <i class='bx bx-arrow-back'></i> Kembali ke Dashboard
            </a>
        </div>
        <div class="card-footer text-white py-3" style="background: var(--bs-primary);">
            <i class='bx bx-time'></i> 2 days ago
        </div>
    </div>
</div>
