/**
 * Exam/Bank Soal Admin JavaScript
 * Handles all interactions for the Bank Soal management page
 */

// Global Variables
window.currentBankId = null;
window.currentBankSoal = [];

// Initialize data from PHP if available
if (typeof window.serverData !== 'undefined') {
    window.allSoal = window.serverData.allSoal || [];
    window.bankSoalList = window.serverData.bankSoalList || [];
}

// Open Bank Detail
window.openBankDetail = function(bankId, bankName) {
    window.currentBankId = bankId;
    document.getElementById('bankListView').classList.add('d-none');
    document.getElementById('bankListView').classList.add('hidden'); // legacy
    document.getElementById('bankDetailView').classList.remove('d-none');
    document.getElementById('bankDetailView').classList.add('active'); // legacy
    document.getElementById('detailBankTitle').textContent = bankName;
    
    // Hide Create Bank Button (Keep Tabs Visible)
    const btnCreate = document.getElementById('btnCreateBank');
    if(btnCreate) btnCreate.classList.add('d-none');

    // Load questions for this bank
    window.loadBankQuestions(bankId);
}

// Close Bank Detail
window.closeBankDetail = function() {
    window.currentBankId = null;
    document.getElementById('bankDetailView').classList.add('d-none');
    document.getElementById('bankDetailView').classList.remove('active'); // legacy
    document.getElementById('bankListView').classList.remove('d-none');
    document.getElementById('bankListView').classList.remove('hidden'); // legacy
    
    // Show Create Bank Button
    const btnCreate = document.getElementById('btnCreateBank');
    if(btnCreate) btnCreate.classList.remove('d-none');
}

// Load questions for specific bank
window.loadBankQuestions = function(bankId) {
    const soalList = document.getElementById('soalList');
    soalList.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Memuat soal...</p></div>';
    
    fetch(baseUrl + '/getBankQuestions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'bank_id=' + bankId
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.currentBankSoal = data.data || [];
            renderSoalList(window.currentBankSoal);
        } else {
            soalList.innerHTML = '<div class="empty-state"><i class="bx bx-error-circle"></i><h4>Gagal memuat soal</h4></div>';
        }
    })
    .catch((err) => {
        console.error('Error loading questions:', err);
        soalList.innerHTML = '<div class="empty-state"><i class="bx bx-error-circle"></i><h4>Terjadi kesalahan</h4></div>';
    });
}

// Render soal list
window.renderSoalList = function(soalArray) {
    const soalList = document.getElementById('soalList');
    
    if (!soalArray || soalArray.length === 0) {
        soalList.innerHTML = `
            <div class="text-center py-5">
                <i class='bx bx-file-blank text-muted fs-1 mb-3'></i>
                <h5 class="text-secondary">Belum Ada Soal</h5>
                <p class="text-muted small">Klik tombol "Tambah Soal" untuk menambahkan soal baru ke bank ini</p>
            </div>`;
        return;
    }
    
    let html = '';
    soalArray.forEach((soal, index) => {
        const isPG = (soal.status_soal || '') === 'pilihan_ganda';
        const optionsHtml = isPG && soal.pilihan ? window.renderOptions(soal.pilihan) : '';
        const borderColor = isPG ? 'border-primary' : 'border-warning';
        
        html += `
        <div class="card mb-3 shadow-sm border-start ${borderColor} border-4" data-id="${soal.id}" data-type="${soal.status_soal || 'essay'}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="badge bg-primary rounded-3 fs-5 px-3 py-2">${index + 1}</div>
                        <span class="badge rounded-pill ${isPG ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning'} px-3 py-2">
                            ${isPG ? 'Pilihan Ganda' : 'Essay'}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light text-primary border-0" onclick="window.editSoal(${soal.id})" title="Edit">
                            <i class='bx bx-edit'></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger border-0" onclick="window.deleteSoal(${soal.id})" title="Hapus">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 text-dark">${soal.deskripsi || ''}</div>
                ${optionsHtml}
                ${soal.jawaban ? `
                <div class="alert alert-success bg-success bg-opacity-10 border-success border-start border-3 d-flex align-items-center gap-2 mb-0">
                    <i class='bx bx-check-circle text-success fs-4'></i>
                    <div>
                        <div class="small fw-bold text-success text-uppercase">Jawaban Benar</div>
                        <div class="text-success">${window.escapeHtml(soal.jawaban)}</div>
                    </div>
                </div>` : ''}
            </div>
        </div>`;
    });
    
    soalList.innerHTML = html;
}

