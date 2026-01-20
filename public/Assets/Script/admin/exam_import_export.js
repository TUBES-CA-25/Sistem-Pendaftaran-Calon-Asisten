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
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message);
            fileInput.value = '';
            
            // Reload if current bank matches
            if (window.currentBankId == bankId) {
                window.loadBankQuestions(bankId);
            }
            
        } else {
            // Show validation errors if any
            let msg = data.message;
            if (data.validation_errors && data.validation_errors.length > 0) {
                msg += ':\n' + data.validation_errors.slice(0, 5).join('\n');
                if (data.validation_errors.length > 5) msg += '\n...dan lainnya.';
            }
            showAlert(msg, false);
        }
    })
    .catch(err => {
        console.error('Import error:', err);
        showAlert('Terjadi kesalahan saat import', false);
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

    const url = baseUrl + '/exportSoal?bank_id=' + bankId;
    window.location.href = url;
    
    // Reset button after short delay
    setTimeout(() => {
        btnExport.disabled = false;
        btnExport.innerHTML = originalBtnText;
    }, 2000);
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
(function() {
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
            
            // Display total immediately
            totalEl.textContent = totalCount;
            
            // For now, show total only (PG/Essay breakdown requires backend data)
            // We'll calculate from the bank's questions
            const bankId = exportSelect.value;
            
            // Try to get breakdown from window.bankSoalList if available
            if (window.bankSoalList) {
                const bank = window.bankSoalList.find(b => b.id == bankId);
                if (bank) {
                    totalEl.textContent = bank.jumlah_soal || bank.pg_count + bank.essay_count || totalCount;
                    pgEl.textContent = bank.pg_count || bank.jumlah_pg || '0';
                    essayEl.textContent = bank.essay_count || bank.jumlah_essay || '0';
                } else {
                    // Fallback to just showing total
                    totalEl.textContent = totalCount;
                    pgEl.textContent = '-';
                    essayEl.textContent = '-';
                }
            } else {
                // No breakdown available, show total only
                totalEl.textContent = totalCount;
                pgEl.textContent = '-';
                essayEl.textContent = '-';
            }
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

