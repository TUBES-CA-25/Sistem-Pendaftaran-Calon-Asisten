/**
 * Exam Import/Export JavaScript
 * Handles import/export functionality and dropdown updates
 */

// Import Soal
window.importSoal = function() {
    const bankId = document.getElementById('selectedBankSoalImport').value;
    const fileInput = document.getElementById('importFile');
    const file = fileInput.files[0];
    
    if (!bankId) {
        showAlert('Pilih bank soal terlebih dahulu', false);
        return;
    }
    
    if (!file) {
        showAlert('Pilih file Excel/CSV terlebih dahulu', false);
        return;
    }
    
    // Show loading state
    const btnImport = document.getElementById('btnImport');
    const originalBtnText = btnImport.innerHTML;
    btnImport.disabled = true;
    btnImport.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengimport...';
    
    const formData = new FormData();
    formData.append('bank_id', bankId);
    formData.append('file', file);
    
    fetch(baseUrl + '/importSoal', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        // Check if response is OK
        if (!res.ok) {
            // Try to parse error message from response
            return res.text().then(text => {
                try {
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.message || `Server error: ${res.status}`);
                } catch (e) {
                    throw new Error(`Server error: ${res.status} - ${text.substring(0, 100)}`);
                }
            });
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message);
            fileInput.value = '';
            
            // Reload if current bank matches
            if (window.currentBankId == bankId) {
                window.loadBankQuestions(bankId);
            }
            
            // Refresh bank card statistics
            if (window.refreshBankCardStats) {
                window.refreshBankCardStats(bankId);
            }
            
        } else {
            // Show validation errors if any
            let msg = data.message || 'Import gagal';
            if (data.validation_errors && data.validation_errors.length > 0) {
                msg += ':\n' + data.validation_errors.slice(0, 5).join('\n');
                if (data.validation_errors.length > 5) msg += '\n...dan lainnya.';
            }
            showAlert(msg, false);
        }
    })
    .catch(err => {
        console.error('Import error:', err);
        showAlert(err.message || 'Terjadi kesalahan saat import', false);
    })
    .finally(() => {
        btnImport.disabled = false;
        btnImport.innerHTML = originalBtnText;
    });
}

// Export Soal
window.exportSoal = function() {
    const bankId = document.getElementById('selectedBankSoal').value;
    
    if (!bankId) {
        showAlert('Pilih bank soal terlebih dahulu', false);
        return;
    }
    
    const btnExport = document.getElementById('btnExport');
    const originalBtnText = btnExport.innerHTML;
    btnExport.disabled = true;
    btnExport.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengexport...';

    // Use a hidden iframe or anchor tag to trigger download without navigation
    const url = baseUrl + '/exportSoal?bank_id=' + bankId;
    
    // Create a temporary anchor element
    const a = document.createElement('a');
    a.href = url;
    a.download = ''; // This attribute triggers download
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    
    // Clean up
    setTimeout(() => {
        document.body.removeChild(a);
        btnExport.disabled = false;
        btnExport.innerHTML = originalBtnText;
    }, 1000);
}

// Download Template
window.downloadTemplate = function() {
    const url = baseUrl + '/downloadTemplatesoal';
    
    // Create a temporary anchor element
    const a = document.createElement('a');
    a.href = url;
    a.download = 'template_soal.csv';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    
    // Clean up
    setTimeout(() => {
        document.body.removeChild(a);
    }, 100);
}

// Refresh Bank Dropdowns in Import/Export Tab
window.refreshBankDropdowns = function(newBankId, newBankName, soalCount) {
    // Update Import Dropdown
    const importSelect = document.getElementById('selectedBankSoalImport');
    if (importSelect) {
        const newOptionImport = document.createElement('option');
        newOptionImport.value = newBankId;
        newOptionImport.setAttribute('data-name', newBankName);
        newOptionImport.setAttribute('data-count', soalCount || 0);
        newOptionImport.textContent = `${newBankName} (${soalCount || 0} soal)`;
        
        // Insert after the first option (placeholder)
        if (importSelect.options.length > 1) {
            importSelect.insertBefore(newOptionImport, importSelect.options[1]);
        } else {
            importSelect.appendChild(newOptionImport);
        }
    }
    
    // Update Export Dropdown
    const exportSelect = document.getElementById('selectedBankSoal');
    if (exportSelect) {
        const newOptionExport = document.createElement('option');
        newOptionExport.value = newBankId;
        newOptionExport.setAttribute('data-name', newBankName);
        newOptionExport.setAttribute('data-count', soalCount || 0);
        newOptionExport.textContent = `${newBankName} (${soalCount || 0} soal)`;
        
        // Insert after the first option (placeholder)
        if (exportSelect.options.length > 1) {
            exportSelect.insertBefore(newOptionExport, exportSelect.options[1]);
        } else {
            exportSelect.appendChild(newOptionExport);
        }
    }
    
    // Update window.bankSoalList for consistency
    if (!window.bankSoalList) {
        window.bankSoalList = [];
    }
    window.bankSoalList.unshift({
        id: newBankId,
        nama: newBankName,
        jumlah_soal: soalCount || 0
    });
}

