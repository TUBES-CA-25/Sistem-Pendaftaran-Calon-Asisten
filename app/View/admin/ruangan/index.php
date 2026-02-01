<?php
/**
 * Ruangan View - Card Layout with Details & Interview
 * 
 * @var array $ruanganList - Daftar ruangan
 */
$ruanganList = $ruanganList ?? [];
?>

<main>


    <!-- SECTION: LIST VIEW -->
    <div id="ruanganListSection">
        <?php
            $title = 'Ruangan Seleksi';
            $subtitle = 'Kelola data ruangan untuk kegiatan seleksi calon asisten';
            $icon = 'bi bi-buildings-fill';
            require_once __DIR__ . '/../../templates/components/PageHeader.php';
        ?>

        <div class="container-fluid px-4 mt-3 pb-3">
            <!-- Controls Toolbar -->
            <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3 bg-white">
                    <div class="position-relative flex-grow-1" style="max-width: 400px;">
                        <i class="bi bi-search position-absolute text-muted" style="left: 18px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                        <input type="text" id="searchInput" class="form-control form-control-lg bg-light border-0 ps-5 fs-6" placeholder="Cari ruangan..." style="border-radius: 12px;">
                    </div>
                    <button class="btn btn-lg px-4 fs-6 fw-semibold shadow d-flex align-items-center gap-2 btn-add-room" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Tambah Ruangan</span>
                    </button>
                </div>
            </div>

            <!-- Grid -->
            <div class="row g-3" id="ruanganGrid">
                <?php if (empty($ruanganList)) { ?>
                    <div class="col-12 py-5">
                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center mx-auto" style="max-width: 500px;">
                             <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-primary" style="width: 80px; height: 80px;">
                                <i class="bi bi-buildings fs-1"></i>
                             </div>
                             <h4 class="fw-bold text-dark">Belum ada Ruangan</h4>
                             <p class="text-muted mb-4">Mulai dengan menambahkan ruangan baru untuk seleksi.</p>
                             <div>
                                 <button class="btn btn-add-room rounded-pill px-5 py-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                                    <i class="bi bi-plus-circle-fill me-2"></i> Tambah Sekarang
                                 </button>
                             </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php foreach ($ruanganList as $ruangan) { ?>
                        <div class="col-md-6 col-lg-4 col-xl-3 room-item">
                            <div class="card h-100 border-0 shadow room-card position-relative"
                                data-id="<?= $ruangan['id'] ?>"
                                data-name="<?= htmlspecialchars($ruangan['nama']) ?>"
                                style="border-radius: 16px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden;">

                                <!-- Gradient Header Bar -->
                                <div class="position-absolute top-0 start-0 w-100"
                                     style="height: 5px; background: linear-gradient(135deg, #3dc2ec 0%, #2563eb 100%);"></div>

                                <!-- Card Body -->
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Icon -->
                                    <div class="d-flex justify-content-center mb-3 mt-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center room-icon-container"
                                             style="width: 90px; height: 90px; background: linear-gradient(135deg, rgba(61, 194, 236, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%); transition: all 0.3s ease;">
                                            <i class="bi bi-buildings-fill text-primary" style="font-size: 2.5rem; transition: all 0.3s ease;"></i>
                                        </div>
                                    </div>

                                    <!-- Room Info -->
                                    <div class="text-center mb-3 flex-grow-1">
                                        <h5 class="fw-bold text-dark mb-2 room-name" style="font-size: 1.15rem; letter-spacing: 0.3px;">
                                            <?= htmlspecialchars($ruangan['nama']) ?>
                                        </h5>
                                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill"
                                             style="background-color: rgba(61, 194, 236, 0.08);">
                                            <i class="bi bi-geo-alt-fill text-primary" style="font-size: 0.75rem;"></i>
                                            <span class="text-muted small fw-medium">Ruangan Seleksi</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-auto">
                                        <button class="btn btn-outline-primary btn-sm flex-fill fw-medium btn-edit-room py-2"
                                                data-id="<?= $ruangan['id'] ?>"
                                                data-name="<?= htmlspecialchars($ruangan['nama']) ?>"
                                                title="Ubah Nama"
                                                style="border-radius: 10px; border-width: 1.5px; transition: all 0.2s ease;">
                                            <i class="bi bi-pencil-square me-1"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm flex-fill fw-medium btn-delete-room py-2"
                                                data-id="<?= $ruangan['id'] ?>"
                                                title="Hapus"
                                                style="border-radius: 10px; border-width: 1.5px; transition: all 0.2s ease;">
                                            <i class="bi bi-trash me-1"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

</main>

<!-- Modals -->
<div class="modal fade" id="tambahRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Ruangan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form id="tambahRuanganForm">
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary">Nama Ruangan</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="namaRuangan" name="namaRuangan" placeholder="Contoh: Lab RPL 1" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">Simpan Ruangan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Ubah Nama Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form id="updateRuanganForm">
                    <input type="hidden" id="updateRuanganId">
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary">Nama Ruangan</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="updateNamaRuangan" name="updateNamaRuangan" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Confirmation Modal -->


<!-- Custom Room Card Styles -->
<style>
/* Add Room Button Styling */
.btn-add-room {
    background: linear-gradient(135deg, #3dc2ec 0%, #2563eb 100%);
    border: none;
    border-radius: 12px;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-add-room::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
    transition: left 0.5s ease;
}

.btn-add-room:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(61, 194, 236, 0.4) !important;
    background: linear-gradient(135deg, #2ab5d9 0%, #1e4fd4 100%);
}

.btn-add-room:hover::before {
    left: 100%;
}

.btn-add-room:active {
    transform: translateY(0);
}

.btn-add-room i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.btn-add-room:hover i {
    transform: rotate(90deg);
}

/* Room Card Hover Effects - Disabled click functionality */
.room-card {
    cursor: default;
    transform-origin: center;
}

.room-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(61, 194, 236, 0.15) !important;
}

.room-card:hover .room-icon-container {
    background: linear-gradient(135deg, rgba(61, 194, 236, 0.15) 0%, rgba(37, 99, 235, 0.2) 100%) !important;
    transform: scale(1.02);
}

.room-card:hover .room-icon-container i {
    transform: scale(1.05);
}

.room-card:hover .room-name {
    color: #1e40af !important;
}

/* Button Hover Effects */
.btn-edit-room:hover {
    background-color: #3dc2ec !important;
    color: white !important;
    border-color: #3dc2ec !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(61, 194, 236, 0.3);
}

.btn-delete-room:hover {
    background-color: #dc3545 !important;
    color: white !important;
    border-color: #dc3545 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

/* Prevent button hover from triggering card hover */
.btn-edit-room, .btn-delete-room {
    z-index: 10;
    position: relative;
}

/* Card Footer Animation */
.room-card:hover .card-footer small {
    gap: 0.75rem !important;
}

.room-card:hover .card-footer .bi-arrow-right {
    transform: translateX(4px);
    transition: transform 0.3s ease;
}

.card-footer .bi-arrow-right {
    transition: transform 0.3s ease;
}

/* Empty State Styling */
.room-item:empty {
    display: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .room-card {
        margin-bottom: 1rem;
    }
}

/* Smooth entrance animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.room-item {
    animation: fadeInUp 0.5s ease-out;
}

.room-item:nth-child(1) { animation-delay: 0.05s; }
.room-item:nth-child(2) { animation-delay: 0.1s; }
.room-item:nth-child(3) { animation-delay: 0.15s; }
.room-item:nth-child(4) { animation-delay: 0.2s; }
.room-item:nth-child(5) { animation-delay: 0.25s; }
.room-item:nth-child(6) { animation-delay: 0.3s; }
</style>

<!-- Load Custom Script -->
<script src="<?= APP_URL ?>/Assets/js/rooms.js?v=2.0"></script>