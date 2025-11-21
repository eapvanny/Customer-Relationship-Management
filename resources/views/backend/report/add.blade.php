@extends('backend.layouts.master')

@section('pageTitle')
    Reports
@endsection

@section('bodyCssClass')
@endsection

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
                font-size: 15px;
            }

            .btn-info {
                font-size: 10px !important;
            }

            .btn-default {
                font-size: 10px !important;
            }
        }

        @media (max-width: 390px) {
            .wrap-outter-header-title h4 {
                font-size: 15px;
            }

            .btn-info {
                font-size: 10px !important;
            }

            .btn-default {
                font-size: 10px !important;
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

@php
    use App\Http\Helpers\AppHelper;
@endphp

@section('pageContent')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('report.index') }}"> {{ __('Reports') }} </a></li>
            <li class="active">
                @if ($report)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>

    <section class="content">
        <form novalidate id="entryForm"
            action="@if ($report) {{ URL::Route('report.update', $report->id) }} @else {{ URL::Route('report.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>
                            @if ($report)
                                {{ __('Update Report') }}
                            @else
                                {{ __('Add New Report') }}
                            @endif
                            {{-- {{ __('Customer Data') }} --}}
                        </h4>
                        <div class="action-btn-top none_fly_action_btn">
                            <a href="{{ URL::route('report.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white"><i
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
                    @if ($report)
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
                                                        @if (old('area', $report->area_id ?? '') == $area_id) selected @endif>
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
                                <label for="outlet_id">{{ __("Depo's Name") }} <span class="text-danger">*</span></label>
                                <select name="outlet_id" class="form-control select2" id="outlet_id" required>
                                    <option value="">{{ __('Select area first') }}</option>
                                    @foreach ($depos as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('outlet_id', $report->outlet_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('outlet_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="customer_id">{{ __('Customer Name') }} <span
                                        class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control select2" id="customer_id" required>
                                    <option value="">{{ __('Select outlet first') }}</option>
                                    @foreach ($customers as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('customer_id', $report->customer_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="customer_type">{{ __('Customer Type') }}
                                    <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select customer type"></i>
                                </label>
                                <select name="customer_type" id="customer_type" class="form-control select2" disabled>
                                    <option value="">{{ __('Select customer first') }}</option>
                                    @foreach ($customerType as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('customer_type', $report->customer_type ?? '') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('customer_type') }}</span>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-xl-6 d-none">
                            <div class="form-group has-feedback">
                                <label for="date"> {{ __('Date') }}</label>
                                <input type="date" class="form-control" name="date"
                                    value="{{ isset($report) ? $report['date'] : old('date') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="250_ml"> {{ __('250ml') }} <span>{{ __('(Boxes)') }}</span></label>
                                <input type="number" class="form-control" name="250_ml"
                                    value="{{ isset($report) ? $report['250_ml'] : old('250_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="350_ml"> {{ __('350ml') }} <span>{{ __('(Boxes)') }}</span></label>
                                <input type="number" class="form-control" name="350_ml"
                                    value="{{ isset($report) ? $report['350_ml'] : old('350_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="600_ml"> {{ __('600ml') }} <span>{{ __('(Boxes)') }}</span></label>
                                <input type="number" class="form-control" name="600_ml"
                                    value="{{ isset($report) ? $report['600_ml'] : old('600_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="1500_ml"> {{ __('1500ml') }} <span>{{ __('(Boxes)') }}</span></label>
                                <input type="number" class="form-control" name="1500_ml"
                                    value="{{ isset($report) ? $report['1500_ml'] : old('1500_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-xl-12">
                            {{-- <div class="form-group has-feedback">
                                <label for="other"> {{ __('Other') }}</label>
                                <input type="number" class="form-control" name="other" placeholder="other"
                                    value="@if ($report) {{ $report->other }}@else{{ old('other') }} @endif">
                            </div> --}}
                            <div class="form-group has-feedback">
                                <label for="other"> {{ __('other') }}</label>
                                <textarea class="form-control" name="other" id="other" rows="2"
                                    placeholder="{{ __('Enter other here...') }}">{{ isset($report) ? $report['other'] : old('other') }}
                                </textarea>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <fieldset>
                                <legend>{{ __('Photo Attachment') }} <span class="text-danger">*
                                        {{ __('(For the amount of water that has been sold)') }}</span></legend>
                                <div class="row">
                                    <div class="form-group has-feedback">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group has-feedback position-relative">
                                                    <input type="file" id="outlet_photo" name="outlet_photo"
                                                        style="display: none" accept="image/*" required>
                                                    <button type="button"
                                                        class="btn btn-light text-secondary fs-5 position-absolute d-none m-2 end-0 z-1"
                                                        id="btn-remove-outlet-photo">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                    <fieldset id="outlet-photo-upload"
                                                        class="p-0 d-flex align-items-center justify-content-center z-0 position-relative">
                                                        <img class="rounded mx-auto d-block @if (!old('old_outlet_photo') && !isset($report->outlet_photo)) d-none @endif z-1"
                                                            id="outlet-photo-preview" name="old_outlet_photo"
                                                            src="@if (isset($report->outlet_photo)) {{ asset('storage/' . $report->outlet_photo) }}@endif"
                                                            alt="outlet-photo">
                                                        <input type="hidden" id="outlet-img-preview"
                                                            name="old_outlet_photo"
                                                            value="@if (isset($report->outlet_photo)) {{ $report->outlet_photo }} @endif">
                                                        <div class="d-flex align-items-center justify-content-center bg-transparent z-2 @if (!old('outlet-img-preview') && !isset($report->outlet_photo)) opacity-100 @else opacity-25 @endif"
                                                            id="open-outlet-camera-btn">
                                                            <button class="btn p-3 rounded-circle"
                                                                id="btn-upload-outlet-photo" type="button"
                                                                data-action="open-outlet-camera">
                                                                <i class="fa-solid fa-camera-retro"></i>
                                                            </button>
                                                        </div>
                                                        <label id="outlet-camera-label"
                                                            class="position-absolute bottom-0 text-center w-100 mb-2">
                                                            @if (isset($report->outlet_photo) || old('outlet-img-preview'))
                                                                {{ __('Delete the old photo before you can open the camera') }}
                                                            @else
                                                                {{ __('Click to open camera and capture photo') }}
                                                            @endif
                                                        </label>
                                                    </fieldset>
                                                    @error('outlet_photo')
                                                        <span class="text-danger1">{{ __($message) }}</span>
                                                    @enderror
                                                </div>
                                                <div id="outlet-camera-modal" class="camera-modal d-none">
                                                    <div class="camera-content">
                                                        <div class="video-container position-relative">
                                                            <video id="outlet-webcam" autoplay playsinline></video>
                                                            <div class="camera-overlay">
                                                                <div class="overlay-top"></div>
                                                                <div class="overlay-bottom"></div>
                                                                <div class="overlay-left"></div>
                                                                <div class="overlay-right"></div>
                                                                <div class="focus-circle"></div>
                                                            </div>
                                                        </div>
                                                        <div class="camera-controls">
                                                            <button id="switch-outlet-camera-btn"
                                                                class="btn switch-camera-btn" type="button">
                                                                <i class="fa-solid fa-camera-rotate"></i>
                                                            </button>
                                                            <button id="capture-outlet-btn" class="btn capture-btn"
                                                                type="button">
                                                                <i class="fa-solid fa-camera"></i>
                                                            </button>
                                                            <button id="close-outlet-camera-btn"
                                                                class="btn close-camera-btn" type="button">
                                                                <i class="fa-solid fa-times"></i>
                                                            </button>
                                                        </div>
                                                        <canvas id="outlet-canvas" class="d-none"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>{{ __('Photo Attachment') }}</legend>
                                <div class="row">
                                    <div class="form-group has-feedback">
                                        <div class="row">
                                            <div class="row-span-6 col-sm-12 col-md-12 col-lg-12 col-xl-6">
                                                <div class="form-group has-feedback position-relative">
                                                    <input type="file" id="photo" name="photo"
                                                        style="display: none" accept="image/*">
                                                    <button type="button"
                                                        class="btn btn-light text-secondary fs-5 position-absolute d-none m-2 end-0 z-1"
                                                        id="btn-remove-photo">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                    <fieldset id="photo-upload"
                                                        class="p-0 d-flex align-items-center justify-content-center z-0 position-relative">
                                                        <img class="rounded mx-auto d-block @if (!old('oldphoto') && !isset($report->photo)) d-none @endif z-1"
                                                            id="photo-preview" name="oldphoto"
                                                            src="@if (isset($report->photo)) {{ asset('storage/' . $report->photo) }} @else {{ old('oldphoto') }} @endif"
                                                            alt="photo">
                                                        <input type="hidden" id="img-preview" name="oldphoto"
                                                            value="@if (isset($report->photo)) {{ $report->photo }} @endif">
                                                        <div class="d-flex align-items-center justify-content-center bg-transparent z-2 @if (!old('img-preview') && !isset($report->photo)) opacity-100 @else opacity-25 @endif"
                                                            id="open-camera-btn">
                                                            <button class="btn p-3 rounded-circle" id="btn-upload-photo"
                                                                type="button" data-action="open-camera">
                                                                <i class="fa-solid fa-camera-retro"></i>
                                                            </button>
                                                        </div>
                                                        <label id="camera-label"
                                                            class="position-absolute bottom-0 text-center w-100 mb-2">
                                                            @if (isset($report->photo) || old('img-preview'))
                                                                {{ __('Delete the old photo before you can open the camera') }}
                                                            @else
                                                                {{ __('Click to open camera and capture photo') }}
                                                            @endif
                                                        </label>
                                                    </fieldset>
                                                </div>
                                                <div id="camera-modal" class="camera-modal d-none">
                                                    <div class="camera-content">
                                                        <div class="video-container position-relative">
                                                            <video id="webcam" autoplay playsinline></video>
                                                            <div class="camera-overlay">
                                                                <div class="overlay-top"></div>
                                                                <div class="overlay-bottom"></div>
                                                                <div class="overlay-left"></div>
                                                                <div class="overlay-right"></div>
                                                                <div class="focus-circle"></div>
                                                            </div>
                                                        </div>
                                                        <div class="camera-controls">
                                                            <button id="switch-camera-btn" class="btn switch-camera-btn"
                                                                type="button">
                                                                <i class="fa-solid fa-camera-rotate"></i>
                                                            </button>
                                                            <button id="capture-btn" class="btn capture-btn"
                                                                type="button">
                                                                <i class="fa-solid fa-camera"></i>
                                                            </button>
                                                            <button id="close-camera-btn" class="btn close-camera-btn"
                                                                type="button">
                                                                <i class="fa-solid fa-times"></i>
                                                            </button>
                                                        </div>
                                                        <canvas id="canvas" class="d-none"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-12 col-xl-6 col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="posm"> {{ __('POSM 1') }}
                                                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                                                    data-placement="bottom" title="Select POSM"></i>
                                                            </label>
                                                            {!! Form::select(
                                                                'posm',
                                                                [
                                                                    AppHelper::UMBRELLA => __(AppHelper::MATERIAL[AppHelper::UMBRELLA]),
                                                                    AppHelper::TUMBLER => __(AppHelper::MATERIAL[AppHelper::TUMBLER]),
                                                                    AppHelper::PARASOL => __(AppHelper::MATERIAL[AppHelper::PARASOL]),
                                                                    AppHelper::JACKET => __(AppHelper::MATERIAL[AppHelper::JACKET]),
                                                                    AppHelper::BOTTLE_HOLDER => __(AppHelper::MATERIAL[AppHelper::BOTTLE_HOLDER]),
                                                                    AppHelper::ICE_BOX_200L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_200L]),
                                                                    AppHelper::CAP_BLUE => __(AppHelper::MATERIAL[AppHelper::CAP_BLUE]),
                                                                    AppHelper::HAT => __(AppHelper::MATERIAL[AppHelper::HAT]),
                                                                    AppHelper::GLASS_CUP => __(AppHelper::MATERIAL[AppHelper::GLASS_CUP]),
                                                                    AppHelper::ICE_BOX_27L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_27L]),
                                                                    AppHelper::ICE_BOX_45L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_45L]),
                                                                    AppHelper::T_SHIRT_RUNNING => __(AppHelper::MATERIAL[AppHelper::T_SHIRT_RUNNING]),
                                                                    AppHelper::LUNCH_BOX => __(AppHelper::MATERIAL[AppHelper::LUNCH_BOX]),
                                                                    AppHelper::LSK_FAN_16_DSF_9163 => __(AppHelper::MATERIAL[AppHelper::LSK_FAN_16_DSF_9163]),
                                                                    AppHelper::PAPER_CUP_250ML => __(AppHelper::MATERIAL[AppHelper::PAPER_CUP_250ML]),
                                                                    AppHelper::TISSUE_BOX => __(AppHelper::MATERIAL[AppHelper::TISSUE_BOX]),
                                                                ],
                                                                old('posm', optional($report)->posm),
                                                                [
                                                                    'placeholder' => __('Select material type'),
                                                                    'id' => 'posm',
                                                                    'name' => 'posm',
                                                                    'class' => 'form-control select2',
                                                                ],
                                                            ) !!}
                                                            <span class="form-control-feedback"></span>
                                                            <span class="text-danger">{{ $errors->first('posm') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="qty"> {{ __('Quantity') }} </label>
                                                            <input type="number" class="form-control" name="qty"
                                                                value="{{ old('qty', $report->qty ?? '') }}">
                                                            <span class="fa fa-info form-control-feedback"></span>
                                                            <span class="text-danger">{{ $errors->first('qty') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="posm2"> {{ __('POSM 2') }}
                                                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                                                    data-placement="bottom" title="Select POSM"></i>
                                                            </label>
                                                            {!! Form::select(
                                                                'posm2',
                                                                [
                                                                    AppHelper::UMBRELLA => __(AppHelper::MATERIAL[AppHelper::UMBRELLA]),
                                                                    AppHelper::TUMBLER => __(AppHelper::MATERIAL[AppHelper::TUMBLER]),
                                                                    AppHelper::PARASOL => __(AppHelper::MATERIAL[AppHelper::PARASOL]),
                                                                    AppHelper::JACKET => __(AppHelper::MATERIAL[AppHelper::JACKET]),
                                                                    AppHelper::BOTTLE_HOLDER => __(AppHelper::MATERIAL[AppHelper::BOTTLE_HOLDER]),
                                                                    AppHelper::ICE_BOX_200L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_200L]),
                                                                    AppHelper::CAP_BLUE => __(AppHelper::MATERIAL[AppHelper::CAP_BLUE]),
                                                                    AppHelper::HAT => __(AppHelper::MATERIAL[AppHelper::HAT]),
                                                                    AppHelper::GLASS_CUP => __(AppHelper::MATERIAL[AppHelper::GLASS_CUP]),
                                                                    AppHelper::ICE_BOX_27L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_27L]),
                                                                    AppHelper::ICE_BOX_45L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_45L]),
                                                                    AppHelper::T_SHIRT_RUNNING => __(AppHelper::MATERIAL[AppHelper::T_SHIRT_RUNNING]),
                                                                    AppHelper::LUNCH_BOX => __(AppHelper::MATERIAL[AppHelper::LUNCH_BOX]),
                                                                    AppHelper::LSK_FAN_16_DSF_9163 => __(AppHelper::MATERIAL[AppHelper::LSK_FAN_16_DSF_9163]),
                                                                    AppHelper::PAPER_CUP_250ML => __(AppHelper::MATERIAL[AppHelper::PAPER_CUP_250ML]),
                                                                    AppHelper::TISSUE_BOX => __(AppHelper::MATERIAL[AppHelper::TISSUE_BOX]),
                                                                ],
                                                                old('posm2', optional($report)->posm2),
                                                                [
                                                                    'placeholder' => __('Select material type'),
                                                                    'id' => 'posm2',
                                                                    'name' => 'posm2',
                                                                    'class' => 'form-control select2',
                                                                ],
                                                            ) !!}
                                                            <span class="form-control-feedback"></span>
                                                            <span class="text-danger">{{ $errors->first('posm2') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="qty2"> {{ __('Quantity') }} </label>
                                                            <input type="number" class="form-control" name="qty2"
                                                                value="{{ old('qty2', $report->qty2 ?? '') }}">
                                                            <span class="fa fa-info form-control-feedback"></span>
                                                            <span class="text-danger">{{ $errors->first('qty2') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="posm3"> {{ __('POSM 3') }}
                                                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                                                    data-placement="bottom" title="Select POSM"></i>
                                                            </label>
                                                            {!! Form::select(
                                                                'posm3',
                                                                [
                                                                    AppHelper::UMBRELLA => __(AppHelper::MATERIAL[AppHelper::UMBRELLA]),
                                                                    AppHelper::TUMBLER => __(AppHelper::MATERIAL[AppHelper::TUMBLER]),
                                                                    AppHelper::PARASOL => __(AppHelper::MATERIAL[AppHelper::PARASOL]),
                                                                    AppHelper::JACKET => __(AppHelper::MATERIAL[AppHelper::JACKET]),
                                                                    AppHelper::BOTTLE_HOLDER => __(AppHelper::MATERIAL[AppHelper::BOTTLE_HOLDER]),
                                                                    AppHelper::ICE_BOX_200L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_200L]),
                                                                    AppHelper::CAP_BLUE => __(AppHelper::MATERIAL[AppHelper::CAP_BLUE]),
                                                                    AppHelper::HAT => __(AppHelper::MATERIAL[AppHelper::HAT]),
                                                                    AppHelper::GLASS_CUP => __(AppHelper::MATERIAL[AppHelper::GLASS_CUP]),
                                                                    AppHelper::ICE_BOX_27L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_27L]),
                                                                    AppHelper::ICE_BOX_45L => __(AppHelper::MATERIAL[AppHelper::ICE_BOX_45L]),
                                                                    AppHelper::T_SHIRT_RUNNING => __(AppHelper::MATERIAL[AppHelper::T_SHIRT_RUNNING]),
                                                                    AppHelper::LUNCH_BOX => __(AppHelper::MATERIAL[AppHelper::LUNCH_BOX]),
                                                                    AppHelper::LSK_FAN_16_DSF_9163 => __(AppHelper::MATERIAL[AppHelper::LSK_FAN_16_DSF_9163]),
                                                                    AppHelper::PAPER_CUP_250ML => __(AppHelper::MATERIAL[AppHelper::PAPER_CUP_250ML]),
                                                                    AppHelper::TISSUE_BOX => __(AppHelper::MATERIAL[AppHelper::TISSUE_BOX]),
                                                                ],
                                                                old('posm3', optional($report)->posm3),
                                                                [
                                                                    'placeholder' => __('Select material type'),
                                                                    'id' => 'posm3',
                                                                    'name' => 'posm3',
                                                                    'class' => 'form-control select2',
                                                                ],
                                                            ) !!}
                                                            <span class="form-control-feedback"></span>
                                                            <span class="text-danger">{{ $errors->first('posm') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                                        <div class="form-group has-feedback">
                                                            <label for="qty3"> {{ __('Quantity') }} </label>
                                                            <input type="number" class="form-control" name="qty3"
                                                                value="{{ old('qty3', $report->qty3 ?? '') }}">
                                                            <span class="fa fa-info form-control-feedback"></span>
                                                            <span class="text-danger">{{ $errors->first('qty3') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <!-- Location Fields and Map -->
                        <div class="col-lg-12 col-md-12 col-xl-12">
                            <fieldset>
                                <legend>{{ __('Location') }}</legend>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="latitude">{{ __('Latitude') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="latitude" id="latitude"
                                                value="{{ isset($report) ? $report->latitude : old('latitude') }}"
                                                readonly required>
                                            <span class="fa fa-info form-control-feedback"></span>
                                            <span class="text-danger">{{ $errors->first('latitude') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="longitude">{{ __('Longitude') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="longitude" id="longitude"
                                                value="{{ isset($report) ? $report->longitude : old('longitude') }}"
                                                readonly required>
                                            <span class="fa fa-info form-control-feedback"></span>
                                            <span class="text-danger">{{ $errors->first('longitude') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="city">{{ __('Address') }}<span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="city" id="city" cols="30" rows="1" readonly required>{{ isset($report) ? $report->city : old('city') }}</textarea>
                                            <span class="fa fa-info form-control-feedback"></span>
                                            <span class="text-danger">{{ $errors->first('city') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="country">{{ __('Country') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="{{ isset($report) ? $report->country : old('country') }}" readonly
                                                required>
                                            <span class="fa fa-info form-control-feedback"></span>
                                            <span class="text-danger">{{ $errors->first('country') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-xl-12 mt-3">
                                        <button type="button" class="btn btn-primary" id="getLocationBtn">
                                            <i class="fa-solid fa-location-dot"></i> {{ __('Get Location') }}
                                        </button>
                                        <div id="map" style="height: 400px; margin-top: 15px;"></div>
                                    </div>

                                    <!-- Loading Overlay -->
                                    <div class="loading-overlay" id="loadingOverlay">
                                        <div class="loading-content">
                                            <img src="{{ asset('images/loading-waiting.gif') }}" alt="Loading...">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="has_driver" value="{{ request('has_driver') }}">
            <input type="hidden" name="driver_id" value="{{ request('driver_id') }}">
        </form>
    </section>
@endsection

@section('extraScript')
    <script>
        let map;
        let marker;

        // Initialize map function
        function initMap(lat = 0, lng = 0) {
            if (map) {
                map.remove();
            }

            map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(map);

            marker = L.marker([lat, lng]).addTo(map);
        }

        // Function to update map position
        function updateMap(lat, lng) {
            if (!map) {
                initMap(lat, lng);
            } else {
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
            }
        }

        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'block';
        }

        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        document.getElementById('getLocationBtn').addEventListener('click', async function() {
            if (navigator.geolocation) {
                showLoading();

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;

                            try {
                                const response = await fetch(
                                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`
                                );
                                const data = await response.json();

                                const country = data.address.country || 'Unknown';
                                const province = data.address.state || 'Unknown';
                                const comune = data.address.comune || 'Unknown';
                                const district = data.address.district || 'Unknown';
                                const village = data.address.village || 'Unknown';
                                const khan = data.address.city || 'Unknown';
                                const town = data.address.town;

                                const city = [khan, village, comune, district, town, province]
                                    .filter(location => location !== 'Unknown')
                                    .join(', ') || 'Unknown';

                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lon;
                                document.getElementById('city').value = city;
                                document.getElementById('country').value = country;

                                updateMap(lat, lon);

                                console.log(`Country: ${country}, Province: ${province}, City: ${city}`);
                            } catch (error) {
                                alert('Failed to fetch location details. Please try again.');
                                console.error(error);
                            } finally {
                                hideLoading();
                            }
                        },
                        (error) => {
                            hideLoading();
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    alert('User denied the request for Geolocation.');
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    alert('Location information is unavailable.');
                                    break;
                                case error.TIMEOUT:
                                    alert('The request to get user location timed out.');
                                    break;
                                case error.UNKNOWN_ERROR:
                                    alert('An unknown error occurred.');
                                    break;
                            }
                            console.error(error);
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                );
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        });

        @if ($report && $report->latitude && $report->longitude)
            window.onload = function() {
                initMap({{ $report->latitude }}, {{ $report->longitude }});
            }
        @else
            window.onload = function() {
                initMap(0, 0);
            }
        @endif

        $(document).ready(function() {
            let video = document.getElementById('webcam');
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let imgPreview = $('#photo-preview');
            let imgInput = $('#img-preview');
            let cameraModal = $('#camera-modal');
            let cameraLabel = $('#camera-label');
            let currentFacingMode = 'user';

            // Function to update camera label based on photo presence
            function updateCameraLabel() {
                if (imgPreview.hasClass('d-none')) {
                    cameraLabel.text('{{ __('Click to open camera and capture photo') }}');
                } else {
                    cameraLabel.text('{{ __('Delete the old photo before you can open the camera') }}');
                }
            }

            // Initialize label on page load
            updateCameraLabel();

            @if ($report && $report->photo)
                $('#btn-upload-photo').addClass('d-none');
                $('#btn-remove-photo').removeClass('d-none');
            @endif

            function startCamera(facingMode) {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    video.setAttribute('playsinline', 'true');
                    video.setAttribute('autoplay', 'true');

                    navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: facingMode,
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            }
                        })
                        .then(function(stream) {
                            video.srcObject = stream;
                            video.play();
                        })
                        .catch(function(error) {
                            alert('Unable to access camera: ' + error.message);
                            console.error('Camera error:', error);
                            cameraModal.addClass('d-none');
                            $('#photo').click();
                        });
                } else {
                    alert('Camera not supported on this device.');
                    $('#photo').click();
                }
            }

            // Use click event only, targeting the button specifically
            $('[data-action="open-camera"]').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                cameraModal.removeClass('d-none');
                startCamera(currentFacingMode);
            });

            $('#switch-camera-btn').on('click', function() {
                currentFacingMode = (currentFacingMode === 'user') ? 'environment' : 'user';
                stopCamera();
                startCamera(currentFacingMode);
            });

            $('#capture-btn').on('click', function(e) {
                e.preventDefault();
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                let imageData = canvas.toDataURL('image/png');
                imgPreview.attr('src', imageData).removeClass('d-none');
                imgInput.val(imageData);
                stopCamera();
                $('#btn-upload-photo').addClass('d-none');
                $('#btn-remove-photo').removeClass('d-none');
                cameraModal.addClass('d-none');
                updateCameraLabel(); // Update label after capturing photo
            });

            $('#btn-remove-photo').on('click', function() {
                $('#photo').val('');
                $('#img-preview').val('');
                $('#photo-preview').removeAttr('src').addClass('d-none');
                $('#btn-remove-photo').addClass('d-none');
                $('#btn-upload-photo').removeClass('d-none');
                updateCameraLabel(); // Update label after removing photo
            });

            $('#close-camera-btn').on('click', function(e) {
                e.preventDefault();
                stopCamera();
                cameraModal.addClass('d-none');
            });

            $('#photo').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.attr('src', e.target.result).removeClass('d-none');
                        imgInput.val(e.target.result);
                        $('#btn-upload-photo').addClass('d-none');
                        $('#btn-remove-photo').removeClass('d-none');
                        updateCameraLabel(); // Update label after uploading photo
                    };
                    reader.readAsDataURL(file);
                }
            });

            function stopCamera() {
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                }
                video.srcObject = null;
            }

            const selectedAreaId = '{{ old('area', $report->area_id ?? '') }}';
            const selectedOutletId = '{{ old('outlet_id', $report->outlet_id ?? '') }}';
            const selectedCustomerId = '{{ old('customer_id', $report->customer_id ?? '') }}';
            const selectedCustomerType = '{{ old('customer_type', $report->customer_type ?? '') }}';

            // Handle area change
            $('#area').on('change', function() {
                const areaId = $(this).val();
                const $outletSelect = $('#outlet_id');
                const $customerSelect = $('#customer_id');
                const $customerTypeSelect = $('#customer_type');

                // Clear dependent selects
                $outletSelect.empty().append('<option value="">{{ __('Loading...') }}</option>');
                $customerSelect.empty().append('<option value="">{{ __('Select outlet first') }}</option>')
                    .trigger('change.select2');
                $customerTypeSelect.empty().append(
                    '<option value="">{{ __('Select customer first') }}</option>').trigger(
                    'change.select2');

                if (areaId) {
                    $.ajax({
                        url: "{{ route('customers.outlet') }}",
                        method: "GET",
                        data: {
                            area_id: areaId
                        },
                        success: function(response) {
                            $outletSelect.empty().append(
                                '<option value="">{{ __('Select Depo') }}</option>');

                            if (Object.keys(response).length === 0) {
                                $outletSelect.append(
                                    '<option value="">{{ __('Depo Not Found!') }}</option>'
                                    );
                            } else {
                                $.each(response, function(id, name) {
                                    // Pre-select outlet if it matches selectedOutletId
                                    const isSelected = id == selectedOutletId ?
                                        'selected' : '';
                                    $outletSelect.append(
                                        `<option value="${id}" ${isSelected}>${name}</option>`
                                        );
                                });
                            }
                            $outletSelect.trigger('change.select2');

                            // Trigger outlet change if an outlet is selected
                            if (selectedOutletId && areaId == selectedAreaId) {
                                $outletSelect.val(selectedOutletId).trigger('change');
                            }
                        },
                        error: function() {
                            $outletSelect.empty().append(
                                '<option value="">{{ __('Error loading depo') }}</option>'
                                ).trigger('change.select2');
                        }
                    });
                } else {
                    $outletSelect.empty().append(
                        '<option value="">{{ __('Select area first') }}</option>').trigger(
                        'change.select2');
                }
            });

            // Handle outlet change
            $('#outlet_id').on('change', function() {
                const areaId = $('#area').val();
                const outletId = $(this).val();
                const $customerSelect = $('#customer_id');
                const $customerTypeSelect = $('#customer_type');

                // Clear dependent selects
                $customerSelect.empty().append('<option value="">{{ __('Select Customer') }}</option>')
                    .trigger('change.select2');
                $customerTypeSelect.empty().append(
                    '<option value="">{{ __('Select customer first') }}</option>').trigger(
                    'change.select2');

                if (areaId && outletId) {
                    $.ajax({
                        url: '{{ route('customers.getNames') }}',
                        type: 'GET',
                        data: {
                            area_id: areaId,
                            outlet_id: outletId
                        },
                        success: function(response) {
                            if (response.success && response.customers.length > 0) {
                                $.each(response.customers, function(index, customer) {
                                    // Pre-select customer if it matches selectedCustomerId
                                    const isSelected = customer.id ==
                                        selectedCustomerId ? 'selected' : '';
                                    $customerSelect.append(
                                        `<option value="${customer.id}" ${isSelected}>${customer.name}</option>`
                                    );
                                });
                            } else {
                                $customerSelect.append(
                                    '<option value="">{{ __('No customers found') }}</option>'
                                    );
                            }
                            $customerSelect.trigger('change.select2');

                            // Trigger customer change if a customer is selected
                            if (selectedCustomerId && outletId == selectedOutletId) {
                                $customerSelect.val(selectedCustomerId).trigger('change');
                            }
                        },
                        error: function(xhr) {
                            $customerSelect.empty().append(
                                '<option value="">{{ __('Error loading customers') }}</option>'
                                ).trigger('change.select2');
                            console.error('Error:', xhr.responseJSON?.error ||
                                'Failed to load customers');
                        }
                    });
                }
            });

            // Handle customer change
            $('#customer_id').on('change', function() {
                const areaId = $('#area').val();
                const outletId = $('#outlet_id').val();
                const customerId = $(this).val();
                const $customerTypeSelect = $('#customer_type');

                // Clear customer type select
                $customerTypeSelect.empty().append('<option value="">{{ __('Loading...') }}</option>').trigger('change.select2');

                if (areaId && outletId && customerId) {
                    $.ajax({
                        url: '{{ route('customers.getCustomerTypes') }}',
                        type: 'GET',
                        data: { customer_id: customerId },
                        success: function(data) {
                            $customerTypeSelect.empty();

                            if (data.customer_types && data.customer_types.length > 0) {
                                const customerType = data.customer_types[0]; // Assuming one type per customer

                                // Append and auto-select
                                $customerTypeSelect.append(
                                    `<option value="${customerType.id}" selected>${customerType.name}</option>`
                                );

                                // Create hidden input to submit the value since "disabled" fields aren't submitted
                                $('#hidden_customer_type').remove();
                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'hidden_customer_type',
                                    name: 'customer_type',
                                    value: customerType.id
                                }).appendTo('form');
                            } else {
                                $customerTypeSelect.append('<option value="">{{ __('Customer Type Not Found!') }}</option>');
                                $('#hidden_customer_type').remove();
                            }

                            $customerTypeSelect.trigger('change.select2');
                        },
                        error: function(xhr) {
                            $customerTypeSelect.empty().append(
                                '<option value="">{{ __('Error loading customer types') }}</option>'
                            ).trigger('change.select2');
                            console.error('Error:', xhr.responseJSON?.error || 'Failed to load customer types');
                        }
                    });
                } else {
                    $customerTypeSelect.empty().append('<option value="">{{ __('Select customer first') }}</option>').trigger('change.select2');
                    $('#hidden_customer_type').remove();
                }
            });

            // Trigger initial changes if values are pre-selected
            if (selectedAreaId) {
                $('#area').trigger('change');
            }
            let outletVideo = document.getElementById('outlet-webcam');
            let outletCanvas = document.getElementById('outlet-canvas');
            let outletContext = outletCanvas.getContext('2d');
            let outletImgPreview = $('#outlet-photo-preview');
            let outletImgInput = $('#outlet-img-preview');
            let outletCameraModal = $('#outlet-camera-modal');
            let outletCameraLabel = $('#outlet-camera-label');
            let outletCurrentFacingMode = 'user';

            function updateOutletCameraLabel() {
                if (outletImgPreview.hasClass('d-none')) {
                    outletCameraLabel.text('{{ __('Click to open camera and capture photo') }}');
                } else {
                    outletCameraLabel.text('{{ __('Delete the old photo before you can open the camera') }}');
                }
            }

            updateOutletCameraLabel();

            @if ($report && $report->outlet_photo)
                $('#btn-upload-outlet-photo').addClass('d-none');
                $('#btn-remove-outlet-photo').removeClass('d-none');
            @endif

            function startOutletCamera(facingMode) {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    outletVideo.setAttribute('playsinline', 'true');
                    outletVideo.setAttribute('autoplay', 'true');

                    navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: facingMode,
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            }
                        })
                        .then(function(stream) {
                            outletVideo.srcObject = stream;
                            outletVideo.play();
                        })
                        .catch(function(error) {
                            alert('Unable to access camera: ' + error.message);
                            console.error('Camera error:', error);
                            outletCameraModal.addClass('d-none');
                            $('#outlet_photo').click();
                        });
                } else {
                    alert('Camera not supported on this device.');
                    $('#outlet_photo').click();
                }
            }

            $('[data-action="open-outlet-camera"]').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                outletCameraModal.removeClass('d-none');
                startOutletCamera(outletCurrentFacingMode);
            });

            $('#switch-outlet-camera-btn').on('click', function() {
                outletCurrentFacingMode = (outletCurrentFacingMode === 'user') ? 'environment' : 'user';
                stopOutletCamera();
                startOutletCamera(outletCurrentFacingMode);
            });

            $('#capture-outlet-btn').on('click', function(e) {
                e.preventDefault();
                outletCanvas.width = outletVideo.videoWidth;
                outletCanvas.height = outletVideo.videoHeight;
                outletContext.drawImage(outletVideo, 0, 0, outletCanvas.width, outletCanvas.height);
                let imageData = outletCanvas.toDataURL('image/png');
                outletImgPreview.attr('src', imageData).removeClass('d-none');
                outletImgInput.val(imageData);
                stopOutletCamera();
                $('#btn-upload-outlet-photo').addClass('d-none');
                $('#btn-remove-outlet-photo').removeClass('d-none');
                outletCameraModal.addClass('d-none');
                updateOutletCameraLabel();
            });

            $('#btn-remove-outlet-photo').on('click', function() {
                $('#outlet_photo').val('');
                $('#outlet-img-preview').val('');
                $('#outlet-photo-preview').removeAttr('src').addClass('d-none');
                $('#btn-remove-outlet-photo').addClass('d-none');
                $('#btn-upload-outlet-photo').removeClass('d-none');
                updateOutletCameraLabel();
            });

            $('#close-outlet-camera-btn').on('click', function(e) {
                e.preventDefault();
                stopOutletCamera();
                outletCameraModal.addClass('d-none');
            });

            $('#outlet_photo').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        outletImgPreview.attr('src', e.target.result).removeClass('d-none');
                        outletImgInput.val(e.target.result);
                        $('#btn-upload-outlet-photo').addClass('d-none');
                        $('#btn-remove-outlet-photo').removeClass('d-none');
                        updateOutletCameraLabel();
                    };
                    reader.readAsDataURL(file);
                }
            });

            function stopOutletCamera() {
                if (outletVideo.srcObject) {
                    outletVideo.srcObject.getTracks().forEach(track => track.stop());
                }
                outletVideo.srcObject = null;
            }

            // Update form submission to handle outlet photo
            $('#entryForm').on('submit', function(e) {
                // Handle main photo
                let imageData = $('#img-preview').val();
                if (imageData && imageData.startsWith('data:image/')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'photo_base64',
                        value: imageData
                    }).appendTo(this);
                } else {
                    let existingPhoto =
                        '@if (isset($report->photo)) {{ $report->photo }} @endif';
                    if (existingPhoto) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'oldphoto',
                            value: existingPhoto
                        }).appendTo(this);
                    }
                }

                // Handle outlet photo
                let outletImageData = $('#outlet-img-preview').val();
                if (outletImageData && outletImageData.startsWith('data:image/')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'outlet_photo_base64',
                        value: outletImageData
                    }).appendTo(this);
                } else {
                    let existingOutletPhoto =
                        '@if (isset($report->outlet_photo)) {{ $report->outlet_photo }} @endif';
                    if (existingOutletPhoto) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'old_outlet_photo',
                            value: existingOutletPhoto
                        }).appendTo(this);
                    }
                }

                // Validate outlet photo is required
                if ($('#outlet-photo-preview').hasClass('d-none') && !$('#outlet_photo').val() && !
                    existingOutletPhoto) {
                    e.preventDefault();
                    alert('{{ __('Outlet photo is required') }}');
                    return false;
                }
            });
        });
        $(document).ready(function() {
            // Check if outlet photo already exists (either old input or from $report)
            let existingOutletPhoto = "{{ old('old_outlet_photo', $report->outlet_photo ?? '') }}";

            if (existingOutletPhoto) {
                $('#btn-upload-outlet-photo').addClass('d-none');
                $('#btn-remove-outlet-photo').removeClass('d-none');
            }
        });
        // $('#outlet').on('input', function() {
        //     let outletValue = $(this).val().trim();

        //     if (outletValue.length > 0) {
        //         $.ajax({
        //             method: 'GET',
        //             data: { outlet: outletValue },
        //             dataType: 'json',
        //             success: function(response) {
        //                 if (response.success) {
        //                     $('input[name="area"]').val(response['area'] || '');
        //                     $('#customer').val(response.customer || '');
        //                     $('#customer_type').val(response.customer_type).trigger('change');

        //                     $('input[name="250_ml"]').val(response['250_ml'] || '');
        //                     $('input[name="350_ml"]').val(response['350_ml'] || '');
        //                     $('input[name="600_ml"]').val(response['600_ml'] || '');
        //                     $('input[name="1500_ml"]').val(response['1500_ml'] || '');
        //                     $('input[name="phone"]').val(response.phone || '');
        //                     $('input[name="other"]').val(response.other || '');
        //                 } else {
        //                     $('input[name="area"]').val('');
        //                     $('#customer').val('');
        //                     $('#customer_type').val('Select customer type').trigger('change');
        //                     $('input[name="250_ml"]').val('');
        //                     $('input[name="350_ml"]').val('');
        //                     $('input[name="600_ml"]').val('');
        //                     $('input[name="1500_ml"]').val('');
        //                     $('input[name="phone"]').val('');
        //                     $('input[name="other"]').val('');
        //                 }
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error('Error fetching customer data:', error);
        //                 $('input[name="area"]').val('');
        //                 $('#customer').val('');
        //                 $('#customer_type').val('Select customer type').trigger('change');
        //                 $('input[name="250_ml"]').val('');
        //                 $('input[name="350_ml"]').val('');
        //                 $('input[name="600_ml"]').val('');
        //                 $('input[name="1500_ml"]').val('');
        //                 $('input[name="phone"]').val('');
        //                 $('input[name="other"]').val('');
        //             }
        //         });
        //     } else {
        //         $('input[name="area"]').val('');
        //         $('#customer').val('');
        //         $('#customer_type').val('Select customer type').trigger('change');
        //         $('input[name="250_ml"]').val('');
        //         $('input[name="350_ml"]').val('');
        //         $('input[name="600_ml"]').val('');
        //         $('input[name="1500_ml"]').val('');
        //         $('input[name="phone"]').val('');
        //         $('input[name="other"]').val('');
        //     }
        // });
    </script>
@endsection
