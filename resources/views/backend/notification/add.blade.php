<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Notification @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->
@section('extraStyle')

@endsection

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Notification') }}
            <small> {{ __('Send') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{URL::route('notification.index')}}"><i class="fa icon-parent"></i> {{ __('Notification') }} </a></li>
            <li class="active">Send</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="{{route('notification.send')}}" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                        @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="title"> {{ __('Title') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="title" value="{{old('title')}}" required="" maxlength="150">
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="url"> {{ __('Link') }} </label>
                                            <input type="text" class="form-control" id="url" name="url" placeholder="Link" value="{{old('url')}}" maxlength="150">
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('url') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="user_type"> {{ __('User Target') }}
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Select user target"></i>
                                        </label>
                                        <select class="form-control select2" name="user_type">
                                            <option value=""> {{ __('All') }} </option>
                                            @foreach ($roles as $role)
                                                @if (old('status') === $role->id )
                                                    <option value="{{$role->id}}"selected>{{$role->name}}</option>
                                                @else
                                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                                @endif
                                            @endforeach

                                        </select>
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('user_type') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="body"> {{ __('Message') }} <span class="text-danger">*</span></label>
                                        <textarea name="body" class="form-control"  maxlength="500">{{old('body')}}</textarea>
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('body') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('notification.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"> {{ __('Send') }} </button>

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
            Generic.initCommonPageJS();
        });
    </script>
@endsection
<!-- END PAGE JS-->
