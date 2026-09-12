@extends('backend.layouts.master')

@section('pageTitle')
    {{ __('Customers Map') }}
@endsection


@section('extraStyle')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .head-map{
            padding: 8px;
        }
        .head-map .header{
            margin: 8px 8px 8px 0; 
            border-bottom: 1px solid rgb(206, 206, 208);
        }
        #customerMap {
            width: 100%;
            height: calc(87vh - 130px);
            z-index: 1;
        }

        .customer-popup {
            min-width: 280px;
            font-size: 14px;
        }

        .customer-popup h5 {
            margin: 0 0 12px 0;
            font-size: 16px;
        }

        .customer-popup .info-row {
            margin-bottom: 7px;
        }

        .customer-popup .info-row i {
            width: 18px;
            margin-right: 4px;
        }

        .customer-popup .coordinates {
            font-size: 12px;
            color: #666;
        }

        .customer-popup .view-customer {
            margin-top: 12px;
        }
        .leaflet-control-attribution{
            display: none;
        }
    </style>
@endsection

@section('pageContent')
    <div class="container-fluid">

        <div class="head-map">

            <div class="header">
                <h4 class="mb-0">
                    <i class="fas fa-map-marked-alt"></i>
                    <b>{{ __('Customer Location') }}</b>
                </h4>
            </div>

            <div class="card-body">

                {{-- FILTER --}}
                <div class="row mb-3">

                    <div class="col-md-3 d-none">
                        <label for="customerSearch">
                            {{ __('Search Customer') }}
                        </label>

                        <input type="text" id="customerSearch" class="form-control"
                            placeholder="{{ __('Name / Phone / Code') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="filterArea">
                            {{ __('Area') }}
                        </label>

                        <select id="filterArea" class="form-control select2">
                            <option value="">
                                {{ __('All Areas') }}
                            </option>

                            @foreach ($areas as $id => $area)
                                <option value="{{ $id }}">
                                    {{ $area }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filterUser">
                            {{ __('User') }}
                        </label>

                        <select id="filterUser" class="form-control select2">
                            <option value="">
                                {{ __('All Employees') }}
                            </option>

                            @foreach ($users as $id => $user)
                                <option value="{{ $id }}">
                                    {{ $user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filterDepo">
                            {{ __('Depo') }}
                        </label>

                        <select id="filterDepo" class="form-control select2">
                            <option value="">
                                {{ __('All Depo') }}
                            </option>

                            @foreach ($depos as $id => $depo)
                                <option value="{{ $id }}">
                                    {{ $depo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" id="resetMapFilter" class="btn btn-secondary w-100">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>

                </div>

                {{-- MAP --}}
                <div id="customerMap"></div>

            </div>

        </div>

    </div>
@endsection

@section('extraScript')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        $(document).ready(function() {

            const customers = @json($customers);

            /*
            |--------------------------------------------------------------------------
            | Map
            |--------------------------------------------------------------------------
            */

            const map = L.map('customerMap').setView(
                [11.5564, 104.9282],
                12
            );


            /*
            |--------------------------------------------------------------------------
            | OpenStreetMap
            |--------------------------------------------------------------------------
            */

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    // attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);


            /*
            |--------------------------------------------------------------------------
            | Marker Layer
            |--------------------------------------------------------------------------
            */

            let markerLayer = L.layerGroup().addTo(map);


            /*
            |--------------------------------------------------------------------------
            | Render Markers
            |--------------------------------------------------------------------------
            */

            function renderMarkers(data) {

                // Remove old markers
                markerLayer.clearLayers();

                const markers = [];


                data.forEach(function(customer) {

                    const latitude = parseFloat(customer.latitude);
                    const longitude = parseFloat(customer.longitude);


                    // Invalid coordinates
                    if (
                        isNaN(latitude) ||
                        isNaN(longitude)
                    ) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Customer Information
                    |--------------------------------------------------------------------------
                    */

                    const customerName = customer.name ?? '-';

                    const phone = customer.phone ?? '-';

                    const code = customer.code ?? '-';

                    const city = customer.city ?? '-';

                    const country = customer.country ?? '-';


                    /*
                    |--------------------------------------------------------------------------
                    | User
                    |--------------------------------------------------------------------------
                    */

                    let userName = customer.user
                        ? `${customer.user.family_name ?? ''} ${customer.user.name ?? ''}`.trim()
                        : '-';


                    /*
                    |--------------------------------------------------------------------------
                    | Area
                    |--------------------------------------------------------------------------
                    */

                    let area = '-';

                    if (
                        customer.user &&
                        customer.user.area
                    ) {
                        area = customer.user.area;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Depo
                    |--------------------------------------------------------------------------
                    */

                    let depo = '-';

                    if (customer.depo) {

                        depo =
                            customer.depo.name ??
                            '-';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Marker
                    |--------------------------------------------------------------------------
                    */

                    const marker = L.marker([
                        latitude,
                        longitude
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Detail URL
                    |--------------------------------------------------------------------------
                    */

                    const detailUrl =
                        "{{ url('/customers') }}/" + customer.id;


                    /*
                    |--------------------------------------------------------------------------
                    | Popup
                    |--------------------------------------------------------------------------
                    */

                    const popup = `
                <div class="customer-popup">

                    <h5>
                        <strong>
                            ${customerName}
                        </strong>
                    </h5>

                    <div class="info-row">
                        <i class="fas fa-barcode"></i>
                        <strong>{{ __('Code') }}:</strong>
                        ${code}
                    </div>

                    <div class="info-row">
                        <i class="fas fa-phone"></i>
                        <strong>{{ __('Phone') }}:</strong>
                        ${phone}
                    </div>

                    <div class="info-row">
                        <i class="fas fa-user"></i>
                        <strong>{{ __('Employee') }}:</strong>
                        ${userName}
                    </div>

                    <div class="info-row">
                        <i class="fas fa-map"></i>
                        <strong>{{ __('Area') }}:</strong>
                        ${area}
                    </div>

                    <div class="info-row">
                        <i class="fas fa-warehouse"></i>
                        <strong>{{ __('Depo') }}:</strong>
                        ${depo}
                    </div>

                    <div class="info-row">
                        <i class="fas fa-city"></i>
                        <strong>{{ __('City') }}:</strong>
                        ${city}
                    </div>

                    <div class="info-row">
                        <i class="fas fa-globe"></i>
                        <strong>{{ __('Country') }}:</strong>
                        ${country}
                    </div>

                    <hr>

                    <div class="coordinates">

                        <div>
                            <strong>Latitude:</strong>
                            ${latitude}
                        </div>

                        <div>
                            <strong>Longitude:</strong>
                            ${longitude}
                        </div>

                    </div>

                    
                </div>
            `;
                    // <div class="view-customer">

                    //     <a
                    //         href="${detailUrl}"
                    //         class="btn btn-sm btn-primary"
                    //     >
                    //         <i class="fas fa-eye"></i>
                    //         {{ __('View Customer') }}
                    //     </a>

                    // </div>


                    marker.bindPopup(popup);


                    /*
                    |--------------------------------------------------------------------------
                    | Add Marker
                    |--------------------------------------------------------------------------
                    */

                    markerLayer.addLayer(marker);


                    markers.push([
                        latitude,
                        longitude
                    ]);

                });


                /*
                |--------------------------------------------------------------------------
                | Fit Map
                |--------------------------------------------------------------------------
                */

                if (markers.length > 0) {

                    const bounds = L.latLngBounds(markers);

                    map.fitBounds(
                        bounds, {
                            padding: [30, 30]
                        }
                    );

                } else {

                    // No result
                    map.setView(
                        [11.5564, 104.9282],
                        12
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Fix Map Size
                |--------------------------------------------------------------------------
                */

                setTimeout(function() {

                    map.invalidateSize();

                }, 300);

            }


            /*
            |--------------------------------------------------------------------------
            | Initial Markers
            |--------------------------------------------------------------------------
            */

            renderMarkers(customers);


            /*
            |--------------------------------------------------------------------------
            | Apply Filter
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const search =
                    $('#customerSearch')
                    .val()
                    .toLowerCase()
                    .trim();


                const areaId =
                    $('#filterArea').val();


                const userId =
                    $('#filterUser').val();


                const depoId =
                    $('#filterDepo').val();


                const filteredCustomers = customers.filter(
                    function(customer) {


                        /*
                        |--------------------------------------------------------------------------
                        | Search
                        |--------------------------------------------------------------------------
                        */

                        const searchText = [
                                customer.name,
                                customer.phone,
                                customer.code
                            ]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase();


                        if (
                            search &&
                            !searchText.includes(search)
                        ) {
                            return false;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Area
                        |--------------------------------------------------------------------------
                        */

                        if (
                            areaId &&
                            String(customer.area_id) !== String(areaId)
                        ) {
                            return false;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | User
                        |--------------------------------------------------------------------------
                        */

                        if (
                            userId &&
                            String(customer.user_id) !== String(userId)
                        ) {
                            return false;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Depo
                        |--------------------------------------------------------------------------
                        */

                        if (
                            depoId &&
                            String(customer.depo_id) !== String(depoId)
                        ) {
                            return false;
                        }


                        return true;

                    }
                );


                renderMarkers(filteredCustomers);

            }


            /*
            |--------------------------------------------------------------------------
            | Filter Events
            |--------------------------------------------------------------------------
            */

            $('#filterArea').on(
                'change',
                applyFilters
            );

            $('#filterUser').on(
                'change',
                applyFilters
            );

            $('#filterDepo').on(
                'change',
                applyFilters
            );


            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            $('#customerSearch').on(
                'keyup',
                applyFilters
            );


            /*
            |--------------------------------------------------------------------------
            | Reset
            |--------------------------------------------------------------------------
            */

            $('#resetMapFilter').on('click', function () {

                $('#customerSearch').val('');

                $('#filterArea').val('');
                $('#filterUser').val('');
                $('#filterDepo').val('');

                // Refresh Select2 display only
                $('#filterArea').trigger('change.select2');
                $('#filterUser').trigger('change.select2');
                $('#filterDepo').trigger('change.select2');

                // Reset map markers
                renderMarkers(customers);
            });


            /*
            |--------------------------------------------------------------------------
            | Initial Map Resize
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {

                map.invalidateSize();

            }, 300);

        });
    </script>
@endsection
