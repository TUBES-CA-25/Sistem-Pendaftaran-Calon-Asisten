
// Universal wrapper for AJAX and Direct Load
(function() {
    const initDaftarPesertaScript = function() {
        console.log('Daftar Peserta script loaded');
        
        // Initialize DataTable
        // Initialize DataTable with standard Bootstrap 5 styling
        var table = $('#daftarPesertaTable').DataTable({
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" + 
                 "<'row'<'col-sm-12'tr>>",
            paging: false,
            info: false,
            language: {
                search: "", 
                searchPlaceholder: "Cari peserta...",
                lengthMenu: "Tampilkan _MENU_ data per halaman"
            },
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting on action column
            ]
        });

    // Store current row data
    var currentRowData = null;

    // Handle view detail button click
    document.querySelectorAll('.btn-view').forEach(function(btn) {
        btn.addEventListener('click', function() {
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


            // Modal data is populated below - no button manipulation needed
            
            
            
            // Populate header
            document.getElementById('modalNamaHeader').textContent = data.nama || '-';
            document.getElementById('modalStambukHeader').textContent = data.stambuk || '-';
            
            // Set photo
            var fotoPath = data.foto ? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' + data.foto : '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
            document.getElementById('modalFoto').src = fotoPath;
            document.getElementById('modalFoto').onerror = function() {
                this.src = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
            };
            
            // Populate modal fields (also used in info cards)
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
            
            // Status Badge and Status Icon
            var statusBadge = document.getElementById('modalStatusBadge');
            var statusIcon = document.getElementById('modalStatusIcon');
            var berkasAccepted = data.berkas_accepted;
            
            // Get button elements
            var btnVerifikasi = document.getElementById('btnVerifikasiModal');
            var btnBatalkan = document.getElementById('btnBatalkanModal');
            
            // Get Terima/Tolak button elements
            var btnTerima = document.getElementById('btnTerimaModal');
            var btnTolak = document.getElementById('btnTolakModal');
            
            // Check if elements exist before manipulating
            if (!btnVerifikasi || !btnBatalkan) {
                console.error('Verification buttons not found!');
                console.log('btnVerifikasi:', btnVerifikasi);
                console.log('btnBatalkan:', btnBatalkan);
                return; // Exit early if buttons don't exist
            }
            
            // RESET button states first
            btnVerifikasi.disabled = false;
            btnVerifikasi.style.opacity = '1';
            btnVerifikasi.style.cursor = 'pointer';
            btnVerifikasi.style.display = 'none';
            btnBatalkan.style.display = 'none';
            
            // Reset Terima/Tolak buttons
            if (btnTerima) btnTerima.style.display = 'none';
            if (btnTolak) btnTolak.style.display = 'none';
            
            // Check if status elements exist
            if (!statusBadge || !statusIcon) {
                console.error('Status elements not found!');
                return;
            }
            
            if (berkasAccepted == '1') {
                // TERVERIFIKASI - Show cancel button, hide verification button
                statusBadge.className = 'badge rounded-pill px-4 py-2 badge-diterima';
                statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Berkas Terverifikasi';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow status-icon-verified';
                statusIcon.innerHTML = '<i class="bi bi-check-lg"></i>';
                
                // Hide verification button, show cancel button
                btnVerifikasi.style.display = 'none';
                btnBatalkan.style.display = 'inline-block';
                
            } else if (berkasAccepted == '0') {
                // PENDING - Show verification button (disabled), hide cancel button
                statusBadge.className = 'badge rounded-pill px-4 py-2 badge-process';
                statusBadge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow status-icon-pending';
                statusIcon.innerHTML = '<i class="bi bi-clock"></i>';
                
                // Show verification button enabled
                btnVerifikasi.style.display = 'inline-block';
                btnVerifikasi.disabled = false;
                btnVerifikasi.style.opacity = '1';
                btnVerifikasi.style.cursor = 'pointer';
                btnBatalkan.style.display = 'none';
                
            } else {
                // BELUM UPLOAD - Show Terima/Tolak buttons, hide Verifikasi button
                statusBadge.className = 'badge rounded-pill px-4 py-2 badge-pending';
                statusBadge.innerHTML = '<i class="bi bi-file-earmark-x me-1"></i>Belum Upload Berkas';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow status-icon-none';
                statusIcon.innerHTML = '<i class="bi bi-x-lg"></i>';
                
                // Hide verification buttons
                btnVerifikasi.style.display = 'none';
                btnBatalkan.style.display = 'none';
                
                // Show Terima/Tolak buttons
                if (btnTerima) btnTerima.style.display = 'inline-block';
                if (btnTolak) btnTolak.style.display = 'inline-block';
            }
            
            // Set photo
            var fotoUrl = data.foto ? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' + data.foto : '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
            document.getElementById('modalFoto').src = fotoUrl;
            document.getElementById('modalFoto').onerror = function() {
                this.src = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
            };

            // Set download URLs for berkas - with null checks
            var downloadFotoBtn = document.getElementById('downloadFotoButton');
            var downloadCVBtn = document.getElementById('downloadCVButton');
            var downloadTranskripBtn = document.getElementById('downloadTranskripButton');
            var downloadSuratBtn = document.getElementById('downloadSuratButton');
            
            if (downloadFotoBtn) downloadFotoBtn.setAttribute('data-download-url', data.foto ? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' + data.foto : '');
            if (downloadCVBtn) downloadCVBtn.setAttribute('data-download-url', data.cv ? '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/' + data.cv : '');
            if (downloadTranskripBtn) downloadTranskripBtn.setAttribute('data-download-url', data.transkrip ? '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/' + data.transkrip : '');
            if (downloadSuratBtn) downloadSuratBtn.setAttribute('data-download-url', data.surat ? '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/' + data.surat : '');
            
            // Set download URLs for presentasi files - with null checks
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
            
            // Show/hide no files message
            if (noPresentasiFiles) {
                noPresentasiFiles.style.display = hasPresentasiFiles ? 'none' : 'inline-block';
            }
            
            // SHOW MODAL MANUALLY NOW
            // This prevents race condition with data-bs-toggle vs JS data population
            var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
            
            } catch (error) {
                console.error('Error opening detail modal:', error);
                showAlert('Terjadi kesalahan saat membuka detail peserta: ' + error.message, false);
            }
        });
    });

    // Handle send message button in detail modal
    var btnSendMessage = document.getElementById('btnSendMessageToUser');
    if (btnSendMessage) {
        btnSendMessage.addEventListener('click', function() {
            if (currentRowData) {
                // Close detail modal
                var detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                if (detailModal) detailModal.hide();
                
                // Set data for send message modal
                var messageRecipient = document.getElementById('messageRecipient');
                var messageUserId = document.getElementById('messageUserId');
                var messageMahasiswaId = document.getElementById('messageMahasiswaId');
                var individualMessage = document.getElementById('individualMessage');
                
                if (messageRecipient) messageRecipient.textContent = currentRowData.stambuk + ' - ' + currentRowData.nama;
                if (messageUserId) messageUserId.value = currentRowData.userId;
                if (messageMahasiswaId) messageMahasiswaId.value = currentRowData.id;
                if (individualMessage) individualMessage.value = '';
                
            
            // Show send message modal
            setTimeout(function() {
                var sendMessageModalEl = document.getElementById('sendMessageModal');
                if (sendMessageModalEl) {
                    var sendMessageModal = new bootstrap.Modal(sendMessageModalEl);
                    sendMessageModal.show();
                }
            }, 300);
        }
        });
    }

    // Handle send individual message
    var sendIndividualMessageBtn = document.getElementById('sendIndividualMessage');
    if (sendIndividualMessageBtn) {
        sendIndividualMessageBtn.addEventListener('click', function() {
            var mahasiswaIdEl = document.getElementById('messageMahasiswaId');
            var messageEl = document.getElementById('individualMessage');
            
            if (!mahasiswaIdEl || !messageEl) {
                console.error('Message form elements not found');
                return;
            }
            
            var mahasiswaId = mahasiswaIdEl.value;
            var message = messageEl.value;
            
            if (!message.trim()) {
                showAlert('Pesan tidak boleh kosong', false);
                return;
            }
            
            // Send notification
            fetch('/Sistem-Pendaftaran-Calon-Asisten/addallnotif', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mahasiswaIds: [mahasiswaId],
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Pesan berhasil dikirim!', true);
                    var modal = bootstrap.Modal.getInstance(document.getElementById('sendMessageModal'));
                    if (modal) modal.hide();
                } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat mengirim pesan', false);
        });
        });
    }

    // Handle download buttons
    document.querySelectorAll('[id^="download"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-download-url');
            if (url) {
                window.open(url, '_blank');
            } else {
                showAlert('File tidak tersedia', false);
            }
        });
    });

    /* DEPRECATED: These IDs do not exist. Use acceptParticipant() and rejectParticipant() instead.
    // Get accept button reference
    const acceptButton = document.getElementById('acceptButton');
    if (acceptButton) {
        acceptButton.addEventListener('click', function() {
           // ... logic moved to acceptParticipant()
        });
    }
    */

    // Function to verify berkas
    function verifyBerkas(mahasiswaId, button, fromModal) {
        fetch('/Sistem-Pendaftaran-Calon-Asisten/public/acceptberkas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + mahasiswaId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Berkas berhasil diverifikasi!', true);

                // Update button state if from table
                if (button && !fromModal) {
                    // Change verify button to verified button
                    button.classList.remove('btn-verify');
                    button.classList.add('btn-verified');
                    button.setAttribute('title', 'Berkas Terverifikasi');
                    button.setAttribute('disabled', 'disabled');
                    button.style.cursor = 'not-allowed';
                }

                // Update status badge in table row
                var row = document.querySelector('tr[data-id="' + mahasiswaId + '"]');
                if (row) {
                    var statusBadge = row.querySelector('.badge-status');
                    if (statusBadge) {
                        statusBadge.className = 'badge-status badge-diterima';
                        statusBadge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Diterima';
                    }

                    // Also update the view button data
                    var viewBtn = row.querySelector('.btn-view');
                    if (viewBtn) {
                        viewBtn.dataset.berkas_accepted = '1';
                    }
                }

                // Close modal if from modal
                if (fromModal) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                    if (modal) modal.hide();
                }

                // Reload page after 1 second to reflect changes
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memverifikasi berkas', false);
        });
    }

    // Handle delete button click
    // Handle delete button click
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var row = this.closest('tr');
            var userId = row.getAttribute('data-userid');
            
            showConfirmDelete(function() {
                fetch('/Sistem-Pendaftaran-Calon-Asisten/deletemahasiswa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + userId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Data berhasil dihapus!', true);
                        location.reload();
                    } else {
                        showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat menghapus data', false);
                });
            }, 'Apakah Anda yakin ingin menghapus data peserta ini?<br>Tindakan ini tidak dapat dibatalkan.');
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
            list.innerHTML = '<li class="list-group-item text-muted text-center py-3"><i class="bi bi-inbox me-1"></i>Belum ada peserta dipilih</li>';
        } else {
            selectedMahasiswa.forEach(function(mhs, index) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                li.innerHTML = '<span class="small">' + mhs.text + '</span>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" data-index="' + index + '">' +
                    '<i class="bi bi-x"></i></button>';
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
    
    // Add single mahasiswa
    document.getElementById('addMahasiswaButton').addEventListener('click', function() {
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
    
    // Add all mahasiswa
    document.getElementById('addAllMahasiswaButton').addEventListener('click', function() {
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
    
    // Submit notification form
    document.getElementById('addNotificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var message = document.getElementById('notifMessage').value;
        
        if (selectedMahasiswa.length === 0) {
            showAlert('Pilih peserta terlebih dahulu', false);
            return;
        }
        
        if (!message.trim()) {
            showAlert('Pesan tidak boleh kosong', false);
            return;
        }
        
        var mahasiswaIds = selectedMahasiswa.map(function(m) { return m.id; });
        
        fetch('/Sistem-Pendaftaran-Calon-Asisten/addallnotif', {
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
                showAlert('Notifikasi berhasil dikirim ke ' + selectedMahasiswa.length + ' peserta!', true);
                var modal = bootstrap.Modal.getInstance(document.getElementById('addNotification'));
                if (modal) modal.hide();
                
                // Reset form
                selectedMahasiswa = [];
                renderSelectedMahasiswa();
                document.getElementById('notifMessage').value = '';
                document.getElementById('mahasiswa').selectedIndex = 0;
            } else {
                showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat mengirim notifikasi', false);
        });
    });
    
    // Initialize
    renderSelectedMahasiswa();

    // Call custom initialization if available
    if (typeof window.initDaftarPeserta === 'function') {
        window.initDaftarPeserta();
    }
    
    console.log('Daftar Peserta script initialization complete');
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
// UTILITY FUNCTIONS
// ============================================
function showAlert(message, isSuccess) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${isSuccess ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alertDiv.innerHTML = `
        <i class="bi bi-${isSuccess ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 3000);
}

// ============================================
// TRIGGER VERIFICATION FROM DETAIL MODAL
// ============================================
function triggerVerificationFromModal() {
    console.log('=== Verification button clicked ===');
    
    // Check if button is disabled
    const btnVerifikasi = document.getElementById('btnVerifikasiModal');
    if (btnVerifikasi.disabled) {
        console.log('Button is disabled - cannot verify');
        showAlert('Status masih pending, tidak bisa diverifikasi', false);
        return;
    }
    
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaLengkap = document.getElementById('modalNamaHeader').textContent;
    
    console.log('Mahasiswa ID:', mahasiswaId);
    console.log('Nama Lengkap:', namaLengkap);
    
    if (mahasiswaId && namaLengkap) {
        // Close detail modal first
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        if (detailModal) {
            console.log('Closing detail modal...');
            detailModal.hide();
        }
        
        // Show verification popup after a short delay
        setTimeout(function() {
            console.log('Showing verification popup...');
            showVerificationPopup(mahasiswaId, namaLengkap);
        }, 300);
    } else {
        console.error('Missing data - ID:', mahasiswaId, 'Name:', namaLengkap);
        showAlert('Data peserta tidak ditemukan', false);
    }
}

// ============================================
// CANCEL VERIFICATION FROM DETAIL MODAL
// ============================================
function cancelVerification() {
    console.log('=== Cancel verification button clicked ===');
    
    const mahasiswaId = document.getElementById('modalMahasiswaId').value;
    const namaLengkap = document.getElementById('modalNamaHeader').textContent;
    
    console.log('Mahasiswa ID:', mahasiswaId);
    console.log('Nama Lengkap:', namaLengkap);
    
    if (mahasiswaId && namaLengkap) {
        // Close detail modal first
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        if (detailModal) {
            console.log('Closing detail modal...');
            detailModal.hide();
        }
        
        // Show cancellation popup after a short delay
        setTimeout(function() {
            console.log('Showing cancellation popup...');
            showCancellationPopup(mahasiswaId, namaLengkap);
        }, 300);
    } else {
        console.error('Missing data - ID:', mahasiswaId, 'Name:', namaLengkap);
        showAlert('Data peserta tidak ditemukan', false);
    }
}

// ============================================
// CANCELLATION POPUP FUNCTION
// ============================================
function showCancellationPopup(mahasiswaId, namaLengkap) {
    // Create custom modal HTML
    const popupHTML = `
        <div class="modal fade" id="cancellationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                    <!-- Header with gradient -->
                    <div class="modal-header border-0 text-white position-relative" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px;">
                        <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                        <div class="w-100 text-center position-relative">
                            <div class="mb-3">
                                <i class="bi bi-x-circle" style="font-size: 4rem; opacity: 0.9;"></i>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Batalkan Verifikasi Berkas</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white position-absolute" style="top: 15px; right: 15px;" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <!-- Body -->
                    <div class="modal-body text-center px-4 py-4">
                        <p class="text-muted mb-2" style="font-size: 0.9rem;">Anda akan membatalkan verifikasi berkas untuk:</p>
                        <h6 class="fw-bold mb-4" style="color: #1f2937; font-size: 1.1rem;">${namaLengkap}</h6>
                        <p class="text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Status akan kembali menjadi "Menunggu Verifikasi"
                        </p>
                    </div>
                    
                    <!-- Footer -->
                    <div class="modal-footer border-0 justify-content-center px-4 pb-4 pt-0">
                        <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal" style="background: #f3f4f6; color: #6b7280; border: none; border-radius: 10px; min-width: 120px;">
                            <i class="bi bi-x-lg me-2"></i>Batal
                        </button>
                        <button type="button" class="btn px-4 py-2" onclick="confirmCancellation(${mahasiswaId})" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); min-width: 120px;">
                            <i class="bi bi-x-circle me-2"></i>Batalkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('cancellationModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Append to body
    document.body.insertAdjacentHTML('beforeend', popupHTML);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('cancellationModal'));
    modal.show();
    
    // Remove modal from DOM after it's hidden AND cleanup backdrop
    document.getElementById('cancellationModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
        
        // Clean up all backdrops
        setTimeout(function() {
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            
            // Reset body state
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 100);
    }, { once: true });
}

function confirmCancellation(mahasiswaId) {
    // Close the modal first
    const modal = bootstrap.Modal.getInstance(document.getElementById('cancellationModal'));
    if (modal) modal.hide();
    
    // Show loading state
    showAlert('Membatalkan verifikasi...', true);
    
    // Send cancellation request
    fetch('/Sistem-Pendaftaran-Calon-Asisten/public/acceptberkas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + mahasiswaId + '&status=0'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessPopup('Verifikasi berhasil dibatalkan!');
            // Reload page after 1.5 seconds
            setTimeout(function() {
                location.reload();
            }, 1500);
        } else {
            showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error 1925: ' + error.message, false);
    });
}

// ============================================
// VERIFICATION POPUP FUNCTION
// ============================================
function showVerificationPopup(mahasiswaId, namaLengkap) {
    // Create custom modal HTML
    const popupHTML = `
        <div class="modal fade" id="verificationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                    <!-- Header with gradient -->
                    <div class="modal-header border-0 text-white position-relative" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px;">
                        <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                        <div class="w-100 text-center position-relative">
                            <div class="mb-3">
                                <i class="bi bi-check-circle" style="font-size: 4rem; opacity: 0.9;"></i>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Konfirmasi Verifikasi Berkas</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white position-absolute" style="top: 15px; right: 15px;" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <!-- Body -->
                    <div class="modal-body text-center px-4 py-4">
                        <p class="text-muted mb-2" style="font-size: 0.9rem;">Anda akan memverifikasi berkas untuk:</p>
                        <h6 class="fw-bold mb-4" style="color: #1f2937; font-size: 1.1rem;">${namaLengkap}</h6>
                        <p class="text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Pastikan semua dokumen telah sesuai sebelum melanjutkan
                        </p>
                    </div>
                    
                    <!-- Footer -->
                    <div class="modal-footer border-0 justify-content-center px-4 pb-4 pt-0">
                        <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal" style="background: #f3f4f6; color: #6b7280; border: none; border-radius: 10px; min-width: 120px;">
                            <i class="bi bi-x-lg me-2"></i>Batal
                        </button>
                        <button type="button" class="btn px-4 py-2" onclick="confirmVerification(${mahasiswaId})" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); min-width: 120px;">
                            <i class="bi bi-check-circle me-2"></i>Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('verificationModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Append to body
    document.body.insertAdjacentHTML('beforeend', popupHTML);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
    modal.show();
    
    // Remove modal from DOM after it's hidden AND cleanup backdrop
    document.getElementById('verificationModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
        
        // Clean up all backdrops
        setTimeout(function() {
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            
            // Reset body state
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 100);
    }, { once: true });
}

function confirmVerification(mahasiswaId) {
    // Close the modal first
    const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
    if (modal) modal.hide();
    
    // Show loading state
    showAlert('Memproses verifikasi...', true);
    
    // Send verification request
    fetch('/Sistem-Pendaftaran-Calon-Asisten/public/acceptberkas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + mahasiswaId + '&status=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessPopup('Berkas berhasil diverifikasi!');
            // Reload page after 1.5 seconds
            setTimeout(function() {
                location.reload();
            }, 1500);
        } else {
            showAlert('Gagal: ' + (data.message || 'Terjadi kesalahan'), false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error 2023: ' + error.message, false);
    });
}

function showSuccessPopup(message) {
    const successHTML = `
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 text-center" style="border-radius: 20px; padding: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bi bi-check-lg text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: #1f2937;">Berhasil!</h6>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">${message}</p>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', successHTML);
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
    
    setTimeout(function() {
        modal.hide();
        document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }, 1500);
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
            if (confirm(`Kirim reminder ke ${nama} untuk upload berkas?`)) {
                // Show loading
                showAlert('Mengirim reminder...', true);
                
                // Send reminder (you can customize the endpoint and message)
                fetch('/Sistem-Pendaftaran-Calon-Asisten/public/sendNotification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&message=Mohon segera upload berkas pendaftaran Anda.&title=Reminder Upload Berkas`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showSuccessPopup('Reminder berhasil dikirim!');
                    } else {
                        showAlert('Gagal mengirim reminder: ' + (data.message || 'Unknown error'), false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat mengirim reminder', false);
                });
            }
        }
    });
});

