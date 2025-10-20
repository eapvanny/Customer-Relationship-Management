@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    {{ __('Region Management') }}
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        /* Existing Styles with iOS Fixes */
        .checkbox,
        .radio {
            display: inline-block;
        }

        .checkbox {
            margin-left: 10px;
        }

        legend {
            margin: 0;
            width: unset;
            font-weight: 700;
            font-size: 14px;
            color: #0059a1;
            display: block;
            padding-inline-start: 2px;
            padding-inline-end: 2px;
            border-width: initial;
            border-style: none;
            border-color: initial;
            border-image: initial;
        }

        .text-danger1 {
            display: block;
            text-align: center !important;
            color: #DC3545;
        }

        fieldset {
            padding: 1em 0.625em 1em;
            border: 1px solid #9a9a9a;
            margin: 2px 2px;
            padding: .35em .625em .75em;
            margin-top: 4px;
        }

        #map {
            height: 300px;
            width: 100%;
            margin-top: 10px;
        }

        .leaflet-touch .leaflet-control-attribution,
        .leaflet-touch .leaflet-control-layers,
        .leaflet-touch .leaflet-bar {
            display: none;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .loading-content img {
            width: 50px;
            height: 50px;
            opacity: 0.4;
        }

        fieldset {
            padding: 1em 0.625em 1em;
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 0.35em 0.625em 0.75em;
            border-radius: 10px;
        }

        fieldset .form-group {
            margin-bottom: 0px;
        }

        .list-radio .form-group.has-error .help-block {
            position: absolute;
            width: 300px;
            bottom: -18px;
        }

        .list-time-schedule .error.help-block {
            position: absolute;
            width: 300px;
            bottom: -18px;
            color: #dd4b39;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .display-flex {
                display: inline-flex;
            }
        }

        @media (max-width: 768px) {
            .display-flex {
                display: inline-flex;
            }
        }

        @media (max-width: 414px) {
            .wrap-outter-header-title h4 {
                font-size: 16px !important;
            }

            .btn-default .btn-info .btn-success {
                font-size: 7px !important;
            }
        }

        @media (max-width: 375px) {
            .wrap-outter-header-title h4 {
                font-size: 16px !important;
            }

            .btn-default .btn-info .btn-success {
                font-size: 7px !important;
            }

        }

        fieldset>#open-outlet-camera-btn {
            overflow: hidden;
            cursor: pointer;
            width: 100%;
            height: 340px;
            background-color: #f5f5f5;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        fieldset>#open-outlet-camera-btn:hover {
            background-color: #e0e0e0;
        }

        fieldset>#open-outlet-camera-btn>#btn-upload-outlet-photo {
            min-width: 100px;
            min-height: 100px;
            background-color: #ddd;
            font-size: 25px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        fieldset>#open-outlet-camera-btn>#btn-upload-outlet-photo:hover {
            transform: scale(1.05);
        }

        fieldset>#outlet-photo-preview {
            height: 238px;
            width: 238px;
            position: absolute;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .camera-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: flex !important;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            -webkit-overflow-scrolling: touch;
        }

        .camera-modal.d-none {
            display: none !important;
        }

        .camera-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 90vw;
            max-height: 90vh;
            margin: auto;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            background: #000;
            -webkit-transform: translateZ(0);
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .overlay-top,
        .overlay-bottom,
        .overlay-left,
        .overlay-right {
            position: absolute;
            border: 1px solid #fff;
            background: rgba(54, 54, 54, 0.5);
        }

        .overlay-top {
            top: 0;
            left: 0;
            width: 100%;
            height: 15%;
        }

        .overlay-bottom {
            bottom: 0;
            left: 0;
            width: 100%;
            height: 15%;
        }

        .overlay-left {
            top: 0;
            left: 0;
            width: 15%;
            height: 100%;
        }

        .overlay-right {
            top: 0;
            right: 0;
            width: 15%;
            height: 100%;
        }

        .focus-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80px;
            height: 80px;
            border: 3px solid #fff;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.7;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
            }

            100% {
                opacity: 0.7;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .camera-controls {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: env(safe-area-inset-bottom);
        }

        .switch-camera-btn {
            background: linear-gradient(145deg, #ffffff, #e0e0e0);
            color: #333;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .switch-camera-btn:hover {
            background: linear-gradient(145deg, #e0e0e0, #ffffff);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        .switch-camera-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .capture-btn {
            background: linear-gradient(145deg, #ff4d4d, #e63939);
            color: white;
            border: 4px solid white;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .capture-btn:hover {
            background: linear-gradient(145deg, #e63939, #ff4d4d);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        .capture-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .close-camera-btn {
            background: linear-gradient(145deg, #ff4d4d, #e63939);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .close-camera-btn:hover {
            background: linear-gradient(145deg, #e63939, #ff4d4d);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        .close-camera-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* iOS-specific fixes */
        fieldset#photo-upload {
            touch-action: manipulation;
            user-select: none;
        }

        #open-camera-btn {
            touch-action: manipulation;
            cursor: pointer;
        }

        #btn-upload-photo {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('region.index') }}"> {{ __('Region') }} </a></li>
            <li class="active">
                @if ($region)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>
    <!-- ./Section header -->

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <form id="entryForm"
            action="@if ($region) {{ URL::Route('region.update', $region->id) }} @else {{ URL::Route('region.store') }} @endif"
            method="post" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>{{ __('Region') }}</h1>
                        <div class="action-btn-top none_fly_action_btn">
                            <a href="{{ URL::route('region.index') }}" class="btn btn-default"> {{ __('Cancel') }}</a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white">
                                <i class="fa @if ($region) fa-refresh @else fa-check-circle @endif"></i>
                                @if ($region)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Save') }}
                                @endif
                            </button>
                            {{-- @if (!$region)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white">
                                    <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>

            @csrf
            @if ($region)
                @method('PUT')
            @endif
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="region_name"> {{ __('Region Name') }} <span class="text-danger">*</span></label>
                                    <input id="region_name" name="region_name" class="form-control"
                                        placeholder="{{ __('Region Name') }}" required
                                        value="@if($region){{ old('region_name') ?? $region->region_name }}@else{{ old('region_name') }}@endif">

                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('region_name') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="sd_name"> {{ __("SD's Name") }} <span class="text-danger">*</span></label>
                                    <input id="sd_name" name="sd_name" class="form-control"
                                        placeholder=" {{ __("SD's Name") }}" required
                                        value="@if($region){{ old('sd_name') ?? $region->sd_name }}@else{{ old('sd_name') }}@endif">

                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('sd_name') }}</span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="sm_name"> {{ __("SM's Name") }} <span class="text-danger">*</span></label>
                                    <input id="sm_name" name="sm_name" class="form-control"
                                        placeholder=" {{ __("SD's Name") }}" required
                                        value="@if($region){{ old('sm_name') ?? $region->sm_name }}@else{{ old('sm_name') }}@endif">
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('sm_name') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="se_code">{{ __('SE Code') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="se_code" class="form-control"
                                        placeholder="{{ __('SE Code') }}"
                                        value="{{ old('se_code', $region->se_code ?? '') }}" required>
                                    <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('se_code') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="rg_manager_kh">{{ __('Region Manager (KH)') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="rg_manager_kh" class="form-control"
                                        placeholder="{{ __('Region Manager (KH)') }}"
                                        value="{{ old('rg_manager_kh', $region->rg_manager_kh ?? '') }}" required>
                                    <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('rg_manager_kh') }}</span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="rg_manager_en"> {{ __('Region Manager (EN)') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="rg_manager_en"
                                        placeholder="{{ __('Region Manager (EN)') }}" id="rg_manager_en"
                                        value="@if($region){{ $region->rg_manager_en }}@else{{ old('rg_manager_en') }}@endif" required>
                                    <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('rg_manger_kh') }}</span>

                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="province"> {{ __('Province') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="province"
                                        placeholder="{{ __('Province') }}" id="province"
                                        value="@if($region){{ $region->province }}@else{{ old('province') }}@endif" required>
                                    <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('rg_manger_kh') }}</span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-xl-6">
                                 <div class="form-group has-feedback">
                                    <label for="active_status"> {{ __('Status') }}</label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" @if ($region)
                                            {{ $region->active_status == 1 ? 'checked' : '' }}
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
            </div>
        </form>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });




        });
    </script>
@endsection
<!-- END PAGE JS-->
