<!-- Master page -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    Reports
@endsection
<!-- End block -->

@section('extraStyle')
    <!-- Same styles as provided, no changes needed -->
    <style>
        .chat-container {
            flex-grow: 1;
            overflow-y: auto;
        }

        .chat-input-container {
            position: sticky;
            bottom: 0;
            width: 100%;
            background: white;
            padding: 10px;
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

        /* .table-responsive {
                                display: block;
                                width: 100%;
                                overflow-x: auto;
                                -webkit-overflow-scrolling: touch;
                            }

                            .table {
                                width: 100%;
                                table-layout: fixed;
                            }

                            .table td,
                            .table th {
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                            }

                            .table th,
                            .table td {
                                min-width: 0;
                                max-width: none;
                            } */
        .list-group-unbordered .list-group-item {
            padding: 10px 0;
            font-size: 0.95rem;
        }

        .list-group-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .col-lg-8 {
            flex: 1;
        }
    </style>
@endsection

<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->
@php
    use App\Http\Helpers\AppHelper;
@endphp
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li class="active"> {{ __('Reports') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
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
                        <div><span></span></div>
                        <div><span></span></div>
                        <div><span></span></div>
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
                <div class="wrap-outter-header-title">
                    <h1>{{ __('Reports List') }}</h1>
                    <div class="box-tools pull-right">
                        <button id="filters" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#filterContainer">
                            <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                        </button>

                        @if($showModal)
                            <button type="button" class="btn btn-info text-white" id="openModalBtn">
                                <i class="fa fa-plus-circle idPopup"></i> {{ __('Add New') }}
                            </button>
                        @else
                            <a class="btn btn-info text-white" href="{{ URL::route('report.create') }}">
                                <i class="fa fa-plus-circle idPopup"></i> {{ __('Add New') }}
                            </a>
                        @endif


                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <form action="{{ route('report.index') }}" method="GET" id="filterForm">
                                        <div class="wrap_filter_form @if (!$is_filter) collapse @endif"
                                            id="filterContainer">
                                            <a id="close_filter" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <div class="form-group">
                                                        <label for="date1">{{ __('From Date') }}</label>
                                                        <input type="date" name="date1" id="date1"
                                                            class="form-control" value="{{ request('date1') }}">
                                                    </div>
                                                </div>
                                                <div class="col-xl-3">
                                                    <div class="form-group">
                                                        <label for="date2">{{ __('To Date') }}</label>
                                                        <input type="date" name="date2" id="date2"
                                                            class="form-control" value="{{ request('date2') }}">
                                                    </div>
                                                </div>
                                                {{-- @if (in_array(auth()->user()->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]))
                                                    <div class="col-xl-3">
                                                        <div class="form-group">
                                                            <label for="full_name">{{ __('Employee Name') }}</label>
                                                            {!! Form::select('full_name', $full_name, request('full_name'), [
                                                                'placeholder' => __('Select employee'),
                                                                'id' => 'full_name',
                                                                'class' => 'form-control select2',
                                                            ]) !!}
                                                        </div>
                                                    </div>
                                                @endif --}}
                                                <div class="col-xl-3">
                                                    <div class="form-group">
                                                        <label for="user_id">{{ __('Employee') }}</label>
                                                        {!! Form::select('user_id', $employees, request('user_id'), [
                                                            'placeholder' => __('Select employee'),
                                                            'id' => 'user_id',
                                                            'class' => 'form-control select2',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-xl-3">
                                                    <div class="form-group">
                                                        <label for="area_id">{{ __('Area') }}</label>
                                                        {!! Form::select('area_id', $area_id, request('area_id'), [
                                                            'placeholder' => __('Select area'),
                                                            'id' => 'area_id',
                                                            'class' => 'form-control select2',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mt-2">
                                                    <button id="apply_filter"
                                                        class="btn btn-outline-secondary btn-sm float-end" type="submit">
                                                        <i class="fa-solid fa-magnifying-glass"></i> {{ __('Apply') }}
                                                    </button>
                                                    <a href="{{ route('report.index') }}"
                                                        class="btn btn-outline-secondary btn-sm float-end me-1">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                           <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-success btn-sm" 
                                    href="{{ route('report.export') . '?' . http_build_query(request()->only(['date1', 'date2', 'area_id'])) }}">
                                        <i class="fa-solid fa-download"></i> {{ __('Export') }}
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <table id="datatable"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Area') }}</th>
                                            <th>{{ __("Depo's Name") }}</th>
                                            <th>{{ __('Customer Name') }}</th>
                                            <th>{{ __('Customer Code') }}</th>
                                            <th>{{ __('250ml') }}</th>
                                            <th>{{ __('350ml') }}</th>
                                            <th>{{ __('600ml') }}</th>
                                            <th>{{ __('1500ml') }}</th>
                                            <th>{{ __('Default') }}</th>
                                            <th class="notexport" style="max-width: 82px">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="btn-group">
                            <form id="myAction" method="POST">
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal photo -->
    <div class="modal modal-xl fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">{{ __('Report Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="btnClose"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body img-popup">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                            <div class="report-details">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-lg-12 col-xl-6">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item"><i class="fa fa-user"></i>
                                                <strong>{{ __('Employee Name') }}:</strong> <span
                                                    id="modalEmployeeName"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-id-card"></i>
                                                <strong>{{ __('Staff ID') }}:</strong> <span id="modalIdCard"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-chart-area"></i>
                                                <strong>{{ __('Area') }}:</strong> <span id="modalArea"></span></li>
                                            <li class="list-group-item"><i class="fa fa-user"></i>
                                                <strong>{{ __('Customer Name') }}:</strong> <span
                                                    id="modalCustomerName"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-home"></i>
                                                <strong>{{ __('Outlet') }}:</strong> <span id="modalOutlet"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-calendar-days"></i>
                                                <strong>{{ __('Date') }}:</strong> <span id="modalDate"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-location-dot"></i>
                                                <strong>{{ __('Address') }}:</strong> <span id="modalCity"></span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i>
                                                <strong>{{ __('250ml') }}:</strong> <span id="modal250ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i>
                                                <strong>{{ __('350ml') }}:</strong> <span id="modal350ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i>
                                                <strong>{{ __('600ml') }}:</strong> <span id="modal600ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i>
                                                <strong>{{ __('1500ml') }}:</strong> <span id="modal1500ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i>
                                                <strong>{{ __('Other') }}:</strong> <span id="modalOther"></span></li>
                                            <li class="list-group-item"><i class="fa-brands fa-square-letterboxd"></i>
                                                <strong>{{ __('POSM1') }}:</strong> <span id="modalPosm"></span>, &nbsp;
                                                <strong>{{ __('POSM2') }}:</strong> <span id="modalPosm2"></span>, &nbsp;
                                                <strong>{{ __('POSM3') }}:</strong> <span id="modalPosm3"></span>
                                            </li>
                                            <li class="list-group-item"><i class="fa-brands fa-elementor"></i>
                                                <strong>{{ __('Quantity1') }}:</strong> <span id="modalQty"></span>, &nbsp;
                                                <strong>{{ __('Quantity2') }}:</strong> <span id="modalQty2"></span>, &nbsp;
                                                <strong>{{ __('Quantity3') }}:</strong> <span id="modalQty3"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-6 mt-2">
                            <img style="border: 1px solid #cfcfcf; width: auto; height: 350px; object-fit: cover;" id="modalPhotoOutlet" src=""
                                class="img-fluid photo-detail" alt="Photo_outlet Detail">
                            <div class="text-center mt-3">
                                <b>{{ __('Outlet Photo') }}</b>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-6 mt-2">
                            <img style="border: 1px solid #cfcfcf;  width: auto; height: 350px; object-fit: cover;" id="modalPhoto" src=""
                                class="img-fluid photo-detail" alt="Photo Detail">
                            <div class="text-center mt-3">
                                <b>{{ __('POSM Photo') }}</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btnClose"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal form select id pru-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static"
     data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="fas fa-id-card me-2"></i> {{ __('Driver info') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="redirectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Do you have a Driver ?') }}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="hasDriver" id="radioYes" value="yes">
                                <label class="form-check-label" for="radioYes">{{ __('Yes') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="hasDriver" id="radioNo" value="no">
                                <label class="form-check-label" for="radioNo">{{ __('No') }}</label>
                            </div>
                        </div>
                         <div id="radioError" class="text-danger mt-1" style="display: none;">
                            {{ __('Please select Yes or No.') }}
                        </div>
                    </div>
                     <!-- Driver ID input -->
                    <div class="mb-3" id="driverIdGroup" style="display: none;">
                        <label for="driverId" class="form-label fw-semibold">
                            {{ __('Driver ID') }} <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="driverId" name="driverId" autocomplete="off">
                        <div class="invalid-feedback">
                            {{ __('Driver ID is required.') }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-arrow-right"></i> {{ __('Continue') }}</button>
                    {{-- <button type="button" class="btn btn-secondary btnClose"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button> --}}
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            @if (session('show_popup'))
                var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
                if (isChrome) {
                    $('.infinityChrome').show();
                    $('.infinity').hide();
                } else {
                    $('.infinityChrome').hide();
                    $('.infinity').show();
                }
                setTimeout(function() {
                    $('.infinity-wrapper').addClass('fade-out');
                    setTimeout(function() {
                        $('.infinityChrome').hide();
                        $('.infinity').hide();
                        $('.infinity-wrapper').removeClass('fade-out').hide();
                    }, 1000);
                }, 3000);
            @endif

            var table = $('#datatable').DataTable({
                processing: false,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('report.index') }}",
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.search_value = d.search.value;
                        d.date1 = "{{ request('date1') }}";
                        d.date2 = "{{ request('date2') }}";
                        d.area_id = "{{ request('area_id') }}";
                        d.user_id = "{{ request('user_id') }}";
                    },
                    error: function(xhr, error, thrown) {
                        console.log('AJAX Error:', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'area', name: 'area' },
                    { data: 'outlet_id', name: 'outlet_id' },
                    { data: 'customer', name: 'customer' },
                    { data: 'customer_code', name: 'customer_code' },
                    { data: '250ml', name: '250ml' },
                    { data: '350ml', name: '350ml' },
                    { data: '600ml', name: '600ml' },
                    { data: '1500ml', name: '1500ml' },
                    { data: 'default', name: 'default' },
                    { 
                        data: 'action', 
                        name: 'action',
                        orderable: false,
                        searchable: false 
                    }
                ]
            });

            // Trigger table redraw when search input changes
            $('#datatable_filter input').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Handle filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                window.location = "{{ route('report.index') }}?" + $(this).serialize();
            });

            // Close filter panel
            $('#close_filter').click(function() {
                $("#filters").trigger('click');
            });

            // Handle view details
            $(document).on('click', '.img-detail', function() {
                var reportId = $(this).data('id');
                $.ajax({
                    url: '/report/' + reportId,
                    method: 'GET',
                    success: function(response) {
                        var report = response.report;
                        $('#modalPhoto').attr('src', report.photo);
                        $('#modalPhotoOutlet').attr('src', report.outlet_photo);
                        $('#modalEmployeeName').text(report.employee_name);
                        $('#modalIdCard').text(report.staff_id_card);
                        $('#modalArea').text(report.area);
                        $('#modalCustomerName').text(report.customer);
                        $('#modalOutlet').text(report.outlet);
                        $('#modalDate').text(report.date);
                        $('#modalOther').text(report.other);
                        $('#modal250ml').text(report['250_ml']);
                        $('#modal350ml').text(report['350_ml']);
                        $('#modal600ml').text(report['600_ml']);
                        $('#modal1500ml').text(report['1500_ml']);
                        $('#modalCity').text(report.city);
                        $('#modalPosm').text(report.posm);
                        $('#modalQty').text(report.qty);
                        $('#modalPosm2').text(report.posm2);
                        $('#modalQty2').text(report.qty2);
                        $('#modalPosm3').text(report.posm3);
                        $('#modalQty3').text(report.qty3);
                        $('#viewModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        alert('Failed to load report details.');
                    }
                });
            });

            // Handle modal close
            $(document).on('click', '.btnClose', function() {
                $('#viewModal').modal('hide');
                $('.img-popup').val('');
            });

            $('#openModalBtn').on('click', function () {
                $('#confirmModal').modal('show');
            });


            // Show/hide Customer ID input and clear radio error on selection
            // Show/hide Driver ID input and clear radio error on selection
            $('input[name="hasDriver"]').on('change', function () {
                $('#radioError').hide();

                if ($(this).val() === 'yes') {
                    $('#driverIdGroup').show();
                    $('#driverId').prop('required', true);
                } else {
                    $('#driverIdGroup').hide();
                    $('#driverId').prop('required', false).val('');
                    $('#driverId').removeClass('is-invalid');
                }
            });

            // Handle form submission
            $('#redirectForm').on('submit', function (e) {
                e.preventDefault();

                const hasDriver = $('input[name="hasDriver"]:checked').val();
                const driverId = $('#driverId').val().trim();

                let isValid = true;

                // Validate radio selection
                if (!hasDriver) {
                    $('#radioError').show();
                    isValid = false;
                } else {
                    $('#radioError').hide();
                }

                // Validate Driver ID only if "Yes" is selected
                if (hasDriver === 'yes' && driverId === '') {
                    $('#driverId').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#driverId').removeClass('is-invalid');
                }

                if (!isValid) {
                    return;
                }

                // Build URL with query parameters
                let url = "{{ route('report.create') }}" + "?has_driver=" + hasDriver;

                if (hasDriver === 'yes') {
                    url += "&driver_id=" + encodeURIComponent(driverId);
                }

                // Redirect with parameters
                window.location.href = url;
            });

            // Reset form when modal is closed
            $('#confirmModal').on('hidden.bs.modal', function () {
                // Clear radio selection
                $('input[name="hasDriver"]').prop('checked', false);
                // Hide radio error
                $('#radioError').hide();
                // Hide driver ID input and clear its value
                $('#driverIdGroup').hide();
                $('#driverId').prop('required', false).val('');
                $('#driverId').removeClass('is-invalid');
            });

            // Handle delete
            $('#datatable').on('click', '.delete', function(e) {
                e.preventDefault();
                let action = $(this).attr('href');
                $('#myAction').attr('action', action);
                swal({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this record!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dd4848',
                    cancelButtonColor: '#8f8f8f',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.value) {
                        $('#myAction').submit();
                    }
                });
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
