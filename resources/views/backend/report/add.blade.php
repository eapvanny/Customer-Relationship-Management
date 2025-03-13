@extends('backend.layouts.master')

@section('pageTitle')
    Reports
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

        fieldset>#student-photo {
            overflow: hidden;
            cursor: pointer;
            width: 100%;
            height: 340px;
            background-color: #f5f5f5;
        }

        fieldset>#student-photo>#btn-upload-photo {
            min-width: 100px;
            min-height: 100px;
            background-color: #ddd;
            font-size: 25px;
        }

        fieldset>#photo-preview {
            height: 250px;
            width: 250px;
            position: absolute;
            object-fit: cover;
        }

        .fly_action_btn {
            z-index: 2;
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
                    {{__('Update')}}
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
                        <h1>
                            {{ __('Customer Data') }}
                            <small class="toch">
                                @if ($report)
                                    {{__('Update')}}
                                @else
                                    {{ __('Add New') }}
                                @endif
                            </small>
                        </h1>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('report.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
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
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="area"> {{ __('Area') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="area" placeholder="name"
                                    value="@if ($report) {{ $report->area }}@else{{ old('area') }} @endif"
                                    required>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('area') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="outlet"> {{ __('Outlet') }} <span class="text-danger">*</span></label>
                                <textarea name="outlet" class="form-control" placeholder="" rows="1" maxlength="500" required>@if ($report){{ old('outlet') ?? $report->outlet }}@else{{ old('outlet') }}@endif</textarea>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('outlet') }}</span>
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
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="other"> {{ __('Other') }}</label>
                                <input type="text" class="form-control" name="other" placeholder="other"
                                    value="@if ($report) {{ $report->other }}@else{{ old('other') }} @endif">
                            </div>
                        </div>
                        <fieldset>
                            <legend>{{ __('Photo Attachment') }}</legend>
                            <div class="row">
                                <div class="form-group has-feedback">
                                    <div class="row">
                                        <div class="row-span-6 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group has-feedback position-relative">
                                                <input type="file" id="photo" name="photo" style="display: none" accept="image/*">
                                                <button type="button" class="btn btn-light text-secondary fs-5 position-absolute d-none m-2 end-0 z-1" id="btn-remove-photo"><i class="fa-solid fa-trash"></i></button>
                                                <fieldset id="photo-upload" class="p-0 d-flex align-items-center justify-content-center z-0 position-relative">
                                                    <img class="rounded mx-auto d-block @if(!old('oldphoto') && !old('img-preview') && !isset($report)){{'d-none'}}@endif z-1" id="photo-preview" name="oldphoto" src="@if(optional($report)->photo){{asset('storage/' . $report->photo)}}@else{{old('oldphoto')}}@endif" alt="photo">
                                                    <input type="hidden" id="img-preview" name="oldphoto" value="@if(optional($report)->photo){{asset($report->photo)}}@endif">
                                                    <div class="d-flex align-items-center justify-content-center bg-transparent z-2  @if(!old('img-preview')){{'opacity-100'}} @else {{'opacity-25'}}@endif" id="student-photo">
                                                        <button class="btn p-3 rounded-circle" id="btn-upload-photo" type="button" onclick="" >
                                                            <i class="fa-solid fa-camera-retro"></i>
                                                        </button>
                                                    </div>
                                                    <label class="position-absolute bottom-0 text-center w-100 mb-2" for="photo">
                                                        {{__('Photo Attachment only accept jpg, png, jpeg images')}}
                                                    </label>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-12 col-xl-12">
                                                    <div class="form-group has-feedback">
                                                        <label for="posm"> {{ __('Material Type') }}
                                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="Select POSM"></i>
                                                        </label>
                                                        {!! Form::select('posm', 
                                                        [
                                                            AppHelper::UMBRELLA => __(AppHelper::MATERIAL[AppHelper::UMBRELLA]),
                                                            AppHelper::SHIRT => __(AppHelper::MATERIAL[AppHelper::SHIRT]),
                                                            AppHelper::FAN => __(AppHelper::MATERIAL[AppHelper::FAN]),
                                                            AppHelper::CALENDAR => __(AppHelper::MATERIAL[AppHelper::CALENDAR]),
                                                        ], old('posm', optional($report)->posm), [
                                                            'placeholder' => __('Select material type'),
                                                            'id' => 'posm',
                                                            'name' => 'posm',
                                                            'class' => 'form-control select2',
                                                        ]) !!}
                                                        <span class="form-control-feedback"></span>
                                                        <span class="text-danger">{{ $errors->first('department_id') }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xl-12">
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
                        <!-- Location Fields and Map -->
                        <div class="col-lg-12 col-md-12 col-xl-12">
                            <fieldset>
                                <legend>{{ __('Location') }}</legend>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="latitude">{{ __('Latitude') }}<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="latitude" id="latitude"
                                                value="{{ isset($report) ? $report->latitude : old('latitude') }}"
                                                readonly required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="longitude">{{ __('Longitude') }}<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="longitude" id="longitude"
                                                value="{{ isset($report) ? $report->longitude : old('longitude') }}"
                                                readonly required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="city">{{ __('Address') }}<span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="city" id="city" cols="30" rows="1" readonly required>{{ isset($report) ? $report->city : old('city') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="country">{{ __('Country') }}<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="{{ isset($report) ? $report->country : old('country') }}" readonly
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-xl-12 mt-3">
                                        <button type="button" class="btn btn-primary" id="getLocationBtn">
                                            <i class="fa-solid fa-location-dot"></i>{{ __('Get Location') }}
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
{{-- <script src="{{asset('js/leaflet.js')}}"></script> --}}
    <script>
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
            $('#photo-upload').on('click', function(){
			$("#photo").trigger("click")
		});
		$('#btn-remove-photo').on('click', function(){
			$("#photo").val('');
			$('#img-preview').val('');
			$("#photo-preview").removeAttr('src').addClass('d-none');
			$('#btn-remove-photo').addClass('d-none');
			$('#btn-upload-photo').removeClass('d-none');
			$('#student-photo').removeClass('opacity-25').addClass('opacity-100')
		})
		$("#photo").change(function(e) {
			var file = e.target.files[0];
			if (!file) {
				return;
			}
			var reader = new FileReader();
			reader.onload = function(event) {
				$("#photo-preview").attr("src", event.target.result);
				$('#img-preview').val(event.target.result);
				$("#photo-preview").removeClass("d-none");
				$('#btn-upload-photo').addClass('d-none');
				$('#btn-remove-photo').removeClass('d-none');
				$('#student-photo').removeClass('opacity-100').addClass('opacity-25')
			};
			reader.readAsDataURL(file);
		});

		//hide show image preview
		if ($("#photo-preview").attr("src")) {
			$('#btn-upload-photo').addClass('d-none');
			$('#btn-remove-photo').removeClass('d-none');
		} else {
			$('#btn-upload-photo').removeClass('d-none');
			$('#btn-remove-photo').addClass('d-none');
			$("#photo-preview").addClass('d-none');
		}  
        });
    </script>
@endsection
