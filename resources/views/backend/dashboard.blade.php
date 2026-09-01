@extends('backend.layouts.master')

@section('pageTitle')
    Dashboard
@endsection

@section('extraStyle')
    <style>
        h6 {
            font-size: 19px;
        }

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

        .crm-title {
            font-size: 24px;
            font-weight: 800;
            color: #1f2937;
        }

        /* Filter wrapper */
        .crm-filter-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            width: 100%;
        }

        /* Each filter */
        .crm-filter-item {
            flex: 1;
            min-width: 130px;
        }

        .crm-filter-item label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
        }

        /* Inputs */
        .crm-control {
            height: 35px !important;
            min-height: 35px;
            border: 1px solid #d9dee7 !important;
            border-radius: 10px !important;
            background-color: #fff !important;
            font-size: 15px;
            color: #374151;
            padding: 0 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .crm-control:focus {
            border-color: #4f8dcc !important;
            box-shadow: 0 0 0 3px rgba(79, 141, 204, 0.12) !important;
        }

        /* Buttons container */
        .crm-filter-buttons {
            display: flex;
            gap: 8px;
        }

        /* Common button */
        .crm-btn {
            height: 35px;
            min-width: 80px;
            border: none;
            border-radius: 10px;
            padding: 0 16px;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            font-size: 14px;
            font-weight: 600;
            text-decoration: none !important;

            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* Filter */
        .crm-btn-filter {
            color: #fff;
            background: #4285c5;
            box-shadow: 0 3px 7px rgba(66, 133, 197, 0.25);
        }

        .crm-btn-filter:hover {
            color: #fff;
            background: #3476b6;
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(66, 133, 197, 0.30);
        }

        .crm-btn-filter i {
            font-size: 14px;
        }

        /* Clear */
        .crm-btn-clear {
            color: #374151;
            background: #f1f3f5;
            border: 1px solid #d9dee7;
        }

        .crm-btn-clear:hover {
            color: #dc3545;
            background: #fff1f2;
            border-color: #fecdd3;
            transform: translateY(-1px);
        }

        .crm-btn-clear i {
            font-size: 14px;
        }

        /* Mobile */
        @media (max-width: 1199px) {
            .crm-filter-wrapper {
                flex-wrap: wrap;
            }

            .crm-filter-item {
                flex: 1 1 calc(33.333% - 12px);
            }

            .crm-filter-buttons {
                flex: 1 1 100%;
            }

            .crm-btn {
                flex: 1;
            }
        }

        @media (max-width: 767px) {
            .crm-filter-wrapper {
                flex-direction: column;
                align-items: stretch;
            }

            .crm-filter-item {
                width: 100%;
            }

            .crm-filter-buttons {
                width: 100%;
            }

            .crm-btn {
                flex: 1;
            }

            .crm-title {
                font-size: 20px;
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

        .progress-circle {

            width: 180px;
            height: 180px;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

        }


        .circle-content {

            width: 140px;
            height: 140px;

            background: white;
            border-radius: 50%;

            display: flex;
            flex-direction: column;

            justify-content: center;
            align-items: center;

        }



        .target-item {

            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 14px 18px;

            background: #f8f9fa;

            border-radius: 12px;

            margin-bottom: 12px;

            font-size: 16px;

        }



        .rank-box {

            padding: 15px 18px;

            background: linear-gradient(135deg,
                    #fff7d6,
                    #ffffff);

            border-radius: 15px;

            border-left: 5px solid #ffc107;

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
                    <div class="row w-100 align-items-center">

                        {{-- Title --}}
                        <div class="col-12 col-xl-5 mb-3 mb-xl-0">
                            <h4 class="crm-title mb-0">
                                {{ __('Customer Relationship Management') }}
                            </h4>
                        </div>

                        {{-- Filters --}}
                        <div class="col-12 col-xl-7">
                            <form method="GET" action="{{ url()->current() }}">
                                <div class="crm-filter-wrapper">

                                    {{-- Year --}}
                                    <div class="crm-filter-item">
                                        <label for="year">
                                            {{ __('Year') }}
                                        </label>

                                        <select
                                            name="year"
                                            id="year"
                                            class="form-control select2 crm-control"
                                        >
                                            <option value="all" {{ request('year', now()->year) === 'all' ? 'selected' : '' }}>
                                                {{ __('All') }}
                                            </option>

                                            @for ($year = 2024; $year <= now()->year; $year++)
                                                <option
                                                    value="{{ $year }}"
                                                    {{ (string) request('year', now()->year) === (string) $year ? 'selected' : '' }}
                                                >
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="crm-filter-item">
                                        <label for="from_date">
                                            {{ __('From Date') }}
                                        </label>

                                        <input
                                            type="date"
                                            name="from_date"
                                            id="from_date"
                                            class="form-control crm-control"
                                            value="{{ request()->input('from_date') }}"
                                        >
                                    </div>

                                    <div class="crm-filter-item">
                                        <label for="to_date">
                                            {{ __('To Date') }}
                                        </label>

                                        <input
                                            type="date"
                                            name="to_date"
                                            id="to_date"
                                            class="form-control crm-control"
                                            value="{{ request()->input('to_date') }}"
                                        >
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="crm-filter-buttons">

                                        <button type="submit" class="crm-btn crm-btn-filter">
                                            <i class="fas fa-filter"></i>
                                            <span>{{ __('Filter') }}</span>
                                        </button>

                                        <a
                                            href="{{ url()->current() }}"
                                            class="crm-btn crm-btn-clear"
                                        >
                                            <i class="fas fa-times"></i>
                                            <span>{{ __('Cancel') }}</span>
                                        </a>

                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6">
                <div class="card" style="color: rgb(60, 60, 60); font-size: xx-large; border-bottom: 3px solid #39a1ea;">
                    <h6 style="font-weight: 900;">{{ __('Total SO') }}</h6>
                    <h3 id="all-report" style="font-weight: 900;">{{ $allReports }}</h3>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6">
                <div class="card" style="color: rgb(60, 60, 60); font-size: xx-large; border-bottom: 3px solid #2aaa91;">
                    <h6 style="font-weight: 900;">{{ __('Total Case') }}</h6>
                    <h3 id="today-report" style="font-weight: 900;">{{ $totalCase }}</h3>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6">
                <div class="card" style="color: rgb(60, 60, 60); font-size: xx-large; border-bottom: 3px solid #95d1d4;">
                    <h6 style="font-weight: 900;">{{ __('Today Case') }}</h6>
                            <h3 id="all-customer" style="font-weight: 900;">{{ $todayCase }}</h3>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6">
                <div class="card" style="color: rgb(60, 60, 60); font-size: xx-large; border-bottom: 3px solid #95d1d4;">
                    <h6 style="font-weight: 900;">{{ __('Total Customer') }}</h6>
                    <h3 id="all-customer" style="font-weight: 900;">{{ $allCustomers }}</h3>
                </div>
            </div>
            {{-- <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6">
                <div class="card" style="color: rgb(60, 60, 60); font-size: xx-large; border-bottom: 3px solid #feb559;">
                    <h6 style="font-weight: 900;">{{ __('All User') }}</h6>
                    <h3 id="all-user" style="font-weight: 900;">{{ $allUsers }}</h3>
                </div>
            </div> --}}
            {{-- <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6">
                <div class="card" style="color: rgb(60, 60, 60); font-size: xx-large; border-bottom: 3px solid #39a1ea;">
                    <h6 style="font-weight: 900;">{{ __('User Active') }}</h6>
                    <h3 id="all-report" style="font-weight: 900;">{{ $userActive }}</h3>
                </div>
            </div> --}}
        </div>
    </section>
    <section>
        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">
                    <i class="fa fa-bullseye text-warning"></i>
                    {{ __('Monthly Sales Target') }}
                </h4>
                <form method="GET" action="{{ url()->current() }}">
                    <div class="form-group has-feedback"
                        style="width: 700px; margin-right: 10px;">

                        <select name="employee_id"
                            id="employee_id"
                            class="form-control select2"
                            onchange="this.form.submit()">

                        <option value="">
                            {{ __('Select Employee') }}
                        </option>

                        @foreach($allUsersEmployee as $employee)

                            <option value="{{ $employee->id }}"
                                {{ (int) $selectedEmployeeId === (int) $employee->id ? 'selected' : '' }}>

                                .{{ $employee->display_name }}

                                @if($employee->rsm_name)
                                    | RSM: {{ $employee->rsm_name }}
                                @endif

                                @if($employee->asm_name)
                                    | ASM: {{ $employee->asm_name }}
                                @endif

                                @if($employee->sup_name)
                                    | SUP: {{ $employee->sup_name }}
                                @endif

                            </option>

                        @endforeach

                    </select>
                    </div>
                </form>
                @if ($currentRank == 'Rank A' && $targetPercent >= 100)
                    <span class="badge bg-success px-3 py-2">
                        <i class="fa fa-trophy"></i> {{__('Completed')}}
                    </span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="fa fa-spinner"></i> {{__('In Progress')}}
                    </span>
                @endif
            </div>


            <div class="card-body">

                <div class="row align-items-center">


                    {{-- Circle Progress --}}
                    <div class="col-md-4 text-center">

                        <div class="progress-circle mx-auto"
                            style="
                            background: conic-gradient(
                                {{ $progressColor }} {{ $targetPercent }}%,
                                #eeeeee {{ $targetPercent }}%
                            );">

                            <div class="circle-content">
                                <h2 class="fw-bold mb-0">
                                    {{ $targetPercent }}%
                                </h2>
                                <small class="text-muted">
                                    {{__('Completed')}}
                                </small>
                            </div>

                        </div>

                    </div>



                    {{-- Detail --}}
                    <div class="col-md-8">


                        <div class="rank-box mb-3">

                            <div>
                                <span class="text-muted">
                                    <i class="fa fa-trophy"></i> {{__('Current Rank')}}
                                </span>

                            </div>

                            <h3 class="fw-bold mt-2" style="color: {{ $progressColor }}">
                                {{ __($currentRank) }}
                            </h3>

                        </div>



                        <div class="target-item">

                            <span>
                                <i class="fa fa-shopping-cart"></i> {{__('Actual Sales')}}
                            </span>

                            <b>
                                {{ number_format($soldThisMonth) }} {{__('Cases')}}
                            </b>

                        </div>

                        <div class="target-item">

                            <span>
                                <i class="fa fa-hourglass-half"></i> {{__('Remaining')}}
                            </span>

                            <b class="text-danger">

                                @if ($remaining > 0)
                                    {{ number_format($remaining) }} {{__('Cases')}}
                                @else
                                    {{__('Completed')}} <i class="fa fa-check-circle text-success"></i>
                                @endif

                            </b>

                        </div>

                        <div class="target-item">

                            <span>
                                <i class="fa fa-trophy"></i> {{__('Next Rank')}}
                            </span>

                            <b>
                                {{ $nextRank ?? __('Completed All Rank') }}
                            </b>

                        </div>


                    </div>


                </div>



                {{-- Progress Bar --}}
                <div class="mt-4">

                    <div class="d-flex justify-content-between mb-2">

                        <small>
                            Progress to {{ $nextRank ?? 'Rank C' }}
                        </small>

                        <small class="fw-bold">
                            {{ $targetPercent }}%
                        </small>

                    </div>


                    <div class="progress rounded-pill" style="height:12px;">

                        <div class="progress-bar"
                            style="
                        width: {{ $targetPercent }}%;
                        background-color: {{ $progressColor }};
                    ">
                        </div>

                    </div>

                </div>


            </div>

        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                {{-- <div class="card mt-3">
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
                </div> --}}
                <div class="card mt-3 ticket-chat">
                    <div class="chart-container">
                        <span style="font-weight: bold">{{ __('All cases in this year') }}</span>
                        <canvas id="lineChartCase"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card mt-3 ticket-chat">
                    <div class="chart-container">
                        <span style="font-weight: bold">{{ __('All SO in this year') }}</span>
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
            // $('input[type="date"]').val(currentDate);
            var reportLabel = "{{ __('Reports') }}";
            // Data from Laravel passed as JSON
            var monthlyReporttData = @json($monthlyData);

            // Line chart data for reports opened per month
            var monthlyData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: reportLabel,
                    borderColor: '#F59E0B  ',
                    backgroundColor: 'rgba(245, 158, 11, 0.46)',
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

            var caseLabel = "{{ __('Cases') }}";
            // Data from Laravel passed as JSON
            var monthlyCaseData = @json($monthlyCaseData);

            // Line chart data for cases opened per month
            var monthlyData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: caseLabel,
                    borderColor: '#4299e1',
                    backgroundColor: 'rgba(30, 143, 255, 0.46)',
                    data: monthlyCaseData,
                    fill: true,
                    tension: 0.4
                }]
            };

            var ctx = document.getElementById('lineChartCase').getContext('2d');
            var lineChartCase = new Chart(ctx, {
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
            @if (!request()->has('from_date'))
                $('#from_date').val('');
            @endif

            @if (!request()->has('to_date'))
                $('#to_date').val('');
            @endif
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
