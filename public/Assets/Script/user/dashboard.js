/**
 * Dashboard User JavaScript
 * Handles calendar generation and other dashboard interactions
 */

/**
 * Generate calendar for specific month and year
 */
function generateCalendar(year, month) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    const currentYear = today.getFullYear();
    const currentMonth = today.getMonth();
    const currentDate = today.getDate();

    let html = '';

    // Empty cells before first day of month
    for (let i = 0; i < firstDay; i++) {
        html += '<div class="calendar-date other-month"></div>';
    }

    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = (day === currentDate &&
                        month === currentMonth &&
                        year === currentYear);

        const classes = `calendar-date ${isToday ? 'today' : ''}`;
        html += `<div class="${classes}">${day}</div>`;
    }

    // Fill remaining cells (optional, for complete grid)
    const totalCells = firstDay + daysInMonth;
    const remainingCells = 7 - (totalCells % 7);
    if (remainingCells < 7) {
        for (let i = 0; i < remainingCells; i++) {
            html += '<div class="calendar-date other-month"></div>';
        }
    }

    return html;
}

/**
 * Update calendar month display
 */
function updateCalendarMonth(year, month) {
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const monthYearElement = document.getElementById('calendar-month-year');
    if (monthYearElement) {
        monthYearElement.textContent = `${monthNames[month]} ${year}`;
    } else {
        console.warn('Calendar month-year element not found');
    }
}

/**
 * Initialize calendar on page load
 */
function initializeCalendar() {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();

    // Generate and insert calendar
    const calendarContainer = document.getElementById('calendar-dates');
    if (calendarContainer) {
        calendarContainer.innerHTML = generateCalendar(year, month);
    } else {
        console.warn('Calendar dates container not found');
        return;
    }

    // Update month/year display
    updateCalendarMonth(year, month);
}

/**
 * Animate circular progress on load
 */
function animateCircularProgress() {
    const progressCircle = document.querySelector('.progress-ring-circle');
    if (progressCircle) {
        // Add animation class or trigger animation
        progressCircle.style.transition = 'stroke-dashoffset 1.2s ease-in-out';
    }
}

/**
 * Animate stepper line on load
 */
function animateStepperLine() {
    const stepperLine = document.querySelector('.stepper-line');
    if (stepperLine) {
        // Get current width from inline style
        const currentStyle = stepperLine.style.width;
        const targetWidth = currentStyle || '0%';

        // Start from 0 width
        stepperLine.style.transition = 'none';
        stepperLine.style.width = '0';

        // Force reflow
        stepperLine.offsetHeight;

        // Animate to target width after brief delay
        setTimeout(() => {
            stepperLine.style.transition = 'width 0.6s ease-in-out';
            stepperLine.style.width = targetWidth;
        }, 100);
    } else {
        console.warn('Stepper line not found');
    }
}

/**
 * Add click handlers to calendar dates (future enhancement)
 */
function addCalendarClickHandlers() {
    const calendarDates = document.querySelectorAll('.calendar-date:not(.other-month)');
    calendarDates.forEach(dateElement => {
        dateElement.addEventListener('click', function() {
            // Remove active class from all dates
            calendarDates.forEach(el => el.classList.remove('active'));
            // Add active class to clicked date
            this.classList.add('active');

            // Optional: Do something with the selected date
            const selectedDate = this.textContent;
            console.log('Selected date:', selectedDate);
        });
    });
}

/**
 * Initialize all dashboard features when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize calendar
    initializeCalendar();

    // Add calendar click handlers
    addCalendarClickHandlers();

    // Animate progress indicators
    animateCircularProgress();
    animateStepperLine();

    console.log('Dashboard initialized successfully');
});

/**
 * Refresh calendar (can be called externally)
 */
window.refreshCalendar = function(year, month) {
    const calendarContainer = document.getElementById('calendar-dates');
    if (calendarContainer) {
        calendarContainer.innerHTML = generateCalendar(year, month);
        updateCalendarMonth(year, month);
        addCalendarClickHandlers();
    }
};

/**
 * Navigate to different pages (used by Edit Profile button)
 */
window.navigateTo = function(page) {
    const baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/home';

    switch(page) {
        case 'biodata':
            window.location.href = baseUrl + '/biodata';
            break;
        case 'berkas':
            window.location.href = baseUrl + '/berkas';
            break;
        case 'dashboard':
            window.location.href = baseUrl + '/dashboard';
            break;
        default:
            console.warn('Unknown page:', page);
    }
};
