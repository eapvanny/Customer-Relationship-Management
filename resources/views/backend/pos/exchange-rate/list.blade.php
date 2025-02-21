<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Category @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Exchange Rate') }}
            <small> {{ __('List') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li class="active"> {{ __('Exchange Rate') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <form method="get" id="frm_search">
                            {!! AppHelper::selectOrgOfUser($seleted_org) !!}
                        </form>
                        <div class="col-md-3 pull-right">
                            @can('pos.menu.store')
                            <div class="form-group box-tools pull-right">
                                <a class="btn btn-info text-white" href="{{ URL::route('pos.exchange-rate.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                            </div>
                            @endcan
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%"> {{ __('Organization') }} </th>
                                    <th width="10%"> {{ __('From Currency') }} </th>
                                    <th width="10%"> {{ __('To Currency') }} </th>
                                    <th width="10%"> {{ __('Rate') }} </th>
                                    <th width="10%"> {{ __('Fee') }} </th>
                                    <th width="10%"> {{ __('Status') }} </th>
                                    <th class="notexport" width="15%"> {{ __('Action') }} </th>
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
            window.postUrl = '{{URL::Route("pos.exchange-rate.status", 0)}}';
            window.changeExportColumnIndex = 5;
            window.excludeFilterComlumns = [0,1,6,7];
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            window.filter_org = 1;
            Generic.initFilter();

            $('select[name="org_id"]').on('change', function () {
                let org_id = $(this).val();
                if (org_id.trim()) {
                    $('#frm_search').submit();
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }

            });
            var t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax:{
                    url: "{!!  route('pos.exchange-rate.index',request()->all()) !!}",
                },
                columns:[
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'org_id',
                        name: 'org_id'
                    },
                    {
                        data: 'from_currency',
                        name: 'from_currency'
                    },
                    {
                        data: 'to_currency',
                        name: 'to_currency'
                    },
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: 'fee',
                        name: 'fee'
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
                    },
                ],
                "fnDrawCallback": function() {
                    $('#datatabble input.statusChange').bootstrapToggle({
                        on: "Active",
                        off: "Inactive"
                    });
                }
            });

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
