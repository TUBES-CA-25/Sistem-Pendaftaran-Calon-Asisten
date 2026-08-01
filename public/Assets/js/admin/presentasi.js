/**
 * admin/presentasi.js
 *
 * Dipindahkan dari app/View/admin/presentasi/index.php agar berkas view tetap ringkas
 * (markup saja) dan skrip halaman bisa di-cache browser.
 *
 * Bergantung pada: APP_URL (global dari layout), dom.js, ui.js.
 * Handler memakai dom.on() = delegasi di document, sehingga aman
 * dieksekusi ulang ketika SPA menyuntik ulang konten.
 */
// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content (dulu dipaksakan dengan .off().on()).
(function() {
    // APP_URL sudah tersedia sebagai konstanta global dari layout.

    dom.on('keyup', '#searchJadwal', function() {
        const term = this.value.toLowerCase();
        dom.qsa('#jadwalTableBody tr').forEach(function(row) {
            dom.toggle(row, row.textContent.toLowerCase().indexOf(term) > -1);
        });
    });

    function loadRuangan() {
        dom.postJSON(APP_URL + '/getallruangan', {}).then(function(res) {
            if (res.status === 'success') {
                let opts = '<option value="">-- Pilih Ruangan --</option>';
                res.data.forEach(r => opts += `<option value="${r.id}">${r.nama}</option>`);
                dom.qsa('#selectRuangan, #editRuangan').forEach(function(sel) {
                    sel.innerHTML = opts;
                });
            }
        });
    }

    function loadAvailableMahasiswa() {
        dom.postJSON(APP_URL + '/getavailablemahasiswa', {}).then(function(res) {
            if (res.status === 'success') {
                let opts = '<option value="">-- Pilih Mahasiswa --</option>';
                res.data.forEach((m) => {
                    opts += `<option value="${m.id_presentasi}">${m.nama_lengkap} - ${m.stambuk}</option>`;
                });
                dom.html(dom.qs('#selectMahasiswa'), opts);
            }
        });
    }

    function loadJadwal() {
        dom.postJSON(APP_URL + '/getjadwalpresentasi', {}).then(function(res) {
            if(res.status==='success') {
                let html = '';
                if(res.data.length===0) html='<tr><td colspan="8" class="text-center text-slate-400 py-10 font-medium">Belum ada jadwal</td></tr>';
                else {
                    res.data.forEach((j, i) => {
                        html += `<tr class="dt-body-row" data-id="${j.id}">
                            <td class="text-center py-4 px-4">${i+1}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="${j.photoPath || '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'}" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                    <div>
                                        <div class="font-bold text-slate-800">${j.nama_lengkap}</div>
                                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">${j.stambuk}</td>
                            <td class="py-4 px-4">${j.judul||'-'}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs">
                                    ${j.ruangan}
                                </span>
                            </td>
                            <td class="py-4 px-4">${new Date(j.tanggal).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})}</td>
                            <td class="py-4 px-4">${j.waktu}</td>
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
                dom.html(dom.qs('#jadwalTableBody'), html);
            }
        });
    }

    dom.on('click', '#btnAddJadwal', function(e) {
        e.preventDefault();
        loadAvailableMahasiswa(); loadRuangan();
        const form = dom.qs('#formAddJadwal');
        if (form) form.reset();
        UI.modal.open('#addJadwalModal');
    });

    dom.on('submit', '#formAddJadwal', function(e) {
        e.preventDefault();
        dom.postJSON(APP_URL + '/savejadwalpresentasi', {
            id_presentasi: dom.val(dom.qs('#selectMahasiswa')),
            id_ruangan: dom.val(dom.qs('#selectRuangan')),
            tanggal: dom.val(dom.qs('#inputTanggal')),
            waktu: dom.val(dom.qs('#inputWaktu'))
        }).then(function(res) {
            UI.modal.close('#addJadwalModal');
            if(res.status==='success') { showAlert('Disimpan!'); loadJadwal(); }
            else showAlert(res.message, false);
        });
    });

    dom.on('click', '.btn-edit-jadwal', function() {
        const data = this.dataset;
        loadRuangan();
        dom.val(dom.qs('#editId'), data.id);
        dom.val(dom.qs('#editNama'), data.nama);
        dom.val(dom.qs('#editTanggal'), data.tanggal);

        let timeStr = data.waktu;
        if (timeStr && timeStr.length > 5) {
            timeStr = timeStr.substring(0, 5);
        }
        dom.val(dom.qs('#editWaktu'), timeStr);

        // Tunggu loadRuangan() selesai mengisi <option> sebelum set nilainya
        setTimeout(function() {
            dom.val(dom.qs('#editRuangan'), data.ruangan);
        }, 300);

        UI.modal.open('#updateJadwalModal');
    });

    dom.on('submit', '#formUpdateJadwal', function(e) {
        e.preventDefault();
        dom.postJSON(APP_URL + '/updatejadwalpresentasi', {
            id: dom.val(dom.qs('#editId')),
            id_ruangan: dom.val(dom.qs('#editRuangan')),
            tanggal: dom.val(dom.qs('#editTanggal')),
            waktu: dom.val(dom.qs('#editWaktu'))
        }).then(function(res) {
            UI.modal.close('#updateJadwalModal');
            if(res.status==='success') { showAlert('Berhasil diupdate!'); loadJadwal(); }
            else showAlert(res.message, false);
        });
    });

    dom.on('click', '.btn-delete-jadwal', function() {
        const id = this.dataset.id;
        showConfirmDelete(function() {
            dom.postJSON(APP_URL + '/deletejadwalpresentasi', { id: id }).then(function(res) {
                if(res.status === 'success') {
                    showAlert('Jadwal berhasil dihapus!', true);
                    setTimeout(function() {
                        loadJadwal();
                    }, 1000);
                } else {
                    showAlert(res.message, false);
                }
            });
        }, 'Apakah Anda yakin ingin menghapus jadwal presentasi ini?');
    });
})();
