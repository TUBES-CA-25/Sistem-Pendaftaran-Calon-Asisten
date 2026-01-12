<?php
/**
 * Daftar Peserta View
 * 
 * Data yang diterima dari controller:
 * @var array $mahasiswaList - Daftar mahasiswa
 * @var array $result - Result mahasiswa
 */
$mahasiswaList = $mahasiswaList ?? [];
$result = $result ?? [];
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f5f7fa;
        min-height: 100vh;
    }

    main {
        padding: 0;
        margin: -20px -20px -20px -20px;
        width: calc(100% + 40px);
    }

    /* Page Header - Match Dashboard Admin Style (Larger) */
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

    .page-header h1 i {
        font-size: 1.7rem;
    }

    .page-header .subtitle {
        margin: 0;
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
        min-height: calc(100vh - 120px);
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

    .entries-select {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
        color: #64748b;
    }

    .entries-select select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.875rem;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .entries-select select:focus {
        border-color: #2f66f6;
        outline: none;
    }

    .search-box {
        position: relative;
        width: 280px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 2px solid #e2e8f0;
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
        color: #94a3b8;
    }

    /* Table Responsive Container */
    .table-responsive {
        overflow-x: auto;
        border-radius: 12px;
        padding-bottom: 20px; /* Space for bottom shadow */
    }

    /* Data Table Styling (Reference: PresentasiAdmin.php) */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05); /* Increased bottom presence */
        margin-bottom: 4px;
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
        border: none;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .data-table tbody tr:nth-child(odd) {
        background-color: #f8fafc;
    }

    .data-table tbody tr:hover {
        background-color: rgba(47, 102, 246, 0.08) !important;
    }

    .data-table td {
        padding: 14px 20px;
        color: #475569;
        font-size: 0.95rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table td:first-child {
        text-align: center;
        font-weight: 600;
        color: #2f66f6;
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }

    .user-name {
        font-weight: bold;
        color: #212529;
        margin: 0;
        font-size: 0.9rem;
    }

    /* Badge Styles - Deep Solid Compact */
    .badge-status {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 90px;
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

    .badge-diterima {
        background: #198754;
        color: #fff;
    }

    .badge-process {
        background: #0d6efd;
        color: #fff;
    }

    .badge-ditolak {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-pending {
        background: #ffc107;
        color: #000;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: center;
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

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-view {
        background: #e0f2fe;
        color: #0284c7;
    }

    .btn-view:hover {
        background: #0284c7;
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

    /* DataTables Override */
    .dataTables_wrapper .dataTables_info {
        color: #6c757d;
        font-size: 0.875rem;
        padding-top: 16px;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 16px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6 !important;
        background: white !important;
        color: #2f66f6 !important;
        font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2f66f6 !important;
        color: white !important;
        border-color: #2f66f6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f0f5ff !important;
        color: #2f66f6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #1e4fd8 !important;
        color: white !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        main {
            margin: -20px -20px 0 -20px;
            width: calc(100% + 40px);
        }

        .page-header {
            padding: 25px 20px;
        }

        .page-header h1 {
            font-size: 1.4rem;
        }

        .card-table {
            padding: 16px;
        }

        .table-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            min-width: 100%;
        }

        .table > thead > tr > th {
            font-size: 0.7rem;
            padding: 10px 8px;
        }

        .table > tbody > tr > td {
            padding: 10px 8px;
            font-size: 0.8rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
        }

        .btn-action {
            width: 30px;
            height: 30px;
        }

        .badge-status {
            padding: 6px 12px;
            font-size: 0.7rem;
            min-width: 85px;
            border-radius: 6px;
        }

        .badge-status i {
            font-size: 0.75rem;
        }
    }
</style>

<main>
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-people-fill"></i> Daftar Peserta</h1>
        <p class="subtitle">Kelola data peserta pendaftaran calon asisten</p>
    </div>

    <!-- Table Card -->
    <div class="card-content">
        <!-- Table Controls -->
        <div class="table-controls">
            <div class="entries-select">
                <span>Show</span>
                <select id="entriesPerPage" class="form-select form-select-sm">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Search...">
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table id="daftarPesertaTable" class="data-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Nama</th>
                        <th>Judul Presentasi</th>
                        <th>Stambuk</th>
                        <th>Jurusan</th>
                        <th>Kelas</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($result as $row): ?>
                        <?php
                            // Determine status
                            $status = $row['status'] ?? 'pending';
                            $statusClass = 'badge-pending';
                            $statusText = 'Pending';
                            $statusIcon = 'bi-hourglass';

                            if (isset($row['berkas']['accepted'])) {
                                if ($row['berkas']['accepted'] == 1) {
                                    $statusClass = 'badge-diterima';
                                    $statusText = 'Diterima';
                                    $statusIcon = 'bi-check-circle-fill';
                                } elseif ($row['berkas']['accepted'] == 0) {
                                    $statusClass = 'badge-process';
                                    $statusText = 'Process';
                                    $statusIcon = 'bi-clock-fill';
                                }
                            }
                            
                            // Get photo path
                            $photoPath = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . ($row['berkas']['foto'] ?? 'default.png');
                        ?>
                        <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['idUser'] ?>">
                            <td><?= $i ?></td>
                            <td>
                                <div class="user-info">
                                    <img src="<?= $photoPath ?>" alt="Avatar" class="user-avatar" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png'">
                                    <p class="user-name"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></p>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($row['judul_presentasi'])): ?>
                                    <span class="text-dark"><?= htmlspecialchars($row['judul_presentasi']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jurusan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['kelas'] ?? '-') ?></td>
                            <td class="text-center">
                                <span class="badge-status <?= $statusClass ?>">
                                    <i class="bi <?= $statusIcon ?>"></i>
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-view" title="Lihat Detail" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal"
                                            data-id="<?= $row['id'] ?>"
                                            data-userid="<?= $row['idUser'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? '') ?>"
                                            data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '') ?>"
                                            data-jurusan="<?= htmlspecialchars($row['jurusan'] ?? '') ?>"
                                            data-kelas="<?= htmlspecialchars($row['kelas'] ?? '') ?>"
                                            data-alamat="<?= htmlspecialchars($row['alamat'] ?? '') ?>"
                                            data-tempat_lahir="<?= htmlspecialchars($row['tempat_lahir'] ?? '') ?>"
                                            data-notelp="<?= htmlspecialchars($row['notelp'] ?? '') ?>"
                                            data-tanggal_lahir="<?= htmlspecialchars($row['tanggal_lahir'] ?? '') ?>"
                                            data-jenis_kelamin="<?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?>"
                                            data-judul_presentasi="<?= htmlspecialchars($row['judul_presentasi'] ?? '') ?>"
                                            data-foto="<?= $row['berkas']['foto'] ?? '' ?>"
                                            data-cv="<?= $row['berkas']['cv'] ?? '' ?>"
                                            data-transkrip="<?= $row['berkas']['transkrip_nilai'] ?? '' ?>"
                                            data-surat="<?= $row['berkas']['surat_pernyataan'] ?? '' ?>"
                                            data-berkas_accepted="<?= $row['berkas']['accepted'] ?? '' ?>"
                                            data-makalah="<?= $row['presentasi']['makalah'] ?? '' ?>"
                                            data-ppt="<?= $row['presentasi']['ppt'] ?? '' ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn-action btn-delete" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Kirim Notifikasi -->
<div class="modal fade" id="addNotification" tabindex="-1" aria-labelledby="addNotificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addNotificationLabel">
                    <i class="bi bi-send me-2"></i>Kirim Notifikasi ke Peserta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addNotificationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mahasiswa" class="form-label fw-semibold">
                                    <i class="bi bi-person-plus me-1"></i>Pilih Peserta
                                </label>
                                <select class="form-select" id="mahasiswa">
                                    <option value="" disabled selected>-- Pilih Peserta --</option>
                                    <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                        <option value="<?= $mahasiswa['id'] ?>" data-userid="<?= $mahasiswa['idUser'] ?>">
                                            <?= htmlspecialchars($mahasiswa['stambuk']) ?> - <?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addMahasiswaButton">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" id="addAllMahasiswaButton">
                                        <i class="bi bi-people me-1"></i>Tambah Semua
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-people-fill me-1"></i>Peserta Terpilih
                                    <span class="badge bg-primary ms-1" id="selectedCount">0</span>
                                </label>
                                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush" id="selectedMahasiswaList">
                                        <li class="list-group-item text-muted text-center py-3">
                                            <i class="bi bi-inbox me-1"></i>Belum ada peserta dipilih
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notifMessage" class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Pesan Notifikasi
                        </label>
                        <textarea class="form-control" id="notifMessage" rows="4" placeholder="Tulis pesan notifikasi untuk peserta..." required></textarea>
                        <div class="form-text">Pesan ini akan dikirim ke semua peserta yang dipilih.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary" form="addNotificationForm">
                    <i class="bi bi-send me-1"></i>Kirim Notifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Peserta -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <!-- Header dengan Background Gradient -->
            <div class="position-relative" style="background: #2563EB; padding: 25px 30px 90px 30px;">
                <button type="button" class="btn-close btn-close-white position-absolute" style="top: 15px; right: 15px; opacity: 0.8; z-index: 20;" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <!-- Decorative Elements -->
                <div class="position-absolute" style="top: -40px; right: -40px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: 30px; left: -30px; width: 100px; height: 100px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div class="position-absolute" style="top: 20px; left: 30%; width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                
                <!-- Title -->
                <h5 class="text-white fw-semibold mb-0 position-relative" style="z-index: 10;">
                    <i class="bi bi-person-badge me-2"></i>Detail Peserta
                </h5>
            </div>
            
            <!-- Profile Card yang Overlap -->
            <div class="px-4" style="margin-top: -70px; position: relative; z-index: 10;">
                <div class="bg-white rounded-4 shadow p-4" style="border: 1px solid rgba(0,0,0,0.05);">
                    <div class="row align-items-center">
                        <!-- Photo Column -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <img id="modalFoto" src="" alt="Foto Peserta" 
                                     class="rounded-circle shadow-lg"
                                     style="width: 130px; height: 130px; object-fit: cover; border: 5px solid white; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                <span id="modalStatusIcon" class="position-absolute bottom-0 end-0 rounded-circle shadow" 
                                      style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                </span>
                            </div>
                        </div>
                        
                        <!-- Info Column -->
                        <div class="col-md-9">
                            <div class="d-flex flex-wrap align-items-start justify-content-between">
                                <div>
                                    <h3 class="fw-bold mb-1" id="modalNamaHeader" style="color: #1f2937; font-size: 1.5rem;">Nama Peserta</h3>
                                    <p class="text-muted mb-2" style="font-size: 0.95rem;">
                                        <i class="bi bi-credit-card-2-front me-1"></i>
                                        <span id="modalStambukHeader">-</span>
                                    </p>
                                    <span id="modalStatusBadge" class="badge rounded-pill px-4 py-2" style="font-size: 0.85rem; font-weight: 500;">
                                        Status
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Quick Stats Row -->
                            <div class="row g-2 mt-3">
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border: 1px solid #667eea20;">
                                        <i class="bi bi-mortarboard-fill d-block mb-1" style="font-size: 1.2rem; color: #667eea;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Jurusan</p>
                                        <p class="fw-semibold mb-0 text-truncate" id="modalJurusan" style="font-size: 0.8rem; color: #374151;" title="">-</p>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #10b98115 0%, #059b7015 100%); border: 1px solid #10b98120;">
                                        <i class="bi bi-door-open-fill d-block mb-1" style="font-size: 1.2rem; color: #10b981;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Kelas</p>
                                        <p class="fw-semibold mb-0" id="modalKelas" style="font-size: 0.8rem; color: #374151;">-</p>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #8b5cf615 0%, #7c3aed15 100%); border: 1px solid #8b5cf620;">
                                        <i class="bi bi-gender-ambiguous d-block mb-1" style="font-size: 1.2rem; color: #8b5cf6;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Gender</p>
                                        <p class="fw-semibold mb-0" id="modalJenis_kelamin" style="font-size: 0.8rem; color: #374151;">-</p>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #f59e0b15 0%, #d9770615 100%); border: 1px solid #f59e0b20;">
                                        <i class="bi bi-telephone-fill d-block mb-1" style="font-size: 1.2rem; color: #f59e0b;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Telepon</p>
                                        <p class="fw-semibold mb-0 text-truncate" id="modalNoTelp" style="font-size: 0.8rem; color: #374151;">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Body Content -->
            <div class="modal-body px-4 pb-4 pt-3">
                <div class="row g-3">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <!-- Biodata Section -->
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm" style="border: 1px solid #e5e7eb;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="bi bi-person-vcard text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #1f2937;">Biodata Peserta</h6>
                                    <small class="text-muted">Informasi personal</small>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 rounded-3" style="background: #f8fafc; border-left: 3px solid #667eea;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap</label>
                                        <p class="fw-semibold mb-0" id="modalNama" style="color: #1f2937; font-size: 1rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Stambuk/NIM</label>
                                        <p class="fw-medium mb-0" id="modalStambuk" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Tempat Lahir</label>
                                        <p class="fw-medium mb-0" id="modalTempat_lahir" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Lahir</label>
                                        <p class="fw-medium mb-0" id="modalTanggal_lahir" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Jenis Kelamin</label>
                                        <p class="fw-medium mb-0" id="modalJenisKelaminDetail" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="bi bi-geo-alt me-1"></i>Alamat
                                        </label>
                                        <p class="fw-medium mb-0" id="modalAlamat" style="color: #374151; font-size: 0.9rem; line-height: 1.5;">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <!-- Presentasi Section -->
                        <div class="bg-white rounded-4 p-4 mb-3 shadow-sm" id="presentasiSection" style="border: 1px solid #e5e7eb;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #10b981 0%, #059b70 100%);">
                                    <i class="bi bi-easel text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #1f2937;">Presentasi</h6>
                                    <small class="text-muted">Materi presentasi peserta</small>
                                </div>
                            </div>
                            
                            <div class="p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, #10b98110 0%, #059b7010 100%); border: 1px solid #10b98130;">
                                <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Judul Presentasi</label>
                                <p class="fw-semibold mb-0" id="modalJudulPresentasi" style="color: #1f2937; font-size: 0.95rem;">-</p>
                            </div>
                            
                            <div class="d-flex gap-2 flex-wrap" id="presentasiButtons">
                                <button type="button" class="btn btn-sm px-3 py-2" id="downloadMakalahButton" data-download-url="" style="display: none; background: linear-gradient(135deg, #10b981 0%, #059b70 100%); color: white; border: none; border-radius: 10px;">
                                    <i class="bi bi-file-earmark-text me-2"></i>Download Makalah
                                </button>
                                <button type="button" class="btn btn-sm px-3 py-2" id="downloadPptButton" data-download-url="" style="display: none; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; border-radius: 10px;">
                                    <i class="bi bi-file-earmark-slides me-2"></i>Download PPT
                                </button>
                                <span id="noPresentasiFiles" class="text-muted fst-italic" style="font-size: 0.85rem; display: none;">
                                    <i class="bi bi-info-circle me-1"></i>Belum ada file presentasi
                                </span>
                            </div>
                        </div>
                        
                        <!-- Berkas Section -->
                        <div class="bg-white rounded-4 p-4 shadow-sm" style="border: 1px solid #e5e7eb;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                    <i class="bi bi-folder2-open text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #1f2937;">Berkas Pendaftaran</h6>
                                    <small class="text-muted">Dokumen yang diunggah</small>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn w-100 p-3 text-start position-relative berkas-btn" id="downloadFotoButton" data-download-url="" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: #dbeafe;">
                                                <i class="bi bi-image" style="color: #2563eb; font-size: 1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.85rem; color: #374151;">Foto</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">Pas foto</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%); color: #9ca3af;"></i>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn w-100 p-3 text-start position-relative berkas-btn" id="downloadCVButton" data-download-url="" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: #ede9fe;">
                                                <i class="bi bi-file-person" style="color: #7c3aed; font-size: 1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.85rem; color: #374151;">CV</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">Curriculum Vitae</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%); color: #9ca3af;"></i>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn w-100 p-3 text-start position-relative berkas-btn" id="downloadTranskripButton" data-download-url="" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: #d1fae5;">
                                                <i class="bi bi-file-text" style="color: #059669; font-size: 1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.85rem; color: #374151;">Transkrip</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">Nilai akademik</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%); color: #9ca3af;"></i>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn w-100 p-3 text-start position-relative berkas-btn" id="downloadSuratButton" data-download-url="" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: #fef3c7;">
                                                <i class="bi bi-file-earmark-check" style="color: #d97706; font-size: 1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.85rem; color: #374151;">Surat</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">Pernyataan</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%); color: #9ca3af;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <input type="hidden" id="modalMahasiswaId" value="">
                <input type="hidden" id="modalUserId" value="">
                <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal" style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; color: #6b7280;">
                    <i class="bi bi-x-lg me-2"></i>Tutup
                </button>
                <button type="button" class="btn px-4 py-2" id="btnSendMessageToUser" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px;">
                    <i class="bi bi-envelope me-2"></i>Kirim Pesan
                </button>
                <button type="button" class="btn px-4 py-2" id="acceptButton" style="background: linear-gradient(135deg, #10b981 0%, #059b70 100%); color: white; border: none; border-radius: 10px;">
                    <i class="bi bi-check-circle me-2"></i>Verifikasi Berkas
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Detail Modal Styles */
    #detailModal .modal-content {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    #detailModal .badge-status {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .badge-diterima {
        background: #d1fae5 !important;
        color: #047857 !important;
    }

    .badge-process {
        background: #dbeafe !important;
        color: #1d4ed8 !important;
    }

    .badge-pending {
        background: #e2e8f0 !important;
        color: #64748b !important;
    }

    .badge-ditolak {
        background: #fee2e2 !important;
        color: #b91c1c !important;
    }
    
    #detailModal .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    #detailModal .berkas-btn:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    #detailModal .berkas-btn:hover .bi-download {
        color: #667eea !important;
    }
    
    #detailModal .rounded-4 {
        border-radius: 16px !important;
    }
    
    #detailModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    
    #detailModal .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    #detailModal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    #detailModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Status Icon Styles */
    .status-icon-verified {
        background: #10b981;
        color: white;
    }

    .status-icon-pending {
        background: #f59e0b;
        color: white;
    }

    .status-icon-none {
        background: #6b7280;
        color: white;
    }
    
    /* Animation */
    #detailModal .modal-dialog {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<!-- Modal Kirim Pesan Individual -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendMessageModalLabel">
                    <i class="bi bi-envelope me-2"></i>Kirim Pesan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kepada:</label>
                    <p class="mb-0" id="messageRecipient">-</p>
                </div>
                <div class="mb-3">
                    <label for="individualMessage" class="form-label fw-semibold">Pesan:</label>
                    <textarea class="form-control" id="individualMessage" rows="4" placeholder="Tulis pesan untuk peserta..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="messageUserId" value="">
                <input type="hidden" id="messageMahasiswaId" value="">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="sendIndividualMessage">
                    <i class="bi bi-send me-1"></i>Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-trash3 text-danger" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Apakah Anda yakin ingin menghapus data peserta ini?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash3 me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Load Custom JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/common.js"></script>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/daftarPeserta.js"></script>

