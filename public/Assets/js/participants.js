
// Universal wrapper for AJAX and Direct Load
(function() {
    const initDaftarPesertaScript = function() {
        console.log('Daftar Peserta script loaded');
        
        // Link custom search input
        $('#searchPeserta').off('input').on('input', function() {
            var val = $(this).val().toLowerCase();
            $('#daftarPesertaTable tbody tr').each(function() {
                var row = $(this);
                var name = row.find('td:nth-child(2)').text().toLowerCase();
                var stambuk = row.find('td:nth-child(3)').text().toLowerCase();
                var jurusan = row.find('td:nth-child(4)').text().toLowerCase();
                var status = row.find('td:nth-child(5)').text().toLowerCase();
                
                if (name.indexOf(val) > -1 || stambuk.indexOf(val) > -1 || jurusan.indexOf(val) > -1 || status.indexOf(val) > -1) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });

    // Store current row data
    var currentRowData = null;

    // Clean up existing handlers first to prevent double-binding
    $(document).off('click', '.btn-view');
    $(document).off('click', '.btn-delete');
    $(document).off('click', '.btn-reminder');
    $(document).off('click', '#btnSendMessageToUser');
    $(document).off('click', '#sendIndividualMessage');
    $(document).off('click', '.btn-download-berkas, #downloadMakalahButton, #downloadPptButton');
    $(document).off('click', '.tab-btn');
    // sad
    // ============================================
    // SEND MESSAGE FUNCTIONS (Inside jQuery Ready)
    // ============================================
    $(document).on('click', '#btnSendMessageToUser', function() {
        console.log('Open message modal clicked');
        var mahasiswaId = document.getElementById('modalMahasiswaId').value;
        var nama = document.getElementById('modalNama').textContent;
        
        if (!mahasiswaId) {
            showAlert('ID Peserta tidak valid.', false);
            return;
        }

        // Use Bootstrap Modal instance properly
        var detailModalEl = document.getElementById('detailModal');
        var detailModal = bootstrap.Modal.getInstance(detailModalEl);
        if (detailModal) {
            detailModal.hide();
        }

        // Wait shortly for modal transition
        setTimeout(function() {
            document.getElementById('messageRecipient').textContent = nama;
            document.getElementById('messageMahasiswaId').value = mahasiswaId;
            document.getElementById('individualMessage').value = '';
            
            var msgModal = new bootstrap.Modal(document.getElementById('sendMessageModal'));
            msgModal.show();
        }, 300);
    });

    $(document).on('click', '#sendIndividualMessage', function() {
        var btn = $(this);
        var mahasiswaId = document.getElementById('messageMahasiswaId').value;
        var message = document.getElementById('individualMessage').value;

        if (!message || message.trim() === '') {
            showAlert('Pesan tidak boleh kosong.', false);
            return;
        }

        // UI Feedback
        var originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');

        fetch(`${APP_URL}/notification`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${mahasiswaId}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                var msgModal = bootstrap.Modal.getInstance(document.getElementById('sendMessageModal'));
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
            btn.prop('disabled', false).html(originalText);
        });
    });
    
    // Handle view detail button click (Event Delegation)
    $(document).on('click', '.btn-view', function() {
        try {
            var data = this.dataset;
            
            // Store mahasiswa ID for accept button
            document.getElementById('modalMahasiswaId').value = data.id;
            document.getElementById('modalUserId').value = data.userid;
            currentRowData = {
                id: data.id,
                userId: data.userid,
                nama: data.nama,
                stambuk: data.stambuk
            };

            // BASE URL ADJUSTMENT: 'res' is at project root, not inside 'public'
            // If APP_URL ends with '/public', strip it to get project root
            const PROJECT_ROOT = APP_URL.replace(/\/public$/, '');
            
            // Get Image Element
            var modalFoto = document.getElementById('modalFoto');
            
            // RESET Image immediately to default to avoid showing previous user's image
            // This prevents the "glitch" where the old image persists while the new one loads
            if (modalFoto) {
                modalFoto.src = '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';
            }

            // Populate header
            document.getElementById('modalNamaHeader').textContent = data.nama || '-';
            document.getElementById('modalStambukHeader').textContent = data.stambuk || '-';
            
            // Set photo
            var fotoPath = data.foto ? `${PROJECT_ROOT}/res/imageUser/${data.foto}` : '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';

            if (modalFoto) {
                modalFoto.src = fotoPath;
                modalFoto.onerror = function() {
                    this.src = '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';
                };
            }
            
            // Populate modal fields
            document.getElementById('modalNama').textContent = data.nama || '-';
            document.getElementById('modalStambuk').textContent = data.stambuk || '-';
            document.getElementById('modalJurusan').textContent = data.jurusan || '-';
            document.getElementById('modalJurusan').title = data.jurusan || '-';
            document.getElementById('modalKelas').textContent = data.kelas || '-';
            document.getElementById('modalAlamat').textContent = data.alamat || '-';
            document.getElementById('modalTempat_lahir').textContent = data.tempat_lahir || '-';
            document.getElementById('modalTanggal_lahir').textContent = data.tanggal_lahir || '-';
            document.getElementById('modalJenis_kelamin').textContent = data.jenis_kelamin || '-';
            document.getElementById('modalJenisKelaminDetail').textContent = data.jenis_kelamin || '-';
            document.getElementById('modalNoTelp').textContent = data.notelp || '-';

            // Populate tab fields if exist
            if (document.getElementById('modalJurusanTab')) {
                document.getElementById('modalJurusanTab').textContent = data.jurusan || '-';
            }
            if (document.getElementById('modalKelasTab')) {
                document.getElementById('modalKelasTab').textContent = data.kelas || '-';
            }
            if (document.getElementById('modalNoTelpTab')) {
                document.getElementById('modalNoTelpTab').textContent = data.notelp || '-';
            }

            // Reset tab visibility when opening modal
            $('.tab-btn').removeClass('active text-blue-600 border-blue-600').addClass('text-slate-500 border-transparent');
            $('.tab-btn[data-tab="tab-profil"]').addClass('active text-blue-600 border-blue-600').removeClass('text-slate-500 border-transparent');
            $('.tab-panel').addClass('hidden');
            $('#tab-profil').removeClass('hidden');
            
            // Judul Presentasi
            var judulPresentasi = data.judul_presentasi;
            var presentasiSection = document.getElementById('presentasiSection');
            var noPresentasiFiles = document.getElementById('noPresentasiFiles');
            
            var judulPresentasiEl = document.getElementById('modalJudulPresentasi');
            if (judulPresentasiEl) {
                if (judulPresentasi && judulPresentasi.trim() !== '') {
                    judulPresentasiEl.textContent = judulPresentasi;
                    judulPresentasiEl.classList.remove('text-muted', 'fst-italic');
                } else {
                    judulPresentasiEl.textContent = 'Belum diisi oleh peserta';
                    judulPresentasiEl.classList.add('text-muted', 'fst-italic');
                }
            }
            
            if (presentasiSection) {
                presentasiSection.style.display = 'block';
            }
            
            var statusBadge = document.getElementById('modalStatusBadge');
            var statusBadgeIcon = document.getElementById('modalStatusBadgeIcon');
            var statusBadgeText = document.getElementById('modalStatusBadgeText');
            
            var statusIcon = document.getElementById('modalStatusIcon');
            var statusIconInner = document.getElementById('modalStatusIconInner');
            
            var berkasAccepted = data.berkas_accepted;
            
            // Get button elements
            var btnVerifikasi = document.getElementById('btnVerifikasiModal');
            var btnBatalkan = document.getElementById('btnBatalkanModal');
            var btnTerima = document.getElementById('btnTerimaModal');
            var btnTolak = document.getElementById('btnTolakModal');
            
            if (!btnVerifikasi || !btnBatalkan) return;
            
            // RESET button states
            btnVerifikasi.style.display = 'none';
            btnBatalkan.style.display = 'none';
            if (btnTerima) btnTerima.style.display = 'none';
            if (btnTolak) btnTolak.style.display = 'none';
            
            if (berkasAccepted == '1') {
                statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-emerald-500 text-white';
                if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-check-circle me-1';
                if (statusBadgeText) statusBadgeText.textContent = 'Berkas Terverifikasi';
                
                statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-emerald-500 border-2 border-white';
                if (statusIconInner) statusIconInner.className = 'bi bi-check-lg';
                btnBatalkan.style.display = 'inline-block';
            } else if (berkasAccepted == '0') {
                statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-blue-500 text-white';
                if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-hourglass-split me-1';
                if (statusBadgeText) statusBadgeText.textContent = 'Menunggu Verifikasi';
                
                statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-blue-500 border-2 border-white';
                if (statusIconInner) statusIconInner.className = 'bi bi-clock';
                btnVerifikasi.style.display = 'inline-block';
                btnVerifikasi.disabled = false;
            } else {
                statusBadge.className = 'inline-block rounded-full px-3 py-1 text-[10px] font-semibold bg-slate-500 text-white';
                if (statusBadgeIcon) statusBadgeIcon.className = 'bi bi-file-earmark-x me-1';
                if (statusBadgeText) statusBadgeText.textContent = 'Belum Upload Berkas';
                
                statusIcon.className = 'absolute bottom-0 right-0 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-[10px] text-white font-bold bg-slate-500 border-2 border-white';
                if (statusIconInner) statusIconInner.className = 'bi bi-x-lg';
                if (btnTerima) btnTerima.style.display = 'inline-block';
                if (btnTolak) btnTolak.style.display = 'inline-block';
            }
            
            // Download Buttons
            const downloads = {
                'downloadFotoButton': data.foto ? `${PROJECT_ROOT}/res/imageUser/${data.foto}` : '',
                'downloadCVButton': data.cv ? `${PROJECT_ROOT}/res/berkasUser/${data.cv}` : '',
                'downloadTranskripButton': data.transkrip ? `${PROJECT_ROOT}/res/berkasUser/${data.transkrip}` : '',
                'downloadSuratButton': data.surat ? `${PROJECT_ROOT}/res/berkasUser/${data.surat}` : ''
            };
            
            for (const [id, url] of Object.entries(downloads)) {
                const btn = document.getElementById(id);
                if (btn) btn.setAttribute('data-download-url', url);
            }
            
            // Presentasi Files
            var makalahBtn = document.getElementById('downloadMakalahButton');
            var pptBtn = document.getElementById('downloadPptButton');
            var hasPresentasiFiles = false;
            
            if (makalahBtn) {
                if (data.makalah) {
                    makalahBtn.setAttribute('data-download-url', '/Sistem-Pendaftaran-Calon-Asisten/res/makalahUser/' + data.makalah);
                    makalahBtn.style.display = 'inline-flex';
                    hasPresentasiFiles = true;
                } else {
                    makalahBtn.style.display = 'none';
                }
            }
            
            if (pptBtn) {
                if (data.ppt) {
                    pptBtn.setAttribute('data-download-url', '/Sistem-Pendaftaran-Calon-Asisten/res/pptUser/' + data.ppt);
                    pptBtn.style.display = 'inline-flex';
                    hasPresentasiFiles = true;
                } else {
                    pptBtn.style.display = 'none';
                }
            }
            
            if (noPresentasiFiles) {
                noPresentasiFiles.style.display = hasPresentasiFiles ? 'none' : 'inline-block';
            }
            
            // Show modal
            var detailModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal'));
            detailModal.show();
            
        } catch (error) {
            console.error('Error opening detail modal:', error);
            showAlert('Terjadi kesalahan saat membuka detail peserta: ' + error.message, false);
        }
    });

    // Tab switching handler
    $(document).on('click', '.tab-btn', function() {
        var tabId = $(this).data('tab');
        
        $('.tab-btn').removeClass('active text-blue-600 border-blue-600').addClass('text-slate-500 border-transparent');
        $(this).addClass('active text-blue-600 border-blue-600').removeClass('text-slate-500 border-transparent');
        
        $('.tab-panel').addClass('hidden');
        $('#' + tabId).removeClass('hidden');
    });

    // Handle download button click (Event Delegation)
    $(document).on('click', '.btn-download-berkas, #downloadMakalahButton, #downloadPptButton', function() {
        var url = $(this).attr('data-download-url');
        if (url && url.trim() !== '') {
            window.open(url, '_blank');
        } else {
            showAlert('File tidak tersedia', false);
        }
    });

    // Handle delete button click (Event Delegation)
    $(document).on('click', '.btn-delete', function() {
        var row = $(this).closest('tr');
        var userId = row.attr('data-userid'); 
        var mhsId = row.attr('data-id');
        
        // Determine which ID to use
        if (!userId && !mhsId) {
            showAlert('ID data tidak ditemukan', false);
            return;
        }
        
        // Show delete confirmation modal
        showDeleteConfirmation({
            message: 'Apakah Anda yakin ingin menghapus data peserta ini?',
            id: userId || mhsId,
            type: userId ? 'user' : 'mahasiswa',
            onConfirm: function(id, type) {
                var bodyParams = type === 'user' ? 'id=' + id : 'mahasiswaId=' + id;
                
                fetch(`${APP_URL}/deletemahasiswa`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: bodyParams
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Data berhasil dihapus!', true);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat menghapus data', false);
                });
            }
        });
    });

    // ============ NOTIFICATION FORM HANDLERS ============
    var selectedMahasiswa = [];
    
    // Update selected count
    function updateSelectedCount() {
        document.getElementById('selectedCount').textContent = selectedMahasiswa.length;
    }
    
    // Render selected mahasiswa list
    function renderSelectedMahasiswa() {
        var list = document.getElementById('selectedMahasiswaList');
        list.innerHTML = '';
        
        if (selectedMahasiswa.length === 0) {
            var emptyLi = document.createElement('li');
            emptyLi.className = 'list-group-item text-muted text-center py-3';
            var emptyIcon = document.createElement('i');
            emptyIcon.className = 'bi bi-inbox me-1';
            emptyLi.appendChild(emptyIcon);
            emptyLi.appendChild(document.createTextNode('Belum ada peserta dipilih'));
            list.appendChild(emptyLi);
        } else {
            selectedMahasiswa.forEach(function(mhs, index) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                
                var span = document.createElement('span');
                span.className = 'small';
                span.textContent = mhs.text;
                
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-outline-danger';
                btn.dataset.index = index;
                
                var icon = document.createElement('i');
                icon.className = 'bi bi-x';
                btn.appendChild(icon);
                
                li.appendChild(span);
                li.appendChild(btn);
                list.appendChild(li);
            });
            
            // Add remove handlers
            list.querySelectorAll('button').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var idx = parseInt(this.dataset.index);
                    selectedMahasiswa.splice(idx, 1);
                    renderSelectedMahasiswa();
                    updateSelectedCount();
                });
            });
        }
        updateSelectedCount();
    }
    
    // Add single mahasiswa - Using jQuery off/on to prevent duplicates
    $('#addMahasiswaButton').off('click').on('click', function() {
        var select = document.getElementById('mahasiswa');
        var selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            var exists = selectedMahasiswa.some(function(m) { return m.id === selectedOption.value; });
            if (!exists) {
                selectedMahasiswa.push({
                    id: selectedOption.value,
                    text: selectedOption.textContent.trim()
                });
                renderSelectedMahasiswa();
            } else {
                showAlert('Peserta sudah dipilih', false);
            }
        } else {
            showAlert('Pilih peserta terlebih dahulu', false);
        }
    });
    
    // Add all mahasiswa - Using jQuery off/on to prevent duplicates
    $('#addAllMahasiswaButton').off('click').on('click', function() {
        var select = document.getElementById('mahasiswa');
        selectedMahasiswa = [];
        
        Array.from(select.options).forEach(function(option) {
            if (option.value) {
                selectedMahasiswa.push({
                    id: option.value,
                    text: option.textContent.trim()
                });
            }
        });
        renderSelectedMahasiswa();
    });
    
    // Submit notification form - Broadcast Mode
    $('#addNotificationForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        var message = document.getElementById('notifMessage').value;
        var btnSubmit = document.querySelector('button[form="addNotificationForm"]');
        var originalText = btnSubmit.innerHTML;
        
        if (!message.trim()) {
            showAlert('Pesan tidak boleh kosong', false);
            return;
        }
        
        // Auto-select ALL students for broadcast
        var select = document.getElementById('mahasiswa');
        var mahasiswaIds = [];
        
        Array.from(select.options).forEach(function(option) {
            if (option.value) {
                mahasiswaIds.push(option.value);
            }
        });

        if (mahasiswaIds.length === 0) {
            showAlert('Tidak ada peserta yang terdaftar untuk dikirimi notifikasi.', false);
            return;
        }

        // Disable button and show loading state
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim Broadcast...';
        
        fetch(`${APP_URL}/addallnotif`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                mahasiswaIds: mahasiswaIds,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Broadcast berhasil dikirim ke ' + mahasiswaIds.length + ' peserta!', true);
                var modal = bootstrap.Modal.getInstance(document.getElementById('addNotification'));
                if (modal) modal.hide();
                
                // Reset form
                document.getElementById('notifMessage').value = '';
            } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat mengirim broadcast', false);
        })
        .finally(() => {
            // Re-enable button
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        });
    });
    
    // Initialize
    renderSelectedMahasiswa();

    // Call custom initialization if available
    if (typeof window.initDaftarPeserta === 'function') {
        window.initDaftarPeserta();
    }
    };

    // Robust initialization with polling
    const waitForJQuery = function(callback, maxAttempts = 50) {
        let attempts = 0;
        const check = function() {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                callback();
            } else {
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(check, 100);
                } else {
                    console.error('jQuery failed to load after ' + (maxAttempts * 100) + 'ms');
                }
            }
        };
        check();
    };

    waitForJQuery(initDaftarPesertaScript);
})(); // Universal Wrapper Ends

