/**
 * admin/jadwaltes.js
 *
 * Dipindahkan dari app/View/admin/jadwaltes/index.php agar berkas view tetap ringkas
 * (markup saja) dan skrip halaman bisa di-cache browser.
 *
 * Bergantung pada: APP_URL (global dari layout), dom.js, ui.js.
 * Handler memakai dom.on() = delegasi di document, sehingga aman
 * dieksekusi ulang ketika SPA menyuntik ulang konten.
 */
// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content.
(function() {
    let selectedMahasiswa = [];

    // Search logic for main table
    dom.on('keyup', '#searchInput', function() {
        const filter = this.value.toLowerCase();
        dom.qsa('#table-body tr').forEach(function(row) {
            dom.toggle(row, row.textContent.toLowerCase().indexOf(filter) > -1);
        });
    });

    // Add student to the single-add modal list
    dom.on('click', '#addMhsToList', function() {
        const sel = dom.qs('#mahasiswaSelect');
        if (!sel) return;
        const id = sel.value;
        const text = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '';
        if (!id) return;
        if (selectedMahasiswa.includes(id)) return showAlert('Mahasiswa sudah ada dalam daftar', false);

        selectedMahasiswa.push(id);
        const list = dom.qs('#selectedMhsList');
        if (list) {
            list.insertAdjacentHTML('beforeend', `
            <li class="flex justify-between items-center py-2 px-3 bg-slate-50 rounded-xl text-slate-700 text-sm border border-slate-100" data-id="${id}">
                <span>${text}</span>
                <button type="button" class="text-red-500 hover:text-red-700 remove-mhs" aria-label="Hapus dari daftar"><i class="bi bi-x-circle text-lg pointer-events-none"></i></button>
            </li>
        `);
        }
    });

    dom.on('click', '.remove-mhs', function() {
        const li = this.closest('li');
        if (!li) return;
        const id = String(li.dataset.id);
        selectedMahasiswa = selectedMahasiswa.filter(item => item !== id);
        li.remove();
    });

    // Save Single Add Schedule
    dom.on('submit', '#addJadwalForm', function(e) {
        e.preventDefault();
        if (selectedMahasiswa.length === 0) return showAlert('Pilih minimal satu mahasiswa', false);

        const data = {
            id: selectedMahasiswa,
            ruangan: dom.val(dom.qs('#ruanganSelect')),
            kegiatan: dom.val(dom.qs('#kegiatanInput')),
            tanggal: dom.val(dom.qs('#tanggalInput')),
            waktu: dom.val(dom.qs('#waktuInput'))
        };

        saveSchedule(data, '#addJadwalModal');
    });

    // Open Edit Modal
    dom.on('click', '.open-edit', function() {
        const data = this.dataset;
        dom.val(dom.qs('#editId'), data.id);
        dom.text(dom.qs('#editMhsInfo'), data.stambuk + ' - ' + data.nama);
        dom.val(dom.qs('#editKegiatan'), data.kegiatan);
        dom.val(dom.qs('#editTanggal'), data.tanggal);

        // Fix time format to HH:mm
        let timeStr = data.waktu;
        if (timeStr && timeStr.length > 5) {
            timeStr = timeStr.substring(0, 5);
        }
        dom.val(dom.qs('#editWaktu'), timeStr);

        // Find room ID based on name or set manually
        const roomName = data.ruangan;
        dom.qsa('#editRuangan option').forEach(function(opt) {
            if (opt.textContent === roomName) opt.selected = true;
        });

        UI.modal.open('#updateJadwalModal');
    });

    // Save Update Schedule
    dom.on('submit', '#updateJadwalForm', function(e) {
        e.preventDefault();
        const data = {
            id: dom.val(dom.qs('#editId')),
            ruangan: dom.val(dom.qs('#editRuangan')),
            kegiatan: dom.val(dom.qs('#editKegiatan')),
            tanggal: dom.val(dom.qs('#editTanggal')),
            waktu: dom.val(dom.qs('#editWaktu'))
        };

        dom.postBodyJSON(APP_URL + '/updateJadwalTes', data).then(function(res) {
            if (res.status === 'success') {
                UI.modal.close('#updateJadwalModal');
                showAlert(res.message, true);
                const link = document.querySelector('a[data-page="jadwaltes"]');
                if (link) link.click();
            } else {
                showAlert(res.message, false);
            }
        });
    });

    function saveSchedule(data, modalId) {
        dom.postBodyJSON(APP_URL + '/saveJadwalTes', data)
            .then(function(res) {
                if (res.status === 'success') {
                    UI.modal.close(modalId);
                    showAlert(res.message, true);
                    // Reload the page content
                    const link = document.querySelector('a[data-page="jadwaltes"]');
                    if (link) link.click();
                } else {
                    showAlert(res.message, false);
                }
            })
            .catch(function() { showAlert('Terjadi kesalahan jaringan', false); });
    }

    // Delete Schedule
    dom.on('click', '.delete-schedule', function() {
        const id = this.dataset.id;
        showConfirmDelete(function() {
            dom.postBodyJSON(APP_URL + '/deleteJadwalTes', { id: id })
                .then(function(res) {
                    if (res.status === 'success') {
                        showAlert(res.message || 'Jadwal berhasil dihapus!', true);
                        setTimeout(function() {
                            const link = document.querySelector('a[data-page="jadwaltes"]');
                            if (link) link.click();
                        }, 1000);
                    } else {
                        showAlert(res.message, false);
                    }
                })
                .catch(function() { showAlert('Terjadi kesalahan jaringan', false); });
        }, 'Apakah Anda yakin ingin menghapus jadwal tes ini?');
    });

    // Reset Exam Handler
    dom.on('click', '.reset-exam', function() {
        const idMahasiswa = this.dataset.id;
        const text = this.dataset.nama;

        showActionConfirmation({
            title: 'Reset Pengerjaan Tes?',
            message: `Apakah Anda yakin ingin mereset pengerjaan tes untuk <strong>${text}</strong>? <br><span class="text-red-600 font-semibold text-xs">Seluruh jawaban dan nilai akan dihapus permanen.</span>`,
            btnText: 'Reset Sekarang',
            type: 'danger',
            onConfirm: function() {
                // Catatan: pembersihan .modal-backdrop / body.modal-open tidak lagi
                // diperlukan — UI.modal memakai backdrop per-modal, bukan global.
                dom.postBodyJSON(APP_URL + '/admin/reset-ujian', { id: idMahasiswa })
                    .then(function(response) {
                        if (response.status === 'success') {
                            showAlert(response.message, true);
                        } else {
                            showAlert(response.message || 'Gagal mereset ujian', false);
                        }
                    })
                    .catch(function() { showAlert('Terjadi kesalahan server', false); });
            }
        });
    });
})();
