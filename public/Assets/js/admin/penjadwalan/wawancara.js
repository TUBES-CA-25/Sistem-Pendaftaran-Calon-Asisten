/**
 * admin/penjadwalan/wawancara.js
 *
 * Dipindahkan dari app/View/admin/wawancara/index.php agar berkas view tetap ringkas
 * (markup saja) dan skrip halaman bisa di-cache browser.
 *
 * Bergantung pada: APP_URL (global dari layout), dom.js, ui.js.
 * Handler memakai dom.on() = delegasi di document, sehingga aman
 * dieksekusi ulang ketika SPA menyuntik ulang konten.
 */
// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content.
    (function () {

        function formatDate(dateString) {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // Catatan: showModal() dan showConfirm() lokal DIHAPUS di sini.
        // Keduanya menduplikasi window.showModal / showConfirmDelete milik
        // app.js (yang selalu dimuat lewat layout) tetapi TIDAK PERNAH dipanggil
        // di berkas ini — kode ini memakai showAlert() dan showConfirmDelete()
        // global. Versi app.js juga lebih lengkap (punya fallback alert dan
        // onCloseCallback).

        // Elemen diambil SAAT DIBUTUHKAN, bukan ditangkap sekali di sini.
        //
        // Dulu keempatnya disimpan sebagai const saat skrip pertama kali
        // dijalankan. Itu berfungsi selama halaman dimuat penuh, tetapi rusak
        // begitu markup disuntik ulang: halaman Penjadwalan mengganti isi tab
        // lewat innerHTML, sehingga elemen lama dibuang dari DOM dan referensi
        // yang tersimpan menunjuk ke elemen yatim. Akibatnya dropdown terlihat
        // terisi di layar, tetapi mahasiswaDropdown.value dibaca kosong dan
        // muncul "Pilih mahasiswa terlebih dahulu!" padahal sudah dipilih.
        //
        // Handler di bawah sudah memakai delegasi dom.on() yang tahan terhadap
        // penyuntikan ulang; pengambilan elemen ini menyusul agar konsisten.
        const el = {
            get dropdown() { return document.getElementById("mahasiswa"); },
            get daftar()   { return document.getElementById("selectedMahasiswaList"); },
            get jumlah()   { return document.getElementById("jumlahMahasiswaTerpilih"); },
        };

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
        window.__wawancaraTerpilih = window.__wawancaraTerpilih || [];

        /**
         * Menyembunyikan peserta yang sudah masuk daftar dari dropdown.
         *
         * Sebelumnya opsi tetap ada setelah dipilih, sehingga admin bisa
         * mencoba menambahkannya lagi dan baru ditolak lewat pesan error -
         * padahal pilihannya memang tidak berlaku lagi. Menyembunyikannya
         * membuat dropdown hanya berisi peserta yang benar-benar bisa dipilih.
         *
         * Dipakai <option hidden> + disabled, bukan menghapus elemennya:
         * daftar peserta bisa dikurangi lagi lewat tombol hapus, dan opsinya
         * harus bisa muncul kembali tanpa perlu memuat ulang data.
         */
        function segarkanOpsiDropdown() {
            const dd = el.dropdown;
            if (!dd) return;

            const terpakai = window.__wawancaraTerpilih.map(function (m) { return String(m.id); });

            // Jenis wawancara yang sedang dipilih menentukan siapa yang layak.
            // Peserta menjalani Lab I lalu Lab II, jadi yang sudah punya jadwal
            // Lab I tetap harus muncul saat admin menjadwalkan Lab II.
            const jenisEl = document.getElementById('wawancara');
            const jenis = jenisEl ? jenisEl.value : '';
            const kunciJenis = /lab II$/i.test(jenis)
                ? 'sudahLab2'
                : (/lab I$/i.test(jenis) ? 'sudahLab1' : null);

            // Pilihan mahasiswa dikunci sampai jenis wawancara ditentukan.
            // Tanpa ini admin bisa menyusun daftar dari peserta yang belum
            // tersaring, lalu separuhnya dikeluarkan begitu jenis dipilih.
            const tombolTambah = document.getElementById('addMahasiswaButton');
            const belumPilihJenis = !kunciJenis;

            dd.disabled = belumPilihJenis;
            dd.classList.toggle('bg-slate-100', belumPilihJenis);
            dd.classList.toggle('cursor-not-allowed', belumPilihJenis);
            dd.title = belumPilihJenis ? 'Pilih Jenis Kegiatan terlebih dahulu' : '';

            if (tombolTambah) {
                tombolTambah.disabled = belumPilihJenis;
                tombolTambah.classList.toggle('opacity-50', belumPilihJenis);
                tombolTambah.classList.toggle('cursor-not-allowed', belumPilihJenis);
            }

            if (belumPilihJenis) {
                Array.prototype.forEach.call(dd.options, function (opt) {
                    if (!opt.value) return;
                    opt.hidden = false;
                    opt.disabled = false;
                });
                return;
            }

            Array.prototype.forEach.call(dd.options, function (opt) {
                if (!opt.value) return; // biarkan placeholder "-- Pilih --"

                const dipakai = terpakai.indexOf(String(opt.value)) !== -1;
                const sudahTahapIni = opt.dataset[kunciJenis] === '1';

                const sembunyikan = dipakai || sudahTahapIni;
                opt.hidden = sembunyikan;
                opt.disabled = sembunyikan;
            });

            // Peserta yang sudah masuk daftar tapi ternyata tidak layak untuk
            // jenis yang baru dipilih harus dikeluarkan, bukan dibiarkan lalu
            // ditolak server saat menyimpan.
            {
                const sebelum = window.__wawancaraTerpilih.length;
                window.__wawancaraTerpilih = window.__wawancaraTerpilih.filter(function (m) {
                    const opt = dd.querySelector('option[value="' + m.id + '"]');
                    return !opt || opt.dataset[kunciJenis] !== '1';
                });
                if (window.__wawancaraTerpilih.length !== sebelum) {
                    renderSelectedMahasiswa();
                    showAlert('Beberapa peserta dikeluarkan: sudah dijadwalkan pada tahap ini', false);
                }
            }
        }

        // Gaya daftar mengikuti tab Presentasi: nomor urut, nama, tombol hapus.
        // Berbeda dari presentasi, wawancara tidak memakai slot berurutan -
        // semua peserta terpilih dijadwalkan pada jam yang sama, jadi nomor di
        // sini hanya penanda urutan daftar, bukan urutan waktu.
        function renderSelectedMahasiswa() {
            const daftar = el.daftar;
            if (!daftar) return;

            const jumlah = el.jumlah;
            if (jumlah) {
                jumlah.textContent = window.__wawancaraTerpilih.length + " peserta";
            }

            if (window.__wawancaraTerpilih.length === 0) {
                daftar.innerHTML =
                    '<li id="daftarMahasiswaKosong" class="text-center text-xs text-slate-400 py-4 border border-dashed border-slate-200 rounded-xl">Belum ada peserta dipilih</li>';
                return;
            }

            daftar.innerHTML = "";
            window.__wawancaraTerpilih.forEach((mahasiswa, i) => {
                const listItem = document.createElement("li");
                listItem.className = "flex items-center gap-2 py-2 px-3 bg-slate-50 rounded-xl border border-slate-100";

                const nomor = document.createElement("span");
                nomor.className = "shrink-0 w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center";
                nomor.textContent = String(i + 1);

                const nama = document.createElement("span");
                nama.className = "flex-1 min-w-0 truncate text-sm text-slate-700";
                nama.textContent = mahasiswa.text;

                const removeButton = document.createElement("button");
                removeButton.type = "button";
                removeButton.className = "shrink-0 text-red-500 hover:text-red-700 transition-colors";
                removeButton.setAttribute("aria-label", "Hapus dari daftar");
                removeButton.innerHTML = '<i class="bi bi-x-circle text-base pointer-events-none"></i>';
                removeButton.addEventListener("click", () => {
                    window.__wawancaraTerpilih = window.__wawancaraTerpilih.filter((item) => item.id !== mahasiswa.id);
                    renderSelectedMahasiswa();
                    segarkanOpsiDropdown();
                });

                listItem.appendChild(nomor);
                listItem.appendChild(nama);
                listItem.appendChild(removeButton);
                daftar.appendChild(listItem);
            });
        }

        // Kosongkan daftar tiap kali modal dibuka. Modal dipicu secara
        // deklaratif lewat data-modal-open sehingga tidak ada fungsi pembuka
        // yang bisa disisipi reset; tanpa ini peserta dari sesi sebelumnya
        // masih tertinggal di daftar saat modal dibuka lagi.
        dom.on("click", '[data-modal-open="#addJadwalModal"]', () => {
            window.__wawancaraTerpilih = [];
            renderSelectedMahasiswa();

            // form.reset() harus lebih dulu: ia mengosongkan pilihan Jenis
            // Kegiatan, dan segarkanOpsiDropdown() membaca nilai itu untuk
            // menentukan kunci. Kalau urutannya terbalik, kunci dihitung dari
            // jenis lama lalu reset membuatnya tidak sinkron.
            const form = document.getElementById("addJadwalForm");
            if (form) form.reset();

            segarkanOpsiDropdown();
        });

        // Mengganti jenis kegiatan harus langsung menyaring ulang daftar
        // peserta - Lab I dan Lab II punya kelayakan yang berbeda.
        dom.on("change", "#wawancara", segarkanOpsiDropdown);

        dom.on("click", "#addMahasiswaButton", () => {
            const mahasiswaDropdown = el.dropdown;
            if (!mahasiswaDropdown) return;

            const selectedOption = mahasiswaDropdown.options[mahasiswaDropdown.selectedIndex];
            const mahasiswaId = mahasiswaDropdown.value;
            const mahasiswaText = selectedOption ? selectedOption.text : null;

            if (!mahasiswaId) {
                showAlert("Pilih mahasiswa terlebih dahulu!", false);
                return;
            }

            if (window.__wawancaraTerpilih.some((item) => item.id === mahasiswaId)) {
                showAlert("Mahasiswa sudah dipilih!", false);
                return;
            }

            window.__wawancaraTerpilih.push({ id: mahasiswaId, text: mahasiswaText });
            renderSelectedMahasiswa();
            segarkanOpsiDropdown();

            mahasiswaDropdown.selectedIndex = 0;
        });





        /**
         * Menyegarkan tabel setelah data berubah.
         *
         * Dulu memakai `document.querySelector('a[data-page="wawancara"]').click()`
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
            const link = document.querySelector('a[data-page="wawancara"]');
            if (link) link.click();
        }

        function saveWawancara(data, modalId) {
            dom.postBodyJSON(APP_URL + "/wawancara", data)
                .then(function (response) {
                    if (response.status === 'success') {
                        UI.modal.close(modalId);
                        showAlert("Jadwal berhasil disimpan", true);
                        segarkanTabel();
                    } else {
                        showAlert("Jadwal gagal disimpan: " + response.message, false);
                    }
                })
                .catch(function (err) {
                    console.error("Error:", err);
                    showAlert("Gagal menyimpan jadwal. Silakan coba lagi.", false);
                });
        }

        dom.on("submit", "#addJadwalForm", (e) => {
            e.preventDefault();

            const ruangan = document.getElementById("ruangan").value;
            const tanggal = document.getElementById("tanggal").value;
            const waktu = document.getElementById("waktu").value;
            const wawancara = document.getElementById("wawancara").value;
            let id = window.__wawancaraTerpilih.map((item) => item.id);

            if (window.__wawancaraTerpilih.length === 0) {
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
        dom.on("click", ".open-update", function () {
            const d = this.dataset;
            const id = d.id;
            const ruangan_id = d.ruangan_id || this.getAttribute("data-ruangan_id");
            const jenisWawancara = d.jeniswawancara;
            const waktu = d.waktu;
            const tanggal = d.tanggal;

            dom.val(dom.qs("#updateWawancaraId"), id);
            dom.val(dom.qs("#updateRuangan"), ruangan_id);
            dom.val(dom.qs("#updateJenisWawancara"), jenisWawancara);

            let timeStr = waktu;
            if (timeStr && timeStr.length > 5) {
                timeStr = timeStr.substring(0, 5);
            }
            dom.val(dom.qs("#updateWaktu"), timeStr);

            dom.val(dom.qs("#updateTanggal"), tanggal);

            UI.modal.open('#updateWawancaraModal');
        });

        dom.on("submit", "#updateWawancaraForm", function (e) {
            e.preventDefault();

            const updateData = {
                id: dom.val(dom.qs("#updateWawancaraId")),
                ruangan: dom.val(dom.qs("#updateRuangan")),
                tanggal: dom.val(dom.qs("#updateTanggal")),
                waktu: dom.val(dom.qs("#updateWaktu")),
                jenisWawancara: dom.val(dom.qs("#updateJenisWawancara")),
            };

            dom.postBodyJSON(APP_URL + "/updatewawancara", updateData)
                .then(function (response) {
                    if (response.status === "success") {
                        showAlert("Jadwal berhasil diupdate", true);
                        UI.modal.close('#updateWawancaraModal');
                        // Refresh content via existing sidebar trigger
                        segarkanTabel();
                    } else {
                        showAlert("Gagal mengupdate jadwal wawancara: " + (response.message || "Unknown error"), false);
                    }
                })
                .catch(function (err) {
                    console.error("Error:", err);
                    showAlert("Gagal menghubungi server", false);
                });
        });

        dom.on("click", ".btn-delete-wawancara", function (e) {
            e.preventDefault();
            const id = this.dataset.id;

            if (!id) {
                console.error("Delete failed: No ID found on button.");
                return;
            }

            showConfirmDelete(function() {
                dom.postBodyJSON(APP_URL + "/deletewawancara", { id: id })
                    .then(function (response) {
                        if (response.status === "success") {
                            showAlert("Jadwal berhasil dihapus", true);

                            // Hapus baris dengan transisi opacity (pengganti fadeOut)
                            const delBtn = dom.qs(`.btn-delete-wawancara[data-id="${id}"]`);
                            const rowToRemove = delBtn ? delBtn.closest('tr') : null;
                            if (!rowToRemove) return;

                            rowToRemove.style.transition = 'opacity 300ms ease';
                            rowToRemove.style.opacity = '0';
                            setTimeout(function () {
                                rowToRemove.remove();
                                // Update row numbers
                                dom.qsa("#table-body tr:not(#noResultsRow)").forEach(function (row, index) {
                                    const firstCell = row.querySelector('td:first-child');
                                    if (firstCell) firstCell.textContent = index + 1;
                                });
                            }, 300);
                        } else {
                            showAlert("Gagal menghapus jadwal: " + (response.message || "Unknown error"), false);
                        }
                    })
                    .catch(function (err) {
                        console.error("Delete Error:", err);
                        showAlert("Gagal menghubungi server untuk menghapus jadwal.", false);
                    });
            }, "Apakah Anda yakin ingin menghapus jadwal wawancara ini?");
        });

        // Search functionality
        dom.on('keyup', '#searchInput', function() {
            const value = this.value.toLowerCase();
            const rows = dom.qsa("#table-body tr:not(#noResultsRow)");

            rows.forEach(function(row) {
                dom.toggle(row, row.textContent.toLowerCase().indexOf(value) > -1);
            });

            // Handle "No results found"
            const visibleRows = rows.filter(function(row) {
                return !row.classList.contains('hidden');
            }).length;

            const tableBody = dom.qs("#table-body");
            const existing = dom.qs('#noResultsRow');

            if (visibleRows === 0 && rows.length > 0) {
                if (!existing && tableBody) {
                    tableBody.insertAdjacentHTML('beforeend', `
                        <tr id="noResultsRow">
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <i class="bi bi-search text-5xl block mb-3 opacity-55"></i>
                                <span class="font-semibold text-sm">Data yang Anda cari tidak ditemukan</span>
                            </td>
                        </tr>
                    `);
                }
            } else if (existing) {
                existing.remove();
            }
        });

        dom.on('click', '.filter-btn', function () {
            const ruanganId = parseInt(this.getAttribute("data-id"), 10);

            dom.postBodyJSON(APP_URL + "/ruangan/getfilter", { id: ruanganId })
                .then(function (response) {
                    if (response.status === "success") {
                        const tableBody = dom.qs("#table-body");
                        if (!tableBody) return;
                        tableBody.innerHTML = '';
                        let i = 1;

                        if (response.data.length === 0) {
                            tableBody.insertAdjacentHTML('beforeend', `
                                <tr>
                                    <td colspan="8" class="text-center py-16 text-slate-400">
                                        <i class="bi bi-inbox text-5xl block mb-3 opacity-55"></i>
                                        <span class="font-semibold text-sm">Belum ada data jadwal wawancara di ruangan ini</span>
                                    </td>
                                </tr>
                            `);
                        } else {
                            response.data.forEach(row => {
                                tableBody.insertAdjacentHTML('beforeend', `
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
                                                        data-modal-open="#updateWawancaraModal"
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
                })
                .catch(function (err) {
                    console.error("Error:", err);
                    showAlert("Terjadi kesalahan dalam mengambil data. Silakan coba lagi.", false);
                });
        });
    })();
