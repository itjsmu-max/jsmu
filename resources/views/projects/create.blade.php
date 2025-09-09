@extends('layouts.app')
@section('title','Tambah Project')
@section('page_title','Tambah Project')

@section('content')
<div class="card">
  <form method="POST" action="{{ route('projects.store') }}">
    @csrf
    <div class="grid">
      <div>
        <label>Kode</label>
        <input id="code" name="code" required>
      </div>
      <div>
        <label>Nama</label>
        <input id="name" name="name" required>
      </div>

      <div>
        <label>Lokasi (Kota/Kabupaten)</label>
        <input id="location" name="location" placeholder="Akan terisi otomatis dari peta / pencarian">
      </div>
      <div class="col-span-1">
        <label>Alamat</label>
        <input id="address" name="address" placeholder="Akan terisi otomatis dari peta / pencarian">
      </div>

      <div>
        <label>Latitude</label>
        <input id="latitude" name="latitude" type="number" step="any" placeholder="-6.2" />
      </div>
      <div>
        <label>Longitude</label>
        <input id="longitude" name="longitude" type="number" step="any" placeholder="106.8" />
      </div>
    </div>

    {{-- MAP BLOCK --}}
    <div style="margin-top:16px">
      <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
        <strong>Peta Lokasi</strong>
        <button type="button" id="btnLocate" class="btn">Gunakan lokasiku</button>
        <small style="opacity:.7">Cari lokasi via kotak pencarian di peta, lalu klik pada peta untuk set pin.</small>
      </div>
      <div id="map" style="height:420px;border-radius:12px"></div>
    </div>

    <div style="margin-top:12px">
      <button class="btn" type="submit">Simpan</button>
      <a class="btn" href="{{ route('projects.index') }}">Batal</a>
    </div>
  </form>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
@endpush

@push('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  <script>
    // elemen form
    const $lat = document.getElementById('latitude');
    const $lng = document.getElementById('longitude');
    const $address = document.getElementById('address');
    const $location = document.getElementById('location');

    // inisialisasi peta (default ke Jakarta)
    const map = L.map('map').setView([-6.1754, 106.8272], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // marker yang bisa digeser
    let marker = null;
    function placeMarker(lat, lng) {
      if (!marker) {
        marker = L.marker([lat, lng], {draggable:true}).addTo(map);
        marker.on('dragend', (e) => {
          const {lat, lng} = e.target.getLatLng();
          updateForm(lat, lng, true);
        });
      } else {
        marker.setLatLng([lat, lng]);
      }
    }

    // geocoder (search box) — Nominatim
    const geocoder = L.Control.geocoder({
      defaultMarkGeocode: false,
      placeholder: 'Cari tempat/alamat…'
    })
    .on('markgeocode', function(e) {
      const center = e.geocode.center;
      map.setView(center, 17);
      placeMarker(center.lat, center.lng);
      updateForm(center.lat, center.lng, true);
    })
    .addTo(map);

    // klik peta untuk set pin
    map.on('click', (e) => {
      const {lat, lng} = e.latlng;
      placeMarker(lat, lng);
      updateForm(lat, lng, true);
    });

    // tombol gunakan lokasiku
    document.getElementById('btnLocate').onclick = () => {
      map.locate({setView:true, maxZoom:16});
    };
    map.on('locationfound', (e) => {
      const {lat, lng} = e.latlng;
      placeMarker(lat, lng);
      updateForm(lat, lng, true);
    });

    // kalau user isi lat/lng manual, pindahkan marker juga
    [$lat, $lng].forEach(inp => {
      inp.addEventListener('change', () => {
        const lat = parseFloat($lat.value), lng = parseFloat($lng.value);
        if (!isNaN(lat) && !isNaN(lng)) {
          map.setView([lat, lng], 16);
          placeMarker(lat, lng);
          updateForm(lat, lng, false); // jangan reverse-geocode lagi
        }
      });
    });

    // isi field + reverse geocode alamat
    function updateForm(lat, lng, doReverse) {
      $lat.value = lat.toFixed(7);
      $lng.value = lng.toFixed(7);

      if (!doReverse) return;

      // reverse geocode Nominatim
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
        .then(r => r.json())
        .then(j => {
          const addr = j.address || {};
          // Lokasi (kota/kabupaten/provinsi)
          const loc = addr.city || addr.town || addr.village || addr.municipality || addr.county || addr.state || '';
          const road = addr.road || '';
          const house = addr.house_number ? (' ' + addr.house_number) : '';
          const suburb = addr.suburb ? (', ' + addr.suburb) : '';
          const display = j.display_name || '';

          $location.value = loc || $location.value;
          // Prioritaskan display_name; kalau kepanjangan, ambil ringkas
          $address.value = display || (road + house + suburb);
        })
        .catch(() => {});
    }
  </script>
@endpush
