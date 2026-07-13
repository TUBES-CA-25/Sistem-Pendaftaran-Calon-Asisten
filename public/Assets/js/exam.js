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
    document.getElementById('bankListView').classList.add('hidden');
    document.getElementById('bankListView').classList.add('hidden'); // legacy
    document.getElementById('bankDetailView').classList.remove('hidden');
    document.getElementById('bankDetailView').classList.add('active'); // legacy
    document.getElementById('detailBankTitle').textContent = bankName;
    
    // Hide Create Bank Button (Keep Tabs Visible)
    const btnCreate = document.getElementById('btnCreateBank');
    if(btnCreate) btnCreate.classList.add('hidden');

    // Load questions for this bank
    window.loadBankQuestions(bankId);
}

// Close Bank Detail
window.closeBankDetail = function() {
    window.currentBankId = null;
    document.getElementById('bankDetailView').classList.add('hidden');
    document.getElementById('bankDetailView').classList.remove('active'); // legacy
    document.getElementById('bankListView').classList.remove('hidden');
    document.getElementById('bankListView').classList.remove('hidden'); // legacy
    
    // Show Create Bank Button
    const btnCreate = document.getElementById('btnCreateBank');
    if(btnCreate) btnCreate.classList.remove('hidden');
}

// Load questions for specific bank
window.loadBankQuestions = function(bankId) {
    const soalList = document.getElementById('soalList');
        soalList.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                <p class="text-slate-500 font-medium">Memuat soal...</p>
            </div>
        `;
    
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
            soalList.innerHTML = `
                <div class="bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 flex flex-col items-center justify-center text-center">
                    <i class="bx bx-error-circle text-4xl mb-2"></i>
                    <h4 class="font-bold">Gagal memuat soal</h4>
                </div>
            `;
        }
    })
    .catch((err) => {
        console.error('Error loading questions:', err);
        soalList.innerHTML = `
            <div class="bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 flex flex-col items-center justify-center text-center">
                <i class="bx bx-error-circle text-4xl mb-2"></i>
                <h4 class="font-bold">Terjadi kesalahan</h4>
            </div>
        `;
    });
}

// Render soal list
window.renderSoalList = function(soalArray) {
    const soalList = document.getElementById('soalList');
    
    // Update Question Count & Points
    const toggleJawaban = document.getElementById('toggleJawaban') ? document.getElementById('toggleJawaban').checked : true;
    
    if(document.getElementById('detailBankQuestionCount')) {
        const totalSoal = soalArray ? soalArray.length : 0;
        const totalPoin = totalSoal * 5;
        document.getElementById('detailBankQuestionCount').innerText = totalSoal;
        document.getElementById('detailBankPoints').innerText = totalPoin;
        if(document.getElementById('panelTotalPoints')) {
            document.getElementById('panelTotalPoints').innerText = totalPoin;
        }
    }
    
    if (!soalArray || soalArray.length === 0) {
        soalList.innerHTML = `
            <div class="text-center py-12 flex flex-col items-center">
                <i class='bx bx-file-blank text-slate-300 text-6xl mb-4'></i>
                <h5 class="text-slate-600 font-bold text-lg mb-1">Belum Ada Soal</h5>
                <p class="text-slate-400 text-sm max-w-sm">Klik tombol "Tambah Soal" untuk menambahkan soal baru ke bank ini</p>
            </div>`;
        return;
    }
    
    let html = '';
    soalArray.forEach((soal, index) => {
        const isPG = (soal.status_soal || '') === 'pilihan_ganda';
        
        // Pass toggle state & actual jawaban
        const optionsHtml = isPG && soal.pilihan ? window.renderOptions(soal.pilihan, soal.jawaban, toggleJawaban) : '';
        
        const questionType = isPG ? 'PILIHAN GANDA' : 'ESSAY';
        const points = 5; // Default 5 points per question as mock
        const timeLimit = '45 detik'; // Mock
        
        html += `
        <div class="bg-white border-b border-slate-100 last:border-0 p-6 sm:px-8 hover:bg-slate-50/50 transition duration-300 group" data-id="${soal.id}" data-type="${soal.status_soal || 'essay'}">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    ${index + 1}. ${questionType} &bull; ${timeLimit} &bull; ${points} poin
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition" onclick="window.editSoal(${soal.id})" title="Edit">
                        <i class='bx bx-edit'></i>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition" onclick="window.deleteSoal(${soal.id})" title="Hapus">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            </div>
            
            <!-- Question Content -->
            <div class="flex flex-col gap-4">
                <!-- Image if any -->
                ${soal.image_url ? `
                <div class="w-full">
                    <img src="${window.getImageUrl(soal.image_url)}" alt="Gambar Soal" class="max-w-full lg:max-w-2xl rounded-xl border border-slate-200 object-contain max-h-80" onerror="this.style.display='none'">
                </div>` : ''}
                
                <!-- Text -->
                <div class="w-full">
                    <div class="text-slate-800 text-[15px] font-medium leading-relaxed mb-4 condition-render-markdown">
                        ${soal.deskripsi ? marked.parse(soal.deskripsi) : ''}
                    </div>
                    ${optionsHtml}
                    ${(!isPG && toggleJawaban && soal.jawaban) ? `
                    <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1 flex items-center gap-1.5"><i class='bx bxs-check-circle'></i> Jawaban Benar</div>
                        <div class="text-emerald-800 text-sm font-medium">${window.escapeHtml(soal.jawaban)}</div>
                    </div>` : ''}
                </div>
            </div>
        </div>`;
    });
    
    soalList.innerHTML = html;
}

window.renderOptions = function(pilihan, jawaban, showJawaban) {
    if (!pilihan) return '';

    let options = [];
    
    try {
        // Try parsing as JSON first
        const parsed = JSON.parse(pilihan);
        if (typeof parsed === 'object' && !Array.isArray(parsed)) {
            options = Object.entries(parsed).map(([key, value]) => ({ key: key, value: value }));
        } else if (Array.isArray(parsed)) {
             options = parsed.map((val, idx) => ({ key: String.fromCharCode(65 + idx), value: val }));
        } else {
             throw new Error("Not an object/array");
        }
    } catch(e) {
        // Fallback to legacy parsing
        const decodedPilihan = new DOMParser().parseFromString(pilihan, "text/html").documentElement.textContent;
        const pattern = /([A-E])\.\s*(.*?)(?=(?:,\s*[A-E]\.)|$)/g;
        let match;

        while ((match = pattern.exec(decodedPilihan)) !== null) {
            options.push({
                key: match[1],
                value: match[2].trim()
            });
        }

        if (options.length === 0) {
            const parts = decodedPilihan.split(',').map(p => p.trim());
            options = parts.map((part, idx) => ({
                key: String.fromCharCode(65 + idx), 
                value: part
            }));
        }
    }

    // Determine the correct key
    let correctKey = null;
    if (jawaban) {
        const jwb = jawaban.trim().toUpperCase();
        options.forEach(opt => {
            if (jwb === opt.key.toUpperCase() || jwb.startsWith(opt.key.toUpperCase() + '.')) {
                correctKey = opt.key;
            }
        });
        if (!correctKey) {
            const matchJawaban = jwb.match(/^([A-E])/);
            if (matchJawaban) correctKey = matchJawaban[1];
        }
    }

    let html = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">';

    options.forEach(opt => {
        const isCorrect = showJawaban && opt.key === correctKey;
        const isImage = opt.value && (
            opt.value.includes('.jpg') ||
            opt.value.includes('.jpeg') ||
            opt.value.includes('.png') ||
            opt.value.includes('.gif') ||
            opt.value.includes('.webp')
        );
        
        // Styling based on correctness
        const borderClass = isCorrect ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 bg-white';
        const iconHtml = isCorrect 
            ? `<div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0"><i class='bx bx-check text-lg'></i></div>`
            : `<div class="w-6 h-6 rounded-full border-2 border-slate-300 shrink-0"></div>`;

        html += `
        <div class="flex items-start gap-3 p-3.5 rounded-xl border ${borderClass} transition-colors">
            ${iconHtml}
            <div class="flex-1">
                ${isImage 
                    ? `<img src="${window.getImageUrl(opt.value)}" class="max-w-full h-auto max-h-32 rounded-lg border border-slate-200" onerror="this.onerror=null; this.src='https://placehold.co/400x200?text=Gambar+Tidak+Ditemukan'; this.style.border='2px dashed #ff0000';">` 
                    : `<div class="text-[14px] text-slate-700 font-medium leading-snug break-words"><strong>${opt.key}.</strong> ${window.escapeHtml(opt.value)}</div>`
                }
            </div>
        </div>`;
    });

    html += '</div>';
    return html;
}

