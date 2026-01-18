// ============================================
// DASHBOARD SCRIPT - Progress Circle & Calendar
// Full comments untuk pembelajaran
// ============================================

// ============================================
// 1. PROGRESS CIRCLE ANIMATION
// ============================================

/**
 * Function untuk animate progress circle
 * Menggunakan SVG stroke-dashoffset untuk animasi
 */
function initProgressCircle() {
    // Ambil element progress circle
    const progressCircle = document.querySelector('.progress-circle');
    
    if (!progressCircle) return; // Exit jika tidak ada element
    
    // Ambil percentage dari data attribute
    const percentage = parseInt(progressCircle.getAttribute('data-percentage'));
    
    // Ambil SVG circle element
    const progressBar = progressCircle.querySelector('.progress-bar');
    
    // Calculate stroke-dashoffset berdasarkan percentage
    // Formula: circumference * (1 - percentage/100)
    // Circumference = 2 * PI * radius = 2 * 3.14159 * 52 = 327
    const circumference = 327;
    const offset = circumference * (1 - percentage / 100);
    
    // Animate dengan setTimeout untuk smooth transition
    setTimeout(() => {
        progressBar.style.strokeDashoffset = offset;
    }, 100);
    
    console.log(`✅ Progress circle animated: ${percentage}%`);
}

// ============================================
// 2. CALENDAR FUNCTIONALITY
// ============================================

/**
 * Calendar state management
 */
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

// Array nama bulan (Bahasa Indonesia bisa diganti)
const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Array nama hari
const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

/**
 * Function untuk generate calendar HTML
 */
function generateCalendar(month, year) {
    // Get first day of month
    const firstDay = new Date(year, month, 1).getDay();
    
    // Get number of days in month
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Get today's date untuk highlight
    const today = new Date();
    const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;
    const todayDate = today.getDate();
    
    // Build calendar HTML
    let calendarHTML = '<div class="calendar-days">';
    
    // Header hari (Sun, Mon, Tue, etc)
    days.forEach(day => {
        calendarHTML += `<div class="calendar-day-header">${day}</div>`;
    });
    
    // Empty cells untuk hari sebelum tanggal 1
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="calendar-day empty"></div>';
    }
    
    // Tanggal 1 sampai akhir bulan
    for (let day = 1; day <= daysInMonth; day++) {
        // Check if today
        const isToday = isCurrentMonth && day === todayDate;
        const todayClass = isToday ? 'today' : '';
        
        calendarHTML += `<div class="calendar-day ${todayClass}">${day}</div>`;
    }
    
    calendarHTML += '</div>';
    
    // Insert ke DOM
    const calendarBody = document.getElementById('calendarBody');
    if (calendarBody) {
        calendarBody.innerHTML = calendarHTML;
    }
    
    // Update month display
    const currentMonthDisplay = document.getElementById('currentMonth');
    if (currentMonthDisplay) {
        currentMonthDisplay.textContent = `${months[month]} ${year}`;
    }
}

/**
 * Function untuk navigate ke bulan sebelumnya
 */
function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
}

/**
 * Function untuk navigate ke bulan berikutnya
 */
function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
}

/**
 * Initialize calendar
 */
function initCalendar() {
    // Generate calendar pertama kali
    generateCalendar(currentMonth, currentYear);
    
    // Setup event listeners untuk navigation
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', previousMonth);
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', nextMonth);
    }
    
    console.log('✅ Calendar initialized');
}

// ============================================
// 3. ADD CSS UNTUK CALENDAR (Dynamic)
// ============================================

/**
 * Function untuk inject calendar CSS
 * Karena calendar dynamic, kita perlu CSS tambahan
 */
function injectCalendarStyles() {
    const styles = `
        <style>
            /* Calendar Days Grid */
            .calendar-days {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 0.5rem;
            }
            
            /* Day Header (Sun, Mon, etc) */
            .calendar-day-header {
                text-align: center;
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--color-text-secondary);
                padding: 0.5rem 0;
                text-transform: uppercase;
            }
            
            /* Calendar Day Cell */
            .calendar-day {
                aspect-ratio: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.875rem;
                color: var(--color-text-primary);
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .calendar-day:not(.empty):hover {
                background: var(--color-primary-light);
                color: var(--color-primary);
            }
            
            /* Empty days (sebelum tanggal 1) */
            .calendar-day.empty {
                cursor: default;
            }
            
            /* Today highlight */
            .calendar-day.today {
                background: var(--color-primary);
                color: var(--color-white);
                font-weight: 600;
            }
            
            .calendar-day.today:hover {
                background: var(--color-primary-dark);
            }
        </style>
    `;
    
    // Inject ke head
    document.head.insertAdjacentHTML('beforeend', styles);
}

// ============================================
// 4. GOOGLE CALENDAR API INTEGRATION (FUTURE)
// ============================================

/**
 * Function untuk fetch events dari Google Calendar API
 * TODO: Implement setelah dapat API Key
 */
async function fetchGoogleCalendarEvents() {
    // Placeholder untuk future implementation
    // const API_KEY = 'YOUR_API_KEY';
    // const CALENDAR_ID = 'YOUR_CALENDAR_ID';
    
    // try {
    //     const response = await fetch(
    //         `https://www.googleapis.com/calendar/v3/calendars/${CALENDAR_ID}/events?key=${API_KEY}`
    //     );
    //     const data = await response.json();
    //     return data.items;
    // } catch (error) {
    //     console.error('Error fetching calendar events:', error);
    //     return [];
    // }
    
    console.log('⏳ Google Calendar API integration pending...');
}

// ============================================
// 5. SMOOTH SCROLL ANIMATION (BONUS)
// ============================================

/**
 * Function untuk smooth scroll ke section
 * Bisa dipanggil dari anywhere
 */
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// ============================================
// 6. CARD HOVER ANIMATION (BONUS)
// ============================================

/**
 * Add subtle animation saat hover cards
 */
function initCardAnimations() {
    const cards = document.querySelectorAll('.stats-card, .biodata-card, .profile-widget, .calendar-widget');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// ============================================
// 7. INITIALIZE EVERYTHING ON PAGE LOAD
// ============================================

/**
 * Main initialization function
 * Dipanggil saat DOM ready
 */
function initDashboard() {
    console.log('🚀 Initializing Dashboard...');
    
    // 1. Init progress circle animation
    initProgressCircle();
    
    // 2. Inject calendar styles
    injectCalendarStyles();
    
    // 3. Init calendar
    initCalendar();
    
    // 4. Init card animations
    initCardAnimations();
    
    // 5. Future: Fetch Google Calendar events
    // fetchGoogleCalendarEvents();
    
    console.log('✅ Dashboard initialized successfully!');
}

// ============================================
// RUN ON PAGE LOAD
// ============================================

// Check if DOM is already loaded
if (document.readyState === 'loading') {
    // DOM masih loading, tunggu sampai ready
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    // DOM sudah ready, langsung init
    initDashboard();
}

// ============================================
// EXPORT FUNCTIONS (untuk dipakai di tempat lain)
// ============================================

// Jika pakai module system, bisa export functions
// export { initProgressCircle, generateCalendar, smoothScrollTo };

// ============================================
// DEBUG MODE (untuk development)
// ============================================

// Uncomment untuk debug
// console.log('Dashboard script loaded!');
// console.log('Current month:', months[currentMonth]);
// console.log('Current year:', currentYear);