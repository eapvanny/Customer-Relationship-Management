<!-- Master page  -->
@extends('backend.layouts.master')
@section('extraStyle')
    <link rel="stylesheet" href="{{asset('portal/css/dropzone.css')}}">
    <link href="{{asset('portal/css/cropper.css')}}" rel="stylesheet"/>
@endsection

<!-- Page title -->
@section('pageTitle') Meun @endsection
<!-- End block -->
@php
    $uri = request()->query();
    $params = http_build_query($uri);
    $url_category = route('pos.category.index',["menu_id" => $menu->id]);
    if(!empty($params)){
        $url_category = $url_category.'&'.$params;
    }

@endphp
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')

    <!-- Section header -->
    <section class="content-header">
        <div class="btn-group">
            {{-- @if($product->created_by==auth()->user()->id) --}}
            <a href="{{URL::route('pos.menu.edit',$menu->id)}}" class="btn-ta btn-sm-ta"><i class="fa fa-edit"></i> Edit</a>
            {{-- @endif --}}
        </div>

        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li><a href="{{URL::route('pos.menu.index')}}"> {{ __('Menu') }} </a></li>
            <li class="active">View</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content main-contents">
            <div class="row">
                <div class="col-md-12">
                    <div id="printableArea">
                        <div class="row">
                            @php
                                $tab = 'profile';
                                if(request()->has('tab'))
                                {
                                 $tab = request()->get('tab');
                                }
                            @endphp
                            <div class="col-sm-12">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs" id="nav_tab">
                                        <li class="{{$tab=='profile' ? 'active' : ''}}"><a href="#profile" data-toggle="tab"> {{ __('Detail') }} </a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane {{$tab=='profile' ? 'active' : ''}}" id="profile">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Organization:') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                        <p>: {{getOrganizationName($menu->org_id)}}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Name') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                        <p>: {{$menu->name}}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Status') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                    <p for="">: {{($menu->status==1)?"Active":"Deactive"}}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Description.') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                    <p for="">: {{$menu->description}}</p>
                                                </div>
                                            </div>
                                            @if ($is_create)
                                                <input type="hidden" name="" id="org_id" value="{{$menu->org_id}}">
                                                <input type="hidden" name="" id="menu_id" value="{{$menu->id}}">
                                                <div class="row">
                                                    <div class="box-header">
                                                        <div class="col-md-3 pull-right">
                                                            <div class="form-group box-tools pull-right">
                                                                <a class="btn btn-info text-white add-category" data-url="{{route('pos.category.store')}}"><i class="fa fa-plus-circle"></i> {{ __('Add Category') }} </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($is_view)
                                                <!-- /.box-header -->
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th width="5%">#</th>
                                                                    <th width="10%"> {{ __('Organization') }} </th>
                                                                    <th width="10%"> {{ __('Menu') }} </th>
                                                                    <th width="10%"> {{ __('Name') }} </th>
                                                                    <th width="10%"> {{ __('Status') }} </th>
                                                                    <th class="notexport" width="15%"> {{ __('Action') }} </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                                <tfoot>
                                                                <tr>
                                                                        <th width="5%">#</th>
                                                                        <th width="10%"> {{ __('Organization') }} </th>
                                                                        <th width="10%"> {{ __('Menu') }} </th>
                                                                        <th width="10%"> {{ __('Name') }} </th>
                                                                        <th width="10%"> {{ __('Status') }} </th>
                                                                        <th class="notexport" width="15%"> {{ __('Action') }} </th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                <!-- /.box-body -->
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <form id="myAction" method="POST">
                    @csrf
                    <input name="_method" type="hidden" value="DELETE">
                </form>
        </div>
    </section>
    <!-- /.content -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel"> {{ __('New Category') }} </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="myCategory" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="name" value="">
                </div>
                <div class="form-group">
                    <fieldset class="form-group">
                        <label> {{ __('Thumnail (Aspec Ratio 1:1)') }}  <span class="text-danger">*</span></label>
                        @component('common.single_dropzone',[
                            'id' => "thumbnailDropzone",
                            'object' => isset($item) ? $item : null,
                            'width' => 100,
                            'height' => 100,
                            'collection_key' => 'thumbnail'])@endcomponent

                    </fieldset>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal"> {{ __('Close') }} </button>
              <a href="#" class="btn btn-primary btn-save-category" id="btn_save_category"> {{ __('Save') }} </a>
            </div>
          </div>
        </div>
      </div>
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
<script src="{{asset('portal/js/dropzone.js')}}"></script>
<script src="{{asset('portal/js/cropper.js')}}"></script>
<script src="/backend/app-assets/custom/js/dropzone.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            window.postUrl = '{{URL::Route("pos.category.status", 0)}}';
            window.cateUrl = '{!! $url_category !!}';
            Generic.initCommonPageJS();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }

            });

            @if($is_view)
                var t = $('#datatabble').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax:{
                        url: window.cateUrl,
                    },
                    columns:[
                        {
                            data: 'id',
                            name: 'id',
                        },
                        {
                            data: 'org_id',
                            name: 'org_id',
                            visible : false
                        },
                        {
                            data: 'menu_item_id',
                            name: 'menu_item_id',
                            visible : false
                        },
                        {
                            data: 'name',
                            name: 'name'
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
                            off: "Deactive"
                        });
                    }
                });

                $('#datatabble').delegate('.delete','click', function(e){
                    let action = $(this).attr('href')+"?menu_item_id="+$("#menu_id").val();
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

            @endif

            @if ($is_create)
                $(".add-category").on("click",function(e){
                    e.preventDefault();
                    $('#exampleModal').modal('show');
                    $("#exampleModalLabel").text("New Category");
                    $("#name").val('');
                    $("#btn_save_category").text("Save");
                    $(".dz-preview").remove();
                    $(".dz-message").show();
                    $("#myCategory").attr("action",$(this).data('url'));
                });
            @endif

            $(".btn-save-category").on("click",function(e){
                e.preventDefault();
                let Url = $("#myCategory").attr('action');;
                let method = null;
                let body = null;
                if($(this).text() == "Save"){
                    method = "POST";
                }else if($(this).text() == "Update"){
                    method = "PUT";
                }
                body = {
                    org_id : $("#org_id").val(),
                    menu_item_id : $("#menu_id").val(),
                    name : $("#name").val(),
                    thumbnailDropzoneImage : $("#thumbnailDropzoneImage").val()
                }
                StoreOrUpdateCategory(Url,method,body);
            });

            $('#exampleModal').on('hidden.bs.modal', function (e) {
                    $("#name").val('');
                    $("#btn_save_category").text("Save");
                    $(".dz-preview").remove();
                    $(".dz-message").show();
            })

            $('#datatabble').delegate(".btn-edit-category", "click", function(e){
                e.preventDefault();
                $("#exampleModalLabel").text("Edit Category");
                $("#myCategory").attr("action",$(this).attr('href'));
                $("#name").val($(this).data('name'));
                $("#btn_save_category").text("Update");
                if ($(this).data('file_name') && $(this).data('file_name') && $(this).data('file_name')) {
                    $data = {
                        name : $(this).data('file_name'),
                        size : $(this).data('size'),
                        imageURL  : $(this).data('url')
                    }
                    $(".dz-message").hide();
                    previewThumbailFromUrl($data);
                }else{
                    $(".dz-preview").remove();
                    $(".dz-message").show();
                }
                $('#exampleModal').modal('show');
            });

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

            function StoreOrUpdateCategory(url,method,body){
                $.ajax({
                    url:url,
                    method:method, //First change type to method here
                    data:body,
                    success:function(response) {
                        if(response.success){
                            toastr.success(response.message);
                            if($('#datatabble').length){
                                $('#datatabble').DataTable().ajax.reload();
                            }
                            $('#exampleModal').modal('hide');
                            $(".dz-preview").remove();
                            $(".dz-message").show();
                            $("#name").val("");
                            $("#btn_save_category").text("Save");
                            $("#thumbnailDropzoneImage").val("");
                        }else{
                            $.each(response.errors, function(key,val) {
                                toastr.error(val);
                            });
                        }
                    },
                    error:function(){
                        alert("error");
                    }

                });
            }

        });
    </script>
@endsection
<!-- END PAGE JS-->
