@extends('backend.layouts.master')

@section('pageTitle')
    Restaurant
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

        fieldset>#open-camera-btn,
        fieldset>#open-camera-btn-foc {
            overflow: hidden;
            cursor: pointer;
            width: 100%;
            height: 340px;
            background-color: #f5f5f5;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        fieldset>#open-camera-btn:hover,
        fieldset>#open-camera-btn-foc:hover {
            background-color: #e0e0e0;
        }

        fieldset>#open-camera-btn>#btn-upload-photo,
        fieldset>#open-camera-btn-foc>#btn-upload-photo-foc  {
            min-width: 100px;
            min-height: 100px;
            background-color: #ddd;
            font-size: 25px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        fieldset>#open-camera-btn>#btn-upload-photo:hover,
        fieldset>#open-camera-btn>#btn-upload-photo-foc:hover {
            transform: scale(1.05);
        }

        fieldset>#photo-preview,
         fieldset>#photo-foc-preview {
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
            <li><a href="{{ URL::route('restaurant.index') }}"> {{ __('Restaurant') }} </a></li>
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
            action="@if ($report) {{ URL::Route('restaurant.update', $report->id) }} @else {{ URL::Route('restaurant.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>
                            {{ __('Customer Data') }}
                            <small class="toch">
                                @if ($report)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Add New') }}
                                @endif
                            </small>
                        </h1>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('restaurant.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
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
                    @if ($report)
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="area">{{ __('Area') }} <span class="text-danger">*</span></label>
                                <select name="area" id="area" class="form-control select2" required>
                                    <option value="">{{ __('Select Area') }}</option>
                                    @foreach(\App\Http\Helpers\AppHelper::getAreas() as $area => $subItems)
                                        <optgroup label="{{ $area }}">
                                            @foreach($subItems as $area_id => $subItem)
                                                <option value="{{ $area_id }}"
                                                    @if(old('area', $report->area_id ?? '') == $area_id) selected @endif>
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
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="outlet_id">{{ __('Outlet') }} <span class="text-danger">*</span></label>
                                <select name="outlet_id" class="form-control select2" id="outlet_id" required>
                                    <option value="">{{ __('Select area first') }}</option>
                                    @if ($report && $report->outlet && !$customers->contains('id', $report->outlet_id))
                                        <option value="{{ $report->outlet_id }}" selected>
                                            {{ $report->customer->outlet }} (Current)
                                        </option>
                                    @endif
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('outlet_id', $report->outlet_id ?? '') == $c->id ? 'selected' : '' }}>
                                            {{ $c->outlet }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('outlet_id') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="customer_id">{{ __('Customer') }} <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control select2" id="customer_id" required>
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @if ($report && $report->customer_id && !$customers->contains('id', $report->customer_id))
                                        <option value="{{ $report->customer_id }}" selected>
                                            {{ $report->customer_id }} (Current)
                                        </option>
                                    @endif
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" {{ old('customer_id', $report->customer_id ?? '') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="customer_type"> {{ __('Customer Type') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select customer type"></i>
                                </label>
                                {!! Form::select('customer_type', $customerType, old('customer_type', optional($report)->customer_type), [
                                    'placeholder' => __('Select customer type'),
                                    'id' => 'customer_type',
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}
                                {{-- <select name="customer_type" id="customer_type" class="form-control select2">
                                    <option selected disabled>{{ __('Select customer type') }}</option>
                                    <option value="test1"
                                        @if ($report) @if ($report->customer_type == 'test1')
                                                selected @endif
                                        @endif
                                        >Test1</option>
                                    <option value="test2"
                                        @if ($report) @if ($report->customer_type == 'test2')
                                                selected @endif
                                        @endif
                                        >Test2</option>
                                </select> --}}
                                <span class="form-control-feedback"></span>
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
                                <label for="250_ml"> {{ __('250ml') }}</label>
                                <input type="text" class="form-control" name="250_ml"
                                    value="{{ isset($report) ? $report['250_ml'] : old('250_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="350_ml"> {{ __('350ml') }}</label>
                                <input type="text" class="form-control" name="350_ml"
                                    value="{{ isset($report) ? $report['350_ml'] : old('350_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="600_ml"> {{ __('600ml') }}</label>
                                <input type="text" class="form-control" name="600_ml"
                                    value="{{ isset($report) ? $report['600_ml'] : old('600_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="1500_ml"> {{ __('1500ml') }}</label>
                                <input type="text" class="form-control" name="1500_ml"
                                    value="{{ isset($report) ? $report['1500_ml'] : old('1500_ml') }}">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-xl-12">
                            <div class="form-group has-feedback">
                                <label for="other"> {{ __('Other') }}</label>
                                <input type="text" class="form-control" name="other" placeholder="other"
                                    value="@if ($report) {{ $report->other }}@else{{ old('other') }} @endif">
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-xl-12">
                            <div class="form-group has-feedback">

                                <input type="hidden" class="form-control" name="old_photo" placeholder="photo"
                                    value="@if ($report) {{ $report->photo }}@else{{ old('old_photo') }} @endif">
                                <input type="hidden" class="form-control" name="old_photo_foc" placeholder="photo foc"
                                    value="@if ($report) {{ $report->photo_foc }}@else{{ old('old_photo_foc') }} @endif">
                            </div>
                        </div>



                        {{-- FOC START  --}}
                        <fieldset>
                            <legend>{{ __('FOC') }}</legend>
                            <div class="row">
                                <div class="form-group has-feedback">
                                    <div class="row">
                                        <div class="row-span-6 col-sm-12 col-md-12 col-lg-12 col-xl-6">
                                            <div class="form-group has-feedback position-relative">
                                                <input type="file" id="photo-foc" name="photo_foc"
                                                    style="display: none" accept="image/*">
                                                <button type="button"
                                                    class="btn btn-light text-secondary fs-5 position-absolute d-none m-2 end-0 z-1"
                                                    id="btn-remove-photo-foc">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                                <fieldset id="photo-foc-upload"
                                                    class="p-0 d-flex align-items-center justify-content-center z-0 position-relative">
                                                    <img class="rounded mx-auto d-block @if (!old('oldphoto-foc') && !old('img-preview-foc') && !isset($report)) {{ 'd-none' }} @endif z-1"
                                                        id="photo-foc-preview" name="oldphoto-foc"
                                                        src="@if (optional($report)->photo_foc) {{ asset('storage/' . $report->photo_foc) }}@else{{ old('oldphoto-foc') }} @endif"
                                                        alt="photo-foc">
                                                    <input type="hidden" id="img-preview-foc" name="oldphoto-foc"
                                                        value="@if (optional($report)->photo_foc) {{ $report->photo_foc }} @endif">
                                                    <div class="d-flex align-items-center justify-content-center bg-transparent z-2 @if (!old('img-preview-foc')) {{ 'opacity-100' }} @else {{ 'opacity-25' }} @endif"
                                                        id="open-camera-btn-foc">
                                                        <button class="btn p-3 rounded-circle" id="btn-upload-photo-foc"
                                                            type="button" data-action="open-camera-foc">
                                                            <i class="fa-solid fa-camera-retro"></i>
                                                        </button>
                                                    </div>
                                                    <label id="camera-label-foc"
                                                        class="position-absolute bottom-0 text-center w-100 mb-2">
                                                        {{ __('Click to open camera and capture photo') }}
                                                    </label>
                                                </fieldset>
                                            </div>
                                            <div id="camera-modal-foc" class="camera-modal d-none">
                                                <div class="camera-content">
                                                    <div class="video-container position-relative">
                                                        <video id="webcam-foc" autoplay playsinline></video>
                                                        <div class="camera-overlay">
                                                            <div class="overlay-top"></div>
                                                            <div class="overlay-bottom"></div>
                                                            <div class="overlay-left"></div>
                                                            <div class="overlay-right"></div>
                                                            <div class="focus-circle"></div>
                                                        </div>
                                                    </div>
                                                    <div class="camera-controls">
                                                        <button id="switch-camera-btn-foc" class="btn switch-camera-btn"
                                                            type="button">
                                                            <i class="fa-solid fa-camera-rotate"></i>
                                                        </button>
                                                        <button id="capture-btn-foc" class="btn capture-btn" type="button">
                                                            <i class="fa-solid fa-camera"></i>
                                                        </button>
                                                        <button id="close-camera-btn-foc" class="btn close-camera-btn"
                                                            type="button">
                                                            <i class="fa-solid fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <canvas id="canvas-foc" class="d-none"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                {{-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="posm"> {{ __('POSM') }}
                                                            <i class="fa fa-question-circle" data-toggle="tooltip"
                                                                data-placement="bottom" title="Select POSM"></i>
                                                        </label>
                                                        {!! Form::select(
                                                            'posm',
                                                            [
                                                                AppHelper::UMBRELLA => __(AppHelper::MATERIAL[AppHelper::UMBRELLA]),
                                                                AppHelper::SHIRT => __(AppHelper::MATERIAL[AppHelper::SHIRT]),
                                                                AppHelper::FAN => __(AppHelper::MATERIAL[AppHelper::FAN]),
                                                                AppHelper::CALENDAR => __(AppHelper::MATERIAL[AppHelper::CALENDAR]),
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
                                                </div> --}}
                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="qty"> {{ __('Quantity') }} </label>
                                                        <input type="number" class="form-control" name="foc_qty"
                                                            value="{{ old('foc_qty', $report->foc_qty ?? '') }}">
                                                        <span class="fa fa-info form-control-feedback"></span>
                                                        <span class="text-danger">{{ $errors->first('foc_qty') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        {{-- FOC END  --}}


                        {{-- POSM START --}}
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
                                                    <img class="rounded mx-auto d-block @if (!old('oldphoto') && !old('img-preview') && !isset($report)) {{ 'd-none' }} @endif z-1"
                                                        id="photo-preview" name="oldphoto"
                                                        src="@if (optional($report)->photo) {{ asset('storage/' . $report->photo) }}@else{{ old('oldphoto') }} @endif"
                                                        alt="photo">
                                                    <input type="hidden" id="img-preview" name="oldphoto"
                                                        value="@if (optional($report)->photo) {{$report->photo}} @endif">
                                                    <div class="d-flex align-items-center justify-content-center bg-transparent z-2 @if (!old('img-preview')) {{ 'opacity-100' }} @else {{ 'opacity-25' }} @endif"
                                                        id="open-camera-btn">
                                                        <button class="btn p-3 rounded-circle" id="btn-upload-photo"
                                                            type="button" data-action="open-camera">
                                                            <i class="fa-solid fa-camera-retro"></i>
                                                        </button>
                                                    </div>
                                                    <label id="camera-label"
                                                        class="position-absolute bottom-0 text-center w-100 mb-2">
                                                        {{ __('Click to open camera and capture photo') }}
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
                                                        <button id="capture-btn" class="btn capture-btn" type="button">
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
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="posm"> {{ __('POSM') }}
                                                            <i class="fa fa-question-circle" data-toggle="tooltip"
                                                                data-placement="bottom" title="Select POSM"></i>
                                                        </label>
                                                        {!! Form::select(
                                                            'posm',
                                                            [
                                                                AppHelper::UMBRELLA => __(AppHelper::MATERIAL[AppHelper::UMBRELLA]),
                                                                AppHelper::SHIRT => __(AppHelper::MATERIAL[AppHelper::SHIRT]),
                                                                AppHelper::FAN => __(AppHelper::MATERIAL[AppHelper::FAN]),
                                                                AppHelper::CALENDAR => __(AppHelper::MATERIAL[AppHelper::CALENDAR]),
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
                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="qty"> {{ __('Quantity') }} </label>
                                                        <input type="number" class="form-control" name="qty"
                                                            value="{{ old('qty', $report->qty ?? '') }}">
                                                        <span class="fa fa-info form-control-feedback"></span>
                                                        <span class="text-danger">{{ $errors->first('qty') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        {{-- POSM END--}}

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
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="longitude">{{ __('Longitude') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="longitude" id="longitude"
                                                value="{{ isset($report) ? $report->longitude : old('longitude') }}"
                                                readonly required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="city">{{ __('Address') }}<span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="city" id="city" cols="30" rows="1" readonly required>{{ isset($report) ? $report->city : old('city') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="country">{{ __('Country') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="{{ isset($report) ? $report->country : old('country') }}" readonly
                                                required>
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
            $('#area').on('change', function() {
                var areaId = $(this).val();
                var selectedCustomerId = $('#customer_id').val(); // Store current customer_id
                var selectedOutletId = $('#outlet_id').val(); // Store current outlet_id

                if (areaId) {
                    $.ajax({
                        url: '{{ route('subwholesale.byArea') }}',
                        type: 'GET',
                        data: {
                            area_id: areaId
                        },
                        success: function(data) {
                            // Handle Customers Dropdown
                            $('#customer_id').empty();
                            if (data.customers.length === 0) {
                                $('#customer_id').append(
                                    '<option value="">{{ __('Customer Not Found!') }}</option>'
                                );
                            } else {
                                $('#customer_id').append(
                                    '<option value="">{{ __('Select Customer') }}</option>'
                                );
                                $.each(data.customers, function(key, customer) {
                                    var isSelected = (customer.id ==
                                        selectedCustomerId) ? 'selected' : '';
                                    $('#customer_id').append('<option value="' +
                                        customer.id + '" ' + isSelected + '>' +
                                        customer.name + '</option>');
                                });
                            }
                            $('#customer_id').trigger('change');

                            // Handle Outlets Dropdown
                            $('#outlet_id').empty();
                            if (data.outlets.length === 0) {
                                $('#outlet_id').append(
                                    '<option value="">{{ __('Outlet Not Found!') }}</option>'
                                );
                            } else {
                                $('#outlet_id').append(
                                    '<option value="">{{ __('Select outlet') }}</option>'
                                );
                                $.each(data.outlets, function(key, outlet) {
                                    var isSelected = (outlet.id == selectedOutletId) ?
                                        'selected' : '';
                                    $('#outlet_id').append('<option value="' + outlet
                                        .id + '" ' + isSelected + '>' + outlet
                                        .name + '</option>');
                                });
                            }
                            $('#outlet_id').trigger('change');
                        },
                        error: function(xhr) {
                            console.log('Error fetching data:', xhr);
                            //orney Handle Customers Dropdown on Error
                            $('#customer_id').empty().append(
                                '<option value="">{{ __('Customer Not Found!') }}</option>'
                            );
                            $('#customer_id').trigger('change');

                            // Handle Outlets Dropdown on Error
                            $('#outlet_id').empty().append(
                                '<option value="">{{ __('Outlet Not Found!') }}</option>'
                            );
                            $('#outlet_id').trigger('change');
                        }
                    });
                } else {
                    // Clear Customers Dropdown
                    $('#customer_id').empty().append(
                        '<option value="">{{ __('Customer Not Found!') }}</option>'
                    );
                    $('#customer_id').trigger('change');

                    // Clear Outlets Dropdown
                    $('#outlet_id').empty().append(
                        '<option value="">{{ __('Outlet Not Found!') }}</option>'
                    );
                    $('#outlet_id').trigger('change');
                }
            });

            // Trigger change on page load if area is pre-selected
            if ($('#area').val()) {
                $('#area').trigger('change');
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

    <script>
        $(document).ready(function() {

            $('#entryForm').on('submit', function(e) {
                let imageData = $('#img-preview').val();
                if (imageData) {
                    $('<input>').attr({
                        type: 'text',
                        name: 'photo_base64',
                        value: imageData
                    }).appendTo(this);
                }

                let imageDataFoc = $('#img-preview-foc').val();
                if (imageDataFoc) {
                    $('<input>').attr({
                        type: 'text',
                        name: 'photo_base64_foc',
                        value: imageDataFoc
                    }).appendTo(this);
                }
            });

            // POSM START
            $(document).on('click', '#btn-upload-photo', function () {
                // POSM START
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
                // POSM END
            });


            // FOC START

            $(document).on('click', '#btn-upload-photo-foc' , function () {



                // FOC START
                    let video = document.getElementById('webcam-foc');
                    let canvas = document.getElementById('canvas-foc');
                    let context = canvas.getContext('2d');
                    let imgPreview = $('#photo-foc-preview');
                    let imgInput = $('#img-preview-foc');
                    let cameraModal = $('#camera-modal-foc');
                    let cameraLabel = $('#camera-label-foc');
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
                        $('#btn-upload-photo-foc').addClass('d-none');
                        $('#btn-remove-photo-foc').removeClass('d-none');
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
                                    $('#photo-foc').click();
                                });
                        } else {
                            alert('Camera not supported on this device.');
                            $('#photo-foc').click();
                        }
                    }

                    // Use click event only, targeting the button specifically
                    $('[data-action="open-camera-foc"]').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        cameraModal.removeClass('d-none');
                        startCamera(currentFacingMode);
                    });

                    $('#switch-camera-btn-foc').on('click', function() {
                        currentFacingMode = (currentFacingMode === 'user') ? 'environment' : 'user';
                        stopCamera();
                        startCamera(currentFacingMode);
                    });

                    $('#capture-btn-foc').on('click', function(e) {
                        e.preventDefault();
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);
                        let imageData = canvas.toDataURL('image/png');
                        imgPreview.attr('src', imageData).removeClass('d-none');
                        imgInput.val(imageData);
                        stopCamera();
                        $('#btn-upload-photo-foc').addClass('d-none');
                        $('#btn-remove-photo-foc').removeClass('d-none');
                        cameraModal.addClass('d-none');
                        updateCameraLabel(); // Update label after capturing photo
                    });

                    $('#btn-remove-photo-foc').on('click', function() {

                        // console.log('remove');

                        $('#photo-foc').val('');
                        $('#img-preview-foc').val('');
                        $('#photo-foc-preview').removeAttr('src').addClass('d-none');
                        $('#btn-remove-photo-foc').addClass('d-none');
                        $('#btn-upload-photo-foc').removeClass('d-none');
                        updateCameraLabel(); // Update label after removing photo
                    });

                    $('#close-camera-btn-foc').on('click', function(e) {
                        e.preventDefault();
                        stopCamera();
                        cameraModal.addClass('d-none');
                    });

                    $('#photo-foc').on('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imgPreview.attr('src', e.target.result).removeClass('d-none');
                                imgInput.val(e.target.result);
                                $('#btn-upload-photo-foc').addClass('d-none');
                                $('#btn-remove-photo-foc').removeClass('d-none');
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

                // FOC END
            });
        });
    </script>


@endsection
