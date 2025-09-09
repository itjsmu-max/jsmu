@extends('layouts.app')
@section('title','Generate PKWT')
@section('page_title','Generate PKWT')
@section('content')
<div class="card">
  <p>Form pembuatan kontrak berdasarkan karyawan & template.</p>
  <form method="POST" action="{{ route('contracts.store') }}">
    @csrf
    <div class="grid">
      <div>
        <label>Karyawan</label>
        <select name="employee_id" required>
          <option value="">-- Pilih --</option>
          @foreach($employees as $e)
            <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->nik ?? '-' }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>Project</label>
        <select name="project_id" required>
          <option value="">-- Pilih --</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>Template</label>
        <select name="template_id" required>
          <option value="">-- Pilih --</option>
          @foreach($templates as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
          @endforeach
        </select>
      </div>
      <div><label>Tanggal Mulai</label><input type="date" name="start_date" required></div>
      <div><label>Tanggal Selesai</label><input type="date" name="end_date" required></div>
      <div><label>Gaji Pokok</label><input type="number" name="base_salary" min="0" value="0"></div>
      <div><label>Tunjangan</label><input type="number" name="allowance" min="0" value="0"></div>
      <div><label>Lokasi Kerja</label><input type="text" name="location"></div>
      <div><label>Nomor Kontrak (opsional)</label><input type="text" name="contract_no" placeholder="Auto jika kosong"></div>
    </div>
    <div style="margin-top:12px"><button class="btn" type="submit">Simpan & Preview</button></div>
  </form>
</div>
@endsection
