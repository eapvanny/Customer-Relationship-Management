<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    ASM Program
@endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        #datatabble th, #datatabble td {
            /* text-align: center; */
            width: 550px !important;
            /* background: rgb(237, 237, 237); */
            font-size: small !important;
            min-width: 100px !important;
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
@php
    use App\Http\Helpers\AppHelper;
@endphp
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li class="active"> {{ __('ASM Program') }} </li>
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
                        {{ __('ASM Program') }}
                        <small class="toch"> {{ __('List') }} </small>
                    </h1>
                    <div class="box-tools pull-right">
                        <button id="filters" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#filterContainer">
                            <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                        </button>
                        <a class="btn btn-info text-white" href="{{ URL::route('asm.create') }}"><i
                                class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                    </div>
                </div>

                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <form action="{{ route('asm.index') }}" method="GET" id="filterForm">
                                        <div class="wrap_filter_form collapse "
                                            id="filterContainer">
                                            <a id="close_filter" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                                            data-bs-target="#filterContainer">
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
                                                    <a href=""
                                                        class="btn btn-outline-secondary btn-sm float-end me-1">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row" style="margin-bottom: -20px">
                                        <div class="col-12">
                                            <a class="btn btn-success btn-sm" href="{{ route('asm.export') }}"><i
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
                                            {{-- <th> {{ __('Photo') }} </th>
                                            <th> {{ __('Staff ID') }} </th>
                                            <th> {{ __('Name') }} </th> --}}
                                            <th> {{ __('Photo') }} </th>
                                            <th> {{ __('Area') }} </th>
                                            <th> {{ __('Outlet') }} </th>
                                            <th> {{ __('Customer') }} </th>
                                            <th> {{ __('Customer Type') }} </th>

                                            <th> {{ __('250ml') }} </th>
                                            <th> {{ __('350ml') }} </th>
                                            <th> {{ __('600ml') }} </th>
                                            <th> {{ __('1500ml') }} </th>
                                            <th> {{ __('Phone number') }} </th>
                                            <th> {{ __('Other') }} </th>
                                            {{-- <th>{{__('Latitude')}}</th>
                                            <th>{{__('Longitude')}}</th> --}}
                                            {{-- <th> {{ __('Material Type') }} </th>
                                            <th> {{ __('Qty') }} </th> --}}
                                            <th> {{ __('Address') }} </th>
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
                            <img  style="border: 1px solid #cfcfcf;" id="modalPhoto" src="" class="img-fluid photo-detail" alt="Photo Detail">
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
        
                                            <li class="list-group-item"><i class="fa-solid fa-location-dot"></i> <strong>{{__('Address')}} :</strong> <span id="modalCity"></span></li>
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
{{-- @section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();

            $('#datatabble').DataTable();
            // $.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         }
            //     });
            //     t = $('#datatabble').DataTable({
            //     processing: false,
            //     serverSide: true,
            //     ajax: {
            //         url: "{!! route('role.index', request()->all()) !!}",
            //     },
            //     pageLength: 10,
            //         columns: [{
            //                 data: 'DT_RowIndex',
            //                 name: 'DT_RowIndex',
            //                 orderable: false,
            //                 searchable: false
            //             },
            //             {
            //                 data: 'name',
            //                 name: 'name'
            //             },
            //             {
            //                 data: 'permission',
            //                 name: 'permission'
            //             },
            //             {
            //                 data: 'action',
            //                 name: 'action',
            //                 orderable: false
            //             }
            //         ],
            // });

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
@endsection --}}



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
                    url: "{!! route('asm.index', request()->all()) !!}",
                },
                pageLength: 10,
                columns: [
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false
                    },
                    // {
                    //     data: 'id_card',
                    //     name: 'id_card'
                    // },
                    // {
                    //     data: 'name',
                    //     name: 'name'
                    // },
                    {
                        data: 'area',
                        name: 'area'
                    },
                    {
                        data: 'outlet',
                        name: 'outlet'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'customer_type',
                        name: 'customer_type'
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
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'other',
                        name: 'other'
                    },
                    // {
                    //     data: 'posm',
                    //     name: 'posm'
                    // },
                    // {
                    //     data: 'qty',
                    //     name: 'qty'
                    // },
                    {
                        data: 'city',
                        name: 'city'
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
                    url: '/asm/' + reportId,
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
