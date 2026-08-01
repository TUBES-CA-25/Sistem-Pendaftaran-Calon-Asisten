/**
 * Dashboard User JavaScript
 * Handles calendar generation and other dashboard interactions
 */

var currentYear, currentMonth;
var calendarActivities = [];


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
            calendarActivities = result.data || [];
            
            // Insert rendered HTML from server
            const calendarContainer = document.getElementById('calendar-dates');
            if (calendarContainer && result.calendarHtml) {
                calendarContainer.innerHTML = result.calendarHtml;
            }
            
            const upcomingBody = document.getElementById('upcomingEventsList');
            if (upcomingBody && result.upcomingHtml) {
                upcomingBody.innerHTML = result.upcomingHtml;
            } else if (upcomingBody && result.upcomingHtml === '') {
                upcomingBody.innerHTML = `
                    <div class="text-center py-6">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-calendar-x text-xl text-slate-400"></i>
                        </div>
                        <p class="text-slate-500 text-sm">Tidak ada kegiatan di bulan ini</p>
                    </div>`;
            }
            
            // Tooltip kalender kini murni CSS (group-hover) — tidak perlu init JS.
            return calendarActivities;
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
    // Actually, always fetch so we get the pre-rendered HTML from the server
    await fetchActivities(currentYear, currentMonth);

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

        // Tooltip kalender murni CSS (group-hover) — tidak ada init/dispose JS.
        updateCalendarMonth(currentYear, currentMonth);
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


// showAllUpcoming() dihapus: Tidak ada tombol/markup yang memanggilnya.

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
