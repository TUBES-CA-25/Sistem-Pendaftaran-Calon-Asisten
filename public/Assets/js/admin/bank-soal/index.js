/**
 * admin/bank-soal/index.js
 *
 * LOGIKA UTAMA halaman Bank Soal: kelola bank soal dan isinya.
 * Membuka rincian bank, memuat daftar soal, tambah/ubah/hapus bank,
 * tambah/ubah/hapus soal, aktifkan-nonaktifkan bank, dan filter.
 *
 * Pembagian berkas di halaman ini:
 *   bank-soal.js               <- INI: aksi data (CRUD bank & soal)
 *   bank-soal-editor.js        editor teks soal (EasyMDE) + pratinjau gambar
 *   bank-soal-import-export.js tab Import & Export berkas Excel/CSV
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
    if(document.getElementById('pageHeaderWrapper')) document.getElementById('pageHeaderWrapper').classList.add('hidden');
    // Sembunyikan tab Daftar/Import saat masuk rincian satu bank - di sini
    // pengguna sedang mengelola isi bank, bukan berpindah antar bagian.
    if(document.getElementById('soalTabs')) document.getElementById('soalTabs').classList.add('hidden');
    document.getElementById('detailBankTitle').textContent = bankName;
    
    // Update badge status based on bank data
    const bankData = window.bankSoalList ? window.bankSoalList.find(b => b.id == bankId) : null;
    // Durasi kartu status sebelumnya ditulis mati "45" di markup, jadi selalu
    // salah untuk bank yang durasinya bukan 45 menit. Ambil dari data bank.
    const elDurasi = document.getElementById('panelDurasi');
    if (elDurasi) elDurasi.textContent = (bankData && bankData.durasi) ? bankData.durasi : 45;

    const badge = document.getElementById('detailBankStatusBadge');
    if (badge && bankData) {
        if (bankData.is_active == 1) {
            badge.textContent = 'AKTIF';
            badge.className = 'bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-md transition-colors';
        } else {
            badge.textContent = 'NON-AKTIF';
            badge.className = 'bg-slate-100 text-slate-500 text-[10px] font-bold px-2 py-1 rounded-md transition-colors';
        }
    }
    

    // Hide Create Bank Button (Keep Tabs Visible)
    const btnCreate = document.getElementById('btnCreateBank');
    if(btnCreate) btnCreate.classList.add('hidden');

    // Load questions for this bank
    window.loadBankQuestions(bankId);
}

// Close Bank Detail
window.closeBankDetail = function() {
    window.currentBankId = null;
    window.currentBankSoal = [];
    document.getElementById('bankDetailView').classList.add('hidden');
    document.getElementById('bankDetailView').classList.remove('active'); // legacy
    document.getElementById('bankListView').classList.remove('hidden');
    document.getElementById('bankListView').classList.remove('hidden'); // legacy
    if(document.getElementById('pageHeaderWrapper')) document.getElementById('pageHeaderWrapper').classList.remove('hidden');
    if(document.getElementById('soalTabs')) document.getElementById('soalTabs').classList.remove('hidden');
    
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
    
    fetch(baseUrl + '/getBankQuestionsHtml', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'bank_id=' + bankId
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.currentBankSoal = data.data || [];
            soalList.innerHTML = data.html || '';

            // Reset filter ke "Semua" tiap ganti bank. Tanpa ini, filter yang
            // masih aktif dari bank sebelumnya bisa menyembunyikan seluruh soal.
            document.querySelectorAll('.filter-btn').forEach(function (b) {
                b.classList.toggle('active', (b.dataset.filter || 'all') === 'all');
            });

            // Re-render markdown if available
            if (typeof marked !== 'undefined') {
                soalList.querySelectorAll('.condition-render-markdown').forEach(el => {
                    const rawMd = el.textContent;
                    const dest = el.nextElementSibling;
                    if (dest && dest.classList.contains('markdown-rendered-content')) {
                        dest.innerHTML = marked.parse(rawMd);
                    }
                });
            }

            // Update stats
            const bankData = window.bankSoalList ? window.bankSoalList.find(b => b.id == bankId) : null;
            const poinPerSoal = bankData && bankData.poin_per_soal ? parseInt(bankData.poin_per_soal) : 10;
            
            const totalSoal = window.currentBankSoal.length;
            const totalPoin = totalSoal * poinPerSoal;
            if(document.getElementById('detailBankQuestionCount')) {
                document.getElementById('detailBankQuestionCount').innerText = totalSoal;
                document.getElementById('detailBankPoints').innerText = totalPoin;
            }
            if(document.getElementById('panelTotalPoints')) {
                document.getElementById('panelTotalPoints').innerText = totalPoin;
            }

        } else {
            soalList.innerHTML = data.html || `
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


// escapeHtml() dihapus: Tidak dipakai; penyusunan HTML di berkas ini memakai template literal langsung.

/**
 * Saring daftar soal berdasarkan tombol filter yang aktif.
 *
 * Sebelumnya fungsi ini DIPANGGIL tapi tidak pernah didefinisikan, sehingga tiap
 * klik tombol filter melempar "TypeError: window.filterSoal is not a function".
 *
 * Kontrak markup:
 *   - tombol : .filter-btn dengan data-filter="all|pilihan_ganda|essay"
 *              (app/View/admin/ujian/index.php)
 *   - baris  : anak langsung #soalList dengan data-type="<status_soal>"
 *              (app/View/admin/ujian/partials/soal_list.php)
 */
