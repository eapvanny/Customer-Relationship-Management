@extends('backend.layouts.master')

@section('pageTitle')
    Live Sales Tracking
@endsection

@section('extraStyle')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    .tracking-dashboard {
        color: #263445;
    }

    .tracking-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 12px;
        padding: 18px;
        height: 130px;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .tracking-card .card-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .tracking-card h3 {
        font-size: 29px;
        font-weight: 750;
        margin: 4px 0;
    }
    .leaflet-control-attribution {
            display: none;
        }

    .tracking-card small {
        color: #818b99;
    }

    .tracking-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        background: white;
        border: 1px solid #edf0f4;
        border-radius: 13px;
        overflow: hidden;
        min-height: 610px;
    }

    .tracking-map-section {
        min-width: 0;
        position: relative;
        padding: 12px;
    }

    #liveMap {
        width: 100%;
        height: 585px;
        border-radius: 9px;
        z-index: 1;
        background: #edf2f5;
    }

    .map-toolbar {
        position: absolute;
        z-index: 500;
        top: 22px;
        left: 22px;
        right: 22px;
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        pointer-events: none;
    }

    .map-toolbar > * {
        pointer-events: auto;
    }

    .map-control {
        background: white;
        border: 1px solid #e9edf2;
        border-radius: 8px;
        padding: 9px 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        height: 40px;
        font-size: 13px;
        outline: none;
    }

    .map-search {
        width: 230px;
    }

    .map-live-indicator {
        position: absolute;
        top: 72px;
        left: 22px;
        z-index: 500;
        background: white;
        padding: 10px 14px;
        border-radius: 9px;
        font-size: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
    }

    .live-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #17b987;
        border-radius: 50%;
        margin-right: 5px;
    }

    .team-panel {
        border-left: 1px solid #edf0f4;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .team-panel-header {
        padding: 19px 16px;
        border-bottom: 1px solid #edf0f4;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .team-panel-header h5 {
        font-weight: 700;
        margin: 0;
    }

    .team-list {
        overflow-y: auto;
        max-height: 530px;
    }

    .team-member {
        display: flex;
        align-items: center;
        padding: 12px 13px;
        border-bottom: 1px solid #f0f2f5;
        cursor: pointer;
        gap: 10px;
        transition: background .2s;
    }

    .team-member:hover,
    .team-member.active {
        background: #f5f9ff;
    }

    .member-avatar {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 700;
    }

    .member-info {
        flex: 1;
        min-width: 0;
    }

    .member-info strong {
        display: block;
        font-size: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .member-info small {
        font-size: 11px;
        color: #8993a1;
    }

    .member-status {
        text-align: right;
        font-size: 11px;
        white-space: nowrap;
    }

    .status-working { color: #159b70; }
    .status-break { color: #d99b18; }
    .status-offline { color: #8993a1; }

    .team-panel-footer {
        margin-top: auto;
        padding: 15px;
        text-align: center;
        border-top: 1px solid #edf0f4;
    }

    .tracking-summary {
        background: white;
        border: 1px solid #edf0f4;
        border-radius: 12px;
        padding: 17px;
        margin-top: 16px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 15px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
    }

    .summary-item strong {
        font-size: 17px;
        margin-left: 8px;
    }

    /* Custom map markers */

    .sales-marker {
        background: transparent;
        border: none;
    }

    .sales-marker-wrapper {
        position: relative;
        width: 48px;
        height: 62px;
    }

    .sales-marker-avatar {
        width: 42px;
        height: 42px;
        position: absolute;
        left: 3px;
        top: 0;
        border: 3px solid white;
        border-radius: 50% 50% 50% 5px;
        transform: rotate(-45deg);
        box-shadow: 0 4px 12px rgba(0,0,0,.22);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sales-marker-avatar span {
        transform: rotate(45deg);
        color: white;
        font-size: 12px;
        font-weight: 800;
    }

    .sales-marker-dot {
        position: absolute;
        width: 12px;
        height: 12px;
        border: 2px solid white;
        background: inherit;
        border-radius: 50%;
        bottom: 2px;
        left: 18px;
        box-shadow: 0 2px 6px rgba(0,0,0,.2);
    }

    .map-user-popup {
        min-width: 150px;
        font-size: 12px;
    }

    @media(max-width: 1100px) {
        .tracking-layout {
            grid-template-columns: minmax(0, 1fr) 250px;
        }
    }

    @media(max-width: 767px) {
        .tracking-layout {
            grid-template-columns: 1fr;
        }

        #liveMap {
            height: 450px;
        }

        .team-panel {
            border-left: none;
            border-top: 1px solid #edf0f4;
        }

        .team-list {
            max-height: 300px;
        }

        .map-search {
            width: 150px;
        }
    }
</style>
@endsection

@section('pageContent')
<div class="container-fluid tracking-dashboard">

    {{-- Dashboard Cards --}}
    <div class="row mb-3">

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="tracking-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Active Reps</small>
                        <h3 id="activeCount">0</h3>
                        <small>Currently working</small>
                    </div>
                    <div class="card-icon"
                         style="background:#e4f8ef;color:#18a879;">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="tracking-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>On Break</small>
                        <h3 id="breakCount">0</h3>
                        <small>Currently on break</small>
                    </div>
                    <div class="card-icon"
                         style="background:#fff6df;color:#e7a51a;">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="tracking-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Not Working</small>
                        <h3 id="offlineCount">0</h3>
                        <small>Currently offline</small>
                    </div>
                    <div class="card-icon"
                         style="background:#fce8ef;color:#e65a83;">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="tracking-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Live Now</small>
                        <h3 id="liveCount">0</h3>
                        <small style="color:#18a879;">Live GPS updates</small>
                    </div>
                    <div class="card-icon"
                         style="background:#f0edff;color:#7955d9;">
                        <i class="fas fa-wave-square"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Map and Team Panel --}}
    <div class="tracking-layout">

        <div class="tracking-map-section">

            <div class="map-toolbar">

                <select id="teamFilter" class="map-control">
                    <option value="all">All Teams</option>
                    <option value="RSM">RSM</option>
                    <option value="ASM">ASM</option>
                    <option value="SUP">SUP</option>
                    <option value="EMPLOYEE">EMPLOYEE</option>
                </select>

                <select id="statusFilter" class="map-control">
                    <option value="all">All Statuses</option>
                    <option value="working">Working</option>
                    <option value="break">On Break</option>
                    <option value="offline">Offline</option>
                </select>

                <input type="search"
                       id="memberSearch"
                       class="map-control map-search"
                       placeholder="Search reps or locations">

            </div>

            <div class="map-live-indicator">
                <span class="live-dot"></span>
                <strong>Live</strong>
                <span class="ml-2">
                    <span id="updateCount">0</span> updates
                </span>
            </div>

            <div id="liveMap"></div>

        </div>

        {{-- Team Status --}}
        <div class="team-panel">

            <div class="team-panel-header">
                <h5>Team Status (<span id="teamCount">0</span>)</h5>
                <span class="text-muted">
                    <i class="fas fa-users"></i>
                </span>
            </div>

            <div class="team-list" id="teamList">
                <div class="text-center text-muted p-4">
                    Loading team members...
                </div>
            </div>

            <div class="team-panel-footer">
                <small class="text-muted">
                    Last synchronization:
                    <span id="lastSync">--</span>
                </small>
            </div>

        </div>

    </div>

    {{-- Bottom Summary --}}
    <div class="tracking-summary">

        <div class="summary-item">
            <span class="live-dot"></span>
            Working
            <strong id="summaryWorking">0</strong>
        </div>

        <div class="summary-item">
            <span style="color:#e6ad30;">●</span>
            On Break
            <strong id="summaryBreak">0</strong>
        </div>

        <div class="summary-item">
            <span style="color:#8993a1;">●</span>
            Offline
            <strong id="summaryOffline">0</strong>
        </div>

        <div class="summary-item">
            <i class="fas fa-users" style="color:#7955d9;"></i>
            Total Reps
            <strong id="summaryTotal">0</strong>
        </div>

    </div>

    {{-- History Controls --}}
    <div class="card mt-3 p-3">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="historyDate">Tracking History Date</label>
                <input type="date"
                       id="historyDate"
                       class="form-control"
                       value="{{ now()->format('Y-m-d') }}">
            </div>

            <div class="col-md-4">
                <button id="clearHistory"
                        class="btn btn-secondary mt-2">
                    <i class="fas fa-eraser"></i> Clear Routes
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('extraScript')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const markers = new Map();
            const trails = new Map();
            const members = new Map();

            let totalUpdates = 0;
            let selectedUser = null;

            const map = L.map('liveMap', {
                zoomControl: false
            }).setView([11.5564, 104.9282], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            L.control.zoom({
                position: 'topright'
            }).addTo(map);

            const colors = [
                '#2777e7',
                '#19a779',
                '#7955d9',
                '#e59a18',
                '#e65c83',
                '#788795'
            ];

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(char) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    })[char];
                });
            }

            function getInitials(name) {
                return (name || 'User')
                    .trim()
                    .split(/\s+/)
                    .slice(0, 2)
                    .map(word => word.charAt(0).toUpperCase())
                    .join('');
            }

            function getColor(userId) {
                const id = Number(userId) || 0;
                return colors[id % colors.length];
            }

            function createMarkerIcon(user) {
                const color = getColor(user.user_id);
                const initials = escapeHtml(getInitials(user.name));
                const photoUrl = user.photo || user.avatar_url || null;
                const avatarContent = photoUrl ?
                    `<img src="${escapeHtml(photoUrl)}" alt="">` :
                    `<span>${initials}</span>`;

                return L.divIcon({
                    className: 'sales-marker',
                    html: `
                    <div class="sales-marker-wrapper">
                        <div class="sales-marker-avatar"
                             style="background:${color}">
                            ${avatarContent}
                        </div>
                        <div class="sales-marker-dot"
                             style="background:${color}"></div>
                    </div>
                `,
                    iconSize: [48, 62],
                    iconAnchor: [24, 58],
                    popupAnchor: [0, -55]
                });
            }

            function getStatus(user) {
                if (user.status) {
                    return user.status.toLowerCase();
                }

                return 'working';
            }

            function getStatusLabel(status) {
                const labels = {
                    working: 'Working',
                    break: 'On Break',
                    offline: 'Offline'
                };

                return labels[status] || 'Working';
            }

            function formatLastSeen(date) {
                if (!date) return 'Unknown';

                const parsed = new Date(date);

                if (isNaN(parsed.getTime())) return 'Unknown';

                const seconds = Math.max(
                    0,
                    Math.floor((Date.now() - parsed.getTime()) / 1000)
                );

                if (seconds < 60) return seconds + ' sec ago';

                if (seconds < 3600) {
                    return Math.floor(seconds / 60) + ' min ago';
                }

                return Math.floor(seconds / 3600) + ' hr ago';
            }

            function popupHtml(user) {
                const name = document.createElement('span');
                name.textContent = user.name || 'User';

                return `
            <div class="map-user-popup">
                <strong>${name.innerHTML}</strong>
                <hr style="margin:6px 0">
                <div>${getStatusLabel(getStatus(user))}</div>
                <div>Last seen:
                    ${formatLastSeen(user.recorded_at)}
                </div>
                <div>Accuracy:
                    ${user.accuracy ?? 'N/A'} m
                </div>
            </div>
        `;
            }

            function updateMarker(data) {

                const userId = String(data.user_id);

                const lat = Number(data.latitude);
                const lng = Number(data.longitude);

                if (!Number.isFinite(lat) ||
                    !Number.isFinite(lng) ||
                    Math.abs(lat) > 90 ||
                    Math.abs(lng) > 180) {
                    return;
                }

                const user = {
                    ...members.get(userId),
                    ...data,
                    user_id: userId
                };

                user.name = data.name ||
                    data.user_name ||
                    user.name ||
                    'User';

                user.recorded_at =
                    data.recorded_at ||
                    user.recorded_at;

                members.set(userId, user);

                const point = [lat, lng];

                if (!markers.has(userId)) {

                    const marker = L.marker(point, {
                        icon: createMarkerIcon(user)
                    }).addTo(map);

                    marker.on('click', function() {
                        selectUser(userId);
                    });

                    markers.set(userId, marker);

                } else {

                    const marker = markers.get(userId);

                    marker.setLatLng(point);
                    marker.setIcon(createMarkerIcon(user));
                }

                markers.get(userId).bindPopup(popupHtml(user));

                if (!trails.has(userId)) {

                    const trail = L.polyline([], {
                        color: getColor(userId),
                        weight: 3,
                        opacity: 0.8
                    }).addTo(map);

                    trails.set(userId, trail);
                }
                const trail = trails.get(userId);

                const lastPoint = trail.getLatLngs();
                const last = lastPoint[lastPoint.length - 1];

                // Avoid duplicate GPS points.
                if (!last ||
                    last.lat !== lat ||
                    last.lng !== lng) {
                    trail.addLatLng(point);
                }

                totalUpdates++;

                document.getElementById('updateCount')
                    .textContent = totalUpdates;

                renderTeam();
                updateSummary();
            }

            function selectUser(userId) {

                selectedUser = String(userId);

                renderTeam();

                const marker = markers.get(selectedUser);

                if (marker) {
                    map.setView(marker.getLatLng(), 16);
                    marker.openPopup();
                }

                loadHistory(selectedUser);
            }

            function renderTeam() {

                const list = document.getElementById('teamList');

                const search = document.getElementById('memberSearch')
                    .value.toLowerCase();

                const team = document.getElementById('teamFilter').value;
                const statusFilter = document.getElementById('statusFilter').value;

                let html = '';
                let visible = 0;

                members.forEach(user => {

                    const status = getStatus(user);

                    const matchesSearch =
                        (user.name || '').toLowerCase().includes(search) ||
                        (user.position || '').toLowerCase().includes(search) ||
                        (user.area || '').toLowerCase().includes(search);

                    const matchesTeam =
                        team === 'all' ||
                        user.position === team;

                    const matchesStatus =
                        statusFilter === 'all' ||
                        status === statusFilter;

                    if (!matchesSearch ||
                        !matchesTeam ||
                        !matchesStatus) {
                        return;
                    }

                    visible++;
                    const color = getColor(user.user_id);
                    const activeClass =
                        selectedUser === String(user.user_id) ?
                        'active' :
                        '';

                    const name = document.createElement('span');
                    name.textContent = user.name || 'User';

                    const photoUrl = user.photo || user.avatar_url || null;
                    const avatarHtml = photoUrl ?
                        `<img src="${escapeHtml(photoUrl)}" alt="">` :
                        escapeHtml(getInitials(user.name));

                    html += `
                <div class="team-member ${activeClass}"
                     data-user-id="${escapeHtml(user.user_id)}">

                    <div class="member-avatar"
                         style="background:${color}">
                        ${avatarHtml}
                    </div>

                    <div class="member-info">
                        <strong>${name.innerHTML}</strong>
                        <small>
                            ${user.area || user.position || 'Sales Team'}
                        </small>
                    </div>

                    <div class="member-status">
                        <span class="status-${status}">
                            ${getStatusLabel(status)}
                        </span>
                        <br>
                        <small>
                            ${formatLastSeen(user.recorded_at)}
                        </small>
                    </div>
                </div>
            `;
                });
                list.innerHTML = html || `
            <div class="text-center text-muted p-4">
                No team members found.
            </div>
        `;

                list.querySelectorAll('.team-member')
                    .forEach(element => {
                        element.addEventListener('click', function() {
                            selectUser(this.dataset.userId);
                        });
                    });

                document.getElementById('teamCount').textContent = visible;
            }

            function updateSummary() {

                let working = 0;
                let onBreak = 0;
                let offline = 0;

                members.forEach(user => {
                    const status = getStatus(user);

                    if (status === 'working') working++;
                    else if (status === 'break') onBreak++;
                    else offline++;
                });

                document.getElementById('activeCount').textContent = working;
                document.getElementById('breakCount').textContent = onBreak;
                document.getElementById('offlineCount').textContent = offline;
                document.getElementById('liveCount').textContent = markers.size;

                document.getElementById('summaryWorking').textContent = working;
                document.getElementById('summaryBreak').textContent = onBreak;
                document.getElementById('summaryOffline').textContent = offline;
                document.getElementById('summaryTotal').textContent = members.size;

                document.getElementById('lastSync').textContent =
                    new Date().toLocaleTimeString();
            }
            let isLoadingActiveUsers = false;

            async function loadActiveUsers() {

                if (isLoadingActiveUsers) return;

                isLoadingActiveUsers = true;

                try {

                    const response = await fetch(
                        @json(route('live-tracking.active-users')), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Unable to load team locations');
                    }

                    const users = await response.json();

                    users.forEach(user => updateMarker(user));

                    updateSummary();

                } catch (error) {

                    console.error(error);

                    document.getElementById('trackingStatus')
                        ?.classList.add('badge-danger');

                } finally {
                    isLoadingActiveUsers = false;
                }
            }
            async function loadHistory(userId) {

                const date = document.getElementById('historyDate').value;

                try {

                    const url = @json(url('/live-tracking/history')) +
                        '/' + encodeURIComponent(userId) +
                        '?date=' + encodeURIComponent(date);

                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error('Unable to load history');
                    }

                    const points = await response.json();

                    if (!trails.has(String(userId))) {
                        return;
                    }

                    const trail = trails.get(String(userId));

                    trail.setLatLngs(
                        points.map(point => [
                            Number(point.latitude),
                            Number(point.longitude)
                        ])
                    );

                    if (points.length > 0) {
                        map.fitBounds(trail.getBounds().pad(0.2));
                    }

                } catch (error) {
                    console.error(error);
                }
            }
            // Filters
            ['teamFilter', 'statusFilter'].forEach(id => {
                document.getElementById(id)
                    .addEventListener('change', renderTeam);
            });

            document.getElementById('memberSearch')
                .addEventListener('input', renderTeam);

            document.getElementById('historyDate')
                .addEventListener('change', function() {
                    if (selectedUser) {
                        loadHistory(selectedUser);
                    }
                });

            document.getElementById('clearHistory')
                .addEventListener('click', function() {
                    trails.forEach(trail => trail.setLatLngs([]));
                });

            // Initial loading
            loadActiveUsers();
            // Laravel Echo is initialized globally in resources/js/app.js.
            const trackingStatus = document.getElementById('trackingStatus');

            if (window.Echo) {
                window.Echo.private('crm.live-location')
                    .listen('.location.updated', function(data) {
                        updateMarker(data);
                    });

                const connection = window.Echo.connector?.pusher?.connection;

                if (connection) {
                    connection.bind('connected', function() {
                        if (trackingStatus) {
                            trackingStatus.textContent = 'LIVE';
                            trackingStatus.className = 'badge badge-success ml-2';
                        }
                    });

                    connection.bind('disconnected', function() {
                        if (trackingStatus) {
                            trackingStatus.textContent = 'Disconnected';
                            trackingStatus.className = 'badge badge-warning ml-2';
                        }
                    });

                    connection.bind('error', function(error) {
                        console.error('Pusher connection error:', error);
                        if (trackingStatus) {
                            trackingStatus.textContent = 'Connection error';
                            trackingStatus.className = 'badge badge-danger ml-2';
                        }
                    });
                }
            } else {
                console.error('Laravel Echo is not initialized. Check resources/js/app.js.');
                if (trackingStatus) {
                    trackingStatus.textContent = 'Echo not loaded';
                    trackingStatus.className = 'badge badge-danger ml-2';
                }
            }
            // Fallback refresh for missed WebSocket updates.
            setInterval(loadActiveUsers, 15000);
        });
    </script>
@endsection
