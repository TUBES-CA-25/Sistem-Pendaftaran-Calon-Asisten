// Active link highlight and Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar function
    function toggleSidebar() {
        const sidebarElement = document.querySelector('.sidebar');
        const overlayElement = document.getElementById('sidebarOverlay');

        if (sidebarElement && overlayElement) {
            sidebarElement.classList.toggle('-translate-x-full');
            sidebarElement.classList.toggle('translate-x-0');
            
            overlayElement.classList.toggle('opacity-0');
            overlayElement.classList.toggle('invisible');
            overlayElement.classList.toggle('opacity-100');
            overlayElement.classList.toggle('visible');
            overlayElement.classList.toggle('pointer-events-none');
            overlayElement.classList.toggle('pointer-events-auto');
            
            document.body.classList.toggle('sidebar-open');
            document.body.classList.toggle('overflow-hidden');
        }
    }

    // Close sidebar function
    function closeSidebar() {
        const sidebarElement = document.querySelector('.sidebar');
        const overlayElement = document.getElementById('sidebarOverlay');

        if (sidebarElement && overlayElement) {
            sidebarElement.classList.add('-translate-x-full');
            sidebarElement.classList.remove('translate-x-0');
            
            overlayElement.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            overlayElement.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
            
            document.body.classList.remove('sidebar-open', 'overflow-hidden');
        }
    }

    // openSidebar() dihapus: Sidebar dibuka lewat toggleSidebar(); openSidebar() tidak pernah dipanggil.

    // Expose functions globally immediately
    window.toggleSidebar = toggleSidebar;
    window.closeSidebar = closeSidebar;

    // --- Static Listeners (Run once) ---
    function initStaticSidebar() {
        // Create overlay if doesn't exist
        let overlay = document.getElementById('sidebarOverlay');
        if (!overlay) {
            const newOverlay = document.createElement('div');
            newOverlay.id = 'sidebarOverlay';
            newOverlay.className = 'fixed inset-0 bg-black/40 z-[1035] opacity-0 invisible transition-all duration-300 backdrop-blur-sm pointer-events-none lg:hidden';
            document.body.appendChild(newOverlay);
            overlay = newOverlay;
        }

        // Handle Dropdown Toggles
        const dropdowns = document.querySelectorAll('.sidebar .dropdown-toggle');
        dropdowns.forEach(dropdown => {
            // Remove existing to be safe, though usually runs once
            // Cloning isn't feasible here as it loses reference for other scripts? No, keeping it simple.
            // Assuming this runs once on static sidebar.
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle the submenu
                const submenu = this.nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    submenu.classList.toggle('show');
                    this.classList.toggle('show');
                }
            });
        });

        // Handle Link Activation - Removed to let app.js handle all navigation logic
        // and mobile sidebar closing is handled there or can be added globally if needed.


        // Overlay click to close
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeSidebar();
            });
        }

        // Close sidebar when clicking outside on mobile (only when sidebar is open)
        document.addEventListener('click', function(e) {
            if (window.innerWidth > 991.98) return; // Skip on desktop

            const sidebarElement = document.querySelector('.sidebar');
            const burgerButton = document.getElementById('burgerMenuBtn');
            const overlayElement = document.getElementById('sidebarOverlay');

            // Check if sidebar is open
            if (sidebarElement && sidebarElement.classList.contains('show')) {
                // Check if click is outside sidebar and burger button
                const isClickInsideSidebar = sidebarElement.contains(e.target);
                const isClickOnBurger = burgerButton && burgerButton.contains(e.target);
                const isClickOnOverlay = overlayElement && overlayElement.contains(e.target);

                if (!isClickInsideSidebar && !isClickOnBurger && !isClickOnOverlay) {
                    closeSidebar();
                }
            }
        });

        // Handle window resize - close sidebar when going to desktop
        let previousWidth = window.innerWidth;
        window.addEventListener('resize', function() {
            const currentWidth = window.innerWidth;

            // Close sidebar when transitioning to desktop
            if (previousWidth <= 991.98 && currentWidth > 991.98) {
                closeSidebar();
            }

            previousWidth = currentWidth;
        });

        // Prevent sidebar clicks from closing it

    }

    // --- Dynamic Listeners (Run on page load/AJAX) ---
    function initDynamicSidebarListeners() {
        // Burger button click handler
        const burgerBtn = document.getElementById('burgerMenuBtn');
        if (burgerBtn) {
            // Check if listener is already attached? 
            // Since elements are replaced, we can just attach. 
            // If somehow element persisted, we might duplicate.
            // Using a property or class to mark attached could work, but for now simple attach.
            // Removing old listener is hard without reference function.
            // But usually this function is called after *loading new content*.
            
            // To be safe against double-attachment on same element:
            if (burgerBtn.dataset.sidebarListener) return;
            
            burgerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
            burgerBtn.dataset.sidebarListener = "true";
        }
    }

    // Expose dynamic init function
    window.initSidebar = initDynamicSidebarListeners;

    // Run initialization
    initStaticSidebar();
    initDynamicSidebarListeners();
});
