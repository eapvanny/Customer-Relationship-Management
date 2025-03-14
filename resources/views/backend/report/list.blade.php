<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    Reports
@endsection
<!-- End block -->

@section('extraStyle')
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
        .col-lg-8{
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
                <div class="wrap-outter-header-title">
                    <h1>
                        {{ __('Reports') }}
                        <small class="toch"> {{ __('List') }} </small>
                    </h1>
                    <div class="box-tools pull-right">
                        <button id="filters" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#filterContainer">
                            <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                        </button>
                        <a class="btn btn-info text-white" href="{{ URL::route('report.create') }}"><i
                                class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
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
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label for="date1">{{ __('From Date') }}</label>
                                                        <input type="date" name="date1" id="date1"
                                                            class="form-control" value="{{ request('date1') }}">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label for="date2">{{ __('To Date') }}</label>
                                                        <input type="date" name="date2" id="date2"
                                                            class="form-control" value="{{ request('date2') }}">
                                                    </div>
                                                </div>
                                                @if(in_array(auth()->user()->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]))
                                                    <div class="col-xl-4">
                                                        <div class="form-group">
                                                            <label for="full_name">{{ __('Employee Name') }}</label>
                                                            {!! Form::select('full_name', $full_name, request('full_name'), [
                                                                'placeholder' => __('Select employee'),
                                                                'id' => 'full_name',
                                                                'class' => 'form-control select2',
                                                            ]) !!}
                                                        </div>      
                                                    </div>
                                                @endif
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
                                    <div class="row" style="margin-bottom: -20px">
                                        <div class="col-12">
                                            <a class="btn btn-success btn-sm" href="{{ route('report.export') }}"><i
                                                    class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- /.box-header -->
                        <div class="box-body margin-top-20">
                            <div class="table-responsive mt-4">
                                <table id="datatabble"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th> {{ __('Photo') }} </th>
                                            <th> {{ __('Staff ID') }} </th>
                                            <th> {{ __('Name') }} </th>
                                            <th> {{ __('Area') }} </th>
                                            <th> {{ __('Outlet') }} </th>
                                            <th> {{ __('250ml') }} </th>
                                            <th> {{ __('350ml') }} </th>
                                            <th> {{ __('600ml') }} </th>
                                            <th> {{ __('1500ml') }} </th>
                                            <th> {{ __('Other') }} </th>
                                            <th> {{ __('Material Type') }} </th>
                                            <th> {{ __('Qty') }} </th>
                                            <th> {{ __('Location') }} </th>
                                            <th> {{ __('Date') }} </th>
                                            <th class="notexport" style="max-width: 82px"> {{ __('Action') }} </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>

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
                        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-4 mt-2">
                            <img id="modalPhoto" src="" class="img-fluid photo-detail" alt="Photo Detail">
                        </div>
                        <div class="col-md-8 col-sm-8 col-lg-8 col-xl-8">
                            <div class="report-details">
                                <div class="row">
                                    <div class="col-md-8 col-sm-8 col-lg-8 col-xl-8">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item"><i class="fa fa-user"></i> <strong>{{__('Employee Name')}}:</strong> <span
                                                    id="modalEmployeeName"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-id-card"></i> <strong>{{__('Staff ID')}}:</strong> <span id="modalIdCard"></span>
                                            </li>
                                            <li class="list-group-item"><i class="fa-solid fa-chart-area"></i> <strong>{{__('Area')}} :</strong> <span id="modalArea"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-home"></i> <strong>{{__('Outlet')}} :</strong> <span id="modalOutlet"></span>
                                            </li>
                                            <li class="list-group-item"><i class="fa-solid fa-calendar-days"></i> <strong>{{__('Date')}} :</strong> <span id="modalDate"></span></li>
        
                                            <li class="list-group-item"><i class="fa-solid fa-location-dot"></i> <strong>{{__('Location')}} :</strong> <span id="modalCity"></span></li>
                                            {{-- <li class="list-group-item"><strong>{{__('Country')}} :</strong> <span id="modalCountry"></ </li> --}}
                                        </ul>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-lg-4 col-xl-4">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i> <strong>{{__('250ml')}} :</strong> <span id="modal250ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i> <strong>{{__('350ml')}} :</strong> <span id="modal350ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i> <strong>{{__('600ml')}} :</strong> <span id="modal600ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i> <strong>{{__('1500ml')}} :</strong> <span id="modal1500ml"></span></li>
                                            <li class="list-group-item"><i class="fa-solid fa-bottle-water"></i> <strong>{{__('Other')}} :</strong> <span id="modalOther"></span></li>
                                            <li class="list-group-item"><i class="fa-brands fa-square-letterboxd"></i> <strong>{{__('Material Type')}} :</strong> <span id="modalPosm"></span></li>
                                            <li class="list-group-item"><i class="fa-brands fa-elementor"></i> <strong>{{__('Quantity')}} :</strong> <span id="modalQty"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btnClose" data-bs-dismiss="modal">{{__('Close')}}</button>
                </div>
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
            t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{!! route('report.index', request()->all()) !!}",
                },
                pageLength: 10,
                columns: [{
                        data: 'photo',
                        name: 'photo',
                        orderable: false
                    },
                    {
                        data: 'id_card',
                        name: 'id_card'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'area',
                        name: 'area'
                    },
                    {
                        data: 'outlet',
                        name: 'outlet'
                    },
                    {
                        data: '250ml',
                        name: '250ml'
                    },
                    {
                        data: '350ml',
                        name: '350ml'
                    },
                    {
                        data: '600ml',
                        name: '600ml'
                    },
                    {
                        data: '1500ml',
                        name: '1500ml'
                    },
                    {
                        data: 'other',
                        name: 'other'
                    },
                    {
                        data: 'posm',
                        name: 'posm'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
            });

            $('#close_filter').click(function() {
                $("#filters").trigger('click');
            });

            $(document).on('click', '.img-detail', function() {
                var reportId = $(this).data('id');

                $.ajax({
                    url: '/report/' + reportId,
                    method: 'GET',
                    success: function(response) {
                        var report = response.report;

                        $('#modalPhoto').attr('src', report.photo);
                        $('#modalEmployeeName').text(report.employee_name);
                        $('#modalIdCard').text(report.staff_id_card);
                        $('#modalArea').text(report.area);
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
                        $('#viewModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        alert('Failed to load report details.');
                    }
                });
            });

            $(document).on('click', '.btnClose', function() {
                $('#viewModal').modal('hide');
                $('.img-popup').val('');
            });

            //delete grade_level
            $('#datatabble').delegate('.delete', 'click', function(e) {
                let action = $(this).attr('href');
                console.log()
                $('#myAction').attr('action', action);
                e.preventDefault();
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
