@extends('layouts.app')
@section('title', $item->id ? 'Edit Project' : 'Tambah Project')

@push('head')
  {{-- Leaflet core --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
  {{-- Leaflet Geocoder (Nominatim) --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
  <style>
    #pickmap{ width:100%; height:380px; border:1px solid #eee; border-radius:12px; overflow:hidden; }
    .grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px }
    .input{ width:100%; padding:12px; border:1px solid #ddd; border-radius:8px }
    .toolbar{ display:flex; gap:8px; align-items:center; margin:10px 0 }
    .btn{ appearance:none; border:0; background:#f05a2b; color:#fff; padding:10px 14px; border-radius:8px; font-weight:700; cursor:pointer }
    .btn.secondary{ background:#e9e9e9; color:#111 }
    .muted{ color:#666; font-size:13px }
    @media(max-width:960px){ .grid{ grid-template-columns:1fr } }
  </style>
@endpush

@section('content')
  <h2 style="margin-top:0">{{ $item->id ? 'Edit Project' : 'Tambah Project' }}</h2>

  @if($errors->any())
    <div style="background:#fdecea;border:1px solid #f5c2c7;color:#842029;padding:10px 12px;border-radius:8px;margin-bottom:12px">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ $item->id ? route('projects.update',$item->id) : route('projects.store') }}"
        class="card" style="max-width:860px">
    @csrf

    <div class="grid">
      <label>Code
        <input name="code" class="input" value="{{ old('code',$item->code) }}" required maxlength="16">
      </label>
      <label>Nama
        <input name="name" class="input" value="{{ old('name',$item->name) }}" required>
      </label>
      <label>Latitude
        <input id="lat" name="latitude" class="input" value="{{ old('latitude',$item->latitude) }}" placeholder="-6.2">
      </label>
      <label>Longitude
        <input id="lng" name="longitude" class="input" value="{{ old('longitude',$item->longitude) }}" placeholder="106.8">
      </label>
      <label>Lokasi (kota/provinsi)
        <input name="location" class="input" value="{{ old('location',$item->location) }}">
      </label>
      <label>Alamat
        <input name="address" class="input" value="{{ old('address',$item->address) }}">
      </label>
    </div>

    <div class="toolbar">
      <span class="muted">Tip: klik peta untuk mengisi koordinat, atau cari lokasi.</span>
      <button type="button" id="btnMyLoc" class="btn secondary" title="Gunakan lokasi saya">Gunakan lokasiku</button>
    </div>

    <div id="pickmap"></div>

    <div style="margin-top:14px; display:flex; gap:8px">
      <button class="btn" type="submit">{{ $item->id ? 'Simpan Perubahan' : 'Simpan' }}</button>
      <a href="{{ route('projects.index') }}" style="align-self:center">Batal</a>
    </div>
  </form>
@endsection

@push('body')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
          integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  {{-- Geocoder (Nominatim via leaflet-control-geocoder) --}}
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  <script>
    const latEl = document.getElementById('lat');
    const lngEl = document.getElementById('lng');
    const startLat = parseFloat(latEl.value) || -2.5;
    const startLng = parseFloat(lngEl.value) || 118.0;
    const startZoom = (latEl.value && lngEl.value) ? 12 : 5;

    const map = L.map('pickmap').setView([startLat, startLng], startZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;
    function setPoint(lat, lng, pan=true){
      if (marker) marker.setLatLng([lat,lng]); else marker = L.marker([lat,lng]).addTo(map);
      latEl.value = lat.toFixed(7);
      lngEl.value = lng.toFixed(7);
      if (pan) map.setView([lat,lng], 14);
    }
    if (latEl.value && lngEl.value) setPoint(startLat,startLng,false);

    // Klik peta -> isi koordinat
    map.on('click', e => setPoint(e.latlng.lat, e.latlng.lng));

    // Geocoder (search box di kiri atas peta)
    const geocoder = L.Control.geocoder({
      defaultMarkGeocode: false,
      placeholder: 'Cari tempat/alamat...',
      errorMessage: 'Tidak ditemukan'
    })
    .on('markgeocode', function(e) {
      const c = e.geocode.center;
      setPoint(c.lat, c.lng, true);
    })
    .addTo(map);

    // Gunakan lokasiku (HTML5 geolocation)
    document.getElementById('btnMyLoc')?.addEventListener('click', () => {
      if (!navigator.geolocation) return alert('Peramban tidak mendukung geolokasi.');
      navigator.geolocation.getCurrentPosition(
        pos => setPoint(pos.coords.latitude, pos.coords.longitude, true),
        err => alert('Gagal memperoleh lokasi: ' + err.message),
        { enableHighAccuracy:true, timeout:10000 }
      );
    });
  </script>
@endpush
