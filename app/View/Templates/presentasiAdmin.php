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

    /* Tab Navigation */
    .tab-nav {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .tab-btn {
        padding: 12px 24px;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }

    .tab-btn:hover {
        color: #2f66f6;
    }

    .tab-btn.active {
        color: #2f66f6;
        font-weight: 600;
    }

    .tab-btn.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: #2f66f6;
        border-radius: 3px 3px 0 0;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
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
    }

    .btn-bulk {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

    .btn-bulk:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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

    .btn-view {
        background: #e0f2fe;
        color: #0284c7;
    }

    .btn-view:hover {
        background: #0284c7;
        color: white;
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

    .btn-accept {
        background: #d1fae5;
        color: #059669;
    }

    .btn-accept:hover {
        background: #059669;
        color: white;
    }

    /* Badge Styles */
    /* Badge Styles - Deep Solid Compact */
    .badge-status {
        padding: 2px 8px; /* Compact padding */
        border-radius: 6px; /* Rounded rectangle */
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 80px; /* Reduced min-width */
        white-space: nowrap;
        text-transform: capitalize;
        letter-spacing: 0.3px;
        transition: opacity 0.2s ease;
        line-height: 1.5;
    }

    .badge-status:hover {
        opacity: 0.9;
    }

    .badge-status i {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    .badge-pending {
        background: #ffc107;
        color: #000;
    }

    .badge-accepted {
        background: #198754;
        color: #fff;
    }

    .badge-scheduled {
        background: #0d6efd;
        color: #fff;
    }

    .badge-rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-schedule {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .btn-schedule:hover {
        background: #1d4ed8;
        color: white;
    }

    .btn-reject {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-reject:hover {
        background: #dc2626;
        color: white;
    }

    /* Ensure Bootstrap Icons Display */
    .btn-action .bi {
        display: inline-block;
        vertical-align: middle;
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
    .modal-presentasi .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-presentasi .modal-header {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: #fff;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
        border: none;
    }

    .modal-presentasi .modal-title {
        font-weight: 600;
        font-size: 1.2rem;
    }

    .modal-presentasi .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-presentasi .modal-body {
        padding: 24px;
    }

    .modal-presentasi .modal-footer {
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
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2f66f6;
        box-shadow: 0 0 0 3px rgba(47, 102, 246, 0.1);
    }

    .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        cursor: pointer;
    }

    .form-select:focus {
        outline: none;
        border-color: #2f66f6;
    }

    /* Multi Select */
    .multi-select-container {
        max-height: 250px;
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

    .multi-select-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #2f66f6;
        flex-shrink: 0;
    }

    .multi-select-item label {
        cursor: pointer;
        margin: 0;
        flex: 1;
        font-size: 0.9rem;
        color: #334155;
    }

    /* Alert Modal */
    .alert-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1100;
        justify-content: center;
        align-items: center;
    }

    .alert-modal.show {
        display: flex;
    }

    .alert-content {
        background: #fff;
        border-radius: 16px;
        padding: 32px;
        text-align: center;
        max-width: 400px;
        animation: scaleIn 0.3s ease;
    }

    .alert-content img {
        width: 80px;
        height: 80px;
        margin-bottom: 16px;
    }

    .alert-content p {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
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

    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
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
        .tab-nav {
            overflow-x: auto;
        }
    }
</style>

<main>
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="bi bi-easel"></i>
            Manajemen Presentasi
        </h1>
        <p class="subtitle">Kelola pengajuan judul dan jadwal presentasi mahasiswa</p>
    </div>

    <!-- Card Content -->
    <div class="card-content">
        <!-- Tab Navigation -->
        <div class="tab-nav">
            <button class="tab-btn active" data-tab="pengajuan">
                <i class="bi bi-file-text"></i> Pengajuan Judul
            </button>
            <button class="tab-btn" data-tab="jadwal">
                <i class="bi bi-calendar-event"></i> Jadwal Presentasi
            </button>
        </div>

        <!-- Tab 1: Pengajuan Judul -->
        <div class="tab-content active" id="tab-pengajuan">
            <div class="table-controls">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchPengajuan" placeholder="Cari nama atau stambuk...">
                </div>
            </div>

            <?php if (empty($mahasiswaList)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>Belum Ada Pengajuan</h3>
                    <p>Data pengajuan judul akan muncul setelah mahasiswa mengajukan judul presentasi</p>
                </div>
            <?php else: ?>
                <table class="data-table" id="tablePengajuan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Stambuk</th>
                            <th>Judul Presentasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
                                <td><?= $i ?></td>
                                <td><strong><?= htmlspecialchars($row['nama'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                <td>
                                    <span class="badge-status <?= $badgeClass ?>">
                                        <?= $badgeText ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-view btn-detail-pengajuan"
                                                data-nama="<?= htmlspecialchars($row['nama'] ?? '') ?>"
                                                data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '') ?>"
                                                data-judul="<?= htmlspecialchars($row['judul'] ?? '') ?>"
                                                data-ppt="<?= htmlspecialchars($row['berkas']['ppt'] ?? '') ?>"
                                                data-makalah="<?= htmlspecialchars($row['berkas']['makalah'] ?? '') ?>"
                                                title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if (!$isAccepted && !$isRejected): ?>
                                            <button class="btn-action btn-accept btn-accept-judul"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>"
                                                    title="Terima Judul">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn-action btn-reject btn-reject-judul"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>"
                                                    title="Tolak Judul">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-action btn-edit btn-send-message"
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
            <?php endif; ?>
        </div>

        <!-- Tab 2: Jadwal Presentasi -->
        <div class="tab-content" id="tab-jadwal">
            <div class="table-controls">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchJadwal" placeholder="Cari nama atau stambuk...">
                </div>
                <div style="display: flex; gap: 12px;">
                    <button class="btn-add" id="btnAddJadwal">
                        <i class="bi bi-plus-circle"></i> Tambah Jadwal
                    </button>
                    <button class="btn-bulk" id="btnBulkJadwal">
                        <i class="bi bi-calendar-plus"></i> Bulk Schedule
                    </button>
                </div>
            </div>

            <table class="data-table" id="tableJadwal">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Stambuk</th>
                        <th>Judul</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="jadwalTableBody">
                    <!-- Data loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Detail Pengajuan -->
<div class="modal fade modal-presentasi" id="detailPengajuanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge"></i> Detail Presentasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nama:</strong> <span id="detailNama">-</span></p>
                <p><strong>Stambuk:</strong> <span id="detailStambuk">-</span></p>
                <p><strong>Judul:</strong> <span id="detailJudul">-</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnDownloadPpt">
                    <i class="bi bi-file-earmark-ppt"></i> Unduh PPT
                </button>
                <button type="button" class="btn btn-primary" id="btnDownloadMakalah">
                    <i class="bi bi-file-earmark-pdf"></i> Unduh Makalah
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Send Message -->
<div class="modal fade modal-presentasi" id="sendMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-chat-dots"></i> Kirim Pesan Revisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSendMessage">
                    <div class="form-group">
                        <label>Pesan untuk Mahasiswa:</label>
                        <textarea class="form-control" id="messageContent" rows="4" required
                                  placeholder="Tuliskan pesan atau catatan revisi..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formSendMessage" class="btn btn-primary">
                    <i class="bi bi-send"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade modal-presentasi" id="addJadwalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Tambah Jadwal Presentasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAddJadwal">
                    <div class="form-group">
                        <label>Pilih Mahasiswa:</label>
                        <select class="form-select" id="selectMahasiswa" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih Ruangan:</label>
                        <select class="form-select" id="selectRuangan" required>
                            <option value="">-- Pilih Ruangan --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal:</label>
                        <input type="date" class="form-control" id="inputTanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Waktu:</label>
                        <input type="time" class="form-control" id="inputWaktu" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formAddJadwal" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bulk Schedule -->
<div class="modal fade modal-presentasi" id="bulkJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Bulk Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formBulkJadwal">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pilih Mahasiswa (multiple):</label>
                                <div class="multi-select-container" id="bulkMahasiswaList">
                                    <!-- Loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pilih Ruangan:</label>
                                <select class="form-select" id="bulkRuangan" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal:</label>
                                <input type="date" class="form-control" id="bulkTanggal" required>
                            </div>
                            <div class="form-group">
                                <label>Waktu Mulai:</label>
                                <input type="time" class="form-control" id="bulkWaktuMulai" required>
                            </div>
                            <div class="form-group">
                                <label>Durasi per Orang (menit):</label>
                                <input type="number" class="form-control" id="bulkDurasi" value="15" min="5" max="60" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formBulkJadwal" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade modal-presentasi" id="editJadwalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Jadwal Presentasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditJadwal">
                    <input type="hidden" id="editJadwalId">
                    <div class="form-group">
                        <label>Mahasiswa:</label>
                        <input type="text" class="form-control" id="editMahasiswaNama" readonly>
                    </div>
                    <div class="form-group">
                        <label>Pilih Ruangan:</label>
                        <select class="form-select" id="editRuangan" required>
                            <option value="">-- Pilih Ruangan --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal:</label>
                        <input type="date" class="form-control" id="editTanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Waktu:</label>
                        <input type="time" class="form-control" id="editWaktu" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formEditJadwal" class="btn btn-primary">
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

    // Detail Pengajuan
    $('.btn-detail-pengajuan').on('click', function() {
        $('#detailNama').text($(this).data('nama'));
        $('#detailStambuk').text($(this).data('stambuk'));
        $('#detailJudul').text($(this).data('judul'));
        $('#btnDownloadPpt').data('url', $(this).data('ppt'));
        $('#btnDownloadMakalah').data('url', $(this).data('makalah'));
        $('#detailPengajuanModal').modal('show');
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

    // Send Message
    $('.btn-send-message').on('click', function() {
        currentMessageId = $(this).data('id');
        $('#messageContent').val('');
        $('#sendMessageModal').modal('show');
    });

    $('#formSendMessage').on('submit', function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatepresentasi', {
            id: currentMessageId,
            message: $('#messageContent').val()
        }, function(res) {
            $('#sendMessageModal').modal('hide');
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
                                    <div class="action-btns">
                                        <button class="btn-action btn-edit btn-edit-jadwal"
                                                data-id="${j.id}"
                                                data-nama="${j.nama_lengkap}"
                                                data-ruangan="${j.id_ruangan}"
                                                data-tanggal="${j.tanggal}"
                                                data-waktu="${j.waktu}"
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-action btn-delete btn-delete-jadwal"
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

    // Add Jadwal Modal
    $('#btnAddJadwal').on('click', function() {
        loadAvailableMahasiswa();
        loadRuangan();
        $('#formAddJadwal')[0].reset();
        $('#selectMahasiswa').prop('disabled', false);
        $('#addJadwalModal').modal('show');
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
            $('#addJadwalModal').modal('hide');
            if (res.status === 'success') {
                showAlert('Jadwal berhasil disimpan!');
            } else {
                showAlert(res.message || 'Gagal menyimpan jadwal', false);
            }
        }, 'json');
    });

    // Bulk Schedule Modal
    $('#btnBulkJadwal').on('click', function() {
        loadAvailableMahasiswa();
        loadRuangan();
        $('#formBulkJadwal')[0].reset();
        $('#bulkJadwalModal').modal('show');
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

                $('#bulkJadwalModal').modal('hide');

                if (errorCount === 0) {
                    showAlert(`${successCount} jadwal berhasil disimpan!`);
                } else if (successCount > 0) {
                    showAlert(`${successCount} jadwal berhasil, ${errorCount} gagal`, false);
                } else {
                    showAlert('Gagal menyimpan jadwal', false);
                }
            });
    });

    // Edit Jadwal
    $(document).on('click', '.btn-edit-jadwal', function() {
        loadRuangan();
        $('#editJadwalId').val($(this).data('id'));
        $('#editMahasiswaNama').val($(this).data('nama'));
        $('#editTanggal').val($(this).data('tanggal'));
        $('#editWaktu').val($(this).data('waktu'));
        setTimeout(() => {
            $('#editRuangan').val($(this).data('ruangan'));
        }, 300);
        $('#editJadwalModal').modal('show');
    });

    $('#formEditJadwal').on('submit', function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatejadwalpresentasi', {
            id: $('#editJadwalId').val(),
            id_ruangan: $('#editRuangan').val(),
            tanggal: $('#editTanggal').val(),
            waktu: $('#editWaktu').val()
        }, function(res) {
            $('#editJadwalModal').modal('hide');
            if (res.status === 'success') {
                showAlert('Jadwal berhasil diupdate!');
            } else {
                showAlert(res.message || 'Gagal update jadwal', false);
            }
        }, 'json');
    });

    // Delete Jadwal
    // Delete Jadwal
    $(document).on('click', '.btn-delete-jadwal', function() {
        const btn = $(this);
        const id = btn.data('id');
        showConfirmDelete(function() {
            $.post(APP_URL + '/deletejadwalpresentasi', { id: id }, function(res) {
                if (res.status === 'success') {
                    showAlert('Jadwal berhasil dihapus!');
                    btn.closest('tr').fadeOut(300, function() { $(this).remove(); });
                } else {
                    showAlert(res.message || 'Gagal hapus jadwal', false);
                }
            }, 'json');
        }, 'Yakin ingin menghapus jadwal ini?');
    });

    // Close alert on click
    $('#alertModal').on('click', function() {
        $(this).removeClass('show');
    });

    // Initial load
    loadRuangan();
});
</script>
