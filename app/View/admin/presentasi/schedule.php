<?php
/**
 * Jadwal Presentasi Admin View
 *
 * @var array $jadwalPresentasi
 * @var array $ruanganList - Passed via JS/AJAX mainly, but useful if preloaded
 */
$jadwalPresentasi = $jadwalPresentasi ?? [];
?>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    .btn-action { width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .table-hover tbody tr:hover { background-color: rgba(61, 194, 236, 0.08); }


    /* Unified Table Style */
    .table-custom { --bs-table-border-color: #e0e0e0; }
    .table-custom thead th {
        color: #2f66f6; font-weight: 700; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.5px;
        background-color: #fff; border-top: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
        padding: 1rem 0.75rem;
    }
    .table-custom tbody td {
        padding: 1rem 0.75rem; color: #333; font-size: 0.875rem;
        border-color: #e0e0e0;
    }
</style>

<main>
    <?php
        $title = 'Jadwal Presentasi';
        $subtitle = 'Kelola jadwal dan ruangan presentasi mahasiswa';
        $icon = 'bi bi-calendar-event';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="container-fluid px-4 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-3">
            <div class="position-relative" style="width: 280px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchJadwal" class="form-control rounded-3 ps-5" placeholder="Cari mahasiswa...">
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-primary bg-gradient-primary border-0 rounded-4 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2" id="btnAddJadwal">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </button>
            </div>
        </div>

        <div class="card mb-3 rounded-0 border-0 shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 table-custom" id="tableJadwal">
                        <thead>
                            <tr>
                        <th class="fw-semibold text-uppercase small py-3 px-3">No</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Nama Lengkap</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Stambuk</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Judul</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Ruangan</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Tanggal</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Waktu</th>
                        <th class="fw-semibold text-uppercase small py-3 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="jadwalTableBody">
                    <?php if (empty($jadwalPresentasi)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada jadwal presentasi</td></tr>
                    <?php else: ?>
                        <?php $i = 1; foreach ($jadwalPresentasi as $row): ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['ruangan'] ?? $row['nama_ruangan'] ?? '-') ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['waktu']) ?></td>
                                <td>
                                    <div class="d-flex gap-2 flex-nowrap align-items-center">
                                        <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 btn-edit-jadwal"
                                                data-id="<?= $row['id'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?>"
                                                data-ruangan="<?= $row['id_ruangan'] ?>"
                                                data-tanggal="<?= $row['tanggal'] ?>"
                                                data-waktu="<?= $row['waktu'] ?>"
                                                title="Edit"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 btn-delete-jadwal"
                                                data-id="<?= $row['id'] ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php $i++; endforeach; ?>
                    <?php endif; ?>
                </tbody>
                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold"><i class="bi bi-calendar-plus me-2"></i>Tambah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddJadwal" method="POST" action="javascript:void(0);">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pilih Mahasiswa:</label>
                        <select class="form-select rounded-3" id="selectMahasiswa" required><option value="">-- Loading --</option></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pilih Ruangan:</label>
                        <select class="form-select rounded-3" id="selectRuangan" required><option value="">-- Loading --</option></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Tanggal:</label>
                        <input type="date" class="form-control rounded-3" id="inputTanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Waktu:</label>
                        <input type="time" class="form-control rounded-3" id="inputWaktu" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formAddJadwal" class="btn btn-primary bg-gradient-primary rounded-3"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bulk Schedule -->


<!-- Modal Update Jadwal -->
<div class="modal fade" id="updateJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>Update Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formUpdateJadwal" method="POST" action="javascript:void(0);">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Mahasiswa:</label>
                        <input type="text" class="form-control rounded-3 bg-light" id="editNama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Ruangan:</label>
                        <select class="form-select rounded-3" id="editRuangan" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Tanggal:</label>
                        <input type="date" class="form-control rounded-3" id="editTanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Waktu:</label>
                        <input type="time" class="form-control rounded-3" id="editWaktu" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formUpdateJadwal" class="btn btn-primary bg-gradient-primary rounded-3"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const APP_URL = '<?= APP_URL ?>';
    
    $('#searchJadwal').on('keyup', function() {
        const term = $(this).val().toLowerCase();
        $('#jadwalTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1)
        });
    });

    function loadRuangan() {
        $.post(APP_URL + '/getallruangan', function(res) {
            if (res.status === 'success') {
                let opts = '<option value="">-- Pilih Ruangan --</option>';
                res.data.forEach(r => opts += `<option value="${r.id}">${r.nama}</option>`);
                $('#selectRuangan, #bulkRuangan, #editRuangan').html(opts);
            }
        }, 'json');
    }

    function loadAvailableMahasiswa() {
        $.post(APP_URL + '/getavailablemahasiswa', function(res) {
            if (res.status === 'success') {
                let opts = '<option value="">-- Pilih Mahasiswa --</option>';
                res.data.forEach((m, idx) => {
                    opts += `<option value="${m.id_presentasi}">${m.nama_lengkap} - ${m.stambuk}</option>`;
                });
                $('#selectMahasiswa').html(opts);
            }
        }, 'json');
    }

    function loadJadwal() {
        $.post(APP_URL + '/getjadwalpresentasi', function(res) {
            if(res.status==='success') {
                let html = '';
                if(res.data.length===0) html='<tr><td colspan="8" class="text-center text-muted">Belum ada jadwal</td></tr>';
                else {
                    res.data.forEach((j, i) => {
                        html += `<tr>
                            <td>${i+1}</td>
                            <td><strong>${j.nama_lengkap}</strong></td>
                            <td>${j.stambuk}</td>
                            <td>${j.judul||'-'}</td>
                            <td>${j.ruangan}</td>
                            <td>${new Date(j.tanggal).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})}</td>
                            <td>${j.waktu}</td>
                            <td>
                                <div class="d-flex gap-2 flex-nowrap align-items-center">
                                    <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 btn-edit-jadwal"
                                            data-id="${j.id}"
                                            data-nama="${j.nama_lengkap}"
                                            data-ruangan="${j.id_ruangan}"
                                            data-tanggal="${j.tanggal}"
                                            data-waktu="${j.waktu}"
                                            title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 btn-delete-jadwal"
                                            data-id="${j.id}" title="Hapus"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>`;
                    });
                }
                $('#jadwalTableBody').html(html);
            }
        }, 'json');
    }

    $('#btnAddJadwal').click(function(e) {
        e.preventDefault();
        loadAvailableMahasiswa(); loadRuangan();
        $('#formAddJadwal')[0].reset();
        new bootstrap.Modal('#addJadwalModal').show();
    });

    $('#formAddJadwal').submit(function(e) {
        e.preventDefault();
        $.post(APP_URL + '/savejadwalpresentasi', {
            id_presentasi: $('#selectMahasiswa').val(),
            id_ruangan: $('#selectRuangan').val(),
            tanggal: $('#inputTanggal').val(),
            waktu: $('#inputWaktu').val()
        }, function(res) {
            bootstrap.Modal.getInstance(document.getElementById('addJadwalModal')).hide();
            if(res.status==='success') { showAlert('Disimpan!'); loadJadwal(); }
            else showAlert(res.message, false);
        }, 'json');
    });



    $(document).on('click', '.btn-edit-jadwal', function() {
        const btn = $(this);
        loadRuangan();
        $('#editId').val(btn.data('id'));
        $('#editNama').val(btn.data('nama'));
        $('#editTanggal').val(btn.data('tanggal'));
        $('#editWaktu').val(btn.data('waktu'));
        
        // Wait slightly for loadRuangan to finish or set it once options exist
        setTimeout(() => {
            $('#editRuangan').val(btn.data('ruangan'));
        }, 300);

        new bootstrap.Modal('#updateJadwalModal').show();
    });

    $('#formUpdateJadwal').submit(function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatejadwalpresentasi', {
            id: $('#editId').val(),
            id_ruangan: $('#editRuangan').val(),
            tanggal: $('#editTanggal').val(),
            waktu: $('#editWaktu').val()
        }, function(res) {
            bootstrap.Modal.getInstance(document.getElementById('updateJadwalModal')).hide();
            if(res.status==='success') { showAlert('Berhasil diupdate!'); loadJadwal(); }
            else showAlert(res.message, false);
        }, 'json');
    });

    $(document).on('click', '.btn-delete-jadwal', function() {
        const id = $(this).data('id');
        showConfirmDelete(function() {
            $.post(APP_URL + '/deletejadwalpresentasi', { id: id }, function(res) {
                if(res.status === 'success') { 
                    showAlert('Jadwal berhasil dihapus!', true); 
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(res.message, false);
                }
            }, 'json');
        }, 'Apakah Anda yakin ingin menghapus jadwal presentasi ini?');
    });

    // Initial Load
    // loadJadwal(); // Actually page loads with data, so minimal need.
});
</script>
