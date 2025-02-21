<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') onlinelibrary Profile @endsection
<!-- End block -->

<!-- Page body extra css -->
@section('extraStyle')
    <style>
        @media print {
            @page {
                size:  A4 landscape;
                margin: 5px;
            }
        }
        .width-100{
            width: 99%;
        }
        .overflow {
            max-width: 38em;
            margin: 0 0 2em 0;

            /**
            * Required properties to achieve text-overflow
            */
            white-space: nowrap;
            overflow: hidden;
        }
        .ellipsis { text-overflow: ellipsis;}

    </style>
@endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <div class="btn-group">
            {{--<a href="#"  class="btn-ta btn-sm-ta btn-print btnPrintInformation"><i class="fa fa-print"></i> Print</a>--}}
        </div>
        <div class="btn-group">
            <a href="{{URL::route('library.onlinelibrary.edit',$online->id)}}" class="btn-ta btn-sm-ta"><i class="fa fa-edit"></i> {{ __('Edit') }} </a>
        </div>

        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li class="active"> {{ __('View') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content main-contents">
        <div class="form" @if($employee < 1) hidden @endif>
            <form action="{{ url('library-upload') }}"
                    class="dropzone"
                    id="my-awesome-dropzone"
                    enctype="multipart/form-data">
                    @csrf
                <input type="hidden" name="onlineLiId" id="getOnlineLiId" value="{{$online->id}}">
                <input type="file" name="file"  style="display: none;">
            </form>
        </div>
        <div class="table-responsive">
            <table id="listDataTableWithSearch" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                <thead>
                    <tr>
                        <th width="20%"> {{ __('Thumbnail') }} </th>
                        <th width="55%"> {{ __('File Name') }} </th>
                        <th width="25%"> {{ __('Size') }} </th>
                    </tr>
                </thead>
                <tbody>
                    @if($mediaItems->count())
                        @foreach( $mediaItems as $med)

                            <?php

                                if(($med->mime_type) == 'video/mp4'){
                                    $thumbnail = asset('images/vthumb.jpg');
                                }elseif(($med->mime_type) == 'application/pdf'){
                                    $thumbnail = asset('images/pdfthumb.png');
                                }else{
                                    $thumbnail = $med->getUrl();
                                }
                            ?>
                            <tr>
                                <td >
                                    <button class="button" onClick="window.open('{{$med->getUrl()}}');">
                                        <img class="img-responsive center" style="height: 100px; width: 100px;" src="{{$thumbnail}}" alt="">
                                   </button>

                                 </td>
                                <td ><p class="overflow ellipsis">{{ $med->file_name }}</p></td>
                                <td >{{ $med->size }}</td>

                            </tr>
                        @endforeach

                    @endif
                </tbody>
            </table>
        </div>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
<script src="{{asset('portal/js/dropzone.js')}}"></script>
<script>
    Dropzone.autoDiscover = false;
</script>
    <script type="text/javascript">
    window.uploadLimit = 512;
        $(document).ready(function () {
            var $ = window.$; // use the global jQuery instance
            if($("#my-awesome-dropzone").length > 0) {
                var token = $('input[name=csrf-token]').val();
                var onlineId = $('input[name=onlineLiId]').val();
                // A quick way setup
                var myDropzone = new Dropzone("#my-awesome-dropzone", {
                    // Setup chunking
                    chunking: true,
                    method: "POST",
                    maxFilesize: 4000,
                    chunkSize: 5000000,
                    timeout: 0,
                    addRemoveLinks: true,
                    // If true, the individual chunks of a file are being uploaded simultaneously.
                    parallelChunkUploads: true,

                    init: function(){
                        var thisDropzone = this;
                        this.on("removedfile", function(file){
                           $.ajax({
                                type: "POST",
                                url: "/library-deletefile",
                                data: {name: file.name,onlineId:onlineId},
                                success: function (data) {
                                    successmessage = 'File Deleted!';
                                    alert(successmessage);
                                },
                                error: function(data) {
                                    successmessage = 'Something Went Wrong!';
                                    alert(successmessage);
                                },
                            });
                        })
                    }
                });

                // Append token to the request - required for web routes
                myDropzone.on('sending', function (file, xhr, formData) {
                    formData.append("_token", token);
                })
                @foreach($mediaItems as $media)

                    <?php

                        if(($media->mime_type) == 'video/mp4'){
                            $thumbnail = asset('images/vthumb.jpg');
                        }elseif(($media->mime_type) == 'application/pdf'){
                            $thumbnail = asset('images/pdfthumb.png');
                        }else{
                            $thumbnail = $media->getUrl();
                        }
                    ?>

                    var mockFile = { name: "{{$media->file_name}}", size: "{{$media->size}}" };

                    myDropzone.emit("addedfile", mockFile);

                    myDropzone.emit("thumbnail", mockFile, "{{$thumbnail}}");
                    // Make sure that there is no progress bar, etc...
                    myDropzone.emit("complete", mockFile)

                @endforeach

            }
        });
    </script>
@endsection
<!-- END PAGE JS-->
