<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Opportunity @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

@section('extraStyle')
    <style>
        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Opportunity') }}
            <small> {{ __('List') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>
            <li class="active"> {{ __('Opportunity') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        {!! AppHelper::selectOrgOfUser($seleted_org) !!}
                        <div class="col-md-3 pull-right">
                            <div class="form-group box-tools pull-right">
                                @can('curriculum.store')
                                    <a class="btn btn-info text-white" href="{{ URL::route('saleitem.opportunity.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
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
                                    <th width="20%"> {{ __('Organization') }} </th>
                                    <th width="15%"> {{ __('Name') }} </th>
                                    <th width="15%"> {{ __('Description') }} </th>
                                    <th width="20%"> {{ __('Is Closed') }} </th>
                                    <th width="10%"> {{ __('Is Won') }} </th>
                                    <th width="10%"> {{ __('Type') }} </th>
                                    <th class="notexport" width="15%"> {{ __('Action') }} </th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        </div>
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

            window.postUrl = '{{URL::Route("curriculum.status", 0)}}';
            window.changeExportColumnIndex = 5;
            window.excludeFilterComlumns = [0,1,6,7];
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            window.filter_org = 1;
            Generic.initFilter();
            var t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax:{
                    url: "{!! route('saleitem.opportunity.index',Request::query()) !!}",
                },
                columns:[
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'org_id',
                        name: 'org_id',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'is_closed',
                        name: 'is_closed',
                        orderable: false
                    },
                    {
                        data: 'is_won',
                        name: 'is_won',
                        orderable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
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

            // t.on( 'order.dt search.dt', function () {
            //     t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            //         cell.innerHTML = i+1;
            //     } );
            // } ).draw();
            $('#datatabble').delegate('.delete','click', function(e){
                let action = $(this).attr('href');
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
