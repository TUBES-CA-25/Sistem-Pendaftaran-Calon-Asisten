/**
 * App.js - Main Application Script
 * Handles navigation, URL routing with History API, and page loading
 */

// Track current page to prevent unnecessary reloads
var _currentPage = null;

// Helper functions for Page Loader
function showPageLoader() {
    let loader = document.getElementById('global-page-loader');
    if (!loader) {
        $('body').append(`
            <div id="global-page-loader" class="fixed inset-0 z-[9999] bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none">
                <div class="relative w-16 h-16">
                    <div class="absolute inset-0 border-4 border-slate-200 rounded-full shadow-inner"></div>
                    <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin shadow-[0_0_15px_rgba(37,99,235,0.4)]"></div>
                </div>
                <div class="mt-4 font-bold text-blue-600 text-sm tracking-[0.2em] uppercase animate-pulse">Memuat...</div>
            </div>
        `);
        loader = document.getElementById('global-page-loader');
        // Force reflow
        loader.offsetHeight;
    }
    loader.classList.remove('opacity-0', 'pointer-events-none');
    loader.classList.add('opacity-100', 'pointer-events-auto');
}

function hidePageLoader() {
    const loader = document.getElementById('global-page-loader');
    if (loader) {
        loader.classList.remove('opacity-100', 'pointer-events-auto');
        loader.classList.add('opacity-0', 'pointer-events-none');
    }
}

// Global function untuk load halaman
function loadPage(page, updateUrl = true) {
    _currentPage = page;

    var $content = $('#content');

    // Tampilkan Loader Animasi
    showPageLoader();

    // STEP 1: Fade-out content FIRST (before touching DOM at all)
    $content.css({ opacity: 0, transition: 'opacity 0.15s ease' });

    // STEP 2: Wait for fade-out to complete, THEN do all DOM work (invisible to user)
    setTimeout(function() {

        // Now safe to destroy DataTables (content already invisible)
        if ($.fn.DataTable) {
            $content.find('table.dataTable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
            });
        }

        // Cleanup dynamic navbar elements
        $('#navbarSearchContainer').remove();

        // Save to localStorage & update sidebar active state
        localStorage.setItem('activePage', page);
        $('.sidebar a').removeClass('active');
        $(`.sidebar a[data-page="${page}"]`).addClass('active');

        // Update URL browser dengan History API
        if (updateUrl) {
            history.pushState({ page: page }, '', `${APP_URL}/${page}`);
        }

        // STEP 3: Load new content via AJAX
        $.ajax({
            url: `${APP_URL}/${page}`,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                // Inject HTML but keep invisible during DataTables init
                $content.css({ visibility: 'hidden' });
                $content.html(response);

                // Scroll to top
                window.scrollTo(0, 0);

                // Re-attach listeners for dynamic content
                if (typeof attachNotificationListeners === 'function') attachNotificationListeners();
                if (typeof window.initSidebar === 'function') window.initSidebar();

                // STEP 4: Init DataTables, THEN fade-in via callback
                initGlobalTables(function() {
                    // Beri jeda animasi loader 0.01 detik (10ms) sesuai permintaan
                    setTimeout(function() {
                        hidePageLoader();
                        $content.css({ visibility: 'visible', opacity: 0 });
                        requestAnimationFrame(function() {
                            $content.css({ transition: 'opacity 0.18s ease', opacity: 1 });
                        });
                    }, 1);
                });
            },
            error: function(xhr, status, error) {
                hidePageLoader();
                $content.css({ visibility: 'visible', opacity: 1 });
                _currentPage = null;
                console.error('Error loading page:', error);
                $content.html('<div class="container-fluid p-4"><div class="alert alert-danger"><i class="bx bx-error-circle me-2"></i>Halaman tidak ditemukan atau terjadi kesalahan.</div></div>');
            }
        });

    }, 160); // matches fade-out transition (0.15s + small buffer)
}

$(document).ready(function () {
    // Get initial page from server or localStorage
    var initialPage = window.INITIAL_PAGE || localStorage.getItem('activePage') || 'dashboard';

    // Initialize tables on first load
    _currentPage = initialPage;
    // On first load the content is already visible; no callback needed
    setTimeout(initGlobalTables, 80);

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

    window.addEventListener('popstate', function(e) {
        _currentPage = null; // allow popstate to always navigate
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
                updateNotificationUI(data.count, data.html);
            }
        })
        .catch(err => {
            if (err.name === 'AbortError') return; // Ignore aborts
            // Silently fail for other errors
        });
}

function updateNotificationUI(count, html) {
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
        dropdownMenu.innerHTML = html;
    }
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
        alert((isSuccess ? 'Ã¢Å“â€œ ' : 'Ã¢Å“â€” ') + message);
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

// Initialize global DataTables â€” accepts optional callback fired after all tables are ready
function initGlobalTables(onReady) {
    var $tables = $('#content').find('table:not(#calendarTable):not(.no-datatable)');

    // If no tables on this page, reveal immediately
    if ($tables.length === 0) {
        if (typeof onReady === 'function') onReady();
        return;
    }

    var pendingInits = 0;

    $tables.each(function() {
        var $table = $(this);

        if (!$.fn.DataTable.isDataTable(this)) {
            pendingInits++;
            $table.DataTable({
                scrollX: false,
                autoWidth: false,
                pageLength: 10,
                dom: '<"dt-top-wrapper"lf>t<"dt-bottom-wrapper"ip>',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Cari data...",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan <strong>_START_</strong> – <strong>_END_</strong> dari <strong>_TOTAL_</strong> data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "<div class='text-center py-10 text-slate-400'>Tidak ada data yang cocok</div>",
                    paginate: {
                        first: '«',
                        last: '»',
                        next: '›',
                        previous: '‹'
                    }
                },
                initComplete: function() {
                    // Automatically move custom buttons to the filter area
                    var $customButtons = $(this).closest('.max-w-7xl').find('.dt-custom-button');
                    if ($customButtons.length) {
                        var $filter = $(this).closest('.dataTables_wrapper').find('.dataTables_filter');
                        $filter.addClass('flex items-center gap-3');
                        $customButtons.each(function() {
                            $filter.append($(this));
                            $(this).removeClass('hidden').css('display', 'inline-flex');
                        });
                    }

                    // Trigger custom event so page-specific scripts can hook into the newly created DOM
                    $table.trigger('dt.initComplete');
                    // Count down pending; fire onReady when all tables are done
                    pendingInits--;
                    if (pendingInits <= 0 && typeof onReady === 'function') {
                        onReady();
                    }
                }
            });
        }
    });

    // If all tables were already initialized (no new ones), fire onReady immediately
    if (pendingInits === 0 && typeof onReady === 'function') {
        onReady();
    }
}

