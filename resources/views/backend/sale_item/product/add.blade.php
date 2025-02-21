@php

@endphp

<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') {{__('Product')}} @endsection
<!-- Page name -->
@section('pageName') {{__('Product')}} @endsection
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
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>
            <li><a href="{{URL::route('saleitem.product.index')}}"> {{ __('Product') }} </a></li>
            <li class="active">@if($product) {{ __('Update') }} @else {{ __('Add') }} @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <form novalidate="novalidate" id="entryForm" action=" @if($product) {{URL::Route('saleitem.product.update', $product->id)}} @else {{URL::Route('saleitem.product.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
            @csrf
            @if($product)
                @method('PUT')
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>
                            {{ __('Product') }}
                            <small>@if($product)  {{ __('Update') }} @else {{ __('Add New') }} @endif</small>
                        </h1>
                        <div class="action-btn-top none_fly_action_btn">
                            <a href="{{URL::route('saleitem.product.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white">
                                <i class="fa @if($product) fa-refresh @else fa-plus-circle @endif"></i>
                                @if($product) {{ __('Update') }} @else {{ __('Add') }} @endif
                            </button>
                            @if(!$product)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white">
                                    <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-group has-feedback">
                                    <label for="status"> {{ __('Status') }} </label>
                                    <select class="form-control select2" name="status">
                                        <option value="1" @if(old('status')) @if(old('status')=='1'){{ 'selected' }} @endif @elseif(isset($product->status)) @if($product->status=='1'){{ 'selected' }}@endif @endif>{{ __('Active') }}</option>
                                        <option value="0" @if(old('status')) @if(old('status')=='0'){{ 'selected' }} @endif @elseif(isset($product->status)) @if($product->status=='0'){{ 'selected' }}@endif @endif>{{ __('Deactive') }}</option>
                                    </select>
                                    <span class="form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('status') }}</span>
                                </div>
                            </div>
                            <x-backend.select-search class="col-md-6 col-xl-3" name="category" value="{{ (old('category') ? old('category') : optional($product)->category) ?? request()->query('category'); }}" :options="$categories" label="Fee Type" placeholder="Pick a category" id="category" :required="true"/>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-group has-feedback">
                                    <label for="code"> {{ __('Code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="code" name="code" placeholder="code" value="@if($product){{$product->code}}@else{{old('code')}}@endif" required="">
                                        <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('code') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-group has-feedback">
                                    <label for="name"> {{ __('Name') }} </label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="name" value="@if($product){{$product->name}}@else{{old('name')}}@endif">
                                        <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-group has-feedback">
                                    <label for="name_latin"> {{ __('Name In Latin') }} </label>
                                        <input type="text" class="form-control" id="name_latin" name="name_latin" placeholder="name in latin" value="@if($product){{$product->name_latin}}@else{{old('name_latin')}}@endif">
                                        <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name_latin') }}</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group has-feedback">
                                    <label for="description"> {{ __('Description') }} </label>
                                    <textarea name="description" class="form-control" maxlength="500" >@if($product){{ $product->description }}@else{{ old('description') }} @endif</textarea>
                                    <span class="fa fa-location-arrow form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /.box-body -->

        </form>

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

            $(".submitClick").on('click', function(){
                event.preventDefault();
                if ($(this).hasClass('submitAndContinue')) {
                    $(".boxfooter").append('<input type="hidden" name="saveandcontinue" value="1" />');
                }else {
                    $("input[name='saveandcontinue']").each(function(){
                        $(this).remove();
                    });
                }
                $("#entryForm").submit();
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
