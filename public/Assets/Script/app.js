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
            // Scroll to top after page load
            window.scrollTo(0, 0);
            
            // Re-attach listeners for dynamic content
            if (typeof attachNotificationListeners === 'function') {
                attachNotificationListeners();
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

    // Set initial history state (replaceState, not pushState)
    history.replaceState({ page: initialPage }, '', `${APP_URL}/${initialPage}`);

    // Mark active sidebar item
    $(`.sidebar a[data-page="${initialPage}"]`).addClass('active');

    // Handle click pada sidebar dan link dengan data-page
    $(document).on('click', '.sidebar a[data-page], .profile a[data-page], .dashboard a[data-page], [data-page]', function (e) {
        if (this.id === "startTestButton" || this.id === "logout-btn") return;

        var page = $(this).data('page');
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

    // Start test button handler
    $('#startTestButton').on('click', function () {
        const nomorMejaInput = $('#nomorMeja').val().trim();

        if (!nomorMejaInput || isNaN(nomorMejaInput) || parseInt(nomorMejaInput) <= 0) {
            $('#errorMessage').text('Nomor meja tidak valid!');
            return;
        }

        $('#errorMessage').text('');

        const targetURL = `${APP_URL}/soal?nomorMeja=${encodeURIComponent(nomorMejaInput)}`;
        window.location.href = targetURL;
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

    const modal = new bootstrap.Modal(modalEl);
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