window.renderOptions = function(pilihan) {
    if (!pilihan) return '';
    
    // Handle HTML entities in the string
    const decodedPilihan = new DOMParser().parseFromString(pilihan, "text/html").documentElement.textContent;
    
    let options = [];
    const pattern = /([A-E])\.\s*(.*?)(?=(?:,\s*[A-E]\.)|$)/g;
    let match;
    
    // Try to parse structured options A. xxx, B. xxx
    while ((match = pattern.exec(decodedPilihan)) !== null) {
        options.push(match[1] + '. ' + match[2].trim());
    }
    
    // Fallback: simple split if no pattern match
    if (options.length === 0) {
        options = decodedPilihan.split(',').map(p => p.trim());
    }
    
    let html = '<div class="p-3 bg-light bg-opacity-50 rounded-3 mb-3"><div class="small fw-bold text-secondary text-uppercase mb-2">Pilihan Jawaban</div>';
    options.forEach(opt => {
        html += `<div class="py-1 text-dark">${escapeHtml(opt)}</div>`;
    });
    html += '</div>';
    return html;
}

window.escapeHtml = function(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Refresh Bank Dropdowns in Import/Export Tab


// Update Dashboard Statistics Real-time
window.updateDashboardStats = function(type, change) {
    const ids = {
        'bank': 'stat-count-bank',
        'total': 'stat-count-total',
        'pg': 'stat-count-pg',
        'essay': 'stat-count-essay'
    };
    
    const element = document.getElementById(ids[type]);
    if (element) {
        let currentVal = parseInt(element.textContent) || 0;
        let newVal = currentVal + change;
        
        // Ensure non-negative
        if (newVal < 0) newVal = 0;
        
        // Animate change
        element.style.transform = 'scale(1.2)';
        element.style.color = '#3b82f6';
        element.style.transition = 'all 0.2s ease';
        
        setTimeout(() => {
            element.textContent = newVal;
            element.style.transform = 'scale(1)';
            element.style.color = '';
        }, 200);
    }
}

// Delete Bank with Real-time Card Removal
window.deleteBank = function(bankId) {
    showConfirmDelete(function() {
        
        fetch(baseUrl + '/deleteBank', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + bankId
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Bank soal berhasil dihapus!');
                
                try {
                    // Update stats
                    if (typeof window.updateDashboardStats === 'function') {
                        updateDashboardStats('bank', -1);
                    }

                    // Remove from dropdowns
                    if (typeof window.removeBankFromDropdowns === 'function') {
                        removeBankFromDropdowns(bankId);
                    }
                    
                    // Remove card from DOM
                    const cardContainer = document.getElementById('bank-card-' + bankId);
                    if (cardContainer) {
                        // Add fade-out animation
                        cardContainer.style.transition = 'all 0.3s ease';
                        cardContainer.style.opacity = '0';
                        cardContainer.style.transform = 'scale(0.9)';
                        
                        setTimeout(() => {
                            cardContainer.remove();
                            
                            // Check if grid is empty
                            const grid = document.getElementById('bankGrid');
                            if (grid) {
                                if (grid.children.length === 0) {
                                    // Add empty state if needed
                                    const listView = document.getElementById('bankListView');
                                    if (listView && !listView.querySelector('.text-center')) {
                                        const emptyState = document.createElement('div');
                                        emptyState.className = 'text-center py-5';
                                        emptyState.innerHTML = `
                                            <i class='bx bx-folder-open text-muted' style="font-size: 5rem;"></i>
                                            <h4 class="mt-3 text-muted">Belum Ada Bank Soal</h4>
                                            <p class="text-muted">Klik tombol "Buat Bank Soal Baru" untuk membuat bank soal pertama</p>
                                        `;
                                        listView.insertBefore(emptyState, grid);
                                    }
                                }
                            }
                        }, 300);
                    }
                } catch (uiError) {
                    console.error('Error updating UI after delete:', uiError);
                }
                
            } else {
                showAlert(data.message || 'Gagal menghapus bank soal', false);
            }
        })
        .catch((err) => {
            console.error('Error deleting bank:', err);
            showAlert('Terjadi kesalahan', false);
        });
    }, 'Apakah Anda yakin ingin menghapus bank soal ini? Semua soal di dalamnya akan ikut terhapus.');
}

