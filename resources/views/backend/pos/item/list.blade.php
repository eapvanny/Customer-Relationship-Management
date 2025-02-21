@php
  $category_ids = [];
  $menu_item_id = $menu_item_id;
  $category_item_id = $category_item_id;
  if (request()->has('menu_item_id') && request()->get('menu_item_id')) {
      $category_ids = App\Models\CategoryItem::where('status',1)->pluck('name', 'id');
  }

@endphp
<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Item @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Item') }}
            <small> {{ __('List') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li class="active"> {{ __('Product') }} </li>
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

                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    {!! Form::select('menu_item_id', $menu_ids, $menu_item_id, ['placeholder' => 'Pick a menu...','class' => 'form-control select2']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    {!! Form::select('category_item_id', $category_ids, $category_item_id , ['placeholder' => 'Pick a category...','class' => 'form-control select2']) !!}
                                </div>
                            </div>
                        </form>
                        <div class="col-md-3 pull-right">
                            @can('pos.item.store')
                            <div class="form-group box-tools pull-right">
                                <a class="btn btn-info text-white" href="{{ URL::route('pos.item.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>
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
                                <th width="10%">{{ __('Organization') }}</th>
                                <th width="10%">{{ __('Category') }}</th>
                                <th width="10%">{{ __('Thumbnail') }}</th>
                                <th width="10%">{{ __('Name') }}</th>
                                <th width="15%">{{ __('Description') }}</th>
                                <th width="10%">{{ __('Default Price') }}</th>
                                <th width="5%">{{ __('Quantity') }}</th>
                                <th width="10%">{{ __('Status') }}</th>
                                <th class="notexport" width="15%">{{ __('Action') }}</th>
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
        var BaseUrl = '{{url('get-category-by-menu')}}';
        var IndexUrl = '{{route('pos.item.index')}}';
        $(document).ready(function () {
            window.postUrl = '{{URL::Route("pos.item.status", 0)}}';
            window.changeExportColumnIndex = 5;
            window.excludeFilterComlumns = [0,1,6,7];
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            // window.filter_org = 1;
            Generic.initFilter();

            $('select[name="menu_item_id"]').on('change', function () {
                let org_id = $('select[name="org_id"]').val();
                let menu_item_id = $(this).val();
                if (menu_item_id.trim() && org_id.trim()) {
                    var finalUri =  IndexUrl+"?org_id="+org_id+"&menu_item_id="+menu_item_id;
                    $('#datatabble').DataTable().ajax.url(finalUri).load();

                    let Url = BaseUrl+"/"+menu_item_id+"?has_other";
                    Generic.loaderStart();
                    axios.get(Url)
                        .then((response) => {
                            if (Object.keys(response.data).length) {
                                $('select[name="category_item_id"]').empty().prepend('<option selected="">All</option>').select2({allowClear: true,placeholder: 'Pick a category...', data: response.data});
                            }
                            else {
                                $('select[name="category_item_id"]').empty().select2({placeholder: 'Pick a category...'});
                                toastr.error('This menu have no category!');
                            }
                            Generic.loaderStop();
                        }).catch((error) => {
                        let status = error.response.statusText;
                        toastr.error(status);
                        Generic.loaderStop();

                    });
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
                url: "{!!  route('pos.item.index',request()->all()) !!}",
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
                    data: 'menu',
                    name: 'menu'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'photo',
                    name: 'photo'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'default_price',
                    name: 'default_price'
                },
                {
                    data: 'qty',
                    name: 'qty'
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

        $('select[name="category_item_id"]').on('change', function () {
            let org_id = $('select[name="org_id"]').val();
            let menu_item_id = $('select[name="menu_item_id"]').val();
            let category_item_id = $(this).val();
            if (category_item_id.trim()) {
                var finalUri =  IndexUrl+"?org_id="+org_id+"&menu_item_id="+menu_item_id+"&category_item_id="+category_item_id;
                t.ajax.url(finalUri).load();
            }
        });

    });
    </script>
@endsection
<!-- END PAGE JS-->
