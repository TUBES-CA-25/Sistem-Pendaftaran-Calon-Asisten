/**
 * App.js - Main Application Script
 * Handles navigation, URL routing with History API, and page loading
 */

// Global function untuk load halaman
function loadPage(page, updateUrl = true) {
    // Cleanup DataTables before replacing content
    if ($.fn.DataTable) {
        $('#content').find('table.dataTable').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });
    }

    // Cleanup dynamic navbar elements (e.g. Search Bar from Participants page)
    $('#navbarSearchContainer').remove();

    // Save to localStorage
    localStorage.setItem('activePage', page);

    // Update sidebar active state
    $('.sidebar a').removeClass('active');
    $(`.sidebar a[data-page="${page}"]`).addClass('active');

    // Update URL browser dengan History API
    if (updateUrl) {
        history.pushState({ page: page }, '', `${APP_URL}/${page}`);
    }

    // Load content via AJAX
    $.ajax({
        url: `${APP_URL}/${page}`,
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
            $('#content').html(response);
            // Initialize tables globally
            initGlobalTables();
            // Scroll to top after page load
            window.scrollTo(0, 0);
            
            // Re-attach listeners for dynamic content
            if (typeof attachNotificationListeners === 'function') {
                attachNotificationListeners();
            }
            if (typeof window.initSidebar === 'function') {
                window.initSidebar();
            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading page:", error);
            $('#content').html('<div class="container-fluid p-4"><div class="alert alert-danger"><i class="bx bx-error-circle me-2"></i>Halaman tidak ditemukan atau terjadi kesalahan.</div></div>');
        }
    });
}

$(document).ready(function () {
    // Get initial page from server or localStorage
    var initialPage = window.INITIAL_PAGE || localStorage.getItem('activePage') || 'dashboard';

    // Initialize tables on first load
    setTimeout(initGlobalTables, 500);

    // Set initial history state (replaceState, not pushState)
    history.replaceState({ page: initialPage }, '', `${APP_URL}/${initialPage}`);

    // Mark active sidebar item
    $(`.sidebar a[data-page="${initialPage}"]`).addClass('active');

    // Handle click pada sidebar dan link dengan data-page
    $(document).on('click', '.sidebar a[data-page], .profile a[data-page], .dashboard a[data-page], [data-page]', function (e) {
        if (this.id === "startTestButton" || this.id === "logout-btn") return;
        
        e.preventDefault();

        var page = $(this).data('page');
        console.log("Navigating to page:", page);

        if (!page) {
            console.error("Data page tidak ditemukan pada elemen ini:", this);
            return;
        }



        // Handle logout separately
        if (page === 'logout') {
            e.preventDefault();
            
            localStorage.removeItem('activePage');
            // Perform logout via AJAX
            $.ajax({
                url: `${APP_URL}/logout`,
                method: 'POST',
                success: function() {
                    window.location.href = APP_URL;
                },
                error: function() {
                    window.location.href = APP_URL;
                }
            });
            return;
        }

        e.preventDefault();
        
        // Close sidebar on mobile automatically
        if (window.innerWidth <= 991.98 && typeof window.closeSidebar === 'function') {
            setTimeout(() => window.closeSidebar(), 150);
        }
        
        loadPage(page);
    });

    // Handle browser back/forward button
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.page) {
            loadPage(e.state.page, false);
        } else {
            // Fallback to dashboard if no state
            loadPage('dashboard', false);
        }
    });

    // Footer scroll behavior
    var lastScrollTop = 0;
    var scrollTimeout;

    window.addEventListener("scroll", function () {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }

        scrollTimeout = setTimeout(function () {
            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            var scrollHeight = document.documentElement.scrollHeight;
            var clientHeight = document.documentElement.clientHeight;

            var footer = document.getElementById('footer');
            if (!footer) return;

            if (currentScroll > lastScrollTop) {
                footer.classList.remove('show-footer');
            } else {
                footer.classList.add('show-footer');
            }

            if (currentScroll + clientHeight >= scrollHeight - 10) {
                footer.classList.add('show-footer');
            }

            lastScrollTop = currentScroll;
        }, 100);
    });

});

// Global showModal function
window.showModal = function(message, gifUrl = null, onCloseCallback = null) {
    const modalEl = document.getElementById("customModal");
    if (!modalEl) {
        alert(message);
        if (onCloseCallback) onCloseCallback();
        return;
    }

    const modalMessage = document.getElementById("modalMessage");
    const modalGif = document.getElementById("modalGif");

    if (modalMessage) modalMessage.textContent = message;

    if (modalGif) {
        if (gifUrl) {
            modalGif.src = gifUrl;
            modalGif.style.display = "block";
        } else {
            modalGif.style.display = "none";
        }
    }

    // Check if bootstrap is available
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.error('Bootstrap Modal is not available');
        alert(message);
        if (onCloseCallback) onCloseCallback();
        return;
    }

    // Get or create modal instance
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    modal.show();

    // Handle close callback
    const closeBtn = document.getElementById("closeModal");
    if (closeBtn && onCloseCallback) {
        // Remove previous listeners to avoid stacking
        const newBtn = closeBtn.cloneNode(true);
        closeBtn.parentNode.replaceChild(newBtn, closeBtn);
        newBtn.addEventListener('click', onCloseCallback);
    }

    // Also handle modal hidden event
    if (onCloseCallback) {
        modalEl.addEventListener('hidden.bs.modal', function handler() {
            onCloseCallback();
            modalEl.removeEventListener('hidden.bs.modal', handler);
        }, { once: true });
    }
};
// Notification Polling Logic
// Global AbortController for notifications
let notificationAbortController = null;

