<?php
/**
 * Ruangan View - Card Layout with Details & Interview
 * 
 * Data yang diterima dari controller:
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

    /* Page Header */
    .page-header {
        background: #2f66f6;
        color: #fff;
        border-radius: 0;
        padding: 35px 30px;
        position: relative;
        overflow: hidden;
        margin-bottom: 0;
        box-shadow: 0 4px 20px rgba(47, 102, 246, 0.2);
    }

    .page-header::after {
        content: "";
        position: absolute;
        right: -180px;
        top: 50%;
        width: 400px;
        height: 400px;
        transform: translateY(-50%);
        border: 5px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        opacity: 0.7;
    }

    .page-header h1 {
        margin: 0 0 8px 0;
        font-size: 2rem;
        font-weight: 700;
        color: white;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .page-header .subtitle {
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        position: relative;
        z-index: 1;
    }

    /* Main Container */
    .content-container {
        padding: 30px;
        min-height: calc(100vh - 120px);
    }

    /* Controls */
    .controls-bar {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .search-box {
        position: relative;
        width: 300px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 15px 10px 40px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #f8faff;
        transition: all 0.2s;
    }

    .search-box input:focus {
        background: white;
        border-color: #2f66f6;
        box-shadow: 0 0 0 3px rgba(47, 102, 246, 0.1);
        outline: none;
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }

    .btn-custom {
        background-color: #2f66f6;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(47, 102, 246, 0.2);
    }

    .btn-custom:hover {
        background-color: #1e4fd8;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(47, 102, 246, 0.3);
    }

    /* Card Styling */
    .room-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        cursor: pointer; /* Clickable */
    }

    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }

    .card-header-img {
        height: 100px;
        background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .room-icon-large {
        font-size: 3rem;
        color: #2f66f6;
        filter: drop-shadow(0 4px 6px rgba(47, 102, 246, 0.2));
    }

    .card-body {
        padding: 24px;
        flex: 1;
        text-align: center;
    }

    .room-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .room-desc {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .card-actions {
        padding: 16px 24px;
        background: #f8faff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .action-group {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-manage {
        background: #2f66f6;
        color: white;
        width: auto;
        padding: 0 16px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .btn-manage:hover { background: #1e4fd8; color: white; }

    .btn-edit { background: #fff; border-color: #e2e8f0; color: #64748b; }
    .btn-edit:hover { border-color: #2f66f6; color: #2f66f6; background: white; }

    .btn-delete { background: #fff; border-color: #e2e8f0; color: #64748b; }
    .btn-delete:hover { border-color: #dc3545; color: #dc3545; background: white; }

    /* Badges for Activities */
    .badge-presentasi { background: #e0f2fe; color: #0284c7; }
    .badge-testulis { background: #fef3c7; color: #d97706; }
    .badge-wawancara { background: #fce7f3; color: #db2777; }
    .badge-activity { padding: 4px 10px; border-radius: 50px; font-weight: 600; font-size: 0.8rem; }

</style>

<main>
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-grid-fill"></i> Ruangan</h1>
        <p class="subtitle">Kelola data ruangan praktikum secara visual</p>
    </div>

    <div class="content-container">
        <!-- Controls Bar -->
        <div class="controls-bar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama ruangan...">
            </div>
            <button class="btn-custom" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                <i class="bi bi-plus-lg me-2"></i>Tambah Ruangan
            </button>
        </div>

        <!-- Room Cards Grid -->
        <div class="row g-4" id="ruanganGrid">
            <?php if (empty($ruanganList)) { ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p>Belum ada data ruangan.</p>
                </div>
            <?php } else { ?>
                <?php foreach ($ruanganList as $ruangan) { ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 room-item">
                        <!-- Add data attributes to card for quick access -->
                        <div class="room-card" 
                            data-id="<?= $ruangan['id'] ?>" 
                            data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                            
                            <div class="card-header-img">
                                <i class="bi bi-building room-icon-large"></i>
                            </div>
                            <div class="card-body">
                                <h3 class="room-title"><?= htmlspecialchars($ruangan['nama']) ?></h3>
                                <p class="room-desc">Praktikum, Ujian & Wawancara</p>
                            </div>
                            <div class="card-actions">
                                <button class="btn-icon btn-manage manage-users-btn" 
                                    data-id="<?= $ruangan['id'] ?>" 
                                    data-name="<?= htmlspecialchars($ruangan['nama']) ?>"
                                    title="Kelola Peserta">
                                    <i class="bi bi-people-fill me-2"></i>Peserta
                                </button>
                                <div class="action-group">
                                    <button class="btn-icon btn-edit edit-button" data-id="<?= $ruangan['id'] ?>" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-icon btn-delete delete-button" data-id="<?= $ruangan['id'] ?>" title="Hapus">
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
</main>

<!-- Modal Tambah Ruangan -->
<div class="modal fade" id="tambahRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="tambahRuanganForm">
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary">Nama Ruangan</label>
                        <input type="text" class="form-control form-control-lg fs-6" id="namaRuangan" name="namaRuangan" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100 py-2">Simpan Ruangan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Ruangan -->
<div class="modal fade" id="updateRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Update Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="updateRuanganForm">
                    <input type="hidden" id="updateRuanganId">
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary">Nama Ruangan</label>
                        <input type="text" class="form-control form-control-lg fs-6" id="updateNamaRuangan" name="updateNamaRuangan" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100 py-2">Update Ruangan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola Peserta (Assignment) -->
<div class="modal fade" id="participantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom pb-3">
                <div class="d-flex flex-column">
                    <h5 class="modal-title fw-bold">Kelola Peserta</h5>
                    <small class="text-muted">Ruangan: <span id="participantRoomName" class="fw-bold text-primary"></span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <!-- Activity Selector -->
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4" id="pills-presentasi-tab" data-bs-toggle="pill" data-bs-target="#pills-presentasi" type="button" role="tab">Presentasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" id="pills-testulis-tab" data-bs-toggle="pill" data-bs-target="#pills-testulis" type="button" role="tab">Tes Tulis</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" id="pills-wawancara-tab" data-bs-toggle="pill" data-bs-target="#pills-wawancara" type="button" role="tab">Wawancara</button>
                    </li>
                </ul>

                <!-- Add Participant Section -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Tambah Peserta</h6>
                        <form id="addParticipantForm" class="d-flex gap-2">
                            <select class="form-select" id="availableParticipants" required>
                                <option value="" selected disabled>Pilih Peserta...</option>
                            </select>
                            <button type="submit" class="btn btn-primary text-nowrap">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Participants List -->
                <h6 class="fw-bold mb-3">Daftar Peserta Terdaftar</h6>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Nama</th>
                                <th>Stambuk</th>
                                <th class="text-center" width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="participantsTableBody">
                            <tr><td colspan="3" class="text-center text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Details (Unified List) -->
<div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom pb-3">
                <div class="d-flex flex-column">
                    <h5 class="modal-title fw-bold">Detail Ruangan</h5>
                    <small class="text-muted"><span id="detailRoomName" class="fw-bold text-primary fs-5"></span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Peserta</th>
                                <th>Stambuk</th>
                                <th>Aktivitas / Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody id="roomOccupantsTableBody">
                            <tr><td colspan="3" class="text-center">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // --- CLIENT SIDE SEARCH ---
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#ruanganGrid .room-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // --- ROOM CARD CLICK (VIEW DETAILS) ---
        $(document).on('click', '.room-card', function(e) {
            // Prevent if clicking buttons
            if($(e.target).closest('button').length) return;
            
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#detailRoomName').text(name);
            
            // Show Modal first
            new bootstrap.Modal(document.getElementById('roomDetailsModal')).show();
            
            // Fetch Unified Data
            $('#roomOccupantsTableBody').html('<tr><td colspan="3" class="text-center text-muted py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Memuat daftar peserta...</div></td></tr>');
            
            $.ajax({
                url: '<?= APP_URL ?>/getroomoccupants',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        const tbody = $('#roomOccupantsTableBody');
                        tbody.empty();
                        if(res.data.length === 0) {
                            tbody.html('<tr><td colspan="3" class="text-center text-muted py-4">Ruangan ini masih kosong.</td></tr>');
                            return;
                        }
                        res.data.forEach(user => {
                            let badgeClass = 'bg-secondary';
                            if(user.activity === 'Presentasi') badgeClass = 'badge-presentasi';
                            if(user.activity === 'Tes Tulis') badgeClass = 'badge-testulis';
                            if(user.activity === 'Wawancara') badgeClass = 'badge-wawancara';
                            
                            tbody.append(`
                                <tr>
                                    <td class="fw-medium">${user.name || '-'}</td>
                                    <td>${user.stambuk || '-'}</td>
                                    <td><span class="badge-activity ${badgeClass}">${user.activity}</span></td>
                                </tr>
                            `);
                        });
                    } else {
                        showAlert('Gagal memuat detail: ' + res.message, false);
                    }
                }
            });
        });

        // --- RUANGAN CRUD ---
        $(document).on('click', '.edit-button', function() {
            const id = $(this).data('id');
            const card = $(this).closest('.room-card');
            const currentName = card.find('.room-title').text();
            
            $('#updateRuanganId').val(id);
            $('#updateNamaRuangan').val(currentName);
            new bootstrap.Modal(document.getElementById('updateRuanganModal')).show();
        });

        $('#tambahRuanganForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= APP_URL ?>/tambahruangan',
                type: 'POST',
                data: { namaRuangan: $('#namaRuangan').val() },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') { showAlert('Berhasil!', true); location.reload(); }
                    else { showAlert('Gagal: ' + res.message, false); }
                }
            });
        });

        $('#updateRuanganForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= APP_URL ?>/updateruangan',
                type: 'POST',
                data: { id: $('#updateRuanganId').val(), namaRuangan: $('#updateNamaRuangan').val() },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') { showAlert('Berhasil!', true); location.reload(); }
                    else { showAlert('Gagal: ' + res.message, false); }
                }
            });
        });

        $(document).on('click', '.delete-button', function() {
            if(!confirm('Hapus ruangan ini?')) return;
            $.ajax({
                url: '<?= APP_URL ?>/deleteruangan',
                type: 'POST',
                data: { id: $(this).data('id') },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') location.reload();
                    else showAlert('Gagal hapus: ' + res.message, false);
                }
            });
        });

        // --- PARTICIPANT MANAGEMENT (ASSIGNMENT) ---
        let currentRoomId = null;
        let currentType = 'presentasi'; // Default

        // Open Modal
        $(document).on('click', '.manage-users-btn', function() {
            currentRoomId = $(this).data('id');
            const roomName = $(this).data('name');
            $('#participantRoomName').text(roomName);
            
            // Activate first tab
            const triggerEl = document.querySelector('#pills-presentasi-tab');
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            currentType = 'presentasi';
            
            loadParticipants();
            new bootstrap.Modal(document.getElementById('participantsModal')).show();
        });

        // Switch Tabs
        const tabEls = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', event => {
                if(event.target.id === 'pills-presentasi-tab') currentType = 'presentasi';
                else if(event.target.id === 'pills-testulis-tab') currentType = 'tes_tulis';
                else if(event.target.id === 'pills-wawancara-tab') currentType = 'wawancara';
                loadParticipants();
            });
        });

        // Load Participants
        function loadParticipants() {
            if(!currentRoomId) return;
            $('#participantsTableBody').html('<tr><td colspan="3" class="text-center">Memuat...</td></tr>');
            
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
                        showAlert('Gagal memuat peserta: ' + res.message, false);
                    }
                },
                error: function() {
                    $('#participantsTableBody').html('<tr><td colspan="3" class="text-center text-danger">Error koneksi</td></tr>');
                }
            });
        }

        function renderParticipants(users) {
            const tbody = $('#participantsTableBody');
            tbody.empty();
            if(users.length === 0) {
                tbody.html('<tr><td colspan="3" class="text-center text-muted">Belum ada peserta di kategori ini</td></tr>');
                return;
            }
            users.forEach(u => {
                tbody.append(`
                    <tr>
                        <td>${u.name || '-'}</td>
                        <td>${u.stambuk || '-'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger remove-participant" data-id="${u.id}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function renderAvailableOptions(users) {
            const select = $('#availableParticipants');
            select.empty().append('<option value="" selected disabled>Pilih Peserta...</option>');
            users.forEach(u => {
                select.append(`<option value="${u.id}">${u.name} (${u.stambuk})</option>`);
            });
        }

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
                    } else {
                        showAlert(res.message, false);
                    }
                }
            });
        });

        $(document).on('click', '.remove-participant', function() {
            if(!confirm('Hapus peserta dari kategori ini?')) return;
            const userId = $(this).data('id');

            $.ajax({
                url: '<?= APP_URL ?>/removeparticipant',
                type: 'POST',
                data: { userId: userId, type: currentType },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        loadParticipants();
                    } else {
showAlert(res.message, false);
                    }
                }
            });
        });
    });
</script>