<script>
// Use IIFE instead of DOMContentLoaded for AJAX-loaded content
(function() {
    console.log('Daftar Peserta script loaded');
    
    // Initialize DataTable
    var table = $('#daftar').DataTable({
        dom: 'rtip', // Remove default search and length selector
        pageLength: 10,
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        },
        columnDefs: [
            { orderable: false, targets: -1 } // Disable sorting on action column
        ]
    });

    // Custom entries per page selector
    $('#entriesPerPage').on('change', function() {
        table.page.len($(this).val()).draw();
    });

    // Custom search box
    $('#searchInput').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    // Store current row data
    var currentRowData = null;

    // Handle view detail button click
    document.querySelectorAll('.btn-view').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var data = this.dataset;
            
            // Store mahasiswa ID for accept button
            document.getElementById('modalMahasiswaId').value = data.id;
            document.getElementById('modalUserId').value = data.userid;
            currentRowData = {
                id: data.id,
                userId: data.userid,
                nama: data.nama,
                stambuk: data.stambuk
            };
            
            // Populate header
            document.getElementById('modalNamaHeader').textContent = data.nama || '-';
            document.getElementById('modalStambukHeader').textContent = data.stambuk || '-';
            
            // Populate modal fields (also used in info cards)
            document.getElementById('modalNama').textContent = data.nama || '-';
            document.getElementById('modalStambuk').textContent = data.stambuk || '-';
            document.getElementById('modalJurusan').textContent = data.jurusan || '-';
            document.getElementById('modalJurusan').title = data.jurusan || '-';
            document.getElementById('modalKelas').textContent = data.kelas || '-';
            document.getElementById('modalAlamat').textContent = data.alamat || '-';
            document.getElementById('modalTempat_lahir').textContent = data.tempat_lahir || '-';
            document.getElementById('modalTanggal_lahir').textContent = data.tanggal_lahir || '-';
            document.getElementById('modalJenis_kelamin').textContent = data.jenis_kelamin || '-';
            document.getElementById('modalJenisKelaminDetail').textContent = data.jenis_kelamin || '-';
            document.getElementById('modalNoTelp').textContent = data.notelp || '-';
            
            // Judul Presentasi
            var judulPresentasi = data.judul_presentasi;
            var presentasiSection = document.getElementById('presentasiSection');
            var noPresentasiFiles = document.getElementById('noPresentasiFiles');
            if (judulPresentasi && judulPresentasi.trim() !== '') {
                document.getElementById('modalJudulPresentasi').textContent = judulPresentasi;
                document.getElementById('modalJudulPresentasi').classList.remove('text-muted', 'fst-italic');
            } else {
                document.getElementById('modalJudulPresentasi').textContent = 'Belum diisi oleh peserta';
                document.getElementById('modalJudulPresentasi').classList.add('text-muted', 'fst-italic');
            }
            presentasiSection.style.display = 'block';
            
            // Status Badge and Status Icon
            var statusBadge = document.getElementById('modalStatusBadge');
            var statusIcon = document.getElementById('modalStatusIcon');
            var berkasAccepted = data.berkas_accepted;
            
            if (berkasAccepted == '1') {
                statusBadge.className = 'badge rounded-pill px-4 py-2 badge-diterima';
                statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Berkas Terverifikasi';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow status-icon-verified';
                statusIcon.innerHTML = '<i class="bi bi-check-lg"></i>';
                document.getElementById('acceptButton').style.display = 'none';
            } else if (berkasAccepted == '0') {
                statusBadge.className = 'badge rounded-pill px-4 py-2 badge-process';
                statusBadge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow status-icon-pending';
                statusIcon.innerHTML = '<i class="bi bi-clock"></i>';
                document.getElementById('acceptButton').style.display = 'inline-block';
            } else {
                statusBadge.className = 'badge rounded-pill px-4 py-2 badge-pending';
                statusBadge.innerHTML = '<i class="bi bi-file-earmark-x me-1"></i>Belum Upload Berkas';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow status-icon-none';
                statusIcon.innerHTML = '<i class="bi bi-x-lg"></i>';
                document.getElementById('acceptButton').style.display = 'none';
            }
            
            // Set photo
            var fotoUrl = data.foto ? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' + data.foto : '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
            document.getElementById('modalFoto').src = fotoUrl;
            document.getElementById('modalFoto').onerror = function() {
                this.src = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
            };

            // Set download URLs for berkas
            document.getElementById('downloadFotoButton').setAttribute('data-download-url', data.foto ? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' + data.foto : '');
            document.getElementById('downloadCVButton').setAttribute('data-download-url', data.cv ? '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/' + data.cv : '');
            document.getElementById('downloadTranskripButton').setAttribute('data-download-url', data.transkrip ? '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/' + data.transkrip : '');
            document.getElementById('downloadSuratButton').setAttribute('data-download-url', data.surat ? '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/' + data.surat : '');
            
            // Set download URLs for presentasi files
            var makalahBtn = document.getElementById('downloadMakalahButton');
            var pptBtn = document.getElementById('downloadPptButton');
            var hasPresentasiFiles = false;
            
            if (data.makalah) {
                makalahBtn.setAttribute('data-download-url', '/Sistem-Pendaftaran-Calon-Asisten/res/makalahUser/' + data.makalah);
                makalahBtn.style.display = 'inline-flex';
                hasPresentasiFiles = true;
            } else {
                makalahBtn.style.display = 'none';
            }
            
            if (data.ppt) {
                pptBtn.setAttribute('data-download-url', '/Sistem-Pendaftaran-Calon-Asisten/res/pptUser/' + data.ppt);
                pptBtn.style.display = 'inline-flex';
                hasPresentasiFiles = true;
            } else {
                pptBtn.style.display = 'none';
            }
            
            // Show/hide no files message
            if (noPresentasiFiles) {
                noPresentasiFiles.style.display = hasPresentasiFiles ? 'none' : 'inline-block';
            }
        });
    });

    // Handle send message button in detail modal
    document.getElementById('btnSendMessageToUser').addEventListener('click', function() {
        if (currentRowData) {
            // Close detail modal
            var detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
            if (detailModal) detailModal.hide();
            
            // Set data for send message modal
            document.getElementById('messageRecipient').textContent = currentRowData.stambuk + ' - ' + currentRowData.nama;
            document.getElementById('messageUserId').value = currentRowData.userId;
            document.getElementById('messageMahasiswaId').value = currentRowData.id;
            document.getElementById('individualMessage').value = '';
            
            // Show send message modal
            setTimeout(function() {
                var sendMessageModal = new bootstrap.Modal(document.getElementById('sendMessageModal'));
                sendMessageModal.show();
            }, 300);
        }
    });

    // Handle send individual message
    document.getElementById('sendIndividualMessage').addEventListener('click', function() {
        var mahasiswaId = document.getElementById('messageMahasiswaId').value;
        var message = document.getElementById('individualMessage').value;
        
        if (!message.trim()) {
            showAlert('Pesan tidak boleh kosong', false);
            return;
        }
        
        // Send notification
        fetch('/Sistem-Pendaftaran-Calon-Asisten/addallnotif', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                mahasiswaIds: [mahasiswaId],
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Pesan berhasil dikirim!', true);
                var modal = bootstrap.Modal.getInstance(document.getElementById('sendMessageModal'));
                if (modal) modal.hide();
            } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat mengirim pesan', false);
        });
    });

    // Handle download buttons
    document.querySelectorAll('[id^="download"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-download-url');
            if (url) {
                window.open(url, '_blank');
            } else {
                showAlert('File tidak tersedia', false);
            }
        });
    });

    // Quick verify button removed from action column
    // Verification is now handled only through the detail modal

    // Handle accept button in modal
    document.getElementById('acceptButton').addEventListener('click', function() {
        var mahasiswaId = document.getElementById('modalMahasiswaId').value;
        if (mahasiswaId) {
            if (confirm('Apakah Anda yakin ingin memverifikasi berkas ini?')) {
                verifyBerkas(mahasiswaId, null, true);
            }
        }
    });

    // Function to verify berkas
    function verifyBerkas(mahasiswaId, button, fromModal) {
        fetch('/Sistem-Pendaftaran-Calon-Asisten/acceptberkas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + mahasiswaId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Berkas berhasil diverifikasi!', true);
                
                // Update button state if from table
                if (button) {
                    button.classList.add('verified');
                    button.setAttribute('title', 'Berkas Terverifikasi');
                    button.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                    button.dataset.status = '1';
                }
                
                // Update status badge in table row
                var row = document.querySelector('tr[data-id="' + mahasiswaId + '"]');
                if (row) {
                    var statusBadge = row.querySelector('.badge-status');
                    if (statusBadge) {
                        statusBadge.className = 'badge-status badge-diterima';
                        statusBadge.textContent = 'Diterima';
                    }
                    
                    // Also update the view button data
                    var viewBtn = row.querySelector('.btn-view');
                    if (viewBtn) {
                        viewBtn.dataset.berkas_accepted = '1';
                    }
                }
                
                // Close modal if from modal
                if (fromModal) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                    if (modal) modal.hide();
                }
            } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memverifikasi berkas', false);
        });
    }

    // Handle delete button click
    // Handle delete button click
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var row = this.closest('tr');
            var userId = row.getAttribute('data-userid');
            
            showConfirmDelete(function() {
                fetch('/Sistem-Pendaftaran-Calon-Asisten/deletemahasiswa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + userId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Data berhasil dihapus!', true);
                        location.reload();
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat menghapus data', false);
                });
            }, 'Apakah Anda yakin ingin menghapus data peserta ini?<br>Tindakan ini tidak dapat dibatalkan.');
        });
    });

    // ============ NOTIFICATION FORM HANDLERS ============
    var selectedMahasiswa = [];
    
    // Update selected count
    function updateSelectedCount() {
        document.getElementById('selectedCount').textContent = selectedMahasiswa.length;
    }
    
    // Render selected mahasiswa list
    function renderSelectedMahasiswa() {
        var list = document.getElementById('selectedMahasiswaList');
        list.innerHTML = '';
        
        if (selectedMahasiswa.length === 0) {
            list.innerHTML = '<li class="list-group-item text-muted text-center py-3"><i class="bi bi-inbox me-1"></i>Belum ada peserta dipilih</li>';
        } else {
            selectedMahasiswa.forEach(function(mhs, index) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                li.innerHTML = '<span class="small">' + mhs.text + '</span>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" data-index="' + index + '">' +
                    '<i class="bi bi-x"></i></button>';
                list.appendChild(li);
            });
            
            // Add remove handlers
            list.querySelectorAll('button').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var idx = parseInt(this.dataset.index);
                    selectedMahasiswa.splice(idx, 1);
                    renderSelectedMahasiswa();
                    updateSelectedCount();
                });
            });
        }
        updateSelectedCount();
    }
    
    // Add single mahasiswa
    document.getElementById('addMahasiswaButton').addEventListener('click', function() {
        var select = document.getElementById('mahasiswa');
        var selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            var exists = selectedMahasiswa.some(function(m) { return m.id === selectedOption.value; });
            if (!exists) {
                selectedMahasiswa.push({
                    id: selectedOption.value,
                    text: selectedOption.textContent.trim()
                });
                renderSelectedMahasiswa();
            } else {
                showAlert('Peserta sudah dipilih', false);
            }
        } else {
            showAlert('Pilih peserta terlebih dahulu', false);
        }
    });
    
    // Add all mahasiswa
    document.getElementById('addAllMahasiswaButton').addEventListener('click', function() {
        var select = document.getElementById('mahasiswa');
        selectedMahasiswa = [];
        
        Array.from(select.options).forEach(function(option) {
            if (option.value) {
                selectedMahasiswa.push({
                    id: option.value,
                    text: option.textContent.trim()
                });
            }
        });
        renderSelectedMahasiswa();
    });
    
    // Submit notification form
    document.getElementById('addNotificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var message = document.getElementById('notifMessage').value;
        
        if (selectedMahasiswa.length === 0) {
            showAlert('Pilih peserta terlebih dahulu', false);
            return;
        }
        
        if (!message.trim()) {
            showAlert('Pesan tidak boleh kosong', false);
            return;
        }
        
        var mahasiswaIds = selectedMahasiswa.map(function(m) { return m.id; });
        
        fetch('/Sistem-Pendaftaran-Calon-Asisten/addallnotif', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                mahasiswaIds: mahasiswaIds,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Notifikasi berhasil dikirim ke ' + selectedMahasiswa.length + ' peserta!', true);
                var modal = bootstrap.Modal.getInstance(document.getElementById('addNotification'));
                if (modal) modal.hide();
                
                // Reset form
                selectedMahasiswa = [];
                renderSelectedMahasiswa();
                document.getElementById('notifMessage').value = '';
                document.getElementById('mahasiswa').selectedIndex = 0;
            } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat mengirim notifikasi', false);
        });
    });
    
    // Initialize
    renderSelectedMahasiswa();

    // Call custom initialization if available
    if (typeof window.initDaftarPeserta === 'function') {
        window.initDaftarPeserta();
    }
    
    console.log('Daftar Peserta script initialization complete');
})(); // IIFE - Executes immediately when script loads
</script>
