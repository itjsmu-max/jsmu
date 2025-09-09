@extends('layouts.app')
@section('title','Generate PKWT')

@section('content')
@if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul style="padding-left:20px;margin:0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<div class="card">
  <h3 style="margin:0 0 12px">Generate PKWT</h3>
  <form method="POST" action="{{ route('contracts.store') }}">
    @csrf
    <div class="grid">
      <div style="grid-column:span 6">
        <label>Karyawan</label>
        <select name="employee_id" required>
          <option value="">-- pilih --</option>
          @foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
        </select>
      </div>
      <div style="grid-column:span 6">
        <label>Project</label>
        <select name="project_id" required>
          <option value="">-- pilih --</option>
          @foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
        </select>
      </div>
      <div style="grid-column:span 6">
        <label>Template PKWT</label>
        <select name="template_id" required>
          @foreach($templates as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
        </select>
      </div>
      <div style="grid-column:span 3">
        <label>Mulai</label>
        <input type="date" name="start_date" required>
      </div>
      <div style="grid-column:span 3">
        <label>Selesai</label>
        <input type="date" name="end_date" required>
      </div>
      <div style="grid-column:span 3">
        <label>Gaji Pokok</label>
        <input type="number" name="base_salary" min="0" value="0">
      </div>
      <div style="grid-column:span 3">
        <label>Tunjangan</label>
        <input type="number" name="allowance" min="0" value="0">
      </div>
      <div style="grid-column:span 6">
        <label>Lokasi TTD</label>
        <input type="text" name="location" placeholder="Bogor / Jakarta / ...">
      </div>
    </div>

    <div style="margin-top:12px;display:flex;gap:10px">
      <button class="btn-primary" type="submit">Buat Draft</button>
      <a class="btn" href="{{ route('contracts.index') }}">Daftar Kontrak</a>
    </div>
  </form>
</div>
@endsection
