(function() {
    // Bootstrap Modal instance for deadline editing
    // Edit Deadline Logic - Use event delegation for dynamically loaded content
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.edit-deadline-btn')) {
            const btn = e.target.closest('.edit-deadline-btn');
            const jenis = btn.getAttribute('data-jenis');
            const label = btn.getAttribute('data-label');
            const date = btn.getAttribute('data-date');

            const jenisInput = document.getElementById('editDeadlineJenis');
            const labelEl = document.getElementById('editDeadlineLabelName');
            const dateInput = document.getElementById('editDeadlineDate');

            if (jenisInput && labelEl && dateInput) {
                jenisInput.value = jenis;
                labelEl.textContent = label;
                dateInput.value = date;

                // Trigger modal using data attribute
                const modalTrigger = document.createElement('button');
                modalTrigger.setAttribute('data-bs-toggle', 'modal');
                modalTrigger.setAttribute('data-bs-target', '#editDeadlineModal');
                modalTrigger.style.display = 'none';
                document.body.appendChild(modalTrigger);
                modalTrigger.click();
                document.body.removeChild(modalTrigger);
            }
        }
    });

    // Handle Edit Deadline Submit
    const editDeadlineForm = document.getElementById('editDeadlineForm');
    if (editDeadlineForm) {
        editDeadlineForm.onsubmit = function(e) {
            e.preventDefault();

            const formData = {
                jenis: document.getElementById('editDeadlineJenis').value,
                tanggal: document.getElementById('editDeadlineDate').value
            };

            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }

            fetch(`${baseUrl}/updatedeadline`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Deadline berhasil diperbarui!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        };
    }

    // Real-time Stats Polling
    function updateDashboardStats() {
        if (typeof baseUrl === 'undefined') {
            var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
        }

        const statTotal = document.getElementById('stat-total');
        const statLulus = document.getElementById('stat-lulus');
        const statPending = document.getElementById('stat-pending');
        const statGagal = document.getElementById('stat-gagal');

        if (!statTotal || !statLulus || !statPending || !statGagal) {
            // Console warn suppressed to avoid noise
            return;
        }

        fetch(`${baseUrl}/dashboard/stats`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                statTotal.innerText = data.total;
                statLulus.innerText = data.lulus;
                statPending.innerText = data.pending;
                statGagal.innerText = data.gagal;
            }
        })
        .catch(console.error);
    }

    // Only start interval if stats elements exist
    if (document.getElementById('stat-total')) {
        setInterval(updateDashboardStats, 5000);
    }

    // --- CALENDAR LOGIC ---

    // Add Activity Modal - Use data-bs-toggle instead of Bootstrap object
    const btnAddActivity = document.getElementById('btnAddActivity');
    if (btnAddActivity) {
        btnAddActivity.setAttribute('data-bs-toggle', 'modal');
        btnAddActivity.setAttribute('data-bs-target', '#addActivityModal');
    }

    // Handle Add Activity Submit
    const addActivityForm = document.getElementById('addActivityForm');
    if (addActivityForm) {
        addActivityForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = {
                judul: document.getElementById('judulKegiatan').value,
                tanggal: document.getElementById('tanggalKegiatan').value,
                deskripsi: document.getElementById('deskripsiKegiatan').value
            };
            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }
            fetch(`${baseUrl}/addkegiatan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Kegiatan berhasil ditambahkan!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        };
    }

    // Calendar Data & Functions
    // eventsData is expected to be defined in global scope (window) or before script load
    const eventsData = window.eventsData || [];
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();

    // Click tracker for activities
    let selectedEvent = null;

    function generateCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();
        const adjustedStart = startDay === 0 ? 6 : startDay - 1; // Mon=0, Sun=6

        const calendarBody = document.getElementById('calendarBody');
        if (!calendarBody) return;

        calendarBody.innerHTML = '';

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const currentMonthEl = document.getElementById('currentMonth');
        if (currentMonthEl) {
            currentMonthEl.textContent = `${monthNames[month]} ${year}`;
        }

        let date = 1;
        // 6 rows max to cover all weeks
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            let hasDateInRow = false;

            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                // Apply inline styling for clean look with borders
                cell.style.height = '70px'; 
                cell.style.verticalAlign = 'middle';
                cell.style.border = '1px solid #E5E7EB';
                cell.style.position = 'relative';
                cell.style.padding = '12px';
                cell.className = 'text-dark';
                
                if (i === 0 && j < adjustedStart) {
                    cell.textContent = '';
                } else if (date > daysInMonth) {
                    cell.textContent = '';
                } else {
                    // Create date number
                    const dateSpan = document.createElement('div');
                    dateSpan.textContent = date;
                    dateSpan.style.fontSize = '14px';
                    dateSpan.style.fontWeight = '500';
                    dateSpan.style.color = '#1F2937'; // Dark gray/black color
                    
                    hasDateInRow = true;
                    
                    const monthPlus1 = String(month + 1).padStart(2, '0');
                    const datePad = String(date).padStart(2, '0');
                    const dateStr = `${year}-${monthPlus1}-${datePad}`;

                    const daysEvents = eventsData.filter(e => e.tanggal === dateStr);

                    // Check if this is today
                    const today = new Date();
                    const isToday = date === today.getDate() && month === today.getMonth() && year === today.getFullYear();

                    if (daysEvents.length > 0) {
                        cell.style.cursor = 'pointer';
                        cell.onclick = function() {
                            showActivityActions(daysEvents);
                        };

                        // Event date styling - light blue background
                        cell.style.backgroundColor = '#E0E7FF';
                        cell.style.borderRadius = '8px';
                        dateSpan.style.fontWeight = '600';
                        
                        // Add red dot below the date
                        const dot = document.createElement('div');
                        dot.style.width = '6px';
                        dot.style.height = '6px';
                        dot.style.backgroundColor = '#DC2626';
                        dot.style.borderRadius = '50%';
                        dot.style.margin = '4px auto 0';
                        
                        cell.appendChild(dateSpan);
                        cell.appendChild(dot);
                    } else if (isToday) {
                        // Today's date - blue border
                        cell.style.border = '2px solid #2563EB';
                        cell.style.borderRadius = '8px';
                        dateSpan.style.fontWeight = '700';
                        cell.appendChild(dateSpan);
                    } else {
                        // Regular date
                        cell.appendChild(dateSpan);
                    }

                    date++;
                }
                row.appendChild(cell);
            }
            if (hasDateInRow || i === 0) { 
                calendarBody.appendChild(row);
            }
            if (date > daysInMonth) break;
        }
    }

    function showActivityActions(events) {
        // For simplicity, we handle the first event of the day if multiple exist
        const event = events[0];
        selectedEvent = event;

        document.getElementById('displayJudul').textContent = event.judul;
        document.getElementById('displayTanggal').textContent = new Date(event.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('displayDeskripsi').textContent = event.deskripsi || 'Tidak ada deskripsi';
        
        const actionsDiv = document.getElementById('calendarActions');
        if (event.jenis === 'Kegiatan') {
            actionsDiv.style.display = 'block';
        } else {
            actionsDiv.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('activityActionModal'));
        modal.show();
    }

    // Edit Button Handler
    document.getElementById('btnEditActivity').onclick = function() {
        if (!selectedEvent) return;
        
        // Hide action modal
        bootstrap.Modal.getInstance(document.getElementById('activityActionModal')).hide();

        document.getElementById('editIdKegiatan').value = selectedEvent.id;
        document.getElementById('editJudulKegiatan').value = selectedEvent.judul;
        document.getElementById('editTanggalKegiatan').value = selectedEvent.tanggal;
        document.getElementById('editDeskripsiKegiatan').value = selectedEvent.deskripsi || '';

        const modal = new bootstrap.Modal(document.getElementById('editActivityModal'));
        modal.show();
    };

    // Delete Button Handler
    document.getElementById('btnDeleteActivity').onclick = function() {
        if (!selectedEvent) return;

        if (typeof baseUrl === 'undefined') {
            var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
        }
        fetch(`${baseUrl}/deletekegiatan`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: selectedEvent.id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Hide Modal First to prevent stacking
                const modalEl = document.getElementById('activityActionModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                
                // Real-time Update: Remove from local data
                // eventsData is a reference to window.eventsData
                const index = eventsData.findIndex(e => e.id == selectedEvent.id);
                if (index > -1) {
                    eventsData.splice(index, 1);
                }
                
                // Re-render calendar
                generateCalendar(currentYear, currentMonth);

                showAlert('Kegiatan berhasil dihapus!', true);
                // No location.reload() needed
            } else {
                showAlert('Gagal: ' + data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan sistem', false);
        });
    };

    // Handle Edit Activity Submit
    const editActivityForm = document.getElementById('editActivityForm');
    if (editActivityForm) {
        editActivityForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = {
                id: document.getElementById('editIdKegiatan').value,
                judul: document.getElementById('editJudulKegiatan').value,
                tanggal: document.getElementById('editTanggalKegiatan').value,
                deskripsi: document.getElementById('editDeskripsiKegiatan').value
            };
            if (typeof baseUrl === 'undefined') {
                var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
            }
            fetch(`${baseUrl}/updatekegiatan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Kegiatan berhasil diperbarui!', true);
                    location.reload();
                } else {
                    showAlert('Gagal: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', false);
            });
        };
    }

    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    if (prevMonthBtn) {
        prevMonthBtn.onclick = function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentYear, currentMonth);
        };
    }

    if (nextMonthBtn) {
        nextMonthBtn.onclick = function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentYear, currentMonth);
        };
    }

    // Init
    generateCalendar(currentYear, currentMonth);

})();
