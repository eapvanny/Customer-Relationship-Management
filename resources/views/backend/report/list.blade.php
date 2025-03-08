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

        .table-responsive {
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
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h1>
                        {{ __('Reports') }}
                        <small> {{ __('List') }} </small>
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
