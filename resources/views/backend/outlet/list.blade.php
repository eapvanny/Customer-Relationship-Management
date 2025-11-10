@extends('backend.layouts.master')

@section('pageTitle')
    Outlet
@endsection

@section('bodyCssClass')
@endsection

@php
    use App\Http\Helpers\AppHelper;
@endphp

@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
            <li class="active">{{ __('Depot Management') }}</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h4>{{ __('Depot List') }}</h4>
                    <div class="box-tools pull-right">
                        <a class="btn btn-info text-white" href="{{ URL::route('outlet.create') }}"><i
                                class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>
                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header"></div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-success btn-sm" href="{{ route('outlet.export') }}"><i
                                            class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table id="datatabble"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Region') }}</th>
                                            <th>{{ __('Depot Code') }}</th>
                                            <th>{{ __('Depot') }}</th>
                                            <th>{{ __('Contact') }}</th>
                                            <th>{{ __('Created by') }}</th>
                                            <th class="notexport" style="min-width: 90px;">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($outlets as $key => $item)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td class="text-start">{{ $item->region->region_name }} @if (auth()->user()->user_lang == 'en')
                                                        {{ $item->region->rg_manager_en }}
                                                    @else
                                                        {{ $item->region->rg_manager_kh }}
                                                    @endif {{ '( ' . $item->region->se_code . ' )' }}
                                                </td>
                                                <td class="text-start">{{ $item->code }}</td>
                                                <td class="text-start">{{ $item->name }}</td>
                                                <td class="text-start"> {{ $item->phone }} </td>
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
                                                    <a type="button" id="detailOutlet" data-id="{{ $item->id }}"
                                                        class="me-2" title="Detail"><i class="fa fa-eye"
                                                            aria-hidden="true"></i></a>
                                                    @hasTypePermission('edit depot management')
                                                        <a href="{{ route('outlet.edit', $item->id) }}" title="Edit"><i
                                                                class="fa fa-edit"></i></a>
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
    <!-- /.content -->

    <!-- Modal photo -->
    <div class="modal modal-lg fade" id="depotDetailModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">{{ __('Depot Detail') }}</h5>
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
                                                <strong>{{ __('Phone Number') }}</strong> <span
                                                    id="modalPhoneNumber"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>{{ __('Customer Type') }}</strong> <span id="modalType"></span>
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
                                <b>{{ __('Depot Photo') }}</b>
                            </div>
                            <img style="border: 1px solid #cfcfcf; width: 100%; height: auto; object-fit: cover;"
                                id="modalPhotoOutlet" src="" class="img-fluid photo-detail"
                                alt="Photo_outlet Detail">

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
    <script type="text/javascript">
        $(document).ready(function() {
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            window.filter_org = 1;
            Generic.initFilter();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#datatabble').dataTable();
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#detailOutlet', function() {
                var id = $(this).data('id');
                var url = "{{ route('outlet.show', ':id') }}".replace(':id', id);

                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json",
                    success: function(response) {
                        var report = response.report;
                        $('#modalRegion').text(report.region);
                        $('#modalDepotCode').text(report.depot_code);
                        $('#modalDepotName').text(report.depot_name);
                        $('#modalPhoneNumber').text(report.phone_number);
                        $('#modalType').text(report.customer_type);
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

                        $('#depotDetailModal').modal('show');

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
