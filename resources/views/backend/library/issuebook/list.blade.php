<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Issue Book @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

@php
    $regi_no       = request()->regi_no??"";
    $isbn_no_code  = request()->isbn_no_code??"";
    $issue_date    = request()->issue_date??"";
    $return_date   = request()->return_date ??"";
    $status        = request()->status ??"";
    $params_array  = Request::query();
    $full_url      = route('library.issuebook.index',$params_array);
@endphp

@section('extraStyle')
    <style>
        @media screen and (max-width: 768px) {
            div#datatabble_filter {
                padding: 10px;
                float: right;
            }
            .get_filter{
                margin-bottom: 10px;
            }
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Issue Book') }}
            <small> {{ __('List') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li class="active"> {{ __('Issue Book') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
            <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header border">
                                {{-- <div class="callout callout-danger">
                                    <p><b>Note:</b> Default search result is 300. If not found book then narrow your search filter.</p>
                                </div> --}}
                                <h3 class="text-info" style="margin-left: 10px;"> {{ __('Filters:') }} </h3>
                                <div class="box-tools pull-right">
                                    <a class="btn btn-info text-white margin-top-20" href="{{ URL::route('library.issuebook.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            {{-- <legend>Filters:</legend> --}}
                                            <form action="" method="get" enctype="multipart/form-data">
                                                <input type="hidden" name="filter" value="1">
                                            <div class="row">
                                                {!! AppHelper::selectOrgOfUser($seleted_org,true) !!}
                                                <div class="col-md-4">
                                                    <div class="form-group has-feedback">
                                                        <label for="regi_no"> {{ __('Student Regi No.') }} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="regi_no" name="regi_no" placeholder="registration number" value="{{$regi_no}}">
                                                        <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group has-feedback">
                                                        <label for="isbn_no_code"> {{ __('ISBN no./Code') }} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="isbn_no_code" name="isbn_no_code" placeholder="isbn number/code" value="{{$isbn_no_code}}">
                                                        <span class="fa fa-book form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $is_d = null;
                                                    $re_d = null;
                                                    if($issue_date){

                                                    }
                                                    $is_d = \Carbon\Carbon::createFromFormat('Y-m-d', $issuebook->issue_date);
                                                     $re_d = \Carbon\Carbon::createFromFormat('Y-m-d', $issuebook->return_date);
                                                @endphp --}}

                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 col-sm-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="issue_date"> {{ __('Issue Date') }} </label>
                                                        <div class="input-group" style="display: block">
                                                            <input type="text" readonly="" class="form-control date_picker_with_clear" name="issue_date" placeholder="date" value="{{$issue_date}}" maxlength="10">
                                                            <span class="fa fa-calendar form-control-feedback"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="return_date"> {{ __('Return Date') }} </label>
                                                        <div class="input-group" style="display: block">
                                                            <input type="text" readonly="" class="form-control date_picker_with_clear" name="return_date" placeholder="date" value="{{$return_date}}" maxlength="10">
                                                            <span class="fa fa-calendar form-control-feedback"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group has-feedback">
                                                        <label for="status"> {{ __('Status') }} </label>
                                                        <select class="form-control" style="padding-right: 14px" name="status">
                                                            <option value="-1" >All</option>
                                                            <option value="0" @if(isset($status)) @if ($status == 0&&$status!='') {{ 'selected' }} @endif  @endif> {{ __('Not Return') }} </option>
                                                            <option value="1" @if(isset($status)) @if ($status == 1) {{ 'selected' }} @endif  @endif> {{ __('Return') }} </option>
                                                        </select>
                                                        <span class="form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-info pull-right text-white get_filter" style="margin-top: 24px;"><i class="fa fa-filter"></i> {{ __('Get List') }} </button>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                        <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%"> {{ __('ISBN no.') }} </th>
                                            <th width="20%"> {{ __('Book Name') }} </th>
                                            <th width="20%"> {{ __('Student Name') }} </th>
                                            <th width="10%"> {{ __('Issue Date') }} </th>
                                            <th width="10%"> {{ __('Return Date') }} </th>
                                            <th width="10%"> {{ __('Quantity') }} </th>
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
            window.postUrl = '{{URL::Route("library.issuebook.status", 0)}}';
            window.excludeFilterComlumns = [0,4,5,6,7];
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax:{
                    url: "{!! $full_url !!}",
                },
                columns:[
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'isbn',
                        name: 'isbn',
                    },
                    {
                        data: 'book_name',
                        name: 'book_name',
                    },
                    {
                        data: 'student_name',
                        name: 'student_name',
                    },
                    {
                        data: 'issue_date',
                        name: 'issue_date',
                    },
                    {
                        data: 'return_date',
                        name: 'return_date',
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
                "fnDrawCallback": function() {
                    $('#datatabble input.statusChange').bootstrapToggle({
                        on: "Return",
                        off: "Not Return"
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
        });
    </script>
@endsection
<!-- END PAGE JS-->
