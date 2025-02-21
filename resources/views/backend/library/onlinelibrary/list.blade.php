<!-- Master page  -->
@extends('backend.layouts.master')
<!-- Page title -->
@section('pageTitle') Online Library @endsection
<!-- End block -->
<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->
{{-- @php
    $iclass                   = request()->list_class??"";
    $section_id               = request()->list_section??"";
    $student_id               = request()->list_regi_id??"";
    $not_format_date_from     = request()->date_from ??"";
    $not_format_date_upto     = request()->date_upto ??"";
    $params_array             = Request::query();
    $full_url                 = route('library.fine.index',$params_array);
@endphp --}}

@section('extraStyle')
    <style>
        /* @media screen and (max-width: 768px) {
            div#datatabble_filter {
                padding: 10px;
                float: right;
            }
            .get_filter{
                margin-bottom: 10px;
            }
        } */
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Online Library') }}
            <small> {{ __('List') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li class="active"> {{ __('Online Library') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="col-md-3 pull-right">
                            <div class="form-group box-tools pull-right">
                                @can('library.onlinelibrary.create')
                                <a class="btn btn-info text-white" href="{{ URL::route('library.onlinelibrary.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%"> {{ __('Title') }} </th>
                                <th width="10%"> {{ __('Author') }} </th>
                                <th width="10%"> {{ __('Privacy') }} </th>
                                <th width="10%"> {{ __('Class') }} </th>
                                <th width="10%"> {{ __('Section') }} </th>
                                <th width="10%"> {{ __('Description') }} </th>
                                <th class="notexport" width="10%"> {{ __('Action') }} </th>
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

    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax:{
                    url: "{!! route('library.onlinelibrary.index',Request::query()) !!}",
                },
                columns:[
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'author',
                        name: 'author',
                    },
                    {
                        data: 'privacy',
                        name: 'privacy',
                    },
                    {
                        data: 'class',
                        name: 'class',
                    },
                    {
                        data: 'section',
                        name: 'section',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
            });

            // t.on( 'order.dt search.dt', function () {
            //     t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            //         cell.innerHTML = i+1;
            //     } );
            // } ).draw();
            $('#datatabble').delegate('.delete','click', function(e){
                let action = $(this).attr('href');
                console.log()
                $('#myAction').attr('action',action);
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
