<?php
/**
 * Daftar Hadir Peserta Admin View - Presentasi Style
 */
$absensiList = $absensiList ?? [];
$mahasiswaList = $mahasiswaList ?? [];
?>
<!-- Bootstrap Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    /* Minimal styles for specific components */
    .avatar-placeholder-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--gradient-header);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.5rem;
        margin: 0 auto 16px auto;
    }

    .multi-select-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 8px;
        background: #fff;
    }

    .multi-select-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        margin-bottom: 4px;
    }

    .multi-select-item:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
    }

    .multi-select-item.selected {
        background: #e0e7ff; /* indigo-100 */
        border-color: #6366f1; /* indigo-500 */
        color: #4338ca; /* indigo-800 */
    }

    .modal-kehadiran .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    
    .modal-kehadiran .btn-close {
        filter: brightness(0) invert(1);
    }
</style>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Rekap Peserta';
        $subtitle = 'Rekapitulasi lengkap tahapan seleksi dan status akhir peserta';
        $icon = 'bi bi-clipboard-check';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="container-fluid px-4">
        <!-- Table Controls -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-3">
            <div class="position-relative" style="width: 280px; max-width: 100%;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchKehadiran" class="form-control ps-5 rounded-3" placeholder="Cari nama atau stambuk...">
            </div>
            <button class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                <i class="bi bi-plus-circle"></i> Tambah Data
            </button>
        </div>

        <?php if (empty($absensiList)): ?>
            <div class="text-center py-5 text-secondary">
                <i class="bi bi-inbox display-1 opacity-50"></i>
                <h3 class="h4 mt-3 mb-2">Belum Ada Data Rekap</h3>
                <p class="mb-0">Data rekap akan muncul setelah Anda menambahkan peserta</p>
            </div>
        <?php else: ?>
            <div class="table-responsive rounded-3 overflow-hidden shadow-sm">
                <table class="table table-bordered table-hover align-middle mb-0" id="monitoringTable">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th class="fw-semibold text-uppercase small text-center" style="width: 50px;">No</th>
                            <th class="fw-semibold text-uppercase small">Nama Lengkap</th>
                            <th class="fw-semibold text-uppercase small">Stambuk</th>
                            <th class="fw-semibold text-uppercase small text-center">Tes Tertulis</th>
                            <th class="fw-semibold text-uppercase small text-center">Presentasi</th>
                            <th class="fw-semibold text-uppercase small text-center">Wawancara I</th>
                            <th class="fw-semibold text-uppercase small text-center">Wawancara II</th>
                            <th class="fw-semibold text-uppercase small text-center">Status Akhir</th>
                            <th class="fw-semibold text-uppercase small text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($absensiList as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                        <div class="small text-muted">Mahasiswa</div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['stambuk']) ?></td>
                            <td class="text-center"><?= renderStatusBadge($row['absensi_tes_tertulis']) ?></td>
                            <td class="text-center"><?= renderStatusBadge($row['absensi_presentasi']) ?></td>
                            <td class="text-center"><?= renderStatusBadge($row['absensi_wawancara_I']) ?></td>
                            <td class="text-center"><?= renderStatusBadge($row['absensi_wawancara_II']) ?></td>
                            <td class="text-center">
                                <?php 
                                    $t = $row['absensi_tes_tertulis'];
                                    $p = $row['absensi_presentasi'];
                                    $w1 = $row['absensi_wawancara_I'];
                                    $w2 = $row['absensi_wawancara_II'];

                                    $allHadir = ($t === 'Hadir' && $p === 'Hadir' && $w1 === 'Hadir' && $w2 === 'Hadir');
                                    $hasFailed = ($t === 'Alpha' || $t === 'Tidak Hadir' || 
                                                  $p === 'Alpha' || $p === 'Tidak Hadir' || 
                                                  $w1 === 'Alpha' || $w1 === 'Tidak Hadir' || 
                                                  $w2 === 'Alpha' || $w2 === 'Tidak Hadir');

                                    if ($allHadir) {
                                        echo '<span class="badge bg-success rounded-pill px-3">Lolos</span>';
                                    } elseif ($hasFailed) {
                                        echo '<span class="badge bg-danger rounded-pill px-3">Tidak Lolos</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary rounded-pill px-3">Pending</span>';
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-info bg-info-subtle text-info border-0 rounded-3 open-rekap"
                                            title="Detail Rekap"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                            data-stambuk="<?= $row['stambuk'] ?>"
                                            data-berkas="<?= $row['berkas_status'] ?? '0' ?>"
                                            data-tes="<?= $row['absensi_tes_tertulis'] ?>"
                                            data-nilai="<?= $row['nilai_akhir'] ?? '' ?>"
                                            data-presentasi="<?= $row['absensi_presentasi'] ?>"
                                            data-wawancara1="<?= $row['absensi_wawancara_I'] ?>"
                                            data-wawancara2="<?= $row['absensi_wawancara_II'] ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning bg-warning-subtle text-warning border-0 rounded-3 open-detail"
                                            title="Edit"
                                            data-id="<?= $row['id'] ?>"
                                            data-userid="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                            data-stambuk="<?= $row['stambuk'] ?>"
                                            data-absensiwawancarai="<?= $row['absensi_wawancara_I'] ?? '' ?>"
                                            data-absensiwawancaraii="<?= $row['absensi_wawancara_II'] ?? '' ?>"
                                            data-absensitestertulis="<?= $row['absensi_tes_tertulis'] ?? '' ?>"
                                            data-absensipresentasi="<?= $row['absensi_presentasi'] ?? '' ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger bg-danger-subtle text-danger border-0 rounded-3 btn-delete-attendance"
                                            title="Hapus"
                                            data-id="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
// Inline helper with XSS protection
function renderStatusBadge($val) {
    // Handle empty/null values
    if (!$val || trim($val) === '' || $val === '-') {
        return '<span class="badge bg-light text-secondary border">Belum Ada</span>';
    }

    // Sanitize input first
    $sanitized = htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
    $v = strtolower($sanitized);

    // Case-insensitive status matching
    $statusMap = [
        'hadir' => ['class' => 'bg-success', 'label' => 'Hadir'],
        'alpha' => ['class' => 'bg-danger', 'label' => 'Alpha'],
        'tidak hadir' => ['class' => 'bg-danger', 'label' => 'Tidak Hadir'],
        'izin' => ['class' => 'bg-warning text-dark', 'label' => 'Izin'],
        'sakit' => ['class' => 'bg-warning text-dark', 'label' => 'Sakit'],
        'process' => ['class' => 'bg-info text-dark', 'label' => 'Process']
    ];

    // Find matching status
    if (isset($statusMap[$v])) {
        $status = $statusMap[$v];
        return '<span class="badge ' . $status['class'] . '">' . $status['label'] . '</span>';
    }

    // Unknown status - show as info
    return '<span class="badge bg-info text-dark">' . ucfirst($sanitized) . '</span>';
}
?>

<!-- MODAL ADD -->
<div class="modal fade modal-kehadiran" id="addMahasiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus"></i>
                    Tambah Data Kehadiran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addJadwalForm">
                    <div class="form-group">
                        <label>Pilih Mahasiswa</label>
                        <div class="input-group">
                            <select class="form-select form-select-custom" id="mahasiswa">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['stambuk'] ?> - <?= htmlspecialchars($m['nama_lengkap']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary" type="button" id="addMahasiswaButton">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mahasiswa Terpilih:</label>
                        <div class="multi-select-container" id="selectedMahasiswaList">
                            <div class="empty-msg text-center text-muted py-3">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-0 mt-2">Belum ada mahasiswa dipilih</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-muted small fw-bold mb-2">STATUS KEHADIRAN</label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select form-select-custom" id="absensiTesTertulis">
                                <option value="" selected>Tes Tertulis...</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Alpha">Alpha</option>
                                <option value="Izin">Izin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select form-select-custom" id="absensiPresentasi">
                                <option value="" selected>Presentasi...</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Alpha">Alpha</option>
                                <option value="Izin">Izin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-custom" id="absensiWawancara1">
                                <option value="" selected>Wawancara I...</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Alpha">Alpha</option>
                                <option value="Izin">Izin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-custom" id="absensiWawancara2">
                                <option value="" selected>Wawancara II...</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Alpha">Alpha</option>
                                <option value="Izin">Izin</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addJadwalForm" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REKAP DETAIL -->
<div class="modal fade modal-kehadiran" id="rekapDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
                <h5 class="modal-title">
                    <i class="bi bi-card-checklist"></i>
                    Rekap Peserta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-4 text-center bg-light border-bottom">
                    <div class="avatar-placeholder-large mx-auto mb-3" style="width:70px; height:70px; font-size:1.75rem;">
                        <span id="rekapAvatar">U</span>
                    </div>
                    <h5 class="fw-bold mb-1" id="rekapNama">Nama Peserta</h5>
                    <p class="text-muted mb-0" id="rekapStambuk">Stambuk</p>
                </div>
                
                <div class="p-4">
                    <h6 class="text-uppercase text-muted small fw-bold mb-3 ls-1">Tahapan Seleksi</h6>
                    
                    <div class="d-flex flex-column gap-3">
                        <!-- Berkas -->
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light">
                            <div>
                                <h6 class="mb-0 fw-bold">1. Kelengkapan Berkas</h6>
                                <small class="text-muted">Administrasi Awal</small>
                            </div>
                            <span id="statusBerkas"></span>
                        </div>

                        <!-- Tes Tertulis -->
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light">
                            <div>
                                <h6 class="mb-0 fw-bold">2. Tes Tertulis</h6>
                                <small class="text-muted" id="scoreTes">Nilai: -</small>
                            </div>
                            <span id="statusTes"></span>
                        </div>

                        <!-- Presentasi -->
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light">
                            <div>
                                <h6 class="mb-0 fw-bold">3. Presentasi</h6>
                                <small class="text-muted">Status Kehadiran</small>
                            </div>
                            <span id="statusPresentasi"></span>
                        </div>

                        <!-- Wawancara -->
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light">
                            <div>
                                <h6 class="mb-0 fw-bold">4. Wawancara</h6>
                                <small class="text-muted">Wawancara I & II</small>
                            </div>
                            <div class="d-flex gap-2">
                                <span id="statusWawancara1"></span>
                                <span id="statusWawancara2"></span>
                            </div>
                        </div>

                        <!-- FINAL RESULT -->
                        <div class="mt-2 p-3 rounded" id="finalResultBox" style="background: #f1f5f9;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">HASIL AKHIR</h6>
                                <span class="badge bg-secondary" id="finalStatus" style="font-size: 0.9rem; padding: 6px 12px;">PENDING</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade modal-kehadiran" id="detailAbsensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i>
                    Edit Data Kehadiran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="avatar-placeholder-large">
                        <span id="avatarInitial">U</span>
                    </div>
                    <h5 class="fw-bold mb-1" id="detailNama">Name</h5>
                    <p class="text-muted small mb-0" id="detailStambuk">Stambuk</p>
                </div>
                <input type="hidden" id="detailUserId">

                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted mb-1">Tes Tertulis</label>
                        <select id="tesTertulis" class="form-select form-select-custom">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-1">Presentasi</label>
                        <select id="presentasi" class="form-select form-select-custom">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="small text-muted mb-1">Wawancara I</label>
                        <select id="wawancaraI" class="form-select form-select-custom">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="small text-muted mb-1">Wawancara II</label>
                        <select id="wawancaraII" class="form-select form-select-custom">
                            <option value="">-</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="saveDetailAbsensi" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->


<script>
$(document).ready(function() {
    const APP_URL = '<?= APP_URL ?>';


    const selectedContainer = $('#selectedMahasiswaList');
    let selectedMahasiswa = [];

    $('#addMahasiswaButton').click(function() {
        const sel = $('#mahasiswa');
        const id = sel.val();
        if(!id) {
            showAlert('Pilih mahasiswa terlebih dahulu', false);
            return;
        }

        // Check duplicate
        if(selectedMahasiswa.includes(id)) {
            showAlert('Mahasiswa sudah dipilih', false);
            return;
        }

        selectedContainer.find('.empty-msg').remove();
        const txt = sel.find('option:selected').text();
        selectedMahasiswa.push(id);

        selectedContainer.append(`
            <div class="multi-select-item selected" data-id="${id}">
                <i class="bi bi-person-check"></i>
                <span style="flex: 1;">${txt}</span>
                <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-item">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `);
        sel.val('');
    });

    $(document).on('click', '.remove-item', function() {
        const item = $(this).closest('.multi-select-item');
        const id = item.data('id');
        selectedMahasiswa = selectedMahasiswa.filter(i => i != id);
        item.remove();

        if (selectedMahasiswa.length === 0) {
            selectedContainer.html(`
                <div class="empty-msg text-center text-muted py-3">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0 mt-2">Belum ada mahasiswa dipilih</p>
                </div>
            `);
        }
    });

    $('#addJadwalForm').submit(function(e) {
        e.preventDefault();

        if(selectedMahasiswa.length === 0) {
            showAlert('Pilih minimal 1 mahasiswa', false);
            return;
        }

        const data = {
            mahasiswa: selectedMahasiswa,
            tesTertulis: $('#absensiTesTertulis').val(),
            presentasi: $('#absensiPresentasi').val(),
            wawancara1: $('#absensiWawancara1').val(),
            wawancara2: $('#absensiWawancara2').val(),
        };

        $.ajax({
            url: APP_URL + "/absensi",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function(res) {
                if(res.status === 'success') {
                    // Flash Message for Add
                    sessionStorage.setItem('pendingToast', JSON.stringify({ 
                        message: 'Data kehadiran berhasil disimpan!', 
                        isSuccess: true 
                    }));
                    location.reload();
                } else {
                    showAlert(res.message || 'Terjadi kesalahan', false);
                }
            },
            error: function(xhr, status, error) {
                showAlert('Gagal menghubungi server', false);
            }
        });
    });

    // --- DELETE LOGIC ---
    $(document).on('click', '.btn-delete-attendance', function() {
        const btn = $(this);
        const id = btn.data('id');
        const nama = btn.data('nama');


        if (typeof showConfirmDelete === 'function') {
            showConfirmDelete(function() {
                $.ajax({
                    url: APP_URL + "/deleteabsensi",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ id: id }),
                    success: function(res) {
                        if(res.status === 'success') {
                            // Use Flash Message pattern - No delay needed
                            sessionStorage.setItem('pendingToast', JSON.stringify({ 
                                message: 'Data kehadiran berhasil dihapus!', 
                                isSuccess: true 
                            }));
                            location.reload(); 
                        } else {
                            showAlert(res.message || 'Gagal menghapus data', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('Gagal menghubungi server', false);
                    }
                });
            }, `Hapus data kehadiran untuk ${nama}?`);
        } else {
             if (confirm(`Hapus data kehadiran untuk ${nama}?`)) {
                 // Fallback AJAX
                 $.ajax({
                    url: APP_URL + "/deleteabsensi",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ id: id }),
                    success: function(res) {
                        if(res.status === 'success') {
                            showAlert('Data kehadiran berhasil dihapus!', true);
                            location.reload();
                        } else {
                            showAlert(res.message || 'Gagal menghapus data', false);
                        }
                    }
                });
             }
        }
    });

    // --- EDIT LOGIC ---
    $('.open-detail').click(function() {
        const btn = $(this);
        const modal = $('#detailAbsensiModal');

        $('#detailNama').text(btn.data('nama'));
        $('#detailStambuk').text(btn.data('stambuk'));
        $('#detailUserId').val(btn.data('userid'));
        $('#avatarInitial').text(btn.data('nama').charAt(0).toUpperCase());

        // Set values
        $('#tesTertulis').val(btn.data('absensitestertulis') || '');
        $('#presentasi').val(btn.data('absensipresentasi') || '');
        $('#wawancaraI').val(btn.data('absensiwawancarai') || '');
        $('#wawancaraII').val(btn.data('absensiwawancaraii') || '');

        modal.modal('show');
    });

    $('#saveDetailAbsensi').click(function() {
        const data = {
            id: $('#detailUserId').val(),
            tesTertulis: $('#tesTertulis').val(),
            presentasi: $('#presentasi').val(),
            wawancaraI: $('#wawancaraI').val(),
            wawancaraII: $('#wawancaraII').val(),
        };

        $.ajax({
            url: APP_URL + "/updateabsensi",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function(res) {
                if(res.status === 'success') {
                    showAlert('Perubahan berhasil disimpan!', true);
                    
                    // Update DOM Row
                    const btn = $(`.open-detail[data-userid="${data.id}"]`);
                    const tr = btn.closest('tr');
                    
                    if (tr.length) {
                        const getBadge = (val) => {
                            if(!val || typeof val !== 'string' || val.trim() === '' || val === '-') {
                                return '<span class="badge bg-light text-secondary border">Belum Ada</span>';
                            }
                            const v = val.toLowerCase().trim();
                            
                            if(v === 'hadir') return '<span class="badge bg-success">Hadir</span>';
                            if(v === 'alpha') return '<span class="badge bg-danger">Alpha</span>';
                            if(v === 'tidak hadir') return '<span class="badge bg-danger">Tidak Hadir</span>';
                            if(v === 'izin') return '<span class="badge bg-warning text-dark">Izin</span>';
                            if(v === 'sakit') return '<span class="badge bg-warning text-dark">Sakit</span>';
                            
                            return `<span class="badge bg-info text-dark">${val}</span>`;
                        };

                        // Update btn data attrs (for next open)
                        btn.data('absensitestertulis', data.tesTertulis);
                        btn.data('absensipresentasi', data.presentasi);
                        btn.data('absensiwawancarai', data.wawancaraI);
                        btn.data('absensiwawancaraii', data.wawancaraII);

                        // Update Table Columns (Tes, Presentasi, Wawancara I, II)
                        // Correct Indices: 
                        // 3: Tes Tertulis
                        // 4: Presentasi
                        // 5: Wawancara I
                        // 6: Wawancara II
                        
                        tr.find('td:eq(3)').html(getBadge(data.tesTertulis));
                        tr.find('td:eq(4)').html(getBadge(data.presentasi));
                        tr.find('td:eq(5)').html(getBadge(data.wawancaraI));
                        tr.find('td:eq(6)').html(getBadge(data.wawancaraII));
                    }
                    
                    // Close Modal (as requested)
                    $('#detailAbsensiModal').modal('hide'); 
                } else {
                    showAlert(res.message || 'Terjadi kesalahan', false);
                }
            },
            error: function(xhr, status, error) {
                showAlert('Gagal menghubungi server', false);
            }
        });
    });
    // --- REKAP DETAIL LOGIC ---
    $('.open-rekap').click(function() {
        const btn = $(this);
        const modal = $('#rekapDetailModal');

        // Basic Info
        $('#rekapNama').text(btn.data('nama'));
        $('#rekapStambuk').text(btn.data('stambuk'));
        $('#rekapAvatar').text(btn.data('nama').charAt(0).toUpperCase());

        // Helper to create badge
        const createBadge = (status, type = 'attendance') => {
            if(!status || status === '-' || status === '') 
                return '<span class="badge bg-light text-secondary border">Belum Ada</span>';
            
            const s = status.toString().toLowerCase();
            
            if(type === 'berkas') {
                if(s === '1') return '<span class="badge bg-success">Diterima</span>';
                if(s === '0') return '<span class="badge bg-warning text-dark">Pending</span>'; // Assuming 0 is pending/not user action
                return '<span class="badge bg-secondary">Ditolak</span>';
            }
            
            // Attendance
            if(s === 'hadir') return '<span class="badge bg-success">Hadir</span>';
            if(s === 'alpha' || s === 'tidak hadir') return '<span class="badge bg-danger">Alpha</span>';
            if(s === 'izin' || s === 'sakit') return '<span class="badge bg-warning text-dark">Izin</span>';
            
            return `<span class="badge bg-info text-dark">${status}</span>`;
        };

        // 1. Berkas
        $('#statusBerkas').html(createBadge(btn.data('berkas'), 'berkas'));

        // 2. Tes Tertulis
        const nilai = btn.data('nilai');
        const tesStatus = btn.data('tes'); // Hadir/Alpha
        
        let tesBadge = createBadge(tesStatus);
        if(nilai !== '') {
            $('#scoreTes').text(`Nilai: ${nilai}`);
            if(nilai >= 70) tesBadge += ' <span class="badge bg-success ms-1">Lulus</span>';
            else tesBadge += ' <span class="badge bg-danger ms-1">Gagal</span>';
        } else {
            $('#scoreTes').text('Nilai: Belum keluar');
        }
        $('#statusTes').html(tesBadge);

        // 3. Presentasi
        $('#statusPresentasi').html(createBadge(btn.data('presentasi')));

        // 4. Wawancara
        $('#statusWawancara1').html(createBadge(btn.data('wawancara1')));
        $('#statusWawancara2').html(createBadge(btn.data('wawancara2')));

        // 5. Final Result
        const box = $('#finalResultBox');
        const badge = $('#finalStatus');
        
        box.removeClass('bg-success-subtle bg-danger-subtle bg-light');
        badge.removeClass('bg-success bg-danger bg-secondary');
        
        const t = btn.data('tes');
        const p = btn.data('presentasi');
        const w1 = btn.data('wawancara1');
        const w2 = btn.data('wawancara2');

        const isAllHadir = (t === 'Hadir' && p === 'Hadir' && w1 === 'Hadir' && w2 === 'Hadir');
        const isFailed = (t === 'Alpha' || t === 'Tidak Hadir' || 
                          p === 'Alpha' || p === 'Tidak Hadir' || 
                          w1 === 'Alpha' || w1 === 'Tidak Hadir' || 
                          w2 === 'Alpha' || w2 === 'Tidak Hadir');

        if(isAllHadir) {
            box.addClass('bg-success-subtle'); // Light green
            badge.addClass('bg-success').text('LOLOS');
        } else if (isFailed) {
            box.addClass('bg-danger-subtle'); // Light red
            badge.addClass('bg-danger').text('TIDAK LOLOS');
        } else {
            box.addClass('bg-light');
            badge.addClass('bg-secondary').text('PROSES');
        }

        modal.modal('show');
    });

});
</script>
