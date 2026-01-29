/**
 * Global Helper Functions
 * Includes delete confirmation logic and backward compatibility
 */

// 1. Core Delete Confirmation Logic
function showDeleteConfirmation(options) {
    const {
        message = 'Apakah Anda yakin ingin menghapus data ini?',
        id = '',
        type = '',
        onConfirm = null
    } = options;
    
    // Set message and data with null checks
    const msgEl = document.getElementById('deleteConfirmMessage');
    const idEl = document.getElementById('deleteTargetId');
    const typeEl = document.getElementById('deleteTargetType');

    if (msgEl) msgEl.textContent = message;
    if (idEl) idEl.value = id;
    if (typeEl) typeEl.value = type;
    
    // Store callback on window to be accessed by the confirm button click handler
    if (onConfirm && typeof onConfirm === 'function') {
        window._deleteConfirmCallback = onConfirm;
    } else {
        window._deleteConfirmCallback = null;
    }
    
    // Show modal if it exists
    const modalEl = document.getElementById('deleteConfirmModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        console.warn('Delete confirmation modal element not found in DOM! Falling back to native confirm.');
        // Fallback to native confirm if modal is missing
        if (confirm(message)) {
            if (onConfirm && typeof onConfirm === 'function') {
                onConfirm(id, type);
            }
        }
    }
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