// ============================================
// ACCEPT/REJECT PARTICIPANT FUNCTIONS
// ============================================
function acceptParticipant() {
    var mahasiswaId = document.getElementById('modalMahasiswaId').value;
    var nama = document.getElementById('modalNama').textContent;
    
    if (!mahasiswaId) {
        showAlert('ID Mahasiswa tidak ditemukan', false);
        return;
    }
    
    // Close detail modal first
    var detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
    if (detailModal) {
        detailModal.hide();
    }
    
    // Wait for detail modal to fully close, then show confirmation
    setTimeout(function() {
        // Force cleanup any leftover backdrops before showing new modal
        var existingBackdrops = document.querySelectorAll('.modal-backdrop');
        existingBackdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Create custom confirmation popup (premium design)
        var popupHTML = `
            <div class="modal fade" id="confirmAcceptModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                        <!-- Header with gradient -->
                        <div class="modal-header border-0 text-white position-relative" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px;">
                            <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                            <div class="w-100 text-center position-relative">
                                <div class="mb-3">
                                    <i class="bi bi-check-circle" style="font-size: 4rem; opacity: 0.9;"></i>
                                </div>
                                <h5 class="modal-title fw-bold mb-0">Konfirmasi Verifikasi Berkas</h5>
                            </div>
                            <button type="button" class="btn-close btn-close-white position-absolute" style="top: 15px; right: 15px;" data-bs-dismiss="modal"></button>
                        </div>
                        
                        <!-- Body -->
                        <div class="modal-body text-center px-4 py-4">
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">Anda akan memverifikasi berkas untuk:</p>
                            <h6 class="fw-bold mb-4" style="color: #1f2937; font-size: 1.1rem;">${nama}</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Pastikan semua dokumen telah sesuai sebelum melanjutkan
                            </p>
                        </div>
                        
                        <!-- Footer -->
                        <div class="modal-footer border-0 justify-content-center px-4 pb-4 pt-0">
                            <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal" style="background: #f3f4f6; color: #6b7280; border: none; border-radius: 10px; min-width: 120px;">
                                <i class="bi bi-x-lg me-2"></i>Batal
                            </button>
                            <button type="button" class="btn px-4 py-2" onclick="confirmAccept(${mahasiswaId})" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); min-width: 120px;">
                                <i class="bi bi-check-circle me-2"></i>Verifikasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        var existingModal = document.getElementById('confirmAcceptModal');
        if (existingModal) existingModal.remove();
        
        // Append to body
        document.body.insertAdjacentHTML('beforeend', popupHTML);
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('confirmAcceptModal'));
        modal.show();
        
        // Cleanup BEFORE modal is hidden (prevents backdrop accumulation)
        document.getElementById('confirmAcceptModal').addEventListener('hide.bs.modal', function() {
            // Remove all backdrops immediately
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, { once: true });
        
        // Cleanup when modal is hidden (including when Batal is clicked)
        document.getElementById('confirmAcceptModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
            
            // IMMEDIATE cleanup (no delay)
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Double check with delay
            setTimeout(function() {
                var remainingBackdrops = document.querySelectorAll('.modal-backdrop');
                remainingBackdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 100);
        }, { once: true });
    }, 400);
}

function confirmAccept(mahasiswaId) {
    // Close the modal first
    var modal = bootstrap.Modal.getInstance(document.getElementById('confirmAcceptModal'));
    if (modal) modal.hide();
    
    // Show loading
    showAlert('Memproses verifikasi...', true);
    
    // Send accept request
    fetch('/Sistem-Pendaftaran-Calon-Asisten/public/acceptberkas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + mahasiswaId + '&status=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessPopup('Berkas berhasil diverifikasi!');
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Gagal memverifikasi berkas: ' + (data.message || 'Unknown error'), false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan saat memverifikasi berkas', false);
    });
}

function rejectParticipant() {
    var mahasiswaId = document.getElementById('modalMahasiswaId').value;
    var nama = document.getElementById('modalNama').textContent;
    
    if (!mahasiswaId) {
        showAlert('ID Mahasiswa tidak ditemukan', false);
        return;
    }
    
    // Close detail modal first
    var detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
    if (detailModal) {
        detailModal.hide();
    }
    
    // Wait for detail modal to fully close, then show confirmation
    setTimeout(function() {
        // Force cleanup any leftover backdrops before showing new modal
        var existingBackdrops = document.querySelectorAll('.modal-backdrop');
        existingBackdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Create custom confirmation popup (premium design)
        var popupHTML = `
            <div class="modal fade" id="confirmRejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                        <!-- Header with gradient -->
                        <div class="modal-header border-0 text-white position-relative" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px;">
                            <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                            <div class="w-100 text-center position-relative">
                                <div class="mb-3">
                                    <i class="bi bi-x-circle" style="font-size: 4rem; opacity: 0.9;"></i>
                                </div>
                                <h5 class="modal-title fw-bold mb-0">Batalkan Verifikasi Berkas</h5>
                            </div>
                            <button type="button" class="btn-close btn-close-white position-absolute" style="top: 15px; right: 15px;" data-bs-dismiss="modal"></button>
                        </div>
                        
                        <!-- Body -->
                        <div class="modal-body text-center px-4 py-4">
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">Anda akan membatalkan verifikasi berkas untuk:</p>
                            <h6 class="fw-bold mb-4" style="color: #1f2937; font-size: 1.1rem;">${nama}</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Status akan kembali menjadi "Menunggu Verifikasi"
                            </p>
                        </div>
                        
                        <!-- Footer -->
                        <div class="modal-footer border-0 justify-content-center px-4 pb-4 pt-0">
                            <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal" style="background: #f3f4f6; color: #6b7280; border: none; border-radius: 10px; min-width: 120px;">
                                <i class="bi bi-x-lg me-2"></i>Batal
                            </button>
                            <button type="button" class="btn px-4 py-2" onclick="confirmReject(${mahasiswaId})" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); min-width: 120px;">
                                <i class="bi bi-x-circle me-2"></i>Batalkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        var existingModal = document.getElementById('confirmRejectModal');
        if (existingModal) existingModal.remove();
        
        // Append to body
        document.body.insertAdjacentHTML('beforeend', popupHTML);
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('confirmRejectModal'));
        modal.show();
        
        // Cleanup BEFORE modal is hidden (prevents backdrop accumulation)
        document.getElementById('confirmRejectModal').addEventListener('hide.bs.modal', function() {
            // Remove all backdrops immediately
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, { once: true });
        
        // Cleanup when modal is hidden (including when Batal is clicked)
        document.getElementById('confirmRejectModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
            
            // IMMEDIATE cleanup (no delay)
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Double check with delay
            setTimeout(function() {
                var remainingBackdrops = document.querySelectorAll('.modal-backdrop');
                remainingBackdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 100);
        }, { once: true });
    }, 400);
}

