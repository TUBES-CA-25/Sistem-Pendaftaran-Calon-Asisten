/**
 * admin/penjadwalan/tes.js
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
    // State disimpan di window, bukan variabel lokal IIFE.
    //
    // Halaman Penjadwalan mengeksekusi ulang skrip tab setiap kali tab
    // dibuka. Tiap eksekusi membuat closure baru, tetapi dom.on() menolak
    // memasang handler yang sama dua kali - jadi handler yang HIDUP tetap
    // milik eksekusi pertama sementara fungsi penggambar yang dipanggil
    // adalah milik eksekusi terbaru. Keduanya lalu membaca array yang
    // berbeda: peserta masuk ke daftar lama, penghitung tetap 0.
    //
    // window membuat seluruh eksekusi berbagi satu state yang sama.
    window.__jadwaltesTerpilih = window.__jadwaltesTerpilih || [];

    // Search logic for main table
    dom.on('keyup', '#searchInput', function() {
        const filter = this.value.toLowerCase();
        dom.qsa('#table-body tr').forEach(function(row) {
            dom.toggle(row, row.textContent.toLowerCase().indexOf(filter) > -1);
        });
    });


    /**
     * Menyegarkan tabel setelah data berubah.
     *
     * Dulu memakai `document.querySelector('a[data-page="jadwaltes"]').click()`
     * untuk memicu navigasi SPA. Itu berhenti bekerja setelah menu Penjadwalan
     * digabung: tautan sidebar dengan data-page tersebut sudah tidak ada, jadi
     * querySelector mengembalikan null dan tabel TIDAK pernah menyegar -
     * perubahan baru terlihat setelah halaman dimuat ulang manual.
     *
     * muatUlangTabJadwal() disediakan penjadwalan.js untuk memuat ulang isi tab
     * yang sedang aktif. Fallback ke tautan sidebar dipertahankan agar halaman
     * tetap berfungsi bila dibuka lewat rute lamanya secara langsung.
     */
    function segarkanTabel() {
        if (typeof window.muatUlangTabJadwal === 'function') {
            window.muatUlangTabJadwal();
            return;
        }
        const link = document.querySelector('a[data-page="jadwaltes"]');
        if (link) link.click();
    }

    // Nama peserta disimpan berdampingan dengan id supaya daftar bisa digambar
    // ulang - nomor urut harus menyesuaikan setelah ada yang dihapus.
    window.__jadwaltesNama = window.__jadwaltesNama || {};

    /**
     * Menyembunyikan peserta yang sudah masuk daftar dari dropdown.
     *
     * Dipakai <option hidden> + disabled, bukan menghapus elemennya, supaya
     * opsinya bisa muncul kembali saat peserta dikeluarkan dari daftar.
     */
    function segarkanOpsiDropdown() {
        const sel = dom.qs('#mahasiswaSelect');
        if (!sel) return;
        Array.prototype.forEach.call(sel.options, function(opt) {
            if (!opt.value) return; // placeholder dibiarkan
            const dipakai = window.__jadwaltesTerpilih.indexOf(String(opt.value)) !== -1;
            opt.hidden = dipakai;
            opt.disabled = dipakai;
        });
    }

    // Gaya daftar mengikuti tab Presentasi & Wawancara: nomor urut, nama,
    // tombol hapus, plus penghitung dan empty state.
    function gambarDaftarMhs() {
        const list = dom.qs('#selectedMhsList');
        if (!list) return;

        const badge = dom.qs('#jumlahMhsTerpilih');
        if (badge) badge.textContent = window.__jadwaltesTerpilih.length + ' peserta';

        if (window.__jadwaltesTerpilih.length === 0) {
            list.innerHTML = '<li id="daftarMhsKosong" class="text-center text-xs text-slate-400 py-4 border border-dashed border-slate-200 rounded-xl">Belum ada peserta dipilih</li>';
            return;
        }

        let html = '';
        window.__jadwaltesTerpilih.forEach(function(id, i) {
            html +=
                '<li class="flex items-center gap-2 py-2 px-3 bg-slate-50 rounded-xl border border-slate-100" data-id="' + id + '">' +
                    '<span class="shrink-0 w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">' + (i + 1) + '</span>' +
                    '<span class="flex-1 min-w-0 truncate text-sm text-slate-700">' + (window.__jadwaltesNama[id] || '') + '</span>' +
                    '<button type="button" class="shrink-0 text-red-500 hover:text-red-700 transition-colors remove-mhs" aria-label="Hapus dari daftar">' +
                        '<i class="bi bi-x-circle text-base pointer-events-none"></i>' +
                    '</button>' +
                '</li>';
        });
        list.innerHTML = html;
    }

    // Kosongkan daftar tiap kali modal dibuka supaya peserta dari sesi
    // sebelumnya tidak tertinggal.
    dom.on('click', '[data-modal-open="#addJadwalModal"]', function() {
        window.__jadwaltesTerpilih = [];
        window.__jadwaltesNama = {};
        gambarDaftarMhs();
        segarkanOpsiDropdown();
    });

    // Add student to the single-add modal list
    dom.on('click', '#addMhsToList', function() {
        const sel = dom.qs('#mahasiswaSelect');
        if (!sel) return;
        const id = sel.value;
        const text = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '';
        if (!id) return showAlert('Pilih mahasiswa terlebih dahulu', false);
        if (window.__jadwaltesTerpilih.includes(id)) return showAlert('Mahasiswa sudah ada dalam daftar', false);

        window.__jadwaltesTerpilih.push(id);
        window.__jadwaltesNama[id] = text;
        gambarDaftarMhs();
        segarkanOpsiDropdown();
        sel.selectedIndex = 0;
    });

    dom.on('click', '.remove-mhs', function() {
        const li = this.closest('li');
        if (!li) return;
        const id = String(li.dataset.id);
        window.__jadwaltesTerpilih = window.__jadwaltesTerpilih.filter(item => item !== id);
        delete window.__jadwaltesNama[id];
        gambarDaftarMhs();
        segarkanOpsiDropdown();
    });

    // Save Single Add Schedule
    dom.on('submit', '#addJadwalForm', function(e) {
        e.preventDefault();
        if (window.__jadwaltesTerpilih.length === 0) return showAlert('Pilih minimal satu mahasiswa', false);

        const data = {
            id: window.__jadwaltesTerpilih,
            ruangan: dom.val(dom.qs('#ruanganSelect')),
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
            tanggal: dom.val(dom.qs('#editTanggal')),
            waktu: dom.val(dom.qs('#editWaktu'))
        };

        dom.postBodyJSON(APP_URL + '/updateJadwalTes', data).then(function(res) {
            if (res.status === 'success') {
                UI.modal.close('#updateJadwalModal');
                showAlert(res.message, true);
                segarkanTabel();
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
                    segarkanTabel();
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
                            segarkanTabel();
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
