/**
 * rooms.js — Vanilla JS (no jQuery dependency)
 * Admin Rooms Management: handles room CRUD and participant assignment.
 */

(function () {

    function initRoomsScript() {
        console.log('Rooms script loaded v3.0 - Vanilla JS');

        let currentRoomId   = null;
        let currentRoomName = '';
        let currentType     = 'tes_tulis'; // default tab

        // ── Helper: get element by id ────────────
        function el(id) { return document.getElementById(id); }

        // ── NAVIGATION ───────────────────────────
        function showDetailView(id, name) {
            currentRoomId   = id;
            currentRoomName = name;

            el('ruanganListSection')  && el('ruanganListSection').classList.add('hidden');
            el('ruanganDetailSection')&& el('ruanganDetailSection').classList.remove('hidden');
            el('detailRoomTitle')     && (el('detailRoomTitle').textContent = name);

            const triggerEl = document.querySelector('#pills-testulis-tab');
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
                currentType = 'tes_tulis';
                loadParticipants();
            }

            window.scrollTo(0, 0);
        }

        function showListView() {
            currentRoomId = null;
            el('ruanganDetailSection')&& el('ruanganDetailSection').classList.add('hidden');
            el('ruanganListSection')  && el('ruanganListSection').classList.remove('hidden');
        }

        var backBtn = el('backToListBtn');
        if (backBtn) backBtn.addEventListener('click', showListView);

        // ── TABS ─────────────────────────────────
        document.querySelectorAll('.nav-link').forEach(function (navLink) {
            navLink.addEventListener('shown.bs.tab', function (e) {
                const targetId = e.target.id;
                if      (targetId === 'pills-presentasi-tab') currentType = 'presentasi';
                else if (targetId === 'pills-testulis-tab')   currentType = 'tes_tulis';
                else if (targetId === 'pills-wawancara-tab')  currentType = 'wawancara';
                loadParticipants();
            });
        });

        // ── LOAD PARTICIPANTS ─────────────────────
        function loadParticipants() {
            if (!currentRoomId) return;
            var tbody = el('participantsTableBody');
            if (tbody) tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary speed-fast" role="status"></div></td></tr>';

            const formData = new URLSearchParams({ id: currentRoomId, type: currentType });
            fetch(`${APP_URL}/getroomparticipants`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderParticipants(res.assigned);
                    updateParticipantCount(res.assigned.length);
                } else {
                    showAlert('Error: ' + res.message, false);
                }
            })
            .catch(() => {
                var tbody = el('participantsTableBody');
                if (tbody) tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-5">Gagal memuat data. Periksa koneksi.</td></tr>';
            });
        }

        function updateParticipantCount(count) {
            var countEl = el('participantCount');
            if (countEl) countEl.textContent = count;
        }

        function renderParticipants(users) {
            var tbody = el('participantsTableBody');
            if (!tbody) return;
            var hasStatusCol = (currentType === 'tes_tulis');

            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 block mb-3 opacity-50"></i>
                                <h6 class="fw-semibold">Belum ada peserta</h6>
                                <p class="small mb-0">Peserta akan muncul di sini setelah ditambahkan</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = '';
            users.forEach((u, index) => {
                var statusBadge = '';
                if (hasStatusCol) {
                    statusBadge = u.is_finished == 1
                        ? `<td class="text-center"><span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Selesai</span></td>`
                        : `<td class="text-center"><span class="badge bg-warning px-3 py-2"><i class="bi bi-clock me-1"></i>Pending</span></td>`;
                } else {
                    statusBadge = `<td class="text-center"><span class="badge bg-info px-3 py-2"><i class="bi bi-person-check me-1"></i>Terdaftar</span></td>`;
                }

                var tr = document.createElement('tr');
                tr.className = 'participant-row';
                tr.innerHTML = `
                    <td class="text-center fw-semibold text-muted">${index + 1}</td>
                    <td class="participant-name fw-medium">${u.name || '-'}</td>
                    <td class="participant-stambuk text-muted">${u.stambuk || '-'}</td>
                    ${statusBadge}
                `;
                tbody.appendChild(tr);
            });
        }

        // ── SEARCH PARTICIPANTS ───────────────────
        var searchParticipants = el('searchParticipants');
        if (searchParticipants) {
            searchParticipants.addEventListener('keyup', function () {
                var term = this.value.toLowerCase();
                document.querySelectorAll('.participant-row').forEach(function (row) {
                    var name    = (row.querySelector('.participant-name')   || {}).textContent || '';
                    var stambuk = (row.querySelector('.participant-stambuk')|| {}).textContent || '';
                    row.style.display = (name.toLowerCase().includes(term) || stambuk.toLowerCase().includes(term)) ? '' : 'none';
                });
            });
        }

        // ── ADD ROOM ─────────────────────────────
        var tambahForm = el('tambahRuanganForm');
        if (tambahForm) {
            tambahForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var namaRuangan = el('namaRuangan') ? el('namaRuangan').value : '';
                fetch(`${APP_URL}/tambahruangan`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'namaRuangan=' + encodeURIComponent(namaRuangan)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showAlert('Ruangan berhasil ditambahkan!', true);
                        
                        // Close modal
                        const modalEl = document.getElementById('tambahRuanganModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            el('tambahRuanganForm').reset();
                        }
                        
                        // Dynamically append new row
                        const tbody = document.getElementById('ruanganTableBody');
                        if (tbody) {
                            // Remove empty state if it exists
                            if (tbody.querySelector('td[colspan="3"]')) {
                                tbody.innerHTML = '';
                            }
                            
                            const rowCount = tbody.querySelectorAll('tr').length + 1;
                            const newRow = document.createElement('tr');
                            newRow.className = 'dt-body-row room-item border-b border-slate-100 hover:bg-slate-50 transition-colors';
                            newRow.setAttribute('data-id', res.id);
                            newRow.setAttribute('data-name', res.nama);
                            
                            newRow.innerHTML = `
                                <td class="text-center py-4 px-4 font-semibold text-slate-600">${rowCount}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0 border border-blue-100">
                                            <i class="bi bi-buildings-fill text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-base">${res.nama}</div>
                                            <div class="text-[10px] font-bold text-slate-400 tracking-wider mt-0.5 uppercase"><i class="bi bi-geo-alt-fill text-[9px] me-1"></i>Ruangan Seleksi</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-blue-50 hover:bg-blue-100 text-blue-600 btn-edit-room" 
                                                title="Ubah Nama"
                                                data-id="${res.id}"
                                                data-name="${res.nama}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center transition bg-red-50 hover:bg-red-100 text-red-600 btn-delete-room" 
                                                title="Hapus"
                                                data-id="${res.id}"
                                                data-name="${res.nama}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(newRow);
                        }
                    } else {
                        showAlert(res.message, false);
                    }
                });
            });
        }

        // ── EDIT ROOM ─────────────────────────────
        var updateForm = el('updateRuanganForm');
        if (updateForm) {
            updateForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var id   = el('updateRuanganId')    ? el('updateRuanganId').value    : '';
                var name = el('updateNamaRuangan')  ? el('updateNamaRuangan').value  : '';

                fetch(`${APP_URL}/updateruangan`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${encodeURIComponent(id)}&namaRuangan=${encodeURIComponent(name)}`
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        if (currentRoomId == id) {
                            el('detailRoomTitle') && (el('detailRoomTitle').textContent = name);
                            currentRoomName = name;
                        }
                        
                        showAlert('Nama ruangan diperbarui', true);
                        
                        // Close modal
                        const modalEl = document.getElementById('updateRuanganModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            el('updateRuanganForm').reset();
                        }
                        
                        // Dynamically update row
                        const row = document.querySelector(`.room-item[data-id="${id}"]`);
                        if (row) {
                            row.setAttribute('data-name', name);
                            const nameEl = row.querySelector('.font-bold.text-slate-800.text-base');
                            if (nameEl) nameEl.textContent = name;
                            
                            // update button data attributes
                            const btnEdit = row.querySelector('.btn-edit-room');
                            if (btnEdit) btnEdit.setAttribute('data-name', name);
                            
                            const btnDelete = row.querySelector('.btn-delete-room');
                            if (btnDelete) btnDelete.setAttribute('data-name', name);
                        }
                    } else {
                        showAlert(res.message, false);
                    }
                });
            });
        }

        // ── DELETE PARTICIPANT ───────────────────
    if (!window._roomsDocBound) {
        document.addEventListener('click', function (e) {
            // Edit room button
            var editBtn = e.target.closest('.btn-edit-room');
            if (editBtn) {
                e.stopPropagation();
                var id   = editBtn.getAttribute('data-id');
                var name = editBtn.getAttribute('data-name');
                el('updateRuanganId')   && (el('updateRuanganId').value   = id);
                el('updateNamaRuangan') && (el('updateNamaRuangan').value = name);
                var modal = new bootstrap.Modal(document.getElementById('updateRuanganModal'));
                modal.show();
                return;
            }

            // Delete room button
            var deleteBtn = e.target.closest('.btn-delete-room');
            if (deleteBtn) {
                e.stopPropagation();
                var id   = deleteBtn.getAttribute('data-id');
                var name = deleteBtn.getAttribute('data-name') || 'ruangan ini';
                showDeleteConfirmation({
                    message: `Apakah Anda yakin ingin menghapus "${name}" beserta seluruh datanya?`,
                    id: id,
                    type: 'room',
                    onConfirm: function (deleteId) { handleDeleteRoom(deleteId); }
                });
                return;
            }
        });
        window._roomsDocBound = true;
    }

        function handleDeleteRoom(id) {
            fetch(`${APP_URL}/deleteruangan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showAlert('Ruangan berhasil dihapus!', true);
                    const roomRow = document.querySelector(`.room-item[data-id="${id}"]`);
                    if (roomRow) {
                        // Remove the row from the table
                        roomRow.remove();
                    }
                } else {
                    showAlert(res.message, false);
                }
            });
        }

        // ── SEARCH ROOMS ──────────────────────────
        var searchInput = el('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function () {
                var value = this.value.toLowerCase();
                document.querySelectorAll('.room-item').forEach(function (item) {
                    item.style.display = item.textContent.toLowerCase().includes(value) ? '' : 'none';
                });
            });
        }
    }

    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRoomsScript);
    } else {
        initRoomsScript();
    }

})();
