<?php
/**
 * Pengajuan Judul Admin View
 *
 * @var array $mahasiswaList
 * @var array $mahasiswaAccStatus
 */
$mahasiswaList = $mahasiswaList ?? [];
?>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    .btn-action { width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .badge-status { min-width: 80px; }
    .table-hover tbody tr:hover { background-color: rgba(61, 194, 236, 0.08); }
    .empty-state i { font-size: 4rem; opacity: 0.5; margin-bottom: 1rem; }
    .nav-tabs { display: none; } /* Hide tabs if inherited styles interfere */
</style>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Pengajuan Judul';
        $subtitle = 'Validasi judul presentasi mahasiswa';
        $icon = 'bi bi-file-text';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="container-fluid px-4 mt-3">
        <!-- Search & Filter -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-3">
            <div class="position-relative" style="width: 280px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchPengajuan" class="form-control rounded-3 ps-5" placeholder="Cari mahasiswa...">
            </div>
        </div>

        <?php if (empty($mahasiswaList)): ?>
            <div class="empty-state text-center py-5 text-muted">
                <i class="bi bi-inbox"></i>
                <h3 class="fs-4 text-secondary mb-2">Belum Ada Pengajuan</h3>
                <p>Data pengajuan judul akan muncul setelah mahasiswa mengajukan judul presentasi</p>
            </div>
        <?php else: ?>
            <div class="table-responsive rounded-3 overflow-hidden shadow-sm">
                <table class="table table-bordered table-hover align-middle mb-0" id="tablePengajuan">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
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
                                
                                if ($hasSchedule) {
                                    $badgeClass = 'bg-primary text-white';
                                    $badgeText = 'Terjadwal';
                                } elseif ($isRejected) {
                                    $badgeClass = 'bg-danger text-white';
                                    $badgeText = 'Ditolak';
                                } elseif ($isAccepted) {
                                    $badgeClass = 'bg-success text-white';
                                    $badgeText = 'Diterima';
                                } else {
                                    $badgeClass = 'bg-secondary text-white';
                                    $badgeText = 'Menunggu';
                                }
                            ?>
                            <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                                <td class="text-muted"><?= $i ?></td>
                                <td><strong class="text-dark"><?= htmlspecialchars($row['nama'] ?? '-') ?></strong></td>
                                <td class="text-secondary"><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> badge-status px-3 py-2 rounded-3"><?= $badgeText ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-nowrap align-items-center">
                                        <button class="btn btn-sm btn-action bg-info-subtle text-info border-0 rounded-3 btn-detail-pengajuan"
                                                data-nama="<?= htmlspecialchars($row['nama'] ?? '') ?>"
                                                data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '') ?>"
                                                data-judul="<?= htmlspecialchars($row['judul'] ?? '') ?>"
                                                data-ppt="<?= htmlspecialchars($row['berkas']['ppt'] ?? '') ?>"
                                                data-makalah="<?= htmlspecialchars($row['berkas']['makalah'] ?? '') ?>"
                                                title="Lihat Detail"><i class="bi bi-eye"></i></button>

                                        <?php if (!$isAccepted && !$isRejected): ?>
                                            <button class="btn btn-sm btn-action bg-success-subtle text-success border-0 rounded-3 btn-accept-judul"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>" title="Terima Judul"><i class="bi bi-check-lg"></i></button>
                                            <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 btn-reject-judul"
                                                    data-userid="<?= $row['id_mahasiswa'] ?>" title="Tolak Judul"><i class="bi bi-x-lg"></i></button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 btn-send-message"
                                                data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>" title="Kirim Pesan"><i class="bi bi-chat-dots"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Detail -->
<div class="modal fade" id="detailPengajuanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold"><i class="bi bi-person-badge me-2"></i>Detail Presentasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><strong class="text-secondary">Nama:</strong> <p class="mb-0 text-dark" id="detailNama">-</p></div>
                <div class="mb-3"><strong class="text-secondary">Stambuk:</strong> <p class="mb-0 text-dark" id="detailStambuk">-</p></div>
                <div class="mb-3"><strong class="text-secondary">Judul:</strong> <p class="mb-0 text-dark" id="detailJudul">-</p></div>
            </div>
            <div class="modal-footer border-top border-light">
                <button class="btn btn-primary bg-gradient-primary rounded-3" id="btnDownloadPpt"><i class="bi bi-file-earmark-ppt"></i> PPT</button>
                <button class="btn btn-primary bg-gradient-primary rounded-3" id="btnDownloadMakalah"><i class="bi bi-file-earmark-pdf"></i> Makalah</button>
                <button class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Send Message -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-gradient-header text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold"><i class="bi bi-chat-dots me-2"></i>Kirim Pesan Revisi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formSendMessage">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pesan:</label>
                        <textarea class="form-control rounded-3" id="messageContent" rows="4" required placeholder="Tulis pesan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-light">
                <button class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formSendMessage" class="btn btn-primary bg-gradient-primary rounded-3"><i class="bi bi-send"></i> Kirim</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const APP_URL = '<?= APP_URL ?>';
    let currentMessageId = null, currentUserId = null;

    $('#searchPengajuan').on('keyup', function() {
        const term = $(this).val().toLowerCase();
        $('#tablePengajuan tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1)
        });
    });

    $('.btn-detail-pengajuan').on('click', function() {
        $('#detailNama').text($(this).data('nama'));
        $('#detailStambuk').text($(this).data('stambuk'));
        $('#detailJudul').text($(this).data('judul'));
        $('#btnDownloadPpt').data('url', $(this).data('ppt'));
        $('#btnDownloadMakalah').data('url', $(this).data('makalah'));
        new bootstrap.Modal('#detailPengajuanModal').show();
    });

    $('#btnDownloadPpt').click(function() {
        const url = $(this).data('url');
        if(url) window.location.href = APP_URL.replace(/\/public$/, '') + '/res/pptUser/' + url;
        else showAlert('File tidak tersedia', false);
    });
    $('#btnDownloadMakalah').click(function() {
        const url = $(this).data('url');
        if(url) window.location.href = APP_URL.replace(/\/public$/, '') + '/res/makalahUser/' + url;
        else showAlert('File tidak tersedia', false);
    });

    $('.btn-accept-judul').click(function() {
        if(confirm('Terima judul?')) {
            $.post(APP_URL + '/updatestatus', { id: $(this).data('userid'), status: 1 }, function(res) {
                if(res.status === 'success') { showAlert('Judul diterima!'); setTimeout(() => location.reload(), 1000); }
                else showAlert(res.message, false);
            }, 'json');
        }
    });
    $('.btn-reject-judul').click(function() {
        if(confirm('Tolak judul?')) {
            $.post(APP_URL + '/updatestatus', { id: $(this).data('userid'), status: 2 }, function(res) {
                if(res.status === 'success') { showAlert('Judul ditolak!'); setTimeout(() => location.reload(), 1000); }
                else showAlert(res.message, false);
            }, 'json');
        }
    });

    $('.btn-send-message').click(function() {
        currentMessageId = $(this).data('id');
        currentUserId = $(this).data('userid');
        $('#messageContent').val('');
        new bootstrap.Modal('#sendMessageModal').show();
    });

    $('#formSendMessage').submit(function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatepresentasi', { id: currentMessageId, userid: currentUserId, message: $('#messageContent').val() }, function(res) {
            bootstrap.Modal.getInstance(document.getElementById('sendMessageModal')).hide();
            if(res.status === 'success') showAlert('Pesan terkirim!');
            else showAlert(res.message, false);
        }, 'json');
    });
});
</script>
