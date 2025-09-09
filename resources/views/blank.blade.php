@extends('layouts.app')
@section('title',$title ?? 'Halaman')
@section('content')
  <div class="card">
    <h3 style="margin:0 0 8px">{{ $title ?? 'Halaman' }}</h3>
    <p>Konten belum dibuat.</p>
  </div>
@endsection
