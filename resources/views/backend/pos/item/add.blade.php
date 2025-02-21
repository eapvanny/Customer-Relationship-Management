@php
  $is_cate = false;
//   dd(session()->getOldInput());
  $menu_id = old('menu_item_id');
  $infoImage = null;
  $is_read_only = null;
  $thumbnailDropzoneImage = old('thumbnailDropzoneImage');
  $name = old('name') ?? ($item ? $item->name : null);
  $default_price = old('default_price') ?? ($item ? $item->default_price : null);
  $status = old('status') ?? ($item ? $item->status : null);
  $description = old('description') ?? ($item ? $item->description : null);
  $default_qty = old('qty') ?? ($item ? $item->qty : null);
  $category_item_id = old('category_item_id');
  $option_name = old('option_name') ?? ($item && count($item->option) ? $item->option[0]->name : old('option_name'));
  $option_value = old('value') ?? [];
  $option_price = old('price') ?? [];
  $option_defalut = old('old_default') ?? [];
  $option_qty = old('option_qty') ?? [];
  $option_id = old('option_id') ?? [];
  $options = [];

  if($option_defalut && in_array(1,$option_defalut)){
    $is_read_only = "readonly";
  }

  if($thumbnailDropzoneImage){
      $path = "storage/temp/".$thumbnailDropzoneImage;
      $infoImage = getInfoImage($path);
  }

  if (count($option_value) && count($option_price)) {
     foreach ($option_value as $key => $value) {
         $price = isset($option_price[$key]) ? $option_price[$key] : null;
         $defalut = isset($option_defalut[$key]) ? $option_defalut[$key] : 0;
         $qty = isset($option_qty[$key]) ? $option_qty[$key] : 0;
         $id = isset($option_id[$key]) ?  $option_id[$key] : null;
         $options[] = ["value" => $value,"price" => $price,"default" => $defalut,"qty" => $qty,'id' => $id];
     }
    //  dd($options);
  } else {

    if($item && count($option)){
        $is_read_only = "readonly";
        $options = $option;
    }
  }

// dd($options,$infoImage);
  $categories = [];
 $menu = App\Models\MenuItem::where('status',1)->pluck('name', 'id');
  if(!$menu_id){
    $menu_id = $item ?  $item->menu_item_id : old('menu_item_id');
    $category_item_id = $item ?  $item->category_item_id : old('category_item_id');
    $is_cate = $item ?  true : false;
  }else{
    $is_cate = true;
  }

  if ($is_cate) {
    $categories = App\Models\CategoryItem::where('status',1)->pluck('name', 'id');
  }
@endphp

<!-- Master page  -->
@extends('backend.layouts.master')
@section('extraStyle')
    <link rel="stylesheet" href="{{asset('portal/css/dropzone.css')}}">
    <link href="{{asset('portal/css/cropper.css')}}" rel="stylesheet"/>

    <style>
    input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

    input[type="number"] {
        -moz-appearance: textfield;
    }
    </style>

{{--    <link rel="stylesheet" type="text/css" href="/backend/app-assets/vendors/css/ui/prism.min.css">--}}
{{--    <link rel="stylesheet" type="text/css" href="/backend/app-assets/css/plugins/file-uploaders/dropzone.css">--}}
@endsection
<!-- Page title -->
@section('pageTitle') Item @endsection
<!-- Page name -->
@section('pageName') Item @endsection
<!-- End block -->
@php
    $listStatus = array(
        '0' => 'Deactive',
        '1' => 'Active'
    )
