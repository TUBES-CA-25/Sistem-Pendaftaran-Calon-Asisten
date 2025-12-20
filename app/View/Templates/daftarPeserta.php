<?php
use App\Controllers\user\BerkasUserController;
use App\Controllers\user\MahasiswaController;
$mahasiswaList = MahasiswaController::viewAllMahasiswa() ?? [];
$result = MahasiswaController::viewAllMahasiswa() ?? [];
?>

<style>
    /* Font Poppins */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f4f8;
    }


    .table-modern {
        background: linear-gradient(145deg, #ffffff, #f3f6fa);
        /* Gradient background */
        border-radius: 16px;
        /* Rounded corners */
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        /* Depth effect */
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
        /* Remove gaps */
        width: 100%;
        margin: 20px 0;
        /* Add spacing around the table */
    }

    .table-modern th,
    .table-modern td {
        padding: 16px 20px;
        /* Add padding for spacing */
        text-align: left;
        color: #333;
        /* Dark text for readability */
        border-bottom: 1px solid #eaeaea;
        /* Light borders between rows */
    }

    .table-modern th {
        background-color: #3dc2ec;
        /* Subtle header background */
        font-weight: 600;
        /* Bold font */
        color: white;
        /* Slightly darker text for headers */
        font-size: 1rem;
        /* Adjust header font size */
    }

    .table-modern tr:hover td {
        background-color: rgba(61, 194, 236, 0.1);
        /* Highlight row on hover */
        cursor: pointer;
    }

    .table-modern tr:last-child td {
        border-bottom: none;
        /* Remove bottom border for the last row */
    }

    .table-modern tbody tr:first-child td:first-child {
        border-top-left-radius: 16px;
        /* Rounded top-left corner */
    }

    .table-modern tbody tr:first-child td:last-child {
        border-top-right-radius: 16px;
        /* Rounded top-right corner */
    }

    .table-modern tbody tr:last-child td:first-child {
        border-bottom-left-radius: 16px;
        /* Rounded bottom-left corner */
    }

    .table-modern tbody tr:last-child td:last-child {
        border-bottom-right-radius: 16px;
        /* Rounded bottom-right corner */
    }

    .table-modern .action-icons img {
        width: 24px;
        height: 24px;
        margin: 0 8px;
        transition: transform 0.3s ease;
    }

    .table-modern .action-icons img:hover {
        transform: scale(1.1);
        /* Slight zoom on hover */
    }
</style>

<main>
    <h1 class="dashboard">Daftar peserta</h1>
    <table id="daftar" class="display table-modern">
        <button type="button" data-bs-toggle="modal" data-bs-target="#addNotification" class="btn btn-primary mb-3">
            Kirim pesan
        </button>
        <thead>
            <tr>
                <th>no</th>
                <th>Nama Lengkap</th>
                <th>Stambuk</th>
                <th>Jurusan</th>
                <th>Kelas</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($result as $row) { ?>
                <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['idUser'] ?>" style="cursor: pointer;">
                    <td><?= $i ?></td>
                    <td>
                        <span data-bs-toggle="modal" data-bs-target="#detailModal" data-nama="<?= $row['nama_lengkap'] ?>"
                            data-stambuk="<?= $row['stambuk'] ?>" data-jurusan="<?= $row['jurusan'] ?>"
                            data-kelas="<?= $row['kelas'] ?>" data-alamat="<?= $row['alamat'] ?>"
                            data-tempat_lahir="<?= $row['tempat_lahir'] ?>" data-notelp="<?= $row['notelp'] ?>"
                            data-tanggal_lahir="<?= $row['tanggal_lahir'] ?>"
                            data-jenis_kelamin="<?= $row['jenis_kelamin'] ?>" data-foto="<?= $row['berkas']['foto'] ?>"
                            data-cv="<?= $row['berkas']['cv'] ?>" data-transkrip="<?= $row['berkas']['transkrip_nilai'] ?>"
                            data-surat="<?= $row['berkas']['surat_pernyataan'] ?>">
                            <?= $row['nama_lengkap'] ?>
                        </span>

                    </td>
                    <td><?= $row['stambuk'] ?></td>
                    <td><?= $row['jurusan'] ?></td>
                    <td><?= $row['kelas'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td>
                        <div style="display: flex; gap:5%;">
                            <img src="/Kelompok1_IC-ASSIST/public/Assets/Img/edit.svg" alt="edit" style="cursor: pointer;">
                            <img src="/Kelompok1_IC-ASSIST/public/Assets/Img/delete.svg" alt="delete" style="cursor: pointer;">
                        </div>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php } ?>
        </tbody>
    </table>
</main>

<div class="modal fade" id="addNotification" tabindex="-1" aria-labelledby="addNotificationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNotificationLabel">Tambah Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addNotificationForm">
                    <div class="mb-3">
                        <label for="mahasiswa" class="form-label">Pilih Mahasiswa</label>
                        <select class="form-select" id="mahasiswa">
                            <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                            <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                <option value="<?= $mahasiswa['id'] ?>">
                                    <?= $mahasiswa['stambuk'] ?> - <?= $mahasiswa['nama_lengkap'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-secondary mt-2" id="addMahasiswaButton">Tambah
                            Mahasiswa</button>
                        <button type="button" class="btn btn-success mt-2" id="addAllMahasiswaButton">Tambah
                            Semua</button>
                    </div>
                    <div class="mb-3">
                        <label for="selectedMahasiswa" class="form-label">Mahasiswa Terpilih</label>
                        <ul class="list-group" id="selectedMahasiswaList"></ul>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalFoto" src="" alt="" style="width: 100%; max-height: 300px; object-fit: cover;">
                <p><strong>Nama Lengkap:</strong> <span id="modalNama"></span></p>
                <p><strong>Stambuk:</strong> <span id="modalStambuk"></span></p>
                <p><strong>Jurusan:</strong> <span id="modalJurusan"></span></p>
                <p><strong>Kelas:</strong> <span id="modalKelas"></span></p>
                <p><strong>Alamat:</strong> <span id="modalAlamat"></span></p>
                <p><strong>Tempat Lahir:</strong> <span id="modalTempat_lahir"></span></p>
                <p><strong>Tanggal Lahir:</strong> <span id="modalTanggal_lahir"></span></p>
                <p><strong>Jenis Kelamin:</strong> <span id="modalJenis_kelamin"></span></p>
                <p><strong>No Telephone:</strong> <span id="modalNoTelp"></span></p>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="collapse"
                        data-bs-target="#downloadOptions" aria-expanded="false" aria-controls="downloadOptions">
                        Unduh Berkas
                    </button>

                    <div class="collapse mt-3" id="downloadOptions">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-sm" id="downloadFotoButton"
                                data-download-url="" target="blank">Unduh Foto</button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadCVButton"
                                data-download-url="" target="blank">Unduh CV</button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadTranskripButton"
                                data-download-url="" target="blank">Unduh Transkrip Nilai</button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadSuratButton"
                                data-download-url="" target="blank">Unduh Surat Pernyataan</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success" id="acceptButton">
                    Accept
                </button>
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

        function showConfirm(message, onConfirm = null, onCancel = null) {
            const modal = document.getElementById('confirmModal');
            if (!modal) {

                return;
            }

            const modalMessage = document.getElementById('confirmModalMessage');
            const confirmButton = document.getElementById('confirmModalConfirm');
            const cancelButton = document.getElementById('confirmModalCancel');

            modalMessage.textContent = message;
            modal.style.display = 'flex';

            $(confirmButton).off('click').on('click', function () {
                if (onConfirm) onConfirm();
                modal.style.display = 'none';
            });

            $(cancelButton).off('click').on('click', function () {
                if (onCancel) onCancel();
                modal.style.display = 'none';
            });

            $(window).off('click').on('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

    $(document).ready(function () {
     
        const table = $('#daftar').DataTable();
        const mahasiswaDropdown = document.getElementById("mahasiswa");
        const addMahasiswaButton = document.getElementById("addMahasiswaButton");
        const selectedMahasiswaList = document.getElementById("selectedMahasiswaList");
        const addNotificationForm = document.getElementById("addNotificationForm");

        let selectedMahasiswa = [];

        function renderSelectedMahasiswa() {
            selectedMahasiswaList.innerHTML = "";
            selectedMahasiswa.forEach((mahasiswa) => {
                const listItem = document.createElement("li");
                listItem.className = "list-group-item d-flex justify-content-between align-items-center";
                listItem.textContent = mahasiswa.text;

                const removeButton = document.createElement("button");
                removeButton.className = "btn btn-sm btn-danger";
                removeButton.textContent = "Hapus";
                removeButton.addEventListener("click", () => {
                    selectedMahasiswa = selectedMahasiswa.filter((item) => item.id !== mahasiswa.id);
                    renderSelectedMahasiswa();
                });

                listItem.appendChild(removeButton);
                selectedMahasiswaList.appendChild(listItem);
            });
        }

        $(addMahasiswaButton).on("click", () => {
            const selectedOption = mahasiswaDropdown.options[mahasiswaDropdown.selectedIndex];
            const mahasiswaId = mahasiswaDropdown.value;
            const mahasiswaText = selectedOption ? selectedOption.text : null;

            if (!mahasiswaId) {
                showModal("Pilih mahasiswa terlebih dahulu");
                return;
            }

            if (selectedMahasiswa.some((item) => item.id === mahasiswaId)) {
                showModal("mahasiswa sudah dipilih");
                return;
            }

            selectedMahasiswa.push({ id: mahasiswaId, text: mahasiswaText });
            renderSelectedMahasiswa();
            mahasiswaDropdown.selectedIndex = 0;
        });

        $(addAllMahasiswaButton).on("click", () => {
            for (let i = 0; i < mahasiswaDropdown.options.length; i++) {
                const option = mahasiswaDropdown.options[i];
                const mahasiswaId = option.value;
                const mahasiswaText = option.text;
                console.log(mahasiswaId, mahasiswaText);
                if (mahasiswaId && !selectedMahasiswa.some(item => item.id === mahasiswaId)) {
                    selectedMahasiswa.push({ id: mahasiswaId, text: mahasiswaText });
                }
            }
            renderSelectedMahasiswa();
        });

        $(addNotificationForm).on("submit", (e) => {
            e.preventDefault();

            const message = document.getElementById("message").value;
            const mahasiswaIds = selectedMahasiswa.map(item => item.id);

            if (selectedMahasiswa.length === 0) {
                showModal("Pilih mahasiswa terlebih dahulu");
                return;
            }

            if (!message) {
                showModal("Isi pesan terlebih dahulu");
                return;
            }

            const notificationData = {
                mahasiswaIds,
                message,
            };
            console.log(notificationData)
            $.ajax({
                url: "<?=APP_URL?>/addallnotif", 
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(notificationData),
                success: function (response) {
                    if (response.status === 'success') {
                        showModal(response.message || 'Pesan berhasil dikirim');
                        location.reload();
                    } else {
                        showModal(response.message || 'Pesan berhasil dikirim');
                    }

                    addNotificationForm.reset();
                    selectedMahasiswa = [];
                    renderSelectedMahasiswa();
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("Gagal mengirim pesan. Silakan coba lagi.");
                }
            });
        });
    });
    $('#detailModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const idMhs = button.closest('tr').data('id');
        const nama = button.data('nama');
        const stambuk = button.data('stambuk');
        const jurusan = button.data('jurusan');
        const kelas = button.data('kelas');
        const alamat = button.data('alamat');
        const tempat_lahir = button.data('tempat_lahir');
        const notelp = button.data('notelp');
        const tanggal_lahir = button.data('tanggal_lahir');
        const jenis_kelamin = button.data('jenis_kelamin');
        const foto = button.data('foto');
        const cv = button.data('cv');
        const transkrip = button.data('transkrip');
        const surat = button.data('surat');

        $(this).data('id', idMhs);
        $('#modalNama').text(nama);
        $('#modalStambuk').text(stambuk);
        $('#modalJurusan').text(jurusan);
        $('#modalKelas').text(kelas);
        $('#modalAlamat').text(alamat);
        $('#modalTempat_lahir').text(tempat_lahir);
        $('#modalNoTelp').text(notelp);
        $('#modalTanggal_lahir').text(tanggal_lahir);
        $('#modalJenis_kelamin').text(jenis_kelamin);

        $('#modalFoto').attr('src', "/Kelompok1_IC-ASSIST/res/imageUser/" + (foto || 'default-image.jpg'));
        $('#modalFoto').attr('alt', `Foto ${nama}`);

        $('#downloadFotoButton').attr('data-download-url', foto ? `/Kelompok1_IC-ASSIST/res/imageUser/${foto}` : '#');
        $('#downloadCVButton').attr('data-download-url', cv ? `/Kelompok1_IC-ASSIST/res/berkasUser/${cv}` : '#');
        $('#downloadTranskripButton').attr('data-download-url', transkrip ? `/Kelompok1_IC-ASSIST/res/berkasUser/${transkrip}` : '#');
        $('#downloadSuratButton').attr('data-download-url', surat ? `/Kelompok1_IC-ASSIST/res/berkasUser/${surat}` : '#');
    });

    $('button[data-download-url]').on('click', function () {
        const url = $(this).data('download-url');
        if (url && url !== '#') {
            window.location.href = url;
        } else {
            alert('Berkas tidak tersedia.');
        }
    });
    $('#daftar tbody').on('click', 'img[alt="delete"]', function (event) {
        event.stopPropagation();
        const id = $(this).closest('tr').data('id');
        const userid = $(this).closest('tr').data('userid');
        $('#deleteModal')
            .data('id', id)
            .data('userid', userid)
            .modal('show');
    });

    $('#daftar tbody').on('click', 'img[alt="edit"]', function (event) {
        event.stopPropagation();
        const id = $(this).closest('tr').data('id');
        $('#editModal').data('id', id).modal('show');
    });

    $('#acceptButton').on('click', function () {
        const idToSend = $('#detailModal').data('id');
        console.log('ID yang dikirim ke server: ', idToSend);

        if (!idToSend) {
            alert('ID tidak ditemukan di modal!');
            return;
        }

        $.ajax({
            url: '<?= APP_URL ?>/acceptberkas',
            type: 'POST',
            data: { id: idToSend },
            success: function (response) {
                if (response.status === 'success') {
                    showModal(
                        "Mahasiswa berhasil di terima",
                        "/Kelompok1_IC-ASSIST/public/Assets/gif/success.gif"
                    );
                    document.querySelector('a[data-page="lihatPeserta"]').click();
                    $('#detailModal').modal('hide');
                } else {
                    showModal(
                        "Gagal menerima mahasiswa",
                        "/Kelompok1_IC-ASSIST/public/Assets/gif/failed.gif"
                    );
                    console.log(response.message);
                }
            },
            error: function (xhr) {
                console.error('Error: ', xhr.responseText);
            }
        });
    });

    $('#confirmDelete').on('click', function (e) {
        e.preventDefault();
        const userid = $('#deleteModal').data('userid');
        console.log(userid);
        $.ajax({
            url: '<?= APP_URL ?>/deletemhs',
            type: 'POST',
            data: { id: userid },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    showModal(
                        "Mahasiswa berhasil dihapus",
                        "/Kelompok1_IC-ASSIST/public/Assets/gif/success.gif"
                    );
                    document.querySelector('a[data-page="lihatPeserta"]').click();
                    $('#deleteModal').modal('hide');

                } else {
                    showModal(
                        "Gagal menghapus mahasiswa",
                        "/Kelompok1_IC-ASSIST/public/Assets/gif/failed.gif"
                    );
                    $('#deleteModal').modal('hide');
                    console.log(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('error: ', xhr.responseText);
                console.error('status: ', status);
                console.error('error: ', error);
            }
        });
    });
    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editModal').data('id');
        const message = $('#message').val();

        console.log(id, message);
        $.ajax({
            url: '<?= APP_URL ?>/notification',
            type: 'POST',
            data: { id: id, message: message },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    showModal(
                        "Pesan berhasil dikirim",
                        "/Kelompok1_IC-ASSIST/public/Assets/gif/success.gif"
                    );
                    document.querySelector('a[data-page="lihatPeserta"]').click();
                } else {
                    showModal(
                        response.message || "Gagal mengirim pesan",
                        "/Kelompok1_IC-ASSIST/public/Assets/gif/failed.gif"
                    );
                }
            },
            error: function (xhr, status, error, response) {
                console.log('error: ', xhr.responseText);
                console.log('status: ', status);
                console.log('error : ', error);
            }
        });
        $('#editModal').modal('hide');
    });

</script>