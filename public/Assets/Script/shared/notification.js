/**
 * Notification Module
 * Provides alert/notification functionality for the application
 */

/**
 * Show alert message to user
 * @param {string} message - Message to display
 * @param {string} type - Type of alert ('success', 'error', 'warning', 'info')
 */
export function showAlert(message, type = 'success') {
    const alertClass = getAlertClass(type);
    const iconClass = getIconClass(type);

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
            <i class="bi ${iconClass}"></i>
            <div>${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Find or create alert container
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        alertContainer.style.minWidth = '300px';
        alertContainer.style.maxWidth = '500px';
        document.body.appendChild(alertContainer);
    }

    // Add new alert
    const alertDiv = document.createElement('div');
    alertDiv.innerHTML = alertHtml;
    alertContainer.appendChild(alertDiv.firstElementChild);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
}

/**
 * Get Bootstrap alert class based on type
 * @param {string} type - Alert type
 * @returns {string} Bootstrap alert class
 */
function getAlertClass(type) {
    const classes = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'danger': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    return classes[type] || 'alert-info';
}

/**
 * Get Bootstrap icon class based on type
 * @param {string} type - Alert type
 * @returns {string} Bootstrap icon class
 */
function getIconClass(type) {
    const icons = {
        'success': 'bi-check-circle-fill',
        'error': 'bi-exclamation-triangle-fill',
        'danger': 'bi-exclamation-triangle-fill',
        'warning': 'bi-exclamation-triangle-fill',
        'info': 'bi-info-circle-fill'
    };
    return icons[type] || 'bi-info-circle-fill';
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast ('success', 'error', 'warning', 'info')
 */
export function showToast(message, type = 'success') {
    // Alias for showAlert with shorter duration
    showAlert(message, type);
}

export default { showAlert, showToast };