@endphp
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Item') }}
            <small>@if($item) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li><a href="{{URL::route('pos.item.index')}}"> {{ __('Product') }} </a></li>
            <li class="active">@if($item) Update @else Add @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($item) {{URL::Route('pos.item.update', $item->id)}} @else {{URL::Route('pos.item.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="option" value="" id="option">
                        <div class="box-body">
                            @csrf
                            @if($item)  {{ method_field('PATCH') }} @endif
                            <!-- Organization -->
                            @auth
                                {!! AppHelper::selectOrg($errors, $item->org_id ?? 0,$role_ref_id) !!}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required placeholder="name" value="{{$name}}">
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group has-feedback">--}}
{{--                                        <label for="code">SKU<span class="text-danger">*</span></label>--}}
{{--                                            <input type="text" class="form-control" id="code" name="code" placeholder="code" value="@if($item){{$item->code}}@else{{old('code')}}@endif" required="">--}}
{{--                                            <span class="fa fa-info form-control-feedback"></span>--}}
{{--                                        <span class="text-danger">{{ $errors->first('code') }}</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="price"> {{ __('Default Price') }} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="default_price" name="default_price" required placeholder="Default price" maxlength="7" value="{{$default_price}}" {{$is_read_only}}>
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('default_price') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="status"> {{ __('Status') }} </label>
                                        {!! Form::select('status', \AppHelper::STATUS,$status, ['class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label>{{ __('Thumbnail (Aspec Ratio 1:1)') }}  <span class="text-danger">*</span></label>
                                        @component('common.single_dropzone',[
                                            'id' => "thumbnailDropzone",
                                            'object' => $item ? $item : null,
                                            'width' => 100,
                                            'height' => 100,
                                            'collection_key' => 'thumbnail'])'
                                        @endcomponent

                                    </fieldset>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="menu_item_id"> {{ __('Menu') }} <span class="text-danger">*</span></label>
                                        {!! Form::select('menu_item_id', $menu, $menu_id, ['placeholder' => 'Pick a menu...','class' => 'form-control select2', 'required' => 'true']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('menu_item_id') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="category_item_id"> {{ __('Category') }} </label>
                                        {!! Form::select('category_item_id', $categories,$category_item_id, ['placeholder' => 'Pick a category...','class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('category_item_id') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Description') }} </label>
                                        <textarea name="description" rows="4" class="form-control" maxlength="500" >{{$description}}</textarea>
                                        <span class="fa fa-location-arrow form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>
                            </div>
                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <h3 class="panel-title"> {{ __('Option') }} </h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="col-md-4">
                                                    <div class="form-group has-feedback">
                                                        <label for="option_name"> {{ __('Name') }} </label>
                                                        <input type="text" name="option_name" class="form-control" id="optionName" value="{{$option_name}}">
                                                            @if (session()->has('option_name'))
                                                                <span class="text-danger">{{session()->get('option_name')}}</span>
                                                            @endif
                                                    </div>
                                                </div>
                                                <table id="myTable" class=" table order-list">
                                                    <thead>
                                                    <tr>
                                                        <td width="10%"> {{ __('Select Default') }} </td>
                                                        <td> {{ __('Value') }} </td>
                                                        <td> {{ __('Price') }} </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($options))
                                                        @foreach($options as $key => $op)
                                                            <tr>
                                                                <td class="">
                                                                    <input type="radio" name="default[]"  class="form-control choose-option" value="{{$op['default']}}" {{$op['default'] ? 'checked' : null}}/><br>
                                                                </td>
                                                                <td class="">
                                                                    <input type="text" name="value[]"  class="form-control" value="{{$op['value']}}"/>
                                                                    <input type="hidden" name="old_default[]" value="{{$op['default']}}">
                                                                    @if (empty($op['value']))
                                                                        <span class="text-danger"> {{ __('This field is required.') }} </span>
                                                                    @endif

                                                                </td>
                                                                <td class="">
                                                                    <input type="number" name="price[]"  class="form-control change-price" value="{{$op['price']}}"/>
                                                                    <input type="hidden" name="option_id[]" value="{{isset($op['id']) ? $op['id'] : null }}">
                                                                    <input type="hidden" name="option_qty[]" value="{{isset($op['qty']) ? $op['qty'] : 0 }}">
                                                                    @if (empty($op['price']))
                                                                        <span class="text-danger"> {{ __('This field is required.') }} </span>
                                                                    @endif
                                                                </td>

                                                                <td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>

                                                                <td class=""><a class="deleteRow"></a></td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="">
                                                                    <input type="radio" name="default[]"  class="form-control choose-option" value="0"/>

                                                                </td>
                                                                <td class="">
                                                                    <input type="text" name="value[]"  class="form-control"/>
                                                                    <input type="hidden" name="old_default[]" value="0">
                                                                </td>
                                                                <td class="">
                                                                    <input type="number" name="price[]"  class="form-control change-price"/>
                                                                </td>
                                                                <td class=""><a class="deleteRow"></a></td>
                                                            </tr>
                                                    @endif
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4" style="text-align: left;">
                                                            <input type="button" class="btn btn-lg btn-block " id="addrow" value="Add Row" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('pos.item.index')}}" class="btn btn-default">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-info pull-right text-white" id="submit-test"><i class="fa @if($item) fa-refresh @else fa-plus-circle @endif"></i> @if($item) Update @else {{ __('Add') }} @endif</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script src="{{asset('portal/js/dropzone.js')}}"></script>
    <script src="{{asset('portal/js/cropper.js')}}"></script>
    <script src="/backend/app-assets/custom/js/dropzone.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            @if($infoImage)
                $data = {
                        name : "{!! $infoImage['file_name'] !!}",
                        size : {!! $infoImage['size'] !!},
                        imageURL  : "{!! $infoImage['url'] !!}"
                    }
                $(".dz-message").hide();
                previewThumbailFromUrl($data);
            @endif

            @if (session()->has('default'))
                    toastr.error("{{session()->get('default')}}");
            @endif

            Academic.attendanceInit();
            $('select[name="menu_item_id"]').on('change', function () {
                Generic.loaderStart();
                let menu_item_id =  $(this).val();
                getCategory(menu_item_id, function (res={}) {
                    // console.log(res);
                    if (Object.keys(res).length){
                        $('select[name="category_item_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a category...', data: res});
                    }
                    else{
                        // clear subject list dropdown
                        $('select[name="category_item_id"]').empty().select2({placeholder: 'Pick a category...'});
                        // toastr.warning('This section have no subject!');
                    }
                    Generic.loaderStop();
                });

            });

            // Disable scroll when focused on a number input.
            $('html').on('focus', 'input[type=number]', function(e) {
                    $(this).on('wheel', function(e) {
                        e.preventDefault();
                    });
                });

            // Restore scroll on number inputs.
            $('html').on('blur', 'input[type=number]', function(e) {
                $(this).off('wheel');
            });

            // Disable up and down keys.
            $('html').on('keydown', 'input[type=number]', function(e) {
                if ( e.which == 38 || e.which == 40 )
                    e.preventDefault();
            });


        });
        function getCategory(menu_item_id, cb) {
            let getUrl = '/get-category-by-menu/' + menu_item_id;
            if (menu_item_id) {
                axios.get(getUrl)
                    .then((response) => {
                        cb(response.data);

                    }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);
                    cb();

                });
            }
            else {
                cb();
            }
        }
        $(document).ready(function () {
            var counter = 0;

            $("#addrow").on("click", function () {
                var newRow = $("<tr>");
                var cols = "";
                cols += '<td><input type="radio" name="default[]"  class="form-control choose-option" value="0"/></td>';
                cols += '<td><input type="text"   class="form-control" name="value[]"/><input type="hidden" name="old_default[]" value="0"></td>';
                cols += '<td><input type="number" class="form-control change-price" name="price[]"/></td>';

                cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
                newRow.append(cols);
                $("table.order-list").append(newRow);
                counter++;
                initIcheckBox();
            });

            $("table.order-list").on("click", ".ibtnDel", function (event) {
                $(this).closest("tr").remove();
                var rowCount = $('#myTable >tbody >tr').length;
                if(rowCount == 0){
                    $("#optionName").val("");
                    $("#default_price").val("");
                    $('#myTable #addrow').trigger('click');
                }
                counter -= 1
            });

            $('#myTable').delegate(".choose-option", "ifClicked", function(event){
                    if(this.value == 1){
                        $("#default_price").removeAttr("readonly");
                        var cual= this;
                        setTimeout(function(){ $(cual).iCheck('uncheck');}, 1);
                    }
           });

            $('#myTable').delegate(".choose-option", "ifChecked", function(event){

                   this.value = 1;
                   let parents = $(this).closest('tr');
                   parents.children().eq(1).children().eq(1).val(1);
                   $("#default_price").val(parents.children().eq(2).children().eq(0).val());
                   $("#qty").val(parents.children().eq(3).children().eq(0).val());
                   $("#default_price").attr("readonly","readonly");
                   $("#qty").attr("readonly","readonly");

            });

            $('#myTable').delegate(".choose-option", "ifUnchecked", function(event){
                   let parents = $(this).closest('tr');
                   parents.children().eq(1).children().eq(1).val(0);
                   this.value = 0;
            });

            $('#myTable').delegate('.change-price','keyup change', function(e) {
                    let parents = $(this).closest('tr');
                    let checked = parents.children().eq(0).children().eq(0).hasClass('checked');
                    if(checked){
                        $("#default_price").val(parents.children().eq(2).children().eq(0).val());
                    }
            });
        });

        function initIcheckBox(){
            $('input').not('.dont-style').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
        }

        function previewThumbailFromUrl(opts) {
                var myDropzone = Dropzone.forElement("#thumbnailDropzone");
                var mockFile = {
                    name: opts.name,
                    size: opts.size,
                    accepted: true,
                    kind: 'image',
                    upload: {
                        filename: opts.name,
                    },
                    dataURL: opts.imageURL,
                };
                myDropzone.files.push(mockFile);
                myDropzone.emit("addedfile", mockFile);
                myDropzone.createThumbnailFromUrl(
                    mockFile,
                    myDropzone.options.thumbnailWidth,
                    myDropzone.options.thumbnailHeight,
                    myDropzone.options.thumbnailMethod,
                    true,
                    function(thumbnail) {
                        myDropzone.emit('thumbnail', mockFile, thumbnail);
                        myDropzone.emit("complete", mockFile);
                    }
                );
            }

    </script>
@endsection
<!-- END PAGE JS-->
