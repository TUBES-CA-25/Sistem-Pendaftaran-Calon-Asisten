<?php
/**
 * Daftar Nilai Tes Tertulis Admin View
 *
 * Data yang diterima dari controller:
 * @var array $nilai - Daftar nilai mahasiswa
 */
$nilai = $nilai ?? [];
?>
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
    .card-table {
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

    /* Stats Cards */
    .stats-row {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .stat-card {
        flex: 1;
        min-width: 150px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 16px 20px;
        border-left: 4px solid #2f66f6;
    }

    .stat-card.success {
        border-left-color: #10b981;
    }

    .stat-card.warning {
        border-left-color: #f59e0b;
    }

    .stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-card .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 4px;
    }

    /* Table Styling */
    .table-nilai {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .table-nilai thead th {
        background: #2f66f6;
        color: #fff;
        font-weight: 600;
        padding: 16px 20px;
        text-align: left;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-nilai tbody tr {
        transition: all 0.2s ease;
    }

    .table-nilai tbody tr:nth-child(odd) {
        background-color: #f8fafc;
    }

    .table-nilai tbody tr:nth-child(even) {
        background-color: #fff;
    }

    .table-nilai tbody tr:hover {
        background-color: rgba(47, 102, 246, 0.08);
    }

    .table-nilai td {
        padding: 14px 20px;
        color: #475569;
        font-size: 0.95rem;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Button Styles */
    .btn-detail {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-detail:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(47, 102, 246, 0.3);
    }

    /* Badge Styles */
    .badge-nilai {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-nilai.tinggi {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #047857;
    }

    .badge-nilai.sedang {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
    }

    .badge-nilai.rendah {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #b91c1c;
    }

    .badge-nilai.belum {
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        color: #475569;
    }

    /* Modal Styles */
    .modal-nilai {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.2s ease;
    }

    .modal-nilai.show {
        display: flex;
    }

    .modal-nilai-content {
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease;
    }

    .modal-nilai-header {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: #fff;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 16px 16px 0 0;
    }

    .modal-nilai-header h2 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .modal-nilai-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .modal-nilai-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .modal-nilai-body {
        padding: 24px;
    }

    /* Info Cards in Modal */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        border-left: 4px solid #2f66f6;
    }

    .info-card label {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-card span {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
    }

    /* Soal Jawaban Cards */
    .soal-section {
        margin-top: 24px;
    }

    .soal-section h5 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e2e8f0;
    }

    .soal-jawaban-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 16px;
    }

    .soal-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s ease;
    }

    .soal-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .soal-card .soal-number {
        background: #2f66f6;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .soal-card .question {
        font-size: 0.95rem;
        color: #334155;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .soal-card .answers {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .soal-card .answer-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 0.85rem;
    }

    .soal-card .answer-label {
        font-weight: 600;
        color: #64748b;
        min-width: 100px;
    }

    .soal-card .answer-correct {
        color: #10b981;
    }

    .soal-card .answer-wrong {
        color: #ef4444;
    }

    /* Form Input */
    .nilai-form-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 2px solid #e2e8f0;
    }

    .nilai-form-section h5 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
    }

    .nilai-input-group {
        display: flex;
        gap: 12px;
        align-items: stretch;
    }

    .nilai-input-group input {
        flex: 1;
        max-width: 200px;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .nilai-input-group input:focus {
        outline: none;
        border-color: #2f66f6;
        box-shadow: 0 0 0 3px rgba(47, 102, 246, 0.1);
    }

    .nilai-input-group button {
        padding: 12px 24px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nilai-input-group button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    /* Alert/Feedback Modal */
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

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
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

        .stats-row {
            flex-direction: column;
        }

        .soal-jawaban-grid {
            grid-template-columns: 1fr;
        }

        .nilai-input-group {
            flex-direction: column;
        }

        .nilai-input-group input {
            max-width: 100%;
        }
    }
</style>

<main>
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="bi bi-clipboard-data"></i>
            Daftar Nilai Tes Tertulis
        </h1>
        <p class="subtitle">Kelola dan lihat nilai tes tertulis mahasiswa</p>
    </div>

    <!-- Card Container -->
    <div class="card-table">
        <?php
        // Calculate statistics
        $totalMahasiswa = count($nilai);
        $nilaiTinggi = 0;
        $nilaiRendah = 0;
        $belumDinilai = 0;

        foreach ($nilai as $v) {
            $total = $v['total'] ?? null;
            if ($total === null || $total === '') {
                $belumDinilai++;
            } elseif ((int)$total >= 70) {
                $nilaiTinggi++;
            } else {
                $nilaiRendah++;
            }
        }
        ?>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value"><?= $totalMahasiswa ?></div>
                <div class="stat-label">Total Peserta</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value"><?= $nilaiTinggi ?></div>
                <div class="stat-label">Lulus (≥70)</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value"><?= $nilaiRendah ?></div>
                <div class="stat-label">Tidak Lulus (<70)</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $belumDinilai ?></div>
                <div class="stat-label">Belum Dinilai</div>
            </div>
        </div>

        <!-- Table Controls -->
        <div class="table-controls">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama atau stambuk...">
            </div>
        </div>

        <?php if (empty($nilai)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3>Belum Ada Data Nilai</h3>
                <p>Data nilai akan muncul setelah mahasiswa mengerjakan tes tertulis</p>
            </div>
        <?php else: ?>
            <!-- Data Table -->
            <table class="table-nilai" id="tableNilai">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Stambuk</th>
                        <th style="width: 140px;">Nilai Tes</th>
                        <th style="width: 140px;">Nilai Akhir</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($nilai as $value): ?>
                        <?php
                        $nilaiTes = $value['nilai'] ?? '-';
                        $nilaiTotal = $value['total'] ?? null;

                        // Determine badge class
                        $badgeClass = 'belum';
                        if ($nilaiTotal !== null && $nilaiTotal !== '') {
                            if ((int)$nilaiTotal >= 70) {
                                $badgeClass = 'tinggi';
                            } elseif ((int)$nilaiTotal >= 50) {
                                $badgeClass = 'sedang';
                            } else {
                                $badgeClass = 'rendah';
                            }
                        }
                        ?>
                        <tr>
                            <td class="text-center"><?= $i ?></td>
                            <td>
                                <strong><?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?></strong>
                            </td>
                            <td><?= htmlspecialchars($value['stambuk'] ?? '-') ?></td>
                            <td>
                                <span class="badge-nilai sedang"><?= htmlspecialchars($nilaiTes) ?></span>
                            </td>
                            <td>
                                <span class="badge-nilai <?= $badgeClass ?>">
                                    <?= $nilaiTotal !== null && $nilaiTotal !== '' ? htmlspecialchars($nilaiTotal) : 'Belum dinilai' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-detail"
                                        data-id="<?= htmlspecialchars($value['id'] ?? '') ?>"
                                        data-nama="<?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?>"
                                        data-stambuk="<?= htmlspecialchars($value['stambuk'] ?? '-') ?>"
                                        data-nilai="<?= htmlspecialchars($nilaiTes) ?>"
                                        data-total="<?= htmlspecialchars($nilaiTotal ?? '') ?>">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<!-- Detail Modal -->
<div id="detailModal" class="modal-nilai">
    <div class="modal-nilai-content">
        <div class="modal-nilai-header">
            <h2><i class="bi bi-person-badge"></i> Detail Nilai Mahasiswa</h2>
            <button class="modal-nilai-close" id="closeDetailModal">&times;</button>
        </div>
        <div class="modal-nilai-body">
            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <label>Nama Lengkap</label>
                    <span id="modalNama">-</span>
                </div>
                <div class="info-card">
                    <label>Stambuk</label>
                    <span id="modalStambuk">-</span>
                </div>
                <div class="info-card">
                    <label>Nilai Tes Tertulis</label>
                    <span id="modalNilai">-</span>
                </div>
                <div class="info-card">
                    <label>Nilai Akhir</label>
                    <span id="modalTotalNilai">-</span>
                </div>
            </div>

            <!-- Soal Jawaban Section -->
            <div class="soal-section">
                <h5><i class="bi bi-list-check"></i> Soal dan Jawaban</h5>
                <div id="soalJawabanList" class="soal-jawaban-grid">
                    <p class="text-muted">Memuat data...</p>
                </div>
            </div>

            <!-- Nilai Form -->
            <div class="nilai-form-section">
                <h5><i class="bi bi-pencil-square"></i> Input Nilai Akhir</h5>
                <form id="formNilaiAkhir">
                    <div class="nilai-input-group">
                        <input type="number"
                               id="nilaiAkhir"
                               placeholder="Masukkan nilai (0-100)"
                               min="0"
                               max="100"
                               required>
                        <button type="submit">
                            <i class="bi bi-check-lg"></i> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div id="alertModal" class="alert-modal">
    <div class="alert-content">
        <img id="alertGif" src="" alt="Status">
        <p id="alertMessage">-</p>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentMahasiswaId = null;

    // Search functionality
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#tableNilai tbody tr').each(function() {
            const nama = $(this).find('td:eq(1)').text().toLowerCase();
            const stambuk = $(this).find('td:eq(2)').text().toLowerCase();
            if (nama.includes(searchTerm) || stambuk.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Show alert modal
    function showAlert(message, isSuccess = true) {
        const gifUrl = isSuccess
            ? '<?= APP_URL ?>/public/Assets/gif/success.gif'
            : '<?= APP_URL ?>/public/Assets/gif/failed.gif';

        $('#alertGif').attr('src', gifUrl);
        $('#alertMessage').text(message);
        $('#alertModal').addClass('show');

        setTimeout(function() {
            $('#alertModal').removeClass('show');
            if (isSuccess) {
                location.reload();
            }
        }, 2000);
    }

    // Input validation
    $('#nilaiAkhir').on('input', function() {
        let value = parseInt(this.value);
        if (value > 100) this.value = 100;
        if (value < 0) this.value = 0;
    });

    // Open detail modal
    $('.btn-detail').on('click', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const stambuk = $(this).data('stambuk');
        const nilai = $(this).data('nilai');
        const total = $(this).data('total');

        currentMahasiswaId = id;

        $('#modalNama').text(nama);
        $('#modalStambuk').text(stambuk);
        $('#modalNilai').text(nilai);
        $('#modalTotalNilai').text(total || 'Belum dinilai');
        $('#nilaiAkhir').val(total || '');

        // Load soal jawaban
        $('#soalJawabanList').html('<p class="text-muted">Memuat data...</p>');

        $.ajax({
            url: '<?= APP_URL ?>/getsoaljawaban',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((item, index) => {
                        const isCorrect = item.jawaban === item.jawaban_user;
                        const answerClass = isCorrect ? 'answer-correct' : 'answer-wrong';

                        html += `
                            <div class="soal-card">
                                <div class="soal-number">${index + 1}</div>
                                <div class="question">${item.deskripsi}</div>
                                <div class="answers">
                                    <div class="answer-item">
                                        <span class="answer-label">Pilihan:</span>
                                        <span>${item.pilihan}</span>
                                    </div>
                                    <div class="answer-item">
                                        <span class="answer-label">Jawaban Benar:</span>
                                        <span class="answer-correct">${item.jawaban}</span>
                                    </div>
                                    <div class="answer-item">
                                        <span class="answer-label">Jawaban User:</span>
                                        <span class="${answerClass}">${item.jawaban_user || '-'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#soalJawabanList').html(html);
                } else {
                    $('#soalJawabanList').html('<p class="text-muted">Tidak ada data soal dan jawaban.</p>');
                }
            },
            error: function() {
                $('#soalJawabanList').html('<p class="text-danger">Gagal memuat data.</p>');
            }
        });

        $('#detailModal').addClass('show');
    });

    // Close modal
    $('#closeDetailModal, #detailModal').on('click', function(e) {
        if (e.target === this) {
            $('#detailModal').removeClass('show');
            currentMahasiswaId = null;
        }
    });

    // Prevent modal content click from closing
    $('.modal-nilai-content').on('click', function(e) {
        e.stopPropagation();
    });

    // Submit nilai form
    $('#formNilaiAkhir').on('submit', function(e) {
        e.preventDefault();

        const nilaiAkhir = $('#nilaiAkhir').val();

        if (!currentMahasiswaId) {
            showAlert('ID mahasiswa tidak ditemukan', false);
            return;
        }

        if (!nilaiAkhir || nilaiAkhir === '') {
            showAlert('Mohon masukkan nilai terlebih dahulu', false);
            return;
        }

        $.ajax({
            url: '<?= APP_URL ?>/updatenilaiakhir',
            type: 'POST',
            data: { id: currentMahasiswaId, nilai: nilaiAkhir },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('Nilai berhasil disimpan!', true);
                } else {
                    showAlert(response.message || 'Gagal menyimpan nilai', false);
                }
            },
            error: function() {
                showAlert('Terjadi kesalahan saat menyimpan nilai', false);
            }
        });
    });

    // Close alert modal on click
    $('#alertModal').on('click', function() {
        $(this).removeClass('show');
    });
});
</script>
