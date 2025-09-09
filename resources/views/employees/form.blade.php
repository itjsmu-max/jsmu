@extends('layouts.app')
@section('title', $item->id ? 'Edit Karyawan' : 'Tambah Karyawan')

@push('head')
  <style>
    .grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px }
    .input, .select{ width:100%; padding:12px; border:1px solid #ddd; border-radius:8px }
    @media(max-width:960px){ .grid{ grid-template-columns:1fr } }
  </style>
@endpush

@section('content')
  <h2 style="margin-top:0">{{ $item->id ? 'Edit Karyawan' : 'Tambah Karyawan' }}</h2>

  @if($errors->any())
    <div style="background:#fdecea;border:1px solid #f5c2c7;color:#842029;padding:10px 12px;border-radius:8px;margin-bottom:12px">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ $item->id ? route('employees.update',$item->id) : route('employees.store') }}"
        class="card" style="max-width:860px">
    @csrf

    <div class="grid">
      <label>NIK
        <input name="nik" class="input" value="{{ old('nik',$item->nik) }}" required maxlength="32">
      </label>
      <label>Nama Lengkap
        <input name="full_name" class="input" value="{{ old('full_name',$item->full_name) }}" required>
      </label>
      {{-- ... di dalam form ... --}}
<label>Project
  <select name="project_id" class="select">
    <option value="">— Pilih Project —</option>
    @foreach($projects as $p)
      <option value="{{ $p->id }}" {{ (string)old('project_id', $assignment->project_id)===(string)$p->id ? 'selected':'' }}>
        {{ $p->name }}
      </option>
    @endforeach
  </select>
</label>
<label>Jabatan
  <input name="position" class="input" value="{{ old('position', $assignment->position) }}">
</label>
<label>Tanggal Mulai
  <input type="date" name="start_date" class="input" value="{{ old('start_date', $assignment->start_date) }}">
</label>
<label>Tanggal Selesai
  <input type="date" name="end_date" class="input" value="{{ old('end_date', $assignment->end_date) }}">
</label>
<label>Gaji Pokok
  <input type="number" step="0.01" name="base_salary" class="input" value="{{ old('base_salary', $assignment->base_salary) }}">
</label>

    </div>

    <div style="margin-top:14px; display:flex; gap:8px">
      <button class="btn" type="submit">{{ $item->id ? 'Simpan Perubahan' : 'Simpan' }}</button>
      <a href="{{ route('employees.index') }}" style="align-self:center">Batal</a>
    </div>
  </form>
@endsection
