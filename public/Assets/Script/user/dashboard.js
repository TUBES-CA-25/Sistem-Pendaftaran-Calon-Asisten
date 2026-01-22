/**
 * Dashboard User JavaScript
 * Handles calendar generation and other dashboard interactions
 */

var currentYear, currentMonth;
var calendarActivities = [];

/**
 * Generate calendar for specific month and year
 */
function generateCalendar(year, month, activities = []) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    const currentFullYear = today.getFullYear();
    const currentFullMonth = today.getMonth();
    const currentDate = today.getDate();

    let html = '';

    // Empty cells before first day of month
    for (let i = 0; i < firstDay; i++) {
        html += '<div class="calendar-date other-month"></div>';
    }

    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = (day === currentDate &&
                        month === currentFullMonth &&
                        year === currentFullYear);

        // Check if this date has activities
        const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayActivities = activities.filter(act => act.tanggal === formattedDate);
        const hasActivity = dayActivities.length > 0;

        let tooltiptxt = '';
        if (hasActivity) {
            tooltiptxt = dayActivities.map(act => `${act.jenis}: ${act.judul}`).join('\n');
        }

        let classes = `calendar-date ${isToday ? 'today' : ''} ${hasActivity ? 'has-activity' : ''}`;
        
        let activityDots = '';
        if (hasActivity) {
            activityDots = '<div class="activity-dots">';
            dayActivities.slice(0, 3).forEach(act => {
                const colorClass = act.jenis === 'Wawancara' ? 'bg-primary' : (act.jenis === 'Presentasi' ? 'bg-info' : 'bg-warning');
                activityDots += `<span class="dot ${colorClass}"></span>`;
            });
            activityDots += '</div>';
        }

        html += `
            <div class="${classes}" 
                 data-date="${formattedDate}" 
                 onclick="showDayDetails('${formattedDate}')"
                 data-bs-toggle="tooltip"
                 data-bs-html="true"
                 data-bs-title="${tooltiptxt.replace(/"/g, '&quot;').replace(/\n/g, '<br>')}">
                <span class="date-num">${day}</span>
                ${activityDots}
            </div>`;
    }

    // Fill remaining cells
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
 * Handle date click to show details (if needed)
 */
window.showDayDetails = function(dateStr) {
    const dayActivities = calendarActivities.filter(act => act.tanggal === dateStr);
    if (dayActivities.length === 0) return;

    // Optional: Show a toast or small modal with activity list
    console.log('Activities for ' + dateStr + ':', dayActivities);
};

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
    }
}

/**
 * Fetch activities from server
 */
async function fetchActivities(year, month) {
    try {
        const url = (typeof APP_URL !== 'undefined') ? `${APP_URL}/getactivities` : '/Sistem-Pendaftaran-Calon-Asisten/public/getactivities';
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ year, month: month + 1 })
        });
        const result = await response.json();
        if (result.status === 'success') {
            calendarActivities = result.data;
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching activities:', error);
    }
    return [];
}

/**
 * Initialize calendar
 */
async function initializeCalendar() {
    const now = new Date();
    currentYear = now.getFullYear();
    currentMonth = now.getMonth();

    // Use initial data if available, otherwise fetch
    if (window.initialActivities && Array.isArray(window.initialActivities)) {
        calendarActivities = window.initialActivities;
    } else {
        calendarActivities = await fetchActivities(currentYear, currentMonth);
    }

    renderCalendar();
    initializeNavigation();
}

/**
 * Render/Redraw calendar
 */
function renderCalendar() {
    try {
        const calendarContainer = document.getElementById('calendar-dates');
        if (!calendarContainer) return;

        // Dispose old tooltips if bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(t => {
                try {
                    const instance = bootstrap.Tooltip.getInstance(t);
                    if (instance) instance.dispose();
                } catch(e) {}
            });
        }

        // Ensure activities is an array
        const activities = Array.isArray(calendarActivities) ? calendarActivities : [];

        calendarContainer.innerHTML = generateCalendar(currentYear, currentMonth, activities);
        updateCalendarMonth(currentYear, currentMonth);
        updateUpcomingSection();

        // Initialize new tooltips if bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            const newTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            newTooltips.forEach(t => {
                try {
                    new bootstrap.Tooltip(t);
                } catch(e) {}
            });
        }
    } catch (err) {
        console.error('Error rendering calendar:', err);
    }
}

/**
 * Initialize prev/next buttons
 */
function initializeNavigation() {
    document.getElementById('prev-month')?.addEventListener('click', async () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        calendarActivities = await fetchActivities(currentYear, currentMonth);
        renderCalendar();
    });

    document.getElementById('next-month')?.addEventListener('click', async () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        calendarActivities = await fetchActivities(currentYear, currentMonth);
        renderCalendar();
    });
}

/**
 * Update Upcoming Events section with real data from current month
 */
function updateUpcomingSection() {
    // Find upcoming body by looking for a card header that contains "Upcoming"
    let upcomingBody = null;
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        const header = card.querySelector('.card-header');
        if (header && header.textContent.includes('Upcoming')) {
            upcomingBody = card.querySelector('.card-body');
        }
    });

    if (!upcomingBody) return;

    // Filter future activities
    const today = new Date().toISOString().split('T')[0];
    const upcoming = calendarActivities
        .filter(act => act.tanggal >= today)
        .sort((a, b) => a.tanggal.localeCompare(b.tanggal))
        .slice(0, 3);

    if (upcoming.length === 0) {
        // Keep original PHP content or show empty state if no activities found
        // For now, let's only update if we have new data to avoid flashing
        return;
    }

    let html = '';
    upcoming.forEach(act => {
        const icon = act.jenis === 'Wawancara' ? 'bi-people' : (act.jenis === 'Presentasi' ? 'bi-display' : 'bi-calendar-event');
        const colorClass = act.jenis === 'Wawancara' ? 'bg-primary' : (act.jenis === 'Presentasi' ? 'bg-info' : 'bg-warning');
        
        html += `
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom last-child-no-border">
                <div class="rounded-circle ${colorClass} d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px; height:42px">
                    <i class="bi ${icon} text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <p class="mb-0 fw-semibold small">${act.judul}</p>
                    <small class="text-muted" style="font-size: 0.7rem;">
                        <i class="bi bi-calendar3 me-1"></i>${formatDate(act.tanggal)}
                    </small>
                </div>
            </div>`;
    });

    upcomingBody.innerHTML = html;
}

function formatDate(dateStr) {
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateStr).toLocaleDateString('en-GB', options);
}

/**
 * Animate progress indicators
 */
function animateProgress() {
    const stepperLine = document.querySelector('.stepper-line');
    if (stepperLine) {
        const targetWidth = stepperLine.style.width || '0%';
        stepperLine.style.width = '0';
        setTimeout(() => {
            stepperLine.style.transition = 'width 1s ease-in-out';
            stepperLine.style.width = targetWidth;
        }, 300);
    }
}

/**
 * Initialize everything
 */
function initDashboard() {
    initializeCalendar();
    animateProgress();
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    // Document already loaded, probably AJAX
    initDashboard();
} else {
    document.addEventListener('DOMContentLoaded', initDashboard);
}

// Helper for navigation
window.navigateTo = function(page) {
    const baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/home';
    window.location.href = baseUrl + '/' + page;
};
