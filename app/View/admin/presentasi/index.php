<?php
/**
 * Jadwal Presentasi Admin View
 *
 * @var array $jadwalPresentasi
 * @var array $ruanganList - Passed via JS/AJAX mainly, but useful if preloaded
 */
$jadwalPresentasi = $jadwalPresentasi ?? [];
?>

<main>
    <?php
        $title = 'Jadwal Presentasi';
        $subtitle = 'Kelola jadwal dan ruangan presentasi mahasiswa';
        $icon = 'bi bi-calendar-event';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="relative w-full sm:w-72">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="searchJadwal" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Cari mahasiswa...">
            </div>
            <div>
                <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2 shadow-md shadow-blue-500/10" id="btnAddJadwal">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm" id="tableJadwal">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-center" style="width: 5%;">No</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 20%;">Nama Lengkap</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 15%;">Stambuk</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 25%;">Judul</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 15%;">Ruangan</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 10%;">Tanggal</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4" style="width: 10%;">Waktu</th>
                            <th class="font-bold text-xs uppercase tracking-wider py-4 px-4 text-center" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jadwalTableBody" class="divide-y divide-slate-100">
                        <?php if (empty($jadwalPresentasi)): ?>
                            <tr><td colspan="8" class="text-center text-slate-400 py-10 font-medium">Belum ada jadwal presentasi</td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($jadwalPresentasi as $row): ?>
                                <tr class="hover:bg-slate-50/85 transition">
                                    <td class="text-center font-semibold text-slate-400 py-4 px-4"><?= $i ?></td>
                                    <td class="py-4 px-4"><span class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '-') ?></span></td>
                                    <td class="text-slate-500 py-4 px-4 font-semibold"><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                    <td class="text-slate-600 py-4 px-4 font-medium leading-relaxed"><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold">
                                            <?= htmlspecialchars($row['ruangan'] ?? $row['nama_ruangan'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-slate-500 py-4 px-4 font-medium"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-slate-650 py-4 px-4 font-bold"><?= htmlspecialchars($row['waktu']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 btn-edit-jadwal"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?>"
                                                    data-ruangan="<?= $row['id_ruangan'] ?>"
                                                    data-tanggal="<?= $row['tanggal'] ?>"
                                                    data-waktu="<?= $row['waktu'] ?>"
                                                    title="Edit"><i class="bi bi-pencil"></i></button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-650 btn-delete-jadwal"
                                                    data-id="<?= $row['id'] ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php $i++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2"><i class="bi bi-calendar-plus text-lg"></i>Tambah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <form id="formAddJadwal" method="POST" action="javascript:void(0);" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="selectMahasiswa" required><option value="">-- Loading --</option></select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="selectRuangan" required><option value="">-- Loading --</option></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                            <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputTanggal" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu:</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="inputWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formAddJadwal" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Jadwal -->
<div class="modal fade" id="updateJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h5 class="modal-title font-bold flex items-center gap-2"><i class="bi bi-pencil-square text-lg"></i>Update Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <form id="formUpdateJadwal" method="POST" action="javascript:void(0);" class="space-y-4">
                    <input type="hidden" id="editId">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Mahasiswa:</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-semibold" id="editNama" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Ruangan:</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editRuangan" required></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal:</label>
                            <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editTanggal" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu:</label>
                            <input type="time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition" id="editWaktu" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formUpdateJadwal" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-md shadow-blue-500/10 flex items-center gap-2"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const APP_URL = '<?= APP_URL ?>';
    
    $('#searchJadwal').on('keyup', function() {
        const term = $(this).val().toLowerCase();
        $('#jadwalTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1)
        });
    });

    function loadRuangan() {
        $.post(APP_URL + '/getallruangan', function(res) {
            if (res.status === 'success') {
                let opts = '<option value="">-- Pilih Ruangan --</option>';
                res.data.forEach(r => opts += `<option value="${r.id}">${r.nama}</option>`);
                $('#selectRuangan, #editRuangan').html(opts);
            }
        }, 'json');
    }

    function loadAvailableMahasiswa() {
        $.post(APP_URL + '/getavailablemahasiswa', function(res) {
            if (res.status === 'success') {
                let opts = '<option value="">-- Pilih Mahasiswa --</option>';
                res.data.forEach((m) => {
                    opts += `<option value="${m.id_presentasi}">${m.nama_lengkap} - ${m.stambuk}</option>`;
                });
                $('#selectMahasiswa').html(opts);
            }
        }, 'json');
    }

    function loadJadwal() {
        $.post(APP_URL + '/getjadwalpresentasi', function(res) {
            if(res.status==='success') {
                let html = '';
                if(res.data.length===0) html='<tr><td colspan="8" class="text-center text-slate-400 py-10 font-medium">Belum ada jadwal</td></tr>';
                else {
                    res.data.forEach((j, i) => {
                        html += `<tr class="hover:bg-slate-50/85 transition">
                            <td class="text-center font-semibold text-slate-400 py-4 px-4">${i+1}</td>
                            <td class="py-4 px-4"><span class="font-bold text-slate-800">${j.nama_lengkap}</span></td>
                            <td class="text-slate-500 py-4 px-4 font-semibold">${j.stambuk}</td>
                            <td class="text-slate-650 py-4 px-4 font-medium leading-relaxed">${j.judul||'-'}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold">
                                    ${j.ruangan}
                                </span>
                            </td>
                            <td class="text-slate-500 py-4 px-4 font-medium">${new Date(j.tanggal).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})}</td>
                            <td class="text-slate-650 py-4 px-4 font-bold">${j.waktu}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-amber-50 hover:bg-amber-100 text-amber-600 btn-edit-jadwal"
                                            data-id="${j.id}"
                                            data-nama="${j.nama_lengkap}"
                                            data-ruangan="${j.id_ruangan}"
                                            data-tanggal="${j.tanggal}"
                                            data-waktu="${j.waktu}"
                                            title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 btn-delete-jadwal"
                                            data-id="${j.id}" title="Hapus"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>`;
                    });
                }
                $('#jadwalTableBody').html(html);
            }
        }, 'json');
    }

    $('#btnAddJadwal').click(function(e) {
        e.preventDefault();
        loadAvailableMahasiswa(); loadRuangan();
        $('#formAddJadwal')[0].reset();
        new bootstrap.Modal('#addJadwalModal').show();
    });

    $('#formAddJadwal').submit(function(e) {
        e.preventDefault();
        $.post(APP_URL + '/savejadwalpresentasi', {
            id_presentasi: $('#selectMahasiswa').val(),
            id_ruangan: $('#selectRuangan').val(),
            tanggal: $('#inputTanggal').val(),
            waktu: $('#inputWaktu').val()
        }, function(res) {
            bootstrap.Modal.getInstance(document.getElementById('addJadwalModal')).hide();
            if(res.status==='success') { showAlert('Disimpan!'); loadJadwal(); }
            else showAlert(res.message, false);
        }, 'json');
    });

    $(document).on('click', '.btn-edit-jadwal', function() {
        const btn = $(this);
        loadRuangan();
        $('#editId').val(btn.data('id'));
        $('#editNama').val(btn.data('nama'));
        $('#editTanggal').val(btn.data('tanggal'));
        $('#editWaktu').val(btn.data('waktu'));
        
        setTimeout(() => {
            $('#editRuangan').val(btn.data('ruangan'));
        }, 300);

        new bootstrap.Modal('#updateJadwalModal').show();
    });

    $('#formUpdateJadwal').submit(function(e) {
        e.preventDefault();
        $.post(APP_URL + '/updatejadwalpresentasi', {
            id: $('#editId').val(),
            id_ruangan: $('#editRuangan').val(),
            tanggal: $('#editTanggal').val(),
            waktu: $('#editWaktu').val()
        }, function(res) {
            bootstrap.Modal.getInstance(document.getElementById('updateJadwalModal')).hide();
            if(res.status==='success') { showAlert('Berhasil diupdate!'); loadJadwal(); }
            else showAlert(res.message, false);
        }, 'json');
    });

    $(document).on('click', '.btn-delete-jadwal', function() {
        const id = $(this).data('id');
        showConfirmDelete(function() {
            $.post(APP_URL + '/deletejadwalpresentasi', { id: id }, function(res) {
                if(res.status === 'success') { 
                    showAlert('Jadwal berhasil dihapus!', true); 
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(res.message, false);
                }
            }, 'json');
        }, 'Apakah Anda yakin ingin menghapus jadwal presentasi ini?');
    });
});
</script>
