<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Book @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Book') }}
            <small>@if($book) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li><a href="{{URL::route('library.book.index')}}"> {{ __('Book') }} </a></li>
            <li class="active">@if($book) Update @else {{ __('Add') }} @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($book) {{URL::Route('library.book.update', $book->id)}} @else {{URL::Route('library.book.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off"> 
                        <div class="box-body">
                            @csrf
                            @if($book)  {{ method_field('PATCH') }} @endif  
                            <!-- Organization -->
                            @auth
                            {{-- @if(auth()->user()->newRole->role_id === AppHelper::USER_SUPER_ADMIN) --}}
                            {!! AppHelper::selectOrg($errors, $book->org_id ?? 0,$role_ref_id) !!}
                            {{-- @endif --}}
                            @endauth
                            <!-- End organization -->                                                   
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="code"> {{ __('Code/ISBN No.') }} <span class="text-danger">*</span></label>
                                        <input autofocus="" type="text" class="form-control" name="code" placeholder="ISBN No." value="@if($book){{$book->code}}@else{{old('code')}}@endif" required="" minlength="1" maxlength="20">
                                        <span class="fa fa-code form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('code') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" placeholder="name" value="@if($book){{$book->name}}@else{{old('name')}}@endif" required="" minlength="1" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="author"> {{ __('Author') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="author" placeholder="author name" value="@if($book){{$book->author}}@else{{old('author')}}@endif" required="" minlength="1" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('author') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="type"> {{ __('Type') }} <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="type" placeholder="Pick a type..." required>
                                            <option value=""> {{ __('Pick a type...') }} </option>
                                            <option value="Academic" @if(old('type')) @if(old('type')=='Academic'){{ 'selected' }} @endif @elseif(isset($book->type)) @if($book->type=='Academic'){{ 'selected' }}@endif @endif> {{ __('Academic') }} </option>
                                            <option value="Novel" @if(old('type')) @if(old('type')=='Novel'){{ 'selected' }} @endif @elseif(isset($book->type)) @if($book->type=='Novel'){{ 'selected' }}@endif @endif> {{ __('Novel') }} </option>
                                            <option value="Magazine" @if(old('type')) @if(old('type')=='Magazine'){{ 'selected' }} @endif @elseif(isset($book->type)) @if($book->type=='Magazine'){{ 'selected' }}@endif @endif> {{ __('Magazine') }} </option>
                                            <option value="Other" @if(old('type')) @if(old('type')=='Other'){{ 'selected' }} @endif @elseif(isset($book->type)) @if($book->type=='Other'){{ 'selected' }}@endif @endif> {{ __('Other') }} </option>
                                            
                                        </select>
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('type') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="i_classes_id"> {{ __('Class') }} <span class="text-danger">*</span></label>
                                        {!! Form::select('i_classes_id', $classes, $class , ['id' => 'book_add_edit_class_change', 'placeholder' => 'Pick a class...','class' => 'form-control select2','required']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('i_classes_id') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="qty"> {{ __('Quantity') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="qty" placeholder="40" value="@if($book){{$book->qty}}@else{{old('qty')}}@endif" required="" min="1">
                                        <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('qty') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="rack_no"> {{ __('Rack No.') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="rack_no" placeholder="r-40" value="@if($book){{$book->rack_no}}@else{{old('rack_no')}}@endif" required="" maxlength="10">
                                        <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('rack_no') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="row_no"> {{ __('Row No.') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="row_no" placeholder="5" value="@if($book){{$book->row_no}}@else{{old('row_no')}}@endif" required="" maxlength="10">
                                        <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('row_no') }}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="file_attachment"> {{ __('File Attachment') }} <br><span class="text-danger"> [files:jpeg,jpg, png, PDF max-size 2MB]</span></label>
                                    <input  type="file" multiple class="form-control" accept=".jpeg, .jpg, .png, .PDF" name="file_attachment[]" placeholder="Photo image">
                                    <span class="text-danger">{{ $errors->first('file_attachment*') }}</span>
                                    @if($book && $attachfile->count())
                                        @foreach($attachfile as $file_name)
                                            <div class="file_attach">
                                                <a target="_blank" href="{{asset("storage/book").'/'.$file_name->path}}">{{$file_name->original_name}}</a><a href="#" class="remove-attach"><i class="fa fa-fw fa-times-circle" style="color: red"></i></a>
                                                <input type="hidden" name="oldfile_attachment[]" value="{{$file_name->id}}">
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Description') }} </label>
                                        <textarea name="description" class="form-control">@if($book){{ $book->description }}@else{{ old('description') }} @endif</textarea>
                                        <span class="fa fa-info-circle form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('library.book.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($book) fa-refresh @else fa-plus-circle @endif"></i> @if($book) Update @else {{ __('Add') }} @endif</button>

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
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            window.section_list_url = '{{URL::Route("academic.section")}}';
            Academic.attendanceInit();

            $(".remove-attach").click(function(e){
                e.preventDefault();
                $(this).parent().remove();
            });

        });
    </script>
@endsection
<!-- END PAGE JS-->
