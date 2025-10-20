@extends('backend.layouts.master')

@section('pageTitle')
    {{ __('Sub-Wholesale') }}
@endsection

@section('bodyCssClass')
@endsection

@section('extraStyle')
    <style>
        /* fieldset .form-group {
                                                margin-bottom: 0px;
                                            }

                                            fieldset .iradio .error,
                                            fieldset .icheck .error {
                                                display: none !important;
                                            }

                                            @media (max-width: 600px) {
                                                .display-flex {
                                                    display: inline-flex;
                                                }
                                            } */

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

        /* Add to existing extraStyle section */
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

        fieldset>#open-camera-btn {
            overflow: hidden;
            cursor: pointer;
            width: 100%;
            height: 340px;
            background-color: #f5f5f5;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        fieldset>#open-camera-btn:hover {
            background-color: #e0e0e0;
        }

        fieldset>#open-camera-btn>#btn-upload-photo {
            min-width: 100px;
            min-height: 100px;
            background-color: #ddd;
            font-size: 25px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        fieldset>#open-camera-btn>#btn-upload-photo:hover {
            transform: scale(1.05);
        }

        fieldset>#photo-preview {
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
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
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
            max-height: 93.3vh;
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
        }

        /* Camera Overlay */
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

        /* Camera Controls */
        .camera-controls {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 20px;
            /* Space between buttons */
        }

        /* Switch Camera Button */
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

        /* Capture Button */
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

        /* Close Camera Button */
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
    </style>
@endsection

@php
    use App\Http\Helpers\AppHelper;
@endphp

@section('pageContent')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('displaysub.index') }}"> {{ __('Sub-Wholesale') }} </a></li>
            <li class="active">
                @if ($report)
                    {{ __('Update') }}
                @else
                    {{ __('Import data') }}
                @endif
            </li>
        </ol>
    </section>

    <section class="content">
        <form novalidate id="entryForm"
            action="{{ URL::route('displaysub.import.save') }}"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>
                            {{ __('Report Data') }}
                            <small class="toch">
                                @if ($report)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Import data') }}
                                @endif
                            </small>
                        </h1>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('displaysub.index') }}"
                                class="btn btn-default">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i
                                    class="fa @if ($report) fa-refresh @else fa-plus-circle @endif"></i>
                                @if ($report)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Add') }}
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
                    {{-- @if ($report)
                        @method('PUT')
                    @endif --}}
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="file"> {{ __('Select a File') }} <span class="text-danger">*</span></label>
                                <input type="file" name="file" id="file" class="form-control"
                                    accept=".xlsx, .xls, .csv">
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('file') }}</span>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="employee_id"> {{ __('Select Employee') }} <span
                                        class="text-danger">*</span></label>
                                <select name="employee" class="form-control select2" id="employee_id" required>
                                    <option value="">{{__('Select Employee')}}</option>
                                    @foreach ($selectUsers as $u)
                                        <option value="{{ $u->id }}"
                                            {{ old('employee_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->staff_id_card }} - @if (auth()->user()->user_lang == 'en')
                                                {{ $u->family_name_latin . ' ' . $u->name_latin }}
                                            @else
                                                {{ $u->family_name . ' ' . $u->name }}
                                            @endif
                                            ({{ $u->role->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('employee') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

@section('extraScript')
    {{-- <script src="{{asset('js/leaflet.js')}}"></script> --}}
    <script>
        $(document).on('change', '#file', function() {
            $('#fileChosen').removeClass('d-none');
        });

        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'block';
        }

        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }



    </script>
@endsection
