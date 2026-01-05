@extends('backend.layouts.master')

@section('pageTitle')
    Depo
@endsection

@section('bodyCssClass')
@endsection

@section('extraStyle')
    <style>
       
    </style>
@endsection

@php
    use App\Http\Helpers\AppHelper;
@endphp

@section('pageContent')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('depo.index') }}"> {{ __('Depo') }} </a></li>
            <li class="active">
                @if ($depo)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>

    <section class="content">
        <form novalidate id="entryForm"
            action="@if ($depo) {{ URL::Route('depo.update', $depo->id) }} @else {{ URL::Route('depo.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>
                            @if ($depo)
                                {{ __('Update Depo') }}
                            @else
                                {{ __('Add New Depo') }}
                            @endif
                            {{-- {{ __('Customer Data') }} --}}
                        </h4>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('depo.index') }}" class="btn btn-default">{{__('Cancel')}}</a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white" onclick="disableButtons(this)"><i
                                    class="fa @if ($depo) fa-refresh @else fa-plus-circle @endif"></i>
                                @if ($depo)
                                    {{__('Update')}}
                                @else
                                    {{__('Add')}}
                                @endif
                            </button>
                            @if(!$depo)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white" onclick="disableButtons(this)">
                                <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrap-outter-box">
                <input id="org_detail" type="hidden" name="org_detail" value="">
                <div class="box-body">
                    @csrf
                    @if ($depo)
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="area">{{ __('Area') }} <span class="text-danger">*</span></label>
                                <select name="area" id="area" class="form-control select2" required>
                                    <option value="">{{ __('Select Area') }}</option>
                                    @foreach ($areas as $area => $subItems)
                                        <optgroup label="{{ $area }}">
                                            @foreach ($subItems as $area_id => $subItem)
                                                <option value="{{ $area_id }}"
                                                    @if (old('area', $depo->area_id ?? '') == $area_id) selected @endif>
                                                    {{ $subItem }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('area') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="name">{{ __('Depo Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $depo->name ?? '') }}"
                                    required>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ __($errors->first('name')) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

@section('extraScript')
     <script type="text/javascript">
        $(document).ready(function() {
            Generic.initDeleteDialog();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            
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
