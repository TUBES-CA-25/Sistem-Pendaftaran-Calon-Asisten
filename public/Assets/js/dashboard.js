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
    let upcomingBody = document.getElementById('upcomingEventsList');
    if (!upcomingBody) return;

    const today = new Date().toISOString().split('T')[0];
    const upcoming = window.initialActivities || [];
    const upcomingFuture = upcoming
        .filter(act => act.tanggal >= today)
        .sort((a, b) => a.tanggal.localeCompare(b.tanggal))
        .slice(0, 1);

    if (upcomingFuture.length === 0) return;

    let html = '';
    upcomingFuture.forEach(act => {
        const icon = act.jenis === 'Wawancara' ? 'bi-people' : (act.jenis === 'Presentasi' ? 'bi-display' : 'bi-calendar-event');
        const colorClass = act.jenis === 'Wawancara' ? 'bg-blue-50 text-blue-600' : (act.jenis === 'Presentasi' ? 'bg-cyan-50 text-cyan-600' : 'bg-amber-50 text-amber-600');
        
        html += `
            <div class="flex gap-3">
                <div class="w-10 h-10 rounded-full ${colorClass} flex items-center justify-center shrink-0">
                    <i class="bi ${icon} text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm mb-0.5">${act.judul}</p>
                    <span class="text-slate-400 text-xs block mb-0.5">
                        <i class="bi bi-calendar3 me-1"></i>${formatDate(act.tanggal)}
                    </span>
                    <span class="text-slate-400 text-xs block">
                        <i class="bi bi-folder me-1"></i>${act.jenis}
                    </span>
                </div>
            </div>`;
    });

    upcomingBody.innerHTML = html;
}

/**
 * Show all upcoming activities in a modal
 */
window.showAllUpcoming = function() {
    const today = new Date().toISOString().split('T')[0];
    const upcoming = window.initialActivities || [];
    const upcomingFuture = upcoming
        .filter(act => act.tanggal >= today)
        .sort((a, b) => a.tanggal.localeCompare(b.tanggal));

    let html = '';
    if (upcomingFuture.length === 0) {
        html = `
            <div class="text-center py-8 text-slate-400">
                <i class="bi bi-calendar-x text-5xl mb-3 block opacity-60"></i>
                <p class="text-sm">Tidak ada jadwal</p>
            </div>`;
    } else {
        upcomingFuture.forEach(act => {
            const icon = act.jenis === 'Wawancara' ? 'bi-people' : (act.jenis === 'Presentasi' ? 'bi-display' : 'bi-calendar-event');
            const colorClass = act.jenis === 'Wawancara' ? 'bg-blue-50 text-blue-600' : (act.jenis === 'Presentasi' ? 'bg-cyan-50 text-cyan-600' : 'bg-amber-50 text-amber-600');
            
            html += `
                <div class="flex gap-3 mb-4 pb-4 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                    <div class="w-12 h-12 rounded-full ${colorClass} flex items-center justify-center shrink-0">
                        <i class="bi ${icon} text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm mb-1">${act.judul}</p>
                        <div class="flex gap-3 text-xs text-slate-500">
                            <span><i class="bi bi-calendar3 me-1"></i>${formatDate(act.tanggal)}</span>
                            <span><i class="bi bi-folder me-1"></i>${act.jenis}</span>
                        </div>
                    </div>
                </div>`;
        });
    }

    document.getElementById('upcomingActivitiesBody').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('upcomingActivitiesModal'));
    modal.show();
};

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
    const baseUrl = '/Sistem-Pendaftaran-Calon-Asisten/public';
    window.location.href = baseUrl + '/' + page;
};