// Close Bank Detail
window.closeBankDetail = function() {
    window.currentBankId = null;
    window.currentBankSoal = [];
    document.getElementById('bankDetailView').classList.add('d-none');
    document.getElementById('bankDetailView').classList.remove('active');
    document.getElementById('bankListView').classList.remove('d-none');
    document.getElementById('bankListView').classList.remove('hidden');
}

// Global Base URL for JS (avoid redeclaration if already defined)
if (typeof baseUrl === 'undefined' && window.appUrl) {
    var baseUrl = window.appUrl;
}

// Initialize Event Listeners
(function() {
    // Type Selector for Add Modal
    document.querySelectorAll('#addSoalModal .type-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('#addSoalModal .type-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            const type = this.dataset.type;
            document.getElementById('soalType').value = type;
            
            // Show/hide pilihan ganda fields
            const isPG = type === 'pilihan_ganda';
            document.getElementById('pilihanContainer').style.display = isPG ? 'block' : 'none';
            document.getElementById('jawabanPGContainer').style.display = isPG ? 'block' : 'none';
            document.getElementById('jawabanEssayContainer').style.display = isPG ? 'none' : 'block';
            
            // Toggle required attributes
            document.querySelectorAll('#pilihanContainer input[name^="pilihan_"]').forEach((input, idx) => {
                if (idx < 4) input.required = isPG; // A, B, C, D required
            });
            document.querySelector('#jawabanPGContainer input[name="jawaban"]').required = isPG;
        });
    });

    // Type Selector for Edit Modal
    document.querySelectorAll('#editSoalModal .type-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('#editSoalModal .type-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            const type = this.dataset.type;
            document.getElementById('editSoalType').value = type;
            
            // Show/hide pilihan ganda fields
            const isPG = type === 'pilihan_ganda';
            document.getElementById('editPilihanContainer').style.display = isPG ? 'block' : 'none';
            document.getElementById('editJawabanPGContainer').style.display = isPG ? 'block' : 'none';
            document.getElementById('editJawabanEssayContainer').style.display = isPG ? 'none' : 'block';
        });
    });

    // Filter Buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            window.filterSoal();
        });
    });

    // Search
    const searchInput = document.getElementById('searchSoal');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            window.filterSoal();
        });
    }

    // Form Submit - Add Soal
    const addSoalForm = document.getElementById('addSoalForm');
    if (addSoalForm) {
        addSoalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!window.currentBankId) {
                showAlert('Error: Bank Soal ID tidak ditemukan', false);
                return;
            }
            
            // Manual construction to handle fields properly based on type
            const formData = new FormData(this);
            const type = formData.get('status_soal');
            
            // Construct pilihan string for PG
            let pilihanStr = '';
            if (type === 'pilihan_ganda') {
                const opts = [];
                ['A', 'B', 'C', 'D', 'E'].forEach(opt => {
                    const val = formData.get('pilihan_' + opt);
                    if (val) opts.push(`${opt}. ${val}`);
                });
                pilihanStr = opts.join(', ');
            } else {
                // For essay, use 'jawaban_essay' as 'jawaban'
                const jawEssay = formData.get('jawaban_essay');
                formData.set('jawaban', jawEssay);
            }
            
            const dataToSend = new URLSearchParams();
            dataToSend.append('bank_id', window.currentBankId);
            dataToSend.append('deskripsi', formData.get('deskripsi'));
            dataToSend.append('status_soal', type);
            dataToSend.append('pilihan', pilihanStr);
            dataToSend.append('jawaban', formData.get('jawaban'));
            
            fetch(baseUrl + '/saveSoal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: dataToSend
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Soal berhasil ditambahkan!');
                    bootstrap.Modal.getInstance(document.getElementById('addSoalModal')).hide();
                    this.reset();
                    window.loadBankQuestions(window.currentBankId);
                    
                    // Update stats
                    updateDashboardStats('total', 1);
                    if(type === 'pilihan_ganda') updateDashboardStats('pg', 1);
                    else updateDashboardStats('essay', 1);
                    
                } else {
                    showAlert(data.message || 'Gagal menambahkan soal', false);
                }
            })
            .catch((err) => {
                console.error('Error adding soal:', err);
                showAlert('Terjadi kesalahan', false);
            });
        });
    }

    // Form Submit - Edit Soal
    const editSoalForm = document.getElementById('editSoalForm');
    if (editSoalForm) {
        editSoalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const type = formData.get('status_soal');
            const id = document.getElementById('editSoalId').value;
            
            // Construct pilihan string for PG
            let pilihanStr = '';
            if (type === 'pilihan_ganda') {
                const opts = [];
                ['A', 'B', 'C', 'D', 'E'].forEach(opt => {
                    const val = formData.get('pilihan_' + opt);
                    if (val) opts.push(`${opt}. ${val}`);
                });
                pilihanStr = opts.join(', ');
            } else {
                const jawEssay = formData.get('jawaban_essay');
                formData.set('jawaban', jawEssay);
            }
            
            const dataToSend = new URLSearchParams();
            dataToSend.append('id', id);
            dataToSend.append('deskripsi', formData.get('deskripsi'));
            dataToSend.append('status_soal', type);
            dataToSend.append('pilihan', pilihanStr);
            dataToSend.append('jawaban', formData.get('jawaban'));
            
            fetch(baseUrl + '/updateSoal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: dataToSend
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Soal berhasil diupdate!');
                    bootstrap.Modal.getInstance(document.getElementById('editSoalModal')).hide();
                    window.loadBankQuestions(window.currentBankId);
                } else {
                    showAlert(data.message || 'Gagal mengupdate soal', false);
                }
            })
            .catch((err) => {
                console.error('Error updating soal:', err);
                showAlert('Terjadi kesalahan', false);
            });
        });
    }

    // Form Submit - Edit Bank
    const editBankForm = document.getElementById('editBankForm');
    if (editBankForm) {
        editBankForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = document.getElementById('editBankId').value;
            
            fetch(baseUrl + '/updateBank', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Bank soal berhasil diupdate!');
                    bootstrap.Modal.getInstance(document.getElementById('editBankModal')).hide();
                    
                    // Update local list
                    const bankIdx = window.bankSoalList.findIndex(b => b.id == id);
                    if (bankIdx >= 0) {
                        window.bankSoalList[bankIdx].nama = formData.get('nama');
                        window.bankSoalList[bankIdx].deskripsi = formData.get('deskripsi');
                        window.bankSoalList[bankIdx].token = formData.get('token');
                    }
                    
                    // Update UI if in list view
                    const cardTitle = document.querySelector(`#bank-card-${id} .fw-bold`);
                    const cardDesc = document.querySelector(`#bank-card-${id} .text-secondary.small`);
                    if (cardTitle) cardTitle.textContent = formData.get('nama');
                    if (cardDesc) cardDesc.textContent = formData.get('deskripsi');
                    
                    // Update UI if in detail view
                    if (window.currentBankId == id) {
                        document.getElementById('detailBankTitle').textContent = formData.get('nama');
                    }
                    
                } else {
                    showAlert(data.message || 'Gagal mengupdate bank soal', false);
                }
            })
            .catch((err) => {
                console.error('Error updating bank:', err);
                showAlert('Terjadi kesalahan', false);
            });
        });
    }

    // Form Submit - Create Bank
    const createBankForm = document.getElementById('createBankForm');
    if (createBankForm) {
        createBankForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const nama = formData.get('nama_bank');
            const deskripsi = formData.get('deskripsi_bank');
            const token = formData.get('token_bank');
            
            fetch(baseUrl + '/createBank', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'nama=' + encodeURIComponent(nama) + 
                    '&deskripsi=' + encodeURIComponent(deskripsi) +
                    '&token=' + encodeURIComponent(token)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Bank soal berhasil dibuat!');
                    bootstrap.Modal.getInstance(document.getElementById('createBankModal')).hide();
                    
                    // Remove empty state if exists
                    const listView = document.getElementById('bankListView');
                    const emptyState = listView ? listView.querySelector('.text-center.py-5') : null;
                    if (emptyState && emptyState.textContent.includes('Belum Ada Bank Soal')) {
                        emptyState.remove();
                    }

                    // Create new card HTML (Bootstrap 5)
                    const newId = data.id || Date.now();
                    const newCard = document.createElement('div');
                    newCard.className = 'col-md-6 col-lg-4 col-xl-3';
                    newCard.id = `bank-card-${newId}`;
                    newCard.innerHTML = `
                        <div class="card h-100 border-0 rounded-4 hover-card overflow-hidden">
                                <!-- Card Cover Image -->
                                <div class="card-cover position-relative" style="height: 120px; background-color: #2563eb; background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.1) 0px, rgba(255,255,255,0.1) 2px, transparent 2px, transparent 10px);">
                                    <div class="position-absolute top-0 end-0 p-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-white p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-dots-horizontal-rounded fs-4'></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:void(0)" onclick="window.editBankModal(${newId})">
                                                        <i class='bx bx-edit text-primary'></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="javascript:void(0)" onclick="deleteBank(${newId})">
                                                        <i class='bx bx-trash'></i> Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body pt-0 px-4 pb-4 position-relative">
                                    
                                    <div class="mt-4 pt-2 cursor-pointer" onclick="openBankDetail(${newId}, '${escapeHtml(nama)}')">
                                        <h5 class="fw-bold text-dark mb-1 text-truncate" title="${escapeHtml(nama)}">
                                            ${escapeHtml(nama)}
                                        </h5>
                                        <p class="text-secondary small mb-3 text-truncate-2" style="min-height: 40px;">
                                            ${escapeHtml(deskripsi || 'Tidak ada deskripsi')}
                                        </p>
                                        
                                        <div class="d-flex gap-2 flex-wrap mb-2">
                                            <span class="badge rounded-pill text-white px-3 py-2 border-0" style="background-color: #ff5252;">
                                                <i class='bx bx-file me-1'></i> 0 Soal
                                            </span>
                                            <span class="badge rounded-pill text-white px-3 py-2 border-0" style="background-color: #448aff;" title="Pilihan Ganda">
                                                PG: 0
                                            </span>
                                            <span class="badge rounded-pill text-dark px-3 py-2 border-0" style="background-color: #ffd740;" title="Essay">
                                                Essay: 0
                                            </span>
                                        </div>
                                        <div class="mb-0">
                                            <span class="badge rounded-pill text-dark px-3 py-2 border-0" style="background-color: #69f0ae;">
                                                <i class='bx bx-key me-1'></i> ${escapeHtml(token)}
                                            </span>
                                        </div>
                                        
                                        <div class="d-none">
                                                <input class="form-check-input" type="checkbox" id="activeSwitch_${newId}" 
                                                onchange="window.activateBank(${newId})">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    `;
                    
                    // Append to grid
                    const grid = document.getElementById('bankGrid');
                    if(grid) {
                        grid.insertBefore(newCard, grid.firstChild);
                    }
                    
                    // Refresh import/export dropdowns in real-time
                    if (window.refreshBankDropdowns) {
                        refreshBankDropdowns(newId, nama, 0);
                    }
                    
                    // Update Dashboard Statistics
                    updateDashboardStats('bank', 1);
                    
                    this.reset();
                } else {
                    showAlert(data.message || 'Gagal membuat bank soal', false);
                }
            })
            .catch((err) => {
                console.error('Error create bank:', err);
                showAlert('Terjadi kesalahan', false);
            });
        });
    }



})();

