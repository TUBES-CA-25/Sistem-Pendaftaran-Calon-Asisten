/**
 * Global Helper Functions
 * Includes delete confirmation logic and backward compatibility
 */

// 1. Core Delete Confirmation Logic
// 1. Core Delete Confirmation Logic
function showDeleteConfirmation(options) {
    const {
        message = 'Apakah Anda yakin ingin menghapus data ini?',
        id = '',
        type = '',
        onConfirm = null
    } = options;
    
    // Redirect to new Action Confirmation Modal
    showActionConfirmation({
        title: 'Hapus Data',
        message: message,
        btnText: 'Hapus',
        type: 'danger', // Makes it Red
        onConfirm: function() {
            if (onConfirm && typeof onConfirm === 'function') {
                onConfirm(id, type);
            }
        }
    });
}

// 2. Initialize Modal Event Handlers
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmDeleteButton');
    if (confirmBtn) {
        // Remove old listeners to avoid duplicates
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.addEventListener('click', function() {
            const id = document.getElementById('deleteTargetId') ? document.getElementById('deleteTargetId').value : null;
            const type = document.getElementById('deleteTargetType') ? document.getElementById('deleteTargetType').value : null;
            
            // Close modal
            const modalEl = document.getElementById('deleteConfirmModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            
            // Execute callback if exists
            if (window._deleteConfirmCallback && typeof window._deleteConfirmCallback === 'function') {
                window._deleteConfirmCallback(id, type);
                // We DON'T clear it immediately because some handlers might expect it, 
                // but usually it's better to clear it to avoid accidental repeat execution
                window._deleteConfirmCallback = null; 
            }
        });
    }
});

// 3. Backward Compatibility Wrapper
function showConfirmDelete(callback, message) {
    showDeleteConfirmation({
        message: message || 'Apakah Anda yakin ingin menghapus data ini?',
        id: null,
        type: 'generic',
        onConfirm: function() {
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

// 5. Generic Action Confirmation Helper
function showActionConfirmation(options) {
    const {
        title = 'Konfirmasi',
        message = 'Apakah Anda yakin?',
        btnText = 'Ya',
        type = 'primary', // primary, success, danger, warning, info
        onConfirm = null
    } = options;

    const modalEl = document.getElementById('actionConfirmModal');
    if (!modalEl) {
        if(confirm(message)) {
            if(onConfirm) onConfirm();
        }
        return;
    }

    // Map types to styles (Premium Gradients)
    const styles = {
        primary: { 
            bg: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)', 
            icon: 'bi-question-lg', 
            btnBg: '#0d6efd',
            shadow: '0 4px 6px rgba(13, 110, 253, 0.3)'
        },
        success: { 
            bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 
            icon: 'bi-check-lg', 
            btnBg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            shadow: '0 4px 6px rgba(16, 185, 129, 0.3)'
        },
        danger: { 
            bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 
            icon: 'bi-x-lg', 
            btnBg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
            shadow: '0 4px 6px rgba(239, 68, 68, 0.3)'
        },
        warning: { 
            bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 
            icon: 'bi-exclamation-lg', 
            btnBg: '#f59e0b',
            shadow: '0 4px 6px rgba(245, 158, 11, 0.3)'
        },
        info: { 
            bg: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', 
            icon: 'bi-info-lg', 
            btnBg: '#3b82f6',
            shadow: '0 4px 6px rgba(59, 130, 246, 0.3)'
        }
    };

    const style = styles[type] || styles.primary;

    // Update Header Style
    const header = document.getElementById('actionConfirmHeader');
    header.style.background = style.bg;

    // Update Icon
    const icon = document.getElementById('actionConfirmIcon');
    // Reset icon classes completely to ensure only the requested icon is present
    icon.className = `bi ${style.icon}`; // e.g., bi bi-check-lg
    // The parent circle is already styled in HTML
    
    // Update Content
    document.getElementById('actionConfirmTitle').textContent = title;
    document.getElementById('actionConfirmMessage').innerHTML = message;
    
    // Update Button
    const btn = document.getElementById('actionConfirmButton');
    btn.innerHTML = (type === 'success' || type === 'primary' ? '<i class="bi bi-check-circle me-2"></i>' : (type === 'danger' ? '<i class="bi bi-trash3 me-2"></i>' : '')) + btnText;
    
    // Apply button styling
    btn.style.background = style.btnBg;
    btn.style.boxShadow = style.shadow;

    // Handle Click
    // Clone to remove old listeners
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.addEventListener('click', function() {
        if(onConfirm) onConfirm();
        bootstrap.Modal.getInstance(modalEl).hide();
    });

    // Show Modal
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// 4. Helper for showing alerts
function showAlert(message, isSuccess) {
    // If a toast system exists, use it
    const toastMessageEl = document.getElementById('toastMessage');
    const toastEl = document.getElementById('liveToast');
    
    if (toastMessageEl && toastEl) {
        toastMessageEl.textContent = message;
        toastEl.classList.remove('bg-success', 'bg-danger');
        toastEl.classList.add(isSuccess ? 'bg-success' : 'bg-danger');
        
        const bootstrapToast = new bootstrap.Toast(toastEl);
        bootstrapToast.show();
    } else {
        // Fallback to alert
        alert((isSuccess ? '✓ ' : '✗ ') + message);
    }
}
