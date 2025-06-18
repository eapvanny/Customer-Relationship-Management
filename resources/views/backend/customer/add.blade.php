@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    {{ __('Customer') }}
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
            .btn-default .btn-info .btn-success {
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
            <li><a href="{{ URL::route('customer.index') }}"> {{ __('Customer') }} </a></li>
            <li class="active">
                @if ($customer)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>
    <!-- ./Section header -->

    <!-- Main content -->
    <section class="content">
        <form id="entryForm"
            action="@if ($customer) {{ URL::Route('customer.update', $customer->id) }} @else {{ URL::Route('customer.store') }} @endif"
            method="post" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>{{ __('Customer') }}</h4>
                        <div class="action-btn-top none_fly_action_btn">
                            <a href="{{ URL::route('customer.index') }}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white">
                                <i class="fa @if ($customer) fa-refresh @else fa-check-circle @endif"></i>
                                @if ($customer)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Save') }}
                                @endif
                            </button>
                            @if (!$customer)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white">
                                    <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @csrf
            @if ($customer)
                @method('PUT')
            @endif
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="area">{{ __('Area') }} <span class="text-danger">*</span></label>
                                    <select name="area" id="area" class="form-control select2" required>
                                        <option value="">{{ __('Select Area') }}</option>
                                        @foreach (\App\Http\Helpers\AppHelper::getAreas() as $area => $subItems)
                                            <optgroup label="{{ $area }}">
                                                @foreach ($subItems as $area_id => $subItem)
                                                    <option value="{{ $area_id }}"
                                                        @if (old('area', $customer->area_id ?? '') == $area_id) selected @endif>
                                                        {{ "$subItem" }}</option>
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
                                    <label for="outlet"> {{ __("Depo's Name") }} <span class="text-danger">*</span></label>
                                    <textarea id="outlet" name="outlet" class="form-control" placeholder="" rows="1" maxlength="500" required>
@if ($customer)
{{ old('outlet') ?? $customer->outlet }}@else{{ old('outlet') }}
@endif
</textarea>
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('outlet') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-4">
                                <div class="form-group has-feedback">
                                    <label for="name">{{ __('Customer Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $customer->name ?? '') }}" required>
                                    <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-4">
                                <div class="form-group has-feedback">
                                    <label for="phone"> {{ __('Phone Number') }}<span
                                            class="text-danger"> *</span></label>
                                    <input type="tel" class="form-control" name="phone"
                                        placeholder="{{ __('Phone number') }}"
                                        value="@if ($customer) {{ $customer->phone }}@else{{ old('phone') }} @endif">
                                         <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-4">
                            <div class="form-group has-feedback">
                                <label for="customer_type"> {{ __('Customer Type') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select customer type"></i>
                                </label>
                                {!! Form::select('customer_type', $customerType, old('customer_type', optional($customer)->customer_type), [
                                    'placeholder' => __('Select customer type'),
                                    'id' => 'customer_type',
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('customer_type') }}</span>
                            </div>
                        </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <fieldset>
                                    <legend>{{ __('Photo Outlet') }} <span class="text-danger">*</span></legend>
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
                                                            <img class="rounded mx-auto d-block @if (!old('old_outlet_photo') && !isset($customer->outlet_photo)) d-none @endif z-1"
                                                                id="outlet-photo-preview" name="old_outlet_photo"
                                                                src="@if (isset($customer->outlet_photo)) {{ asset('storage/' . $customer->outlet_photo) }} @else {{ old('old_outlet_photo') }} @endif"
                                                                alt="outlet-photo">
                                                            <input type="hidden" id="outlet-img-preview"
                                                                name="old_outlet_photo"
                                                                value="@if (isset($customer->outlet_photo)) {{ $customer->outlet_photo }} @endif">
                                                            <div class="d-flex align-items-center justify-content-center bg-transparent z-2 @if (!old('outlet-img-preview') && !isset($customer->outlet_photo)) opacity-100 @else opacity-25 @endif"
                                                                id="open-outlet-camera-btn">
                                                                <button class="btn p-3 rounded-circle"
                                                                    id="btn-upload-outlet-photo" type="button"
                                                                    data-action="open-outlet-camera">
                                                                    <i class="fa-solid fa-camera-retro"></i>
                                                                </button>
                                                            </div>
                                                            <label id="outlet-camera-label"
                                                                class="position-absolute bottom-0 text-center w-100 mb-2">
                                                                @if (isset($customer->outlet_photo) || old('outlet-img-preview'))
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
                            </div>
 <!-- Location Fields and Map -->
                        <div class="col-lg-12 col-md-12 col-xl-12">
                            <fieldset>
                                <legend>{{ __('Customer location') }}</legend>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="latitude">{{ __('Latitude') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="latitude" id="latitude"
                                                value="{{ isset($customer) ? $customer->latitude : old('latitude') }}"
                                                readonly required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="longitude">{{ __('Longitude') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="longitude" id="longitude"
                                                value="{{ isset($customer) ? $customer->longitude : old('longitude') }}"
                                                readonly required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="city">{{ __('Address') }}<span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="city" id="city" cols="30" rows="1" readonly required>{{ isset($customer) ? $customer->city : old('city') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="country">{{ __('Country') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="{{ isset($customer) ? $customer->country : old('country') }}" readonly
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

            // Existing map initialization and geolocation code (unchanged)
            let map;
            let marker;

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

            function updateMap(lat, lng) {
                if (!map) {
                    initMap(lat, lng);
                } else {
                    map.setView([lat, lng], 15);
                    marker.setLatLng([lat, lng]);
                }
            }

            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'block';
            }

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

            @if ($customer && $customer->latitude && $customer->longitude)
                window.onload = function() {
                    initMap({{ $customer->latitude }}, {{ $customer->longitude }});
                }
            @else
                window.onload = function() {
                    initMap(0, 0);
                }
            @endif

           // Handle form submission for Save and Save & Add New
            $(".submitClick").on('click', function(event) {
                event.preventDefault();

                // Handle outlet photo for camera capture
                let outletImageData = $('#outlet-img-preview').val();
                if (outletImageData && outletImageData.startsWith('data:image/')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'outlet_photo_base64',
                        value: outletImageData
                    }).appendTo('#entryForm');
                }

                

                // Handle saveandcontinue logic
                if ($(this).hasClass('submitAndContinue')) {
                    $(".boxfooter").append('<input type="hidden" name="saveandcontinue" value="1" />');
                } else {
                    $("input[name='saveandcontinue']").each(function() {
                        $(this).remove();
                    });
                }

                // Submit the form
                $("#entryForm").submit();
            });

            // Initialize Select2
            $('.select2').select2();

            // Camera and photo handling (unchanged)
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

            @if ($customer && $customer->outlet_photo)
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
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
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

            
        });
    </script>
@endsection
<!-- END PAGE JS-->