window.filterSoal = function() {
    const list = document.getElementById('soalList');
    if (!list) return;

    const activeBtn = document.querySelector('.filter-btn.active');
    const filter = activeBtn ? (activeBtn.dataset.filter || 'all') : 'all';

    const rows = list.querySelectorAll('[data-type]');
    let shown = 0;

    rows.forEach(row => {
        const match = (filter === 'all') || (row.dataset.type === filter);
        row.classList.toggle('hidden', !match);
        if (match) shown++;
    });

    // Pesan "tidak ada hasil" — dibuat sekali lalu dipakai ulang
    let empty = list.querySelector('[data-filter-empty]');
    if (shown === 0 && rows.length > 0) {
        if (!empty) {
            empty = document.createElement('div');
            empty.setAttribute('data-filter-empty', '');
            empty.className = 'py-12 text-center text-slate-400';
            empty.innerHTML =
                '<i class="bi bi-funnel text-4xl block mb-2 opacity-50"></i>' +
                '<span class="text-sm font-medium">Tidak ada soal untuk filter ini</span>';
            list.appendChild(empty);
        }
        empty.classList.remove('hidden');
    } else if (empty) {
        empty.classList.add('hidden');
    }
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
            document.querySelectorAll('#jawabanPGContainer input[name="jawaban"]').forEach(input => input.required = isPG);
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

    // Filter Buttons.
    // Delegasi di document: daftar soal (#soalList) di-render ulang lewat
    // innerHTML oleh loadBankQuestions(), jadi listener yang diikat langsung
    // ke elemen akan hilang tiap ganti bank.
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        window.filterSoal();
    });


    // Form Submit - Edit Soal
    const editSoalForm = document.getElementById('editSoalForm');
    if (editSoalForm) {
        editSoalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Sync EasyMDE to FormData immediately and validate
            if (window.easyMDE_edit) {
                const desc = window.easyMDE_edit.value();
                if (!desc.trim()) {
                    showAlert('Pertanyaan tidak boleh kosong', false);
                    return;
                }
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
                    UI.modal.close(document.getElementById('editSoalModal'));
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
                    UI.modal.close(document.getElementById('editBankModal'));
                    
                    // Update local list
                    const bankIdx = window.bankSoalList.findIndex(b => b.id == id);
                    if (bankIdx >= 0) {
                        window.bankSoalList[bankIdx].nama = formData.get('nama');
                        window.bankSoalList[bankIdx].deskripsi = formData.get('deskripsi');
                        window.bankSoalList[bankIdx].token = formData.get('token');
                        window.bankSoalList[bankIdx].durasi = formData.get('durasi');
                        window.bankSoalList[bankIdx].poin_per_soal = formData.get('poin_per_soal');
                    }
                    
                    // Segarkan nama di baris tabel.
                    //
                    // Dulu memakai `.fw-bold` dan `.text-secondary.small` - keduanya
                    // kelas Bootstrap yang sudah tidak ada sejak tampilan pindah ke
                    // Tailwind, jadi nama tidak pernah ikut berubah sampai halaman
                    // dimuat ulang. Sekarang memakai penanda khusus `.bank-nama`
                    // yang tidak ikut berubah bila gayanya diubah lagi nanti.
                    // Deskripsi tidak disegarkan karena tabel tidak menampilkannya.
                    const cardTitle = document.querySelector(`#bank-card-${id} .bank-nama`);
                    if (cardTitle) cardTitle.textContent = formData.get('nama');
                    
                    // Update UI if in detail view
                    if (window.currentBankId == id) {
                        document.getElementById('detailBankTitle').textContent = formData.get('nama');
                        const elDurasi = document.getElementById('panelDurasi');
                        if (elDurasi) elDurasi.textContent = formData.get('durasi') || 45;
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
            const durasi = formData.get('durasi_bank');
            const poin = formData.get('poin_bank');
            
            fetch(baseUrl + '/createBank', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'nama=' + encodeURIComponent(nama) + 
                    '&deskripsi=' + encodeURIComponent(deskripsi) +
                    '&token=' + encodeURIComponent(token) +
                    '&durasi=' + encodeURIComponent(durasi) +
                    '&poin_per_soal=' + encodeURIComponent(poin)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('Bank soal berhasil dibuat!', true);
                    UI.modal.close(document.getElementById('createBankModal'));
                    
                    // Remove empty state if exists
                    const listView = document.getElementById('bankListView');
                    const emptyState = listView ? listView.querySelector('.text-center.py-5') : null;
                    if (emptyState && emptyState.textContent.includes('Belum Ada Bank Soal')) {
                        emptyState.remove();
                    }

                    // Reload page using SPA logic to fetch updated HTML from server
                    if (typeof window.loadPage === 'function') {
                        const currentPage = _currentPage;
                        _currentPage = null; // force reload
                        window.loadPage(currentPage || 'admin/ujian', false);
                    }
                    
                    // Refresh import/export dropdowns in real-time
                    if (window.refreshBankDropdowns) {
                        refreshBankDropdowns(data.id, nama, 0);
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
    document.getElementById('editBankDesc').value = bank.deskripsi || '';
    document.getElementById('editBankToken').value = bank.token || '';
    document.getElementById('editBankDurasi').value = bank.durasi || 45;
    document.getElementById('editBankPoin').value = bank.poin_per_soal || 10;

    // Show Modal
    const modal = UI.modal.ref(document.getElementById('editBankModal'));
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
            const topBadge = document.getElementById('topBadge_' + bankId);

            if (isActive) {
                // Now active
                if (statusText) {
                    statusText.innerText = 'Aktif';
                    statusText.classList.remove('text-danger', 'text-slate-400', 'text-white/50');
                    statusText.classList.add('text-emerald-600', 'text-success');
                }
                if (topBadge) {
                    topBadge.innerText = '● AKTIF';
                    topBadge.classList.remove('bg-black/30', 'text-white/80');
                    topBadge.classList.add('bg-emerald-500/90', 'text-white');
                }
            } else {
                // Now inactive
                if (statusText) {
                    statusText.innerText = 'Non-aktif';
                    statusText.classList.remove('text-success', 'text-emerald-600');
                    statusText.classList.add('text-slate-500');
                    statusText.classList.remove('text-white/50', 'text-slate-400'); // Clean up old classes
                }
                if (topBadge) {
                    topBadge.innerText = '○ NON-AKTIF';
                    topBadge.classList.remove('bg-emerald-500/90', 'text-white');
                    topBadge.classList.add('bg-black/30', 'text-white/80');
                }
            }
        } else {
            showAlert(data.message || 'Gagal mengubah status', false);
            // Revert state on failure
            switchEl.checked = !isActive;
            // Berhenti di sini. Tanpa return, dua blok di bawah tetap
            // berjalan dan menulis status BARU ke window.bankSoalList
            // serta badge halaman detail - padahal server menolak dan
            // saklarnya sudah dikembalikan. Akibatnya tampilan dan data
            // di memori bertentangan dengan keadaan sebenarnya.
            return;
        }
        
        // Update in-memory data
        if (window.bankSoalList) {
            const bData = window.bankSoalList.find(b => b.id == bankId);
            if (bData) bData.is_active = isActive ? 1 : 0;
        }
        
        // If this bank is currently open in detail view, update its badge too
        if (window.currentBankId == bankId) {
            const detailBadge = document.getElementById('detailBankStatusBadge');
            if (detailBadge) {
                if (isActive) {
                    detailBadge.textContent = 'AKTIF';
                    detailBadge.className = 'bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-md transition-colors';
                } else {
                    detailBadge.textContent = 'NON-AKTIF';
                    detailBadge.className = 'bg-slate-100 text-slate-500 text-[10px] font-bold px-2 py-1 rounded-md transition-colors';
                }
            }
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
        const radio = document.querySelector(`#editSoalForm input[name="jawaban"][value="${jawab}"]`);
        if(radio) radio.checked = true;

    } else {
        document.getElementById('editJawabanEssay').value = soalData.jawaban || '';
    }

    const modal = UI.modal.ref(document.getElementById('editSoalModal'));
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

            // Sync EasyMDE to FormData immediately and validate
            if (window.easyMDE_add) {
                const desc = window.easyMDE_add.value();
                if (!desc.trim()) {
                    showAlert('Pertanyaan tidak boleh kosong', false);
                    
                    // Reset button state
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    return;
                }
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
                    const modal = UI.modal.ref(document.getElementById('addSoalModal'));
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


