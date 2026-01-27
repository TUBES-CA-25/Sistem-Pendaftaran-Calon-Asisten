/**
 * Admin Rooms Management Script
 * Handles room CRUD and participant assignment
 */

(function() {
    const initRoomsScript = function() {
        console.log('Rooms script loaded v1.1 - Cleaned');
        
        let currentRoomId = null;
        let currentRoomName = '';
        let currentType = 'tes_tulis'; // Default type

        // --- NAVIGATION ---
        function showDetailView(id, name) {
            currentRoomId = id;
            currentRoomName = name;
            
            $('#ruanganListSection').addClass('d-none');
            $('#ruanganDetailSection').removeClass('d-none');
            
            // Update both title elements
            $('#detailRoomTitle').text(name);
            $('#detailRoomTitleAlt').text(name);
            $('#editRoomBtn').data('id', id);
            $('#deleteRoomBtn').data('id', id);
            
            // Activate Default Tab (Tes Tulis)
            // Use bootstrap tab instance if possible, or click mock
            const triggerEl = document.querySelector('#pills-testulis-tab');
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
                // Trigger click logic manually if needed for variable update
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
            $('#participantsTableBody').html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary speed-fast" role="status"></div></td></tr>');
            
            $.ajax({
                url: `${APP_URL}/getroomparticipants`, // Use global APP_URL
                type: 'POST',
                data: { id: currentRoomId, type: currentType },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        renderParticipants(res.assigned);
                    } else {
                        showAlert('Error: ' + res.message, false);
                    }
                },
                error: function() {
                    $('#participantsTableBody').html('<tr><td colspan="5" class="text-center text-danger py-5">Gagal memuat data. Periksa koneksi.</td></tr>');
                }
            });
        }

        function renderParticipants(users) {
            const tbody = $('#participantsTableBody');
            
            let hasStatusCol = (currentType === 'tes_tulis');
            
            // Clean table
            tbody.empty();
            
            // Calculate Stats
            let total = users.length;
            let finished = 0;
            let pending = 0;

            users.forEach(u => {
                if (hasStatusCol) {
                    if (u.is_finished == 1) finished++;
                    else pending++;
                } else {
                    pending++; 
                }
            });

            // Update Stats UI with animation
            animateCount('statTotal', total);
            animateCount('statFinished', finished);
            animateCount('statPending', pending);

            if (users.length === 0) {
                const colSpan = 5;
                tbody.html(`
                    <tr>
                        <td colspan="${colSpan}" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-0">Belum ada peserta</p>
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
                        statusBadge = `<td class="text-center"><span class="badge bg-success">Selesai</span></td>`;
                    } else {
                        statusBadge = `<td class="text-center"><span class="badge bg-warning">Pending</span></td>`;
                    }
                } else {
                    statusBadge = `<td class="text-center"><span class="badge bg-info">Terdaftar</span></td>`;
                }

                // Initial Avatar
                let initial = u.name ? u.name.charAt(0).toUpperCase() : '?';
                let colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                let colorClass = colors[index % colors.length];

                tbody.append(`
                    <tr class="participant-row">
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle ${colorClass} text-white d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                    ${initial}
                                </div>
                                <span class="participant-name">${u.name || '-'}</span>
                            </div>
                        </td>
                        <td class="participant-stambuk">${u.stambuk || '-'}</td>
                         ${statusBadge}
                    </tr>
                `);
            });
    }

    // Helper for number animation
    function animateCount(id, target) {
        let current = 0;
        let element = $('#' + id);
        element.text('0');
        
        if(target === 0) return;

        let step = Math.ceil(target / 20);
        let timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.text(current);
        }, 30);
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

        // --- EDIT/DELETE ROOM (From Detail View) ---
        $('#editRoomBtn').on('click', function() {
            $('#updateRuanganId').val(currentRoomId);
            $('#updateNamaRuangan').val(currentRoomName);
            new bootstrap.Modal(document.getElementById('updateRuanganModal')).show();
        });

        $('#deleteRoomBtn').on('click', function() {
            showConfirmDelete(() => {
                handleDeleteRoom(currentRoomId);
            }, 'Apakah Anda yakin ingin menghapus ruangan ini beserta seluruh datanya?');
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
