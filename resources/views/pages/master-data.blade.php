@extends('layouts.app')
@section('title','Master Data')
@section('page_title','Master Data')
@section('content')
<div class="grid" style="grid-template-columns:repeat(2,1fr);gap:16px">
  <div class="card">
    <h3 style="margin:0 0 8px">Projects</h3>
    <p>Kelola data proyek, lokasi, dan koordinat.</p>
    <a class="btn" href="{{ route('projects.index') }}">Kelola Projects</a>
  </div>
  <div class="card">
    <h3 style="margin:0 0 8px">Employees</h3>
    <p>Kelola data karyawan.</p>
    <a class="btn" href="{{ route('employees.index') }}">Kelola Employees</a>
  </div>
</div>
@endsection
