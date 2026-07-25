/**
 * App.js - Main Application Script (Vanilla JS — no jQuery dependency)
 * Handles navigation, URL routing with History API, and page loading
 */

// Track current page to prevent unnecessary reloads
var _currentPage = null;

// ─────────────────────────────────────────────
// PAGE LOADER
// ─────────────────────────────────────────────
function showPageLoader() {
    let loader = document.getElementById('global-page-loader');
    if (!loader) {
        const div = document.createElement('div');
        div.id = 'global-page-loader';
        div.className = 'fixed inset-0 z-[9999] bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none';
        div.innerHTML = `
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-slate-200 rounded-full shadow-inner"></div>
                <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin shadow-[0_0_15px_rgba(37,99,235,0.4)]"></div>
            </div>
            <div class="mt-4 font-bold text-blue-600 text-sm tracking-[0.2em] uppercase animate-pulse">Memuat...</div>
        `;
        document.body.appendChild(div);
        loader = div;
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

// ─────────────────────────────────────────────
// PAGE NAVIGATION
// ─────────────────────────────────────────────
function loadPage(page, updateUrl = true) {
    _currentPage = page;

    const content = document.getElementById('content');
    if (!content) return;

    showPageLoader();

    // STEP 1: Fade-out content
    content.style.opacity = '0';
    content.style.transition = 'opacity 0.15s ease';

    // STEP 2: Wait for fade-out, then do DOM work
    setTimeout(function () {

        // Destroy existing DataTables
        destroyAllDataTables(content);

        // Cleanup dynamic navbar elements
        const navSearch = document.getElementById('navbarSearchContainer');
        if (navSearch) navSearch.remove();

        // Save to localStorage & update sidebar active state
        localStorage.setItem('activePage', page);
        document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
        const activeLink = document.querySelector(`.sidebar a[data-page="${page}"]`);
        if (activeLink) activeLink.classList.add('active');

        // Update URL with History API
        if (updateUrl) {
            history.pushState({ page: page }, '', `${APP_URL}/${page}`);
        }

        // STEP 3: Load new content via fetch
        fetch(`${APP_URL}/${page}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.text();
        })
        .then(html => {
            // Inject HTML, keep invisible during DataTables init
            content.style.visibility = 'hidden';
            content.innerHTML = html;

            // EXPLICITLY EXECUTE SCRIPTS (Replicate jQuery.html() behavior)
            const scripts = content.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                } else {
                    newScript.textContent = oldScript.textContent;
                }
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Scroll to top
            window.scrollTo(0, 0);

            // Re-attach listeners for dynamic content
            if (typeof attachNotificationListeners === 'function') attachNotificationListeners();
            if (typeof window.initSidebar === 'function') window.initSidebar();
            
            // Init VanillaPaginator for new content
            _initVanillaPaginators(content);

            // STEP 4: Init DataTables, then fade-in
            initGlobalTables(function () {
                setTimeout(function () {
                    hidePageLoader();
                    content.style.visibility = 'visible';
                    content.style.opacity = '0';
                    requestAnimationFrame(function () {
                        content.style.transition = 'opacity 0.18s ease';
                        content.style.opacity = '1';
                    });
                }, 1);
            });
        })
        .catch(err => {
            hidePageLoader();
            content.style.visibility = 'visible';
            content.style.opacity = '1';
            _currentPage = null;
            console.error('Error loading page:', err);
            content.innerHTML = '<div class="container-fluid p-4"><div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-200"><i class="bi bi-exclamation-circle me-2"></i>Halaman tidak ditemukan atau terjadi kesalahan.</div></div>';
        });

    }, 160);
}

// ─────────────────────────────────────────────
// DOCUMENT READY
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Get initial page from server or localStorage
    var initialPage = window.INITIAL_PAGE || localStorage.getItem('activePage') || 'dashboard';

    _currentPage = initialPage;

    // Initialize tables on first load
    setTimeout(initGlobalTables, 80);

    // Set initial history state
    history.replaceState({ page: initialPage }, '', `${APP_URL}/${initialPage}`);

    // Mark active sidebar item
    const activeLink = document.querySelector(`.sidebar a[data-page="${initialPage}"]`);
    if (activeLink) activeLink.classList.add('active');

    // Handle click on sidebar links and data-page elements
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.sidebar a[data-page], .profile a[data-page], .dashboard a[data-page], [data-page]');
        if (!link) return;
        if (link.id === 'startTestButton' || link.id === 'logout-btn') return;

        e.preventDefault();

        const page = link.getAttribute('data-page');
        if (!page) {
            console.error('data-page tidak ditemukan pada elemen ini:', link);
            return;
        }

        // Handle logout separately
        if (page === 'logout') {
            localStorage.removeItem('activePage');
            fetch(`${APP_URL}/logout`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(() => { window.location.href = APP_URL; })
            .catch(() => { window.location.href = APP_URL; });
            return;
        }

        // Close sidebar on mobile automatically
        if (window.innerWidth <= 991.98 && typeof window.closeSidebar === 'function') {
            setTimeout(() => window.closeSidebar(), 150);
        }

        loadPage(page);
    });

    // Handle browser back/forward
    window.addEventListener('popstate', function (e) {
        _currentPage = null;
        if (e.state && e.state.page) {
            loadPage(e.state.page, false);
        } else {
            loadPage('dashboard', false);
        }
    });

    // Footer scroll behavior
    var lastScrollTop = 0;
    var scrollTimeout;
    window.addEventListener('scroll', function () {
        if (scrollTimeout) clearTimeout(scrollTimeout);
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

    // Init notification polling
    initNotificationPolling();
});

// ─────────────────────────────────────────────
// SHOW MODAL (vanilla bootstrap emulation)
// ─────────────────────────────────────────────
window.showModal = function (message, gifUrl = null, onCloseCallback = null) {
    const modalEl = document.getElementById('customModal');
    if (!modalEl) {
        alert(message);
        if (onCloseCallback) onCloseCallback();
        return;
    }

    const modalMessage = document.getElementById('modalMessage');
    const modalGif = document.getElementById('modalGif');

    if (modalMessage) modalMessage.textContent = message;

    if (modalGif) {
        if (gifUrl) {
            modalGif.src = gifUrl;
            modalGif.style.display = 'block';
        } else {
            modalGif.style.display = 'none';
        }
    }

    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.error('Bootstrap Modal is not available');
        alert(message);
        if (onCloseCallback) onCloseCallback();
        return;
    }

    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();

    const closeBtn = document.getElementById('closeModal');
    if (closeBtn && onCloseCallback) {
        const newBtn = closeBtn.cloneNode(true);
        closeBtn.parentNode.replaceChild(newBtn, closeBtn);
        newBtn.addEventListener('click', onCloseCallback);
    }

    if (onCloseCallback) {
        modalEl.addEventListener('hidden.bs.modal', function handler() {
            onCloseCallback();
            modalEl.removeEventListener('hidden.bs.modal', handler);
        }, { once: true });
    }
};

// ─────────────────────────────────────────────
// NOTIFICATION POLLING
// ─────────────────────────────────────────────
let notificationAbortController = null;

function initNotificationPolling() {
    if (!window.notificationInterval) {
        checkNotifications();
        window.notificationInterval = setInterval(checkNotifications, 5000);
    }
    attachNotificationListeners();
}

function attachNotificationListeners() {
    const bellBtn = document.querySelector('.navbar-action-btn');
    if (!bellBtn) return;
    const dropdownElement = bellBtn.closest('.dropdown');
    if (dropdownElement) {
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
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const badge = document.querySelector('.navbar-action-btn .badge');
            if (badge) badge.classList.add('d-none');
        }
    })
    .catch(err => console.error('Error marking read:', err));
}

function checkNotifications() {
    if (!document.querySelector('.navbar-action-btn')) return;
    if (notificationAbortController) notificationAbortController.abort();
    notificationAbortController = new AbortController();

    fetch(`${APP_URL}/getnotifications`, {
        signal: notificationAbortController.signal
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(data => {
        if (data.status === 'success') updateNotificationUI(data.count, data.html);
    })
    .catch(err => {
        if (err.name === 'AbortError') return;
    });
}

function updateNotificationUI(count, html) {
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
    const dropdownMenu = document.querySelector('.navbar-notification-dropdown');
    if (dropdownMenu) {
        dropdownMenu.style.width = '320px';
        dropdownMenu.style.maxWidth = '90vw';
        dropdownMenu.innerHTML = html;
    }
}

// ─────────────────────────────────────────────
// DELETE / CONFIRM HELPERS
// ─────────────────────────────────────────────
function showDeleteConfirmation(options) {
    const {
        message = 'Apakah Anda yakin ingin menghapus data ini?',
        id = '',
        type = '',
        onConfirm = null
    } = options;

    showActionConfirmation({
        title: 'Hapus Data',
        message: message,
        btnText: 'Hapus',
        type: 'danger',
        onConfirm: function () {
            if (onConfirm && typeof onConfirm === 'function') onConfirm(id, type);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const confirmBtn = document.getElementById('confirmDeleteButton');
    if (confirmBtn) {
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        newConfirmBtn.addEventListener('click', function () {
            const id   = document.getElementById('deleteTargetId')   ? document.getElementById('deleteTargetId').value   : null;
            const type = document.getElementById('deleteTargetType') ? document.getElementById('deleteTargetType').value : null;
            const modalEl = document.getElementById('deleteConfirmModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            if (window._deleteConfirmCallback && typeof window._deleteConfirmCallback === 'function') {
                window._deleteConfirmCallback(id, type);
                window._deleteConfirmCallback = null;
            }
        });
    }
});

function showConfirmDelete(callback, message) {
    showDeleteConfirmation({
        message: message || 'Apakah Anda yakin ingin menghapus data ini?',
        id: null,
        type: 'generic',
        onConfirm: function () {
            if (typeof callback === 'function') callback();
        }
    });
}

function showAlert(message, isSuccess = true) {
    const toastMessageEl = document.getElementById('toastMessage');
    const toastEl = document.getElementById('liveToast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastBtn = document.getElementById('toastBtn');
    
    if (toastMessageEl && toastEl) {
        toastMessageEl.innerHTML = message;
        
        // Reset base classes
        toastEl.className = 'toast border-0 border-l-[4px] rounded-r-lg shadow-lg transition-all duration-200 pointer-events-auto';
        
        if (isSuccess) {
            toastEl.style.borderColor = '#10b981'; // emerald-500
            toastEl.style.backgroundColor = '#f3fcf6'; // extremely light green
            if (toastIcon) {
                toastIcon.className = 'bi bi-check-circle-fill text-xl';
                toastIcon.style.color = '#10b981';
            }
            if (toastTitle) {
                toastTitle.textContent = 'Success!';
                toastTitle.style.color = '#059669'; // emerald-600
            }
            if (toastMessageEl) {
                toastMessageEl.style.color = '#059669'; 
            }
            if (toastBtn) {
                toastBtn.textContent = 'Close';
                toastBtn.style.color = '#059669';
                toastBtn.style.borderColor = '#a7f3d0'; // emerald-200
                toastBtn.style.backgroundColor = 'transparent';
            }
        } else {
            toastEl.style.borderColor = '#ef4444'; // red-500
            toastEl.style.backgroundColor = '#fef2f2'; // red-50
            if (toastIcon) {
                toastIcon.className = 'bi bi-x-circle-fill text-xl';
                toastIcon.style.color = '#ef4444';
            }
            if (toastTitle) {
                toastTitle.textContent = 'Error!';
                toastTitle.style.color = '#dc2626'; // red-600
            }
            if (toastMessageEl) {
                toastMessageEl.style.color = '#dc2626';
            }
            if (toastBtn) {
                toastBtn.textContent = 'Close';
                toastBtn.style.color = '#dc2626';
                toastBtn.style.borderColor = '#fecaca'; // red-200
                toastBtn.style.backgroundColor = 'transparent';
            }
        }

        // Dispose old instance so Bootstrap re-reads the delay option
        const existingToast = bootstrap.Toast.getInstance(toastEl);
        if (existingToast && typeof existingToast.dispose === 'function') {
            existingToast.dispose();
        }
        const bootstrapToast = new bootstrap.Toast(toastEl, { delay: 1500, autohide: true });
        bootstrapToast.show();
        
        // Force hide after 2000ms
        setTimeout(() => bootstrapToast.hide(), 2000);
    } else {
        alert((isSuccess ? '✔ ' : '✘ ') + message);
    }
}

function showActionConfirmation(options) {
    const {
        title   = 'Konfirmasi',
        message = 'Apakah Anda yakin?',
        btnText = 'Ya',
        type    = 'primary',
        onConfirm = null
    } = options;

    const modalEl = document.getElementById('actionConfirmModal');
    if (!modalEl) {
        if (confirm(message)) { if (onConfirm) onConfirm(); }
        return;
    }

    const styles = {
        primary: { bg: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)', icon: 'bi-question-lg',    btnBg: '#0d6efd',                                          shadow: '0 4px 6px rgba(13, 110, 253, 0.3)' },
        success: { bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', icon: 'bi-check-lg',        btnBg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', shadow: '0 4px 6px rgba(16, 185, 129, 0.3)' },
        danger:  { bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', icon: 'bi-x-lg',            btnBg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', shadow: '0 4px 6px rgba(239, 68, 68, 0.3)' },
        warning: { bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', icon: 'bi-exclamation-lg', btnBg: '#f59e0b',                                           shadow: '0 4px 6px rgba(245, 158, 11, 0.3)' },
        info:    { bg: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', icon: 'bi-info-lg',         btnBg: '#3b82f6',                                           shadow: '0 4px 6px rgba(59, 130, 246, 0.3)' }
    };
    const style = styles[type] || styles.primary;

    const header = document.getElementById('actionConfirmHeader');
    if (header) header.style.background = style.bg;

    const icon = document.getElementById('actionConfirmIcon');
    if (icon) icon.className = `bi ${style.icon}`;

    const titleEl = document.getElementById('actionConfirmTitle');
    if (titleEl) titleEl.textContent = title;

    const msgEl = document.getElementById('actionConfirmMessage');
    if (msgEl) msgEl.innerHTML = message;

    const btn = document.getElementById('actionConfirmButton');
    if (btn) {
        const iconHtml = type === 'success' || type === 'primary'
            ? '<i class="bi bi-check-circle me-2"></i>'
            : type === 'danger' ? '<i class="bi bi-trash3 me-2"></i>' : '';
        btn.innerHTML = iconHtml + btnText;
        btn.style.background = style.btnBg;
        btn.style.boxShadow = style.shadow;

        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        newBtn.addEventListener('click', function () {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            setTimeout(function () {
                if (onConfirm) onConfirm();
            }, 150);

            setTimeout(function () {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 300);
        });
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ─────────────────────────────────────────────
// CUSTOM PAGINATION ENGINE (Tailwind CSS + Vanilla JS)
// ─────────────────────────────────────────────

/**
 * VanillaPaginator: Lightweight, feature-rich table pagination
 * - Tailwind CSS styled
 * - Per-page selector
 * - Search/filter support
 * - Smooth row transitions
 */
class VanillaPaginator {
    constructor(tableId, options = {}) {
        this.table = document.getElementById(tableId);
        if (!this.table) return;

        this.options = Object.assign({
            perPageOptions: [5, 10, 25, 50, 100],
            defaultPerPage: 10,
            searchable: true,
            showInfo: true,
        }, options);

        this.allRows = [];
        this.filteredRows = [];
        this.currentPage = 1;
        this.perPage = this.options.defaultPerPage;
        this.searchQuery = '';
        this.containerId = tableId + '_paginator';

        this._init();
    }

    _init() {
        // Cache all tbody rows
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        this.tbody = tbody;
        this.allRows = Array.from(tbody.querySelectorAll('tr'));
        this.filteredRows = [...this.allRows];

        // Build the UI wrapper around the table
        this._buildUI();
        this._render();
    }

    _buildUI() {
        // Wrap table in a container
        const wrapper = document.createElement('div');
        wrapper.id = this.containerId;
        wrapper.className = 'vp-wrapper';

        // Create top bar
        const topBar = document.createElement('div');
        topBar.className = 'flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-5 pt-5 pb-3 border-b border-slate-100 bg-white';

        // Per-page selector
        const perPageGroup = document.createElement('div');
        perPageGroup.className = 'flex items-center gap-2';
        perPageGroup.innerHTML = `
            <span class="text-sm font-medium text-slate-500 whitespace-nowrap">Tampilkan</span>
            <div class="relative">
                <select id="${this.containerId}_perPage"
                    class="appearance-none bg-white border border-slate-200 text-slate-700 text-sm font-semibold
                           rounded-lg pl-3 pr-8 py-1.5 cursor-pointer shadow-sm
                           hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                           transition-all duration-200">
                    ${this.options.perPageOptions.map(n =>
                        `<option value="${n}" ${n === this.perPage ? 'selected' : ''}>${n}</option>`
                    ).join('')}
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        `;

        // Search input
        const searchGroup = document.createElement('div');
        searchGroup.className = 'relative w-full sm:w-auto min-w-[200px] max-w-sm';
        searchGroup.innerHTML = `
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input id="${this.containerId}_search" type="text" placeholder="Cari data..."
                class="w-full pl-9 pr-4 py-1.5 text-sm bg-slate-50 border border-slate-200 rounded-lg shadow-sm
                       text-slate-700 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white
                       hover:border-slate-300 transition-all duration-200"/>
        `;

        topBar.appendChild(perPageGroup);

        const rightGroup = document.createElement('div');
        rightGroup.className = 'flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto justify-end';

        // Custom Buttons insertion
        const customContainer = this.table.closest('div') ? this.table.closest('div').parentElement : document;
        const customButtons = customContainer.querySelectorAll('.vp-custom-button');
        customButtons.forEach(btn => {
            btn.classList.remove('hidden', 'vp-custom-button');
            btn.style.display = 'inline-flex';
            rightGroup.appendChild(btn);
        });

        if (this.options.searchable) {
            rightGroup.appendChild(searchGroup);
        }

        if (rightGroup.children.length > 0) {
            topBar.appendChild(rightGroup);
        }
        // Info bar + pagination (bottom)
        const bottomBar = document.createElement('div');
        bottomBar.className = 'flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-4 border-t border-slate-100 bg-white';

        const infoEl = document.createElement('div');
        infoEl.id = `${this.containerId}_info`;
        infoEl.className = 'text-sm text-slate-500 font-medium';

        const paginationEl = document.createElement('nav');
        paginationEl.id = `${this.containerId}_pagination`;
        paginationEl.className = 'flex flex-wrap items-center gap-1 justify-center';
        paginationEl.setAttribute('aria-label', 'Navigasi halaman');

        bottomBar.appendChild(infoEl);
        bottomBar.appendChild(paginationEl);

        // Assemble: insert wrapper intelligently
        let insertPoint = this.table;
        let parentScroll = this.table.closest('.overflow-x-auto');
        if (parentScroll && parentScroll.children.length === 1 && parentScroll.children[0] === this.table) {
            // Replace the parent overflow-x-auto so our topBar doesn't scroll
            insertPoint = parentScroll;
        }

        insertPoint.parentNode.insertBefore(wrapper, insertPoint);

        const tableScrollWrap = document.createElement('div');
        tableScrollWrap.className = 'overflow-x-auto w-full';
        
        wrapper.appendChild(topBar);
        wrapper.appendChild(tableScrollWrap);
        tableScrollWrap.appendChild(this.table);
        wrapper.appendChild(bottomBar);

        if (parentScroll && parentScroll !== insertPoint && parentScroll.children.length === 0) {
            parentScroll.remove();
        } else if (parentScroll === insertPoint) {
            parentScroll.remove();
        }

        // Store refs
        this._perPageSelect = document.getElementById(`${this.containerId}_perPage`);
        this._searchInput   = document.getElementById(`${this.containerId}_search`);
        this._infoEl        = document.getElementById(`${this.containerId}_info`);
        this._paginationEl  = document.getElementById(`${this.containerId}_pagination`);

        // Event listeners
        this._perPageSelect.addEventListener('change', () => {
            this.perPage = parseInt(this._perPageSelect.value);
            this.currentPage = 1;
            this._render();
        });

        if (this._searchInput) {
            let debounceTimer;
            this._searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.searchQuery = this._searchInput.value.toLowerCase().trim();
                    this.currentPage = 1;
                    this._filterRows();
                    this._render();
                }, 250);
            });
        }
    }

    _filterRows() {
        if (!this.searchQuery) {
            this.filteredRows = [...this.allRows];
            return;
        }
        this.filteredRows = this.allRows.filter(row => {
            return row.textContent.toLowerCase().includes(this.searchQuery);
        });
    }

    _render() {
        const total = this.filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / this.perPage));
        if (this.currentPage > totalPages) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.perPage;
        const end   = Math.min(start + this.perPage, total);

        // Show/hide rows
        this.allRows.forEach(row => { row.style.display = 'none'; });
        this.filteredRows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? '' : 'none';
        });

        // Update info text
        if (this._infoEl) {
            if (total === 0) {
                this._infoEl.innerHTML = `<span class="text-slate-400">Tidak ada data yang ditemukan</span>`;
            } else {
                this._infoEl.innerHTML =
                    `Menampilkan <strong class="text-slate-700">${start + 1}</strong> – ` +
                    `<strong class="text-slate-700">${end}</strong> dari ` +
                    `<strong class="text-slate-700">${total}</strong> data` +
                    (this.searchQuery ? ` <span class="text-blue-500">(difilter)</span>` : '');
            }
        }

        // Update pagination
        this._renderPagination(totalPages);
    }

    _renderPagination(totalPages) {
        if (!this._paginationEl) return;
        this._paginationEl.innerHTML = '';

        const current = this.currentPage;

        const btnBase = `inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-semibold transition-all duration-200 select-none`;
        const btnActive = `bg-blue-600 text-white shadow-md shadow-blue-200 scale-105`;
        const btnNormal = `bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 shadow-sm`;
        const btnDisabled = `bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed`;

        const makeBtn = (label, page, isDisabled = false, isActive = false, ariaLabel = '') => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = label;
            btn.setAttribute('aria-label', ariaLabel || label);
            btn.className = `${btnBase} ${isDisabled ? btnDisabled : isActive ? btnActive : btnNormal}`;
            if (!isDisabled) {
                btn.addEventListener('click', () => {
                    this.currentPage = page;
                    this._render();
                    // Smooth scroll to table top
                    this.table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            } else {
                btn.disabled = true;
            }
            return btn;
        };

        // ← First & Prev
        this._paginationEl.appendChild(makeBtn(
            `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/></svg>`,
            1, current === 1, false, 'Halaman pertama'
        ));
        this._paginationEl.appendChild(makeBtn(
            `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>`,
            current - 1, current === 1, false, 'Halaman sebelumnya'
        ));

        // Page number buttons (window of 5)
        let pageRange = this._getPageRange(current, totalPages);
        pageRange.forEach(p => {
            if (p === '...') {
                const ellipsis = document.createElement('span');
                ellipsis.className = `inline-flex items-center justify-center w-9 h-9 text-slate-400 text-sm font-semibold`;
                ellipsis.textContent = '···';
                this._paginationEl.appendChild(ellipsis);
            } else {
                this._paginationEl.appendChild(makeBtn(p, p, false, p === current, `Halaman ${p}`));
            }
        });

        // Next & Last →
        this._paginationEl.appendChild(makeBtn(
            `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>`,
            current + 1, current === totalPages, false, 'Halaman berikutnya'
        ));
        this._paginationEl.appendChild(makeBtn(
            `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M6 5l7 7-7 7"/></svg>`,
            totalPages, current === totalPages, false, 'Halaman terakhir'
        ));
    }

    _getPageRange(current, total) {
        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

        const delta = 2;
        const range = [];
        const left  = Math.max(2, current - delta);
        const right = Math.min(total - 1, current + delta);

        range.push(1);
        if (left > 2) range.push('...');
        for (let i = left; i <= right; i++) range.push(i);
        if (right < total - 1) range.push('...');
        range.push(total);

        return range;
    }

    /** Public API: refresh (use after dynamic row changes) */
    refresh() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        this.allRows = Array.from(tbody.querySelectorAll('tr'));
        this._filterRows();
        this._render();
    }

    /** Public API: go to specific page */
    goTo(page) {
        this.currentPage = page;
        this._render();
    }
}

// ─────────────────────────────────────────────
// DATATABLES — VANILLA JS WRAPPER
// ─────────────────────────────────────────────

/**
 * Destroy all DataTable instances inside a container element.
 * Works with the DataTables plain JS API (no jQuery).
 */
function destroyAllDataTables(container) {
    if (!container) return;
    // DataTables plain API stores instances; check if DataTable global exists
    if (typeof DataTable === 'undefined') return;
    container.querySelectorAll('table').forEach(function (tbl) {
        if (DataTable.isDataTable(tbl)) {
            new DataTable(tbl).destroy();
        }
    });
}

/**
 * Initialize DataTables for all tables inside #content.
 * Accepts an optional callback fired when all tables finish initializing.
 */
function initGlobalTables(onReady) {
    const content = document.getElementById('content');
    if (!content) {
        if (typeof onReady === 'function') onReady();
        return;
    }

    // Select tables, excluding calendar and explicitly opted-out tables
    const tables = Array.from(
        content.querySelectorAll('table:not(#calendarTable):not(.no-datatable)')
    );

    if (tables.length === 0) {
        if (typeof onReady === 'function') onReady();
        return;
    }

    // Check DataTables plain JS global
    if (typeof DataTable === 'undefined') {
        console.warn('DataTable is not loaded.');
        if (typeof onReady === 'function') onReady();
        return;
    }

    var pendingInits = 0;

    tables.forEach(function (tbl) {
        if (!DataTable.isDataTable(tbl)) {
            pendingInits++;

            new DataTable(tbl, {
                scrollX: false,
                autoWidth: false,
                pageLength: 10,
                dom: '<"dt-top-wrapper"lf>t<"dt-bottom-wrapper"ip>',
                language: {
                    search: '',
                    searchPlaceholder: '🔍 Cari data...',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    info: 'Menampilkan <strong>_START_</strong> – <strong>_END_</strong> dari <strong>_TOTAL_</strong> data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: "<div class='text-center py-10 text-slate-400'>Tidak ada data yang cocok</div>",
                    paginate: {
                        first: '«',
                        last: '»',
                        next: '›',
                        previous: '‹'
                    }
                },
                initComplete: function () {
                    // Move .dt-custom-button elements into the filter bar
                    const wrapper = tbl.closest('.max-w-7xl');
                    if (wrapper) {
                        const customButtons = wrapper.querySelectorAll('.dt-custom-button');
                        if (customButtons.length) {
                            const dtWrapper = tbl.closest('.dataTables_wrapper');
                            const filterEl = dtWrapper ? dtWrapper.querySelector('.dataTables_filter') : null;
                            if (filterEl) {
                                filterEl.classList.add('flex', 'items-center', 'gap-3');
                                customButtons.forEach(function (btn) {
                                    filterEl.appendChild(btn);
                                    btn.classList.remove('hidden');
                                    btn.style.display = 'inline-flex';
                                });
                            }
                        }
                    }

                    // Trigger custom event for page-specific scripts
                    tbl.dispatchEvent(new CustomEvent('dt.initComplete', { bubbles: true }));

                    // Count down; fire onReady when all tables done
                    pendingInits--;
                    if (pendingInits <= 0 && typeof onReady === 'function') {
                        onReady();
                    }
                }
            });
        }
    });

    // If all tables were already initialized
    if (pendingInits === 0 && typeof onReady === 'function') {
        onReady();
    }
}

// ─────────────────────────────────────────────
// AUTO-INIT: VanillaPaginator for tables with
// data-paginator="true" attribute (opt-in)
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    _initVanillaPaginators();
});

// Called again after AJAX page loads
function _initVanillaPaginators(root) {
    const context = root || document;
    context.querySelectorAll('table[data-paginator]').forEach(function (tbl) {
        if (!tbl.id) {
            tbl.id = 'vp_' + Math.random().toString(36).slice(2, 9);
        }
        // Avoid double-init
        if (tbl.closest('.vp-wrapper')) return;

        const opts = {};
        if (tbl.dataset.paginatorPerpage)  opts.defaultPerPage  = parseInt(tbl.dataset.paginatorPerpage);
        if (tbl.dataset.paginatorOptions)  opts.perPageOptions  = JSON.parse(tbl.dataset.paginatorOptions);
        if (tbl.dataset.paginatorSearch === 'false') opts.searchable = false;

        new VanillaPaginator(tbl.id, opts);
    });
}
