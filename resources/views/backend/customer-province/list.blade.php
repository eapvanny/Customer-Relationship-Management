@extends('backend.layouts.master')

@section('pageTitle')
    {{ __('Customers Management (Province)') }}
@endsection

@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
            <li class="active">{{ __('Customer Management (Province)') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h4>
                        {{ __('Customer List (Province)') }}
                    </h4>
                    <div class="action-btn-top none_fly_action_btn">
                        <a href="{{ route('cp.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i>
                            {{ __('Add New') }}
                        </a>
                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-success btn-sm" href="{{ route('cp.export') }}"><i
                                            class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <table id="datatable"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('No') }}</th>
                                            <th>{{ __('Customer Code') }}</th>
                                            <th>{{ __('Region') }}</th>
                                            <th>{{ __('Depot') }}</th>
                                            <th>{{ __('Customer Name') }}</th>
                                            <th>{{ __('Customer Type') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Created by') }}</th>
                                            <th style="max-width: 82px">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $key => $item)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td class="text-start">{{ $item->code }}</td>
                                                <td class="text-start">
                                                    {{ $item->region->region_name . ' - ' . $item->region->se_code }}</td>
                                                <td class="text-start">{{ $item->outlet->name }}</td>
                                                <td class="text-start">{{ $item->name }}</td>
                                                <td class="text-start">
                                                    {{ \App\Http\Helpers\AppHelper::CUSTOMER_TYPE_PROVINCE[$item->customer_type] ?? 'N/A' }}
                                                </td>
                                                <td class="text-start">{{ $item->phone }}</td>
                                                <td class="text-start">
                                                    @if (auth()->user()->user_lang == 'en')
                                                        {{ $item->user->family_name_latin . ' ' . $item->user->name_latin }}
                                                    @else
                                                        {{ $item->user->family_name . ' ' . $item->user->name }}
                                                    @endif
                                                    <span
                                                        class="text-muted d-block">{{ Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i:s A') }}</span>
                                                </td>
                                                <td>
                                                    <a type="button" id="detailCustomer" data-id="{{ $item->id }}"
                                                        class="me-2" title="Detail"><i class="fa fa-eye"
                                                            aria-hidden="true"></i></a>

                                                    @hasTypePermission('update customer province')
                                                        <a href="{{ route('cp.edit', $item->id) }}" title="Edit"><i
                                                                class="fa fa-edit"></i></a>
                                                    @endHasTypePermission
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
    <div class="modal modal-lg fade" id="customerDetailModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">{{ __('Customer Detail') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="btnClose"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body img-popup">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-lg-12">
                            <div class="report-details">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <ul class="list-group list-group-unbordered profile-log">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Region') }}</strong> <span id="modalRegion"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Depot Code') }}</strong> <span id="modalDepotCode"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Depot Name') }}</strong> <span id="modalDepotName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Customer Code') }}</strong> <span id="modalCustomerCode"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Customer Name') }}</strong> <span id="modalCustomerName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Phone Number') }}</strong> <span
                                                    id="modalPhoneNumber"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Customer Type') }}</strong> <span id="modalType"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Create By') }}</strong> <span id="modalCreateBy"></span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Date') }}</strong> <span id="modalDate"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-lg-12">
                            <div class="mt-2">
                                <b>{{ __('Customer Photo') }}</b>
                            </div>
                            <img style="border: 1px solid #cfcfcf; width: 100%; height: auto; object-fit: cover;"
                                id="modalPhotoOutlet" src="" class="img-fluid photo-detail"
                                alt="Photo Detail">

                        </div>
                        <div class="col-md-12 col-sm-12 col-lg-12">
                            <div class="mt-2">
                                <b>{{ __('Address') }}</b>
                            </div>
                            <div id="modalAddress" class="col-md-12 col-sm-12 col-lg-12">

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
@endsection

@section('extraScript')
    <script>
        $(document).ready(function() {
            const table = $('#datatable').DataTable();
        });
    </script>

    <script>
         $(document).ready(function() {
            $(document).on('click', '#detailCustomer', function() {
                var id = $(this).data('id');
                var url = "{{ route('cp.show', ':id') }}".replace(':id', id);

                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json",
                    success: function(response) {
                        var report = response.report;
                        $('#modalRegion').text(report.region);
                        $('#modalDepotCode').text(report.depot_code);
                        $('#modalDepotName').text(report.depot_name);

                        $('#modalCustomerCode').text(report.customer_code);
                        $('#modalCustomerName').text(report.customer_name);

                        $('#modalPhoneNumber').text(report.phone_number);
                        $('#modalType').text(report.customer_type);
                        $('#modalCreateBy').text(report.created_by);
                        $('#modalDate').text(report.date);
                        $('#modalPhotoOutlet').attr('src', report.outlet_photo);

                        $('#modalAddress').html(`
                            <p>${report.city}</p>
                            <div>
                                <iframe
                                    width="100%"
                                    height="250"
                                    style="border:0;"
                                    loading="lazy"
                                    allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps?q=${report.lat},${report.long}&hl=en&z=14&output=embed">
                                </iframe>
                            </div>
                        `);

                        $('#customerDetailModal').modal('show');

                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        alert('Failed to load report details.');
                    }
                });
            });
        });
    </script>
@endsection
