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
            $title = 'Ruangan Praktikum';
            $subtitle = 'Kelola data ruangan, peserta, dan aktivitas praktikum';
            $icon = 'bi bi-buildings-fill';
            require_once __DIR__ . '/../../templates/components/PageHeader.php';
        ?>

        <div class="container-fluid px-4 pb-4">
            <!-- Controls Toolbar -->
            <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3 bg-white">
                    <div class="position-relative flex-grow-1" style="max-width: 400px;">
                        <i class="bi bi-search position-absolute text-muted" style="left: 18px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                        <input type="text" id="searchInput" class="form-control form-control-lg bg-light border-0 ps-5 fs-6" placeholder="Cari ruangan..." style="border-radius: 12px;">
                    </div>
                    <button class="btn btn-primary btn-lg px-4 fs-6 fw-semibold shadow-sm d-flex align-items-center gap-2" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Ruangan</span>
                    </button>
                </div>
            </div>

            <!-- Grid -->
            <div class="row g-4" id="ruanganGrid">
                <?php if (empty($ruanganList)) { ?>
                    <div class="col-12 py-5">
                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center mx-auto" style="max-width: 500px;">
                             <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-primary" style="width: 80px; height: 80px;">
                                <i class="bi bi-buildings fs-1"></i>
                             </div>
                             <h4 class="fw-bold text-dark">Belum ada Ruangan</h4>
                             <p class="text-muted mb-4">Mulai dengan menambahkan ruangan baru untuk praktikum.</p>
                             <div>
                                 <button class="btn btn-outline-primary rounded-pill px-4 fw-medium" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Sekarang
                                 </button>
                             </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php foreach ($ruanganList as $ruangan) { ?>
                        <div class="col-md-6 col-lg-4 col-xl-3 room-item">
                            <div class="card h-100 border-0 shadow-sm hover-elevate rounded-4 overflow-hidden room-card cursor-pointer position-relative group-hover" 
                                data-id="<?= $ruangan['id'] ?>" 
                                data-name="<?= htmlspecialchars($ruangan['nama']) ?>"
                                style="transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);">
                                
                                <!-- Decorative Header -->
                                <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background: linear-gradient(90deg, #3dc2ec 0%, #2563eb 100%);"></div>

                                <div class="card-body p-4 pt-5 d-flex flex-column align-items-center text-center">
                                    <!-- Icon Container -->
                                    <div class="mb-4 position-relative">
                                        <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; transition: all 0.3s ease;">
                                            <i class="bi bi-buildings-fill fs-1"></i>
                                        </div>
                                        <div class="position-absolute bottom-0 end-0 p-1 bg-white rounded-circle">
                                             <div class="bg-success rounded-circle" style="width: 12px; height: 12px; border: 2px solid white;"></div>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <h5 class="fw-bold text-dark mb-1 text-uppercase ls-1"><?= htmlspecialchars($ruangan['nama']) ?></h5>
                                    <p class="text-muted small mb-4">Ruangan Praktikum</p>

                                    <!-- Action Divider -->
                                    <div class="w-100 border-top border-light mb-auto"></div>

                                    <!-- Buttons (Always Visible but styled) -->
                                     <!-- Buttons (Always Visible but styled) -->
                                    <div class="d-flex gap-2 mt-3 w-100 justify-content-center">
                                         <button class="btn btn-light text-primary btn-sm px-3 py-2 rounded-3 flex-grow-1 fw-medium btn-edit-room d-flex align-items-center justify-content-center gap-2"
                                            data-id="<?= $ruangan['id'] ?>" 
                                            data-name="<?= htmlspecialchars($ruangan['nama']) ?>" 
                                            title="Ubah Nama">
                                            <i class="bi bi-pencil-square"></i> <span class="small">Ubah</span>
                                        </button>
                                        <button class="btn btn-light text-danger btn-sm px-3 py-2 rounded-3 flex-grow-1 fw-medium btn-delete-room d-flex align-items-center justify-content-center gap-2"
                                            data-id="<?= $ruangan['id'] ?>" 
                                            title="Hapus">
                                            <i class="bi bi-trash"></i> <span class="small">Hapus</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-0 py-2 text-center">
                                     <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Klik untuk detail peserta <i class="bi bi-arrow-right ms-1"></i></small>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

</main>

<!-- SECTION: DETAIL VIEW (Outside main to avoid padding) -->
<div id="ruanganDetailSection" class="d-none" style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background: white; z-index: 1000; overflow-y: auto;">
    
    <!-- Simple Clean Header -->
    <div class="bg-white border-bottom shadow-sm">
        <div class="container-fluid p-0">
            <!-- Back Button & Title -->
            <div class="d-flex justify-content-between align-items-center px-4 py-4">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light rounded-circle" id="backToListBtn" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <div>
                        <h3 class="fw-bold text-dark mb-1" id="detailRoomTitle">Nama Ruangan</h3>
                        <p class="text-muted small mb-0">Monitor peserta di ruangan ini</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="editRoomBtn" data-id="">
                        <i class="bi bi-pencil me-2"></i>Edit Nama
                    </button>
                    <button class="btn btn-outline-danger" id="deleteRoomBtn" data-id="">
                        <i class="bi bi-trash me-2"></i>Hapus Ruangan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 py-4">
        
        <!-- Simple Table Card -->
        <div class="card border-0 shadow-sm">
            
            <!-- Tabs -->
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs border-0 card-header-tabs" id="roomTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="pills-testulis-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab" type="button">
                            <i class="bi bi-file-text me-2"></i>Tes Tulis
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="pills-presentasi-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab" type="button">
                            <i class="bi bi-easel me-2"></i>Presentasi
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="pills-wawancara-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab" type="button">
                            <i class="bi bi-chat-text me-2"></i>Wawancara
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Toolbar -->
            <div class="card-body border-bottom bg-light">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="position-relative">
                             <i class="bi bi-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                             <input type="text" id="searchParticipants" class="form-control ps-5" placeholder="Cari mahasiswa di ruangan ini...">
                        </div>
                    </div>
                    <!-- Form Tambah Mahasiswa (Restored) -->
                    <div class="col-md-5">
                        <form id="assignMahasiswaForm" class="d-flex gap-2">
                             <select class="form-select flex-grow-1" id="selectAvailableMahasiswa" required>
                                 <option value="" disabled selected>Pilih Mahasiswa...</option>
                             </select>
                             <button type="submit" class="btn btn-primary text-nowrap">
                                <i class="bi bi-plus-lg"></i> Tambah
                             </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 40%;">Mahasiswa</th>
                            <th style="width: 20%;">Stambuk</th>
                             <th class="text-center" style="width: 15%;">Status</th>
                             <th class="text-center" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="participantsTableBody">
                        <tr><td colspan="5" class="text-center py-4 text-muted">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="bi bi-exclamation-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi Hapus</h5>
                <p class="text-muted mb-4" id="deleteModalMessage">Apakah Anda yakin ingin menghapus data ini?<br>Tindakan ini tidak dapat dibatalkan.</p>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-light flex-fill rounded-3 py-2 fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmDelete" class="btn btn-danger flex-fill rounded-3 py-2 fw-medium">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Custom Script -->
<script src="<?= APP_URL ?>/Assets/Script/admin/rooms.js?v=1.1"></script>