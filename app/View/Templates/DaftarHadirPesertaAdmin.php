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
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        background: #f5f7fa;
        min-height: 100vh;
    }

    main {
        padding: 0;
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
    }

    .page-header::after {
        content: "";
        position: absolute;
        right: -180px;
        top: 50%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translateY(-50%);
    }

    .page-header h1 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 600;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header .subtitle {
        margin: 8px 0 0 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        position: relative;
        z-index: 1;
    }

    /* Card Container */
    .card-content {
        background: #fff;
        border-radius: 0;
        padding: 24px;
        margin: 0;
        min-height: calc(100vh - 140px);
    }

    /* Table Controls */
    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        width: 280px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: #2f66f6;
        box-shadow: 0 0 0 3px rgba(47, 102, 246, 0.1);
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    /* Buttons */
    .btn-add {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(47, 102, 246, 0.3);
        color: white;
    }

    /* Table Styling */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .data-table thead th {
        background: #2f66f6;
        color: #fff;
        font-weight: 600;
        padding: 16px 20px;
        text-align: left;
        vertical-align: middle;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
    }

    .data-table tbody tr:nth-child(odd) {
        background-color: #f8fafc;
    }

    .data-table tbody tr:nth-child(even) {
        background-color: #fff;
    }

    .data-table tbody tr:hover {
        background-color: rgba(47, 102, 246, 0.08);
    }

    .data-table td {
        padding: 14px 20px;
        color: #475569;
        font-size: 0.95rem;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-subtitle {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
        align-items: center;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 1rem;
        padding: 0;
    }

    .btn-action i,
    .btn-action i.bi {
        font-size: 1.1rem;
        line-height: 1;
        display: inline-block !important;
        font-style: normal;
    }

    .btn-edit {
        background: #fef3c7;
        color: #d97706;
    }

    .btn-edit:hover {
        background: #d97706;
        color: white;
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #dc2626;
        color: white;
    }

    /* Badge Styles */
    .badge-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-hadir {
        background: #d1fae5;
        color: #047857;
    }

    .badge-alpha {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-izin {
        background: #fef3c7;
        color: #b45309;
    }

    .badge-empty {
        background: #f1f5f9;
        color: #64748b;
    }

    .badge-process {
        background: #dbeafe;
        color: #1d4ed8;
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

    /* Modal Styles */
    .modal-kehadiran .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-kehadiran .modal-header {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: #fff;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
        border: none;
    }

    .modal-kehadiran .modal-title {
        font-weight: 600;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-kehadiran .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-kehadiran .modal-body {
        padding: 24px;
    }

    .modal-kehadiran .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #334155;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: #2f66f6;
        box-shadow: 0 0 0 3px rgba(47, 102, 246, 0.1);
    }

    .form-select-custom {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        cursor: pointer;
    }

    .form-select-custom:focus {
        outline: none;
        border-color: #2f66f6;
    }

    /* Multi Select */
    .multi-select-container {
        max-height: 200px;
        overflow-y: auto;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px;
        background: #fff;
    }

    .multi-select-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        margin-bottom: 4px;
    }

    .multi-select-item:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }

    .multi-select-item.selected {
        background: #dbeafe;
        border-color: #2f66f6;
    }

    /* Avatar Large for Modal */
    .avatar-placeholder-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.5rem;
        margin: 0 auto 16px auto;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 1.3rem;
        color: #475569;
        margin-bottom: 8px;
    }

    /* DataTables Override */
    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 16px;
        color: #64748b;
        font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 16px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 8px 14px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        color: #64748b !important;
        font-weight: 500;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f8fafc !important;
        border-color: #2f66f6 !important;
        color: #2f66f6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2f66f6 !important;
        border-color: #2f66f6 !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }



    /* Responsive */
    @media (max-width: 768px) {
        .table-controls {
            flex-direction: column;
            align-items: stretch;
        }
        .search-box {
            width: 100%;
        }
        .page-header {
            padding: 25px 20px;
        }
        .page-header h1 {
            font-size: 1.4rem;
        }
        .card-content {
            padding: 16px;
        }
        .data-table thead th,
        .data-table td {
            padding: 12px 14px;
            font-size: 0.85rem;
        }
        .badge-status {
            font-size: 0.7rem;
            padding: 3px 8px;
        }
        .avatar-placeholder {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
        }
        .btn-action {
            width: 32px;
            height: 32px;
            min-width: 32px;
        }
        .user-subtitle {
            display: none;
        }
        

    }
</style>

