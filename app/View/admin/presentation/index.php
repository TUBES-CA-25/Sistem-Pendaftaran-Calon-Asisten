<?php
/**
 * Presentasi Admin View
 *
 * Data yang diterima dari controller:
 * @var array $mahasiswaList - Daftar mahasiswa presentasi
 * @var array $mahasiswaAccStatus - Status acc mahasiswa
 * @var array $ruanganList - Daftar ruangan
 * @var array $jadwalPresentasi - Jadwal presentasi
 */
$mahasiswaList = $mahasiswaList ?? [];
$mahasiswaAccStatus = $mahasiswaAccStatus ?? [];
$ruanganList = $ruanganList ?? [];
$jadwalPresentasi = $jadwalPresentasi ?? [];
?>
<!-- Bootstrap Icons CSS (fallback) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    /* Custom styles that can't be replaced by Bootstrap utilities */

    /* Tab Navigation Custom Styling */
    .tab-btn {
        position: relative;
    }

    .tab-btn.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--bs-primary);
        border-radius: 3px 3px 0 0;
    }

    /* Action Button Hover Effects */
    .btn-action {
        width: 36px;
        height: 36px;
        min-width: 36px;
        padding: 0;
    }

    .btn-action i,
    .btn-action i.bi {
        font-size: 1.1rem;
        line-height: 1;
        display: inline-block !important;
        font-style: normal;
    }

    /* Tooltip on hover */
    .btn-action[title] {
        position: relative;
    }

    .btn-action[title]:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 100;
        margin-bottom: 4px;
    }

    /* Badge Status Custom */
    .badge-status {
        min-width: 80px;
    }

    /* Multi Select Specific */
    .multi-select-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--bs-primary);
        flex-shrink: 0;
    }

    .multi-select-item label {
        cursor: pointer;
        margin: 0;
        flex: 1;
    }

    /* Table hover effect */
    .table-hover tbody tr:hover {
        background-color: rgba(61, 194, 236, 0.08);
    }

    /* Empty State */
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<main class="p-0 m-n3 w-100">
    <!-- Page Header -->
    <?php
        $title = 'Manajemen Presentasi';
        $subtitle = 'Kelola pengajuan judul dan jadwal presentasi mahasiswa';
        $icon = 'bi bi-easel';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <!-- Card Content -->
    <div class="bg-white p-4 min-vh-100">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs border-bottom-2 mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active tab-btn fw-semibold px-4 py-3 border-0 rounded-3 me-2" id="pengajuan-tab" data-bs-toggle="tab" data-bs-target="#tab-pengajuan" data-tab="pengajuan" type="button" role="tab">
                    <i class="bi bi-file-text me-2"></i>Pengajuan Judul
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-btn fw-semibold px-4 py-3 border-0 rounded-3" id="jadwal-tab" data-bs-toggle="tab" data-bs-target="#tab-jadwal" data-tab="jadwal" type="button" role="tab">
                    <i class="bi bi-calendar-event me-2"></i>Jadwal Presentasi
                </button>
            </li>
        </ul>

        <!-- Tab Content Container -->
        <div class="tab-content">
            <!-- Tab 1: Pengajuan Judul -->
            <div class="tab-pane fade show active" id="tab-pengajuan" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div class="position-relative" style="width: 280px;">
                        <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="searchPengajuan" class="form-control rounded-3 ps-5" placeholder="Cari nama atau stambuk...">
                    </div>
                </div>

                <?php if (empty($mahasiswaList)): ?>
                    <div class="empty-state text-center py-5 text-muted">
                        <i class="bi bi-inbox"></i>
                        <h3 class="fs-4 text-secondary mb-2">Belum Ada Pengajuan</h3>
                        <p>Data pengajuan judul akan muncul setelah mahasiswa mengajukan judul presentasi</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive rounded-4 shadow-sm">
                        <table class="table table-hover align-middle mb-0" id="tablePengajuan">
                            <thead class="table-primary">
                                <tr>
                                    <th class="fw-semibold text-uppercase small">No</th>
                                    <th class="fw-semibold text-uppercase small">Nama Lengkap</th>
                                    <th class="fw-semibold text-uppercase small">Stambuk</th>
                                    <th class="fw-semibold text-uppercase small">Judul Presentasi</th>
                                    <th class="fw-semibold text-uppercase small">Status</th>
                                    <th class="fw-semibold text-uppercase small">Aksi</th>
                                </tr>
                            </thead>
                    <tbody>
                        <?php $i = 1; foreach ($mahasiswaList as $row): ?>
                            <?php
                                $isAccepted = isset($row['is_accepted']) && $row['is_accepted'] == 1;
                                $isRejected = isset($row['is_accepted']) && $row['is_accepted'] == 2;
                                $hasSchedule = isset($row['has_schedule']) && $row['has_schedule'];

                                // Status badge class and text
                                if ($hasSchedule) {
                                    $badgeClass = 'badge-scheduled';
                                    $badgeText = 'Terjadwal';
                                } elseif ($isRejected) {
                                    $badgeClass = 'badge-rejected';
                                    $badgeText = 'Ditolak';
                                } elseif ($isAccepted) {
                                    $badgeClass = 'badge-accepted';
                                    $badgeText = 'Diterima';
                                } else {
                                    $badgeClass = 'badge-pending';
                                    $badgeText = 'Menunggu';
                                }
                            ?>
                            <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                                <td class="text-muted"><?= $i ?></td>
                                <td><strong class="text-dark"><?= htmlspecialchars($row['nama'] ?? '-') ?></strong></td>
                                <td class="text-secondary"><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> badge-status px-3 py-2 rounded-3">
                                        <?= $badgeText ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-nowrap align-items-center">
                                        <button class="btn btn-sm btn-action bg-info-subtle text-info border-0 rounded-3 d-inline-flex align-items-center justify-content-center btn-detail-pengajuan"
                                                data-nama="<?= htmlspecialchars($row['nama'] ?? '') ?>"
                                                data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '') ?>"
                                                data-judul="<?= htmlspecialchars($row['judul'] ?? '') ?>"
                                                data-ppt="<?= htmlspecialchars($row['berkas']['ppt'] ?? '') ?>"
                                                data-makalah="<?= htmlspecialchars($row['berkas']['makalah'] ?? '') ?>"
                                                title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if (!$isAccepted && !$isRejected): ?>
                                            <button class="btn btn-sm btn-action bg-success-subtle text-success border-0 rounded-3 d-inline-flex align-items-center justify-content-center btn-accept-judul"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>"
                                                    title="Terima Judul">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 d-inline-flex align-items-center justify-content-center btn-reject-judul"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>"
                                                    title="Tolak Judul">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 d-inline-flex align-items-center justify-content-center btn-send-message"
                                                data-id="<?= $row['id'] ?>"
                                                data-userid="<?= $row['id_mahasiswa'] ?>"
                                                title="Kirim Pesan/Revisi">
                                            <i class="bi bi-chat-dots"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php $i++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 2: Jadwal Presentasi -->
            <div class="tab-pane fade" id="tab-jadwal" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div class="position-relative" style="width: 280px;">
                        <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="searchJadwal" class="form-control rounded-3 ps-5" placeholder="Cari nama atau stambuk...">
                    </div>
                    <div class="d-flex gap-3">
                        <button class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" id="btnAddJadwal">
                            <i class="bi bi-plus-circle"></i> Tambah Jadwal
                        </button>
                        <button class="btn btn-success bg-gradient border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" id="btnBulkJadwal">
                            <i class="bi bi-calendar-plus"></i> Bulk Schedule
                        </button>
                    </div>
                </div>

                <div class="table-responsive rounded-4 shadow-sm">
                    <table class="table table-hover align-middle mb-0" id="tableJadwal">
                        <thead class="table-primary">
                            <tr>
                                <th class="fw-semibold text-uppercase small">No</th>
                                <th class="fw-semibold text-uppercase small">Nama Lengkap</th>
                                <th class="fw-semibold text-uppercase small">Stambuk</th>
                                <th class="fw-semibold text-uppercase small">Judul</th>
                                <th class="fw-semibold text-uppercase small">Ruangan</th>
                                <th class="fw-semibold text-uppercase small">Tanggal</th>
                                <th class="fw-semibold text-uppercase small">Waktu</th>
                                <th class="fw-semibold text-uppercase small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="jadwalTableBody">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="detailPengajuanModal" tabindex="-1" aria-labelledby="detailPengajuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold" id="detailPengajuanModalLabel">
                    <i class="bi bi-person-badge me-2"></i>Detail Presentasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <strong class="text-secondary">Nama:</strong>
                    <p class="mb-0 text-dark" id="detailNama">-</p>
                </div>
                <div class="mb-3">
                    <strong class="text-secondary">Stambuk:</strong>
                    <p class="mb-0 text-dark" id="detailStambuk">-</p>
                </div>
                <div class="mb-3">
                    <strong class="text-secondary">Judul:</strong>
                    <p class="mb-0 text-dark" id="detailJudul">-</p>
                </div>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" id="btnDownloadPpt">
                    <i class="bi bi-file-earmark-ppt"></i> Unduh PPT
                </button>
                <button type="button" class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" id="btnDownloadMakalah">
                    <i class="bi bi-file-earmark-pdf"></i> Unduh Makalah
                </button>
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Send Message -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold" id="sendMessageModalLabel">
                    <i class="bi bi-chat-dots me-2"></i>Kirim Pesan Revisi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formSendMessage">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pesan untuk Mahasiswa:</label>
                        <textarea class="form-control rounded-3 border-2" id="messageContent" rows="4" required
                                  placeholder="Tuliskan pesan atau catatan revisi..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formSendMessage" class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-send"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold" id="addJadwalModalLabel">
                    <i class="bi bi-calendar-plus me-2"></i>Tambah Jadwal Presentasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddJadwal">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pilih Mahasiswa:</label>
                        <select class="form-select rounded-3 border-2" id="selectMahasiswa" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pilih Ruangan:</label>
                        <select class="form-select rounded-3 border-2" id="selectRuangan" required>
                            <option value="">-- Pilih Ruangan --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Tanggal:</label>
                        <input type="date" class="form-control rounded-3 border-2" id="inputTanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Waktu:</label>
                        <input type="time" class="form-control rounded-3 border-2" id="inputWaktu" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formAddJadwal" class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bulk Schedule -->
