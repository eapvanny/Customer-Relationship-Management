<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Book @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->
@php
    $code         = request()->code??"";
    $name         = request()->name??"";
    $auth         = request()->author??"";
    $type         = request()->type ??"";
    $class        = request()->i_classes_id ??"";
    $params_array = Request::query();
    $full_url     = route('library.book.index',$params_array);
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
            {{ __('Book') }}
            <small> {{ __('List') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{URL::route('library.book.index')}}"> {{ __('Library') }} </a></li>
            <li class="active"> {{ __('Book') }} </li>
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
                                    <a class="btn btn-info text-white margin-top-20" href="{{ URL::route('library.book.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
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
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        <label for="isbn_no"> {{ __('Code/ISBN No.') }} </label>
                                                        <input autofocus="" type="text" class="form-control" name="code" placeholder="ISBN No." value="{{$code}}" maxlength="20">
                                                        <span class="fa fa-code form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        <label for="name"> {{ __('Name') }} </label>
                                                        <input type="text" class="form-control" name="name" placeholder="name" value="{{$name}}" maxlength="255">
                                                        <span class="fa fa-info form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        <label for="author"> {{ __('Author') }} </label>
                                                        <input type="text" class="form-control" name="author" placeholder="author name" value="{{$auth}}" maxlength="255">
                                                        <span class="fa fa-info form-control-feedback"></span>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        <label for="type"> {{ __('Type') }} </label>
                                                        {{-- <select class="form-control select2 select2-hidden-accessible" required="true" name="type" data-select2-id="1" tabindex="-1" aria-hidden="true"><option value="0" data-select2-id="3">All</option><option value="1">Academic</option><option value="2">Novel</option><option value="3">Magazine</option><option value="4">Other</option></select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="2" style="width: 307.771px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-type-q8-container"><span class="select2-selection__rendered" id="select2-type-q8-container" role="textbox" aria-readonly="true" title="All">All</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span> --}}
                                                        {{-- {!! Form::select('type', $classes, $type ) !!} --}}
                                                        <select class="form-control select2" name="type">
                                                            <option value="All">All</option>
                                                            <option value="Academic" @if($type) @if($type=='Academic'){{ 'selected' }}  @endif  @endif> {{ __('Academic') }} </option>
                                                            <option value="Novel" @if($type) @if($type=='Novel'){{ 'selected' }} @endif @endif> {{ __('Novel') }} </option>
                                                            <option value="Magazine" @if($type) @if($type=='Magazine'){{ 'selected' }} @endif  @endif> {{ __('Magazine') }} </option>
                                                            <option value="Other" @if($type) @if($type=='Other'){{ 'selected' }} @endif @endif> {{ __('Other') }} </option>

                                                        </select>
                                                        <span class="form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        <label for="class_id"> {{ __('Class') }} </label>
                                                        {{-- <select class="form-control select2 select2-hidden-accessible" required="true" name="class_id" data-select2-id="4" tabindex="-1" aria-hidden="true"><option value="0" selected="selected" data-select2-id="6">All</option><option value="1">One</option><option value="2">Two</option><option value="3">Three</option><option value="4">Four</option><option value="5">Five</option><option value="6">Six</option><option value="7">Seven</option><option value="8">Eight</option><option value="9">Nine Science</option><option value="10">Nine Humanities</option><option value="11">nursury</option><option value="12">Grade 1</option></select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="5" style="width: 307.771px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-class_id-v5-container"><span class="select2-selection__rendered" id="select2-class_id-v5-container" role="textbox" aria-readonly="true" title="All">All</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span> --}}
                                                        {!! Form::select('i_classes_id', $classes, $class , ['class' => 'form-control select2']) !!}
                                                        <span class="form-control-feedback"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-info pull-right text-white get_filter" style="margin-top: 20px;"><i class="fa fa-filter"></i> {{ __('Get List') }} </button>
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
                                            <th width="20%"> {{ __('Name') }} </th>
                                            <th width="10%"> {{ __('Author') }} </th>
                                            <th width="10%"> {{ __('Type') }} </th>
                                            <th width="10%"> {{ __('Class') }} </th>
                                            <th width="7%"> {{ __('Total Quantity') }} </th>
                                            <th width="6%"> {{ __('Lost Quantity') }} </th>
                                            <th width="7%"> {{ __('Stock Quantity') }} </th>
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

        {{-- <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <h3 class="text-info" style="margin-left: 10px;">Filters</h3>
                        <div class="box-tools pull-right">
                            <a class="btn btn-info text-white" href="{{ URL::route('library.book.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body margin-top-20">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" enctype="multipart/form-data">
                                    <input type="hidden" name="filter" value="1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group has-feedback">
                                                <label for="name">Code/ISBN No.</label>
                                                <input autofocus type="text" class="form-control" name="code" placeholder="ISBN No" >
                                                <span class="fa fa-code form-control-feedback"></span>
                                                <span class="text-danger">{{ $errors->first('code') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group has-feedback">
                                                <label for="name">Name</label>
                                                <input autofocus type="text" class="form-control" name="name" placeholder="name" >
                                                <span class="fa fa-info form-control-feedback"></span>
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group has-feedback">
                                                <label for="name">Author</label>
                                                <input autofocus type="text" class="form-control" name="author" placeholder="author" >
                                                <span class="fa fa-info form-control-feedback"></span>
                                                <span class="text-danger">{{ $errors->first('author') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="name">Type</label>
                                            <div class="form-group has-feedback">
                                                {!! Form::select('i_classes_id', $classes, $class , ['class' => 'form-control select2']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="name">Class</label>
                                            <div class="form-group has-feedback">
                                                {!! Form::select('i_classes_id', $classes, $class , ['class' => 'form-control select2']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-info pull-right text-white" style="margin-top: 20px;"><i class="fa fa-filter"></i> Get List</button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <input type="hidden" name="filter" value="1">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group has-feedback">
                                                {!! Form::select('employee_id', $employees, $employee , ['class' => 'form-control select2']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group has-feedback">
                                                {!! Form::select('book_type', array_merge([0 => "All"] , AppHelper::LEAVE_TYPES), $book_type , ['class' => 'form-control select2',]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group has-feedback">
                                                <input type='text' class="form-control date_picker_with_clear"  readonly name="leave_date" placeholder="date" value="@if($leave_date){{$leave_date}} @endif" required minlength="10" maxlength="10" />
                                                <span class="fa fa-calendar form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group has-feedback">
                                                {!! Form::select('status', [0=> "All", 1=> 'Pending', 2 => 'Aprroved', 3=> 'Rejected' ], $status , ['class' => 'form-control select2',]) !!}

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-info text-white" type="submit"><i class="fa fa-list"></i> Get List</button>

                                        </div>

                //                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="listDataTableWithSearch" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">ISBN no.</th>
                                    <th width="5%">name</th>
                                    <th width="5%">Author</th>
                                    <th width="10%">Type</th>
                                    <th width="5%">Class</th>
                                    <th width="5%">Total Quantity</th>
                                    <th width="5%">Stock Quantity</th>
                                    <th class="notexport" width="20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($books as $book)
                                    <tr>
                                        <td>
                                            {{$loop->iteration}}
                                        </td>
                                        <td>{{ $book->code }}</td>
                                        <td>{{ $book->name}}</td>
                                        <td>{{ $book->author}}</td>
                                        <td>{{ $book->type}}</td>
                                        <td>{{ $book->i_classes_id}}</td>
                                        <td>{{ $book->qty}}</td>
                                        <td>

                                            <div class="btn-group">
                                                <form  class="myAction" method="POST" action="{{URL::route('library.book.destroy',$book->id)}}">
                                                    @csrf
                                                    {{ method_field('delete') }}
                                                    <input type="hidden" name="hiddenId" value="{{$book->id}}">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div> --}}

    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function () {
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
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'author',
                        name: 'author',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'class',
                        name: 'class',
                    },
                    {
                        data: 't_qty',
                        name: 't_qty',
                    },
                    {
                        data: 'l_qty',
                        name: 'l_qty',
                    },
                    {
                        data: 's_qty',
                        name: 's_qty',
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
