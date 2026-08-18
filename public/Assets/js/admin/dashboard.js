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
                    
                    const modalEl = document.getElementById('editDeadlineModal');
                    if (modalEl) {
                        const modal = UI.modal.ref(modalEl);
                        if (modal) modal.hide();
                    }

                    /* Muat ulang halaman setelah deadline disimpan.
                    
                       Sebelumnya JS hanya menulis ulang TEKS TANGGAL-nya saja,
                       sedangkan status tiap tahap (Selesai / Sedang Berlangsung /
                       Akan Datang), warna kartu, titik penanda, dan persentase
                       progres semuanya dihitung di server dari perbandingan
                       deadline vs hari ini - dan bergantung berantai antar-tahap.
                    
                       Akibatnya, mengubah satu deadline membuat timeline
                       menampilkan tanggal baru tetapi status LAMA, sampai halaman
                       dimuat ulang secara manual. Menghitung ulang seluruh rantai
                       itu di sisi klien berarti menduplikasi logika server, jadi
                       lebih tepat meminta server merendernya kembali. */
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
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
    let currentEventsList = [];
    let currentEventIndex = 0;

    window.showActivityActions = function(eventsJson) {
        let events = [];
        try {
            events = JSON.parse(eventsJson);
        } catch(e) {
            console.error("Invalid events data");
            return;
        }
        
        if (!events || events.length === 0) return;
        
        currentEventsList = events;
        currentEventIndex = 0;
        
        renderEventModal();

        const modal = UI.modal.ref(document.getElementById('activityActionModal'));
        modal.show();
    }

    function renderEventModal() {
        const event = currentEventsList[currentEventIndex];
        selectedEvent = event;

        // Tampilkan/sembunyikan panah navigasi jika ada lebih dari 1 acara
        const navEl = document.getElementById('eventNavigation');
        const countEl = document.getElementById('eventCounter');
        if (currentEventsList.length > 1) {
            if (navEl) navEl.classList.remove('hidden');
            if (countEl) countEl.textContent = (currentEventIndex + 1) + '/' + currentEventsList.length;
        } else {
            if (navEl) navEl.classList.add('hidden');
        }

        document.getElementById('displayJudul').textContent = event.judul;
        document.getElementById('displayTanggal').textContent = new Date(event.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('displayDeskripsi').textContent = event.deskripsi || 'Tidak ada deskripsi.';

        // Set dynamic content layout (icons and box colors) per event type
        const jenis = event.jenis || 'Kegiatan';
        const displayIcon = document.getElementById('displayIcon');
        const descBox = document.getElementById('descBox');
        const dateBox = document.getElementById('dateBox');
        const dateLabel = document.getElementById('dateLabel');
        const dateIcon = document.getElementById('dateIcon');
        const displayTanggal = document.getElementById('displayTanggal');
        const jenisBox = document.getElementById('jenisBox');
        const jenisLabel = document.getElementById('jenisLabel');
        const jenisIcon = document.getElementById('jenisIcon');
        const displayJenisLengkap = document.getElementById('displayJenisLengkap');
        const watermarkIcon = document.getElementById('watermarkIcon');
        const descContainer = document.getElementById('descContainer');

        if (descBox && dateBox && jenisBox) {
            // Semua box menggunakan warna biru gradasi/soft biru yang elegan dengan sedikit efek depth (tidak datar)
            descBox.className = "bg-gradient-to-br from-blue-50/50 to-indigo-50/50 rounded-xl p-4 border border-blue-100/60 shadow-[inset_0_2px_10px_rgba(59,130,246,0.04)] transition-colors duration-300 min-h-[80px]";
            descBox.querySelector('p').className = "text-[13px] text-slate-700 leading-relaxed whitespace-pre-wrap m-0 transition-colors duration-300 font-medium";
            
            dateBox.className = "bg-[#E6F0FF] rounded-xl p-3.5 border border-transparent transition-colors duration-300";
            dateLabel.className = "text-[10px] text-blue-500/80 font-bold uppercase tracking-wider block mb-1 transition-colors duration-300";
            dateIcon.className = "bx bx-calendar text-blue-600 text-lg transition-colors duration-300";
            displayTanggal.className = "text-[13px] font-bold text-blue-700 transition-colors duration-300";

            jenisBox.className = "bg-blue-50 rounded-xl p-3.5 border border-blue-100 transition-colors duration-300";
            jenisLabel.className = "text-[10px] text-blue-400 font-bold uppercase tracking-wider block mb-1 transition-colors duration-300";
            jenisIcon.className = "bx bx-tag text-blue-500 text-lg transition-colors duration-300";
            displayJenisLengkap.className = "text-[13px] font-bold text-blue-800 transition-colors duration-300";

            // Sembunyikan box deskripsi secara default (nanti dimunculkan hanya untuk Kegiatan)
            if (descContainer) {
                descContainer.style.display = 'none';
            }

            // Atur icon & watermark berdasarkan jenis kegiatan (tanpa ubah warna)
            if (jenis.toLowerCase().includes('tes tertulis')) {
                if (displayIcon) displayIcon.className = "bi bi-journal-text text-xl";
                if (watermarkIcon) watermarkIcon.className = "bi bi-journal-text absolute -right-4 -top-4 text-9xl opacity-[0.03] rotate-[-15deg] transition-all duration-500 pointer-events-none text-blue-900";
            } else if (jenis.toLowerCase().includes('wawancara')) {
                if (displayIcon) displayIcon.className = "bi bi-people text-xl";
                if (watermarkIcon) watermarkIcon.className = "bi bi-people absolute -right-4 -top-4 text-9xl opacity-[0.03] rotate-[-15deg] transition-all duration-500 pointer-events-none text-blue-900";
            } else if (jenis.toLowerCase().includes('presentasi')) {
                if (displayIcon) displayIcon.className = "bi bi-easel text-xl";
                if (watermarkIcon) watermarkIcon.className = "bi bi-easel absolute -right-4 -top-4 text-9xl opacity-[0.03] rotate-[-15deg] transition-all duration-500 pointer-events-none text-blue-900";
            } else {
                if (displayIcon) displayIcon.className = "bi bi-calendar-event text-xl";
                if (watermarkIcon) watermarkIcon.className = "bi bi-calendar-event absolute -right-4 -top-4 text-9xl opacity-[0.03] rotate-[-15deg] transition-all duration-500 pointer-events-none text-blue-900";
                
                // Kegiatan biasa memunculkan box deskripsi
                if (descContainer) {
                    descContainer.style.display = 'block';
                }
            }
        }

        if (displayJenisLengkap) {
            displayJenisLengkap.textContent = jenis;
        }

        const actionsDiv = document.getElementById('calendarActions');
        const manageDiv = document.getElementById('calendarManageAction');
        
        if (actionsDiv) actionsDiv.style.display = 'none';
        if (manageDiv) manageDiv.style.display = 'none';

        if (event.jenis === 'Kegiatan') {
            if (actionsDiv) actionsDiv.style.display = 'grid';
        } else if (['Wawancara', 'Presentasi', 'Tes Tertulis'].includes(event.jenis)) {
            if (manageDiv) {
                manageDiv.style.display = 'block';
                const manageBtn = document.getElementById('btnManageSchedule');
                if (manageBtn) {
                    let targetUrl = '';
                    if (event.jenis === 'Presentasi') {
                        targetUrl = '/jadwalPresentasi';
                    } else if (event.jenis === 'Wawancara') {
                        targetUrl = '/wawancara';
                    } else if (event.jenis === 'Tes Tertulis') {
                        targetUrl = '/jadwaltes';
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
    }

    // Event listeners untuk panah navigasi slider kegiatan
    const btnPrevEvent = document.getElementById('btnPrevEvent');
    if (btnPrevEvent) {
        btnPrevEvent.addEventListener('click', function() {
            if (currentEventIndex > 0) {
                currentEventIndex--;
            } else {
                currentEventIndex = currentEventsList.length - 1; // loop ke akhir
            }
            renderEventModal();
        });
    }

    const btnNextEvent = document.getElementById('btnNextEvent');
    if (btnNextEvent) {
        btnNextEvent.addEventListener('click', function() {
            if (currentEventIndex < currentEventsList.length - 1) {
                currentEventIndex++;
            } else {
                currentEventIndex = 0; // loop ke awal
            }
            renderEventModal();
        });
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

        /* Minta konfirmasi dulu. Sebelumnya kegiatan langsung terhapus begitu
           tombol diklik - tanpa peringatan dan tanpa cara membatalkan, padahal
           penghapusannya permanen. Semua aksi hapus lain di aplikasi ini sudah
           memakai showConfirmDelete; hanya yang ini terlewat. */
        const judulKegiatan = selectedEvent.judul || 'kegiatan ini';
        showConfirmDelete(function () {
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
        }, `Hapus kegiatan "${judulKegiatan}"? Tindakan ini tidak dapat dibatalkan.`);
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
