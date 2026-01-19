/**
 * Common Utilities
 * Fungsi-fungsi yang digunakan di banyak halaman
 * Updated: Bootstrap 5 Integration
 */

// Constants untuk path - Check if already declared to prevent errors when loaded via AJAX
if (typeof APP_PATHS === 'undefined') {
    var APP_PATHS = {
        images: '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser',
        documents: '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser',
        gifs: '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif'
    };
}

/**
 * Menampilkan modal custom (Bootstrap Modal)
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string|null} gifUrl - URL GIF (optional)
 */
function showModal(message, gifUrl = null) {
    const modalEl = document.getElementById('customModal');
    if (!modalEl) return;

    const modalMessage = document.getElementById('modalMessage');
    const modalGif = document.getElementById('modalGif');

    if (modalMessage) modalMessage.textContent = message;
    if (modalGif) {
        modalGif.style.display = gifUrl ? 'block' : 'none';
        if (gifUrl) modalGif.src = gifUrl;
    }

    // Use Bootstrap Modal API
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

/**
 * Menampilkan modal konfirmasi (Bootstrap Modal)
 * @param {string} message - Pesan konfirmasi
 * @param {function} onConfirm - Callback saat confirm
 * @param {function} onCancel - Callback saat cancel
 */
function showConfirm(message, onConfirm = null, onCancel = null) {
    const modalEl = document.getElementById('confirmModal');
    if (!modalEl) return;

    const modalMessage = document.getElementById('confirmModalMessage');
    const confirmButton = document.getElementById('confirmModalConfirm');
    const cancelButton = document.getElementById('confirmModalCancel');

    if (modalMessage) modalMessage.textContent = message;

    // Use Bootstrap Modal API
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    if (confirmButton) {
        const newConfirmBtn = confirmButton.cloneNode(true);
        confirmButton.parentNode.replaceChild(newConfirmBtn, confirmButton);
        newConfirmBtn.addEventListener('click', () => {
            if (onConfirm) onConfirm();
            modal.hide();
        });
    }

    if (cancelButton) {
        const newCancelBtn = cancelButton.cloneNode(true);
        cancelButton.parentNode.replaceChild(newCancelBtn, cancelButton);
        newCancelBtn.addEventListener('click', () => {
            if (onCancel) onCancel();
            modal.hide();
        });
    }

    modal.show();
}

/**
 * Helper untuk mendapatkan URL gambar
 * @param {string} filename - Nama file
 * @returns {string} URL lengkap
 */
function getImageUrl(filename) {
    return filename ? `${APP_PATHS.images}/${filename}` : `${APP_PATHS.images}/default-image.jpg`;
}

/**
 * Helper untuk mendapatkan URL dokumen
 * @param {string} filename - Nama file
 * @returns {string} URL lengkap atau '#' jika tidak ada
 */
function getDocumentUrl(filename) {
    return filename ? `${APP_PATHS.documents}/${filename}` : '#';
}

/**
 * Helper untuk AJAX request
 * @param {string} url - URL endpoint
 * @param {object} data - Data yang akan dikirim
 * @param {function} onSuccess - Callback success
 * @param {function} onError - Callback error
 */
function sendAjax(url, data, onSuccess, onError) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: onSuccess,
        error: onError || function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

/**
 * Global Toast Notification Function (Bootstrap Toast)
 * @param {string} message - Message to display
 * @param {boolean} isSuccess - True for success (green), False for error (red)
 * @param {string|null} redirectUrl - Optional URL to redirect after toast closes
 */
function showAlert(message, isSuccess = true, redirectUrl = null) {
    // Ensure toast container exists
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const type = isSuccess ? 'success' : 'danger';
    const title = isSuccess ? 'Berhasil' : 'Gagal';
    const icon = isSuccess ? 'bx bx-check-circle' : 'bx bx-error-circle';
    const duration = 3000;

    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="${icon} fs-5"></i>
                    <div>
                        <strong>${title}</strong>
                        <div class="small">${message}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);

    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: duration });

    // Handle redirect after toast hides
    if (redirectUrl) {
        toastEl.addEventListener('hidden.bs.toast', () => {
            window.location.href = redirectUrl;
        });
    }

    // Remove element after hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });

    toast.show();
}

/**
 * Show Global Delete Confirmation Modal (Bootstrap Modal)
 * @param {function} onConfirm - Callback function to execute when 'Hapus' is clicked
 * @param {string} message - Optional custom message
 */
function showConfirmDelete(onConfirm, message = 'Apakah Anda yakin ingin menghapus data ini?<br>Tindakan ini tidak dapat dibatalkan.') {
    const modalEl = document.getElementById('deleteConfirmModal');
    if (!modalEl) {
        // Fallback: use native confirm
        if (confirm(message.replace(/<br>/g, '\n'))) {
            if (typeof onConfirm === 'function') onConfirm();
        }
        return;
    }

    // Set message if provided
    const messageEl = document.getElementById('deleteModalMessage');
    if (messageEl && message) {
        messageEl.innerHTML = message;
    }

    // Setup confirm button
    const btnConfirm = document.getElementById('btnConfirmDelete');
    if (btnConfirm) {
        // Clone button to remove previous event listeners
        const newBtn = btnConfirm.cloneNode(true);
        btnConfirm.parentNode.replaceChild(newBtn, btnConfirm);

        // Add new event listener
        newBtn.addEventListener('click', function() {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
            closeDeleteModal();
        });
    }

    // Show modal using Bootstrap Modal API
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

/**
 * Close Delete Modal (Bootstrap Modal)
 */
function closeDeleteModal() {
    const modalEl = document.getElementById('deleteConfirmModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

/**
 * Hide any Bootstrap modal by ID
 * @param {string} modalId - ID of the modal element
 */
function hideModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

/**
 * Show any Bootstrap modal by ID
 * @param {string} modalId - ID of the modal element
 */
function showModalById(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

// Check for pending toasts on load
document.addEventListener('DOMContentLoaded', function() {
    const pendingToast = sessionStorage.getItem('pendingToast');
    if (pendingToast) {
        const data = JSON.parse(pendingToast);
        showAlert(data.message, data.isSuccess);
        sessionStorage.removeItem('pendingToast');
    }
});
