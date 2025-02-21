<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Library @endsection
<!-- End block -->
@section('extraStyle')
    <link rel="stylesheet" href="{{asset('portal/css/dropzone.css')}}">
 @endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Online Library') }}
            <small>@if($onlineLibrary) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li><a href="{{URL::route('library.onlinelibrary.index')}}"> {{ __('Online Library') }} </a></li>
            <li class="active">@if($onlineLibrary) Update @else {{ __('Add') }} @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($onlineLibrary) {{URL::Route('library.onlinelibrary.update', $onlineLibrary->id)}} @else {{URL::Route('library.onlinelibrary.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            @if($onlineLibrary)  {{ method_field('PATCH') }} @endif
                            <!-- Organization -->
                            @auth
                            {!! AppHelper::selectOrg($errors, $library->org_id ?? 0,$role_ref_id) !!}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="title"> {{ __('Title') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" placeholder="title" value="@if($onlineLibrary){{$onlineLibrary->title}}@else{{old('title')}}@endif" required="" minlength="1" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="author"> {{ __('Author') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="author" placeholder="author name" value="@if($onlineLibrary){{$onlineLibrary->author}}@else{{old('author')}}@endif" minlength="1" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('author') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="privacy"> {{ __('Privacy') }} <span class="text-danger">*</span>
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="select privacy type"></i>
                                        </label>
                                        {!! Form::select('privacy', AppHelper::PRIVACY, $privacy , ['class' => 'form-control select2', 'required' => 'true', 'id'=>'privacy']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('privacy') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Description') }} </label>
                                        <textarea name="description" class="form-control"> @if($onlineLibrary){{ old('description')??$onlineLibrary->description }}@else{{ old('description') }} @endif </textarea>
                                        <span class="fa fa-info-circle form-control-feedback"></span>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 classes">
                                    <div class="form-group has-feedback">
                                        <label for="class_id"> {{ __('Class Name') }}
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Set class that student belongs to"></i>
                                            {{-- <span class="text-danger">*</span> --}}
                                        </label>
                                        {!! Form::select('class_id', $classes, $iclass , ['id'=>'exam_add_class_change','placeholder' => 'Pick a class...','class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('class_id') }}</span>

                                    </div>
                                </div>
                                <div class="col-md-4 section">
                                    <div class="form-group has-feedback">
                                        <label for="section_id"> {{ __('Section') }}
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Set section that student belongs to"></i>
                                            {{-- <span class="text-danger">*</span> --}}
                                        </label>
                                        {!! Form::select('section_id',$sections,$section, ['placeholder' => 'Pick a section...','class' => 'form-control select2','id' => 'section_id']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('section_id') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('library.onlinelibrary.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa fa-plus-circle"></i> {{ __('Save') }} </button>
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
    <script type="text/javascript">
        $(document).ready(function () {
            window.section_list_url = '{{URL::Route("academic.section")}}';
            Academic.studentInit();

            if( $("#privacy option:selected").val() == 1){
                $(".classes").hide();
                $(".section").hide();
            }
            else{
                $(".classes").show();
                $(".section").show();
            }

            $('#privacy').on('change', function() {
                if(this.value == 1){
                    $(".classes").hide();
                    $(".section").hide();
                }
                else{
                    $(".classes").show();
                    $(".section").show();
                }
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
