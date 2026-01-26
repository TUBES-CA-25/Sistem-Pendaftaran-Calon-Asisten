<?php
/**
 * Written Test Schedule View
 * Allows admins to activate/deactivate exam banks (Open/Close Exam Sessions)
 */
?>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Jadwal Tes Tertulis';
        $subtitle = 'Atur jadwal dan status aktif untuk ujian tertulis';
        $icon = 'bx bx-calendar';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
        <i class='bx bx-info-circle fs-3 me-3'></i>
        <div>
            <strong>Info Penjadwalan:</strong>
            <div class="small">Saat ini penjadwalan dilakukan dengan mengaktifkan (Buka) atau menonaktifkan (Tutup) Bank Soal yang sesuai. Peserta hanya dapat melihat dan mengerjakan ujian yang statusnya <strong>Aktif</strong>.</div>
        </div>
    </div>

    <!-- Schedule/Bank List -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="fw-bold mb-0">Daftar Sesi Ujian (Bank Soal)</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4 rounded-start-3" width="5%">No</th>
                            <th class="py-3" width="30%">Nama Ujian</th>
                            <th class="py-3" width="15%">Token</th>
                            <th class="py-3" width="15%">Jumlah Soal</th>
                            <th class="py-3" width="20%">Status</th>
                            <th class="py-3 pe-4 rounded-end-3" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['bankSoalList'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class='bx bx-calendar-x fs-1 mb-2'></i>
                                    <p class="mb-0">Belum ada bank soal tersedia.</p>
                                    <a href="#" data-page="bankSoal" class="btn btn-sm btn-primary mt-3">Buat Bank Soal</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['bankSoalList'] as $index => $bank): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($bank['nama']) ?></div>
                                        <div class="text-secondary small text-truncate" style="max-width: 250px;">
                                            <?= htmlspecialchars($bank['deskripsi'] ?? '-') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class='bx bx-key me-1'></i> <?= htmlspecialchars($bank['token']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-info text-white">
                                            <?= $bank['jumlah_soal'] ?> Soal
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input fs-5 cursor-pointer" type="checkbox" 
                                                       id="switch_<?= $bank['id'] ?>"
                                                       <?= ($bank['is_active'] ?? 0) == 1 ? 'checked' : '' ?>
                                                       onchange="toggleExamStatus(<?= $bank['id'] ?>, this)">
                                            </div>
                                            <span class="ms-2 fw-bold <?= ($bank['is_active'] ?? 0) == 1 ? 'text-success' : 'text-danger' ?>" id="status_label_<?= $bank['id'] ?>">
                                                <?= ($bank['is_active'] ?? 0) == 1 ? 'Dibuka (Aktif)' : 'Ditutup' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="pe-4">
                                        <button class="btn btn-sm btn-outline-primary" onclick="window.location.href = '?page=bankSoal'">
                                            <i class='bx bx-edit me-1'></i> Kelola Soal
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    function toggleExamStatus(bankId, checkbox) {
        const isActive = checkbox.checked;
        const statusLabel = document.getElementById('status_label_' + bankId);
        
        // Optimistic UI Update
        if (isActive) {
            statusLabel.textContent = 'Dibuka (Aktif)';
            statusLabel.classList.remove('text-danger');
            statusLabel.classList.add('text-success');
        } else {
            statusLabel.textContent = 'Ditutup';
            statusLabel.classList.remove('text-success');
            statusLabel.classList.add('text-danger');
        }

        const endpoint = isActive ? '<?= APP_URL ?>/activateBank' : '<?= APP_URL ?>/deactivateBank';
        
        const formData = new FormData();
        formData.append('id', bankId);

        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Success feedback if needed
                // console.log('Status updated');
            } else {
                // Revert on failure
                checkbox.checked = !isActive;
                alert('Gagal mengubah status: ' + data.message);
                
                // Revert label
                if (!isActive) { // We wanted to set true, but failed, so we are back to false
                    statusLabel.textContent = 'Dibuka (Aktif)';
                    statusLabel.classList.remove('text-danger'); 
                    statusLabel.classList.add('text-success');
                } else {
                    statusLabel.textContent = 'Ditutup';
                    statusLabel.classList.remove('text-success');
                    statusLabel.classList.add('text-danger');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !isActive;
            alert('Terjadi kesalahan jaringan');
        });
    }
</script>
