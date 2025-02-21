@php

@endphp

<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Category @endsection
<!-- Page name -->
@section('pageName') Category @endsection
<!-- End block -->
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Category
            <small>@if($menu) Update @else Add New @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('pos.category.index')}}"><i class="fa fa-list-alt"></i>Category</a></li>
            <li class="active">@if($menu) Update @else Add @endif</li>
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
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="name" value="@if($menu){{$menu->name}}@else{{old('name')}}@endif">
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="description">Description</label>
                                        <textarea name="description" class="form-control" maxlength="500" >@if($menu){{ $menu->description }}@else{{ old('description') }} @endif</textarea>
                                        <span class="fa fa-location-arrow form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="status">Status</label>
                                        {!! Form::select('status', \AppHelper::STATUS, $menu ? $menu->status : 1  , ['class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('pos.menu.index')}}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($menu) fa-refresh @else fa-plus-circle @endif"></i> @if($menu) Update @else Add @endif</button>

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
            Academic.attendanceInit();
        });
    </script>
@endsection
<!-- END PAGE JS-->