// Refresh Bank Card Statistics after import
window.refreshBankCardStats = function(bankId) {
    // Fetch updated bank details
    fetch(baseUrl + '/getBankDetails?id=' + bankId)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.bank) {
                const bank = data.bank;
                const card = document.getElementById('bank-card-' + bankId);
                
                if (card) {
                    // Update Total Soal badge
                    const totalBadge = card.querySelector('.badge[style*="#ff5252"]');
                    if (totalBadge) {
                        totalBadge.innerHTML = `<i class='bx bx-file me-1'></i> ${bank.jumlah_soal || 0} Soal`;
                    }
                    
                    // Update PG badge
                    const pgBadge = card.querySelector('.badge[style*="#448aff"]');
                    if (pgBadge) {
                        pgBadge.textContent = `PG: ${bank.jumlah_pg || 0}`;
                    }
                    
                    // Update Essay badge
                    const essayBadge = card.querySelector('.badge[style*="#ffd740"]');
                    if (essayBadge) {
                        essayBadge.textContent = `Essay: ${bank.jumlah_essay || 0}`;
                    }
                    
                    // Add animation
                    [totalBadge, pgBadge, essayBadge].forEach(badge => {
                        if (badge) {
                            badge.style.transform = 'scale(1.1)';
                            badge.style.transition = 'transform 0.2s ease';
                            setTimeout(() => {
                                badge.style.transform = 'scale(1)';
                            }, 200);
                        }
                    });
                }
                
                // Update dropdown options
                const importSelect = document.getElementById('selectedBankSoalImport');
                const exportSelect = document.getElementById('selectedBankSoal');
                
                [importSelect, exportSelect].forEach(select => {
                    if (select) {
                        const option = select.querySelector(`option[value="${bankId}"]`);
                        if (option) {
                            option.textContent = `${bank.nama} (${bank.jumlah_soal || 0} soal)`;
                            option.setAttribute('data-count', bank.jumlah_soal || 0);
                        }
                    }
                });
            }
        })
        .catch(err => {
            console.error('Error refreshing bank stats:', err);
        });
}

// Remove Bank from Dropdowns when deleted
window.removeBankFromDropdowns = function(bankId) {
    // Remove from Import Dropdown
    const importSelect = document.getElementById('selectedBankSoalImport');
    if (importSelect) {
        const options = importSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value == bankId) {
                option.remove();
            }
        });
    }
    
    // Remove from Export Dropdown
    const exportSelect = document.getElementById('selectedBankSoal');
    if (exportSelect) {
        const options = exportSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value == bankId) {
                option.remove();
            }
        });
    }
    
    // Update window.bankSoalList
    if (window.bankSoalList) {
        window.bankSoalList = window.bankSoalList.filter(bank => bank.id != bankId);
    }
}

// Initialize Event Listeners
;(function() {
    // Make these functions global so they can be called directly from HTML
    window.updateImportButtonState = function() {
        const importSelect = document.getElementById('selectedBankSoalImport');
        const fileInput = document.getElementById('importFile');
        const btn = document.getElementById('btnImport');
        
        if (btn && importSelect && fileInput) {
            const hasBank = importSelect.value && importSelect.value !== '';
            const hasFile = fileInput.files && fileInput.files.length > 0;
            btn.disabled = !(hasBank && hasFile);
        }
    }
    
    window.updateExportButtonState = function() {
        const exportSelect = document.getElementById('selectedBankSoal');
        const btn = document.getElementById('btnExport');
        
        if (btn && exportSelect) {
            btn.disabled = !exportSelect.value || exportSelect.value === '';
        }
        
        // Update export summary using data from dropdown
        const totalEl = document.getElementById('exportTotalCount');
        const pgEl = document.getElementById('exportPGCount');
        const essayEl = document.getElementById('exportEssayCount');
        
        if (exportSelect && exportSelect.value && totalEl && pgEl && essayEl) {
            const selectedOption = exportSelect.options[exportSelect.selectedIndex];
            const totalCount = selectedOption.getAttribute('data-count') || '0';
            const pgCount = selectedOption.getAttribute('data-pg') || '0';
            const essayCount = selectedOption.getAttribute('data-essay') || '0';
            
            // Display stats immediately from data attributes
            totalEl.textContent = totalCount;
            pgEl.textContent = pgCount;
            essayEl.textContent = essayCount;
        } else if (totalEl && pgEl && essayEl) {
            // Reset summary
            totalEl.textContent = '-';
            pgEl.textContent = '-';
            essayEl.textContent = '-';
        }
    }

    // Attach listeners as backup, but we will mostly rely on inline calls
    const importSelect = document.getElementById('selectedBankSoalImport');
    if (importSelect) importSelect.addEventListener('change', window.updateImportButtonState);

    const importFile = document.getElementById('importFile');
    if (importFile) importFile.addEventListener('change', window.updateImportButtonState);

    const exportSelect = document.getElementById('selectedBankSoal');
    if (exportSelect) exportSelect.addEventListener('change', window.updateExportButtonState);
    
    // Initial check
    window.updateImportButtonState();
    window.updateExportButtonState();
})();