// ============================================
// TRIGGER VERIFICATION FROM DETAIL MODAL
// ============================================
// ============================================
// TRIGGER VERIFICATION FROM DETAIL MODAL
// ============================================
function triggerVerificationFromModal() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaLengkap = document.getElementById('modalNamaHeader').textContent;
    
    if (mahasiswaId && namaLengkap) {
        // Close detail modal first
        bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
        
        showActionConfirmation({
            title: 'Konfirmasi Verifikasi Berkas',
            message: `Anda akan memverifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${namaLengkap}</strong><span class="text-secondary small"><i class="bi bi-info-circle me-1"></i>Pastikan semua dokumen telah sesuai sebelum melanjutkan</span>`,
            btnText: 'Verifikasi',
            type: 'success',
            onConfirm: function() {
                // Show loading state
                showAlert('Memproses verifikasi...', true);
                
                fetch(`${APP_URL}/acceptberkas`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + mahasiswaId + '&status=1'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Berhasil! Berkas berhasil diverifikasi!', true);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                    }
                })
                .catch(error => showAlert('Error: ' + error.message, false));
            }
        });
    } else {
        showAlert('Data peserta tidak ditemukan', false);
    }
}

// ============================================
// CANCEL VERIFICATION FROM DETAIL MODAL
// ============================================
function cancelVerification() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaLengkap = document.getElementById('modalNamaHeader').textContent;
    
    if (mahasiswaId && namaLengkap) {
        // Close detail modal first
        bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
        
        showActionConfirmation({
            title: 'Batalkan Verifikasi Berkas',
            message: `Anda akan membatalkan verifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${namaLengkap}</strong><span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Status akan kembali menjadi "Menunggu Verifikasi"</span>`,
            btnText: 'Batalkan',
            type: 'danger',
            onConfirm: function() {
                showAlert('Membatalkan verifikasi...', true);
                
                fetch(`${APP_URL}/acceptberkas`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + mahasiswaId + '&status=0'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Berhasil! Verifikasi dibatalkan.', true);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                    }
                })
                .catch(error => showAlert('Error: ' + error.message, false));
            }
        });
    } else {
        showAlert('Data peserta tidak ditemukan', false);
    }
}
// ============================================
// REMINDER BUTTON HANDLER
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for reminder buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-reminder')) {
            const btn = e.target.closest('.btn-reminder');
            const userId = btn.getAttribute('data-userid');
            const nama = btn.getAttribute('data-nama');
            
            // Show confirmation
            showActionConfirmation({
                title: 'Kirim Reminder',
                message: `Kirim reminder ke <strong>${nama}</strong> untuk upload berkas?`,
                btnText: 'Kirim',
                type: 'primary', // Blue for neutral/info action
                onConfirm: function() {
                    // Show loading
                    showAlert('Mengirim reminder...', true);
                    
                    // Send reminder
                    fetch(`${APP_URL}/sendNotification`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `user_id=${userId}&message=Mohon segera upload berkas pendaftaran Anda.&title=Reminder Upload Berkas`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert('Reminder berhasil dikirim!', true);
                        } else {
                            showAlert('Gagal mengirim reminder: ' + (data.message || 'Unknown error'), false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Error saat mengirim reminder', false);
                    });
                }
            });
        }
    });
});
// End of Reminder Handler
// No extra code here