function confirmReject(mahasiswaId) {
    // Close the modal first
    var modal = bootstrap.Modal.getInstance(document.getElementById('confirmRejectModal'));
    if (modal) modal.hide();
    
    // Show loading
    showAlert('Memproses pembatalan...', true);
    
    // Send reject request
    fetch('/Sistem-Pendaftaran-Calon-Asisten/public/acceptberkas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + mahasiswaId + '&status=2'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessPopup('Verifikasi berkas berhasil dibatalkan!');
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Gagal membatalkan verifikasi: ' + (data.message || 'Unknown error'), false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan saat membatalkan verifikasi', false);
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



(function() {
    const initParticipantsSearch = function() {
        // Ensure jQuery is available
        if (typeof jQuery === 'undefined') return;

        $(document).ready(function() {
            // ---------------------------------------------------------
            // 1. INJECT SEARCH BAR INTO NAVBAR (Without modifying global component)
            // ---------------------------------------------------------
            var searchHtml = `
                <div id="navbarSearchContainer" class="d-none d-md-flex align-items-center flex-grow-1 mx-4 justify-content-center">
                    <div class="position-relative w-100" style="max-width: 600px;">
                        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y text-secondary ms-3"></i>
                        <input type="text" id="navbarSearchInput" class="form-control rounded-pill ps-5 py-2 border-0 bg-light-subtle shadow-sm" placeholder="Cari nama, stambuk, atau status...">
                    </div>
                </div>
            `;
            
            // Insert after the brand logo section
            // Use .first() to ensure we target the main navbar if multiple exist (unlikely but safe)
            if ($('#navbarSearchContainer').length === 0) {
                $('.navbar .navbar-brand').first().after(searchHtml);
            }

            // ---------------------------------------------------------
            // 2. INITIALIZE DATATABLE
            // ---------------------------------------------------------
            // Fix: Destroy existing table if it exists to prevent "Cannot reinitialise" error
            if ($.fn.DataTable.isDataTable('#daftarPesertaTable')) {
                $('#daftarPesertaTable').DataTable().destroy();
            }

            var table = $('#daftarPesertaTable').DataTable({
                "paging": false,       // User requirement: No pagination (vertical list)
                "lengthChange": false,
                "searching": true,     // Enable search logic
                "ordering": true,
                "info": false,         // User requirement: No info text
                "autoWidth": false,
                "responsive": true,
                "dom": 'rt',           // Hide default search input ('f')
                "language": {
                    "emptyTable": "Tidak ada data peserta",
                    "zeroRecords": "Tidak ada hasil pencarian yang sesuai"
                }
            });

            // ---------------------------------------------------------
            // 3. LINK INJECTED INPUT TO DATATABLE
            // ---------------------------------------------------------
            // Use delegation since element is dynamic (though inserted synchronously above)
            $(document).on('keyup', '#navbarSearchInput', function() {
                table.search(this.value).draw();
            });
            
            $(document).on('keydown', '#navbarSearchInput', function(e) {
                if (e.key === 'Enter') e.preventDefault();
            });
            // ---------------------------------------------------------
            // 4. GLOBAL CLEANUP (Fixes stuck backdrops)
            // ---------------------------------------------------------
            // Runs periodically - FASTER INTERVAL
            setInterval(function() {
                // Check if there are any modals open
                var openModals = document.querySelectorAll('.modal.show');
                var backdrops = document.querySelectorAll('.modal-backdrop');
                
                // If no modals are open but backdrops exist, remove them
                if (openModals.length === 0 && backdrops.length > 0) {
                    backdrops.forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }
                
                // If there are more backdrops than modals, remove extras
                if (backdrops.length > openModals.length) {
                    var extraBackdrops = backdrops.length - openModals.length;
                    for (var i = 0; i < extraBackdrops; i++) {
                        backdrops[i].remove();
                    }
                }
            }, 200); // Check every 200ms
        });
    };

    // Robust initialization with polling
    const waitForJQuerySearch = function(callback, maxAttempts = 50) {
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

    waitForJQuerySearch(initParticipantsSearch);
})();



