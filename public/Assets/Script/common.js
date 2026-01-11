/**
 * Common Utilities
 * Fungsi-fungsi yang digunakan di banyak halaman
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
 * Menampilkan modal custom
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string|null} gifUrl - URL GIF (optional)
 */
function showModal(message, gifUrl = null) {
    const modal = document.getElementById('customModal');
    if (!modal) return;

    const modalMessage = document.getElementById('modalMessage');
    const modalGif = document.getElementById('modalGif');
    const closeModal = document.getElementById('closeModal');
    
    if (modalMessage) modalMessage.textContent = message;
    if (modalGif) {
        modalGif.style.display = gifUrl ? 'block' : 'none';
        if (gifUrl) modalGif.src = gifUrl;
    }

    modal.style.display = 'flex';

    if (closeModal) {
        $(closeModal).off('click').on('click', () => modal.style.display = 'none');
    }

    $(window).off('click.customModal').on('click.customModal', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });
}

/**
 * Menampilkan modal konfirmasi
 * @param {string} message - Pesan konfirmasi
 * @param {function} onConfirm - Callback saat confirm
 * @param {function} onCancel - Callback saat cancel
 */
function showConfirm(message, onConfirm = null, onCancel = null) {
    const modal = document.getElementById('confirmModal');
    if (!modal) return;

    const modalMessage = document.getElementById('confirmModalMessage');
    const confirmButton = document.getElementById('confirmModalConfirm');
    const cancelButton = document.getElementById('confirmModalCancel');

    if (modalMessage) modalMessage.textContent = message;
    modal.style.display = 'flex';

    if (confirmButton) {
        $(confirmButton).off('click').on('click', () => {
            if (onConfirm) onConfirm();
            modal.style.display = 'none';
        });
    }

    if (cancelButton) {
        $(cancelButton).off('click').on('click', () => {
            if (onCancel) onCancel();
            modal.style.display = 'none';
        });
    }

    $(window).off('click.confirmModal').on('click.confirmModal', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });
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
 * Global Toast Notification Function
 * @param {string} message - Message to display
 * @param {boolean} isSuccess - True for success (green), False for error (red)
 * @param {string|null} redirectUrl - Optional URL to redirect after toast closes
 */
function showAlert(message, isSuccess = true, redirectUrl = null) {
    // Ensure toast container exists
    if ($('#toast-container').length === 0) {
        $('body').append('<div id="toast-container"></div>');
    }

    const type = isSuccess ? 'success' : 'error';
    const title = isSuccess ? 'Berhasil' : 'Gagal';
    const icon = isSuccess ? 'bi-check-lg' : 'bi-exclamation-triangle';
    const duration = 3000; // 3 seconds

    const toastHtml = `
        <div class="toast-notification ${type}">
            <div class="toast-icon">
                <i class="bi ${icon}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <div class="toast-close" onclick="$(this).parent().remove()">
                <i class="bi bi-x"></i>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        </div>
    `;

    const $toast = $(toastHtml);
    $('#toast-container').append($toast);

    // Trigger reflow for animation
    setTimeout(() => $toast.addClass('show'), 10);

    // Progress bar animation
    const $progressBar = $toast.find('.toast-progress-bar');
    $progressBar.css('transition', `transform ${duration}ms linear`);
    setTimeout(() => $progressBar.css('transform', 'scaleX(0)'), 10);

    // Auto remove
    setTimeout(() => {
        $toast.removeClass('show');
        setTimeout(() => {
            $toast.remove();
            // Handle redirect if provided
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        }, 400); // Wait for slide out animation
    }, duration);
}
