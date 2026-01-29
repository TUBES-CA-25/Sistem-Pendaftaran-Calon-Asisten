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

<style>
    /* Custom styles for action buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        min-width: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-action i {
        font-size: 1rem;
        line-height: 1;
    }
</style>

<div class="container-fluid px-4 py-4">

            <!-- Table Controls -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-3">
                <div class="position-relative" style="width: 280px;">
                    <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="searchInput" class="form-control rounded-3 ps-5" placeholder="Cari nama atau stambuk...">
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-primary btn-gradient-primary border-0 rounded-4 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                        <i class="bi bi-plus-circle"></i> Tambah Jadwal
                    </button>
                    <button class="btn btn-success btn-gradient-success border-0 rounded-4 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#bulkScheduleModal">
                        <i class="bi bi-calendar-plus"></i> Bulk Schedule
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="table-responsive rounded-3">
                <table id="wawancaraMahasiswa" class="table table-hover table-bordered align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 px-3 text-center" width="5%">NO</th>
                            <th class="py-3 px-3" width="25%">NAMA LENGKAP</th>
                            <th class="py-3 px-3" width="15%">STAMBUK</th>
                            <th class="py-3 px-3" width="20%">KEGIATAN</th>
                            <th class="py-3 px-3" width="10%">RUANGAN</th>
                            <th class="py-3 px-3" width="10%">TANGGAL</th>
                            <th class="py-3 px-3" width="10%">WAKTU</th>
                            <th class="py-3 px-3 text-center" width="5%">AKSI</th>
                        </tr>
                    </thead>
                <tbody id="table-body" class="bg-white">
                    <?php if (empty($wawancara)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada data jadwal wawancara
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($wawancara as $row): ?>
                            <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                                <td class="text-center fw-bold text-secondary"><?= $i ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                </td>
                                <td class="fw-medium text-secondary"><?= htmlspecialchars($row['stambuk']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_wawancara']) ?></td>
                                <td><?= htmlspecialchars($row['ruangan']) ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['waktu']) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 open-update" 
                                                data-bs-toggle="modal" data-bs-target="#updateWawancaraModal"
                                                data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                data-stambuk="<?= htmlspecialchars($row['stambuk']) ?>"
                                                data-ruangan="<?= htmlspecialchars($row['ruangan']) ?>"
                                                data-ruangan_id="<?= $row['id_ruangan'] ?>"
                                                data-jeniswawancara="<?= htmlspecialchars($row['jenis_wawancara']) ?>"
                                                data-waktu="<?= htmlspecialchars($row['waktu']) ?>"
                                                data-tanggal="<?= htmlspecialchars($row['tanggal']) ?>"
                                                data-id="<?= $row['id'] ?>"
                                                title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 btn-delete-wawancara" 
                                                data-id="<?= $row['id'] ?>"
                                                title="Hapus Data">
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

<!-- Modal Bulk Schedule Interview -->
<div class="modal fade" id="bulkScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header modal-header-gradient border-0">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-calendar-plus me-2"></i>Bulk Schedule Wawancara</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="bulkInterviewForm">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Pilih Mahasiswa</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchBulkMhs" class="form-control border-start-0" placeholder="Cari stambuk/nama...">
                            </div>
                            <div class="border rounded-3 p-2 shadow-sm" style="max-height: 300px; overflow-y: auto;">
                                <div class="list-group list-group-flush" id="bulkMahasiswaChecklist">
                                    <?php foreach ($mahasiswaList as $m): ?>
                                        <label class="list-group-item d-flex align-items-center gap-3 py-2 cursor-pointer">
                                            <input class="form-check-input m-0 flex-shrink-0" type="checkbox" value="<?= $m['id'] ?>">
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small"><?= htmlspecialchars($m['nama_lengkap']) ?></span>
                                                <span class="text-secondary smaller" style="font-size: 0.75rem;"><?= htmlspecialchars($m['stambuk']) ?></span>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <span class="text-muted smaller" id="selectedCount">0 dipilih</span>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="selectAllBulk">Pilih Semua</button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ruangan</label>
                                <select class="form-select" name="ruangan" required>
                                    <option value="" disabled selected>Pilih Ruangan</option>
                                    <?php foreach ($ruanganList as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Wawancara</label>
                                <select class="form-select" name="wawancara" required>
                                    <option value="" disabled selected>Pilih Jenis</option>
                                    <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                                    <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Waktu Mulai</label>
                                <input type="time" class="form-control" name="waktu" required>
                            </div>
                            <div class="alert alert-info border-0 shadow-sm p-2 mb-0" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Seluruh mahasiswa terpilih akan dijadwalkan pada waktu dan ruangan yang sama.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="bulkInterviewForm" class="btn btn-success btn-gradient-success px-4 rounded-3 text-white">Simpan Bulk</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-wawancara" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header modal-header-gradient border-0">
                <h5 class="modal-title fw-bold text-white" id="addJadwalModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addJadwalForm">
                    <div class="mb-3">
                        <label for="mahasiswa" class="form-label fw-bold">Pilih Mahasiswa</label>
                        <div class="d-flex gap-2 mb-2">
                             <select class="form-select" id="mahasiswa">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                    <option value="<?= $mahasiswa['id'] ?>">
                                        <?= $mahasiswa['stambuk'] ?> - <?= $mahasiswa['nama_lengkap'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-secondary" id="addMahasiswaButton">Tambah</button>
                        </div>
                        <ul class="list-group list-group-flush border rounded-3 overflow-hidden shadow-sm" id="selectedMahasiswaList" style="max-height: 150px; overflow-y: auto;">
                        </ul>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 mb-3">
                            <label for="ruangan" class="form-label fw-bold">Ruangan</label>
                            <select class="form-select" id="ruangan" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php foreach ($ruanganList as $ruangan): ?>
                                    <option value="<?= $ruangan['id'] ?>">
                                        <?= $ruangan['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="wawancara" class="form-label fw-bold">Jenis Kegiatan</label>
                            <select class="form-select" id="wawancara" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                                <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 mb-3">
                            <label for="tanggal" class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="waktu" class="form-label fw-bold">Waktu</label>
                            <input type="time" class="form-control" id="waktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addJadwalForm" class="btn btn-primary px-4 rounded-3">Tambah Jadwal</button>
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

        function formatDate(dateString) {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

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


        // Bulk Search logic
        $('#searchBulkMhs').on('keyup', function() {
            let filter = $(this).val().toLowerCase();
            $('#bulkMahasiswaChecklist label').each(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1);
            });
        });

        // Select All Bulk
        $('#selectAllBulk').click(function() {
            const isAllChecked = $('#bulkMahasiswaChecklist input:checked').length === $('#bulkMahasiswaChecklist input:visible').length;
            $('#bulkMahasiswaChecklist input:visible').prop('checked', !isAllChecked).trigger('change');
            $(this).text(!isAllChecked ? 'Batalkan Semua' : 'Pilih Semua');
        });

        $(document).on('change', '#bulkMahasiswaChecklist input', function() {
            $('#selectedCount').text($('#bulkMahasiswaChecklist input:checked').length + ' dipilih');
        });

        // Save Bulk Interview
        $('#bulkInterviewForm').submit(function(e) {
            e.preventDefault();
            const checked = [];
            $('#bulkMahasiswaChecklist input:checked').each(function() { checked.push($(this).val()); });

            if (checked.length === 0) return showAlert('Pilih minimal satu mahasiswa', false);

            const fd = new FormData(this);
            const data = {
                id: checked,
                ruangan: fd.get('ruangan'),
                wawancara: fd.get('wawancara'),
                tanggal: fd.get('tanggal'),
                waktu: fd.get('waktu')
            };

            saveWawancara(data, '#bulkScheduleModal');
        });

        function saveWawancara(data, modalId) {
            $.ajax({
                url: "<?= APP_URL ?>/wawancara",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.status === 'success') {
                        $(modalId).modal('hide');
                        showModal("Jadwal berhasil disimpan");
                        document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showModal("Jadwal gagal disimpan: " + response.message);
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    showAlert("Gagal menyimpan jadwal. Silakan coba lagi.", false);
                }
            });
        }

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

            const jadwalData = {
                id,
                ruangan,
                tanggal,
                waktu,
                wawancara,
            };

            saveWawancara(jadwalData, '#addJadwalModal');
        });
        $(document).on("click", ".open-update", function () {
            const btn = $(this);
            const id = btn.data("id") || btn.attr("data-id");
            const ruangan_id = btn.data("ruangan_id") || btn.attr("data-ruangan_id");
            const jenisWawancara = btn.data("jeniswawancara") || btn.attr("data-jeniswawancara");
            const waktu = btn.data("waktu") || btn.attr("data-waktu");
            const tanggal = btn.data("tanggal") || btn.attr("data-tanggal");

            $("#updateWawancaraId").val(id);
            $("#updateRuangan").val(ruangan_id);
            $("#updateJenisWawancara").val(jenisWawancara);
            $("#updateWaktu").val(waktu);
            $("#updateTanggal").val(tanggal);
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

            $.ajax({
                url: "<?= APP_URL ?>/updatewawancara",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(updateData),
                success: function (response) {
                    if (response.status === "success") {
                        showModal("Jadwal berhasil diupdate");
                        $("#updateWawancaraModal").modal("hide");
                        // Refresh content via existing sidebar trigger
                        document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showModal("Gagal mengupdate jadwal wawancara: " + (response.message || "Unknown error"));
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    showModal("Gagal menghubungi server");
                },
            });
        });

        $(document).on("click", ".btn-delete-wawancara", function (e) {
            e.preventDefault();
            const btn = $(this);
            const id = btn.data("id") || btn.attr("data-id");
            
            if (!id) {
                console.error("Delete failed: No ID found on button.");
                return;
            }

            showConfirmDelete(function() {
                $.ajax({
                    url: "<?= APP_URL ?>/deletewawancara",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ id: id }),
                    success: function (response) {
                        if (response.status === "success") {
                            showAlert("Jadwal berhasil dihapus", true);
                            
                            // Remove the row from the table
                            const rowToRemove = $(`.btn-delete-wawancara[data-id="${id}"]`).closest('tr');
                            rowToRemove.fadeOut(300, function() { 
                                $(this).remove(); 
                                // Update row numbers
                                $("#table-body tr:not(#noResultsRow)").each(function(index) {
                                    $(this).find('td:first-child').text(index + 1);
                                });
                            });
                        } else {
                            showAlert("Gagal menghapus jadwal: " + (response.message || "Unknown error"), false);
                        }
                    },
                    error: function (xhr) {
                        console.error("Delete AJAX Error:", xhr.responseText);
                        showAlert("Gagal menghubungi server untuk menghapus jadwal.", false);
                    },
                });
            }, "Apakah Anda yakin ingin menghapus jadwal wawancara ini?");
        });

        // Search functionality
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#table-body tr").filter(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(value) > -1);
            });
            
            // Handle "No results found"
            var visibleRows = $("#table-body tr:not(#noResultsRow):visible").length;
            if (visibleRows === 0 && $("#table-body tr:not(#noResultsRow)").length > 0) {
                if ($('#noResultsRow').length === 0) {
                    $("#table-body").append(`
                        <tr id="noResultsRow">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                Data yang Anda cari tidak ditemukan
                            </td>
                        </tr>
                    `);
                }
            } else {
                $('#noResultsRow').remove();
            }
        });

        $(".filter-btn").click(function () {
            let ruanganId = parseInt($(this).attr("data-id"), 10);
            let requestData = { id: ruanganId };
            
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
                        
                        if (response.data.length === 0) {
                            tableBody.append(`
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                        Belum ada data jadwal wawancara di ruangan ini
                                    </td>
                                </tr>
                            `);
                        } else {
                            response.data.forEach(row => {
                                tableBody.append(`
                                    <tr data-id="${row.id}" data-userid="${row.id_mahasiswa}">
                                        <td class="text-center fw-bold text-secondary">${i}</td>
                                        <td>
                                            <div class="fw-bold text-dark">${row.nama_lengkap}</div>
                                        </td>
                                        <td class="fw-medium text-secondary">${row.stambuk}</td>
                                        <td>${row.jenis_wawancara}</td>
                                        <td>${row.ruangan}</td>
                                        <td>${formatDate(row.tanggal)}</td>
                                        <td>${row.waktu}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-action bg-warning-subtle text-warning border-0 rounded-3 open-update" 
                                                        data-bs-toggle="modal" data-bs-target="#updateWawancaraModal"
                                                        data-nama="${row.nama_lengkap}"
                                                        data-stambuk="${row.stambuk}"
                                                        data-ruangan="${row.ruangan}"
                                                        data-ruangan_id="${row.id_ruangan}"
                                                        data-jeniswawancara="${row.jenis_wawancara}"
                                                        data-waktu="${row.waktu}"
                                                        data-tanggal="${row.tanggal}"
                                                        data-id="${row.id}"
                                                        title="Edit Data">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-action bg-danger-subtle text-danger border-0 rounded-3 btn-delete-wawancara" 
                                                        data-id="${row.id}"
                                                        title="Hapus Data">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `);
                                i++;
                            });
                        }
                    } else {
                        showAlert(response.message || "Gagal memfilter data", false);
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
