
// Universal wrapper for AJAX and Direct Load
(function() {
    const initDaftarPesertaScript = function() {
        console.log('Daftar Peserta script loaded');
        
        // Initialize DataTable
        // Check if already initialized and destroy if so to prevent errors
        if ($.fn.DataTable.isDataTable('#daftarPesertaTable')) {
            $('#daftarPesertaTable').DataTable().destroy();
        }

        // Initialize DataTable with custom search (Paging disabled as requested)
        var table = $('#daftarPesertaTable').DataTable({
            dom: "t",
            paging: false,
            info: false,
            language: {
                search: "", 
                searchPlaceholder: "Cari peserta..."
            },
            columnDefs: [
                { orderable: false, targets: [1, -1] } // Disable sorting on avatar and action columns
            ]
        });

        // Link custom search input
        $('#searchPeserta').on('keyup', function() {
            table.search(this.value).draw();
        });

    // Store current row data
    var currentRowData = null;

    // Clean up existing handlers first
    $(document).off('click', '.btn-view');
    $(document).off('click', '.btn-delete');
    $(document).off('click', '.btn-reminder');
    
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
                showSuccessPopup('Pesan berhasil dikirim!');
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
                modalFoto.src = `${PROJECT_ROOT}/res/imageUser/default.png`; 
            }

            // Populate header
            document.getElementById('modalNamaHeader').textContent = data.nama || '-';
            document.getElementById('modalStambukHeader').textContent = data.stambuk || '-';
            
            // Set photo
            var fotoPath = data.foto ? `${PROJECT_ROOT}/res/imageUser/${data.foto}` : `${PROJECT_ROOT}/res/imageUser/default.png`;
            
            if (modalFoto) {
                modalFoto.src = fotoPath;
                modalFoto.onerror = function() {
                    this.src = `${PROJECT_ROOT}/res/imageUser/default.png`;
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
            var btnTerima = document.getElementById('btnTerimaModal');
            var btnTolak = document.getElementById('btnTolakModal');
            
            if (!btnVerifikasi || !btnBatalkan) return;
            
            // RESET button states
            btnVerifikasi.style.display = 'none';
            btnBatalkan.style.display = 'none';
            if (btnTerima) btnTerima.style.display = 'none';
            if (btnTolak) btnTolak.style.display = 'none';
            
            if (berkasAccepted == '1') {
                statusBadge.className = 'badge rounded-pill px-4 py-2 bg-success text-white';
                statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Berkas Terverifikasi';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow bg-success text-white d-flex align-items-center justify-content-center';
                statusIcon.style.width = '30px';
                statusIcon.style.height = '30px';
                statusIcon.innerHTML = '<i class="bi bi-check-lg"></i>';
                btnBatalkan.style.display = 'inline-block';
            } else if (berkasAccepted == '0') {
                statusBadge.className = 'badge rounded-pill px-4 py-2 bg-info text-white';
                statusBadge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow bg-info text-white d-flex align-items-center justify-content-center';
                statusIcon.style.width = '30px';
                statusIcon.style.height = '30px';
                statusIcon.innerHTML = '<i class="bi bi-clock"></i>';
                btnVerifikasi.style.display = 'inline-block';
                btnVerifikasi.disabled = false;
            } else {
                statusBadge.className = 'badge rounded-pill px-4 py-2 bg-secondary text-white';
                statusBadge.innerHTML = '<i class="bi bi-file-earmark-x me-1"></i>Belum Upload Berkas';
                statusIcon.className = 'position-absolute bottom-0 end-0 rounded-circle shadow bg-secondary text-white d-flex align-items-center justify-content-center';
                statusIcon.style.width = '30px';
                statusIcon.style.height = '30px';
                statusIcon.innerHTML = '<i class="bi bi-x-lg"></i>';
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
        var bodyParams = '';
        if (userId) {
            bodyParams = 'id=' + userId; // Delete User (and linked Mahasiswa)
        } else if (mhsId) {
            bodyParams = 'mahasiswaId=' + mhsId; // Delete Mahasiswa only
        } else {
            showAlert('ID data tidak ditemukan', false);
            return;
        }
        
        showConfirmDelete(function() {
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
        }, 'Apakah Anda yakin ingin menghapus data peserta ini?<br>Tindakan ini tidak dapat dibatalkan.');
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
        var btnSubmit = this.querySelector('button[type="submit"]');
        var originalText = btnSubmit.innerHTML;
        
        if (selectedMahasiswa.length === 0) {
            showAlert('Pilih peserta terlebih dahulu', false);
            return;
        }
        
        if (!message.trim()) {
            showAlert('Pesan tidak boleh kosong', false);
            return;
        }
        
        // Disable button and show loading state
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
        
        var mahasiswaIds = selectedMahasiswa.map(function(m) { return m.id; });
        
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
    
    console.log('Daftar Peserta script initialization complete');
    };

    // Robust initialization with polling
    const waitForJQuery = function(callback, maxAttempts = 50) {
        let attempts = 0;
        const check = function() {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined' && $.fn.DataTable) {
                callback();
            } else {
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(check, 100);
                } else {
                    console.error('jQuery/DataTables failed to load after ' + (maxAttempts * 100) + 'ms');
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
    fetch(`${APP_URL}/acceptberkas`, {
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
    fetch(`${APP_URL}/acceptberkas`, {
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






// ============================================
// SEND MESSAGE FUNCTIONS (Dynamic Event Listeners)
// ============================================



