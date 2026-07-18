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

    window.showActivityActions = function(eventsJson) {
        let events = [];
        try {
            events = JSON.parse(eventsJson);
        } catch(e) {
            console.error("Invalid events data");
            return;
        }
        
        if (!events || events.length === 0) return;
        
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
                showAlert('Kegiatan berhasil dihapus!', true);
                setTimeout(() => location.reload(), 1000);
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



    function updateCalendarView(year, month) {
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const currentMonthEl = document.getElementById('currentMonth');
        if (currentMonthEl) {
            currentMonthEl.textContent = `${monthNames[month]} ${year}`;
        }
        
        if (typeof baseUrl === 'undefined') {
            var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
        }
        
        fetch(`${baseUrl}/getadminactivities`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ year: year, month: month + 1 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const calendarBody = document.getElementById('calendarBody');
                if (calendarBody && data.calendarHtml) {
                    calendarBody.innerHTML = data.calendarHtml;
                }
            }
        })
        .catch(console.error);
    }

    const prevMonthBtn = document.getElementById('prevMonth');
    if (prevMonthBtn) {
        prevMonthBtn.onclick = function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            updateCalendarView(currentYear, currentMonth);
        };
    }

    const nextMonthBtn = document.getElementById('nextMonth');
    if (nextMonthBtn) {
        nextMonthBtn.onclick = function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            updateCalendarView(currentYear, currentMonth);
        };
    }

    // Removed generateCalendar(currentYear, currentMonth);
    // Calendar is rendered initially by PHP

})();
