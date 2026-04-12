<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control"
            value="{{ old('nama_toko', $toko->nama_toko ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">No HP</label>
        <input type="text" name="no_hp" class="form-control"
            value="{{ old('no_hp', $toko->no_hp ?? '') }}">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $toko->alamat ?? '') }}</textarea>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Latitude</label>
        <input type="text" name="latitude" class="form-control"
            value="{{ old('latitude', $toko->latitude ?? '') }}" readonly
            placeholder="-7.816895">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Longitude</label>
        <input type="text" name="longitude" class="form-control"
            value="{{ old('longitude', $toko->longitude ?? '') }}" readonly
            placeholder="112.011398">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Pin Lokasi Toko</label>
        <div id="toko-map" style="height: 380px; border-radius: 12px; border: 1px solid #d9dee3;"></div>
        <small class="text-muted d-block mt-2">Klik peta atau geser pin untuk menentukan lokasi toko.</small>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('tokos.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var latitudeInput = document.querySelector('input[name="latitude"]');
    var longitudeInput = document.querySelector('input[name="longitude"]');
    var mapElement = document.getElementById('toko-map');

    if (!mapElement || !latitudeInput || !longitudeInput || typeof L === 'undefined') {
        return;
    }

    var defaultLat = -7.816895;
    var defaultLng = 112.011398;
    var initialLat = parseFloat(latitudeInput.value);
    var initialLng = parseFloat(longitudeInput.value);
    var hasInitialCoordinate = !Number.isNaN(initialLat) && !Number.isNaN(initialLng);
    var centerLat = hasInitialCoordinate ? initialLat : defaultLat;
    var centerLng = hasInitialCoordinate ? initialLng : defaultLng;

    var map = L.map(mapElement).setView([centerLat, centerLng], hasInitialCoordinate ? 15 : 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([centerLat, centerLng], {
        draggable: true
    }).addTo(map);

    function updateCoordinate(lat, lng) {
        latitudeInput.value = lat.toFixed(8);
        longitudeInput.value = lng.toFixed(8);
        marker.setLatLng([lat, lng]);
    }

    marker.on('dragend', function (event) {
        var position = event.target.getLatLng();
        updateCoordinate(position.lat, position.lng);
    });

    map.on('click', function (event) {
        updateCoordinate(event.latlng.lat, event.latlng.lng);
    });

    updateCoordinate(centerLat, centerLng);

    setTimeout(function () {
        map.invalidateSize();
    }, 200);
});
</script>