// Notification Polling Logic
function initNotificationPolling() {
    // Only start interval if not already running
    if (!window.notificationInterval) {
        // Initial check
        checkNotifications();
        // Poll every 5 seconds
        window.notificationInterval = setInterval(checkNotifications, 5000);
    }
    
    // Attach listeners for current page
    attachNotificationListeners();
}

function attachNotificationListeners() {
    const bellBtn = document.querySelector('.navbar-action-btn');
    if (!bellBtn) return; 

    const dropdownElement = bellBtn.closest('.dropdown');
    if (dropdownElement) {
        // Remove existing listener to avoid duplicates if re-attaching
        dropdownElement.removeEventListener('shown.bs.dropdown', handleDropdownShown);
        dropdownElement.addEventListener('shown.bs.dropdown', handleDropdownShown);
    }
}

function handleDropdownShown() {
    markNotificationsAsRead();
}

function markNotificationsAsRead() {
    fetch(`${APP_URL}/marknotificationsread`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const badge = document.querySelector('.navbar-action-btn .badge');
            if (badge) badge.classList.add('d-none');
        }
    })
    .catch(err => console.error("Error marking read:", err));
}

function checkNotifications() {
    // Check if bell button exists on current page before fetching
    if (!document.querySelector('.navbar-action-btn')) return;

    // Abort previous request if it's still running
    if (notificationAbortController) {
        notificationAbortController.abort();
    }
    notificationAbortController = new AbortController();

    fetch(`${APP_URL}/getnotifications`, {
        signal: notificationAbortController.signal
    })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (data.status === 'success') {
                updateNotificationUI(data.data, data.count);
            }
        })
        .catch(err => {
            if (err.name === 'AbortError') return; // Ignore aborts
            // Silently fail for other errors
        });
}

function updateNotificationUI(notifications, count) {
    // Update Badge
    const badge = document.querySelector('.navbar-action-btn .badge');
    if (badge) {
        if (count > 0) {
            badge.innerText = count;
            badge.classList.remove('d-none');
            badge.style.display = ''; 
        } else {
            badge.classList.add('d-none');
        }
    }

    // Update Dropdown List
    const dropdownMenu = document.querySelector('.navbar-notification-dropdown');
    if (dropdownMenu) {
        // Set fixed width for better readability
        dropdownMenu.style.width = '320px';
        dropdownMenu.style.maxWidth = '90vw';

        let html = `
            <li class="dropdown-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Notifikasi</span>
                ${count > 0 ? `<span class="badge bg-primary rounded-pill">${count}</span>` : ''}
            </li>
            <li><hr class="dropdown-divider my-1"></li>
        `;

        if (notifications.length > 0) {
            notifications.slice(0, 5).forEach(notif => {
                // Format Date
                let dateStr = '';
                if (notif.created_at) {
                    const date = new Date(notif.created_at.replace(' ', 'T'));
                    dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                }

                html += `
                    <li>
                        <a class="dropdown-item notification-item p-3" href="#" data-page="notification" style="white-space: normal;">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="notification-icon flex-shrink-0 mt-1">
                                    <i class='bx bx-info-circle text-primary'></i>
                                </div>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <p class="mb-1 small text-dark fw-medium lh-sm text-wrap text-break">${escapeHtml(notif.pesan)}</p>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">${dateStr}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            });
            html += `
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item text-center small text-primary fw-semibold py-2" href="#" data-page="notification">
                        Lihat Semua Notifikasi
                    </a>
                </li>
            `;
        } else {
            html += `
                <li>
                    <div class="dropdown-item text-center text-muted py-3">
                        <i class='bx bx-bell-off fs-3 d-block mb-2'></i>
                        <small>Tidak ada notifikasi</small>
                    </div>
                </li>
            `;
        }

        dropdownMenu.innerHTML = html;
    }
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

$(document).ready(function() {
    initNotificationPolling();
});

// ==========================================
// MERGED FROM global-helpers.js
// ==========================================

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
    
    // Redirect to new Action Confirmation Modal
    // Note: showActionConfirmation is defined below
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

// 4. Helper for showing alerts
function showAlert(message, isSuccess = true) {
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
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }

        // Execute callback after modal starts hiding
        if(onConfirm) {
            setTimeout(function() {
                onConfirm();
            }, 150); // Small delay to ensure modal backdrop is being removed
        }

        // Ensure backdrop is removed (failsafe)
        setTimeout(function() {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 300);
    });

    // Show Modal
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Initialize global DataTables for all tables
function initGlobalTables() {
    $('#content').find('table:not(#calendarTable)').each(function() {
        // Apply Tailwind UI clean table template
        $(this).removeClass('border-collapse border border-slate-200 w-full text-sm text-left')
               .addClass('min-w-full divide-y divide-slate-200 align-middle shadow ring-1 ring-black ring-opacity-5 rounded-lg');
               
        $(this).find('thead').addClass('bg-slate-50');
        
        // Remove existing heavy gradients and text-white from tr/th in header
        $(this).find('thead tr').removeClass('bg-gradient-to-r from-blue-600 to-indigo-600 text-white');
        $(this).find('th').removeClass('border border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-xs uppercase tracking-wider py-4 px-4')
                          .addClass('py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 border-b border-slate-200');
                          
        $(this).find('tbody').addClass('divide-y divide-slate-200 bg-white');
        $(this).find('td').removeClass('border border-slate-200 py-4 px-4')
                          .addClass('whitespace-nowrap py-4 pl-4 pr-3 text-sm text-slate-600');
                          
        $(this).find('tbody tr').removeClass('hover:bg-slate-50/85').addClass('hover:bg-slate-50 transition-colors');

        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                scrollX: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    });
}


