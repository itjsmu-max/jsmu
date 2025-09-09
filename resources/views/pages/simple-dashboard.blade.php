@extends('layouts.app')
@section('title','Dashboard')
@section('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>#map{height:420px;border-radius:14px}</style>
@endsection
@section('content')
<div class="card">
  <h3>Stats</h3>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
    <div class="card"><div>Projects</div><div style="font-size:26px;font-weight:700">{{ $stats['projects'] }}</div></div>
    <div class="card"><div>Employees</div><div style="font-size:26px;font-weight:700">{{ $stats['employees'] }}</div></div>
    <div class="card"><div>SIGNED</div><div style="font-size:26px;font-weight:700">{{ $stats['contracts_signed'] }}</div></div>
    <div class="card"><div>H-7 / H-30</div><div style="font-size:26px;font-weight:700">{{ $stats['contracts_h7'] }} / {{ $stats['contracts_h30'] }}</div></div>
  </div>
</div>
<div class="card">
  <h3>Peta Proyek</h3>
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