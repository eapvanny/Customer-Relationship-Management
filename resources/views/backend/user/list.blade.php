<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    User
@endsection
<!-- End block -->

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
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
            {{-- <li> {{ __('Administrator') }} </li> --}}
            <li class="active">{{ __('User') }}</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h4>
                        {{ __('User List') }}
                    </h4>
                    <div class="box-tools pull-right">
                        @if (in_array(auth()->user()->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]))
                            <button id="filters" class="btn btn-outline-secondary d-none" data-bs-toggle="collapse"
                                data-bs-target="#filterContainer">
                                <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                            </button>
                        @endif
                        @hasTypePermission('create user')
                            <a class="btn btn-info text-white" href="{{ URL::route('user.create') }}"><i
                                    class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>
                        @endHasTypePermission
                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header">
                            <div class="row d-none">
                                <div class="col-12 mb-2">
                                    {{-- <form action="{{ route('user.index') }}" method="GET" id="filterForm">
                                        <div class="wrap_filter_form @if (!$is_filter) collapse @endif"
                                            id="filterContainer">
                                            <a id="close_filter" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                            <div class="row">
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label for="manager_id">{{ __('Manager') }}</label>
                                                        {!! Form::select('manager_id', $areaManager, request('manager_id'), [
                                                            'placeholder' => __('Select manager'),
                                                            'id' => 'manager_id',
                                                            'class' => 'form-control select2',
                                                        ]) !!}
                                                    </div>
                                                </div>
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
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mt-2">
                                                    <button id="apply_filter"
                                                        class="btn btn-outline-secondary btn-sm float-end" type="submit">
                                                        <i class="fa-solid fa-magnifying-glass"></i> {{ __('Apply') }}
                                                    </button>
                                                    <a href="{{ route('user.index') }}"
                                                        class="btn btn-outline-secondary btn-sm float-end me-1">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form> --}}
                                </div>
                            </div>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive mt-3">
                                <table id="datatabble"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Photo') }}</th>
                                            <th>{{ __('Staff ID') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Position') }}</th>
                                            <th>{{ __('Area') }}</th>
                                            <th>{{ __('Username') }}</th>
                                            {{-- <th>{{ __('Email') }}</th> --}}
                                            @if (auth()->user()->role_id != AppHelper::USER_MANAGER)
                                                <th>{{ __('Managed By') }}</th>
                                            @endif
                                            <th>{{ __('Phone No.') }}</th>
                                            <th>{{ __('Role') }}</th>
                                            <th>{{ __('User Type') }}</th>
                                            <th>{{ __('Gender') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="notexport" style="min-width: 90px;">{{ __('Action') }}</th>
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
        </div>

    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            window.filter_org = 1;
            Generic.initFilter();
            var t = $('#datatabble').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.index') }}",
                    type: 'GET', // or 'POST' depending on your route method
                    error: function(xhr, error, thrown) {
                        console.log(xhr.responseText); // Log the error for debugging
                    }
                },
                columns: [{
                        data: 'photo',
                        name: 'photo',
                        orderable: false
                    },
                    {
                        data: 'staff_id_card',
                        name: 'staff_id_card'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'area',
                        name: 'area'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    @if (auth()->user()->role_id != AppHelper::USER_MANAGER)
                        {
                            data: 'managed_by',
                            name: 'managed_by',
                            orderable: false
                        },
                    @endif {
                        data: 'phone_no',
                        name: 'phone_no'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ]
            });

            $(document).on('click', '.disable-user', function() {
                var userId = $(this).data('id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to disable this user?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, disable it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/disable/' + userId,
                            type: 'POST',
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Disabled!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    t.ajax.reload(function() {
                                        row.find('td').css('color', 'red');
                                        row.find('.disable-user')
                                            .removeClass(
                                                'btn-danger disable-user')
                                            .addClass('btn-success enable-user')
                                            .html('<i class="fa fa-check"></i>')
                                            .attr('title', 'Enable');
                                    }, false);

                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error: ' + (xhr.responseJSON
                                        ?.message || 'Something went wrong'
                                    ),
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Enable user
            $(document).on('click', '.enable-user', function() {
                var userId = $(this).data('id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to enable this user?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, enable it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/enable/' + userId,
                            type: 'POST',
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Enabled!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    t.ajax.reload(function() {
                                        row.find('td').css('color', '');
                                        row.find('span').css('color', '')
                                            .addClass('status-active');
                                        row.find('.enable-user')
                                            .removeClass(
                                                'btn-success enable-user')
                                            .addClass('btn-danger disable-user')
                                            .html('<i class="fa fa-ban"></i>')
                                            .attr('title', 'Disable');
                                    }, false);

                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error: ' + (xhr.responseJSON
                                        ?.message || 'Something went wrong'
                                    ),
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });


            // t.on( 'order.dt search.dt', function () {
            //     t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            //         cell.innerHTML = i+1;
            //     } );
            // } ).draw();
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
