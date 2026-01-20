<?php
/**
 * Ruangan View - Card Layout with Details & Interview
 * 
 * @var array $ruanganList - Daftar ruangan
 */
$ruanganList = $ruanganList ?? [];
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background: #f5f7fa;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    main {
        margin: -20px -20px -20px -20px;
        width: calc(100% + 40px);
    }

/* Page Header Styles moved to components/PageHeader.php */

    /* Main Container */
    .content-container {
        padding: 30px 40px; 
        margin-top: 0; 
        position: relative;
        z-index: 2;
    }

    /* --- List View Specifics --- */
    .controls-bar {
        background: white;
        border-radius: 12px; /* Slightly smaller radius */
        padding: 16px 20px; /* Reduced padding from 24px */
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Softer shadow */
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .room-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        cursor: pointer;
    }

    .room-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(47, 102, 246, 0.15);
    }

    .card-header-img {
        height: 120px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .room-icon-large { font-size: 3.5rem; color: #3b82f6; }

    .card-body { padding: 24px; text-align: center; }
    .room-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .room-desc { color: #94a3b8; font-size: 0.85rem; margin-bottom: 0; font-weight: 500; }
    
    .card-actions {
        padding: 20px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: center;
    }

    /* --- Detail View Specifics --- */
    .detail-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        overflow: hidden;
        border: none;
    }

    /* REFINED TABS STYLE */
    .nav-tabs-custom {
        padding: 0 20px;
        background: white;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        /* justify-content: center; REMOVED per user request */
        gap: 40px; 
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 24px 12px;
        font-size: 1.1rem;
        position: relative;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: color 0.2s;
    }
    
    .nav-tabs-custom .nav-link:hover {
        color: #2f66f6;
    }

    .nav-tabs-custom .nav-link.active {
        color: #2f66f6;
    }

    /* Custom underline for active state */
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px; /* Align with border-bottom */
        left: 0;
        width: 100%;
        height: 4px;
        background: #2f66f6;
        border-radius: 4px 4px 0 0;
    }

    .toolbar-section {
        padding: 24px 30px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Table Styles */
    .custom-table thead th {
        background-color: #2f66f6;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        padding: 18px 24px;
        border: none;
    }

    .custom-table tbody td {
        padding: 18px 24px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-weight: 500;
    }
    
    .custom-table tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Buttons */
    .btn-header-back {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
    }
    .btn-header-back:hover { background: rgba(255,255,255,0.3); transform: translateX(-4px); }

    .btn-header-action {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-header-action:hover { background: rgba(255,255,255,0.3); }
    .btn-header-danger { background: rgba(220, 38, 38, 0.8); }
    .btn-header-danger:hover { background: rgba(220, 38, 38, 1); }

    /* TOAST CUSTOM STYLES */
    #toast-container {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .toast-notification {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border: none;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        min-width: 350px;
        transform: translateX(120%);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .toast-notification.success {
        border-left: 6px solid #22c55e;
    }
    
    .toast-notification.error {
        border-left: 6px solid #ef4444;
    }

    .toast-notification.show {
        transform: translateX(0);
    }

    .toast-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .toast-notification.success .toast-icon {
        background: #dcfce7;
        color: #22c55e;
    }
    
    .toast-notification.error .toast-icon {
        background: #fee2e2;
        color: #ef4444;
    }

    .toast-content {
        flex-grow: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .toast-message {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .toast-close {
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.25rem;
        transition: color 0.2s;
        padding: 0;
        line-height: 1;
    }

    .toast-close:hover {
        color: #475569;
    }

    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: transparent;
    }
    
    .toast-progress-bar {
        height: 100%;
        background: #22c55e;
        width: 100%;
        transform-origin: left;
    }
    
    .toast-notification.error .toast-progress-bar {
        background: #ef4444;
    }

    /* Modal icon warning */
    .modal-icon-warning {
        width: 80px;
        height: 80px;
        background: #fef2f2;
        color: #ef4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
    }

</style>

<main>
    <!-- SECTION: LIST VIEW -->
    <div id="ruanganListSection">
        <?php
            $title = 'Ruangan Praktikum';
            $subtitle = 'Kelola data ruangan, peserta, dan aktivitas praktikum';
            $icon = 'bi bi-buildings-fill';
            require_once __DIR__ . '/../../templates/components/PageHeader.php';
        ?>

        <div class="content-container">
            <div class="controls-bar">
                <div class="position-relative" style="width: 300px;">
                    <i class="bi bi-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); font-size: 0.9rem;"></i>
                    <input type="text" id="searchInput" class="form-control border-0 bg-light ps-5" style="border-radius: 8px; font-size: 0.95rem; padding-top: 10px; padding-bottom: 10px;" placeholder="Cari ruangan...">
                </div>
                <button class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 8px; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Ruangan
                </button>
            </div>

            <div class="row g-4" id="ruanganGrid">
                <?php if (empty($ruanganList)) { ?>
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-4 shadow-sm d-inline-block">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted m-0">Belum ada data ruangan.</p>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php foreach ($ruanganList as $ruangan) { ?>
                        <div class="col-md-6 col-lg-4 col-xl-3 room-item">
                            <div class="room-card" 
                                data-id="<?= $ruangan['id'] ?>" 
                                data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                                <div class="card-header-img">
                                    <i class="bi bi-buildings-fill room-icon-large"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="room-title"><?= htmlspecialchars($ruangan['nama']) ?></h3>
                                    <p class="room-desc">Ruangan Aktivitas Seleksi</p>
                                </div>
                                <div class="card-actions d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-medium small">Kelola <i class="bi bi-arrow-right"></i></span>
                                    <div>
                                        <button class="btn btn-sm btn-light text-primary btn-edit-room me-1" 
                                            data-id="<?= $ruangan['id'] ?>" 
                                            data-name="<?= htmlspecialchars($ruangan['nama']) ?>" 
                                            title="Ubah Nama">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger btn-delete-room" 
                                            data-id="<?= $ruangan['id'] ?>" 
                                            title="Hapus Ruangan">
                                            <i class="bi bi-trash"></i>
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

    <!-- SECTION: DETAIL VIEW (FULL PAGE STYLE) -->
    <div id="ruanganDetailSection" class="d-none">
        
        <!-- Header Detail -->
        <div class="page-header align-items-center">
            <div class="d-flex justify-content-between w-100 align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <button class="btn-header-back" id="backToListBtn" title="Kembali">
                        <i class="bi bi-arrow-left fs-4"></i>
                    </button>
                    <div>
                        <h2 id="detailRoomTitle" class="fw-bold m-0 fs-2 text-white">Nama Ruangan</h2>
                        <div class="d-flex align-items-center gap-2 mt-1 opacity-75 text-white">
                            <i class="bi bi-grid"></i>
                            <span class="fs-6">Detail & Manajemen Peserta</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="content-container">
            <div class="detail-card">
                <!-- Navigation Tabs -->
                <div class="nav-tabs-custom" id="pills-tab" role="tablist">
                    <!-- REORDERED: Tes Tulis First -->
                    <button class="nav-link active" id="pills-testulis-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab">
                        <i class="bi bi-file-text me-2"></i>Tes Tulis
                    </button>
                    <button class="nav-link" id="pills-presentasi-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab">
                        <i class="bi bi-easel me-2"></i>Presentasi
                    </button>
                    <button class="nav-link" id="pills-wawancara-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab">
                        <i class="bi bi-chat-text me-2"></i>Wawancara
                    </button>
                </div>

                <!-- Toolbar / Form Area -->
                <div class="toolbar-section">
                    <div class="row align-items-center gy-3">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-dark m-0">Daftar Peserta Terdaftar</h5>
                            <p class="text-muted small m-0">Kelola peserta untuk aktivitas ini</p>
                        </div>
                        <div class="col-md-6">
                            <form id="addParticipantForm" class="d-flex gap-2 justify-content-md-end">
                                <select class="form-select" id="availableParticipants" required style="max-width: 300px;">
                                    <option value="" selected disabled>Pilih Mahasiswa...</option>
                                </select>
                                <button type="submit" class="btn btn-primary px-4 text-nowrap">
                                    <i class="bi bi-plus-lg me-2"></i>Tambah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table custom-table w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="ps-5" style="width: 10%;">No</th>
                                <th style="width: 45%;">Nama Mahasiswa</th>
                                <th style="width: 25%;">Stambuk</th>
                                <th class="text-center" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="participantsTableBody">
                            <tr><td colspan="4" class="text-center py-5 text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
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
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-4">
                <div class="modal-icon-warning">
                    <i class="bi bi-exclamation-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi Hapus</h5>
                <p class="text-muted mb-4" id="deleteModalMessage">Apakah Anda yakin ingin menghapus peserta ini? <br>Tindakan ini tidak dapat dibatalkan.</p>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-secondary flex-fill rounded-3 py-2" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmDelete" class="btn btn-danger flex-fill rounded-3 py-2">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
(function() {
    // --- CONFIRMATION MODAL LOGIC (Bootstrap) ---
    let deleteModalInstance = null;

    function showConfirmDelete(onConfirm, message) {
        if (message) document.getElementById('deleteModalMessage').innerHTML = message;

        const btnConfirm = document.getElementById('btnConfirmDelete');
        const newBtn = btnConfirm.cloneNode(true);
        btnConfirm.parentNode.replaceChild(newBtn, btnConfirm);

        newBtn.addEventListener('click', function() {
            if (typeof onConfirm === 'function') onConfirm();
            closeDeleteModal();
        });

        const modalEl = document.getElementById('deleteConfirmModal');
        deleteModalInstance = new bootstrap.Modal(modalEl);
        deleteModalInstance.show();
    }

    function closeDeleteModal() {
        if (deleteModalInstance) {
            deleteModalInstance.hide();
        }
    }

    // Universal wrapper for AJAX and Direct Load
    const initRoomsScript = function() {
        $(document).ready(function () {
            let currentRoomId = null;
            let currentRoomName = '';
            let currentType = 'tes_tulis'; // DEFAULT TYPE IS NOW tes_tulis

        // --- NAVIGATION ---
        function showDetailView(id, name) {
            currentRoomId = id;
            currentRoomName = name;
            
            $('#ruanganListSection').addClass('d-none');
            $('#ruanganDetailSection').removeClass('d-none');
            
            $('#detailRoomTitle').text(name);
            $('#editRoomBtn').data('id', id);
            $('#deleteRoomBtn').data('id', id);
            
            // Activate Default Tab (Tes Tulis)
            const triggerEl = document.querySelector('#pills-testulis-tab');
            $(triggerEl).click(); 
            
            window.scrollTo(0, 0);
        }

        function showListView() {
            currentRoomId = null;
            $('#ruanganDetailSection').addClass('d-none');
            $('#ruanganListSection').removeClass('d-none');
        }

        $('#backToListBtn').click(showListView);

        // --- CLICKS ---
        $(document).on('click', '.room-card', function(e) {
            if($(e.target).closest('button').length) return;
            showDetailView($(this).data('id'), $(this).data('name'));
        });

        // --- TABS & DATA ---
        $('.nav-link').click(function() {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            if(this.id === 'pills-presentasi-tab') currentType = 'presentasi';
            else if(this.id === 'pills-testulis-tab') currentType = 'tes_tulis';
            else if(this.id === 'pills-wawancara-tab') currentType = 'wawancara';
            
            loadParticipants();
        });

        function loadParticipants() {
            if(!currentRoomId) return;
            $('#participantsTableBody').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary speed-fast" role="status"></div></td></tr>');
            
            $.ajax({
                url: '<?= APP_URL ?>/getroomparticipants',
                type: 'POST',
                data: { id: currentRoomId, type: currentType },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        renderParticipants(res.assigned);
                        renderAvailableOptions(res.available);
                    } else {
                        showAlert('Error: ' + res.message, false);
                    }
                },
                error: function() {
                    $('#participantsTableBody').html('<tr><td colspan="4" class="text-center text-danger py-5">Gagal memuat data. Periksa koneksi.</td></tr>');
                }
            });
        }

        function renderParticipants(users) {
            const tbody = $('#participantsTableBody');
            const thead = $('.custom-table thead tr');
            
            // Adjust header based on type
            if(currentType === 'tes_tulis') {
                if(thead.find('th').length === 4) {
                    thead.find('th:nth-child(3)').after('<th class="text-center" style="width: 20%;">Status</th>');
                }
            } else {
                if(thead.find('th').length === 5) {
                    thead.find('th:nth-child(4)').remove(); // Remove Status column
                }
            }

            tbody.empty();
            if(users.length === 0) {
                const colSpan = currentType === 'tes_tulis' ? 5 : 4;
                tbody.html(`<tr><td colspan="${colSpan}" class="text-center text-muted py-5 fw-light">Belum ada peserta di aktivitas ini.</td></tr>`);
                return;
            }

            users.forEach((u, index) => {
                let statusBadge = '';
                if(currentType === 'tes_tulis') {
                    if(u.is_finished == 1) {
                        statusBadge = `<td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Selesai</span></td>`;
                    } else {
                        statusBadge = `<td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Belum Selesai</span></td>`;
                    }
                }

                tbody.append(`
                    <tr>
                        <td class="fw-bold ps-5 text-secondary">${index + 1}</td>
                        <td class="fw-medium text-dark">${u.name || '-'}</td>
                        <td class="text-secondary">${u.stambuk || '-'}</td>
                        ${statusBadge}
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-danger px-3 remove-participant" data-id="${u.id}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function renderAvailableOptions(users) {
            const select = $('#availableParticipants');
            select.empty().append('<option value="" selected disabled>Pilih Mahasiswa...</option>');
            users.forEach(u => {
                select.append(`<option value="${u.id}">${u.name} (${u.stambuk})</option>`);
            });
        }

        // --- FORMS ---
        $('#addParticipantForm').on('submit', function(e) {
            e.preventDefault();
            const userId = $('#availableParticipants').val();
            if(!userId) return;

            $.ajax({
                url: '<?= APP_URL ?>/assignparticipant',
                type: 'POST',
                data: { userId: userId, roomId: currentRoomId, type: currentType },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        loadParticipants();
                        showAlert('Peserta berhasil ditambahkan', true);
                    }
                    else showAlert(res.message, false);
                }
            });
        });

        $(document).on('click', '.remove-participant', function() {
            const userId = $(this).data('id');
            const row = $(this).closest('tr');
            
            showConfirmDelete(() => {
                $.ajax({
                    url: '<?= APP_URL ?>/removeparticipant',
                    type: 'POST',
                    data: { userId: userId, type: currentType },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status === 'success') {
                            row.fadeOut(300, () => loadParticipants());
                            showAlert('Perubahan berhasil disimpan!', true);
                        } else showAlert(res.message, false);
                    }
                });
            }, 'Apakah Anda yakin ingin menghapus peserta ini?');
        });

        // --- EDIT/DELETE ROOM ---
        $('#editRoomBtn').on('click', function() {
            $('#updateRuanganId').val(currentRoomId);
            $('#updateNamaRuangan').val(currentRoomName);
            new bootstrap.Modal(document.getElementById('updateRuanganModal')).show();
        });

        $('#updateRuanganForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= APP_URL ?>/updateruangan',
                type: 'POST',
                data: { id: $('#updateRuanganId').val(), namaRuangan: $('#updateNamaRuangan').val() },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        $('#detailRoomTitle').text($('#updateNamaRuangan').val());
                        currentRoomName = $('#updateNamaRuangan').val();
                        bootstrap.Modal.getInstance(document.getElementById('updateRuanganModal')).hide();
                        showAlert('Nama ruangan diperbarui', true);
                    } else showAlert(res.message, false);
                }
            });
        });

        $('#deleteRoomBtn').on('click', function() {
            showConfirmDelete(() => {
                $.ajax({
                    url: '<?= APP_URL ?>/deleteruangan',
                    type: 'POST',
                    data: { id: currentRoomId },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status === 'success') {
                            sessionStorage.setItem('pendingToast', JSON.stringify({
                                 message: 'Ruangan berhasil dihapus!',
                                 isSuccess: true
                            }));
                            location.reload();
                        } else showAlert(res.message, false);
                    }
                });
            }, 'Apakah Anda yakin ingin menghapus ruangan ini beserta seluruh datanya?');
        });
        
        // --- ADD ROOM ---
        $('#tambahRuanganForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= APP_URL ?>/tambahruangan',
                type: 'POST',
                data: { namaRuangan: $('#namaRuangan').val() },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') location.reload();
                    else showAlert(res.message, false);
                }
            });
        });

        // --- LIST VIEW ACTIONS ---
        $(document).on('click', '.btn-edit-room', function(e) {
            e.stopPropagation(); // Prevent card click
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            $('#updateRuanganId').val(id);
            $('#updateNamaRuangan').val(name);
            new bootstrap.Modal(document.getElementById('updateRuanganModal')).show();
        });

        $(document).on('click', '.btn-delete-room', function(e) {
            e.stopPropagation(); // Prevent card click
            const id = $(this).data('id');
            currentRoomId = id; // Set for delete logic
            
            showConfirmDelete(() => {
                $.ajax({
                    url: '<?= APP_URL ?>/deleteruangan',
                    type: 'POST',
                    data: { id: currentRoomId },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status === 'success') {
                            sessionStorage.setItem('pendingToast', JSON.stringify({
                                 message: 'Ruangan berhasil dihapus!',
                                 isSuccess: true
                            }));
                            location.reload();
                        } else showAlert(res.message, false);
                    }
                });
            }, 'Apakah Anda yakin ingin menghapus ruangan ini beserta seluruh datanya?');
        });

        // Search
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.room-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        });
    };

    if (typeof jQuery !== 'undefined') {
        initRoomsScript();
    } else {
        document.addEventListener('DOMContentLoaded', initRoomsScript);
    }
})();
</script>