// Question Type Selection Handler
(function() {
    const typeOptions = document.querySelectorAll('.type-option');
    const soalTypeInput = document.getElementById('soalType');
    const pilihanContainer = document.getElementById('pilihanContainer');
    const jawabanPGContainer = document.getElementById('jawabanPGContainer');
    const jawabanEssayContainer = document.getElementById('jawabanEssayContainer');
    
    typeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const selectedType = this.getAttribute('data-type');
            
            // Remove selected class from all options
            typeOptions.forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.check-icon').classList.add('d-none');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
            this.querySelector('.check-icon').classList.remove('d-none');
            
            // Update hidden input
            if (soalTypeInput) {
                soalTypeInput.value = selectedType;
            }
            
            // Show/hide appropriate containers
            if (selectedType === 'essay') {
                // Hide multiple choice fields
                if (pilihanContainer) pilihanContainer.style.display = 'none';
                if (jawabanPGContainer) jawabanPGContainer.style.display = 'none';
                
                // Show essay field
                if (jawabanEssayContainer) jawabanEssayContainer.style.display = 'block';
                
                // Remove required from pilihan fields
                document.querySelectorAll('[name^="pilihan_"]').forEach(input => {
                    input.removeAttribute('required');
                });
                document.querySelectorAll('[name="jawaban"]').forEach(input => {
                    input.removeAttribute('required');
                });
            } else {
                // Show multiple choice fields
                if (pilihanContainer) pilihanContainer.style.display = 'block';
                if (jawabanPGContainer) jawabanPGContainer.style.display = 'block';
                
                // Hide essay field
                if (jawabanEssayContainer) jawabanEssayContainer.style.display = 'none';
                
                // Add required to pilihan A-D
                ['a', 'b', 'c', 'd'].forEach(opt => {
                    const input = document.querySelector(`[name="pilihan_${opt}"]`);
                    if (input) input.setAttribute('required', 'required');
                });
                
                // Add required to jawaban radio
                document.querySelectorAll('[name="jawaban"]').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            }
        });
    });
})();

