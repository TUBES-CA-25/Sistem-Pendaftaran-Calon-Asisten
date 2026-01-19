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
    main {
        padding: 0;
        margin: -20px -20px -20px -20px;
        width: calc(100% + 40px);
    }

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

    <!-- Card Container -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
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

            <!-- View List -->
            <div id="view-list">
                <!-- Stats Row using Bootstrap Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-value"><?= $totalMahasiswa ?></div>
                            <div class="stat-label">Total Peserta</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card success">
                            <div class="stat-value"><?= $nilaiTinggi ?></div>
                            <div class="stat-label">Lulus (≥70)</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card warning">
                            <div class="stat-value"><?= $nilaiRendah ?></div>
                            <div class="stat-label">Tidak Lulus (<70)</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-value"><?= $belumDinilai ?></div>
                            <div class="stat-label">Belum Dinilai</div>
                        </div>
                    </div>
                </div>

                <!-- Table Controls using Bootstrap -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                    <div class="position-relative" style="width: 280px; max-width: 100%;">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="searchInput" class="form-control ps-5 rounded-3" placeholder="Cari nama atau stambuk...">
                    </div>
                </div>

                <?php if (empty($nilai)): ?>
                    <div class="text-center py-5 text-secondary">
                        <i class="bi bi-inbox display-1 opacity-50"></i>
                        <h3 class="h4 mt-3 mb-2">Belum Ada Data Nilai</h3>
                        <p class="mb-0">Data nilai akan muncul setelah mahasiswa mengerjakan tes tertulis</p>
                    </div>
                <?php else: ?>
                    <!-- Data Table using Bootstrap Table -->
                    <div class="table-responsive rounded-3 overflow-hidden shadow-sm">
                        <table class="table table-hover align-middle mb-0" id="tableNilai">
                            <thead class="table-primary text-white" style="background: var(--gradient-header);">
                                <tr>
                                    <th class="text-center" style="width: 60px;">No</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Stambuk</th>
                                    <th style="width: 140px;">Nilai Tes</th>
                                    <th style="width: 140px;">Status</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($nilai as $value): ?>
                                    <?php
                                    $nilaiTes = $value['nilai'] ?? '-';
                                    $nilaiTotal = $value['total'] ?? null;

                                    // Determine badge class using Bootstrap badges
                                    $badgeClass = 'bg-secondary';
                                    if ($nilaiTotal !== null && $nilaiTotal !== '') {
                                        if ((int)$nilaiTotal >= 70) {
                                            $badgeClass = 'bg-success';
                                        } elseif ((int)$nilaiTotal >= 50) {
                                            $badgeClass = 'bg-warning text-dark';
                                        } else {
                                            $badgeClass = 'bg-danger';
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
                                            <span class="badge bg-warning text-dark rounded-pill px-3"><?= htmlspecialchars($nilaiTes) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusLabel = 'Belum Dinilai';
                                            $statusBadge = 'bg-secondary';

                                            if ($nilaiTes !== '-' && $nilaiTes !== null && $nilaiTes !== '') {
                                                $score = (int)$nilaiTes;
                                                if ($score >= 70) {
                                                    $statusLabel = 'Memenuhi';
                                                    $statusBadge = 'bg-success';
                                                } else {
                                                    $statusLabel = 'Tidak Memenuhi';
                                                    $statusBadge = 'bg-danger';
                                                }
                                            }
                                            ?>
                                            <span class="badge <?= $statusBadge ?> rounded-pill px-3">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
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

                    <!-- Soal Jawaban Section -->
                    <div class="mt-4">
                        <h5 class="fw-semibold text-dark pb-2 border-bottom mb-3">
                            <i class="bi bi-list-check"></i> Soal dan Jawaban
                        </h5>
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
                <img id="alertGif" src="" alt="Status" class="mb-3" style="width: 80px; height: 80px; display: none;">
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
        const gifUrl = isSuccess
            ? '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/check.gif'
            : '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/cross.gif';
        $('#alertGif').attr('src', gifUrl).show();
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
                    response.data.forEach((item, index) => {
                        const isCorrect = item.jawaban === item.jawaban_user;
                        const cardClass = isCorrect ? 'correct' : 'wrong';
                        const borderClass = isCorrect ? 'border-success' : 'border-danger';
                        const icon = isCorrect
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';

                        html += `
                            <div class="col-lg-6">
                                <div class="card soal-card ${cardClass} border-start border-4 ${borderClass} h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="soal-number">${index + 1}</div>
                                            <div style="font-size: 1.2rem;">${icon}</div>
                                        </div>
                                        <div class="mb-3">
                                            <strong class="text-dark">${item.deskripsi}</strong>
                                        </div>
                                        <div class="small">
                                            <div class="mb-2">
                                                <span class="text-muted">Pilihan:</span>
                                                <span class="ms-1">${item.pilihan}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted">Jawaban Benar:</span>
                                                <span class="badge bg-success ms-1">${item.jawaban}</span>
                                            </div>
                                            <div>
                                                <span class="text-muted">Jawaban Mahasiswa:</span>
                                                <span class="badge ${isCorrect ? 'bg-success' : 'bg-danger'} ms-1">
                                                    ${item.jawaban_user || '<span class="text-muted fst-italic">Tidak menjawab</span>'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#soalJawabanList').html(html);
                } else {
                    $('#soalJawabanList').html('<div class="col-12"><p class="text-muted">Tidak ada data soal dan jawaban.</p></div>');
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
