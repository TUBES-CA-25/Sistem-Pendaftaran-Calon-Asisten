/**
 * peserta.js — Vanilla JS (no jQuery dependency)
 * Handles the Daftar Peserta (Participants List) admin page functionality.
 */

(function () {

    // ─────────────────────────────────────────────
    // HELPER: delegate event safely without jQuery
    // ─────────────────────────────────────────────
    function delegate(parent, selector, event, handler) {
        parent.addEventListener(event, function (e) {
            const target = e.target.closest(selector);
            if (target) handler.call(target, e);
        });
    }

    // ─────────────────────────────────────────────
    // HELPER: tampilkan satu panel tab, sembunyikan sisanya.
    //
    // Memakai class `hidden` (utility Tailwind yang benar-benar ada di CSS),
    // bukan aturan @apply hasil <style type="text/tailwindcss">. Blok style
    // semacam itu hanya dikompilasi Play CDN pada load awal; saat halaman
    // masuk lewat navigasi SPA (innerHTML) blok tersebut tidak pernah
    // dikompilasi sehingga .tab-panel tak punya gaya sama sekali dan ketiga
    // panel muncul bertumpuk. `active-tab` tetap di-set agar penanda panel
    // aktif konsisten dengan markup.
    // ─────────────────────────────────────────────
    function showTabPanel(tabId) {
        document.querySelectorAll('.tab-panel').forEach(function (p) {
            const isTarget = p.id === tabId;
            p.classList.toggle('hidden', !isTarget);
            p.classList.toggle('active-tab', isTarget);
        });
    }

    // ─────────────────────────────────────────────
    // MAIN INIT
    // ─────────────────────────────────────────────
    function initDaftarPesertaScript() {
        console.log('Daftar Peserta script loaded');

        // ── Custom search input ──────────────────
        const searchInput = document.getElementById('searchPeserta');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const val = this.value.toLowerCase();
                document.querySelectorAll('#daftarPesertaTable tbody tr').forEach(function (row) {
                    const cells = row.querySelectorAll('td');
                    const name    = cells[1] ? cells[1].textContent.toLowerCase() : '';
                    const stambuk = cells[2] ? cells[2].textContent.toLowerCase() : '';
                    const jurusan = cells[3] ? cells[3].textContent.toLowerCase() : '';
                    const status  = cells[4] ? cells[4].textContent.toLowerCase() : '';
                    const match   = name.includes(val) || stambuk.includes(val) || jurusan.includes(val) || status.includes(val);
                    row.style.display = match ? '' : 'none';
                });
            });
        }

        var currentRowData = null;

        // ── Send message to user button ──────────
        if (!window._participantsDocBound) {
            delegate(document, '#btnSendMessageToUser', 'click', function () {
            console.log('Open message modal clicked');
            var mahasiswaId = document.getElementById('modalMahasiswaId') ? document.getElementById('modalMahasiswaId').value : null;
            var nama = document.getElementById('modalNama') ? document.getElementById('modalNama').textContent : '';

            if (!mahasiswaId) {
                showAlert('ID Peserta tidak valid.', false);
                return;
            }

            var detailModalEl = document.getElementById('detailModal');
            var detailModal = UI.modal.ref(detailModalEl);
            if (detailModal) detailModal.hide();

            setTimeout(function () {
                var recipientEl = document.getElementById('messageRecipient');
                var idEl = document.getElementById('messageMahasiswaId');
                var msgEl = document.getElementById('individualMessage');
                if (recipientEl) recipientEl.textContent = nama;
                if (idEl) idEl.value = mahasiswaId;
                if (msgEl) msgEl.value = '';

                var msgModal = UI.modal.ref(document.getElementById('sendMessageModal'));
                msgModal.show();
            }, 300);
        });

        // ── Send individual message ──────────────
        delegate(document, '#sendIndividualMessage', 'click', function (e) {
            var btn = this;
            var mahasiswaId = document.getElementById('messageMahasiswaId') ? document.getElementById('messageMahasiswaId').value : null;
            var message = document.getElementById('individualMessage') ? document.getElementById('individualMessage').value : '';

            if (!message || message.trim() === '') {
                showAlert('Pesan tidak boleh kosong.', false);
                return;
            }

            var originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

            fetch(`${APP_URL}/notification`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${mahasiswaId}&message=${encodeURIComponent(message)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    var msgModal = UI.modal.ref(document.getElementById('sendMessageModal'));
                    if (msgModal) msgModal.hide();
                    showAlert('Pesan berhasil dikirim!', true);
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(err => {
                console.error(err);
                showAlert('Gagal mengirim pesan.', false);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });

        // ── View detail button (event delegation) ─
        delegate(document, '.btn-view', 'click', function () {
            try {
                var data = this.dataset;

                document.getElementById('modalMahasiswaId').value = data.id;
                document.getElementById('modalUserId').value = data.userid;
                currentRowData = { id: data.id, userId: data.userid, nama: data.nama, stambuk: data.stambuk };

                const PROJECT_ROOT = APP_URL.replace(/\/public$/, '');

                var modalFoto = document.getElementById('modalFoto');
                if (modalFoto) modalFoto.src = `${APP_URL}/Assets/Downloads/default.png`;

                document.getElementById('modalNamaHeader').textContent = data.nama || '-';
                document.getElementById('modalStambukHeader').textContent = data.stambuk || '-';

                var fotoPath = data.foto ? `${PROJECT_ROOT}/res/imageUser/${data.foto}` : `${APP_URL}/Assets/Downloads/default.png`;
                if (modalFoto) {
                    modalFoto.src = fotoPath;
                    modalFoto.onerror = function () { this.src = `${APP_URL}/Assets/Downloads/default.png`; };
                }

                // Populate fields
                var fields = ['modalNama','modalStambuk','modalJurusan','modalKelas','modalAlamat','modalTempat_lahir','modalTanggal_lahir','modalJenis_kelamin','modalJenisKelaminDetail','modalNoTelp'];
                var keys   = ['nama','stambuk','jurusan','kelas','alamat','tempat_lahir','tanggal_lahir','jenis_kelamin','jenis_kelamin','notelp'];
                fields.forEach((id, i) => {
                    var el = document.getElementById(id);
                    if (el) el.textContent = data[keys[i]] || '-';
                });
                var jurusanEl = document.getElementById('modalJurusan');
                if (jurusanEl) jurusanEl.title = data.jurusan || '-';

                var optionalTabs = {modalJurusanTab:'jurusan', modalKelasTab:'kelas', modalNoTelpTab:'notelp'};
                Object.entries(optionalTabs).forEach(([id, key]) => {
                    var el = document.getElementById(id);
                    if (el) el.textContent = data[key] || '-';
                });

                // Reset tabs
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.remove('text-blue-600');
                    btn.classList.remove('border-blue-600');
                    btn.classList.add('text-slate-500');
                    btn.classList.add('border-transparent');
                });
                var firstTabBtn = document.querySelector('.tab-btn[data-tab="tab-profil"]');
                if (firstTabBtn) {
                    firstTabBtn.classList.add('active');
                    firstTabBtn.classList.add('text-blue-600');
                    firstTabBtn.classList.add('border-blue-600');
                    firstTabBtn.classList.remove('text-slate-500');
                    firstTabBtn.classList.remove('border-transparent');
                }
                showTabPanel('tab-profil');

                // Judul Presentasi
                var judulPresentasi = data.judul_presentasi;
                var judulPresentasiEl = document.getElementById('modalJudulPresentasi');
                if (judulPresentasiEl) {
                    if (judulPresentasi && judulPresentasi.trim() !== '') {
                        judulPresentasiEl.textContent = judulPresentasi;
                        judulPresentasiEl.classList.remove('text-muted','fst-italic');
                    } else {
                        judulPresentasiEl.textContent = 'Belum diisi oleh peserta';
                        judulPresentasiEl.classList.add('text-muted','fst-italic');
                    }
                }
                var presentasiSection = document.getElementById('presentasiSection');
                if (presentasiSection) presentasiSection.style.display = 'block';

                // Status badge
                var statusBadge = document.getElementById('modalStatusBadge');
                var statusBadgeIcon = document.getElementById('modalStatusBadgeIcon');
                var statusBadgeText = document.getElementById('modalStatusBadgeText');
                var statusIcon = document.getElementById('modalStatusIcon');
                var statusIconInner = document.getElementById('modalStatusIconInner');
                var btnVerifikasi = document.getElementById('btnVerifikasiModal');
                var btnBatalkan = document.getElementById('btnBatalkanModal');
                var btnTerima = document.getElementById('btnTerimaModal');
                var btnTolak = document.getElementById('btnTolakModal');

                if (!btnVerifikasi || !btnBatalkan) return;

                btnVerifikasi.style.display = 'none';
                btnBatalkan.style.display = 'none';
                if (btnTerima) btnTerima.style.display = 'none';
                if (btnTolak) btnTolak.style.display = 'none';

                var berkasAccepted = data.berkas_accepted;
                if (berkasAccepted == '1') {
                    if (statusBadge) statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-emerald-500 text-white';
                    if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-check-circle me-1';
                    if (statusBadgeText) statusBadgeText.textContent = 'Berkas Terverifikasi';
                    if (statusIcon) statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-emerald-500 border-2 border-white';
                    if (statusIconInner) statusIconInner.className = 'bi bi-check-lg';
                    btnBatalkan.style.display = 'inline-block';
                } else if (berkasAccepted == '0') {
                    if (statusBadge) statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-blue-500 text-white';
                    if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-hourglass-split me-1';
                    if (statusBadgeText) statusBadgeText.textContent = 'Menunggu Verifikasi';
                    if (statusIcon) statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-blue-500 border-2 border-white';
                    if (statusIconInner) statusIconInner.className = 'bi bi-clock';
                    btnVerifikasi.style.display = 'inline-block';
                    btnVerifikasi.disabled = false;
                    if (btnTolak) btnTolak.style.display = 'inline-block';
                } else if (berkasAccepted == '2') {
                    if (statusBadge) statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-red-500 text-white';
                    if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-x-circle me-1';
                    if (statusBadgeText) statusBadgeText.textContent = 'Berkas Ditolak';
                    if (statusIcon) statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-red-500 border-2 border-white';
                    if (statusIconInner) statusIconInner.className = 'bi bi-x-lg';
                    btnBatalkan.style.display = 'inline-block';
                } else {
                    if (statusBadge) statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-slate-500 text-white';
                    if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-file-earmark-x me-1';
                    if (statusBadgeText) statusBadgeText.textContent = 'Belum Upload Berkas';
                    if (statusIcon) statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-slate-500 border-2 border-white';
                    if (statusIconInner) statusIconInner.className = 'bi bi-dash';
                }

                // Download buttons
                const downloads = {
                    'downloadFotoButton':     data.foto     ? `${PROJECT_ROOT}/res/imageUser/${data.foto}`    : '',
                    'downloadCVButton':       data.cv       ? `${PROJECT_ROOT}/res/berkasUser/${data.cv}`     : '',
                    'downloadTranskripButton':data.transkrip? `${PROJECT_ROOT}/res/berkasUser/${data.transkrip}`: '',
                    'downloadSuratButton':    data.surat    ? `${PROJECT_ROOT}/res/berkasUser/${data.surat}`  : ''
                };
                Object.entries(downloads).forEach(([id, url]) => {
                    var btn = document.getElementById(id);
                    if (btn) btn.setAttribute('data-download-url', url);
                });

                // Presentasi files
                var makalahBtn = document.getElementById('downloadMakalahButton');
                var pptBtn = document.getElementById('downloadPptButton');
                var noPresentasiFiles = document.getElementById('noPresentasiFiles');
                var hasPresentasiFiles = false;
                if (makalahBtn) {
                    if (data.makalah) {
                        makalahBtn.setAttribute('data-download-url', `${PROJECT_ROOT}/res/makalahUser/${data.makalah}`);
                        makalahBtn.style.display = 'inline-flex';
                        hasPresentasiFiles = true;
                    } else {
                        makalahBtn.style.display = 'none';
                    }
                }
                if (pptBtn) {
                    if (data.ppt) {
                        pptBtn.setAttribute('data-download-url', `${PROJECT_ROOT}/res/pptUser/${data.ppt}`);
                        pptBtn.style.display = 'inline-flex';
                        hasPresentasiFiles = true;
                    } else {
                        pptBtn.style.display = 'none';
                    }
                }
                if (noPresentasiFiles) noPresentasiFiles.style.display = hasPresentasiFiles ? 'none' : 'inline-block';

                // Show modal
                var detailModal = UI.modal.ref(document.getElementById('detailModal'));
                detailModal.show();

            } catch (error) {
                console.error('Error opening detail modal:', error);
                showAlert('Terjadi kesalahan saat membuka detail peserta: ' + error.message, false);
            }
        });

        // ── Tab switching ────────────────────────
        delegate(document, '.tab-btn', 'click', function () {
            var tabId = this.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('text-blue-600');
                btn.classList.remove('border-blue-600');
                btn.classList.add('text-slate-500');
                btn.classList.add('border-transparent');
            });
            this.classList.add('active');
            this.classList.add('text-blue-600');
            this.classList.add('border-blue-600');
            this.classList.remove('text-slate-500');
            this.classList.remove('border-transparent');
            showTabPanel(tabId);
        });

        // ── Download berkas button ───────────────
        delegate(document, '.btn-download-berkas, #downloadMakalahButton, #downloadPptButton', 'click', function () {
            var url = this.getAttribute('data-download-url');
            if (url && url.trim() !== '') {
                window.open(url, '_blank');
            } else {
                showAlert('File tidak tersedia', false);
            }
        });

        // ── Delete button ────────────────────────
        delegate(document, '.btn-delete', 'click', function () {
            var row = this.closest('tr');
            var userId = row ? row.getAttribute('data-userid') : null;
            var mhsId = row ? row.getAttribute('data-id') : null;

            if (!userId && !mhsId) {
                showAlert('ID data tidak ditemukan', false);
                return;
            }

            showDeleteConfirmation({
                message: 'Apakah Anda yakin ingin menghapus data peserta ini?',
                id: userId || mhsId,
                type: userId ? 'user' : 'mahasiswa',
                onConfirm: function (id, type) {
                    var bodyParams = type === 'user' ? 'id=' + id : 'mahasiswaId=' + id;
                    fetch(`${APP_URL}/deletemahasiswa`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: bodyParams
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert('Data berhasil dihapus!', true);
                            const row = document.querySelector(`.dt-body-row[data-userid="${id}"]`) || document.querySelector(`.dt-body-row[data-id="${id}"]`);
                            if (row) row.remove();
                        } else {
                            showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        showAlert('Terjadi kesalahan saat menghapus data', false);
                    });
                }
            });
        });

        // ── Notification form handlers ───────────
        var selectedMahasiswa = [];

        function updateSelectedCount() {
            var el = document.getElementById('selectedCount');
            if (el) el.textContent = selectedMahasiswa.length;
        }

        function renderSelectedMahasiswa() {
            var list = document.getElementById('selectedMahasiswaList');
            if (!list) return;
            list.innerHTML = '';

            if (selectedMahasiswa.length === 0) {
                var li = document.createElement('li');
                li.className = 'list-group-item text-muted text-center py-3';
                li.innerHTML = '<i class="bi bi-inbox me-1"></i>Belum ada peserta dipilih';
                list.appendChild(li);
            } else {
                selectedMahasiswa.forEach(function (mhs, index) {
                    var li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                    var span = document.createElement('span');
                    span.className = 'small';
                    span.textContent = mhs.text;
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-outline-danger';
                    btn.dataset.index = index;
                    btn.innerHTML = '<i class="bi bi-x"></i>';
                    li.appendChild(span);
                    li.appendChild(btn);
                    list.appendChild(li);
                });

                list.querySelectorAll('button').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var idx = parseInt(this.dataset.index);
                        selectedMahasiswa.splice(idx, 1);
                        renderSelectedMahasiswa();
                        updateSelectedCount();
                    });
                });
            }
        }

        var addBtn = document.getElementById('addMahasiswaButton');
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                var select = document.getElementById('mahasiswa');
                var selectedOption = select.options[select.selectedIndex];
                if (selectedOption.value) {
                    var exists = selectedMahasiswa.some(m => m.id === selectedOption.value);
                    if (!exists) {
                        selectedMahasiswa.push({ id: selectedOption.value, text: selectedOption.textContent.trim() });
                        renderSelectedMahasiswa();
                    } else {
                        showAlert('Peserta sudah dipilih', false);
                    }
                } else {
                    showAlert('Pilih peserta terlebih dahulu', false);
                }
            });
        }

        var addAllBtn = document.getElementById('addAllMahasiswaButton');
        if (addAllBtn) {
            addAllBtn.addEventListener('click', function () {
                var select = document.getElementById('mahasiswa');
                selectedMahasiswa = [];
                Array.from(select.options).forEach(function (opt) {
                    if (opt.value) selectedMahasiswa.push({ id: opt.value, text: opt.textContent.trim() });
                });
                renderSelectedMahasiswa();
            });
        }

        var notifForm = document.getElementById('addNotificationForm');
        if (notifForm) {
            notifForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var message = document.getElementById('notifMessage') ? document.getElementById('notifMessage').value : '';
                var btnSubmit = document.querySelector('button[form="addNotificationForm"]');
                var originalText = btnSubmit ? btnSubmit.innerHTML : '';

                if (!message.trim()) {
                    showAlert('Pesan tidak boleh kosong', false);
                    return;
                }

                var select = document.getElementById('mahasiswa');
                var mahasiswaIds = Array.from(select.options).filter(o => o.value).map(o => o.value);

                if (mahasiswaIds.length === 0) {
                    showAlert('Tidak ada peserta yang terdaftar untuk dikirimi notifikasi.', false);
                    return;
                }

                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim Broadcast...';
                }

                fetch(`${APP_URL}/addallnotif`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mahasiswaIds, message })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Broadcast berhasil dikirim ke ' + mahasiswaIds.length + ' peserta!', true);
                        var modal = UI.modal.ref(document.getElementById('addNotification'));
                        if (modal) modal.hide();
                        var msgEl = document.getElementById('notifMessage');
                        if (msgEl) msgEl.value = '';
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showAlert('Terjadi kesalahan saat mengirim broadcast', false);
                })
                .finally(() => {
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalText;
                    }
                });
            });

            window._participantsDocBound = true;
        }
        }

        renderSelectedMahasiswa();

        if (typeof window.initDaftarPeserta === 'function') {
            window.initDaftarPeserta();
        }
    }

    // ─────────────────────────────────────────────
    // INIT: Run when DOM is ready (no jQuery needed)
    // ─────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDaftarPesertaScript);
    } else {
        // Already loaded (e.g., after AJAX page inject)
        initDaftarPesertaScript();
    }

})(); // Universal Wrapper Ends

