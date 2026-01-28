/**
 * Admin Rooms Management Script
 * Handles room CRUD and participant assignment
 */

(function() {
    const initRoomsScript = function() {
        console.log('Rooms script loaded v2.0 - Read Only');

        let currentRoomId = null;
        let currentRoomName = '';
        let currentType = 'tes_tulis'; // Default type

        // --- NAVIGATION ---
        function showDetailView(id, name) {
            currentRoomId = id;
            currentRoomName = name;

            $('#ruanganListSection').addClass('d-none');
            $('#ruanganDetailSection').removeClass('d-none');

            // Update title
            $('#detailRoomTitle').text(name);

            // Activate Default Tab (Tes Tulis)
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
            $('#ruanganDetailSection').addClass('d-none');
            $('#ruanganListSection').removeClass('d-none');
        }

        $('#backToListBtn').click(showListView);

        // --- CLICKS ---
        // Use event delegation for room cards
        $(document).on('click', '.room-card', function(e) {
            // If clicked on a button/action inside the card, ignore
            if($(e.target).closest('button').length || $(e.target).closest('a').length) return;
            
            showDetailView($(this).data('id'), $(this).data('name'));
        });

        // --- TABS & DATA ---
        // Determine type based on clicked tab
        $('.nav-link').on('shown.bs.tab', function (e) {
            const targetId = e.target.id; // newly activated tab
            
            if(targetId === 'pills-presentasi-tab') currentType = 'presentasi';
            else if(targetId === 'pills-testulis-tab') currentType = 'tes_tulis';
            else if(targetId === 'pills-wawancara-tab') currentType = 'wawancara';
            
            loadParticipants();
        });

        function loadParticipants() {
            if(!currentRoomId) return;
            $('#participantsTableBody').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary speed-fast" role="status"></div></td></tr>');

            $.ajax({
                url: `${APP_URL}/getroomparticipants`,
                type: 'POST',
                data: { id: currentRoomId, type: currentType },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        renderParticipants(res.assigned);
                        updateParticipantCount(res.assigned.length);
                    } else {
                        showAlert('Error: ' + res.message, false);
                    }
                },
                error: function() {
                    $('#participantsTableBody').html('<tr><td colspan="4" class="text-center text-danger py-5">Gagal memuat data. Periksa koneksi.</td></tr>');
                }
            });
        }

        function updateParticipantCount(count) {
            $('#participantCount').text(count);
        }

        function renderParticipants(users) {
            const tbody = $('#participantsTableBody');
            let hasStatusCol = (currentType === 'tes_tulis');

            tbody.empty();

            if (users.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                <h6 class="fw-semibold">Belum ada peserta</h6>
                                <p class="small mb-0">Peserta akan muncul di sini setelah ditambahkan</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            users.forEach((u, index) => {
                let statusBadge = '';
                if(hasStatusCol) {
                    if(u.is_finished == 1) {
                        statusBadge = `<td class="text-center"><span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Selesai</span></td>`;
                    } else {
                        statusBadge = `<td class="text-center"><span class="badge bg-warning px-3 py-2"><i class="bi bi-clock me-1"></i>Pending</span></td>`;
                    }
                } else {
                    statusBadge = `<td class="text-center"><span class="badge bg-info px-3 py-2"><i class="bi bi-person-check me-1"></i>Terdaftar</span></td>`;
                }

                tbody.append(`
                    <tr class="participant-row">
                        <td class="text-center fw-semibold text-muted">${index + 1}</td>
                        <td class="participant-name fw-medium">${u.name || '-'}</td>
                        <td class="participant-stambuk text-muted">${u.stambuk || '-'}</td>
                        ${statusBadge}
                    </tr>
                `);
            });
        }
    
        // Search functionality
        $('#searchParticipants').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.participant-row').each(function() {
                const name = $(this).find('.participant-name').text().toLowerCase();
                const stambuk = $(this).find('.participant-stambuk').text().toLowerCase();
                const matches = name.includes(searchTerm) || stambuk.includes(searchTerm);
                $(this).toggle(matches);
            });
        });
        
        // --- ADD ROOM ---
        $('#tambahRuanganForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: `${APP_URL}/tambahruangan`,
                type: 'POST',
                data: { namaRuangan: $('#namaRuangan').val() },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        sessionStorage.setItem('pendingToast', JSON.stringify({
                             message: 'Ruangan berhasil ditambahkan!',
                             isSuccess: true
                        }));
                        location.reload();
                    }
                    else showAlert(res.message, false);
                }
            });
        });
        
        // --- EDIT ROOM ---
        $('#updateRuanganForm').on('submit', function(e) {
             e.preventDefault();
             const id = $('#updateRuanganId').val();
             const name = $('#updateNamaRuangan').val();
             
            $.ajax({
                url: `${APP_URL}/updateruangan`,
                type: 'POST',
                data: { id: id, namaRuangan: name },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        // Update UI if in detail mode
                        if(currentRoomId == id) {
                             $('#detailRoomTitle').text(name);
                             currentRoomName = name;
                        }
                        sessionStorage.setItem('pendingToast', JSON.stringify({
                             message: 'Nama ruangan diperbarui',
                             isSuccess: true
                        }));
                        location.reload();
                    } else showAlert(res.message, false);
                }
            });
        });

        // --- LIST VIEW ACTIONS ---
        $(document).on('click', '.btn-edit-room', function(e) {
            e.stopPropagation(); // Prevent card click
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            $('#updateRuanganId').val(id);
            $('#updateNamaRuangan').val(name);
            const modal = new bootstrap.Modal(document.getElementById('updateRuanganModal'));
            modal.show();
        });

        $(document).on('click', '.btn-delete-room', function(e) {
            e.stopPropagation(); // Prevent card click
            const id = $(this).data('id');
            
            showConfirmDelete(() => {
                handleDeleteRoom(id);
            }, 'Apakah Anda yakin ingin menghapus ruangan ini beserta seluruh datanya?');
        });
        
        function handleDeleteRoom(id) {
            $.ajax({
                url: `${APP_URL}/deleteruangan`,
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        sessionStorage.setItem('pendingToast', JSON.stringify({
                             message: 'Ruangan berhasil dihapus!',
                             isSuccess: true
                        }));
                        location.reload();
                    } else showAlert(res.message, false);
                }
            });
        }

        // Search
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.room-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
    };

    // Initialize when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRoomsScript);
    } else {
        initRoomsScript();
    }
})();
