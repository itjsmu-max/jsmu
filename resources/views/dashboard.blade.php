@extends('layouts.app')
@section('title','Beranda')
@section('page_title','Beranda')
@section('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>.kpi{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.kpi .card .val{font-size:28px;font-weight:700;margin-top:4px}
#map{height:420px;border-radius:12px}</style>
@endsection
@section('content')
<div class="kpi">
  <div class="card"><div>Projects</div><div class="val">{{ $stats['projects'] }}</div></div>
  <div class="card"><div>Employees</div><div class="val">{{ $stats['employees'] }}</div></div>
  <div class="card"><div>Contracts (SIGNED)</div><div class="val">{{ $stats['contracts_signed'] }}</div></div>
  <div class="card"><div>Kontrak H‑7 / H‑30</div><div class="val">{{ $stats['contracts_h7'] }} / {{ $stats['contracts_h30'] }}</div></div>
</div>
<div class="card">
  <h3 style="margin:8px 0 12px">Peta Proyek</h3>
  <div id="map"></div>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([-6.2,106.8], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:18}).addTo(map);
fetch("{{ route('map.projects') }}").then(r=>r.json()).then(j=>{
  if(!j.ok) return;
  j.data.forEach(p=>{
    if(p.latitude && p.longitude){
      L.marker([p.latitude, p.longitude]).addTo(map).bindPopup(`<b>${p.name}</b><br/>${p.address||''}`);
    }
  });
});
</script>
@endsection
@push('scripts')
<script>
  // data marker dari controller
  const PROJECT_POINTS = {!! $projectPointsJson ?? '[]' !!};

  // kalau kamu sudah punya variabel `map` untuk Leaflet, lanjutkan di bawah:
  const markers = [];

  PROJECT_POINTS.forEach(p => {
    if (!p.lat || !p.lng) return;

    const popupHtml = `
      <div style="min-width:220px">
        <strong>${p.name}</strong><br>
        <div style="margin-top:6px">
          Employees: <b>${p.employees}</b><br>
          Kontrak berakhir ≤ 7 hari: <b>${p.ending7}</b><br>
          Kontrak berakhir ≤ 30 hari: <b>${p.ending30}</b>
        </div>
      </div>
    `;

    const m = L.marker([p.lat, p.lng]).addTo(map).bindPopup(popupHtml);
    markers.push(m);
  });

  // fit map ke semua marker (kalau ada)
  if (markers.length) {
    const group = L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.2));
  }
</script>
@endpush

