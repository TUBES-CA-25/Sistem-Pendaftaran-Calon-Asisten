<?php
/**
 * Ruangan View - Card Layout with Details & Interview
 * 
 * @var array $ruanganList - Daftar ruangan
 */
$ruanganList = $ruanganList ?? [];
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


<!-- Custom Styles Removed for Bootstrap 5 Refactoring -->


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
            <!-- Controls Bar -->
            <div class="card border-0 shadow-sm mb-4 rounded-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="position-relative" style="width: 300px;">
                        <i class="bi bi-search position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" id="searchInput" class="form-control bg-light border-0 ps-5" placeholder="Cari ruangan..." style="border-radius: 8px;">
                    </div>
                    <button class="btn btn-primary px-4 py-2 fw-medium shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Ruangan
                    </button>
                </div>
            </div>

            <!-- Grid -->
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
                            <div class="card h-100 border-0 shadow-sm rounded-4 room-card cursor-pointer" 
                                data-id="<?= $ruangan['id'] ?>" 
                                data-name="<?= htmlspecialchars($ruangan['nama']) ?>">
                                <div class="card-header border-0 d-flex align-items-center justify-content-center py-5 rounded-top-4" 
                                    style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                                    <i class="bi bi-buildings-fill text-primary" style="font-size: 3.5rem;"></i>
                                </div>
                                <div class="card-body text-center p-4">
                                    <h5 class="fw-bold text-dark mb-2 text-uppercase"><?= htmlspecialchars($ruangan['nama']) ?></h5>
                                    <p class="text-secondary small mb-0 fw-medium">Ruangan Aktivitas Seleksi</p>
                                </div>
                                <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-between align-items-center rounded-bottom-4">
                                    <span class="text-primary fw-medium small">Kelola <i class="bi bi-arrow-right"></i></span>
                                    <div>
                                        <button class="btn btn-light btn-sm text-primary me-1 btn-edit-room" 
                                            data-id="<?= $ruangan['id'] ?>" 
                                            data-name="<?= htmlspecialchars($ruangan['nama']) ?>" 
                                            title="Ubah Nama">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm text-danger btn-delete-room" 
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
        <div class="bg-primary text-white p-4 mb-4 rounded-4 shadow-sm mx-4 position-relative z-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light bg-opacity-25 text-white border-0 p-2 rounded-3" id="backToListBtn" title="Kembali">
                        <i class="bi bi-arrow-left fs-4"></i>
                    </button>
                    <div>
                        <h2 id="detailRoomTitle" class="fw-bold m-0 fs-2">Nama Ruangan</h2>
                        <div class="d-flex align-items-center gap-2 mt-1 opacity-75">
                            <i class="bi bi-grid"></i>
                            <span class="fs-6">Detail & Manajemen Peserta</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-4 pb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Navigation Tabs -->
                <div class="border-bottom px-4">
                    <ul class="nav nav-tabs border-bottom-0 gap-4" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active py-4 text-secondary fw-semibold border-0 bg-transparent" id="pills-testulis-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab">
                                <i class="bi bi-file-text me-2"></i>Tes Tulis
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-4 text-secondary fw-semibold border-0 bg-transparent" id="pills-presentasi-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab">
                                <i class="bi bi-easel me-2"></i>Presentasi
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-4 text-secondary fw-semibold border-0 bg-transparent" id="pills-wawancara-tab" data-bs-toggle="pill" data-bs-target="#pills-content" role="tab">
                                <i class="bi bi-chat-text me-2"></i>Wawancara
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Toolbar / Form Area -->
                <div class="bg-light p-4 border-bottom">
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
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-5 py-3 text-secondary text-uppercase text-xs fw-bold" style="width: 10%;">No</th>
                                <th class="py-3 text-secondary text-uppercase text-xs fw-bold" style="width: 45%;">Nama Mahasiswa</th>
                                <th class="py-3 text-secondary text-uppercase text-xs fw-bold" style="width: 25%;">Stambuk</th>
                                <th class="text-center py-3 text-secondary text-uppercase text-xs fw-bold" style="width: 20%;">Aksi</th>
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
                <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
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