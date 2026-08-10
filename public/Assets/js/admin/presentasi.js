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
                    // Daftar ini sudah disaring di server: hanya peserta yang
                    // HADIR di tes tertulis yang dikembalikan. Jadi tidak ada
                    // lagi penanda "belum tes" - yang muncul pasti memenuhi
                    // syarat urutan tahap.
                    opts += `<option value="${m.id_presentasi}" data-nama="${m.nama_lengkap}">${m.nama_lengkap} - ${m.stambuk}</option>`;
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

    // Peserta yang akan dijadwalkan. Urutan array = urutan slot waktu.
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
    window.__presentasiTerpilih = window.__presentasiTerpilih || [];

    dom.on('click', '#btnAddJadwal', function(e) {
        e.preventDefault();
        loadAvailableMahasiswa(); loadRuangan();
        const form = dom.qs('#formAddJadwal');
        if (form) form.reset();
        window.__presentasiTerpilih = [];
        gambarDaftarPeserta();
        UI.modal.open('#addJadwalModal');
    });

    /**
     * Menyembunyikan peserta yang sudah masuk daftar dari dropdown.
     *
     * Dipakai <option hidden> + disabled, bukan menghapus elemennya, supaya
     * opsinya bisa muncul kembali saat peserta dikeluarkan dari daftar.
     */
    function segarkanOpsiDropdown() {
        const sel = dom.qs('#selectMahasiswa');
        if (!sel) return;
        const terpakai = window.__presentasiTerpilih.map(function(p) { return String(p.id); });
        Array.prototype.forEach.call(sel.options, function(opt) {
            if (!opt.value) return; // placeholder dibiarkan
            const dipakai = terpakai.indexOf(String(opt.value)) !== -1;
            opt.hidden = dipakai;
            opt.disabled = dipakai;
        });
    }

    /** Menambah menit ke "HH:MM" dan mengembalikan "HH:MM". */
    function geserJam(mulai, menit) {
        const bagian = String(mulai || '').split(':');
        if (bagian.length < 2) return '';
        const total = (parseInt(bagian[0], 10) * 60) + parseInt(bagian[1], 10) + menit;
        // Dibungkus 24 jam supaya jadwal yang melewati tengah malam tidak
        // menghasilkan jam seperti "25:30".
        const bungkus = ((total % 1440) + 1440) % 1440;
        const jj = String(Math.floor(bungkus / 60)).padStart(2, '0');
        const mm = String(bungkus % 60).padStart(2, '0');
        return jj + ':' + mm;
    }

    function gambarDaftarPeserta() {
        const daftar = dom.qs('#daftarPesertaTerpilih');
        const jumlah = dom.qs('#jumlahPesertaTerpilih');
        if (!daftar) return;

        if (jumlah) {
            jumlah.textContent = window.__presentasiTerpilih.length + ' peserta';
        }

        if (window.__presentasiTerpilih.length === 0) {
            daftar.innerHTML = '<li id="daftarPesertaKosong" class="text-center text-xs text-slate-400 py-4 border border-dashed border-slate-200 rounded-xl">Belum ada peserta dipilih</li>';
            gambarPratinjau();
            return;
        }

        let html = '';
        window.__presentasiTerpilih.forEach(function(p, i) {
            html +=
                '<li class="flex items-center gap-2 py-2 px-3 bg-slate-50 rounded-xl border border-slate-100" data-id="' + p.id + '">' +
                    '<span class="shrink-0 w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">' + (i + 1) + '</span>' +
                    '<span class="flex-1 min-w-0 truncate text-sm text-slate-700">' + p.nama + '</span>' +
                    '<button type="button" class="shrink-0 text-red-500 hover:text-red-700 hapus-peserta" data-id="' + p.id + '" aria-label="Hapus dari daftar">' +
                        '<i class="bi bi-x-circle text-base pointer-events-none"></i>' +
                    '</button>' +
                '</li>';
        });
        daftar.innerHTML = html;
        gambarPratinjau();
    }

    /** Pratinjau jam tiap peserta - dihitung dengan rumus yang sama dengan server. */
    function gambarPratinjau() {
        const kotak = dom.qs('#pratinjauSlot');
        const isi = dom.qs('#isiPratinjauSlot');
        if (!kotak || !isi) return;

        const mulai = dom.val(dom.qs('#inputWaktu'));
        const durasi = parseInt(dom.val(dom.qs('#inputDurasi')), 10);

        if (window.__presentasiTerpilih.length === 0 || !mulai || !durasi || durasi < 1) {
            kotak.classList.add('hidden');
            isi.innerHTML = '';
            return;
        }

        let html = '';
        window.__presentasiTerpilih.forEach(function(p, i) {
            html += '<li class="flex justify-between gap-2">' +
                        '<span class="truncate">' + (i + 1) + '. ' + p.nama + '</span>' +
                        '<span class="shrink-0 font-bold text-blue-700">' + geserJam(mulai, i * durasi) + '</span>' +
                    '</li>';
        });
        isi.innerHTML = html;
        kotak.classList.remove('hidden');
    }

    dom.on('click', '#btnTambahKeDaftar', function() {
        const sel = dom.qs('#selectMahasiswa');
        if (!sel || !sel.value) return showAlert('Pilih mahasiswa terlebih dahulu', false);

        const opsi = sel.options[sel.selectedIndex];
        if (window.__presentasiTerpilih.some(function(p) { return p.id === sel.value; })) {
            return showAlert('Mahasiswa sudah ada dalam daftar', false);
        }

        window.__presentasiTerpilih.push({
            id: sel.value,
            nama: opsi.getAttribute('data-nama') || opsi.text,
        });
        sel.value = '';
        gambarDaftarPeserta();
        segarkanOpsiDropdown();
    });

    dom.on('click', '.hapus-peserta', function() {
        const id = this.getAttribute('data-id');
        window.__presentasiTerpilih = window.__presentasiTerpilih.filter(function(p) { return p.id !== id; });
        gambarDaftarPeserta();
        segarkanOpsiDropdown();
    });

    // Pratinjau ikut menyesuaikan begitu jam mulai atau durasi diubah.
    dom.on('change', '#inputWaktu', gambarPratinjau);
    dom.on('keyup', '#inputDurasi', gambarPratinjau);
    dom.on('change', '#inputDurasi', gambarPratinjau);

    function simpanJadwalPresentasi() {
        dom.postBodyJSON(APP_URL + '/savebatchjadwalpresentasi', {
            id: window.__presentasiTerpilih.map(function(p) { return p.id; }),
            id_ruangan: dom.val(dom.qs('#selectRuangan')),
            tanggal: dom.val(dom.qs('#inputTanggal')),
            waktu_mulai: dom.val(dom.qs('#inputWaktu')),
            durasi: dom.val(dom.qs('#inputDurasi'))
        }).then(function(res) {
            if (res.status === 'success') {
                UI.modal.close('#addJadwalModal');
                window.__presentasiTerpilih = [];
                showAlert(res.message || 'Jadwal berhasil dibuat');
                loadJadwal();
            } else {
                // Modal sengaja dibiarkan terbuka saat gagal supaya daftar
                // peserta yang sudah disusun tidak hilang begitu saja.
                showAlert(res.message, false);
            }
        });
    }

    dom.on('submit', '#formAddJadwal', function(e) {
        e.preventDefault();

        if (window.__presentasiTerpilih.length === 0) {
            return showAlert('Tambahkan minimal satu peserta ke daftar', false);
        }

        // Urutan tahap tidak lagi bisa dilewati: daftar peserta sudah
        // disaring di server dan endpoint menolak peserta yang belum hadir
        // tes tertulis. Karena itu tidak ada lagi konfirmasi "tetap jadwalkan".
        simpanJadwalPresentasi();
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
