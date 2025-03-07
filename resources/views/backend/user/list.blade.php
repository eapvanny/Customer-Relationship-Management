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
                    <h1>
                        {{ __('User') }}
                        <small> {{ __('List') }} </small>
                    </h1>
                    <div class="box-tools pull-right">
                        {{-- @if (auth()->user()->newRole->role_id != AppHelper::USER_ADMIN)

                            <a class="btn btn-info text-white" href="{{ URL::route('user.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>

                        @endif --}}
                        @can('create user')
                            <a class="btn btn-info text-white" href="{{ URL::route('user.create') }}"><i
                                    class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>
                        @endcan
                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
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
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Phone No.') }}</th>
                                            <th>{{ __('Role') }}</th>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{!! route('user.index', Request::query()) !!}",
                },
                columns: [
                    {   
                        data: 'photo',
                        name: 'photo',
                    },
                    {
                        data: 'staff_id_card',
                        name: 'staff_id_card',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'position',
                        name: 'position',
                    },
                    {
                        data: 'area',
                        name: 'area',
                    },
                    {
                        data: 'username',
                        name: 'username',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'phone_no',
                        name: 'phone_no',
                    },
                    {
                        data: 'role',
                        name: 'role',
                    },
                    {
                        data: 'gender',
                        name: 'gender',
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
                ],
                "fnDrawCallback": function() {
                    $('#datatabble input.statusChange').bootstrapToggle({
                        on: "<i class='fa fa-check-circle'></i>",
                        off: "<i class='fa fa-ban'></i>"
                    });
                }
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
