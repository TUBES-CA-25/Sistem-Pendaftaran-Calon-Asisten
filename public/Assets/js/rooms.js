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
                        sessionStorage.setItem('pendingToast', JSON.stringify({ message: 'Ruangan berhasil ditambahkan!', isSuccess: true }));
                        location.reload();
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
                        sessionStorage.setItem('pendingToast', JSON.stringify({ message: 'Nama ruangan diperbarui', isSuccess: true }));
                        location.reload();
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
                    sessionStorage.setItem('pendingToast', JSON.stringify({ message: 'Ruangan berhasil dihapus!', isSuccess: true }));
                    location.reload();
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
