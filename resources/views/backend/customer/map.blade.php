@extends('backend.layouts.master')

@section('pageTitle')
    {{ __('Customers Map') }}
@endsection


@section('extraStyle')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <style>
        .head-map {
            padding: 15px 2px;
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

        .leaflet-control-attribution {
            display: none;
        }

        /* =========================================================
            Customer Marker Cluster
            ========================================================= */

        .customer-cluster {
            background: rgba(27, 107, 168, 0.20);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .customer-cluster div {
            width: 36px;
            height: 36px;

            border-radius: 50%;

            background: #1B6BA8;

            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 13px;
            font-weight: 600;

            border: 3px solid rgba(255, 255, 255, 0.95);

            box-shadow:
                0 2px 6px rgba(0, 0, 0, 0.25);

            transition: all 0.2s ease;
        }


        /* Small cluster */

        .customer-cluster-small {
            background: rgba(52, 152, 219, 0.20);
        }

        .customer-cluster-small div {
            background: #3498DB;
        }


        /* Medium cluster */

        .customer-cluster-medium {
            background: rgba(27, 107, 168, 0.22);
        }

        .customer-cluster-medium div {
            background: #1B6BA8;
        }


        /* Large cluster */

        .customer-cluster-large {
            background: rgba(21, 76, 121, 0.24);
        }

        .customer-cluster-large div {
            background: #154C79;
        }


        /* Very large cluster */

        .customer-cluster-xlarge {
            background: rgba(76, 61, 139, 0.22);
        }

        .customer-cluster-xlarge div {
            background: #4C3D8B;
        }


        /* Hover */

        .customer-cluster:hover div {
            transform: scale(1.08);
            box-shadow:
                0 4px 10px rgba(0, 0, 0, 0.30);
        }

        /* Customer marker */
.customer-status-marker {
    width: 28px;
    height: 28px;
    border: 4px solid #ffffff;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

.customer-status-active {
    background: #238636;
}

.customer-status-warning {
    background: #E5A000;
}

.customer-status-overdue {
    background: #D93025;
}

.customer-status-unknown {
    background: #808080;
}

.purchase-status-label {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    color: #ffffff;
    font-size: 12px;
    font-weight: 600;
}

.purchase-status-label.active {
    background: #238636;
}

.purchase-status-label.warning {
    background: #E5A000;
}

.purchase-status-label.overdue {
    background: #D93025;
}

.purchase-status-label.unknown {
    background: #808080;
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

            <div class="card-body mt-2">

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

    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        $(document).ready(function() {

            const customers = @json($customers);

            /*
            |--------------------------------------------------------------------------
            | Map
            |--------------------------------------------------------------------------
            */

            const map = L.map('customerMap', {
                preferCanvas: true
            }).setView(
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
                    maxZoom: 19
                }
            ).addTo(map);


            /*
            |--------------------------------------------------------------------------
            | Marker Cluster
            |--------------------------------------------------------------------------
            */

            let markerLayer = L.markerClusterGroup({

                chunkedLoading: true,

                chunkInterval: 100,

                chunkDelay: 10,

                removeOutsideVisibleBounds: true,

                maxClusterRadius: 50,

                disableClusteringAtZoom: 17,

                spiderfyOnMaxZoom: true,

                showCoverageOnHover: false,

                iconCreateFunction: function(cluster) {

                    const count = cluster.getChildCount();

                    let sizeClass = 'customer-cluster-small';

                    if (count >= 10 && count < 50) {
                        sizeClass = 'customer-cluster-medium';
                    } else if (count >= 50 && count < 100) {
                        sizeClass = 'customer-cluster-large';
                    } else if (count >= 100) {
                        sizeClass = 'customer-cluster-xlarge';
                    }

                    return L.divIcon({

                        html: `
                            <div>
                                ${count}
                            </div>
                        `,

                        className: `customer-cluster ${sizeClass}`,

                        iconSize: L.point(46, 46),

                        iconAnchor: L.point(23, 23)

                    });
                }

            });

            map.addLayer(markerLayer);


            /*
            |--------------------------------------------------------------------------
            | Create Popup
            |--------------------------------------------------------------------------
            */

            function createCustomerIcon(customer) {
                const status = customer.purchase_status || 'unknown';

                return L.divIcon({
                    className: 'customer-marker-wrapper',
                    html: `
                        <div class="customer-status-marker customer-status-${status}">
                        </div>
                    `,
                    iconSize: [28, 28],
                    iconAnchor: [14, 28],
                    popupAnchor: [0, -28]
                });
            }

            
            function createPopup(customer) {

                const latitude = parseFloat(customer.latitude);
                const longitude = parseFloat(customer.longitude);

                // Purchase status
                
                const purchaseStatus = customer.purchase_status || 'unknown';

                const days = customer.days_since_purchase;
                const hours = customer.hours_since_purchase;

                let statusLabel = 'No Purchase History';

                if (purchaseStatus === 'active') {
                    statusLabel = 'Recently Purchased';
                } else if (purchaseStatus === 'warning' || purchaseStatus === 'overdue') {
                    if (days !== null && days !== undefined) {
                        statusLabel = `No Purchase for ${days} ${days === 1 ? 'Day' : 'Days'}`;
                    } else {
                        statusLabel = 'No Purchase History';
                    }
                }

                const statusClass = purchaseStatus;

                const lastPurchaseText = customer.last_purchase_at
                    ? customer.last_purchase_at
                    : 'No purchase history';

                let daysSinceText = '—';

                if (hours !== null && hours !== undefined) {
                    if (days !== null && days !== undefined) {
                        daysSinceText = `${days} ${days === 1 ? 'Day' : 'Days'}`;
                    } else {
                        daysSinceText = `${hours} Hours`;
                    }
                }


                return `
                    <div class="customer-popup">

                        <h5>
                            <strong>${customer.name ?? '-'}</strong>
                        </h5>

                        <div class="info-row">
                            <i class="fas fa-barcode"></i>
                            <strong>{{ __('Code') }}:</strong>
                            ${customer.code ?? '-'}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-phone"></i>
                            <strong>{{ __('Phone') }}:</strong>
                            ${customer.phone ?? '-'}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-user"></i>
                            <strong>{{ __('Employee') }}:</strong>
                            ${customer.user_name ?? '-'}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-map"></i>
                            <strong>{{ __('Area') }}:</strong>
                            ${customer.user_area ?? '-'}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-warehouse"></i>
                            <strong>{{ __('Depo') }}:</strong>
                            ${customer.depo_name ?? '-'}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-city"></i>
                            <strong>{{ __('Address') }}:</strong>
                            ${customer.city ?? '-'}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-globe"></i>
                            <strong>{{ __('Country') }}:</strong>
                            ${customer.country ?? '-'}
                        </div>

                        <hr>

                        <div class="info-row">
                            <i class="fas fa-shopping-cart"></i>
                            <strong>{{ __('Purchase Status') }}:</strong>
                            <span class="purchase-status-label ${statusClass}">
                                ${statusLabel}
                            </span>
                        </div>

                        <div class="info-row">
                            <i class="fas fa-calendar-alt"></i>
                            <strong>{{ __('Last Purchase') }}:</strong>
                            ${lastPurchaseText}
                        </div>

                        <div class="info-row">
                            <i class="fas fa-clock"></i>
                            <strong>{{ __('Time Since Purchase') }}:</strong>
                            ${daysSinceText}
                        </div>
                        <hr>

                        <div class="coordinates">
                            <div>
                                <strong>{{ __('Latitude') }}:</strong> ${latitude}
                            </div>
                            <div>
                                <strong>{{ __('Longitude') }}:</strong> ${longitude}
                            </div>
                        </div>

                    </div>
                `;
            }



            /*
            |--------------------------------------------------------------------------
            | Render Markers
            |--------------------------------------------------------------------------
            */

            function renderMarkers(data) {

                markerLayer.clearLayers();

                const markers = [];

                data.forEach(function(customer) {

                    const latitude = parseFloat(customer.latitude);
                    const longitude = parseFloat(customer.longitude);

                    if (
                        !Number.isFinite(latitude) ||
                        !Number.isFinite(longitude)
                    ) {
                        return;
                    }

                    const marker = L.marker(
                        [latitude, longitude],
                        {
                            icon: createCustomerIcon(customer)
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Create popup only when marker is clicked
                    |--------------------------------------------------------------------------
                    */

                    marker.bindPopup(function() {
                        return createPopup(customer);
                    });

                    markers.push(marker);
                });


                /*
                |--------------------------------------------------------------------------
                | Add all markers
                |--------------------------------------------------------------------------
                */

                markerLayer.addLayers(markers);


                /*
                |--------------------------------------------------------------------------
                | Fit bounds
                |--------------------------------------------------------------------------
                */

                if (markers.length > 0) {

                    const group = L.featureGroup(markers);

                    map.fitBounds(
                        group.getBounds(), {
                            padding: [30, 30],
                            maxZoom: 16
                        }
                    );

                } else {

                    map.setView(
                        [11.5564, 104.9282],
                        12
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Fix Leaflet size
                |--------------------------------------------------------------------------
                */

                setTimeout(function() {
                    map.invalidateSize();
                }, 200);
            }


            /*
            |--------------------------------------------------------------------------
            | Apply Filters
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const search = $('#customerSearch')
                    .val()
                    .toLowerCase()
                    .trim();

                const areaId = $('#filterArea').val();

                const userId = $('#filterUser').val();

                const depoId = $('#filterDepo').val();


                const filteredCustomers = customers.filter(function(customer) {

                    /*
                    |--------------------------------------------------------------------------
                    | Search
                    |--------------------------------------------------------------------------
                    */

                    if (search) {

                        const searchText = [
                                customer.name,
                                customer.phone,
                                customer.code
                            ]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase();

                        if (!searchText.includes(search)) {
                            return false;
                        }
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
                });


                renderMarkers(filteredCustomers);
            }


            /*
            |--------------------------------------------------------------------------
            | Initial markers
            |--------------------------------------------------------------------------
            */

            renderMarkers(customers);


            /*
            |--------------------------------------------------------------------------
            | Filter Events
            |--------------------------------------------------------------------------
            */

            $('#filterArea').on('change', applyFilters);

            $('#filterUser').on('change', applyFilters);

            $('#filterDepo').on('change', applyFilters);


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

            $('#resetMapFilter').on('click', function() {

                $('#customerSearch').val('');

                $('#filterArea').val('').trigger('change.select2');

                $('#filterUser').val('').trigger('change.select2');

                $('#filterDepo').val('').trigger('change.select2');

                renderMarkers(customers);
            });


            /*
            |--------------------------------------------------------------------------
            | Resize
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {
                map.invalidateSize();
            }, 300);

        });
    </script>
@endsection