// ─────────────────────────────────────────────
// TRIGGER VERIFICATION FROM DETAIL MODAL
// ─────────────────────────────────────────────
function triggerVerificationFromModal() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaLengkap = document.getElementById('modalNamaHeader').textContent;

    if (mahasiswaId && namaLengkap) {
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = UI.modal.ref(detailModalEl);
        if (detailModal) detailModal.hide();

        setTimeout(() => {
            showActionConfirmation({
                title: 'Konfirmasi Verifikasi Berkas',
                message: `Anda akan memverifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${namaLengkap}</strong><span class="text-secondary small"><i class="bi bi-info-circle me-1"></i>Pastikan semua dokumen telah sesuai sebelum melanjutkan</span>`,
                btnText: 'Verifikasi',
                type: 'success',
                onConfirm: function () {
                    showAlert('Memproses verifikasi...', true);
                    fetch(`${APP_URL}/acceptberkas`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + mahasiswaId + '&status=1'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert('Berhasil! Berkas berhasil diverifikasi!', true);
                            updateParticipantRowStatus(mahasiswaId, 1);
                        } else {
                            showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                        }
                    })
                    .catch(err => showAlert('Error: ' + err.message, false));
                }
            });
        }, 350);
    } else {
        showAlert('Data peserta tidak ditemukan', false);
    }
}

