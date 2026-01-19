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
$colors = ['#2f66f6'];
?>
<style>
    /* Import Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
        font-family: 'Poppins', sans-serif;
    }

    /* ==================== PAGE HEADER ==================== */
    .page-header {
        background: #2f66f6;
        padding: 35px 30px;
        border-radius: 0;
        position: relative;
        overflow: hidden;
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
        color: #fff;
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .page-header h1 i {
        font-size: 1.5rem;
    }

    .page-header .subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.95rem;
        margin-top: 8px;
        position: relative;
        z-index: 1;
    }

    /* ==================== CARD CONTENT ==================== */
    .card-content {
        background: #fff;
        border-radius: 0;
        padding: 24px;
        margin: 0;
        min-height: calc(100vh - 140px);
    }

    /* ==================== TABLE CONTROLS ==================== */
    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* ==================== PRIMARY BUTTON ==================== */
    .btn-add {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add:hover {
        background: linear-gradient(135deg, #1e4fd8 0%, #1a3fc0 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(47, 102, 246, 0.4);
        color: white;
    }

    /* ==================== DATA TABLE ==================== */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .data-table thead th {
        background: #2f66f6;
        color: #fff;
        font-weight: 600;
        padding: 16px 20px;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .data-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        font-size: 0.9rem;
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

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Clickable name */
    .open-detail {
        color: #2f66f6;
        cursor: pointer;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .open-detail:hover {
        color: #1e4fd8;
        text-decoration: underline;
    }

    /* ==================== MODAL STYLES ==================== */
    .modal-wawancara .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-wawancara .modal-header {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: #fff;
        border-radius: 0;
        padding: 20px 24px;
        border: none;
    }

    .modal-wawancara .modal-header h5 {
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .modal-wawancara .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-wawancara .btn-close:hover {
        opacity: 1;
    }

    .modal-wawancara .modal-body {
        padding: 24px;
        color: #333;
    }

    .modal-wawancara .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 16px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* ==================== FORM STYLES ==================== */
    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        padding: 10px 14px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2f66f6;
        box-shadow: 0 0 0 3px rgba(47, 102, 246, 0.15);
        outline: none;
    }

    .form-select {
        appearance: none;
        background: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%232f66f6' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 5.646a.5.5 0 0 1 .708 0L8 11.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") no-repeat right 12px center;
        background-color: #fff;
        background-size: 12px 12px;
    }

    .form-control-plaintext {
        padding: 8px 0;
        font-size: 0.95rem;
        color: #1f2937;
        font-weight: 500;
    }

    /* ==================== BUTTONS ==================== */
    .btn-primary {
        background: linear-gradient(135deg, #2f66f6 0%, #1e4fd8 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e4fd8 0%, #1a3fc0 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(47, 102, 246, 0.35);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-success {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        color: white;
    }

    /* ==================== LIST GROUP ==================== */
    .list-group-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px !important;
        margin-bottom: 6px;
        padding: 10px 14px;
        font-size: 0.9rem;
    }

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 768px) {
        .page-header {
            padding: 25px 20px;
        }

        .page-header h1 {
            font-size: 1.4rem;
        }

        .card-content {
            padding: 16px;
        }

        .table-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-buttons {
            justify-content: center;
        }

        .data-table thead th,
        .data-table tbody td {
            padding: 12px 14px;
            font-size: 0.85rem;
        }
    }
</style>

<main>
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-calendar-event"></i> Jadwal Kegiatan</h1>
        <p class="subtitle">Kelola jadwal wawancara dan kegiatan seleksi</p>
    </div>

    <!-- Card Content -->
    <div class="card-content">
        <!-- Table Controls -->
        <div class="table-controls">
            <div class="filter-buttons">
                <?php foreach ($ruanganList as $index => $ruangan): ?>
                    <button id="filter-<?= $ruangan['id'] ?>" class="btn text-white filter-btn"
                        data-id="<?= (int) $ruangan['id'] ?>"
                        style="background-color: <?= $colors[$index % count($colors)] ?>;">
                        <?= htmlspecialchars($ruangan['nama']) ?>
                    </button>
                <?php endforeach; ?>
                <button id="filter-all" class="btn btn-dark filter-btn" data-id=0>Semua</button>
            </div>
            <button type="button" data-bs-toggle="modal" data-bs-target="#addJadwalModal" class="btn btn-add">
                <i class="bi bi-plus-circle"></i> Tambah Jadwal
            </button>
        </div>

        <!-- Data Table -->
        <table id="wawancaraMahasiswa" class="data-table">
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
                                data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>" data-stambuk="<?= htmlspecialchars($row['stambuk']) ?>"
                                data-ruangan="<?= htmlspecialchars($row['ruangan']) ?>" data-jeniswawancara="<?= htmlspecialchars($row['jenis_wawancara']) ?>"
                                data-waktu="<?= htmlspecialchars($row['waktu']) ?>" data-tanggal="<?= htmlspecialchars($row['tanggal']) ?>">
                                <?= htmlspecialchars($row['nama_lengkap']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['stambuk']) ?></td>
                        <td><?= htmlspecialchars($row['ruangan']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_wawancara']) ?></td>
                        <td><?= htmlspecialchars($row['waktu']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    </tr>
                    <?php $i++; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<div class="modal fade modal-wawancara" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJadwalModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal</h5>
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

<div class="modal fade modal-wawancara" id="wawancaraModal" tabindex="-1" aria-labelledby="presentasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="presentasiModalLabel"><i class="bi bi-info-circle me-2"></i>Detail Wawancara</h5>
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

<div class="modal fade modal-wawancara" id="updateWawancaraModal" tabindex="-1" aria-labelledby="updateWawancaraModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateWawancaraModalLabel"><i class="bi bi-pencil-square me-2"></i>Update Wawancara</h5>
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
            const modalEl = document.getElementById('customModal');
            if (!modalEl) return;

            const modalMessage = document.getElementById('modalMessage');
            const modalGif = document.getElementById('modalGif');

            if (modalMessage) modalMessage.textContent = message;
            if (modalGif) {
                modalGif.style.display = gifUrl ? 'block' : 'none';
                if (gifUrl) modalGif.src = gifUrl;
            }

            // Use Bootstrap Modal API
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function showConfirm(message, onConfirm = null, onCancel = null) {
            const modalEl = document.getElementById('confirmModal');
            if (!modalEl) return;

            const modalMessage = document.getElementById('confirmModalMessage');
            const confirmButton = document.getElementById('confirmModalConfirm');
            const cancelButton = document.getElementById('confirmModalCancel');

            if (modalMessage) modalMessage.textContent = message;

            // Use Bootstrap Modal API
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            if (confirmButton) {
                const newConfirmBtn = confirmButton.cloneNode(true);
                confirmButton.parentNode.replaceChild(newConfirmBtn, confirmButton);
                newConfirmBtn.addEventListener('click', () => {
                    if (onConfirm) onConfirm();
                    modal.hide();
                });
            }

            if (cancelButton) {
                const newCancelBtn = cancelButton.cloneNode(true);
                cancelButton.parentNode.replaceChild(newCancelBtn, cancelButton);
                newCancelBtn.addEventListener('click', () => {
                    if (onCancel) onCancel();
                    modal.hide();
                });
            }

            modal.show();
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
                showAlert("Pilih mahasiswa terlebih dahulu!", false);
                return;
            }

            if (selectedMahasiswa.some((item) => item.id === mahasiswaId)) {
                showAlert("Mahasiswa sudah dipilih!", false);
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
                showAlert("Pilih setidaknya satu mahasiswa!", false);
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
                    showAlert("Gagal menyimpan jadwal. Silakan coba lagi.", false);
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

            showConfirmDelete(function() {
                $.ajax({
                    url: "<?= APP_URL ?>/deletewawancara",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ id }),
                    success: function (response) {
                        if (response.status === "success") {
                            showAlert("Jadwal berhasil dihapus");
                            // Close the detail modal
                            $('#wawancaraModal').modal('hide');
                            // Remove the row from the table
                            $(`tr[data-id="${id}"]`).fadeOut(300, function() { $(this).remove(); });
                        } else {
                            showAlert("Gagal menghapus jadwal", false);
                        }
                    },
                    error: function (xhr) {
                        console.error("Error:", xhr.responseText);
                        showAlert("Gagal menghapus jadwal wawancara. Silakan coba lagi.", false);
                    },
                });
            }, "Apakah Anda yakin ingin menghapus jadwal wawancara ini?");
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
                    showAlert("Terjadi kesalahan dalam mengambil data. Silakan coba lagi.", false);
                }
            });
        });
    });
</script>