<div class="modal fade" id="bulkJadwalModal" tabindex="-1" aria-labelledby="bulkJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold" id="bulkJadwalModalLabel">
                    <i class="bi bi-calendar-plus me-2"></i>Bulk Schedule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formBulkJadwal">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Pilih Mahasiswa (multiple):</label>
                                <div class="border border-2 rounded-3 p-2 bg-light" style="max-height: 250px; overflow-y: auto;" id="bulkMahasiswaList">
                                    <!-- Loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Pilih Ruangan:</label>
                                <select class="form-select rounded-3 border-2" id="bulkRuangan" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Tanggal:</label>
                                <input type="date" class="form-control rounded-3 border-2" id="bulkTanggal" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Waktu Mulai:</label>
                                <input type="time" class="form-control rounded-3 border-2" id="bulkWaktuMulai" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Durasi per Orang (menit):</label>
                                <input type="number" class="form-control rounded-3 border-2" id="bulkDurasi" value="15" min="5" max="60" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formBulkJadwal" class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check-lg"></i> Simpan Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold" id="editJadwalModalLabel">
                    <i class="bi bi-pencil me-2"></i>Edit Jadwal Presentasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditJadwal">
                    <input type="hidden" id="editJadwalId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Mahasiswa:</label>
                        <input type="text" class="form-control rounded-3 border-2 bg-light" id="editMahasiswaNama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pilih Ruangan:</label>
                        <select class="form-select rounded-3 border-2" id="editRuangan" required>
                            <option value="">-- Pilih Ruangan --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Tanggal:</label>
                        <input type="date" class="form-control rounded-3 border-2" id="editTanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Waktu:</label>
                        <input type="time" class="form-control rounded-3 border-2" id="editWaktu" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formEditJadwal" class="btn btn-primary bg-gradient-primary border-0 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check-lg"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    const APP_URL = '<?= APP_URL ?>';
    let currentMessageId = null;
    let ruanganData = [];

    // Tab Navigation
    $('.tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#tab-' + tab).addClass('active');

        if (tab === 'jadwal') {
            loadJadwalData();
        }
    });



    // Search functionality
    $('#searchPengajuan').on('input', function() {
        const term = $(this).val().toLowerCase();
        $('#tablePengajuan tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(term));
        });
    });

    $('#searchJadwal').on('input', function() {
        const term = $(this).val().toLowerCase();
        $('#tableJadwal tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(term));
        });
    });

    // Detail Pengajuan - Bootstrap Modal API
    $('.btn-detail-pengajuan').on('click', function() {
        $('#detailNama').text($(this).data('nama'));
        $('#detailStambuk').text($(this).data('stambuk'));
        $('#detailJudul').text($(this).data('judul'));
        $('#btnDownloadPpt').data('url', $(this).data('ppt'));
        $('#btnDownloadMakalah').data('url', $(this).data('makalah'));
        const modal = new bootstrap.Modal(document.getElementById('detailPengajuanModal'));
        modal.show();
    });

    $('#btnDownloadPpt').on('click', function() {
        const ppt = $(this).data('url');
        if (ppt) window.location.href = APP_URL + '/res/pptUser/' + ppt;
        else showAlert('PPT tidak tersedia', false);
    });

    $('#btnDownloadMakalah').on('click', function() {
        const makalah = $(this).data('url');
        if (makalah) window.location.href = APP_URL + '/res/makalahUser/' + makalah;
        else showAlert('Makalah tidak tersedia', false);
    });

    // Accept Judul
    $('.btn-accept-judul').on('click', function() {
        const userid = $(this).data('userid');
        if (confirm('Terima judul presentasi mahasiswa ini?')) {
            $.post(APP_URL + '/updatestatus', { id: userid, status: 1 }, function(res) {
                if (res.status === 'success') showAlert('Judul berhasil diterima!');
                else showAlert(res.message || 'Gagal menerima judul', false);
            }, 'json');
        }
    });

    // Reject Judul
    $('.btn-reject-judul').on('click', function() {
        const userid = $(this).data('userid');
        if (confirm('Tolak judul presentasi mahasiswa ini? Mahasiswa akan diminta merevisi judulnya.')) {
            $.post(APP_URL + '/updatestatus', { id: userid, status: 2 }, function(res) {
                if (res.status === 'success') showAlert('Judul ditolak. Mahasiswa akan diminta revisi.');
                else showAlert(res.message || 'Gagal menolak judul', false);
            }, 'json');
        }
    });

    // Send Message - Bootstrap Modal API
    $('.btn-send-message').on('click', function() {
        currentMessageId = $(this).data('id');
        $('#messageContent').val('');
        const modal = new bootstrap.Modal(document.getElementById('sendMessageModal'));
        modal.show();
    });

    $('#formSendMessage').on('submit', function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatepresentasi', {
            id: currentMessageId,
            message: $('#messageContent').val()
        }, function(res) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('sendMessageModal'));
            if (modal) modal.hide();
            if (res.status === 'success') showAlert('Pesan berhasil dikirim!');
            else showAlert(res.message || 'Gagal mengirim pesan', false);
        }, 'json');
    });

    // Load Ruangan
    function loadRuangan() {
        $.post(APP_URL + '/getallruangan', function(res) {
            if (res.status === 'success') {
                ruanganData = res.data;
                let options = '<option value="">-- Pilih Ruangan --</option>';
                res.data.forEach(r => {
                    options += `<option value="${r.id}">${r.nama}</option>`;
                });
                $('#selectRuangan, #bulkRuangan, #editRuangan').html(options);
            }
        }, 'json');
    }

    // Load Available Mahasiswa
    function loadAvailableMahasiswa() {
        $.post(APP_URL + '/getavailablemahasiswa', function(res) {
            if (res.status === 'success') {
                let options = '<option value="">-- Pilih Mahasiswa --</option>';
                let checkboxes = '';
                res.data.forEach((m, index) => {
                    options += `<option value="${m.id_presentasi}">${m.nama_lengkap} - ${m.stambuk}</option>`;
                    checkboxes += `
                        <div class="multi-select-item" data-id="${m.id_presentasi}">
                            <input type="checkbox" id="bulk_mhs_${index}" value="${m.id_presentasi}" data-nama="${m.nama_lengkap}">
                            <label for="bulk_mhs_${index}">${m.nama_lengkap} - ${m.stambuk}</label>
                        </div>`;
                });
                $('#selectMahasiswa').html(options);

                if (res.data.length === 0) {
                    $('#bulkMahasiswaList').html('<p class="text-muted text-center py-3">Tidak ada mahasiswa yang tersedia untuk dijadwalkan</p>');
                } else {
                    $('#bulkMahasiswaList').html(checkboxes);

                    // Add click event to toggle checkbox when clicking on item
                    $('#bulkMahasiswaList .multi-select-item').on('click', function(e) {
                        if (e.target.tagName !== 'INPUT') {
                            const checkbox = $(this).find('input[type="checkbox"]');
                            checkbox.prop('checked', !checkbox.prop('checked'));
                        }
                        $(this).toggleClass('selected', $(this).find('input[type="checkbox"]').prop('checked'));
                    });

                    // Update selected class when checkbox changes
                    $('#bulkMahasiswaList input[type="checkbox"]').on('change', function() {
                        $(this).closest('.multi-select-item').toggleClass('selected', $(this).prop('checked'));
                    });
                }
            }
        }, 'json');
    }

    // Load Jadwal Data
    function loadJadwalData() {
        $.post(APP_URL + '/getjadwalpresentasi', function(res) {
            if (res.status === 'success') {
                let html = '';
                if (res.data.length === 0) {
                    html = '<tr><td colspan="8" class="text-center text-muted">Belum ada jadwal presentasi</td></tr>';
                } else {
                    res.data.forEach((j, i) => {
                        html += `
                            <tr>
                                <td>${i + 1}</td>
                                <td><strong>${j.nama_lengkap}</strong></td>
                                <td>${j.stambuk}</td>
                                <td>${j.judul || '-'}</td>
                                <td>${j.ruangan}</td>
                                <td>${formatDate(j.tanggal)}</td>
                                <td>${j.waktu}</td>
                                <td>
                                    <div class="d-flex gap-2 flex-nowrap align-items-center">
                                        <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 d-inline-flex align-items-center justify-content-center btn-edit-jadwal"
                                                data-id="${j.id}"
                                                data-nama="${j.nama_lengkap}"
                                                data-ruangan="${j.id_ruangan}"
                                                data-tanggal="${j.tanggal}"
                                                data-waktu="${j.waktu}"
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 d-inline-flex align-items-center justify-content-center btn-delete-jadwal"
                                                data-id="${j.id}"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                }
                $('#jadwalTableBody').html(html);
            }
        }, 'json');
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    // Add Jadwal Modal - Bootstrap Modal API
    $('#btnAddJadwal').on('click', function() {
        loadAvailableMahasiswa();
        loadRuangan();
        $('#formAddJadwal')[0].reset();
        $('#selectMahasiswa').prop('disabled', false);
        const modal = new bootstrap.Modal(document.getElementById('addJadwalModal'));
        modal.show();
    });

    // Reset dropdown on modal close
    $('#addJadwalModal').on('hidden.bs.modal', function() {
        $('#selectMahasiswa').prop('disabled', false);
    });

    $('#formAddJadwal').on('submit', function(e) {
        e.preventDefault();
        $.post(APP_URL + '/savejadwalpresentasi', {
            id_presentasi: $('#selectMahasiswa').val(),
            id_ruangan: $('#selectRuangan').val(),
            tanggal: $('#inputTanggal').val(),
            waktu: $('#inputWaktu').val()
        }, function(res) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addJadwalModal'));
            if (modal) modal.hide();
            if (res.status === 'success') {
                showAlert('Jadwal berhasil disimpan!');
                loadJadwalData();
            } else {
                showAlert(res.message || 'Gagal menyimpan jadwal', false);
            }
        }, 'json');
    });

    // Bulk Schedule Modal - Bootstrap Modal API
    $('#btnBulkJadwal').on('click', function() {
        loadAvailableMahasiswa();
        loadRuangan();
        $('#formBulkJadwal')[0].reset();
        const modal = new bootstrap.Modal(document.getElementById('bulkJadwalModal'));
        modal.show();
    });

    $('#formBulkJadwal').on('submit', function(e) {
        e.preventDefault();

        const selected = [];
        $('#bulkMahasiswaList input:checked').each(function() {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            showAlert('Pilih minimal satu mahasiswa', false);
            return;
        }

        const ruangan = $('#bulkRuangan').val();
        const tanggal = $('#bulkTanggal').val();
        const waktuMulai = $('#bulkWaktuMulai').val();
        const durasi = parseInt($('#bulkDurasi').val());

        if (!ruangan || !tanggal || !waktuMulai) {
            showAlert('Lengkapi semua field', false);
            return;
        }

        // Function to add minutes to time string
        function addMinutes(time, minutes) {
            const [hours, mins] = time.split(':').map(Number);
            const totalMins = hours * 60 + mins + minutes;
            const newHours = Math.floor(totalMins / 60) % 24;
            const newMins = totalMins % 60;
            return `${String(newHours).padStart(2, '0')}:${String(newMins).padStart(2, '0')}`;
        }

        // Save each mahasiswa with calculated time
        let successCount = 0;
        let errorCount = 0;
        let currentTime = waktuMulai;

        // Process sequentially using promises
        const savePromises = selected.map((id, index) => {
            const waktu = addMinutes(waktuMulai, durasi * index);
            return $.post(APP_URL + '/savejadwalpresentasi', {
                id_presentasi: id,
                id_ruangan: ruangan,
                tanggal: tanggal,
                waktu: waktu
            });
        });

        // Wait for all saves to complete
        Promise.all(savePromises.map(p => p.catch(e => e)))
            .then(results => {
                results.forEach(res => {
                    if (res && res.status === 'success') successCount++;
                    else errorCount++;
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById('bulkJadwalModal'));
                if (modal) modal.hide();

                if (errorCount === 0) {
                    showAlert(`${successCount} jadwal berhasil disimpan!`);
                    loadJadwalData();
                } else if (successCount > 0) {
                    showAlert(`${successCount} jadwal berhasil, ${errorCount} gagal`, false);
                    loadJadwalData();
                } else {
                    showAlert('Gagal menyimpan jadwal', false);
                }
            });
    });

    // Edit Jadwal - Bootstrap Modal API
    $(document).on('click', '.btn-edit-jadwal', function() {
        loadRuangan();
        $('#editJadwalId').val($(this).data('id'));
        $('#editMahasiswaNama').val($(this).data('nama'));
        $('#editTanggal').val($(this).data('tanggal'));
        $('#editWaktu').val($(this).data('waktu'));
        setTimeout(() => {
            $('#editRuangan').val($(this).data('ruangan'));
        }, 300);
        const modal = new bootstrap.Modal(document.getElementById('editJadwalModal'));
        modal.show();
    });

    $('#formEditJadwal').on('submit', function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatejadwalpresentasi', {
            id: $('#editJadwalId').val(),
            id_ruangan: $('#editRuangan').val(),
            tanggal: $('#editTanggal').val(),
            waktu: $('#editWaktu').val()
        }, function(res) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editJadwalModal'));
            if (modal) modal.hide();
            if (res.status === 'success') {
                showAlert('Jadwal berhasil diupdate!');
                loadJadwalData();
            } else {
                showAlert(res.message || 'Gagal update jadwal', false);
            }
        }, 'json');
    });

    // Delete Jadwal
    $(document).on('click', '.btn-delete-jadwal', function() {
        const btn = $(this);
        const id = btn.data('id');
        showConfirmDelete(function() {
            $.post(APP_URL + '/deletejadwalpresentasi', { id: id }, function(res) {
                if (res.status === 'success') {
                    showAlert('Jadwal berhasil dihapus!');
                    loadJadwalData();
                } else {
                    showAlert(res.message || 'Gagal hapus jadwal', false);
                }
            }, 'json');
        }, 'Yakin ingin menghapus jadwal ini?');
    });

    // Initial load
    loadRuangan();
});
</script>
