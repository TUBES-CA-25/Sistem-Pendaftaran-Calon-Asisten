<?php
/**
 * Written Test Schedule View - Student Based
 * @var array $data
 */
$jadwalTesList = $data['jadwalTesList'] ?? [];
$mahasiswaList = $data['mahasiswaList'] ?? [];
$ruanganList = $data['ruanganList'] ?? [];
$bankSoalList = $data['bankSoalList'] ?? [];
?>

<style>
    .btn-action {
        width: 32px; height: 32px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s;
    }
    .table-custom thead th {
        color: #2f66f6; font-weight: 700; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.5px;
        background-color: #fff; border-top: 1px solid #dee2e6;
        padding: 1rem 0.75rem;
    }
    .table-custom tbody td {
        padding: 1rem 0.75rem; color: #333; font-size: 0.875rem;
    }
    .modal-header-gradient {
        background: var(--gradient-header); color: #fff;
    }
</style>

<main>
    <?php
        $title = 'Jadwal Tes Tertulis';
        $subtitle = 'Manajemen jadwal ujian tertulis mahasiswa per individu';
        $icon = 'bi bi-calendar-event';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="container-fluid px-4 py-3">
        <!-- Controls -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-3">
            <div class="position-relative" style="width: 280px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchInput" class="form-control rounded-3 ps-5" placeholder="Cari nama atau stambuk...">
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-primary btn-gradient-primary border-0 rounded-4 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </button>
                <button class="btn btn-success btn-gradient-success border-0 rounded-4 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#bulkScheduleModal">
                    <i class="bi bi-calendar-plus"></i> Bulk Schedule
                </button>
            </div>
        </div>

        <!-- Student Schedule Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle table-custom" id="jadwalTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">NO</th>
                                <th width="20%">NAMA LENGKAP</th>
                                <th width="15%">STAMBUK</th>
                                <th width="20%">KEGIATAN</th>
                                <th width="15%">RUANGAN</th>
                                <th width="10%">TANGGAL</th>
                                <th width="10%">WAKTU</th>
                                <th class="text-center" width="5%">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($jadwalTesList)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted fw-bold">Kosong</td>
                                </tr>
                            <?php else: ?>
                                <?php $i = 1; foreach ($jadwalTesList as $row): ?>
                                    <tr data-id="<?= $row['id'] ?>">
                                        <td class="text-center fw-bold text-secondary"><?= $i++ ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                                        <td class="text-secondary"><?= htmlspecialchars($row['stambuk']) ?></td>
                                        <td><?= htmlspecialchars($row['kegiatan']) ?></td>
                                        <td><?= htmlspecialchars($row['ruangan']) ?></td>
                                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= htmlspecialchars($row['waktu']) ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn-action bg-warning-subtle text-warning border-0 open-edit" 
                                                        data-id="<?= $row['id'] ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                        data-stambuk="<?= htmlspecialchars($row['stambuk']) ?>"
                                                        data-ruangan="<?= $row['ruangan'] ?>"
                                                        data-kegiatan="<?= htmlspecialchars($row['kegiatan']) ?>"
                                                        data-tanggal="<?= $row['tanggal'] ?>"
                                                        data-waktu="<?= $row['waktu'] ?>"
                                                        title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn-action bg-danger-subtle text-danger border-0 delete-schedule" data-id="<?= $row['id'] ?>" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Modal Bulk Schedule -->
