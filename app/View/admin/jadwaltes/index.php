<?php
/**
 * Written Test Schedule View - Student Based
 * @var array $data
 */
$jadwalTesList = $data['jadwalTesList'] ?? [];
$mahasiswaList = $data['mahasiswaList'] ?? [];
$ruanganList = $data['ruanganList'] ?? [];
$bankSoalList = $data['bankSoalList'] ?? [];
?>

<main>
    <?php
        $title = 'Jadwal Tes Tertulis';
        $subtitle = 'Manajemen jadwal ujian tertulis mahasiswa per individu';
        $icon = 'bi bi-calendar-event';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Controls -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="relative w-full sm:w-72">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Cari nama atau stambuk...">
            </div>
            <div>
                <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2 shadow-md shadow-blue-500/10" type="button" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </button>
            </div>
        </div>

        <!-- Student Schedule Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm" id="jadwalTable">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                            <th class="text-center font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 5%;">NO</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-left" style="width: 25%;">NAMA LENGKAP</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-left" style="width: 15%;">STAMBUK</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-left" style="width: 20%;">KEGIATAN</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-left" style="width: 15%;">RUANGAN</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-left" style="width: 10%;">TANGGAL</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-left" style="width: 10%;">WAKTU</th>
                            <th class="text-center font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 5%;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="divide-y divide-slate-100">
                        <?php if (empty($jadwalTesList)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400 font-medium">Kosong</td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($jadwalTesList as $row): ?>
                                <tr class="hover:bg-slate-50/85 transition" data-id="<?= $row['id'] ?>">
                                    <td class="text-center font-bold text-slate-400 py-4 px-4"><?= $i++ ?></td>
                                    <td class="py-4 px-4"><span class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap']) ?></span></td>
                                    <td class="text-slate-500 py-4 px-4 font-medium"><?= htmlspecialchars($row['stambuk']) ?></td>
                                    <td class="text-slate-600 py-4 px-4"><?= htmlspecialchars($row['kegiatan']) ?></td>
                                    <td class="text-slate-600 py-4 px-4"><span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold"><?= htmlspecialchars($row['ruangan']) ?></span></td>
                                    <td class="text-slate-600 py-4 px-4 font-medium"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-slate-600 py-4 px-4 font-semibold"><?= htmlspecialchars($row['waktu']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 open-edit" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                    data-stambuk="<?= htmlspecialchars($row['stambuk']) ?>"
                                                    data-ruangan="<?= $row['ruangan'] ?>"
                                                    data-kegiatan="<?= htmlspecialchars($row['kegiatan']) ?>"
                                                    data-tanggal="<?= $row['tanggal'] ?>"
                                                    data-waktu="<?= $row['waktu'] ?>"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 delete-schedule" data-id="<?= $row['id'] ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-cyan-50 hover:bg-cyan-100 text-cyan-600 reset-exam" 
                                                    data-id="<?= $row['id_mahasiswa'] ?>" 
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                    title="Reset Pengerjaan">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </div>
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

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2"><i class="bi bi-calendar-plus text-lg"></i>Tambah Jadwal Tes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <form id="addJadwalForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa</label>
                        <div class="flex gap-2">
                             <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="mahasiswaSelect">
                                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                                <?php foreach ($mahasiswaList as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['stambuk'] ?> - <?= $m['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition flex-shrink-0" id="addMhsToList">Tambah</button>
                        </div>
                        <ul class="mt-3 space-y-2" id="selectedMhsList"></ul>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ruangan</label>
                            <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="ruanganSelect" required>
                                <option value="" disabled selected>Pilih</option>
                                <?php foreach ($ruanganList as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kegiatan</label>
                            <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="kegiatanInput" value="Tes Tertulis">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                            <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="tanggalInput" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="waktuInput" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addJadwalForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Jadwal -->
<div class="modal fade" id="updateJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2"><i class="bi bi-pencil-square text-lg"></i>Update Jadwal Tes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <form id="updateJadwalForm" class="space-y-4">
                    <input type="hidden" id="editId">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Mahasiswa</label>
                        <p id="editMhsInfo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-semibold mb-0"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ruangan</label>
                            <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editRuangan" required>
                                <?php foreach ($ruanganList as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kegiatan</label>
                            <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editKegiatan" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                            <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editTanggal" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="updateJadwalForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10">Update Jadwal</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let selectedMahasiswa = [];

    // Search logic for main table
    $('#searchInput').on('keyup', function() {
        let filter = $(this).val().toLowerCase();
        $('#table-body tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1);
        });
    });

    // Add student to the single-add modal list
    $('#addMhsToList').click(function() {
        const id = $('#mahasiswaSelect').val();
        const text = $('#mahasiswaSelect option:selected').text();
        if (!id) return;
        if (selectedMahasiswa.includes(id)) return showAlert('Mahasiswa sudah ada dalam daftar', false);

        selectedMahasiswa.push(id);
        $('#selectedMhsList').append(`
            <li class="flex justify-between items-center py-2 px-3 bg-slate-50 rounded-xl text-slate-700 text-sm border border-slate-100" data-id="${id}">
                <span>${text}</span>
                <button type="button" class="text-red-500 hover:text-red-700 remove-mhs"><i class="bi bi-x-circle text-lg"></i></button>
            </li>
        `);
    });

    $(document).on('click', '.remove-mhs', function() {
        const id = $(this).closest('li').data('id').toString();
        selectedMahasiswa = selectedMahasiswa.filter(item => item !== id);
        $(this).closest('li').remove();
    });

    // Save Single Add Schedule
    $('#addJadwalForm').submit(function(e) {
        e.preventDefault();
        if (selectedMahasiswa.length === 0) return showAlert('Pilih minimal satu mahasiswa', false);

        const data = {
            id: selectedMahasiswa,
            ruangan: $('#ruanganSelect').val(),
            kegiatan: $('#kegiatanInput').val(),
            tanggal: $('#tanggalInput').val(),
            waktu: $('#waktuInput').val()
        };

        saveSchedule(data, '#addJadwalModal');
    });

    // Open Edit Modal
    $(document).on('click', '.open-edit', function() {
        const btn = $(this);
        $('#editId').val(btn.data('id'));
        $('#editMhsInfo').text(btn.data('stambuk') + ' - ' + btn.data('nama'));
        $('#editKegiatan').val(btn.data('kegiatan'));
        $('#editTanggal').val(btn.data('tanggal'));
        $('#editWaktu').val(btn.data('waktu'));
        
        // Find room ID based on name or set manually
        const roomName = btn.data('ruangan');
        $(`#editRuangan option`).each(function() {
            if ($(this).text() === roomName) $(this).prop('selected', true);
        });

        $('#updateJadwalModal').modal('show');
    });

    // Save Update Schedule
    $('#updateJadwalForm').submit(function(e) {
        e.preventDefault();
        const data = {
            id: $('#editId').val(),
            ruangan: $('#editRuangan').val(),
            kegiatan: $('#editKegiatan').val(),
            tanggal: $('#editTanggal').val(),
            waktu: $('#editWaktu').val()
        };

        $.ajax({
            url: APP_URL + '/updateJadwalTes',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(res) {
                if (res.status === 'success') {
                    $('#updateJadwalModal').modal('hide');
                    showAlert(res.message, true);
                    document.querySelector('a[data-page="jadwaltes"]').click();
                } else {
                    showAlert(res.message, false);
                }
            }
        });
    });

    function saveSchedule(data, modalId) {
        $.ajax({
            url: APP_URL + '/saveJadwalTes',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(res) {
                if (res.status === 'success') {
                    $(modalId).modal('hide');
                    showAlert(res.message, true);
                    // Reload the page content
                    document.querySelector('a[data-page="jadwaltes"]').click();
                } else {
                    showAlert(res.message, false);
                }
            },
            error: function() {
                showAlert('Terjadi kesalahan jaringan', false);
            }
        });
    }

    // Delete Schedule
    $(document).on('click', '.delete-schedule', function() {
        const id = $(this).data('id');
        showConfirmDelete(function() {
            $.ajax({
                url: APP_URL + '/deleteJadwalTes',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: id }),
                success: function(res) {
                    if (res.status === 'success') {
                        showAlert(res.message || 'Jadwal berhasil dihapus!', true);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert(res.message, false);
                    }
                },
                error: function() {
                    showAlert('Terjadi kesalahan jaringan', false);
                }
            });
        }, 'Apakah Anda yakin ingin menghapus jadwal tes ini?');
    });

    // Reset Exam Handler
    $(document).on('click', '.reset-exam', function() {
        const idMahasiswa = $(this).data('id');
        const text = $(this).data('nama');
        
        showActionConfirmation({
            title: 'Reset Pengerjaan Tes?',
            message: `Apakah Anda yakin ingin mereset pengerjaan tes untuk <strong>${text}</strong>? <br><small class="text-red-600 font-semibold">Seluruh jawaban dan nilai akan dihapus permanen.</small>`,
            btnText: 'Reset Sekarang',
            type: 'danger',
            onConfirm: function() {
                $.ajax({
                    url: '<?= APP_URL ?>/admin/reset-ujian',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: idMahasiswa }),
                    success: function(response) {
                        // Clean up any lingering modal backdrops
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });

                        if (response.status === 'success') {
                            showAlert(response.message, true);
                        } else {
                            showAlert(response.message || 'Gagal mereset ujian', false);
                        }
                    },
                    error: function(xhr) {
                        // Clean up any lingering modal backdrops
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });

                        let msg = 'Terjadi kesalahan server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            msg = 'Error: ' + xhr.responseText.substring(0, 100);
                        }
                        showAlert(msg, false);
                    }
                });
            }
        });
    });
});
</script>
