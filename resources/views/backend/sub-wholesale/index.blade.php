<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    {{ __('Sub-Wholesale') }}
@endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        #datatabble th,
        #datatabble td {
            /* text-align: center; */
            /* width: 550px !important; */
            /* background: rgb(237, 237, 237); */
            font-size: small !important;
            /* min-width: 100px !important; */
        }

        .link-group a {
            color: grey;
            padding: 0 5px;
        }

        .link-group a.active {
            color: rgb(0, 121, 190);
            text-decoration: underline;
        }

        .text {
            line-height: 20px !important;
        }

        .text p {
            margin: 0 !important;
        }

        .img-bg {
            width: 100%;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            position: relative !important;
            object-fit: cover !important;
        }

        .lat-long {
            position: absolute !important;
            top: 5px !important;
            background: #ffffff94;
            padding: 0 5px;
            width: fit-content;

        }
        .lat-long p{
            padding: 0 !important;
            margin: 0 !important;
            font-size: 10px;
        }
        .fs-small{
            font-size: medium;
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
            <li class="active"> {{ __('Sub-Wholesale') }} </li>
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
                        {{ __('Sub-Wholesale List') }}
                    </h1>
                    <div class="box-tools pull-right">
                        <button id="filters" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#filterContainer">
                            <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                        </button>
                        <a class="btn btn-secondary text-white" href="{{ URL::route('displaysub.import') }}">
                            <i class="fa fa-download"></i> {{ __('Import data') }}
                        </a>
                        <a class="btn btn-info text-white" href="{{ URL::route('displaysub.create') }}"><i
                                class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                    </div>
                </div>

                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <form action="{{ route('displaysub.index') }}" method="GET" id="filterForm">
                                        <div class="wrap_filter_form @if (!$is_filter) collapse @endif "
                                            id="filterContainer">
                                            <a id="close_filter" class="btn btn-outline-secondary btn-sm"
                                                data-bs-toggle="collapse" data-bs-target="#filterContainer">
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
                                                @if (in_array(auth()->user()->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]))
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
                                                    <a href="{{ route('displaysub.index') }}"
                                                        class="btn btn-outline-secondary btn-sm float-end me-1">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    {{-- <div class="row mb-3">
                                        <div class="col-12 link-group">
                                            <a href="{{ route('retail.index') }}" class="active"> {{ __('Manual List') }} </a> |
                                            <a href="{{ route('retail-import.index')}}"> {{ __('Import List') }} </a>
                                        </div>
                                    </div> --}}
                                    <div class="row" style="margin-bottom: -20px">
                                        <div class="col-12">
                                            <a class="btn btn-success btn-sm"
                                                href="{{ route('displaysub.export', request()->all()) }}"><i
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
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server">
                                    <thead>
                                        <tr>
                                            <th>{{ __('No') }}</th>
                                            <th> {{ __('Staff Info') }} </th>
                                            <th>{{ __('Region') }}</th>
                                            <th>{{ __('Address') }}</th>
                                            <th>{{ __('Customer Code') }}</th>
                                            <th>{{ __('Depot') }}</th>
                                            <th>{{ __('Outlet Type') }}</th>
                                            <th>{{ __('Display QTY') }}</th>
                                            <th>{{ __('FOC') }}</th>
                                            <th class="notexport" style="max-width: 82px"> {{ __('Action') }} </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reports as $key => $item)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td class="text text-start">
                                                    <p>ID: {{ $item->user->staff_id_card }}</p>
                                                    <p>{{ $item->user->family_name . ' ' . $item->user->name }}</p>
                                                </td>
                                                <td class="text">
                                                    {{ $item->region }}
                                                </td>
                                                <td class="text text-start">
                                                    <p> {{ $item->province }}</p>
                                                    <p> {{ $item->district }}</p>
                                                    <p> {{ $item->commune }}</p>
                                                </td>
                                                <td>{{ $item->customer_code }}</td>
                                                <td class="text text-start">
                                                    <p>{{ $item->depot_name }}</p>
                                                    <p>{{ $item->depot_contact }}</p>
                                                </td>
                                                <td>
                                                    {{ $item->outlet_type }}
                                                </td>
                                                <td>{{ $item->display_qty }}</td>
                                                <td>{{ $item->sku }} ML x {{ $item->incentive }}</td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0);" class="img-detail me-2"
                                                        data-id="{{ $item->id }}" title="{{ __('View') }}"><i
                                                            class="fa fa-eye"></i></a>
                                                    @hasTypePermission('update sub-wholesale')
                                                        <a href="{{ route('displaysub.edit', $item->id) }}"
                                                            class="text-success me-2" title="{{ __('Edit') }}"><i
                                                                class="fa fa-edit"></i></a>
                                                    @endHasTypePermission

                                                    @hasTypePermission('take photo sub-wholesale')
                                                        <a href="{{ route('displaysub.takePicture', $item->id) }}"
                                                            class="text-secondary" title="{{ __('Camera') }}"><i
                                                                class="fa fa-camera"></i></a>
                                                    @endHasTypePermission
                                                </td>
                                            </tr>
                                        @endforeach

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

                        <div class="col-md-12">
                            <div class="report-details">
                                <div class="row">

                                    <div class="col-md-6">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Staff ID') }}:</strong> <span id="modalIdCard"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Employee Name') }}:</strong> <span
                                                    id="modalEmployeeName"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Region') }} :</strong> <span id="modalRegion"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Province') }} :</strong> <span id="modalProvince"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('District') }} :</strong> <span id="modalDistrict"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Commune') }} :</strong> <span id="modalCommune"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("SM's Name") }} :</strong> <span id="modalSmName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("RSM's Name") }} :</strong> <span id="modalRsmName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("ASM's Name") }} :</strong> <span id="modalAsmName"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("SE's Name") }} :</strong> <span id="modalSeName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("SE's Code") }} :</strong> <span id="modalSeCode"></span>
                                            </li>


                                            {{-- <li class="list-group-item"><strong>{{__('Country')}} :</strong> <span id="modalCountry"></ </li> --}}
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("Customer's Code") }} :</strong>
                                                <span id="modalCustomerCode"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __("Depot's Name") }} :</strong>
                                                <span id="modalDepoName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Depot Contact') }} :</strong>
                                                <span id="modalDepoContact"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Sub-WS Name') }} :</strong>
                                                <span id="modalWholesaleName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Sub-WS Contact') }} :</strong>
                                                <span id="modalWholesaleContact"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Outlet Type') }} :</strong> <span
                                                    id="modalBusinessType"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Sale KPI') }} :</strong> <span id="modalSaleKPI"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Display QTY') }} :</strong>
                                                <span id="modalDisplayQty"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('SKU') }} :</strong> <span id="modalSKU"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Incentive') }} :</strong> <span id="modalIncentive"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Remark') }} :</strong> <span id="modalRemark"></span>
                                            </li>


                                        </ul>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Create date') }} :</strong> <span
                                                    id="modalCreateDate"></span>
                                            </li>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 my-2">
                            <div class="row mt-4" id="showPictures">
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

            $('#datatabble').DataTable();

            $('#close_filter').click(function() {
                $("#filters").trigger('click');
            });

            $(document).on('click', '.img-detail', function() {
                var reportId = $(this).data('id');
                $('#viewModal').modal('show');

                var url = '{{ route('displaysub.show', ':id') }}'.replace(':id', reportId);
                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var report = response.report;
                        $('#modalEmployeeName').text(report.modalEmployeeName);
                        $('#modalIdCard').text(report.modalIdCard);

                        $('#modalRegion').text(report.modalRegion);
                        $('#modalProvince').text(report.modalProvince);
                        $('#modalDistrict').text(report.modalDistrict);
                        $('#modalCommune').text(report.modalCommune);

                        $('#modalSmName').text(report.modalSmName);
                        $('#modalRsmName').text(report.modalRsmName);
                        $('#modalAsmName').text(report.modalAsmName);
                        $('#modalSeName').text(report.modalSeName);
                        $('#modalSeCode').text(report.modalSeCode);
                        $('#modalCustomerCode').text(report.modalCustomerCode);
                        $('#modalDepoName').text(report.modalDepoName);
                        $('#modalDepoContact').text(report.modalDepoContact);

                        $('#modalWholesaleName').text(report.modalWholesaleName);
                        $('#modalWholesaleContact').text(report.modalWholesaleContact);
                        $('#modalBusinessType').text(report.modalBusinessType);
                        $('#modalSaleKPI').text(report.modalSaleKPI);
                        $('#modalDisplayQty').text(report.modalDisplayQty);

                        $('#modalSKU').text(report.modalSKU);
                        $('#modalIncentive').text(report.modalIncentive);

                        $('#modalRemark').text(report.modalRemark);
                        $('#modalCreateDate').text(report['modalCreateDate']);
                        $('#showPictures').html(response.picture);
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
            // $('#datatabble').delegate('.delete', 'click', function(e) {
            //     let action = $(this).attr('href');
            //     console.log()
            //     $('#myAction').attr('action', action);
            //     e.preventDefault();
            //     swal({
            //         title: 'Are you sure?',
            //         text: 'You will not be able to recover this record!',
            //         type: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#dd4848',
            //         cancelButtonColor: '#8f8f8f',
            //         confirmButtonText: 'Yes, delete it!',
            //         cancelButtonText: 'No, keep it'
            //     }).then((result) => {
            //         if (result.value) {
            //             $('#myAction').submit();
            //         }
            //     });
            // });
        });
    </script>
@endsection


<!-- END PAGE JS-->
