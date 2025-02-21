import Chart from 'chart.js';
import ApexCharts from 'apexcharts'
import Calendar from '@toast-ui/calendar';
// require('fullcalendar');

class Dashboard {
    static init() {

        //calenda ui

        initStudentEnrollmentChart(studentCharts);



        // //donut chart total income
        // var options = {
        //   chart: {
        //       type: 'donut',
        //       width: 130,
        //       height: 130,
        //       dropShadow:{enabled: false},
        //   },
        //   tooltip:{
        //     enabled:false,
        //     followCursor: false,
        //   },
        //   series:[45,55],
        //   colors: ["#1e33f2", "#f5f5f5"],
        //   dataLabels: {
        //       enabled: false,
        //   },
        //   legend:{
        //     show: false,
        //   },
        // }

        // var chart = new ApexCharts(document.querySelector("#chartIncome"), options);

        // chart.render();

        // //donut chart total expanse
        // var options = {
        //   chart: {
        //       type: 'donut',
        //       width: 130,
        //       height: 130,
        //       dropShadow:{enabled: false},
        //   },
        //   tooltip:{
        //     enabled:false,
        //     followCursor: false,
        //   },
        //   series:[45,55],
        //   colors: ["#ff5045", "#f5f5f5"],
        //   dataLabels: {
        //       enabled: false,
        //   },
        //   legend:{
        //     show: false,
        //   },
        // }

        // var chart = new ApexCharts(document.querySelector("#chartExpense"), options);

        // chart.render();

        // //line chart
        // var options = {
        //     chart: {
        //         type: 'line'
        //     },
        //     series: [{
        //         name: 'sales',
        //         data: [30,40,35,50,49,60,70,91,125]
        //     }],
        //     xaxis: {
        //         categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
        //     }
        // }

        // var chart = new ApexCharts(document.querySelector("#chartUpgrade"), options);

        // chart.render();

        // version 1
        //sms chart
        var chartElement = document.getElementById('smsChart');
        if(chartElement) {
            var config = {
                type: 'bar',
                data: {
                    labels: window.smsLabel,
                    datasets: [{
                        label: "SMS",
                        backgroundColor: "#82E0AA",
                        borderColor: "#58D68D",
                        pointBorderColor: "#28B463",
                        pointBackgroundColor: "#2ECC71",
                        pointHoverBackgroundColor: "#82E0AA",
                        pointHoverBorderColor: "#c",
                        pointBorderWidth: 1,
                        data: window.smsValue
                    }]
                },
                options: {
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                    },
                    hover: {
                        mode: 'index'
                    },
                    legend: {
                        display: false
                    }

                }
            };
            var ctx = chartElement.getContext('2d');
            new Chart(ctx, config);
        }
        var chartElement = document.getElementById('accountChart');
        if(chartElement) {
            var config = {
                type: 'line',
                data: {
                    labels: ["Jan,2019", "Feb,2019", "March,2019", "Apr,2019", "May,2019", "Jun,2019", "Jul,2019", "Augt,2019", "Sep,2019", "Oct,2019", "Nov,2019", "Dec,2019"],
                    datasets: [{
                        label: "Income",
                        backgroundColor: "#82E0AA",
                        borderColor: "#58D68D",
                        pointBorderColor: "#28B463",
                        pointBackgroundColor: "#2ECC71",
                        pointHoverBackgroundColor: "#82E0AA",
                        pointHoverBorderColor: "#58D68D",
                        pointBorderWidth: 1,
                        data: [
                            52662545.31,
                            32271914.00,
                            20651857.91,
                            31068496.90,
                            26380827.16,
                            405006.00,
                            0.00,
                            0.00,
                            0.00,
                            0.00,
                            0.00,
                            0.00
                        ]
                    }, {
                        label: "Expence",
                        backgroundColor: "#F1948A",
                        borderColor: "#EC7063",
                        pointBorderColor: "#CB4335",
                        pointBackgroundColor: "#E74C3C",
                        pointHoverBackgroundColor: "#F1948A",
                        pointHoverBorderColor: "#EC7063",
                        pointBorderWidth: 1,
                        data: [
                            57301010.42,
                            32220143.00,
                            20669087.91,
                            27231585.68,
                            25534450.16,
                            20450.00,
                            0.00,
                            0.00,
                            0.00,
                            0.00,
                            0.00,
                            0.00
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                    },
                    hover: {
                        mode: 'index'
                    }

                }
            };
            var ctx = chartElement.getContext('2d');
           new Chart(ctx, config);
        }

        var chartElement = document.getElementById('attendanceChart');
        if(chartElement) {
            var config = {
                type: 'line',
                data: {
                    labels:  window.attendanceLabel,
                    datasets: [{
                        label: 'Present',
                        data: window.presentData,
                        backgroundColor: "rgb(54, 162, 235)",
                        borderColor: "rgb(54, 162, 235)",
                        fill: false,
                        pointRadius: 6,
                        pointHoverRadius: 20,
                    }, {
                        label: 'Absent',
                        data: window.absentData,
                        backgroundColor: "rgb(255, 99, 132)",
                        borderColor: "rgb(255, 99, 132)",
                        fill: false,
                        pointRadius: 6,
                        pointHoverRadius: 20,

                    }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    hover: {
                        mode: 'index'
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Class'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Attendace'
                            }
                        }]
                    },
                    title: {
                        display: false,
                        text: 'Students Today\'s Attendance'
                    }
                }
            };
            var ctx = chartElement.getContext('2d');
            new Chart(ctx, config);
        }