// ============================================
// ACCEPT/REJECT PARTICIPANT FUNCTIONS
// ============================================
// ============================================
// ACCEPT/REJECT PARTICIPANT FUNCTIONS (LEGACY/ALTERNATIVE)
// ============================================

function acceptParticipant() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const nama = document.getElementById('modalNama').textContent;
    
    if (!mahasiswaId) {
        showAlert('ID Mahasiswa tidak ditemukan', false);
        return;
    }
    
    // Close detail modal first
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
    if (detailModal) detailModal.hide();
    
    showActionConfirmation({
        title: 'Verifikasi Berkas',
        message: `Anda akan memverifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${nama}</strong><span class="text-secondary small"><i class="bi bi-info-circle me-1"></i>Pastikan semua dokumen telah sesuai</span>`,
        btnText: 'Verifikasi',
        type: 'success', // Green
        onConfirm: function() {
            showAlert('Memproses verifikasi...', true);
            fetch(`${APP_URL}/acceptberkas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + mahasiswaId + '&status=1'
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    showAlert('Berkas berhasil diverifikasi!', true);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('Gagal: ' + (data.message || 'Unknown'), false);
                }
            })
            .catch(err => showAlert('Error: ' + err.message, false));
        }
    });
}

function rejectParticipant() {
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const nama = document.getElementById('modalNama').textContent;
    
    if (!mahasiswaId) {
        showAlert('ID Mahasiswa tidak ditemukan', false);
        return;
    }
    
    // Close detail modal first
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
    if (detailModal) detailModal.hide();
    
    showActionConfirmation({
        title: 'Tolak Verifikasi Berkas',
        message: `Anda akan menolak verifikasi berkas untuk:<br><strong class="fs-4 text-dark d-block my-2">${nama}</strong><span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Status akan diubah menjadi Ditolak</span>`,
        btnText: 'Tolak Verifikasi',
        type: 'danger', // Red
        onConfirm: function() {
            showAlert('Memproses penolakan...', true);
            fetch(`${APP_URL}/acceptberkas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + mahasiswaId + '&status=2'
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    showAlert('Verifikasi berkas ditolak!', true);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('Gagal: ' + (data.message || 'Unknown'), false);
                }
            })
            .catch(err => showAlert('Error: ' + err.message, false));
        }
    });
}

