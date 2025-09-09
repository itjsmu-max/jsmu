@extends('layouts.app')
@section('title','Karyawan')
@section('page_title','Karyawan')

@section('content')

{{-- alert flash --}}
@if(session('ok'))  <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if(session('error'))  <div class="alert alert-danger">{{ session('error') }}</div> @endif

{{-- Pencarian --}}
<form method="get" class="mb-3" action="{{ route('employees.index') }}">
  <div style="display:flex;gap:8px;">
    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari NIK / nama / email / telepon" style="flex:1;">
    <button class="btn">Cari</button>
    <a class="btn" href="{{ route('employees.create') }}">+ Tambah</a>
  </div>
</form>

<div class="card">
  <table class="table">
    <thead>
      <tr>
        <th style="width:60px">#</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Tempat Lahir</th>
        <th>Tanggal Lahir</th>
        <th>Email</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th style="width:140px">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $e)
        <tr>
          <td>{{ $rows->firstItem() + $i }}</td>
          <td>{{ $e->nik }}</td>
          <td>{{ $e->full_name }}</td>
          <td>{{ $e->birth_place }}</td>
          <td>{{ $e->birth_date }}</td>
          <td>{{ $e->email }}</td>
          <td>{{ $e->phone }}</td>
          <td>{{ $e->address }}</td>
          <td>
            <a class="btn btn-sm" href="{{ route('employees.edit', $e->id) }}">Edit</a>
            <form action="{{ route('employees.destroy', $e->id) }}" method="POST" style="display:inline"
                  onsubmit="return confirm('Hapus karyawan ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center">Belum ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-2">
  {{ $rows->links() }}
</div>
@endsection
