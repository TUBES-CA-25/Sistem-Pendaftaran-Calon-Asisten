<?php
/**
 * Wawancara Admin View
 * 
 * Data yang diterima dari controller:
 * @var array $wawancara - Data wawancara
 * @var array $mahasiswaList - Daftar mahasiswa
 * @var array $ruanganList - Daftar ruangan
 */
$wawancara = $wawancara ?? [];
$mahasiswaList = $mahasiswaList ?? [];
$ruanganList = $ruanganList ?? [];
$colors = ['#3357FF'];
?>
<style>
    /* Import Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    /* Primary Button */
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

    /* Action Buttons */
    .edit-button,
    .delete-button {
        width: 24px;
        height: 24px;
        transition: transform 0.3s ease;
    }

    .edit-button:hover,
    .delete-button:hover {
        transform: scale(1.2);
    }

    /* Modal Styles */
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
        font-size: 1.2rem;
        font-weight: 600;
    }

    .modal-body {
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

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #ccc;
        padding: 10px 15px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3DC2EC;
        box-shadow: 0 0 5px rgba(61, 194, 236, 0.5);
        outline: none;
    }

    .form-select {
        appearance: none;
        background: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%233DC2EC' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 5.646a.5.5 0 0 1 .708 0L8 11.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") no-repeat right 12px center;
        background-size: 12px 12px;
    }
</style>

