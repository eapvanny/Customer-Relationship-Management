@extends('backend.layouts.master')

@section('pageTitle')
    Reports
@endsection

@section('bodyCssClass')
@endsection

@section('extraStyle')
    <style>
        fieldset .form-group {
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
        }

        @media (max-width: 768px) {
            .display-flex {
                display: inline-flex;
            }
        }

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
    </style>
@endsection

@php
    use App\Http\Helpers\AppHelper;
@endphp

@section('pageContent')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('report.index') }}"> {{ __('Report') }} </a></li>
            <li class="active">
                @if ($report)
                    Update
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
                            {{ __('Report') }}
                            <small>
                                @if ($report)
                                    Update
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
                                <label for="depot_stock"> {{ __('Depot Stock') }} <span
                                        class="text-danger">*</span></label>
                                <textarea name="depot_stock" class="form-control" placeholder="" rows="1" maxlength="500" required>
@if ($report)
{{ old('depot_stock') ?? $report->depot_stock }}@else{{ old('depot_stock') }}
@endif
</textarea>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('depot_stock') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="date"> {{ __('Date') }}</label>
                                <input type="date" class="form-control" name="date"
                                    value="{{ isset($report) ? $report['date'] : old('date') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-xl-6">
                            <div class="form-group has-feedback">
                                <label for="other"> {{ __('Other') }}</label>
                                <input type="text" class="form-control" name="other" placeholder="other"
                                    value="@if ($report) {{ $report->other }}@else{{ old('other') }} @endif">
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

                        <!-- Location Fields and Map -->
                        <div class="col-lg-12 col-md-12 col-xl-12">
                            <fieldset>
                                <legend>{{ __('Location') }}</legend>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="latitude"> {{ __('Latitude') }}</label>
                                            <input type="text" class="form-control" name="latitude" id="latitude"
                                                value="{{ isset($report) ? $report['latitude'] : old('latitude') }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="longitude"> {{ __('Longitude') }}</label>
                                            <input type="text" class="form-control" name="longitude" id="longitude"
                                                value="{{ isset($report) ? $report['longitude'] : old('longitude') }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="city"> {{ __('City') }}</label>
                                            <input type="text" class="form-control" name="city" id="city"
                                                value="{{ isset($report) ? $report['city'] : old('city') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xl-6">
                                        <div class="form-group has-feedback">
                                            <label for="country"> {{ __('Country') }}</label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="{{ isset($report) ? $report['country'] : old('country') }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-xl-12">
                                        <button type="button" class="btn btn-primary" id="getLocationBtn">{{ __('Get My Location') }}</button>
                                        <div id="map"></div>
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
        function initMap(lat, lng) {
            const userLocation = {
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            };
            const map = new google.maps.Map(document.getElementById('map'), {
                center: userLocation,
                zoom: 12
            });
            new google.maps.Marker({
                position: userLocation,
                map: map,
                title: $('#city').val() ? `${$('#city').val()}, ${$('#country').val()}` : 'Your Location'
            });
        }

        function handleApiError() {
            alert('Failed to load Google Maps. Please check your API key and internet connection.');
            // Fallback to a static map or default location
            const fallbackLat = 11.5621; // Phnom Penh latitude
            const fallbackLng = 104.9160; // Phnom Penh longitude
            $('#latitude').val(fallbackLat);
            $('#longitude').val(fallbackLng);
            initMap(fallbackLat, fallbackLng);
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        $('#latitude').val(latitude);
                        $('#longitude').val(longitude);
                        initMap(latitude, longitude);
                    },
                    (error) => {
                        console.error('Geolocation error:', error.message);
                        alert('Error getting location: ' + error.message);
                        const fallbackLat = 11.5621;
                        const fallbackLng = 104.9160;
                        $('#latitude').val(fallbackLat);
                        $('#longitude').val(fallbackLng);
                        initMap(fallbackLat, fallbackLng);
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
                const fallbackLat = 11.5621;
                const fallbackLng = 104.9160;
                $('#latitude').val(fallbackLat);
                $('#longitude').val(fallbackLng);
                initMap(fallbackLat, fallbackLng);
            }
        }

        // Load Google Maps API dynamically with error handling
        function loadGoogleMapsApi() {
            const apiKey = '{{ env('GOOGLE_MAPS_API_KEY') }}';
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initializeMap`;
            script.async = true;
            script.defer = true;
            script.onerror = handleApiError;
            document.head.appendChild(script);
        }

        function initializeMap() {
            @if ($report && $report->latitude && $report->longitude)
                initMap({{ $report->latitude }}, {{ $report->longitude }});
            @else
                getCurrentLocation();
            @endif
        }

        // Load the map when the document is ready
        $(document).ready(function() {
            loadGoogleMapsApi();

            $('#getLocationBtn').click(function() {
                getCurrentLocation();
            });
        });
    </script>
@endsection
