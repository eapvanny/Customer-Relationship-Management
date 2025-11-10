@extends('backend.layouts.master')

@section('pageTitle')
    Depo
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
            <li class="active">{{ __('Depo') }}</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h4>{{ __('Depo List') }}</h4>
                    <div class="box-tools pull-right">
                        <a class="btn btn-info text-white" href="{{ URL::route('depo.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>
                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header"></div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive mt-3">
                                <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Created by') }}</th>
                                            <th>{{ __('Area') }}</th>
                                            <th>{{ __('Depo Name') }}</th>
                                            <th class="notexport" style="min-width: 90px;">{{ __('Action') }}</th>
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
    <!-- /.content -->
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

            var t = $('#datatabble').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('depo.index') }}",
                    error: function (xhr, error, thrown) {
                        console.log('AJAX Error: ', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'area', name: 'area' },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', orderable: false }
                ],
                "fnDrawCallback": function() {
                    $('#datatabble input.statusChange').bootstrapToggle({
                        on: "<i class='fa fa-check-circle'></i>",
                        off: "<i class='fa fa-ban'></i>"
                    });
                }
            });

            $('#datatabble').delegate('.delete', 'click', function(e) {
                let action = $(this).attr('href');
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