// ─────────────────────────────────────────────
// CANCEL VERIFICATION FROM DETAIL MODAL
// ─────────────────────────────────────────────
function cancelVerification() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaLengkap = document.getElementById('modalNamaHeader').textContent;

    if (mahasiswaId && namaLengkap) {
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = UI.modal.ref(detailModalEl);
        if (detailModal) detailModal.hide();

        setTimeout(() => {
            showActionConfirmation({
                title: 'Batalkan Verifikasi Berkas',
                message: `Anda akan membatalkan verifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${namaLengkap}</strong><span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Status akan kembali menjadi "Menunggu Verifikasi"</span>`,
                btnText: 'Batalkan',
                type: 'danger',
                onConfirm: function () {
                    showAlert('Membatalkan verifikasi...', true);
                    fetch(`${APP_URL}/acceptberkas`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + mahasiswaId + '&status=0'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert('Berhasil! Verifikasi dibatalkan.', true);
                            updateParticipantRowStatus(mahasiswaId, 0);
                        } else {
                            showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                        }
                    })
                    .catch(err => showAlert('Error: ' + err.message, false));
                }
            });
        }, 350);
    } else {
        showAlert('Data peserta tidak ditemukan', false);
    }
}

// ─────────────────────────────────────────────
// REMINDER BUTTON HANDLER
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    if (window._participantsReminderBound) return;
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-reminder');
        if (!btn) return;
        const userId = btn.getAttribute('data-userid');
        const nama = btn.getAttribute('data-nama');

        showActionConfirmation({
            title: 'Kirim Reminder',
            message: `Kirim reminder ke <strong>${nama}</strong> untuk upload berkas?`,
            btnText: 'Kirim',
            type: 'primary',
            onConfirm: function () {
                showAlert('Mengirim reminder...', true);
                fetch(`${APP_URL}/sendNotification`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `user_id=${userId}&message=Mohon segera upload berkas pendaftaran Anda.&title=Reminder Upload Berkas`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Reminder berhasil dikirim!', true);
                    } else {
                        showAlert('Gagal mengirim reminder: ' + (data.message || 'Unknown error'), false);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showAlert('Error saat mengirim reminder', false);
                });
            }
        });
    });
    window._participantsReminderBound = true;
});

