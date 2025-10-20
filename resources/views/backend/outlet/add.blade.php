@extends('backend.layouts.master')

@section('pageTitle')
    Outlet
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
            <li><a href="{{ URL::route('outlet.index') }}"> {{ __('Outlet Management') }} </a></li>
            <li class="active">
                @if ($outlet)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>

    <section class="content">
        <form novalidate id="entryForm"
            action="@if ($outlet) {{ URL::Route('outlet.update', $outlet->id) }} @else {{ URL::Route('outlet.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>
                            @if ($outlet)
                                {{ __('Update Outlet') }}
                            @else
                                {{ __('Add New Outlet') }}
                            @endif
                            {{-- {{ __('Customer Data') }} --}}
                        </h4>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('outlet.index') }}" class="btn btn-default">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i
                                    class="fa @if ($outlet) fa-refresh @else fa-plus-circle @endif"></i>
                                @if ($outlet)
                                    {{__('Update')}}
                                @else
                                    {{__('Add')}}
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrap-outter-box">
                <input id="org_detail" type="hidden" name="org_detail" value="">
                <div class="box-body">
                    @csrf
                    @if ($outlet)
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="region">{{ __('Region') }} <span class="text-danger">*</span></label>
                                {{-- <select name="region" id="region" class="form-control select2" required>
                                    <option value="">{{ __('Select Region') }}</option>
                                    @foreach ($regions as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($outlet)
                                                {{ $outlet->area_id == $item->id ? 'selected' : ''}}
                                            @else {{ (old('region')  == $item->id ? 'selected' : '') }}@endif>
                                            {{ $item->region_name }}
                                            @if(auth()->user()->user_lang == 'en')
                                            {{ $item->rg_manager_en }}
                                            @else
                                            {{ $item->rg_manager_kh }}
                                            @endif
                                            ( {{ $item->se_code }} )
                                        </option>
                                    @endforeach
                                </select> --}}

                                <select name="region" id="region" class="form-control select2" required>
                                    <option value="" selected>{{ __('Select Region') }}</option>
                                    @foreach($regions as $rgManager => $regionGroup)
                                        <optgroup label="{{ $regionGroup->first()->region_name }}
                                            (@if(auth()->user()->user_lang == 'en')
                                                {{ $regionGroup->first()->rg_manager_en }}
                                            @else
                                                {{ $regionGroup->first()->rg_manager_kh }}
                                            @endif)">

                                            @foreach($regionGroup as $region)
                                                <option value="{{ $region->id }}"
                                                    @if ($outlet && $outlet->area_id == $region->id)
                                                        selected
                                                    @endif
                                                    @if(old('region') == $region->id)
                                                        selected
                                                    @endif>
                                                    {{ $region->se_code }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('region') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="name">{{ __('Outlet Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Outlet Name') }}"
                                    value="{{ old('name', $outlet->name ?? '') }}"
                                    required>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ __($errors->first('name')) }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="active_status"> {{ __('Status') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" @if ($outlet)
                                        {{ $outlet->active_status == 1 ? 'checked' : '' }}
                                        @elseif (old('active_status'))
                                        {{ old('active_status') == 1 ? 'checked' : '' }}
                                        @else
                                        checked
                                    @endif value="1" name="active_status" id="active_status">
                                    <label class="form-check-label" for="active_status">
                                        {{ __("Active") }}
                                    </label>
                                </div>
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