// ============================================
// DETAIL MODAL BACKDROP CLEANUP - AGGRESSIVE MODE
// ============================================
(function() {
    const initModalCleanup = function() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
        
        // DON'T cleanup backdrops in show.bs.modal - let Bootstrap handle it
        // Removing backdrops here causes race condition where Bootstrap creates 2 backdrops
        $(document).on('show.bs.modal', '#detailModal', function(e) {
            // Only reset body state if needed, but don't touch backdrops
            // Bootstrap will handle backdrop creation properly
        });
        
        // Cleanup after modal is fully shown
        $(document).on('shown.bs.modal', '#detailModal', function() {
            // Keep only ONE backdrop
            setTimeout(function() {
                var backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    // Remove all except the last one
                    for (var i = 0; i < backdrops.length - 1; i++) {
                        backdrops[i].remove();
                    }
                }
            }, 50);
        });
        
        // Cleanup when modal is hidden
        $(document).on('hidden.bs.modal', '#detailModal', function() {
            // Aggressive cleanup - remove ALL backdrops
            setTimeout(function() {
                var backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });
                
                // Force reset body state
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                // Double check after another delay
                setTimeout(function() {
                    var remainingBackdrops = document.querySelectorAll('.modal-backdrop');
                    if (remainingBackdrops.length > 0) {
                        remainingBackdrops.forEach(function(backdrop) {
                            backdrop.remove();
                        });
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                }, 100);
            }, 50);
        });
    };
    
    // Robust initialization with polling
    const waitForJQueryModal = function(callback, maxAttempts = 50) {
        let attempts = 0;
        const check = function() {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                callback();
            } else {
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(check, 100);
                }
            }
        };
        check();
    };
    
    waitForJQueryModal(initModalCleanup);
})();






// ============================================
// SEND MESSAGE FUNCTIONS (Dynamic Event Listeners)
// ============================================



