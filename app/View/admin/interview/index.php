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

<?php
    $title = 'Kelola Wawancara';
    $subtitle = 'Kelola jadwal wawancara peserta';
    $icon = 'bi bi-calendar-event';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <!-- Table Controls -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="position-relative" style="flex: 1; max-width: 400px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="searchInput" class="form-control ps-5 rounded-3" placeholder="Cari nama atau stambuk...">
                </div>
                
                <button type="button" data-bs-toggle="modal" data-bs-target="#addJadwalModal" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-4 py-2">
                    <i class="bi bi-plus-circle"></i> 
                    <span>Tambah Data</span>
                </button>
            </div>

            <!-- Data Table -->
            <div class="table-responsive rounded-3">
                <table id="wawancaraMahasiswa" class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 px-3 text-center" width="5%">NO</th>
                            <th class="py-3 px-3" width="30%">NAMA LENGKAP</th>
                            <th class="py-3 px-3" width="20%">STAMBUK</th>
                            <th class="py-3 px-3 text-center" width="20%">WAWANCARA I</th>
                            <th class="py-3 px-3 text-center" width="20%">WAWANCARA II</th>
                            <th class="py-3 px-3 text-center" width="5%">AKSI</th>
                        </tr>
                    </thead>
                <tbody id="table-body" class="bg-white">
                    <?php if (empty($wawancara)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada data mahasiswa
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        // Determine status badges logic
                        if (!function_exists('getStatusBadge')) {
                            function getStatusBadge($absensiStatus, $hasSchedule) {
                                if (!empty($absensiStatus) && $absensiStatus !== 'Belum Ada') {
                                    if ($absensiStatus == 'Hadir') return '<span class="badge bg-success rounded-pill px-3">Hadir</span>';
                                    if ($absensiStatus == 'Tidak Hadir' || $absensiStatus == 'Alpha') return '<span class="badge bg-danger rounded-pill px-3">Alpha</span>';
                                    return '<span class="badge bg-secondary rounded-pill px-3">' . $absensiStatus . '</span>';
                                }
                                if ($hasSchedule) {
                                    return '<span class="badge bg-info text-white rounded-pill px-3">Terjadwal</span>';
                                }
                                return '<span class="badge bg-light text-secondary border rounded-pill px-3">Belum Ada</span>';
                            }
                        }
                        ?>
                        <?php $i = 1; ?>
                        <?php foreach ($wawancara as $row): 
                            $waw1 = getStatusBadge($row['absensi_wawancara_I'] ?? null, $row['wawancara_I_schedule'] ?? false);
                            $waw2 = getStatusBadge($row['absensi_wawancara_II'] ?? null, $row['wawancara_II_schedule'] ?? false);
                        ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $i ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" 
                                             style="width: 40px; height: 40px; font-size: 1.2rem;">
                                            <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                            <div class="text-muted small">Mahasiswa</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium text-secondary"><?= htmlspecialchars($row['stambuk']) ?></td>
                                <td class="text-center"><?= $waw1 ?></td>
                                <td class="text-center"><?= $waw2 ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle border-0 open-detail" 
                                                data-bs-toggle="modal" data-bs-target="#wawancaraModal"
                                                data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                data-id="<?= $row['id'] ?>"
                                                title="Lihat Detail" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning rounded-circle border-0" 
                                                id="editButton" data-id="<?= $row['id'] ?>"
                                                title="Edit Data" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle border-0" 
                                                id="deleteButton" data-id="<?= $row['id'] ?>"
                                                title="Hapus Data" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    // Simple Client-side Search (Optional, given server-side might be better for large data)
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#table-body tr');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

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
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
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
                        <select class="form-select" id="updateJenisWawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
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