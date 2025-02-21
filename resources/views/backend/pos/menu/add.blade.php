@php

@endphp

<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Menu @endsection
<!-- Page name -->
@section('pageName') Menu @endsection
<!-- End block -->

@section('extraStyle')
    <link rel="stylesheet" href="{{asset('portal/css/dropzone.css')}}">
    <link href="{{asset('portal/css/cropper.css')}}" rel="stylesheet"/>
    <style>
        .dropzone .dz-preview .dz-image img{
            width: 100%;
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Menu') }}
            <small>@if($menu) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li><a href="{{URL::route('pos.menu.index')}}"> {{ __('Menu') }} </a></li>
            <li class="active">@if($menu) Update @else {{ __('Add') }} @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($menu) {{URL::Route('pos.menu.update', $menu->id)}} @else {{URL::Route('pos.menu.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            @if($menu)  {{ method_field('PATCH') }} @endif
                            <!-- Organization -->
                            @auth
                                {!! AppHelper::selectOrg($errors, $menu->org_id ?? 0,$role_ref_id) !!}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="name" value="@if($menu){{$menu->name}}@else{{old('name')}}@endif">
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="status"> {{ __('Status') }} </label>
                                        {!! Form::select('status', \AppHelper::STATUS, $menu ? $menu->status : 1  , ['class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Description') }} </label>
                                        <textarea name="description" class="form-control" maxlength="500" rows="8" >@if($menu){{ $menu->description }}@else{{ old('description') }} @endif</textarea>
                                        <span class="fa fa-location-arrow form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="logo"> {{ __('Icon') }} <br><span class="text-danger">[files: jpeg, jpg, png max:50 X 50 ]</span></label>
                                        <div id="icon" class="dropzone">
                                            <div class="dz-message" data-dz-message><span> <i class="fa fa-upload" aria-hidden="true"></i> {{ __('Drop image here') }} Drop image here</span></div>
                                            <div class="dz-default dz-message"></div>
                                        </div>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('icon') }}</span>
                                </div>

                                <input type="hidden" id="temp_path" name="icon_folder" value="icon_{{time()}}">
                                <input type="hidden" id="is_delete" name="is_delete" value="{{$menu['icon']?0:1}}">
                                <input type="hidden" id="icon_name"  name="icon_name" >
                                @if($menu && isset($menu->icon))
                                    <input type="hidden" name="oldIcon" value="{{$menu->icon}}">   
                                @endif

                            </div>

                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('pos.menu.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($menu) fa-refresh @else fa-plus-circle @endif"></i> @if($menu) Update @else {{ __('Add') }} @endif</button>

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


    <script type="text/javascript">
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            Dropzone.autoDiscover = false;

            let isDelete = 1;
            $('button[type=submit]').on('click',function(e) {
                e.preventDefault();
                // $('#is_delete').val('0');
                isDelete = 0;
                $('#entryForm').submit();
            });

            $(window).on('beforeunload', function(){
                    if(isDelete==1){
                        $.ajax({
                            type: "POST",
                            url: "{{route('face.delete.temp')}}",
                            data: {'temp_path' : $("#temp_path").val() },
                            dataType: 'json',
                            async: false,
                            success: function (response) {
                                if( response.status === true ) {
                                    alert('File Deleted!');
                                }
                                else alert('Something Went Wrong!');
                            }
                        });
                    }

                });
        }
        );

        Dropzone.options.icon =
            {
                url: "{{route('face.upload')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                maxFiles: 1,
                resizeWidth: 500,
                maxFilesize: 1024,
                acceptedFiles: ".jpeg,.jpg,.png",
                addRemoveLinks: true,
                timeout: 50000,
                success: function(file, response)
                {
                    $('#icon_name').val(response);

                },
                error: function(file, response)
                {
                    return false;
                },
                transformFile: function(file, done) {
                    // Create Dropzone reference for use in confirm button click handler
                    var myDropZone = this;
                    // Create the image editor overlay
                    var editor = document.createElement('div');
                    editor.style.position = 'fixed';
                    editor.style.left = 0;
                    editor.style.right = 0;
                    editor.style.top = 0;
                    editor.style.bottom = 0;
                    editor.style.zIndex = 9999;
                    editor.style.backgroundColor = '#000';
                    document.body.appendChild(editor);
                    // Create confirm button at the top left of the viewport
                    var buttonConfirm = document.createElement('button');
                    buttonConfirm.style.position = 'absolute';
                    buttonConfirm.style.left = '10px';
                    buttonConfirm.style.top = '10px';
                    buttonConfirm.style.zIndex = 9999;
                    buttonConfirm.textContent = 'Confirm';
                    editor.appendChild(buttonConfirm);
                    buttonConfirm.addEventListener('click', function() {
                        // Get the canvas with image data from Cropper.js
                        var canvas = cropper.getCroppedCanvas({
                            width: {{getSchoolConfig('dropzone_crop_icon_menu_width')}},
                            height: {{getSchoolConfig('dropzone_crop_icon_menu_height')}},

                        });
                        // Turn the canvas into a Blob (file object without a name)
                        canvas.toBlob(function(blob) {
                            // Create a new Dropzone file thumbnail
                            myDropZone.createThumbnail(
                                blob,
                                myDropZone.options.thumbnailWidth,
                                myDropZone.options.thumbnailHeight,
                                myDropZone.options.thumbnailMethod,
                                false,
                                function(dataURL) {

                                    // Update the Dropzone file thumbnail
                                    myDropZone.emit('thumbnail', file, dataURL);
                                    // Return the file to Dropzone
                                    done(blob);
                                });
                        });
                        // Remove the editor from the view
                        document.body.removeChild(editor);
                    });
                    // Create an image node for Cropper.js
                    var image = new Image();
                    image.src = URL.createObjectURL(file);
                    editor.appendChild(image);

                    // Create Cropper.js
                    var cropper = new Cropper(image, {
                        aspectRatio: 1,
                        width:{{getSchoolConfig('dropzone_crop_icon_menu_width')}},
                        height:{{getSchoolConfig('dropzone_crop_icon_menu_height')}},
                        cropBoxResizable: false,
                    });
                },
                init: function () {
                    var imgName = "{{$menu['icon']}}";
                    var imgSize = "{{$menu['iconSize']}}";
                    var currentFile = null;
                    if (imgName){
                        var baseUrl = window.location.protocol + "//" + window.location.host;
                        var mockFile = { name: imgName, size: imgSize, type: 'image/jpeg' };
                        this.options.addedfile.call(this, mockFile);
                        this.options.thumbnail.call(this, mockFile, baseUrl+'/'+imgName);
                        mockFile.previewElement.classList.add('dz-success');
                        mockFile.previewElement.classList.add('dz-complete');
                        currentFile = mockFile;
                    }
                    this.on("addedfile", function(file){
                        if (currentFile) {
                            this.removeFile(currentFile);
                        }
                        currentFile = file;

                    });
                    this.on("maxfilesexceeded", function(file) {
                        this.removeAllFiles();
                        this.addFile(file);
                    });
                    this.on("complete", function (file) {
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            // alert('done');
                        }
                    });
                    this.on("sending", function(file, xhr, formData) {
                        formData.append("temp_path", $("#temp_path").val());
                        formData.append("width", {{getSchoolConfig('dropzone_crop_icon_menu_width')}});
                        formData.append("orgUpload", true);

                    });
                    this.on("removedfile", function (file) {
                        $('#is_delete').val(1);
                        $('#icon_name').val('');
                        $.post({
                            url: "{{route('face.delete.tempfile')}}",
                            data: {filename: file.name,temp_path:$("#temp_path").val(), _token: $('meta[name="csrf-token"]').attr('content')},
                            dataType: 'json',
                            success: function (data) {

                            }
                        });
                    });
                }
            };
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            Settings.instituteInit();
        });
    </script>
@endsection
<!-- END PAGE JS-->