// ─────────────────────────────────────────────
// ACCEPT / REJECT PARTICIPANT FUNCTIONS
// ─────────────────────────────────────────────
function acceptParticipant() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaEl = document.getElementById('modalNama') || document.getElementById('modalNamaHeader');
    const nama = namaEl ? namaEl.textContent : 'Peserta';

    if (!mahasiswaId) { showAlert('ID Mahasiswa tidak ditemukan', false); return; }

    const detailModalEl = document.getElementById('detailModal');
    const detailModal = UI.modal.ref(detailModalEl);
    if (detailModal) detailModal.hide();

    setTimeout(() => {
        showActionConfirmation({
            title: 'Verifikasi Berkas',
            message: `Anda akan memverifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${nama}</strong><span class="text-secondary small"><i class="bi bi-info-circle me-1"></i>Pastikan semua dokumen telah sesuai</span>`,
            btnText: 'Verifikasi',
            type: 'success',
            onConfirm: function () {
                showAlert('Memproses verifikasi...', true);
                fetch(`${APP_URL}/acceptberkas`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + mahasiswaId + '&status=1'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Berkas berhasil diverifikasi!', true);
                        updateParticipantRowStatus(mahasiswaId, 1);
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Unknown'), false);
                    }
                })
                .catch(err => showAlert('Error: ' + err.message, false));
            }
        });
    }, 350);
}

