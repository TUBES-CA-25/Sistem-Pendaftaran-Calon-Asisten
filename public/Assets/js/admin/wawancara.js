/**
 * admin/wawancara.js
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

        dom.on("click", "#addMahasiswaButton", () => {
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
            dom.postBodyJSON(APP_URL + "/wawancara", data)
                .then(function (response) {
                    if (response.status === 'success') {
                        UI.modal.close(modalId);
                        showAlert("Jadwal berhasil disimpan", true);
                        const link = document.querySelector('a[data-page="wawancara"]');
                        if (link) link.click();
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
                        const link = document.querySelector('a[data-page="wawancara"]');
                        if (link) link.click();
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
