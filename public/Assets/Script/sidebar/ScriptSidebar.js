// ============================================
// SIDEBAR SCRIPT - WITH DROPDOWN SUBMENU
// ============================================

// DOM Elements
const sidebar = document.querySelector('.sidebar');
const sidebarToggle = document.getElementById('btn');
const mainContent = document.querySelector('.main-content');
const navLinks = document.querySelectorAll('.nav-link:not(.nav-parent)');
const parentLinks = document.querySelectorAll('.nav-parent');
const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');

// State
let isMobile = window.innerWidth <= 768;

// ============================================
// INITIALIZE
// ============================================

function init() {
    // Set mobile state
    isMobile = window.innerWidth <= 768;
    
    // On mobile, sidebar is closed by default
    if (isMobile) {
        sidebar.classList.remove('active');
    }
    
    // Restore active page from session
    restoreActivePage();
    
    // Setup dropdown functionality
    setupDropdowns();
    
    console.log('✅ Sidebar with dropdown initialized');
}

// ============================================
// DROPDOWN FUNCTIONALITY
// ============================================

function setupDropdowns() {
    parentLinks.forEach(parent => {
        parent.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const menuItem = this.closest('.menu-item-has-children');
            const wasOpen = menuItem.classList.contains('open');
            
            // Close all other dropdowns
            menuItemsWithChildren.forEach(item => {
                if (item !== menuItem) {
                    item.classList.remove('open');
                }
            });
            
            // Toggle current dropdown
            if (wasOpen) {
                menuItem.classList.remove('open');
            } else {
                menuItem.classList.add('open');
            }
        });
    });
}

// ============================================
// TOGGLE SIDEBAR
// ============================================

function toggleSidebar() {
    sidebar.classList.toggle('active');
    
    // Lock body scroll on mobile when sidebar is open
    if (isMobile) {
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
}

// ============================================
// CLOSE SIDEBAR
// ============================================

function closeSidebar() {
    sidebar.classList.remove('active');
    
    if (isMobile) {
        document.body.style.overflow = '';
    }
}

// ============================================
// HANDLE NAV LINK CLICK
// ============================================

function handleNavClick(e) {
    e.preventDefault();
    
    const clickedLink = e.currentTarget;
    const page = clickedLink.getAttribute('data-page');
    
    // Update active state - remove from all links
    navLinks.forEach(link => link.classList.remove('active'));
    
    // Add active to clicked link
    clickedLink.classList.add('active');
    
    // If it's a submenu link, also keep parent highlighted
    if (clickedLink.classList.contains('submenu-link')) {
        const parentItem = clickedLink.closest('.menu-item-has-children');
        if (parentItem) {
            const parentLink = parentItem.querySelector('.nav-parent');
            if (parentLink) {
                parentLink.style.background = 'var(--color-primary-light)';
                parentLink.style.color = 'var(--color-primary)';
            }
        }
    }
    
    // Save to session
    if (page) {
        sessionStorage.setItem('activePage', page);
    }
    
    // Close sidebar on mobile after clicking
    if (isMobile) {
        setTimeout(() => {
            closeSidebar();
        }, 300);
    }
    
    console.log('📄 Navigating to:', page);
}

// ============================================
// RESTORE ACTIVE PAGE
// ============================================

function restoreActivePage() {
    const activePage = sessionStorage.getItem('activePage');
    
    if (activePage) {
        const activeLink = document.querySelector(`[data-page="${activePage}"]`);
        if (activeLink) {
            // Remove active from all
            navLinks.forEach(link => link.classList.remove('active'));
            
            // Add active to current
            activeLink.classList.add('active');
            
            // If it's in a submenu, open the parent
            if (activeLink.classList.contains('submenu-link')) {
                const parentItem = activeLink.closest('.menu-item-has-children');
                if (parentItem) {
                    parentItem.classList.add('open');
                    
                    // Highlight parent
                    const parentLink = parentItem.querySelector('.nav-parent');
                    if (parentLink) {
                        parentLink.style.background = 'var(--color-primary-light)';
                        parentLink.style.color = 'var(--color-primary)';
                    }
                }
            }
        }
    } else {
        // Default: activate dashboard
        const dashboardLink = document.querySelector('[data-page="dashboard"]');
        if (dashboardLink) {
            dashboardLink.classList.add('active');
        }
    }
}

// ============================================
// HANDLE WINDOW RESIZE
// ============================================

function handleResize() {
    const wasMobile = isMobile;
    isMobile = window.innerWidth <= 768;
    
    // If switching from mobile to desktop
    if (wasMobile && !isMobile) {
        sidebar.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // If switching from desktop to mobile
    if (!wasMobile && isMobile) {
        sidebar.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// ============================================
// HANDLE CLICK OUTSIDE (Mobile)
// ============================================

function handleClickOutside(e) {
    if (!isMobile) return;
    
    if (sidebar.classList.contains('active')) {
        // If click is outside sidebar and toggle button
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            closeSidebar();
        }
    }
}

// ============================================
// HANDLE ESC KEY
// ============================================

function handleEscKey(e) {
    if (e.key === 'Escape') {
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        }
        
        // Also close all dropdowns
        menuItemsWithChildren.forEach(item => {
            item.classList.remove('open');
        });
    }
}

// ============================================
// EVENT LISTENERS
// ============================================

// Toggle button
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleSidebar();
    });
}

// Nav links (including submenu links)
navLinks.forEach(link => {
    link.addEventListener('click', handleNavClick);
});

// Window resize (with debounce)
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(handleResize, 250);
});

// Click outside
document.addEventListener('click', handleClickOutside);

// ESC key
document.addEventListener('keydown', handleEscKey);

// ============================================
// RUN ON PAGE LOAD
// ============================================

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// ============================================
// SMOOTH SCROLL TO TOP (Bonus)
// ============================================

const logoContainer = document.querySelector('.logo-container');
if (logoContainer) {
    logoContainer.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ============================================
// HELPER: Close all dropdowns
// ============================================

function closeAllDropdowns() {
    menuItemsWithChildren.forEach(item => {
        item.classList.remove('open');
    });
}

// Auto-close dropdowns when clicking submenu item on mobile
if (isMobile) {
    const submenuLinks = document.querySelectorAll('.submenu-link');
    submenuLinks.forEach(link => {
        link.addEventListener('click', () => {
            setTimeout(() => {
                closeAllDropdowns();
            }, 300);
        });
    });
}