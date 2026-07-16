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

<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- Hidden custom buttons for DataTables -->
    <button class="dt-custom-button hidden px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl items-center gap-2 transition shadow-sm border-0" type="button" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
        <i class="bi bi-plus-circle"></i> Tambah Jadwal
    </button>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table id="wawancaraMahasiswa" class="min-w-full align-middle text-sm text-left">
                <thead>
                    <tr class="">
                        <th class="dt-head-cell text-center" style="width: 5%;">No</th>
                        <th class="dt-head-cell" style="width: 25%;">Nama Lengkap</th>
                        <th class="dt-head-cell" style="width: 15%;">Stambuk</th>
                        <th class="dt-head-cell" style="width: 20%;">Kegiatan</th>
                        <th class="dt-head-cell" style="width: 10%;">Ruangan</th>
                        <th class="dt-head-cell" style="width: 10%;">Tanggal</th>
                        <th class="dt-head-cell" style="width: 10%;">Waktu</th>
                        <th class="dt-head-cell text-center" style="width: 5%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="bg-white divide-y divide-slate-100">
                    <?php if (empty($wawancara)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <i class="bi bi-inbox text-5xl block mb-3 opacity-55"></i>
                                <span class="font-semibold text-sm">Belum ada data jadwal wawancara</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; ?>
                        <?php foreach ($wawancara as $row): ?>
                            <tr class="dt-body-row border-b border-slate-100 hover:bg-slate-50 transition" data-id="<?= $row['id'] ?>" data-userid="<?= $row['id_mahasiswa'] ?>">
                                <td class="text-center py-4 px-4"><?= $i ?></td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= \App\Controllers\HomeController::getUserPhotoPath($row['foto'] ?? 'default.png') ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                        <div>
                                            <div class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['stambuk']) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['jenis_wawancara']) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['ruangan']) ?></td>
                                <td class="py-4 px-4"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['waktu']) ?></td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors border-0 open-update" 
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
                                        <button class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors border-0 btn-delete-wawancara" 
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
</div>

<!-- Add Jadwal Modal -->
<div class="modal fade modal-wawancara" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl">
                <h5 class="modal-title font-bold text-lg" id="addJadwalModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addJadwalForm" method="POST" action="javascript:void(0);">
                <div class="modal-body p-6 space-y-4">
                    <div>
                        <label for="mahasiswa" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pilih Mahasiswa</label>
                        <div class="flex gap-2 mb-3">
                             <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="mahasiswa">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                    <option value="<?= $mahasiswa['id'] ?>">
                                        <?= $mahasiswa['stambuk'] ?> - <?= $mahasiswa['nama_lengkap'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm transition shrink-0 border-0" id="addMahasiswaButton">Tambah</button>
                        </div>
                        <ul class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden shadow-sm" id="selectedMahasiswaList" style="max-height: 150px; overflow-y: auto;">
                        </ul>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="ruangan" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ruangan</label>
                            <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="ruangan" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php foreach ($ruanganList as $ruangan): ?>
                                    <option value="<?= $ruangan['id'] ?>">
                                        <?= $ruangan['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="wawancara" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Kegiatan</label>
                            <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="wawancara" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                                <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal</label>
                            <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="tanggal" required>
                        </div>
                        <div>
                            <label for="waktu" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu</label>
                            <input type="time" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="waktu" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="addJadwalForm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Tambah Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Wawancara Modal -->
<div class="modal fade modal-wawancara" id="updateWawancaraModal" tabindex="-1" aria-labelledby="updateWawancaraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl">
                <h5 class="modal-title font-bold text-lg" id="updateWawancaraModalLabel"><i class="bi bi-pencil-square me-2"></i>Update Wawancara</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateWawancaraForm" method="POST" action="javascript:void(0);">
                <div class="modal-body p-6 space-y-4">
                    <input type="hidden" id="updateWawancaraId">
                    <div>
                        <label for="updateRuangan" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ruangan</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateRuangan" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $ruangan): ?>
                                <option value="<?= $ruangan['id'] ?>">
                                    <?= $ruangan['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="updateTanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal</label>
                        <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateTanggal" required>
                    </div>
                    <div>
                        <label for="updateWaktu" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu</label>
                        <input type="time" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateWaktu" required>
                    </div>
                    <div>
                        <label for="updateJenisWawancara" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Wawancara</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition bg-white" id="updateJenisWawancara" required>
                            <option value="" disabled selected>-- Pilih Jenis Wawancara --</option>
                            <option value="wawancara kepala lab I">Wawancara Kepala Lab I</option>
                            <option value="wawancara kepala lab II">Wawancara Kepala Lab II</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Update Jadwal</button>
                </div>
            </form>
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
                listItem.className = "flex justify-between items-center bg-slate-50/80 px-4 py-2 text-sm font-semibold text-slate-700 rounded-lg mb-1";
                listItem.textContent = mahasiswa.text;

                const removeButton = document.createElement("button");
                removeButton.className = "px-2.5 py-1 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border-0";
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




        function saveWawancara(data, modalId) {
            $.ajax({
                url: "<?= APP_URL ?>/wawancara",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.status === 'success') {
                        $(modalId).modal('hide');
                        showAlert("Jadwal berhasil disimpan", true);
                        document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showAlert("Jadwal gagal disimpan: " + response.message, false);
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
                        showAlert("Jadwal berhasil diupdate", true);
                        $("#updateWawancaraModal").modal("hide");
                        // Refresh content via existing sidebar trigger
                        document.querySelector('a[data-page="wawancara"]').click();
                    } else {
                        showAlert("Gagal mengupdate jadwal wawancara: " + (response.message || "Unknown error"), false);
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    showAlert("Gagal menghubungi server", false);
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
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <i class="bi bi-search text-5xl block mb-3 opacity-55"></i>
                                <span class="font-semibold text-sm">Data yang Anda cari tidak ditemukan</span>
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
                                    <td colspan="8" class="text-center py-16 text-slate-400">
                                        <i class="bi bi-inbox text-5xl block mb-3 opacity-55"></i>
                                        <span class="font-semibold text-sm">Belum ada data jadwal wawancara di ruangan ini</span>
                                    </td>
                                </tr>
                            `);
                        } else {
                            response.data.forEach(row => {
                                tableBody.append(`
                                    <tr class="dt-body-row border-b border-slate-100 hover:bg-slate-50 transition" data-id="${row.id}" data-userid="${row.idUser}">
                                        <td class="text-center py-4 px-4">${i}</td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <img src="${row.photoPath || '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'}" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                                <div>
                                                    <div class="font-bold text-slate-800">${row.nama_lengkap}</div>
                                                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">${row.stambuk}</td>
                                        <td class="py-4 px-4">${row.jenis_wawancara}</td>
                                        <td class="py-4 px-4">${row.ruangan}</td>
                                        <td class="py-4 px-4">${formatDate(row.tanggal)}</td>
                                        <td class="py-4 px-4">${row.waktu}</td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex justify-center gap-2">
                                                <button class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors border-0 open-update" 
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
                                                <button class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors border-0 btn-delete-wawancara" 
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

