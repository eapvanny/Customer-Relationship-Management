@extends('backend.layouts.master')

@section('pageTitle')
    Dashboard
@endsection

@section('extraStyle')
    <style>
        .infinity {
            width: 120px;
            height: 60px;
            position: relative;

            div,
            span {
                position: absolute;
            }

            div {
                top: 0;
                left: 50%;
                width: 60px;
                height: 60px;
                animation: rotate 6.9s linear infinite;

                span {
                    left: -8px;
                    top: 50%;
                    margin: -8px 0 0 0;
                    width: 16px;
                    height: 16px;
                    display: block;
                    background: #8C6FF0;
                    box-shadow: 2px 2px 8px rgba(#8C6FF0, .09);
                    border-radius: 50%;
                    transform: rotate(90deg);
                    animation: move 6.9s linear infinite;

                    &:before,
                    &:after {
                        content: '';
                        position: absolute;
                        display: block;
                        border-radius: 50%;
                        width: 14px;
                        height: 14px;
                        background: inherit;
                        top: 50%;
                        left: 50%;
                        margin: -7px 0 0 -7px;
                        box-shadow: inherit;
                    }

                    &:before {
                        animation: drop1 .8s linear infinite;
                    }

                    &:after {
                        animation: drop2 .8s linear infinite .4s;
                    }
                }

                &:nth-child(2) {
                    animation-delay: -2.3s;

                    span {
                        animation-delay: -2.3s;
                    }
                }

                &:nth-child(3) {
                    animation-delay: -4.6s;

                    span {
                        animation-delay: -4.6s;
                    }
                }
            }
        }

        .infinityChrome {
            width: 128px;
            height: 60px;

            div {
                position: absolute;
                width: 17px;
                height: 17px;
                background: $color;
                box-shadow: 2px 2px 8px rgba($color, .09);
                border-radius: 50%;
                animation: moveSvg 6.9s linear infinite;
                -webkit-filter: url(#goo);
                filter: url(#goo);
                transform: scaleX(-1);
                offset-path: path("M64.3636364,29.4064278 C77.8909091,43.5203348 84.4363636,56 98.5454545,56 C112.654545,56 124,44.4117395 124,30.0006975 C124,15.5896556 112.654545,3.85282763 98.5454545,4.00139508 C84.4363636,4.14996252 79.2,14.6982509 66.4,29.4064278 C53.4545455,42.4803627 43.5636364,56 29.4545455,56 C15.3454545,56 4,44.4117395 4,30.0006975 C4,15.5896556 15.3454545,4.00139508 29.4545455,4.00139508 C43.5636364,4.00139508 53.1636364,17.8181672 64.3636364,29.4064278 Z");

                &:before,
                &:after {
                    content: '';
                    position: absolute;
                    display: block;
                    border-radius: 50%;
                    width: 14px;
                    height: 14px;
                    background: inherit;
                    top: 50%;
                    left: 50%;
                    margin: -7px 0 0 -7px;
                    box-shadow: inherit;
                }

                &:before {
                    animation: drop1 .8s linear infinite;
                }

                &:after {
                    animation: drop2 .8s linear infinite .4s;
                }

                &:nth-child(2) {
                    animation-delay: -2.3s;
                }

                &:nth-child(3) {
                    animation-delay: -4.6s;
                }
            }
        }

        @keyframes moveSvg {
            0% {
                offset-distance: 0%;
            }

            25% {
                background: #5628EE;
            }

            75% {
                background: #23C4F8;
            }

            100% {
                offset-distance: 100%;
            }
        }

        @keyframes rotate {
            50% {
                transform: rotate(360deg);
                margin-left: 0;
            }

            50.0001%,
            100% {
                margin-left: -60px;
            }
        }

        @keyframes move {

            0%,
            50% {
                left: -8px;
            }

            25% {
                background: #5628EE;
            }

            75% {
                background: #23C4F8;
            }

            50.0001%,
            100% {
                left: auto;
                right: -8px;
            }
        }

        @keyframes drop1 {
            100% {
                transform: translate(32px, 8px) scale(0);
            }
        }

        @keyframes drop2 {
            0% {
                transform: translate(0, 0) scale(.9);
            }

            100% {
                transform: translate(32px, -8px) scale(0);
            }
        }


        .infinity {
            display: none;
        }

        html {
            -webkit-font-smoothing: antialiased;
        }

        * {
            box-sizing: border-box;

            &:before,
            &:after {
                box-sizing: border-box;
            }
        }

        /* body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;

            .dribbble {
                position: fixed;
                display: block;
                right: 20px;
                bottom: 20px;

                img {
                    display: block;
                    height: 28px;
                }
            }
        } */

        .infinity-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .infinityChrome,
        .infinity {
            margin: auto;
        }

        .infinity-wrapper.fade-out {
            animation: fadeOut 1s ease-out forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                display: none;
            }
        }
        .calendar-move-today {
            background-color: transparent;
            color: black;
        }

        .calendar-move-today.active {
            background-color: #4299e1;
            color: white;
        }
    </style>
@endsection

@section('pageContent')
    <section>
        <div class="row">
            @if (session('show_popup'))
                <div class="infinity-wrapper">
                    <!-- Google Chrome -->
                    <div class="infinityChrome" style="display: none;">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>

                    <!-- Safari and others -->
                    <div class="infinity" style="display: none;">
                        <div>
                            <span></span>
                        </div>
                        <div>
                            <span></span>
                        </div>
                        <div>
                            <span></span>
                        </div>
                    </div>
                </div>

                <!-- Stuff -->
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="display: none;">
                    <defs>
                        <filter id="goo">
                            <feGaussianBlur in="SourceGraphic" stdDeviation="6" result="blur" />
                            <feColorMatrix in="blur" mode="matrix"
                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo" />
                            <feBlend in="SourceGraphic" in2="goo" />
                        </filter>
                    </defs>
                </svg>

                <!-- dribbble -->
                <a class="dribbble" href="https://dribbble.com/shots/5557955-Infinity-Loader" target="_blank">
                    <img src="https://cdn.dribbble.com/assets/dribbble-ball-mark-2bd45f09c2fb58dbbfb44766d5d1d07c5a12972d602ef8b32204d28fa3dda554.svg"
                        alt="">
                </a>
            @endif
            <div class="col-md-12">
                <div class="sub-head">
                    <div class="row w-100">
                        <div class="col-md-8">
                            <h4>{{__('Customer Relationship Management')}}</h4>
                        </div>
                        {{-- <div class="col-md-4 date-container">
                            <input class="select2" type="date">
                            <input class="select2" type="date">
                        </div> --}}
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card mt-3">
                    <div class="media  d-flex justify-content-between ">
                        <div class="media-body">
                            <p class="mb-0 text-black mb-2" style="font-weight: bold">{{ __('Calendar') }}</p>
                            <span id="menu-navi">
                                <button type="button" id="today" class="calendar-btn calendar-move-today active">
                                    {{ __('Today') }}
                                </button>
                                <button type="button" class="calendar-btn calendar-move-day">
                                    <i id="btn-left" class="calendar-icon ic-arrow-line-left"></i>
                                </button>
                                <button type="button" class="calendar-btn calendar-move-day">
                                    <i id="btn-right" class="calendar-icon ic-arrow-line-right"></i>
                                </button>
                            </span>
                            <span id="year-month" class="calendar-render-range"></span>
                        </div>
                    </div>
                    <div id="calendar" style="height: 380px;"></div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card mt-3 ticket-chat">
                    <div class="chart-container">
                        <span style="font-weight: bold">{{ __('All reports in this year') }}</span>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('extraScript')
    {{-- <script src="{{ asset('js/chart.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            @if (session('show_popup'))
                var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);

                // Show appropriate loader based on browser
                if (isChrome) {
                    document.getElementsByClassName('infinityChrome')[0].style.display = "block";
                    document.getElementsByClassName('infinity')[0].style.display = "none";
                } else {
                    document.getElementsByClassName('infinityChrome')[0].style.display = "none";
                    document.getElementsByClassName('infinity')[0].style.display = "block";
                }

                // Hide loader with fade out effect after 3 seconds
                setTimeout(function() {
                    // Add fade-out class to wrapper
                    $('.infinity-wrapper').addClass('fade-out');

                    // Remove the elements from display after animation completes
                    setTimeout(function() {
                        document.getElementsByClassName('infinityChrome')[0].style.display = "none";
                        document.getElementsByClassName('infinity')[0].style.display = "none";
                        $('.infinity-wrapper').removeClass('fade-out').css('display', 'none');
                    }, 1000); // Matches the animation duration (1s)
                }, 3000);
            @endif
            // Set current date for date inputs
            var currentDate = new Date().toISOString().split('T')[0];
            $('input[type="date"]').val(currentDate);
            var reportLabel = "{{ __('Reports') }}";
            // Data from Laravel passed as JSON
            var monthlyReporttData = @json($monthlyData);

            // Line chart data for reports opened per month
            var monthlyData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: reportLabel,
                    borderColor: '#4299e1',
                    backgroundColor: 'rgba(30, 143, 255, 0.46)',
                    data: monthlyReporttData,
                    fill: true,
                    tension: 0.4
                }]
            };

            var ctx = document.getElementById('lineChart').getContext('2d');
            var lineChart = new Chart(ctx, {
                type: 'line',
                data: monthlyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Ensure responsiveness
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                //text: 'Months
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                //text: 'Tickets Opened'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });


        });
        $(document).ready(function() {
            const calendar = new Calendar('#calendar', {
                defaultView: 'month',
                isReadOnly: true,
                template: {
                    time(event) {
                        const {
                            start,
                            end,
                            title
                        } = event;
                        return `<span><i class="fa-solid fa-flag"></i> ${start} ~ ${end} ${title}</span>`;
                    },
                    allday(event) {
                        return `<span><i class="fa-solid fa-flag"></i> ${event.title}</span>`;
                    },
                },
            });

            calendar.render();

            $('#btn-left').on('click', function() {
                calendar.prev();
                updateMonthYear(calendar);
            });

            $('#btn-right').on('click', function() {
                calendar.next();
                updateMonthYear(calendar);
            });

            $('#today').on('click', function() {
                calendar.today();
                const currentDate = new Date();
                updateMonthYear(calendar, currentDate);
            });

            updateMonthYear(calendar);
        });

        var monthNames = [
            "{{ __('January') }}", "{{ __('February') }}", "{{ __('March') }}", "{{ __('April') }}",
            "{{ __('May') }}", "{{ __('June') }}",
            "{{ __('July') }}", "{{ __('August') }}", "{{ __('September') }}", "{{ __('October') }}",
            "{{ __('November') }}", "{{ __('December') }}"
        ];

        function updateMonthYear(calendar) {
            const currentDate = calendar.getDate();
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const monthName = monthNames[month]; // Use translated month name

            $('#year-month').text(`${monthName} ${year}`);
        }

        $(document).ready(function() {
            let today = new Date();
            let currentDate = new Date();

            function updateTodayButton() {
                let formattedToday = today.toISOString().split("T")[0];
                let formattedCurrent = currentDate.toISOString().split("T")[0];

                if (formattedToday === formattedCurrent) {
                    $("#today").addClass("active");
                } else {
                    $("#today").removeClass("active");
                }
            }

            $("#today").on("click", function() {
                currentDate = new Date();
                updateTodayButton();
            });

            $("#btn-left").on("click", function() {
                currentDate.setDate(currentDate.getDate() - 1);
                updateTodayButton();
            });

            $("#btn-right").on("click", function() {
                currentDate.setDate(currentDate.getDate() + 1);
                updateTodayButton();
            });

            updateTodayButton();
        });
    </script>
@endsection