// Add Soal Form Handler
(function() {
    const addSoalForm = document.getElementById('addSoalForm');
    if (addSoalForm) {
        addSoalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            const formData = new FormData(this);
            
            // Handle essay type - copy jawaban_essay to jawaban and clear pilihan
            const soalType = formData.get('status_soal');
            if (soalType === 'essay') {
                const essayAnswer = formData.get('jawaban_essay');
                formData.set('jawaban', essayAnswer || '');
                formData.delete('jawaban_essay');
                
                // For essay, set pilihan to empty string
                formData.set('pilihan', 'bukan soal pilihan');
                
                // Remove individual pilihan fields
                formData.delete('pilihan_a');
                formData.delete('pilihan_b');
                formData.delete('pilihan_c');
                formData.delete('pilihan_d');
                formData.delete('pilihan_e');
            } else {
                // For multiple choice, combine pilihan fields
                const pilihanArray = [];
                ['a', 'b', 'c', 'd', 'e'].forEach(opt => {
                    const val = formData.get('pilihan_' + opt);
                    if (val && val.trim()) {
                        pilihanArray.push(opt.toUpperCase() + '. ' + val.trim());
                    }
                });
                formData.set('pilihan', pilihanArray.join(', '));
                
                // Remove individual pilihan fields
                formData.delete('pilihan_a');
                formData.delete('pilihan_b');
                formData.delete('pilihan_c');
                formData.delete('pilihan_d');
                formData.delete('pilihan_e');
            }
            
            formData.append('bank_id', window.currentBankId);

            fetch(baseUrl + '/addingsoal', {
                method: 'POST',
                body: formData 
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                if (data.status === 'success' || data.success === true) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSoalModal'));
                    if (modal) modal.hide();
                    this.reset();
                    showAlert('Soal berhasil ditambahkan!', true);
                    if (window.currentBankId) {
                        window.loadBankQuestions(window.currentBankId);
                    }
                } else {
                    showAlert(data.message || 'Gagal menyimpan soal', false);
                }
            })
            .catch(err => {
                console.error('Error adding soal:', err);
                showAlert('Terjadi kesalahan saat menyimpan soal', false);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }
})();

// Import Soal

