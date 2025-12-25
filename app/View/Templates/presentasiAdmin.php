<?php
use App\Controllers\presentasi\RuanganController;
use App\Controllers\User\PresentasiUserController;
use App\Controllers\presentasi\JadwalPresentasiController;
$mahasiswaList = PresentasiUserController::viewAllForAdmin();
$mahasiswaAccStatus = PresentasiUserController::viewAllAccStatusForAdmin();
$ruanganList = RuanganController::viewAllRuangan();
$jadwalPresentasi = JadwalPresentasiController::getJadwalPresentasi();
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    .btn-primary {
        background: linear-gradient(135deg, #3DC2EC, #3392cc);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3392cc, #3DC2EC);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }

    .btn-primary:focus {
        outline: none;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .table-hover {
        background-color: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-top: 20px;
        font-size: 0.95rem;
    }

    .table-hover th,
    .table-hover td {
        padding: 16px 20px;
        text-align: left;
        color: #555;
    }

    .table-hover th {
        background-color: #3DC2EC;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
    }

    .table-hover tr:nth-child(odd) {
        background-color: #f8faff;
    }

    .table-hover tr:nth-child(even) {
        background-color: #e8f4fc;
    }

    .table-hover tr:hover {
        background-color: rgba(61, 194, 236, 0.2);
        cursor: pointer;
    }

    .rounded-table {
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-content {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        font-family: 'Poppins', sans-serif;
    }

    .modal-header {
        background: linear-gradient(135deg, #3DC2EC, #3392cc);
        color: white;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }

    .modal-header h5 {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .modal-body {
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        padding: 20px;
        color: #555;
    }

    .modal-footer {
        border-top: none;
        padding: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ccc;
        padding: 10px 15px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #3DC2EC;
        box-shadow: 0 0 5px rgba(61, 194, 236, 0.5);
        outline: none;
    }

    .form-select {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }

    .form-select:focus {
        border-color: #3DC2EC;
        outline: none;
    }

    .btn-close {
        background: transparent;
        font-size: 1.2rem;
        color: white;
        border: none;
    }

    .btn-close:hover {
        color: #ccc;
    }

    .tes {
        margin-top: 1rem;
        margin-bottom: 1rem;
        background-color: var(--color-white);
        width: 18%;
        text-align: center;
        border-radius: var(--border-radius-1);
        box-shadow: var(--box-shadow);
        color: var(--color-primary);
        border: 1px solid var(--color-light);
    }
</style>

<main>
    <h1 class="dashboard">Presentasi </h1>
    <table id="presentasiMahasiswa" class="table table-hover rounded-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Stambuk</th>
                <th>Judul Presentasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($mahasiswaList as $row) { ?>
                <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                    <td><?= $i ?></td>
                    <td>
                        <span class="open-detail" data-bs-toggle="modal" data-bs-target="#presentasiModal"
                            data-nama="<?= $row['nama'] ?>" data-stambuk="<?= $row['stambuk'] ?>"
                            data-judul="<?= $row['judul'] ?>" data-ppt="<?= $row['berkas']['ppt'] ?>"
                            data-makalah="<?= $row['berkas']['makalah'] ?>">
                            <?= $row['nama'] ?>
                        </span>
                    </td>
                    <td><?= $row['stambuk'] ?></td>
                    <td><?= $row['judul'] ?></td>
                    <td>
                        <div style="display: flex; gap: 5%;">
                            <img src="/tubes_web/public/Assets/Img/edit.svg" alt="edit" class="edit-button">
                        </div>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php } ?>
        </tbody>
    </table>


    <div class="modal fade" id="presentasiModal" tabindex="-1" aria-labelledby="presentasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="presentasiModalLabel">Detail Presentasi Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama Lengkap:</strong> <span id="modalNamaPresentasi"></span></p>
                    <p><strong>Stambuk:</strong> <span id="modalStambukPresentasi"></span></p>
                    <p><strong>Judul Presentasi:</strong> <span id="modalJudulPresentasi"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="downloadPptPresentasi" class="btn btn-primary">Unduh PPT</button>
                    <button type="button" id="downloadMakalahPresentasi" class="btn btn-primary">Unduh Makalah</button>
                    <button type="button" class="btn btn-success" id="acceptButtonPresentasi">Accept</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Send Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan ke Mahasiswa:</label>
                            <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" form="editForm">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            function showModal(message, gifUrl = null) {
                const modal = document.getElementById('customModal');
                if (!modal) {
                    return;
                }
                
                const modalMessage = document.getElementById('modalMessage');
                const modalGif = document.getElementById('modalGif');
                const closeModal = document.getElementById('closeModal');

                modalMessage.textContent = message;
                modalGif.style.display = gifUrl ? 'block' : 'none';
                if (gifUrl) modalGif.src = gifUrl;

                modal.style.display = 'flex';

                $(closeModal).off('click').on('click', function () {
                    modal.style.display = 'none';
                });

                $(window).off('click').on('click', function (event) {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }

            $('#presentasiModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const nama = button.data('nama');
                const stambuk = button.data('stambuk');
                const judul = button.data('judul');
                const ppt = button.data('ppt');
                const makalah = button.data('makalah');
                const idMahasiswa = button.closest('tr').data('userid');

                console.log("makalah : " + makalah);
                console.log("PPt :s " + ppt);
                $('#modalNamaPresentasi').text(nama || 'Data tidak tersedia');
                $('#modalStambukPresentasi').text(stambuk || 'Data tidak tersedia');
                $('#modalJudulPresentasi').text(judul || 'Data tidak tersedia');
                $('#downloadMakalahPresentasi').attr('data-download-url', makalah ? `/tubes_web/res/makalahUser/${makalah}` : '#');
                $('#downloadPptPresentasi').attr('data-download-url', ppt ? `/tubes_web/res/pptUser/${ppt}` : '#');
                $('#acceptButtonPresentasi').data('userid', idMahasiswa);

            });

            $('#downloadPptPresentasi').on('click', function () {
                const url = $(this).data('download-url');
                if (url && url !== '#') {
                    window.location.href = url;
                } else {
                    showModal('Ppt tidak tersedia.');
                }
            });
            $('#downloadMakalahPresentasi').on('click', function () {
                const url = $(this).data('download-url');
                if (url && url !== '#') {
                    window.location.href = url;
                } else {
                    showModal('Makalah tidak tersedia');
                }
            });


            $('#presentasiMahasiswa tbody').on('click', '.delete-button', function (event) {
                event.stopPropagation();
                const id = $(this).closest('tr').data('id');
                const userid = $(this).closest('tr').data('userid');

                $('#deleteModal').data('id', id).data('userid', userid).modal('show');
            });

            $('#confirmDelete').on('click', function () {
                const userid = $('#deleteModal').data('userid');

                $.ajax({
                    url: '<?= APP_URL ?>/deletemhs',
                    type: 'POST',
                    data: { id: userid },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showModal('Data berhasil dihapus')
                            location.reload();
                        } else {
                            showModal('Data tidak berhasil dihapus')
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });

                $('#deleteModal').modal('hide');
            });

            $('#presentasiMahasiswa tbody').on('click', '.edit-button', function (event) {
                event.stopPropagation();
                const id = $(this).closest('tr').data('id');
                $('#editModal').data('id', id).modal('show');
            });

            $('#editForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editModal').data('id');
                const message = $('#message').val();

                console.log('ID:', id);
                console.log('Message:', message);
                $.ajax({
                    url: '<?= APP_URL ?>/updatepresentasi',
                    type: 'POST',
                    data: { id: id, message: message },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showModal('Pesan berhasil dikirim');
                        } else {
                            showModal('Pesan gagal dikirim');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });

                $('#editModal').modal('hide');
            });
            $('#acceptButtonPresentasi').on('click', function () {
                const idMahasiswa = $(this).data('userid');

                if (!idMahasiswa) {
                    showModal('Mahasiswa tidak ditemukan');
                    return;
                }

                $.ajax({
                    url: '<?= APP_URL ?>/updatestatus',
                    type: 'POST',
                    data: { id: idMahasiswa },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showModal('Mahasiswa berhasil di terima');
                            document.querySelector('a[data-page="presentasi"]').click();
                        } else {
                            showModal('Mahasiswa gagal di terima');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Terjadi kesalahan. Coba lagi nanti.');
                    }
                });
                $('#presentasiModal').modal('hide');
            });
        });
    </script>