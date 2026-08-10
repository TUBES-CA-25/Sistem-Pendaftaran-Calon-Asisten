/**
 * admin/kehadiran.js
 *
 * Dipindahkan dari app/View/admin/kehadiran/index.php agar berkas view tetap ringkas
 * (markup saja) dan skrip halaman bisa di-cache browser.
 *
 * Bergantung pada: APP_URL (global dari layout), dom.js, ui.js.
 * Handler memakai dom.on() = delegasi di document, sehingga aman
 * dieksekusi ulang ketika SPA menyuntik ulang konten.
 */
// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content.
(function() {
    // APP_URL sudah tersedia sebagai konstanta global dari layout.
    let selectedMahasiswa = [];

    /** Container dicari saat dipakai (bukan disimpan), agar tetap valid setelah re-inject. */
    function selectedContainer() { return dom.qs('#selectedMahasiswaList'); }

    /* ------------------------------------------------------------------ *
     * Penguncian tahap berurutan
     *
     * Kehadiran hanya boleh diisi mengikuti alur seleksi:
     * tes tertulis -> presentasi -> wawancara I -> wawancara II.
     * Tahap berikutnya terkunci sampai tahap sebelumnya diisi 'Hadir'.
     *
     * Alasannya: peserta yang tidak hadir (atau belum diabsen) di satu tahap
     * tidak mungkin mengikuti tahap sesudahnya, jadi mengisinya pasti keliru.
     * Mengunci lebih baik daripada membiarkan lalu memperbaiki data nanti.
     *
     * 'Izin' TIDAK membuka tahap berikutnya - peserta memang tidak hadir.
     * Kalau ternyata harus dilanjutkan, admin mengubah tahap itu ke 'Hadir'
     * lebih dulu, sehingga keputusannya tercatat.
     * ------------------------------------------------------------------ */

    // Urutan id dropdown per modal. Modal tambah dan edit memakai id berbeda
    // untuk field yang sama, jadi keduanya didaftarkan terpisah.
    const TAHAP_TAMBAH = ['#absensiTesTertulis', '#absensiPresentasi', '#absensiWawancara1', '#absensiWawancara2'];
    const TAHAP_EDIT   = ['#tesTertulis', '#presentasi', '#wawancaraI', '#wawancaraII'];

    const LABEL_TAHAP = ['Tes Tertulis', 'Presentasi', 'Wawancara I', 'Wawancara II'];

    /**
     * Menonaktifkan pilihan "Hadir" pada tahap yang peserta belum dijadwalkan.
     *
     * Peserta tidak mungkin hadir di kegiatan yang tidak pernah dijadwalkan
     * untuknya, jadi 'Hadir' tanpa jadwal hampir pasti salah entri.
     *
     * "Izin" dan "Alpha" sengaja TIDAK dibatasi - keduanya justru menandai
     * peserta yang tidak mengikuti kegiatan, dan admin kadang perlu
     * mencatatnya sebelum jadwal dibuat (mis. peserta mengundurkan diri).
     *
     * Aturan yang sama divalidasi ulang di server; ini hanya lapisan
     * antarmuka supaya admin tahu sebelum menekan Simpan.
     *
     * @param {string[]} daftarId    selektor dropdown, urut per tahap
     * @param {Array}    punyaJadwal jumlah jadwal per tahap (urutan sama)
     */
    function terapkanOpsiHadir(daftarId, punyaJadwal) {
        daftarId.forEach(function(sel, i) {
            const el = dom.qs(sel);
            if (!el) return;

            const ada = parseInt(punyaJadwal[i], 10) > 0;
            const opsi = el.querySelector('option[value="Hadir"]');
            if (!opsi) return;

            opsi.disabled = !ada;
            opsi.textContent = ada ? 'Hadir' : 'Hadir (belum dijadwalkan)';

            // Nilai "Hadir" yang tersimpan sebelum aturan ini berlaku tetap
            // ditampilkan apa adanya - mengosongkannya diam-diam akan
            // menghapus data lama tanpa sepengetahuan admin.
        });
    }

    function terapkanKunciTahap(daftarId) {
        let bolehIsi = true; // tahap pertama selalu terbuka

        // Tahap paling depan yang masih boleh diisi - dipakai untuk menyusun
        // petunjuk "isi ini berikutnya".
        let tahapAktif = -1;
        let tahapPenghenti = -1;   // tahap yang membuat sisanya terkunci
        let nilaiPenghenti = '';

        daftarId.forEach(function(sel, i) {
            const el = dom.qs(sel);
            if (!el) return;

            el.disabled = !bolehIsi;
            el.classList.toggle('bg-slate-100', !bolehIsi);
            el.classList.toggle('cursor-not-allowed', !bolehIsi);
            el.classList.toggle('text-slate-400', !bolehIsi);
            el.classList.toggle('bg-white', bolehIsi);

            el.title = bolehIsi
                ? ''
                : 'Terkunci - isi ' + LABEL_TAHAP[i - 1] + ' dengan "Hadir" terlebih dahulu';

            // Tahap yang terkunci dikosongkan supaya tidak menyimpan nilai
            // sisa yang tidak pernah bisa dilihat/diubah admin.
            if (!bolehIsi) el.value = '';

            if (bolehIsi && tahapAktif === -1 && el.value !== 'Hadir') {
                tahapAktif = i;
                nilaiPenghenti = el.value;
            }

            // Tahap berikutnya terbuka hanya kalau tahap ini 'Hadir'.
            const sebelumnya = bolehIsi;
            bolehIsi = bolehIsi && el.value === 'Hadir';
            if (sebelumnya && !bolehIsi && tahapPenghenti === -1) {
                tahapPenghenti = i;
            }
        });

        gambarPetunjuk(tahapAktif, tahapPenghenti, nilaiPenghenti);
        terapkanOpsiLulus(daftarId);
    }

    /**
     * Menonaktifkan pilihan "Lulus" selama tahapan belum tuntas.
     *
     * Peserta hanya bisa dinyatakan lulus setelah hadir di keempat tahap.
     * "Tidak Lulus" dan "Pending" sengaja dibiarkan bebas: peserta yang gagal
     * atau mundur di tengah jalan tidak akan pernah menyelesaikan semuanya,
     * jadi statusnya harus tetap bisa ditetapkan.
     *
     * Hanya berlaku pada modal edit - modal tambah tidak punya field ini.
     * Aturan yang sama divalidasi ulang di server.
     */
    function terapkanOpsiLulus(daftarId) {
        if (daftarId !== TAHAP_EDIT) return;

        const status = dom.qs('#detailStatusAkhir');
        if (!status) return;

        const opsi = status.querySelector('option[value="Lulus"]');
        if (!opsi) return;

        const tuntas = daftarId.every(function(sel) {
            const el = dom.qs(sel);
            return el && el.value === 'Hadir';
        });

        opsi.disabled = !tuntas;
        opsi.textContent = tuntas ? 'Lulus' : 'Lulus (tahapan belum lengkap)';

        // Kalau "Lulus" sedang terpilih padahal tahapan diturunkan, kembalikan
        // ke Pending supaya nilai yang tersimpan tidak bertentangan dengan
        // aturan - server akan menolaknya juga.
        if (!tuntas && status.value === 'Lulus') {
            status.value = 'Pending';
        }
    }

    /**
     * Menampilkan langkah berikutnya yang harus diisi admin.
     *
     * Field yang abu-abu saja tidak cukup memberi tahu APA yang kurang -
     * admin harus menebak. Panel ini menyebutkan tahapnya secara eksplisit.
     */
    function gambarPetunjuk(tahapAktif, tahapPenghenti, nilaiPenghenti) {
        const kotak = dom.qs('#petunjukTahap');
        if (!kotak) return;

        const ikon = dom.qs('#petunjukIkon');
        const judul = dom.qs('#petunjukJudul');
        const pesan = dom.qs('#petunjukPesan');

        // Semua tahap sudah 'Hadir' - tidak ada yang tersisa.
        if (tahapAktif === -1) {
            kotak.className = 'rounded-xl border p-3 flex items-start gap-2.5 text-xs bg-emerald-50 border-emerald-100 text-emerald-800';
            if (ikon) ikon.className = 'bi bi-check-circle-fill text-base shrink-0 mt-0.5 text-emerald-600';
            if (judul) judul.textContent = 'Seluruh tahapan selesai';
            if (pesan) pesan.textContent = 'Semua tahap sudah ditandai Hadir. Tinggal tentukan Status Akhir peserta.';
            return;
        }

        const namaTahap = LABEL_TAHAP[tahapAktif];

        // Tahap sebelumnya diisi selain 'Hadir' (Alpha/Izin) sehingga alur
        // berhenti - ini kondisi yang perlu ditegaskan, bukan sekadar "isi ini".
        if (tahapPenghenti !== -1 && tahapPenghenti < tahapAktif) {
            kotak.className = 'rounded-xl border p-3 flex items-start gap-2.5 text-xs bg-amber-50 border-amber-100 text-amber-800';
            if (ikon) ikon.className = 'bi bi-exclamation-triangle-fill text-base shrink-0 mt-0.5 text-amber-600';
            if (judul) judul.textContent = 'Tahapan terhenti';
            if (pesan) pesan.textContent = LABEL_TAHAP[tahapPenghenti] + ' bertanda "' +
                (nilaiPenghenti || '-') + '", jadi tahap sesudahnya terkunci. Ubah ke "Hadir" bila peserta melanjutkan seleksi.';
            return;
        }

        const belumDiisi = !nilaiPenghenti;

        if (belumDiisi) {
            // Alur normal: tahap ini tinggal diisi.
            kotak.className = 'rounded-xl border p-3 flex items-start gap-2.5 text-xs bg-blue-50 border-blue-100 text-blue-800';
            if (ikon) ikon.className = 'bi bi-arrow-right-circle-fill text-base shrink-0 mt-0.5 text-blue-600';
            if (judul) judul.textContent = 'Langkah berikutnya: ' + namaTahap;
            if (pesan) {
                pesan.textContent = 'Isi kehadiran ' + namaTahap + ' terlebih dahulu.' +
                    (tahapAktif < LABEL_TAHAP.length - 1
                        ? ' Setelah ditandai "Hadir", ' + LABEL_TAHAP[tahapAktif + 1] + ' akan terbuka.'
                        : '');
            }
            return;
        }

        // Tahap ini sudah diisi Alpha/Izin - alur seleksi peserta berhenti di
        // sini. Diberi warna kuning seperti kondisi "terhenti" lainnya supaya
        // tidak terbaca sebagai langkah normal yang tinggal dilanjutkan.
        kotak.className = 'rounded-xl border p-3 flex items-start gap-2.5 text-xs bg-amber-50 border-amber-100 text-amber-800';
        if (ikon) ikon.className = 'bi bi-exclamation-triangle-fill text-base shrink-0 mt-0.5 text-amber-600';
        if (judul) judul.textContent = 'Tahapan terhenti di ' + namaTahap;
        if (pesan) {
            pesan.textContent = namaTahap + ' bertanda "' + nilaiPenghenti +
                '", sehingga tahap sesudahnya terkunci. Ubah ke "Hadir" bila peserta melanjutkan seleksi.';
        }
    }

    // Mengubah satu tahap harus langsung menghitung ulang kunci tahap
    // sesudahnya - termasuk mengunci kembali kalau nilainya diturunkan.
    dom.on('change', TAHAP_TAMBAH.join(','), function() { terapkanKunciTahap(TAHAP_TAMBAH); });
    dom.on('change', TAHAP_EDIT.join(','),   function() { terapkanKunciTahap(TAHAP_EDIT); });

    // Search filter
    dom.on('keyup', '#searchKehadiran', function() {
        const value = this.value.toLowerCase();
        dom.qsa('#monitoringTable tbody tr').forEach(function(row) {
            dom.toggle(row, row.textContent.toLowerCase().indexOf(value) > -1);
        });
    });

    dom.on('click', '#addMahasiswaButton', function() {
        const sel = dom.qs('#mahasiswa');
        if (!sel) return;
        const id = sel.value;
        if(!id) {
            showAlert('Pilih mahasiswa terlebih dahulu', false);
            return;
        }

        // Check duplicate
        if(selectedMahasiswa.includes(id)) {
            showAlert('Mahasiswa sudah dipilih', false);
            return;
        }

        const container = selectedContainer();
        if (container) {
            container.querySelectorAll('.empty-msg').forEach(function(e) { e.remove(); });
        }
        const txt = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '';
        selectedMahasiswa.push(id);

        if (container) {
            container.insertAdjacentHTML('beforeend', `
            <div class="multi-select-item selected flex items-center gap-3 p-3 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 text-sm" data-id="${id}">
                <i class="bi bi-person-check text-blue-600 text-lg"></i>
                <span class="flex-grow font-semibold">${txt}</span>
                <button type="button" class="text-red-500 hover:text-red-700 remove-item" aria-label="Tutup">
                    <i class="bi bi-x-lg pointer-events-none"></i>
                </button>
            </div>
        `);
        }
        sel.value = '';
    });

    dom.on('click', '.remove-item', function() {
        const item = this.closest('.multi-select-item');
        if (!item) return;
        const id = String(item.dataset.id);
        selectedMahasiswa = selectedMahasiswa.filter(i => i != id);
        item.remove();

        if (selectedMahasiswa.length === 0) {
            const container = selectedContainer();
            if (container) {
                container.innerHTML = `
                <div class="empty-msg text-center text-slate-400 py-6">
                    <i class="bi bi-inbox text-3xl mb-2 block"></i>
                    <p class="text-xs font-semibold">Belum ada mahasiswa dipilih</p>
                </div>
            `;
            }
        }
    });

    dom.on('submit', '#addJadwalForm', function(e) {
        e.preventDefault();

        if(selectedMahasiswa.length === 0) {
            showAlert('Pilih minimal 1 mahasiswa', false);
            return;
        }

        const data = {
            mahasiswa: selectedMahasiswa,
            tesTertulis: dom.val(dom.qs('#absensiTesTertulis')),
            presentasi: dom.val(dom.qs('#absensiPresentasi')),
            wawancara1: dom.val(dom.qs('#absensiWawancara1')),
            wawancara2: dom.val(dom.qs('#absensiWawancara2')),
        };

        dom.postBodyJSON(APP_URL + "/absensi", data)
            .then(function(res) {
                if(res.status === 'success') {
                    sessionStorage.setItem('pendingToast', JSON.stringify({
                        message: 'Data kehadiran berhasil disimpan!',
                        isSuccess: true
                    }));
                    location.reload();
                } else {
                    showAlert(res.message || 'Terjadi kesalahan', false);
                }
            })
            .catch(function() { showAlert('Gagal menghubungi server', false); });
    });

    // --- EDIT LOGIC ---
    dom.on('click', '.open-detail', function() {
        const data = this.dataset;

        dom.text(dom.qs('#detailNama'), data.nama);
        dom.text(dom.qs('#detailStambuk'), data.stambuk);
        dom.val(dom.qs('#detailUserId'), data.id || '');
        dom.val(dom.qs('#detailMhsId'), data.mhsid);
        dom.text(dom.qs('#avatarInitial'), (data.nama || '?').charAt(0).toUpperCase());

        // Tampilkan foto peserta bila sudah diunggah, selain itu pakai avatar
        // inisial. Cache-buster dipakai supaya foto yang baru diganti tidak
        // tertahan cache browser - sama seperti modal Rekap.
        const fotoEdit = dom.qs('#detailFoto');
        const avatarEdit = dom.qs('#detailAvatarContainer');
        if (data.foto && data.foto.trim() !== '') {
            if (fotoEdit) fotoEdit.setAttribute('src', data.foto + '?v=' + new Date().getTime());
            dom.show(fotoEdit);
            dom.hide(avatarEdit);
        } else {
            dom.hide(fotoEdit);
            dom.show(avatarEdit);
        }

        // Set values
        dom.val(dom.qs('#tesTertulis'), data.absensitestertulis || '');
        dom.val(dom.qs('#presentasi'), data.absensipresentasi || '');
        dom.val(dom.qs('#wawancaraI'), data.absensiwawancarai || '');
        dom.val(dom.qs('#wawancaraII'), data.absensiwawancaraii || '');
        dom.val(dom.qs('#detailStatusAkhir'), data.statusakhir || 'Pending');

        terapkanOpsiHadir(TAHAP_EDIT, [
            data.jadwaltes, data.jadwalpresentasi,
            data.jadwalwawancara1, data.jadwalwawancara2
        ]);
        terapkanKunciTahap(TAHAP_EDIT);

        UI.modal.open('#detailAbsensiModal');
    });

    dom.on('click', '#saveDetailAbsensi', function() {
        const data = {
            id: dom.val(dom.qs('#detailUserId')),
            mhsId: dom.val(dom.qs('#detailMhsId')),
            tesTertulis: dom.val(dom.qs('#tesTertulis')),
            presentasi: dom.val(dom.qs('#presentasi')),
            wawancaraI: dom.val(dom.qs('#wawancaraI')),
            wawancaraII: dom.val(dom.qs('#wawancaraII')),
            statusAkhir: dom.val(dom.qs('#detailStatusAkhir')),
        };

        dom.postBodyJSON(APP_URL + "/updateabsensi", data)
            .then(function(res) {
                if(res.status === 'success') {
                    showAlert('Perubahan berhasil disimpan!', true);

                    // Update DOM Row
                    const editBtn = dom.qs(`.open-detail[data-id="${data.id}"]`);
                    const tr = editBtn ? editBtn.closest('tr') : null;

                    if (tr) {
                        const getBadge = (val) => {
                            if(!val || typeof val !== 'string' || val.trim() === '' || val === '-') {
                                return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400 border border-slate-200">Belum Ada</span>';
                            }
                            const v = val.toLowerCase().trim();

                            if(v === 'hadir') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">Hadir</span>';
                            if(v === 'alpha') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-50 text-red-700 border border-red-100">Alpha</span>';
                            if(v === 'tidak hadir') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-50 text-red-700 border border-red-100">Tidak Hadir</span>';
                            if(v === 'izin') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-100">Izin</span>';
                            if(v === 'sakit') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-100">Sakit</span>';

                            return `<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-100">${val}</span>`;
                        };

                        // 1. Update Edit Button Data Attributes (for next edit)
                        editBtn.dataset.absensitestertulis = data.tesTertulis;
                        editBtn.dataset.absensipresentasi = data.presentasi;
                        editBtn.dataset.absensiwawancarai = data.wawancaraI;
                        editBtn.dataset.absensiwawancaraii = data.wawancaraII;
                        editBtn.dataset.statusakhir = data.statusAkhir;

                        // 2. Update Recap (Eye) Button Data Attributes
                        const rekapBtn = tr.querySelector('.open-rekap');
                        if (rekapBtn) {
                            rekapBtn.dataset.tes = data.tesTertulis;
                            rekapBtn.dataset.presentasi = data.presentasi;
                            rekapBtn.dataset.wawancara1 = data.wawancaraI;
                            rekapBtn.dataset.wawancara2 = data.wawancaraII;
                            rekapBtn.dataset.statusakhir = data.statusAkhir;
                        }

                        // 3. Update Table Columns
                        const cells = tr.querySelectorAll('td');
                        if (cells[3]) cells[3].innerHTML = getBadge(data.tesTertulis);
                        if (cells[4]) cells[4].innerHTML = getBadge(data.presentasi);
                        if (cells[5]) cells[5].innerHTML = getBadge(data.wawancaraI);
                        if (cells[6]) cells[6].innerHTML = getBadge(data.wawancaraII);

                        // 4. Update Status Akhir Badge
                        const statusBadge = tr.querySelector('.status-akhir-badge');
                        if(statusBadge) {
                            const s = data.statusAkhir;
                            statusBadge.textContent = s.toUpperCase();

                            dom.removeClass(statusBadge, 'text-emerald-700 bg-emerald-50 border-emerald-100 text-red-700 bg-red-50 border-red-100 text-amber-700 bg-amber-50 border-amber-100');
                            dom.addClass(statusBadge, 'border');

                            if(s === 'Lulus') dom.addClass(statusBadge, 'text-emerald-700 bg-emerald-50 border-emerald-100');
                            else if(s === 'Tidak Lulus') dom.addClass(statusBadge, 'text-red-700 bg-red-50 border-red-100');
                            else dom.addClass(statusBadge, 'text-amber-700 bg-amber-50 border-amber-100');
                        }
                    }

                    UI.modal.close('#detailAbsensiModal');
                } else {
                    showAlert(res.message || 'Terjadi kesalahan', false);
                }
            })
            .catch(function() { showAlert('Gagal menghubungi server', false); });
    });

    // --- REKAP DETAIL LOGIC ---
    dom.on('click', '.open-rekap', function() {
        const btnData = this.dataset;

        // Basic Info
        dom.text(dom.qs('#rekapNama'), btnData.nama);
        dom.text(dom.qs('#rekapStambuk'), btnData.stambuk);
        dom.text(dom.qs('#rekapAvatar'), (btnData.nama || '?').charAt(0).toUpperCase());

        // Handle Photo Display
        const fotoUrl = btnData.foto;
        const fotoImg = dom.qs('#rekapFoto');
        const avatarContainer = dom.qs('#rekapAvatarContainer');

        if (fotoUrl && fotoUrl.trim() !== '') {
            if (fotoImg) fotoImg.setAttribute('src', fotoUrl + '?v=' + new Date().getTime());
            dom.show(fotoImg);
            dom.hide(avatarContainer);
        } else {
            dom.hide(fotoImg);
            dom.show(avatarContainer);
        }

        // Helper to create badge
        const createBadge = (status, type = 'attendance') => {
            if(!status || status === '-' || status === '') 
                return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400 border border-slate-200">Belum Ada</span>';
            
            const s = status.toString().toLowerCase();
            
            if(type === 'berkas') {
                if(s === '1') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">Diterima</span>';
                if(s === '0') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-100">Pending</span>';
                return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-50 text-red-700 border border-red-100">Ditolak</span>';
            }
            
            // Attendance
            if(s === 'hadir') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">Hadir</span>';
            if(s === 'alpha' || s === 'tidak hadir') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-50 text-red-700 border border-red-100">Alpha</span>';
            if(s === 'izin' || s === 'sakit') return '<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-100">Izin</span>';
            
            return `<span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-100">${status}</span>`;
        };

        // 1. Berkas
        dom.html(dom.qs('#statusBerkas'), createBadge(btnData.berkas, 'berkas'));

        // 2. Tes Tertulis
        const nilai = btnData.nilai;
        const tesStatus = btnData.tes;

        let tesBadge = createBadge(tesStatus);
        if(nilai !== '') {
            dom.text(dom.qs('#scoreTes'), `Nilai: ${nilai}`);
            if(nilai >= 70) tesBadge += ' <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-emerald-100 text-emerald-800 ml-1">Lulus</span>';
            else tesBadge += ' <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-red-100 text-red-800 ml-1">Gagal</span>';
        } else {
            dom.text(dom.qs('#scoreTes'), 'Nilai: Belum keluar');
        }
        dom.html(dom.qs('#statusTes'), tesBadge);

        // 3. Presentasi
        dom.html(dom.qs('#statusPresentasi'), createBadge(btnData.presentasi));

        // 4. Wawancara
        dom.html(dom.qs('#statusWawancara1'), createBadge(btnData.wawancara1));
        dom.html(dom.qs('#statusWawancara2'), createBadge(btnData.wawancara2));

        // 5. Final Result
        const box = dom.qs('#finalResultBox');
        const badge = dom.qs('#finalStatus');

        dom.removeClass(box, 'bg-emerald-50 text-emerald-700 border-emerald-100 bg-red-50 text-red-700 border-red-100 bg-slate-50 text-slate-600 border-slate-200 bg-amber-50 text-amber-700 border-amber-100');
        dom.removeClass(badge, 'bg-emerald-600 hover:bg-emerald-700 text-white bg-red-600 hover:bg-red-700 bg-slate-600 bg-amber-500 border-emerald-100 border-red-100 border-slate-200 border-amber-100');

        const statusAkhir = btnData.statusakhir || 'Pending';

        if(statusAkhir === 'Lulus') {
            dom.addClass(box, 'bg-emerald-50 text-emerald-700 border border-emerald-100');
            dom.addClass(badge, 'bg-emerald-600 text-white');
            dom.text(badge, 'LULUS');
        } else if (statusAkhir === 'Tidak Lulus') {
            dom.addClass(box, 'bg-red-50 text-red-700 border border-red-100');
            dom.addClass(badge, 'bg-red-600 text-white');
            dom.text(badge, 'TIDAK LULUS');
        } else {
            dom.addClass(box, 'bg-amber-50 text-amber-700 border border-amber-100');
            dom.addClass(badge, 'bg-amber-500 text-white');
            dom.text(badge, 'PENDING');
        }

        UI.modal.open('#rekapDetailModal');
    });
})();