function rejectParticipant() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaEl = document.getElementById('modalNama') || document.getElementById('modalNamaHeader');
    const nama = namaEl ? namaEl.textContent : 'Peserta';

    if (!mahasiswaId) { showAlert('ID Mahasiswa tidak ditemukan', false); return; }

    const detailModalEl = document.getElementById('detailModal');
    const detailModal = UI.modal.ref(detailModalEl);
    if (detailModal) detailModal.hide();

    setTimeout(() => {
        showActionConfirmation({
            title: 'Tolak Verifikasi Berkas',
            message: `Anda akan menolak verifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${nama}</strong><span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Status akan diubah menjadi Ditolak</span>`,
            btnText: 'Tolak Verifikasi',
            type: 'danger',
            onConfirm: function () {
                showAlert('Memproses penolakan...', true);
                fetch(`${APP_URL}/acceptberkas`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + mahasiswaId + '&status=2'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Verifikasi berkas ditolak!', true);
                        updateParticipantRowStatus(mahasiswaId, 2);
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Unknown'), false);
                    }
                })
                .catch(err => showAlert('Error: ' + err.message, false));
            }
        });
    }, 350);
}

// ─────────────────────────────────────────────
// DETAIL MODAL BACKDROP CLEANUP (Vanilla JS)
// ─────────────────────────────────────────────
function updateParticipantRowStatus(mahasiswaId, status) {
    const row = document.querySelector(`.dt-body-row[data-id="${mahasiswaId}"]`);
    if (row) {
        const badgeContainer = row.querySelector('td:nth-child(5) span');
        if (badgeContainer) {
            badgeContainer.className = 'inline-block px-3 py-1.5 text-xs font-semibold rounded-lg';
            if (status == 1) {
                badgeContainer.classList.add('bg-green-100', 'text-green-700');
                badgeContainer.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Lulus Berkas';
            } else if (status == 2) {
                badgeContainer.classList.add('bg-red-100', 'text-red-700');
                badgeContainer.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Ditolak';
            } else {
                badgeContainer.classList.add('bg-blue-100', 'text-blue-700');
                badgeContainer.innerHTML = '<i class="bi bi-clock-fill me-1"></i>Menunggu Verifikasi';
            }
        }
        
        const viewBtn = row.querySelector('.btn-view');
        if (viewBtn) {
            viewBtn.setAttribute('data-berkas_accepted', status == 0 ? '' : status);
        }
    }
}

// Blok initModalCleanup() dihapus (42 baris).
//
// Isinya membersihkan sisa .modal-backdrop dan class .modal-open milik
// Bootstrap lewat listener 'shown.bs.modal'/'hidden.bs.modal'. Ketiganya
// sudah tidak ada di project ini: Bootstrap dibuang, core/ui.js memakai
// backdrop per-modal dan penghitung scroll-lock sendiri, serta memancarkan
// 'modal:shown'/'modal:hidden'. Jadi listener tidak pernah menyala dan
// selektornya tidak pernah cocok — murni kode mati.