<div class="modal fade" id="bulkScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header modal-header-gradient border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Bulk Schedule Tes Tertulis</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="bulkJadwalForm">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Pilih Mahasiswa</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchBulkMhs" class="form-control border-start-0" placeholder="Cari stambuk/nama...">
                            </div>
                            <div class="border rounded-3 p-2 shadow-sm" style="max-height: 300px; overflow-y: auto;">
                                <div class="list-group list-group-flush" id="bulkMahasiswaChecklist">
                                    <?php foreach ($mahasiswaList as $m): ?>
                                        <label class="list-group-item d-flex align-items-center gap-3 py-2 cursor-pointer">
                                            <input class="form-check-input m-0 flex-shrink-0" type="checkbox" value="<?= $m['id'] ?>" data-name="<?= htmlspecialchars($m['nama_lengkap']) ?>" data-stambuk="<?= htmlspecialchars($m['stambuk']) ?>">
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small"><?= htmlspecialchars($m['nama_lengkap']) ?></span>
                                                <span class="text-secondary smaller" style="font-size: 0.75rem;"><?= htmlspecialchars($m['stambuk']) ?></span>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <span class="text-muted smaller" id="selectedCount">0 dipilih</span>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="selectAllBulk">Pilih Semua</button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ruangan</label>
                                <select class="form-select" name="ruangan" required>
                                    <option value="" disabled selected>Pilih Ruangan</option>
                                    <?php foreach ($ruanganList as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kegiatan</label>
                                <input type="text" class="form-control" name="kegiatan" value="Tes Tertulis">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Waktu Mulai</label>
                                <input type="time" class="form-control" name="waktu" required>
                            </div>
                            <div class="alert alert-info border-0 shadow-sm p-2 mb-0" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Seluruh mahasiswa terpilih akan dijadwalkan pada waktu dan ruangan yang sama.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="bulkJadwalForm" class="btn btn-success btn-gradient-success px-4 rounded-3">Simpan Bulk</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header modal-header-gradient border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Tambah Jadwal Tes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addJadwalForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Mahasiswa</label>
                        <div class="d-flex gap-2 mb-2">
                             <select class="form-select" id="mahasiswaSelect">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['stambuk'] ?> - <?= $m['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-secondary" id="addMhsToList">Tambah</button>
                        </div>
                        <ul class="list-group" id="selectedMhsList"></ul>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Ruangan</label>
                            <select class="form-select" id="ruanganSelect" required>
                                <option value="" disabled selected>Pilih</option>
                                <?php foreach ($ruanganList as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Kegiatan</label>
                            <input type="text" class="form-control" id="kegiatanInput" value="Tes Tertulis">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="tanggalInput" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Waktu</label>
                            <input type="time" class="form-control" id="waktuInput" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addJadwalForm" class="btn btn-primary px-4 rounded-3">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Jadwal -->
<div class="modal fade" id="updateJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header modal-header-gradient border-0">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>Update Jadwal Tes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="updateJadwalForm">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mahasiswa</label>
                        <p id="editMhsInfo" class="form-control-plaintext border rounded-3 px-3 bg-light small mb-0"></p>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Ruangan</label>
                            <select class="form-select" id="editRuangan" required>
                                <?php foreach ($ruanganList as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Kegiatan</label>
                            <input type="text" class="form-control" id="editKegiatan" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="editTanggal" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Waktu</label>
                            <input type="time" class="form-control" id="editWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="updateJadwalForm" class="btn btn-primary px-4 rounded-3 text-white">Update Jadwal</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let selectedMahasiswa = [];

    // Search logic for main table
    $('#searchInput').on('keyup', function() {
        let filter = $(this).val().toLowerCase();
        $('#table-body tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1);
        });
    });

    // Bulk Search logic
    $('#searchBulkMhs').on('keyup', function() {
        let filter = $(this).val().toLowerCase();
        $('#bulkMahasiswaChecklist label').each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1);
        });
    });

    // Select All Bulk
    $('#selectAllBulk').click(function() {
        const isAllChecked = $('#bulkMahasiswaChecklist input:checked').length === $('#bulkMahasiswaChecklist input:visible').length;
        $('#bulkMahasiswaChecklist input:visible').prop('checked', !isAllChecked).trigger('change');
        $(this).text(!isAllChecked ? 'Batalkan Semua' : 'Pilih Semua');
    });

    $(document).on('change', '#bulkMahasiswaChecklist input', function() {
        $('#selectedCount').text($('#bulkMahasiswaChecklist input:checked').length + ' dipilih');
    });

    // Add student to the single-add modal list
    $('#addMhsToList').click(function() {
        const id = $('#mahasiswaSelect').val();
        const text = $('#mahasiswaSelect option:selected').text();
        if (!id) return;
        if (selectedMahasiswa.includes(id)) return showAlert('Mahasiswa sudah ada dalam daftar', false);

        selectedMahasiswa.push(id);
        $('#selectedMhsList').append(`
            <li class="list-group-item d-flex justify-content-between align-items-center py-2" data-id="${id}">
                <span class="small">${text}</span>
                <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0 remove-mhs"><i class="bi bi-x-circle"></i></button>
            </li>
        `);
    });

    $(document).on('click', '.remove-mhs', function() {
        const id = $(this).parent().data('id').toString();
        selectedMahasiswa = selectedMahasiswa.filter(item => item !== id);
        $(this).parent().remove();
    });

    // Save Single Add Schedule
    $('#addJadwalForm').submit(function(e) {
        e.preventDefault();
        if (selectedMahasiswa.length === 0) return showAlert('Pilih minimal satu mahasiswa', false);

        const data = {
            id: selectedMahasiswa,
            ruangan: $('#ruanganSelect').val(),
            kegiatan: $('#kegiatanInput').val(),
            tanggal: $('#tanggalInput').val(),
            waktu: $('#waktuInput').val()
        };

        saveSchedule(data, '#addJadwalModal');
    });

    // Save Bulk Schedule
    $('#bulkJadwalForm').submit(function(e) {
        e.preventDefault();
        const checked = [];
        $('#bulkMahasiswaChecklist input:checked').each(function() { checked.push($(this).val()); });

        if (checked.length === 0) return showAlert('Pilih minimal satu mahasiswa', false);

        const fd = new FormData(this);
        const data = {
            id: checked,
            ruangan: fd.get('ruangan'),
            kegiatan: fd.get('kegiatan'),
            tanggal: fd.get('tanggal'),
            waktu: fd.get('waktu')
        };

        saveSchedule(data, '#bulkScheduleModal');
    });

    // Open Edit Modal
    $(document).on('click', '.open-edit', function() {
        const btn = $(this);
        $('#editId').val(btn.data('id'));
        $('#editMhsInfo').text(btn.data('stambuk') + ' - ' + btn.data('nama'));
        $('#editKegiatan').val(btn.data('kegiatan'));
        $('#editTanggal').val(btn.data('tanggal'));
        $('#editWaktu').val(btn.data('waktu'));
        
        // Find room ID based on name or set manually
        const roomName = btn.data('ruangan');
        $(`#editRuangan option`).each(function() {
            if ($(this).text() === roomName) $(this).prop('selected', true);
        });

        $('#updateJadwalModal').modal('show');
    });

    // Save Update Schedule
    $('#updateJadwalForm').submit(function(e) {
        e.preventDefault();
        const data = {
            id: $('#editId').val(),
            ruangan: $('#editRuangan').val(),
            kegiatan: $('#editKegiatan').val(),
            tanggal: $('#editTanggal').val(),
            waktu: $('#editWaktu').val()
        };

        $.ajax({
            url: APP_URL + '/updateJadwalTes',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(res) {
                if (res.status === 'success') {
                    $('#updateJadwalModal').modal('hide');
                    showAlert(res.message);
                    document.querySelector('a[data-page="jadwaltes"]').click();
                } else {
                    showAlert(res.message, false);
                }
            }
        });
    });

    function saveSchedule(data, modalId) {
        $.ajax({
            url: APP_URL + '/saveJadwalTes',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(res) {
                if (res.status === 'success') {
                    $(modalId).modal('hide');
                    showAlert(res.message);
                    // Reload the page content
                    document.querySelector('a[data-page="jadwaltes"]').click();
                } else {
                    showAlert(res.message, false);
                }
            },
            error: function() {
                showAlert('Terjadi kesalahan jaringan', false);
            }
        });
    }

    // Delete Schedule
    $(document).on('click', '.delete-schedule', function() {
        const id = $(this).data('id');
        showConfirmDelete(() => {
            $.ajax({
                url: APP_URL + '/deleteJadwalTes',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: id }),
                success: function(res) {
                    if (res.status === 'success') {
                        showAlert(res.message);
                        $(`tr[data-id="${id}"]`).fadeOut(300, function() { $(this).remove(); });
                    } else {
                        showAlert(res.message, false);
                    }
                }
            });
        });
    });
});
</script>