        // $('#calendar').fullCalendar({
        //     defaultView: 'month',
        //     height: 300,
        //     contentHeight: 250
        // });

    }

    static initStudentEnrollmentChart(studentCharts){
        var options = {
            title: {
                text: 'Students',
                align: 'left'
            },
            series: [
                {
                    name: 'Female',
                    data: Object.values(studentCharts.series[0].data)
                },
                {
                    name: 'Male',
                    data: Object.values(studentCharts.series[1].data),
                }
            ],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },

            xaxis: {
                type: 'category',
                categories: studentCharts.categories,
                title:{
                    text: 'Academic Year',
                    align: 'end'
                }
            },
            yaxis: {
                type: 'number',
            },
            tooltip: {
                x: {
                format: 'dd/M/yy HH:mm'
                },
            },
        };
        var chart = new ApexCharts(document.querySelector("#chartStudent"), options);
        chart.render();
    }

    static initAcademicCalendar(){
        const calendar = new Calendar('#calendar', {
            defaultView: 'month',
            isReadOnly: true,
            template: {
                time(event) {
                    const { start, end, title } = event;
                    return `<span><i class="fa-solid fa-flag"></i> ${start}~${end} ${title}</span>`;
                },
                allday(event) {
                    return `<span><i class="fa-solid fa-flag"></i> ${event.title}</span>`;
                },
            },
            calendars: [
                {
                    id: 'school_event',
                    name: 'School Event',
                    backgroundColor: '#33bcff',
                    color: '#ffffff',
                },
                {
                    id: 'holiday',
                    name: 'Holiday',
                    backgroundColor: '#FF0000',
                    color: '#ffffff',
                },
                {
                    id: 'examination',
                    name: 'Examination',
                    backgroundColor: '#FFA500',
                    color: '#ffffff',
                },
            ],
        });

        calendar.render();
        this.loadAcademicCalendar(function(response_data){
            var event_arr = [];
            $.each(response_data, function(i, value){
                event_arr.push({
                    id: 'event_' + value.id,
                    calendarId: value.event_type,
                    title: value.title,
                    body: value.description,
                    isAllday: true,
                    start: value.date_from,
                    end: value.date_upto,
                    category: 'allday'
                });
            });
            calendar.createEvents(event_arr);
        });
        // calendar.createEvents([
        //     {
        //         id: 'event1',
        //         calendarId: 'cal1',
        //         title: 'Weekly Meeting',
        //         start: '2024-03-19 09:00:00',
        //         end: '2024-03-25 10:00:00',
        //     },
        // ]);
        // calendar.createEvents([
        //     {
        //         id: 'event2',
        //         calendarId: 'cal2',
        //         title: 'Weekly Meeting2',
        //         start: '2024-03-21 09:00:00',
        //         end: '2024-03-27 10:00:00',
        //     },
        // ]);
        var self = this;
        $('#btn-left').on('click', function () {
            calendar.prev();
            self.updateMonthYear(calendar);
        });
        $('#btn-right').on('click', function () {
            calendar.next();
            self.updateMonthYear(calendar);
        });

        $('#today').on('click', function () {
            calendar.today();
            const currentDate = new Date();
            self.updateMonthYear(calendar, currentDate);
        });
        self.updateMonthYear(calendar);
    }

    static loadAcademicCalendar(callback){
        $.ajax({
            type: "GET",
            url: "/ajax/academic_calendar/search",
            data: {},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    if (callback !== undefined){
                        callback(response.data);
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }


    static updateMonthYear(calendar) {
        const currentDate = calendar.getDate();
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const monthName = monthNames[month];
        // const monthString = month <= 9 ? '0' + month : month; // Ensure two digits for month
        $('#year-month').text(`${monthName} ${year}`);
    }



}

window.Dashboard = Dashboard;