<main>
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="bi bi-clipboard-check"></i>
            Monitoring Kehadiran
        </h1>
        <p class="subtitle">Pantau status kehadiran peserta seleksi secara real-time</p>
    </div>

    <!-- Card Content -->
    <div class="card-content">
        <!-- Table Controls -->
        <div class="table-controls">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchKehadiran" placeholder="Cari nama atau stambuk...">
            </div>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                <i class="bi bi-plus-circle"></i> Tambah Data
            </button>
        </div>

        <?php if (empty($absensiList)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3>Belum Ada Data Kehadiran</h3>
                <p>Data kehadiran akan muncul setelah Anda menambahkan peserta</p>
            </div>
        <?php else: ?>
            <table class="data-table" id="monitoringTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Stambuk</th>
                        <th>Tes Tertulis</th>
                        <th>Presentasi</th>
                        <th>Wawancara I</th>
                        <th>Wawancara II</th>
                        <th>Wawancara III</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($absensiList as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-placeholder">
                                    <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                </div>
                                <div class="user-info">
                                    <span class="user-name"><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                                    <span class="user-subtitle">Mahasiswa</span>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($row['stambuk']) ?></td>
                        <td><?= renderStatusBadge($row['absensi_tes_tertulis']) ?></td>
                        <td><?= renderStatusBadge($row['absensi_presentasi']) ?></td>
                        <td><?= renderStatusBadge($row['absensi_wawancara_I']) ?></td>
                        <td><?= renderStatusBadge($row['absensi_wawancara_II']) ?></td>
                        <td><?= renderStatusBadge($row['absensi_wawancara_III']) ?></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-action btn-edit open-detail"
                                        title="Edit"
                                        data-id="<?= $row['id'] ?>"
                                        data-userid="<?= $row['id'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                        data-stambuk="<?= $row['stambuk'] ?>"
                                        data-absensiwawancarai="<?= $row['absensi_wawancara_I'] ?? '' ?>"
                                        data-absensiwawancaraii="<?= $row['absensi_wawancara_II'] ?? '' ?>"
                                        data-absensiwawancaraiii="<?= $row['absensi_wawancara_III'] ?? '' ?>"
                                        data-absensitestertulis="<?= $row['absensi_tes_tertulis'] ?? '' ?>"
                                        data-absensipresentasi="<?= $row['absensi_presentasi'] ?? '' ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action btn-delete btn-delete-attendance"
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
        <?php endif; ?>
    </div>
</main>

<?php
// Inline helper with XSS protection
function renderStatusBadge($val) {
    // Handle empty/null values
    if (!$val || trim($val) === '' || $val === '-') {
        return '<span class="badge-status badge-empty">Belum Ada</span>';
    }

    // Sanitize input first
    $sanitized = htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
    $v = strtolower($sanitized);

    // Case-insensitive status matching
    $statusMap = [
        'hadir' => ['class' => 'badge-hadir', 'label' => 'Hadir'],
        'alpha' => ['class' => 'badge-alpha', 'label' => 'Alpha'],
        'tidak hadir' => ['class' => 'badge-alpha', 'label' => 'Tidak Hadir'],
        'izin' => ['class' => 'badge-izin', 'label' => 'Izin'],
        'sakit' => ['class' => 'badge-izin', 'label' => 'Sakit'],
        'process' => ['class' => 'badge-process', 'label' => 'Process']
    ];

    // Find matching status
    if (isset($statusMap[$v])) {
        $status = $statusMap[$v];
        return '<span class="badge-status ' . $status['class'] . '">' . $status['label'] . '</span>';
    }

    // Unknown status - show as process with sanitized value
    return '<span class="badge-status badge-process">' . ucfirst($sanitized) . '</span>';
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
                        <div class="col-md-4">
                            <select class="form-select form-select-custom" id="absensiWawancara3">
                                <option value="" selected>Wawancara III...</option>
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
                    <div class="col-4">
                        <label class="small text-muted mb-1">Wawancara III</label>
                        <select id="wawancaraIII" class="form-select form-select-custom">
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
            wawancara3: $('#absensiWawancara3').val()
        };

        $.ajax({
            url: APP_URL + "/absensi",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function(res) {
                if(res.status === 'success') {
                    showAlert('Data kehadiran berhasil disimpan!');
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

        if (!confirm(`Hapus data kehadiran untuk ${nama}?`)) {
            return;
        }

        $.ajax({
            url: APP_URL + "/deleteabsensi",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ id: id }),
            success: function(res) {
                if(res.status === 'success') {
                    showAlert('Data kehadiran berhasil dihapus!');
                } else {
                    showAlert(res.message || 'Gagal menghapus data', false);
                }
            },
            error: function(xhr, status, error) {
                showAlert('Gagal menghubungi server', false);
            }
        });
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
        $('#wawancaraIII').val(btn.data('absensiwawancaraiii') || '');

        modal.modal('show');
    });

    $('#saveDetailAbsensi').click(function() {
        const data = {
            id: $('#detailUserId').val(),
            tesTertulis: $('#tesTertulis').val(),
            presentasi: $('#presentasi').val(),
            wawancaraI: $('#wawancaraI').val(),
            wawancaraII: $('#wawancaraII').val(),
            wawancaraIII: $('#wawancaraIII').val()
        };

        $.ajax({
            url: APP_URL + "/updateabsensi",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function(res) {
                if(res.status === 'success') {
                    showAlert('Data kehadiran berhasil diperbarui!');
                } else {
                    showAlert(res.message || 'Terjadi kesalahan', false);
                }
            },
            error: function(xhr, status, error) {
                showAlert('Gagal menghubungi server', false);
            }
        });
    });
});
</script>