<main>
    <h1 class="dashboard">Jadwal Kegiatan</h1>
    <button type="button" data-bs-toggle="modal" data-bs-target="#addJadwalModal" class="btn btn-primary mb-3">
        Tambah Jadwal Kegiatan
    </button>

    <div class="d-flex gap-2 mb-3">
        <?php foreach ($ruanganList as $index => $ruangan): ?>
            <button id="filter-<?= $ruangan['id'] ?>" class="btn text-white filter-btn"
                data-id="<?= (int) $ruangan['id'] ?>"
                style="background-color: <?= $colors[$index % count($colors)] ?>; width: 200px;">
                <?= $ruangan['nama'] ?>
            </button>
        <?php endforeach; ?>
        <button id="filter-all" class="btn btn-dark filter-btn" data-id=0>Semua</button>
    </div>

    <table id="wawancaraMahasiswa" class="table table-hover rounded-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Stambuk</th>
                <th>Ruangan</th>
                <th>Jadwal Kegiatan</th>
                <th>Waktu</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <?php $i = 1; ?>
            <?php foreach ($wawancara as $row) { ?>
                <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                    <td><?= $i ?></td>
                    <td>
                        <span class="open-detail" data-bs-toggle="modal" data-bs-target="#wawancaraModal"
                            data-nama="<?= $row['nama_lengkap'] ?>" data-stambuk="<?= $row['stambuk'] ?>"
                            data-ruangan="<?= $row['ruangan'] ?>" data-jeniswawancara="<?= $row['jenis_wawancara'] ?>"
                            data-waktu="<?= $row['waktu'] ?>" data-tanggal="<?= $row['tanggal'] ?>">
                            <?= $row['nama_lengkap'] ?>
                        </span>
                    </td>
                    <td><?= $row['stambuk'] ?></td>
                    <td><?= $row['ruangan'] ?></td>
                    <td><?= $row['jenis_wawancara'] ?></td>
                    <td><?= $row['waktu'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                </tr>
                <?php $i++; ?>
            <?php } ?>
        </tbody>
    </table>
</main>

<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJadwalModalLabel">Tambah Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addJadwalForm">
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
                            mahasiswa</button>
                        <button type="button" class="btn btn-success mt-2" id="addAllMahasiswaButton">Tambah
                            Semua</button>
                    </div>
                    <div class="mb-3">
                        <label for="selectedMahasiswa" class="form-label">Mahasiswa Terpilih</label>
                        <ul class="list-group" id="selectedMahasiswaList">
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label for="ruangan" class="form-label">Ruangan</label>
                        <select class="form-select" id="ruangan" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $ruangan): ?>
                                <option value="<?= $ruangan['id'] ?>">
                                    <?= $ruangan['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="waktu" class="form-label">Waktu</label>
                        <input type="time" class="form-control" id="waktu" required>
                    </div>
                    <div class="mb-3">
                        <label for="wawancara" class="form-label">Jenis Kegiatan</label>
                        <select class="form-select" id="wawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Kegiatan --</option>
                            <option value="Tes Tertulis">Tes Tertulis</option>
                            <option value="Presentasi">Presentasi</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                            <option value="wawancara asisten">Wawancara Asisten</option>
                            <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="wawancaraModal" tabindex="-1" aria-labelledby="presentasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="presentasiModalLabel">Detail Wawancara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <p id="modalNama" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stambuk</label>
                    <p id="modalStambuk" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ruangan</label>
                    <p id="modalRuangan" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Wawancara</label>
                    <p id="modalJenisWawancara" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Waktu</label>
                    <p id="modalWaktu" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <p id="modalTanggal" class="form-control-plaintext"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editButton">Edit jadwal wawancara</button>
                <button type="button" class="btn btn-danger" id="deleteButton">Hapus jadwal wawancara</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateWawancaraModal" tabindex="-1" aria-labelledby="updateWawancaraModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateWawancaraModalLabel">Update Wawancara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateWawancaraForm">
                    <input type="hidden" id="updateWawancaraId">
                    <div class="mb-3">
                        <label for="updateRuangan" class="form-label">Ruangan</label>
                        <select class="form-select" id="updateRuangan" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $ruangan): ?>
                                <option value="<?= $ruangan['id'] ?>">
                                    <?= $ruangan['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateTanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="updateTanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateWaktu" class="form-label">Waktu</label>
                        <input type="time" class="form-control" id="updateWaktu" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateJenisWawancara" class="form-label">Jenis Wawancara</label>
                        <select class="form-select" id="updateJenisWawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
                            <option value="wawancara asisten">Wawancara Asisten</option>
                            <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Jadwal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {

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
        const mahasiswaDropdown = document.getElementById("mahasiswa");
        const addMahasiswaButton = document.getElementById("addMahasiswaButton");
        const selectedMahasiswaList = document.getElementById("selectedMahasiswaList");
        const addJadwalForm = document.getElementById("addJadwalForm");

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
                alert("Pilih mahasiswa terlebih dahulu!");
                return;
            }

            if (selectedMahasiswa.some((item) => item.id === mahasiswaId)) {
                alert("Mahasiswa sudah dipilih!");
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

                if (mahasiswaId && !selectedMahasiswa.some(item => item.id === mahasiswaId)) {
                    selectedMahasiswa.push({ id: mahasiswaId, text: mahasiswaText });
                }
            }
            renderSelectedMahasiswa();
        });

        $(addJadwalForm).on("submit", (e) => {
            e.preventDefault();

            const ruangan = document.getElementById("ruangan").value;
            const tanggal = document.getElementById("tanggal").value;
            const waktu = document.getElementById("waktu").value;
            const wawancara = document.getElementById("wawancara").value;
            let id = selectedMahasiswa.map((item) => item.id);
            if (selectedMahasiswa.length === 0) {
                alert("Pilih setidaknya satu mahasiswa!");
                return;
            }
            console.log("id " + id);
            const jadwalData = {
                id,
                ruangan,
                tanggal,
                waktu,
                wawancara,
            };
            $.ajax({
                url: "<?= APP_URL ?>/wawancara",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(jadwalData),
                success: function (response) {
                    if (response.status === 'success') {
                       showModal("Jadwal berhasil disimpan");
                       document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showModal("Jadwal gagal disimpan");
                    }

                    addJadwalForm.reset();
                    selectedMahasiswa = [];
                    renderSelectedMahasiswa();
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("Gagal menyimpan jadwal. Silakan coba lagi.");
                }
            });
            $('#addJadwalModal').modal('hide');
        });
        $(document).on("click", ".open-detail", function () {
            const id = $(this).closest("tr").data("id");
            const nama = $(this).data("nama");
            const stambuk = $(this).data("stambuk");
            const ruangan = $(this).data("ruangan");
            const jenisWawancara = $(this).data("jeniswawancara");
            const waktu = $(this).data("waktu");
            const tanggal = $(this).data("tanggal");

            $("#modalNama").text(nama || "-");
            $("#modalStambuk").text(stambuk || "-");
            $("#modalRuangan").text(ruangan || "-");
            $("#modalJenisWawancara").text(jenisWawancara || "-");
            $("#modalWaktu").text(waktu || "-");
            $("#modalTanggal").text(tanggal || "-");
            $("#editButton").data("id", id);
            $("#deleteButton").data("id", id);
        });
        $(document).on("click", "#editButton", function () {
            const id = $(this).data("id");
            const ruangan = $("#modalRuangan").text();
            const jenisWawancara = $("#modalJenisWawancara").text();
            const waktu = $("#modalWaktu").text();
            const tanggal = $("#modalTanggal").text();

            console.log("id " + id);
            $("#updateWawancaraId").val(id);
            $("#updateRuangan").val(ruangan);
            $("#updateJenisWawancara").val(jenisWawancara);
            $("#updateWaktu").val(waktu);
            $("#updateTanggal").val(tanggal);

            $("#updateWawancaraModal").modal("show");
            $("#wawancaraModal").modal("hide");
        });

        $("#updateWawancaraForm").on("submit", function (e) {
            e.preventDefault();

            const id = $("#updateWawancaraId").val();
            const ruangan = $("#updateRuangan").val();
            const tanggal = $("#updateTanggal").val();
            const waktu = $("#updateWaktu").val();
            const jenisWawancara = $("#updateJenisWawancara").val();

            const updateData = {
                id,
                ruangan,
                tanggal,
                waktu,
                jenisWawancara,
            };

            console.log("id : " + updateData.id);
            console.log("ruangan : " + updateData.ruangan);
            console.log("tanggal : " + updateData.tanggal);
            console.log("waktu : " + updateData.waktu);
            console.log("jenis wawancara : " + updateData.jenisWawancara);

            $.ajax({
                url: "<?= APP_URL ?>/updatewawancara",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(updateData),
                success: function (response) {
                    if (response.status === "success") {
                    showModal("jadwal berhasil di update");
                    document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showModal("Gagal mengupdate jadwal wawancara");
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                },
            });
        });
        $(document).on("click", "#deleteButton", function () {
            const id = $(this).data("id");

            if (!confirm("Apakah Anda yakin ingin menghapus jadwal wawancara ini?")) return;

            $.ajax({
                url: "<?= APP_URL ?>/deletewawancara",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({ id }),
                success: function (response) {
                    if (response.status === "success") {
                        showModal("Jadwal berhasil dihapus");
                        document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showModal("Gagal menghapus jadwal");
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("Gagal menghapus jadwal wawancara. Silakan coba lagi.");
                },
            });
        });

        $(".filter-btn").click(function () {
            let ruanganId = parseInt($(this).attr("data-id"), 10);

            let requestData = { id: ruanganId };
            console.log(requestData);
            $.ajax({
                url: "<?= APP_URL ?>/ruangan/getfilter",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(requestData),
                success: function (response) {
                    if (response.status === "success") {
                        let tableBody = $("#table-body");
                        tableBody.empty();
                        let i = 1;
                        response.data.forEach(row => {
                            tableBody.append(`
                            <tr data-id="${row.id}" data-userid="${row.id_mahasiswa}">
                                <td>${i}</td>
                                <td>
                                    <span class="open-detail" data-bs-toggle="modal" data-bs-target="#wawancaraModal"
                                        data-nama="${row.nama_lengkap}" data-stambuk="${row.stambuk}"
                                        data-ruangan="${row.ruangan}" data-jeniswawancara="${row.jenis_wawancara}"
                                        data-waktu="${row.waktu}" data-tanggal="${row.tanggal}">
                                        ${row.nama_lengkap}
                                    </span>
                                </td>
                                <td>${row.stambuk}</td>
                                <td>${row.ruangan}</td>
                                <td>${row.jenis_wawancara}</td>
                                <td>${row.waktu}</td>
                                <td>${row.tanggal}</td>
                            </tr>
                        `);
                            i++;
                        });

                    } else {
                        // showModal()
                        console.log("Error:", response.message);
                        console.log()
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("Terjadi kesalahan dalam mengambil data. Silakan coba lagi.");
                }
            });
        });
    });
</script>