window.escapeHtml = function(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to get full image URL
window.getImageUrl = function(path) {
    if (!path) return '';
    // If path already starts with http or /, return as is
    if (path.startsWith('http') || path.startsWith('/')) {
        return path;
    }
    // Get base URL from baseUrl variable (defined in PHP)
    const base = typeof baseUrl !== 'undefined' ? baseUrl.replace('/public', '') : '';
    return base + '/' + path;
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
                showAlert('Bank soal berhasil dihapus!', true);
                
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
    document.getElementById('bankDetailView').classList.add('hidden');
    document.getElementById('bankDetailView').classList.remove('active');
    document.getElementById('bankListView').classList.remove('hidden');
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



    // Form Submit - Edit Soal
    const editSoalForm = document.getElementById('editSoalForm');
    if (editSoalForm) {
        editSoalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Sync EasyMDE to FormData immediately
            if (window.easyMDE_edit) {
                const desc = window.easyMDE_edit.value();
                formData.set('deskripsi', desc);
            }
            const type = formData.get('status_soal');
            const id = document.getElementById('editSoalId').value;
            
            // Construct pilihan string for PG
            let pilihanStr = '';
            if (type === 'pilihan_ganda') {
                const opts = [];
                ['a', 'b', 'c', 'd', 'e'].forEach(opt => {
                    const val = formData.get('pilihan_' + opt);
                    if (val) opts.push(`${opt.toUpperCase()}. ${val}`);
                });
                pilihanStr = opts.join(', ');
            } else {
                const jawEssay = formData.get('jawaban_essay');
                formData.set('jawaban', jawEssay);
            }
            
            // Set additional fields to formData
            formData.set('id', id);
            formData.set('pilihan', pilihanStr);

            // Send FormData directly to support file upload
            fetch(baseUrl + '/updatesoal', {
                method: 'POST',
                body: formData // Send as FormData, not URLSearchParams
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
                    showAlert('Bank soal berhasil diperbarui!', true);
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
                    showAlert('Bank soal berhasil dibuat!', true);
                    bootstrap.Modal.getInstance(document.getElementById('createBankModal')).hide();
                    
                    // Remove empty state if exists
                    const listView = document.getElementById('bankListView');
                    const emptyState = listView ? listView.querySelector('.text-center.py-5') : null;
                    if (emptyState && emptyState.textContent.includes('Belum Ada Bank Soal')) {
                        emptyState.remove();
                    }

                    // Create new card HTML (Tailwind CSS)
                    const newId = data.id || Date.now();
                    const newCard = document.createElement('div');
                    newCard.className = 'col-span-1';
                    newCard.id = `bank-card-${newId}`;
                    newCard.innerHTML = `
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition duration-200 overflow-hidden flex flex-col h-full">
                                <!-- Card Cover Pattern -->
                                <div class="h-28 bg-blue-600 relative shrink-0" style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.08) 0px, rgba(255,255,255,0.08) 2px, transparent 2px, transparent 10px);">
                                    <div class="absolute top-3 right-3">
                                        <div class="dropdown">
                                            <button class="w-8 h-8 flex items-center justify-center text-white hover:bg-white/20 rounded-lg transition border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-dots-horizontal-rounded text-xl'></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-xl p-1.5 mt-1 bg-white">
                                                <li>
                                                    <a class="dropdown-item flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 rounded-lg text-sm transition" href="javascript:void(0)" onclick="window.editBankModal(${newId})">
                                                        <i class='bx bx-edit text-blue-600 text-base'></i> <span class="font-medium">Edit</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm transition" href="javascript:void(0)" onclick="deleteBank(${newId})">
                                                        <i class='bx bx-trash text-base'></i> <span class="font-medium">Hapus</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-5 flex flex-col flex-grow">
                                    
                                    <div class="cursor-pointer flex-grow" onclick="openBankDetail(${newId}, '${escapeHtml(nama)}')">
                                        <h3 class="font-bold text-slate-800 text-lg mb-1 line-clamp-1" title="${escapeHtml(nama)}">
                                            ${escapeHtml(nama)}
                                        </h3>
                                        <p class="text-slate-400 text-xs mb-4 line-clamp-2 h-8 font-medium">
                                            ${escapeHtml(deskripsi || 'Tidak ada deskripsi')}
                                        </p>
                                        
                                        <div class="flex gap-2 flex-wrap mb-3">
                                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-600">
                                                <i class='bx bx-file mr-1 text-sm'></i> 0 Soal
                                            </span>
                                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600" title="Pilihan Ganda">
                                                PG: 0
                                            </span>
                                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600" title="Essay">
                                                Essay: 0
                                            </span>
                                        </div>
                                        <div class="mb-4">
                                            <span class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">
                                                <i class='bx bx-key mr-1 text-sm'></i> ${escapeHtml(token)}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center" onclick="event.stopPropagation()">
                                        <span class="text-xs font-bold text-slate-500">Status: 
                                            <span id="statusText_${newId}" class="text-red-500">Tidak Aktif</span>
                                        </span>
                                        <div class="form-check form-switch p-0 m-0 flex items-center">
                                            <input class="form-check-input bank-active-switch cursor-pointer w-9 h-5 bg-slate-200 checked:bg-blue-600 border-0 rounded-full appearance-none transition-colors" type="checkbox" id="activeSwitch_${newId}" 
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



// Edit Bank Modal Helper
window.editBankModal = function(id) {
    // Find bank data
    const bank = window.bankSoalList.find(b => b.id == id);
    if (!bank) {
        return;
    }

    // Populate Form
    document.getElementById('editBankId').value = bank.id;
    document.getElementById('editBankName').value = bank.nama;
    document.getElementById('editBankDesc').value = bank.deskripsi;
    document.getElementById('editBankToken').value = bank.token;

    // Show Modal
    const modal = new bootstrap.Modal(document.getElementById('editBankModal'));
    modal.show();
}

// Activate/Deactivate Bank
window.activateBank = function(bankId) {
    const switchEl = document.getElementById('activeSwitch_' + bankId);
    const isActive = switchEl.checked;
    
    // Disable switch temporarily
    switchEl.disabled = true;
    
    const endpoint = isActive ? '/activateBank' : '/deactivateBank';
    
    fetch(baseUrl + endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + bankId
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Server returned ' + res.status);
        }
        return res.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('SERVER RESPONSE:', text);
                throw new Error('Invalid JSON response');
            }
        });
    })
    .then(data => {
        if (data.status === 'success') {
            const statusText = document.getElementById('statusText_' + bankId);
            if (isActive) {
                statusText.innerText = 'Aktif';
                statusText.classList.remove('text-danger');
                statusText.classList.add('text-success');
            } else {
                statusText.innerText = 'Tidak Aktif';
                statusText.classList.remove('text-success');
                statusText.classList.add('text-danger');
            }
        } else {
            showAlert(data.message || 'Gagal mengubah status', false);
            // Revert state on failure
            switchEl.checked = !isActive;
        }
    })
    .catch((err) => {
        console.error('Error changing bank status:', err);
        showAlert('Terjadi kesalahan', false);
        // Revert state on error
        switchEl.checked = !isActive;
    })
    .finally(() => {
        switchEl.disabled = false;
    });
}


// Make helper functions global
window.editSoal = function(id) {
    const card = document.querySelector(`.card[data-id="${id}"]`);
    if(!card) return;
    
    // Find the original data object in memory if possible
    let soalData = null;
    if (window.currentBankSoal) {
        soalData = window.currentBankSoal.find(s => s.id == id);
    }
    
    // If not found in memory (shouldn't happen), try to parse from DOM (fallback)
    if (!soalData) {
        // ... (fallback implementation if needed, but for now rely on memory)
        return; 
    }

    document.getElementById('editSoalId').value = soalData.id;
    
    // Set type
    const type = soalData.status_soal || 'pilihan_ganda';
    
    // Trigger click on appropriate type option to switch view
    const typeOption = document.querySelector(`#editSoalModal .type-option[data-type="${type}"]`);
    if(typeOption) typeOption.click();

    // Set description
    if (window.easyMDE_edit) {
        window.easyMDE_edit.value(soalData.deskripsi || '');
    } else {
        document.getElementById('editDeskripsi').value = soalData.deskripsi || '';
    }
    
    // Set existing image if available
    const existingImageUrl = soalData.image_url || null;
    document.getElementById('existingImageUrl').value = existingImageUrl || '';

    // Show existing image preview
    const editImagePreview = document.getElementById('editImagePreview');
    const editPreviewImg = document.getElementById('editPreviewImg');

    if (existingImageUrl && existingImageUrl.trim() !== '') {
        editPreviewImg.src = existingImageUrl;
        editImagePreview.style.display = 'block';
    } else {
        editImagePreview.style.display = 'none';
        editPreviewImg.src = '';
    }

    // Reset file input
    document.getElementById('soalImageEditInput').value = '';

    // Set answers
    if (type === 'pilihan_ganda') {
        const pilihanContainer = document.getElementById('editPilihanContainer');
        const pilihanStr = soalData.pilihan || '';

        // Parse "A. xxx, B. xxx" format
        const pattern = /([A-E])\.\s*(.*?)(?=(?:,\s*[A-E]\.)|$)/g;
        let match;
        // reset first
        ['A','B','C','D','E'].forEach(opt => {
            document.getElementById('editPilihan'+opt).value = '';
        });

        while ((match = pattern.exec(pilihanStr)) !== null) {
            const opt = match[1];
            const val = match[2].trim();
            const input = document.getElementById('editPilihan' + opt);
            if(input) input.value = val;
        }

        // Check correct answer
        const jawab = soalData.jawaban;
        const radio = document.querySelector(`input[name="jawaban"][value="${jawab}"]`);
        if(radio) radio.checked = true;

    } else {
        document.getElementById('editJawabanEssay').value = soalData.jawaban || '';
    }

    const modal = new bootstrap.Modal(document.getElementById('editSoalModal'));
    modal.show();
}

window.deleteSoal = function(id) {
    showConfirmDelete(function() {
        fetch(baseUrl + '/deletesoal', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Soal berhasil dihapus!');
                // Update stats
                const card = document.querySelector(`.card[data-id="${id}"]`);
                if(card) {
                    const type = card.dataset.type;
                    updateDashboardStats('total', -1);
                    if(type === 'pilihan_ganda') updateDashboardStats('pg', -1);
                    else updateDashboardStats('essay', -1);
                    card.remove();
                }
                
                // Reload to be safe or rely on DOM removal
                // window.loadBankQuestions(window.currentBankId); 
            } else {
                showAlert(data.message || 'Gagal menghapus soal', false);
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('Terjadi kesalahan', false);
        });
    }, 'Apakah Anda yakin ingin menghapus soal ini?');
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
                opt.querySelector('.check-icon').classList.add('hidden');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
            this.querySelector('.check-icon').classList.remove('hidden');
            
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

            // Sync EasyMDE to FormData immediately
            if (window.easyMDE_add) {
                const desc = window.easyMDE_add.value();
                formData.set('deskripsi', desc);
            }
            
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

