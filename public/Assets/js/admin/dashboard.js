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

                // Buka modal langsung (dulu membuat tombol palsu ber-data-bs-* lalu meng-klik-nya)
                UI.modal.open('#editDeadlineModal');
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
                    
                    const updatedJenis = formData.jenis;
                    const updatedDate = formData.tanggal;
                    const editBtn = document.querySelector(`.edit-deadline-btn[data-jenis="${updatedJenis}"]`);
                    if (editBtn) {
                        editBtn.setAttribute('data-date', updatedDate);
                        // Cari span lewat penanda kelasnya, BUKAN previousElementSibling.
                        // Cara lama membuat markup timeline tidak boleh digeser sama
                        // sekali; sedikit penataan ulang saja langsung mematikannya.
                        const span = document.querySelector(`.deadline-date[data-jenis="${updatedJenis}"]`);
                        if (span) {
                            const d = new Date(updatedDate);
                            const options = { day: '2-digit', month: 'short', year: 'numeric' };
                            span.textContent = 'Deadline: ' + d.toLocaleDateString('id-ID', options);
                        }
                    }
                    
                    const modalEl = document.getElementById('editDeadlineModal');
                    if (modalEl) {
                        const modal = UI.modal.ref(modalEl);
                        if (modal) modal.hide();
                    }
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

    // Add Activity Modal — pakai atribut data-modal-open yang dikenali ui.js
    const btnAddActivity = document.getElementById('btnAddActivity');
    if (btnAddActivity) {
        btnAddActivity.setAttribute('data-modal-open', '#addActivityModal');
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
                    updateCalendarView(currentYear, currentMonth);
                    
                    const modalEl = document.getElementById('addActivityModal');
                    if (modalEl) {
                        const modal = UI.modal.ref(modalEl);
                        if (modal) modal.hide();
                        addActivityForm.reset();
                    }
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
        document.getElementById('displayDeskripsi').textContent = event.deskripsi || 'Tidak ada deskripsi.';

        // Set jenis badge
        const jenisEl = document.getElementById('displayJenis');
        if (jenisEl) {
            const jenisColors = {
                'Kegiatan':   'text-blue-500',
                'Wawancara':  'text-amber-500',
                'Presentasi': 'text-emerald-500'
            };
            jenisEl.textContent = event.jenis || '';
            // Add 'hidden' to the class so it doesn't break the layout if used as a hidden data holder
            jenisEl.className = 'hidden text-[11px] font-bold uppercase tracking-wider ' + (jenisColors[event.jenis] || 'text-slate-400');
        }

        const actionsDiv = document.getElementById('calendarActions');
        const manageDiv = document.getElementById('calendarManageAction');
        
        if (actionsDiv) actionsDiv.style.display = 'none';
        if (manageDiv) manageDiv.style.display = 'none';

        if (event.jenis === 'Kegiatan') {
            if (actionsDiv) actionsDiv.style.display = 'grid';
        } else if (['Wawancara', 'Presentasi'].includes(event.jenis)) {
            if (manageDiv) {
                manageDiv.style.display = 'block';
                const manageBtn = document.getElementById('btnManageSchedule');
                if (manageBtn) {
                    let targetUrl = '';
                    if (event.jenis === 'Presentasi') {
                        targetUrl = '/jadwalPresentasi';
                    } else if (event.jenis === 'Wawancara') {
                        if (event.judul && event.judul.includes('Tes Tertulis')) {
                            targetUrl = '/jadwaltes';
                        } else {
                            targetUrl = '/wawancara';
                        }
                    }
                    
                    if (typeof baseUrl === 'undefined') {
                        var baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
                    }
                    manageBtn.onclick = function() {
                        window.location.href = baseUrl + targetUrl;
                    };
                }
            }
        }

        const modal = UI.modal.ref(document.getElementById('activityActionModal'));
        modal.show();
    }

    // Edit Button Handler
    document.getElementById('btnEditActivity').onclick = function() {
        if (!selectedEvent) return;
        
        // Hide action modal
        UI.modal.close(document.getElementById('activityActionModal'));

        document.getElementById('editIdKegiatan').value = selectedEvent.id;
        document.getElementById('editJudulKegiatan').value = selectedEvent.judul;
        document.getElementById('editTanggalKegiatan').value = selectedEvent.tanggal;
        document.getElementById('editDeskripsiKegiatan').value = selectedEvent.deskripsi || '';

        const modal = UI.modal.ref(document.getElementById('editActivityModal'));
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
                updateCalendarView(currentYear, currentMonth);
                
                const modalEl = document.getElementById('activityActionModal');
                if (modalEl) {
                    const modal = UI.modal.ref(modalEl);
                    if (modal) modal.hide();
                }
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
                    updateCalendarView(currentYear, currentMonth);
                    
                    const modalEl = document.getElementById('editActivityModal');
                    if (modalEl) {
                        const modal = UI.modal.ref(modalEl);
                        if (modal) modal.hide();
                        editActivityForm.reset();
                    }
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
    // Catatan: definisi updateDashboardStats() + setInterval-nya DIHAPUS di sini.
    // Sebelumnya fungsi ini didefinisikan dua kali (baris 81 dan 338) dengan isi
    // identik, dan dua setInterval terpasang -> endpoint /dashboard/stats dipanggil
    // 2x setiap 5 detik. Definisi + interval yang dipertahankan ada di atas.

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
