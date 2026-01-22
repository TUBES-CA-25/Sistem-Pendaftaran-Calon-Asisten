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
