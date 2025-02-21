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
            {{ __('Exchange Rate') }}
            <small>@if($exchange_rate) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li><a href="{{URL::route('pos.exchange-rate.index')}}"> {{ __('Exchange Rate') }} </a></li>
            <li class="active">@if($exchange_rate) Update @else {{ __('Add') }} @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($exchange_rate) {{URL::Route('pos.exchange-rate.update', $exchange_rate->id)}} @else {{URL::Route('pos.exchange-rate.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            @if($exchange_rate)  {{ method_field('PATCH') }} @endif
                            <!-- Organization -->
                            @auth
                                {!! AppHelper::selectOrg($errors, $exchange_rate->org_id ?? 0,$role_ref_id) !!}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="currency"> {{ __('From Currency') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::select('from_currency', getCurrency(), $exchange_rate ? $exchange_rate->from_currency : null  , ['class' => 'form-control select2','required'=>'true','placeholder'=>'Pick a currency...']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('from_currency') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="currency"> {{ __('To Currency') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::select('to_currency', getCurrency(), $exchange_rate ? $exchange_rate->to_currency : null  , ['class' => 'form-control select2','required'=>'true','placeholder'=>'Pick a currency...']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('to_currency') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Rate') }} <span class="text-danger">*</span></label>
                                        {!! Form::number('rate', $exchange_rate ? $exchange_rate->rate : null, ['min' => '0.000001', 'class' => 'form-control','placeholder'=>'0.00024','required'=>true]) !!}
                                        <span class="text-danger">{{ $errors->first('rate') }}</span>
                                     </div>
                                </div>

                        </div>

                   </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                    <a href="{{URL::route('pos.exchange-rate.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                    <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($exchange_rate) fa-refresh @else fa-plus-circle @endif"></i> @if($exchange_rate) Update @else {{ __('Add') }} @endif</button>

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
Academic.attendanceInit();
});
</script>
@endsection
<!-- END PAGE JS-->
