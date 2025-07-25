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
            <li><a href="{{ URL::route('restaurant-import.index') }}"> {{ __('Restaurant') }} </a></li>
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
            action="@if ($report) {{ URL::Route('restaurant-import.update', $report->id) }} @else {{ URL::Route('restaurant-import.store') }} @endif"
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
                                    {{ __('Import data') }}
                                @endif
                            </small>
                        </h1>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('restaurant-import.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
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
                    <div class="row mt-3">
                        <div class="col-md-12 p-4 rounded-3 bg-light border-2 border-secondary text-center" style="border: dashed 2px">
                            <i class="fas fa-upload" style="font-size: 50px"></i>
                            <h4 class="d-block mt-4">{{ __('Import file Excel to store data') }}</h4>
                            <small class="mt-3 text-secondary d-block d-none" id="fileChosen" style="font-style: italic">

                                {{ __('You are selecting a file') }}

                            </small>
                            <label for="file" class="btn btn-success mt-3"> <i class="fas fa-search pe-2"></i> {{ __('Browse to file') }} </label>
                            <input type="file" class="d-none" name="file" id="file" class="form-control-file" accept=".xlsx, .xls, .csv">
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

        let map;
        let marker;

        // Initialize map function
        function initMap(lat = 0, lng = 0) {
            if (map) {
                map.remove(); // Remove existing map if any
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
                showLoading(); // Show loading when starting

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

                                // Populate form fields
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lon;
                                document.getElementById('city').value = city;
                                document.getElementById('country').value = country;

                                // Update map with new coordinates
                                updateMap(lat, lon);

                                console.log(`Country: ${country}, Province: ${province}, City: ${city}`);
                            } catch (error) {
                                alert('Failed to fetch location details. Please try again.');
                                console.error(error);
                            } finally {
                                hideLoading(); // Hide loading when done
                            }
                        },
                        (error) => {
                            hideLoading(); // Hide loading on error
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

        // Initialize map with default coordinates if report exists
        @if ($report && $report->latitude && $report->longitude)
            window.onload = function() {
                initMap({{ $report->latitude }}, {{ $report->longitude }});
            }
        @else
            // Initialize with a default location (e.g., center of the world)
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
            let currentFacingMode = 'user'; // Default to front camera

            @if ($report && $report->photo)
                $('#btn-upload-photo').addClass('d-none');
                $('#btn-remove-photo').removeClass('d-none');
            @endif

            function startCamera(facingMode) {
                if (navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: facingMode
                            }
                        })
                        .then(function(stream) {
                            video.srcObject = stream;
                        })
                        .catch(function(error) {
                            alert('Unable to access camera: ' + error);
                        });
                }
            }

            // Open Camera
            $('#open-camera-btn').on('click', function(e) {
                e.preventDefault();
                cameraModal.removeClass('d-none');
                startCamera(currentFacingMode);
            });

            // Switch Camera
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
            });

            $('#btn-remove-photo').on('click', function() {
                $('#photo').val('');
                $('#img-preview').val('');
                $('#photo-preview').removeAttr('src').addClass('d-none');
                $('#btn-remove-photo').addClass('d-none');
                $('#btn-upload-photo').removeClass('d-none');
            });

            $('#close-camera-btn').on('click', function(e) {
                e.preventDefault();
                stopCamera();
                cameraModal.addClass('d-none');
            });

            function stopCamera() {
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                }
                video.srcObject = null;
            }

            $('#entryForm').on('submit', function(e) {
                let imageData = $('#img-preview').val();
                if (imageData) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'photo_base64',
                        value: imageData
                    }).appendTo(this);
                }
            });

        });
    </script>
@endsection
