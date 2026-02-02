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
    .table-custom {
        --bs-table-border-color: #e0e0e0; /* Matches reference image crispness */
    }
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

    <div class="container-fluid px-4 mt-3">
        <!-- Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
            <div class="position-relative" style="width: 280px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchInput" class="form-control rounded-3 ps-5" placeholder="Cari nama atau stambuk...">
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-primary btn-gradient-primary border-0 rounded-4 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2" type="button" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </button>
            </div>
        </div>

        <!-- Student Schedule Table -->
        <div class="card mb-3 rounded-0 border-0 shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0 table-custom" id="jadwalTable">
                        <thead>
                            <tr>
                                <th class="text-center py-3 px-3" width="5%">NO</th>
                                <th class="py-3 px-3" width="20%">NAMA LENGKAP</th>
                                <th class="py-3 px-3" width="15%">STAMBUK</th>
                                <th class="py-3 px-3" width="20%">KEGIATAN</th>
                                <th class="py-3 px-3" width="15%">RUANGAN</th>
                                <th class="py-3 px-3" width="10%">TANGGAL</th>
                                <th class="py-3 px-3" width="10%">WAKTU</th>
                                <th class="text-center py-3 px-3" width="5%">AKSI</th>
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
                                                <button class="btn-action bg-info-subtle text-info border-0 reset-exam" 
                                                        data-id="<?= $row['id_mahasiswa'] ?>" 
                                                        data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                        title="Reset Pengerjaan">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
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
                    showAlert(res.message, true);
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
                    showAlert(res.message, true);
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
        showConfirmDelete(function() {
            $.ajax({
                url: APP_URL + '/deleteJadwalTes',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: id }),
                success: function(res) {
                    if (res.status === 'success') {
                        showAlert(res.message || 'Jadwal berhasil dihapus!', true);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert(res.message, false);
                    }
                },
                error: function() {
                    showAlert('Terjadi kesalahan jaringan', false);
                }
            });
        }, 'Apakah Anda yakin ingin menghapus jadwal tes ini?');
    });
        // Reset Exam Handler
        $(document).on('click', '.reset-exam', function() {
            const idMahasiswa = $(this).data('id');
            const nama = $(this).data('nama');
            
            showActionConfirmation({
                title: 'Reset Pengerjaan Tes?',
                message: `Apakah Anda yakin ingin mereset pengerjaan tes untuk <strong>${nama}</strong>? <br><small class="text-danger">Seluruh jawaban dan nilai akan dihapus permanen.</small>`,
                btnText: 'Reset Sekarang',
                type: 'danger',
                onConfirm: function() {
                    $.ajax({
                        url: '<?= APP_URL ?>/admin/reset-ujian',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ id: idMahasiswa }),
                        success: function(response) {
                            // Clean up any lingering modal backdrops
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css({
                                'overflow': '',
                                'padding-right': ''
                            });

                            if (response.status === 'success') {
                                showAlert(response.message, true);
                            } else {
                                showAlert(response.message || 'Gagal mereset ujian', false);
                            }
                        },
                        error: function(xhr) {
                            // Clean up any lingering modal backdrops
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css({
                                'overflow': '',
                                'padding-right': ''
                            });

                            let msg = 'Terjadi kesalahan server';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                msg = 'Error: ' + xhr.responseText.substring(0, 100);
                            }
                            showAlert(msg, false);
                        }
                    });
                }
            });
        });
    });
</script>
