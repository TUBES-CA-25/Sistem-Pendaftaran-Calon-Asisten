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
    /* Custom styles that complement Bootstrap */
    /* Main layout reset removed to match other pages */

    /* Stat Cards with custom gradients */
    .stat-card {
        border-radius: 12px;
        padding: 16px 20px;
        border-left: 4px solid #2563eb;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .stat-card.success {
        border-left-color: #10b981;
    }

    .stat-card.warning {
        border-left-color: #f59e0b;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 4px;
    }

    /* Soal Card custom border colors */
    .soal-card.correct {
        border-left: 5px solid #10b981 !important;
    }

    .soal-card.wrong {
        border-left: 5px solid #ef4444 !important;
    }

    .soal-card.unanswered {
        border-left: 5px solid #94a3b8 !important;
        background-color: #f8fafc;
    }

    .soal-number {
        background: var(--gradient-header);
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(47, 102, 246, 0.3);
    }

    /* Filter Button Styles */
    .filter-btn-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-btn {
        border: 2px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .filter-btn:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: #eff6ff;
    }

    .filter-btn.active {
        border-color: #2563eb;
        background: #2563eb;
        color: #fff;
    }

    .filter-btn i {
        margin-right: 4px;
    }
</style>

<main>
    <!-- Page Header -->
    <!-- Page Header -->
    <?php
        $title = 'Daftar Nilai Tes Tertulis';
        $subtitle = 'Kelola dan lihat nilai tes tertulis mahasiswa';
        $icon = 'bi bi-clipboard-data';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="container-fluid px-4 mt-3">
        <!-- View List -->
        <div id="view-list">
        <!-- Search Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
            <div class="position-relative" style="width: 280px; max-width: 100%;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchInput" class="form-control ps-5 rounded-3" placeholder="Cari mahasiswa...">
            </div>
        </div>

        <?php if (empty($nilai)): ?>
            <div class="text-center py-5 text-secondary">
                <i class="bi bi-inbox display-1 opacity-50"></i>
                <h3 class="h4 mt-3 mb-2">Belum ada peserta</h3>
                <p class="mb-0">Data nilai akan muncul setelah mahasiswa mengerjakan tes tertulis</p>
            </div>
        <?php else: ?>
            <!-- Clean Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="tableNilai">
                    <thead style="background-color: #ffffff;">
                        <tr>
                            <th class="text-center fw-bold py-3 px-3" style="font-size: 0.875rem; color: #2563EB; text-transform: uppercase; width: 60px;">No</th>
                            <th class="fw-bold py-3 px-3" style="font-size: 0.875rem; color: #2563EB; text-transform: uppercase;">Mahasiswa</th>
                            <th class="fw-bold py-3 px-3" style="font-size: 0.875rem; color: #2563EB; text-transform: uppercase;">Stambuk</th>
                            <th class="text-center fw-bold py-3 px-3" style="font-size: 0.875rem; color: #2563EB; text-transform: uppercase; width: 120px;">Nilai Akhir</th>
                            <th class="text-center fw-bold py-3 px-3" style="font-size: 0.875rem; color: #2563EB; text-transform: uppercase; width: 140px;">Status</th>
                            <th class="text-center fw-bold py-3 px-3" style="font-size: 0.875rem; color: #2563EB; text-transform: uppercase; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($nilai as $value): ?>
                            <?php
                            // Raw auto-calculated score
                            $nilaiTes = $value['nilai'] ?? null;
                            // Manual override/Final score
                            $nilaiTotal = $value['total'] ?? null;

                            // Determine which value to display
                            // Priority: Total (Manual) > Nilai (Auto)
                            $displayNilai = ($nilaiTotal !== null && $nilaiTotal !== '') ? $nilaiTotal : $nilaiTes;
                            
                            // Determine status badge
                            $statusLabel = 'Belum Dinilai';
                            $statusBadge = 'bg-secondary';

                            if ($displayNilai !== null && $displayNilai !== '-' && $displayNilai !== '') {
                                $score = (int)$displayNilai;
                                if ($score >= 70) {
                                    $statusLabel = 'Memenuhi';
                                    $statusBadge = 'bg-success';
                                } else {
                                    $statusLabel = 'Tidak Memenuhi';
                                    $statusBadge = 'bg-danger';
                                }
                            }
                            ?>
                            <tr>
                                <td class="text-center py-3 px-3" style="color: #6b7280; font-weight: 500;"><?= $i ?></td>
                                <td class="py-3 px-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?></span>
                                        <span class="small text-muted">Mahasiswa</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3" style="color: #4b5563;"><?= htmlspecialchars($value['stambuk'] ?? '-') ?></td>
                                <td class="text-center py-3 px-3">
                                    <span class="badge bg-info text-dark rounded-pill px-3">
                                        <?= ($displayNilai !== null && $displayNilai !== '') ? $displayNilai : 'Belum ada' ?>
                                    </span>
                                </td>
                                <td class="text-center py-3 px-3">
                                    <span class="badge <?= $statusBadge ?> rounded-pill px-3">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="text-center py-3 px-3">
                                    <button class="btn btn-sm btn-primary rounded-3 btn-detail"
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
            </div>
        <?php endif; ?>
    </div>

        <!-- View Detail (Inline) -->
        <div id="view-detail" class="d-none pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 fw-semibold text-dark mb-0">
                    <i class="bi bi-person-badge"></i> Detail Nilai Mahasiswa
                </h2>
                <button class="btn btn-light rounded-3 px-4" id="btnBack">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
            </div>

            <div class="card bg-light border-0">
                <div class="card-body p-4">
                    <!-- Info Grid using Bootstrap Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-start border-4 border-primary h-100">
                                <div class="card-body">
                                    <label class="text-muted text-uppercase small mb-1">Nama Lengkap</label>
                                    <div class="fs-5 fw-semibold text-dark" id="detailNama">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-start border-4 border-primary h-100">
                                <div class="card-body">
                                    <label class="text-muted text-uppercase small mb-1">Stambuk</label>
                                    <div class="fs-5 fw-semibold text-dark" id="detailStambuk">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-start border-4 border-primary h-100">
                                <div class="card-body">
                                    <label class="text-muted text-uppercase small mb-1">Nilai Akhir</label>
                                    <div class="fs-5 fw-semibold text-dark" id="detailTotalNilai">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Jawaban -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-value" id="statTotal">0</div>
                                <div class="stat-label">Total Soal</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card success">
                                <div class="stat-value text-success" id="statBenar">0</div>
                                <div class="stat-label">Benar</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card warning">
                                <div class="stat-value text-danger" id="statSalah">0</div>
                                <div class="stat-label">Salah</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-value text-muted" id="statTidakDijawab">0</div>
                                <div class="stat-label">Tidak Dijawab</div>
                            </div>
                        </div>
                    </div>

                    <!-- Soal Jawaban Section -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3 flex-wrap gap-2">
                            <h5 class="fw-semibold text-dark mb-0">
                                <i class="bi bi-list-check"></i> Soal dan Jawaban
                            </h5>
                            <div class="filter-btn-group">
                                <button class="filter-btn active" data-filter="semua">
                                    <i class="bi bi-grid-3x3-gap"></i> Semua
                                </button>
                                <button class="filter-btn" data-filter="pilihan_ganda">
                                    <i class="bi bi-ui-checks"></i> Pilihan Ganda
                                </button>
                                <button class="filter-btn" data-filter="essay">
                                    <i class="bi bi-pencil-square"></i> Essay
                                </button>
                            </div>
                        </div>
                        <div id="soalJawabanList" class="row g-3">
                            <p class="text-muted">Memuat data...</p>
                        </div>
                    </div>

                    <!-- Nilai Form -->
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold text-dark mb-3">
                            <i class="bi bi-pencil-square"></i> Input Nilai Akhir
                        </h5>
                        <form id="formNilaiAkhir">
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="number"
                                       id="nilaiAkhir"
                                       class="form-control rounded-3"
                                       style="max-width: 200px;"
                                       placeholder="Masukkan nilai (0-100)"
                                       min="0"
                                       max="100">
                                <button type="submit" class="btn btn-success rounded-3 px-4">
                                    <i class="bi bi-check-lg"></i> Simpan Nilai
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<!-- Bootstrap Alert Modal (replacing custom alert modal) -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body text-center p-5">
                <i id="alertIcon" class="bi display-1 mb-3" style="display: none;"></i>
                <p id="alertMessage" class="fs-5 fw-semibold mb-4">-</p>
                <button type="button" class="btn btn-primary px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentMahasiswaId = null;
    const alertModalEl = document.getElementById('alertModal');
    const alertModalBS = new bootstrap.Modal(alertModalEl);

    // Helper function to show alert using Bootstrap Modal
    function showAlert(message, isSuccess) {
        $('#alertMessage').text(message);
        
        const iconClass = isSuccess 
            ? 'bi-check-circle-fill text-success' 
            : 'bi-x-circle-fill text-danger';
            
        $('#alertIcon').removeClass().addClass('bi display-1 mb-3 ' + iconClass).show();
        alertModalBS.show();

        // Auto hide after 2 seconds
        setTimeout(() => {
            alertModalBS.hide();
        }, 2000);
    }

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

    // Input validation
    $('#nilaiAkhir').on('input', function() {
        let value = parseInt(this.value);
        if (value > 100) this.value = 100;
        if (value < 0) this.value = 0;
    });

    // Open Detail View
    $('.btn-detail').on('click', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const stambuk = $(this).data('stambuk');
        const nilai = $(this).data('nilai');
        const total = $(this).data('total');

        currentMahasiswaId = id;

        // Populate Data
        $('#detailNama').text(nama);
        $('#detailStambuk').text(stambuk);
        $('#detailTotalNilai').text(total || 'Belum dinilai');
        $('#nilaiAkhir').val(total || '');

        // Loading State for Questions
        $('#soalJawabanList').html('<div class="col-12"><p class="text-muted">Memuat data...</p></div>');

        // Fetch Questions
        $.ajax({
            url: '<?= APP_URL ?>/getsoaljawaban',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    let totalSoal = response.data.length;
                    let benarCount = 0;
                    let salahCount = 0;
                    let tidakDijawabCount = 0;

                    response.data.forEach((item, index) => {
                        const isAnswered = item.jawaban_user !== null && item.jawaban_user !== '';
                        const isCorrect = isAnswered && (item.jawaban === item.jawaban_user);
                        const tipeSoal = item.status_soal || 'essay';
                        const isPilihanGanda = tipeSoal === 'pilihan_ganda';

                        let cardClass, borderClass, icon, statusBadge;

                        if (!isAnswered) {
                            // Tidak dijawab
                            tidakDijawabCount++;
                            cardClass = 'unanswered';
                            borderClass = 'border-secondary';
                            icon = '<i class="bi bi-question-circle-fill text-muted"></i>';
                            statusBadge = 'bg-secondary';
                        } else if (isCorrect) {
                            // Benar
                            benarCount++;
                            cardClass = 'correct';
                            borderClass = 'border-success';
                            icon = '<i class="bi bi-check-circle-fill text-success"></i>';
                            statusBadge = 'bg-success';
                        } else {
                            // Salah
                            salahCount++;
                            cardClass = 'wrong';
                            borderClass = 'border-danger';
                            icon = '<i class="bi bi-x-circle-fill text-danger"></i>';
                            statusBadge = 'bg-danger';
                        }

                        // Format pilihan untuk ditampilkan
                        let pilihanText = '';
                        try {
                            const pilihanObj = JSON.parse(item.pilihan);
                            pilihanText = Object.entries(pilihanObj)
                                .map(([key, value]) => `${key}. ${value}`)
                                .join('<br>');
                        } catch (e) {
                            pilihanText = item.pilihan;
                        }

                        // Badge tipe soal
                        const tipeBadge = isPilihanGanda
                            ? '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary ms-2"><i class="bi bi-ui-checks"></i> PG</span>'
                            : '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning ms-2"><i class="bi bi-pencil-square"></i> Essay</span>';

                        html += `
                            <div class="col-lg-6 soal-item" data-tipe="${tipeSoal}">
                                <div class="card soal-card ${cardClass} border-start border-4 ${borderClass} h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="soal-number">${index + 1}</div>
                                                ${tipeBadge}
                                            </div>
                                            <div style="font-size: 1.2rem;">${icon}</div>
                                        </div>
                                        <div class="mb-3">
                                            <strong class="text-dark">${item.deskripsi}</strong>
                                        </div>
                                        <div class="small">
                                            ${isPilihanGanda ? `
                                            <div class="mb-2">
                                                <span class="text-muted">Pilihan:</span>
                                                <div class="ms-1 mt-1">${pilihanText}</div>
                                            </div>
                                            ` : ''}
                                            <div class="mb-2">
                                                <span class="text-muted">Jawaban Benar:</span>
                                                <span class="badge bg-success ms-1">${item.jawaban}</span>
                                            </div>
                                            <div>
                                                <span class="text-muted">Jawaban Mahasiswa:</span>
                                                ${isAnswered
                                                    ? `<span class="badge ${statusBadge} ms-1">${item.jawaban_user}</span>`
                                                    : '<span class="badge bg-secondary ms-1">Tidak menjawab</span>'
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Update statistik
                    $('#statTotal').text(totalSoal);
                    $('#statBenar').text(benarCount);
                    $('#statSalah').text(salahCount);
                    $('#statTidakDijawab').text(tidakDijawabCount);

                    $('#soalJawabanList').html(html);
                } else {
                    $('#soalJawabanList').html('<div class="col-12"><p class="text-muted">Tidak ada data soal dan jawaban.</p></div>');
                    // Reset statistik
                    $('#statTotal').text(0);
                    $('#statBenar').text(0);
                    $('#statSalah').text(0);
                    $('#statTidakDijawab').text(0);
                }
            },
            error: function() {
                $('#soalJawabanList').html('<div class="col-12"><p class="text-danger">Gagal memuat data.</p></div>');
            }
        });

        // Switch View using Bootstrap d-none
        $('#view-list').addClass('d-none');
        $('#view-detail').removeClass('d-none');
        window.scrollTo(0, 0);
    });

    // Back Button Logic
    $('#btnBack').on('click', function() {
        $('#view-detail').addClass('d-none');
        $('#view-list').removeClass('d-none');
        currentMahasiswaId = null;
    });

    // Filter Button Logic
    $(document).on('click', '.filter-btn', function() {
        const filter = $(this).data('filter');

        // Update active state
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Filter soal items
        if (filter === 'semua') {
            $('.soal-item').show();
        } else {
            $('.soal-item').hide();
            $(`.soal-item[data-tipe="${filter}"]`).show();
        }
    });

    // Submit nilai form
    $('#formNilaiAkhir').on('submit', function(e) {
        e.preventDefault();

        const nilaiAkhir = $('#nilaiAkhir').val();

        if (!currentMahasiswaId) {
            showAlert('ID mahasiswa tidak ditemukan', false);
            return;
        }

        if (typeof nilaiAkhir === 'undefined' || nilaiAkhir === '') {
            showAlert('Mohon masukkan nilai', false);
            return;
        }

        // Show loading state
        const btnSubmit = $(this).find('button[type="submit"]');
        const originalBtnText = btnSubmit.html();
        btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: '<?= APP_URL ?>/updatenilaiakhir',
            type: 'POST',
            data: { id: currentMahasiswaId, nilai: nilaiAkhir },
            dataType: 'json',
            success: function(response) {
                btnSubmit.prop('disabled', false).html(originalBtnText);

                if (response.status === 'success') {
                    showAlert('Nilai berhasil disimpan!', true);

                    // Real-time Update Logic
                    const btn = $(`.btn-detail[data-id="${currentMahasiswaId}"]`);
                    const tr = btn.closest('tr');

                    if (tr.length) {
                        // Determine badge class based on score using Bootstrap classes
                        let badgeClass = 'bg-secondary';
                        let statusText = 'Belum Dinilai';

                        if (nilaiAkhir !== '') {
                            const score = parseInt(nilaiAkhir);

                            // Update Status Logic - Status reflects Final Grade (Nilai Akhir)
                            if (score >= 70) {
                                badgeClass = 'bg-success';
                                statusText = 'Memenuhi';
                            } else {
                                badgeClass = 'bg-danger';
                                statusText = 'Tidak Memenuhi';
                            }
                        }

                        // Update Nilai Akhir Column (Index 3)
                        tr.find('td:eq(3)').html(`<span class="badge bg-info text-dark rounded-pill px-3">${nilaiAkhir}</span>`);

                        // Update Status Column (Index 4)
                        tr.find('td:eq(4)').html(`<span class="badge ${badgeClass} rounded-pill px-3">${statusText}</span>`);

                        // Update Button Data Attribute for next open
                        btn.data('total', nilaiAkhir);

                        // Update the text in the detail view as well
                        $('#detailTotalNilai').text(nilaiAkhir);
                    }
                } else {
                    showAlert(response.message || 'Gagal menyimpan nilai', false);
                }
            },
            error: function() {
                btnSubmit.prop('disabled', false).html(originalBtnText);
                showAlert('Terjadi kesalahan saat menyimpan nilai', false);
            }
        });
    });
});
</script>
