import Calendar from '@toast-ui/calendar';
window.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('curriculum-calendar');

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            defaultView: 'week',
            week: {
                taskView: false,
                eventView: ['time'],
                startDayOfWeek: 1,
                hourStart: 6,
                hourEnd: 22,
                dayNames: ['អាទិត្យ','ច័ន្ទ','អង្គារ','ពុធ','ព្រហស្បតិ៍','សុក្រ','សៅរ៍'],

                currentTimeIndicator: false
            }

        });

        calendar.render();


        initCurriculumCalendar(calendar);

    } else {
        console.error('Calendar container not found!');
    }
});
