@extends('backend.layouts.master')

@section('pageTitle')
    {{ __('Exclusive Customer') }}
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
        fieldset>#open-camera-btn-foc>#btn-upload-photo-foc {
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
            <li><a> {{ __('Exclusive Customer') }} </a></li>
            {{-- <li><a href="{{ URL::route('school.index') }}"> {{ __('School') }} </a></li> --}}
            <li class="active">
                {{ __('Detail') }}
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h1>
                        {{ __('Report Data') }}
                        <small class="toch">
                            {{ __('Detail') }}
                        </small>
                    </h1>
                    <div class="box-tools pull-right">
                        <a href="{{ url()->previous() }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i>
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrap-outter-box">
            <div class="box-body">
                <div class="row">

                    {{-- user info  --}}
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3 text-muted"> <i class="fa fa-clock" aria-hidden="true"></i> {{ Carbon\Carbon::parse($report->created_at)->format('d M Y h:i:s A') }}</div>
                        <fieldset class="border-0 p-0 m-0 mb-4">
                            <legend class="fs-6">{{ __('Staff Info') }}</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Staff ID')}}</div>
                                            </div>
                                            <span class="">{{$report->user->staff_id_card ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Name')}}</div>
                                            </div>
                                            <span class="">{{session('user_lang') == 'en' ? $report->user->family_name_latin . ' '. $report->user->name_latin : $report->user->family_name . ' '. $report->user->name }} {{"(". $report->user->username . ")"}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Gender')}}</div>
                                            </div>
                                            <span class="text-capitalize">{{isset(AppHelper::GENDER[$report->user->gender]) ? __(AppHelper::GENDER[$report->user->gender]) : __('N/A');}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Positon')}}</div>
                                            </div>
                                            <span class="">{{$report->user->position ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Area')}}</div>
                                            </div>
                                            <span class="">{{$report->user->area ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Phone number')}}</div>
                                            </div>
                                            <span class="">{{$report->user->phone_no ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>

                            </div>
                        </fieldset>
                    </div>

                    {{-- customer info  --}}
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <fieldset class="border-0 p-0 m-0 mb-4">
                            <legend class="fs-6">{{ __('Customer Info') }}</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Region')}}</div>
                                            </div>
                                            <span class="">{{$report->region->region_name . ' - '. $report->region->se_code}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Depot')}}</div>
                                            </div>
                                            <span class="">{{$report->outlet->name ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Customer name')}}</div>
                                            </div>
                                            <span class="text-capitalize">{{$report->CustomerProvince->name ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Customer contact')}}</div>
                                            </div>
                                            <span class="">{{$report->CustomerProvince->phone ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Customer type')}}</div>
                                            </div>
                                            <span class="">{{ \App\Http\Helpers\AppHelper::CUSTOMER_TYPE_PROVINCE[$report->customer_type] ?? 'N/A' }}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Province')}}</div>
                                            </div>
                                            <span class="">{{$report->region->province ?? 'N/A'}}</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Sale data inofo  --}}
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <fieldset class="border-0 p-0 m-0 mb-4">
                            <legend class="fs-6">{{ __('Sale Info') }}</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('250ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'250_ml'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('350ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'350_ml'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('600ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'600_ml'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('1500ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'1500_ml'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>

                                <div class="col-12">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Default')}}</div>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{!! intval($report->{'250_ml'}) + intval($report->{'350_ml'}) + intval($report->{'600_ml'}) + intval($report->{'1500_ml'}) ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>

                                <div class="col-12">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('Other')}}</div>
                                            </div>
                                            <span>{{ $report->other ?? 'N/A' }}</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- FOC info  --}}
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <fieldset class="border-0 p-0 m-0 mb-4">
                            <legend class="fs-6">{{ __('FOC') }}</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('250ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'foc_250_qty'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('350ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'foc_350_qty'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('600ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'foc_600_qty'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('1500ml')}}</div>
                                            </div>
                                            <span class="">{!! $report->{'foc_1500_qty'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div>

                                {{-- <div class="col-12">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold text-danger">{{__('Special FOC')}} {{ $report->foc_other . ' ml' }}</div>

                                            </div>
                                            <span class="">{!! $report->{'foc_other_qty'} ?? 0 !!}  {{__('CTN')}}</span>
                                        </li>
                                    </ol>
                                </div> --}}
                            </div>
                        </fieldset>
                    </div>


                    {{-- POSM info  --}}
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <fieldset class="border-0 p-0 m-0 mb-4">
                            <legend class="fs-6">{{ __('POSM') }}</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('POSM 1')}}</div>
                                            </div>
                                            <span class="">{{ session('user_lang') =='en' ? @$report->posm1->name_en : @$report->posm1->name_kh}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('POSM 2')}}</div>
                                            </div>
                                            <span class="">{{ session('user_lang') =='en' ? @$report->posm2->name_en : @$report->posm1->name_kh}}</span>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <ol class="list-group p-0 rounded-0" style="list-style-type: none;">
                                        <li class="list-group-item px-0 d-flex rounded-0 justify-content-between align-items-start border-0 border-bottom ">
                                            <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{__('POSM 3')}}</div>
                                            </div>
                                            <span class="">{{ session('user_lang') =='en' ? @$report->posm3->name_en : @$report->posm1->name_kh}}</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                            <div class="row">
                                @if ($report->photo_foc)
                                    <div class="col-sm-12 col-md-6 mt-4">
                                        <p>{{__("FOC Photo")}}</p>
                                        <img src="{{ asset('storage/'.$report->photo_foc) }}" width="100%" alt="FOC Photo">
                                    </div>
                                @endif
                                @if ($report->photo)
                                    <div class="col-sm-12 col-md-6 mt-4">
                                        <p>{{__("POSM Photo")}}</p>
                                        <img src="{{ asset('storage/'.$report->photo) }}" width="100%" alt="POSM Photo">
                                    </div>
                                @endif
                            </div>
                        </fieldset>
                    </div>

                    <!-- Location Fields and Map -->
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <fieldset>
                            <legend class="fs-6">{{ __('Location') }}</legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <div class="form-group has-feedback">
                                        <label for="latitude">{{ __('Latitude') }}</label>
                                        <input type="text" class="form-control" name="latitude" id="latitude"
                                            placeholder="{{ __('Latitude') }}"
                                            value="{{ isset($report) ? $report->latitude : old('latitude') }}" readonly>
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('latitude') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <div class="form-group has-feedback">
                                        <label for="longitude">{{ __('Longitude') }}</label>
                                        <input type="text" class="form-control" name="longitude" id="longitude"
                                            placeholder="{{ __('Longitude') }}"
                                            value="{{ isset($report) ? $report->longitude : old('longitude') }}" readonly>

                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('longitude') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <div class="form-group has-feedback">
                                        <label for="city">{{ __('Address') }}</label>
                                        <textarea class="form-control" name="city" placeholder="{{ __('Address') }}" id="city" cols="30"
                                            rows="1" readonly>{{ isset($report) ? $report->city : old('city') }}</textarea>
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('city') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xl-6">
                                    <div class="form-group has-feedback">
                                        <label for="country">{{ __('Country') }}</label>
                                        <input type="text" class="form-control" name="country" id="country"
                                            placeholder="{{ __('Country') }}"
                                            value="{{ isset($report) ? $report->country : old('country') }}" readonly
                                            required>
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('country') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-xl-12 mt-3">
                                    {{-- <button type="button" class="btn btn-primary" id="getLocationBtn">
                                            <i class="fa-solid fa-location-dot"></i> {{ __('Get Location') }}
                                        </button> --}}
                                    <div id="map" style="height: 400px; margin-top: 15px;"></div>
                                </div>


                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('extraScript')
    <script>
        let map;
        let marker;
        var lat = {{ $report->latitude }};
        var long = {{ $report->longitude }};
        // Initialize map function
        function initMap(lat = lat, lng = long) {
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
        window.onload = function() {
            initMap({{ $report->latitude }}, {{ $report->longitude }});
        }
    </script>
@endsection
