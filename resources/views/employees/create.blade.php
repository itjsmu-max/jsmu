@extends('layouts.app')
@section('title','Tambah Karyawan')
@section('page_title','Tambah Karyawan')

@section('content')
<div class="card">
  <form method="POST" action="{{ route('employees.store') }}">
    @csrf

    <div class="grid">
      <div>
        <label>NIK</label>
        <input name="nik" value="{{ old('nik') }}" required>
        @error('nik') <small class="text-red">{{ $message }}</small> @enderror
      </div>
      <div>
        <label>Nama Lengkap</label>
        <input name="full_name" value="{{ old('full_name') }}" required>
        @error('full_name') <small class="text-red">{{ $message }}</small> @enderror
      </div>

      <div>
        <label>Tempat Lahir</label>
        <input name="birth_place" value="{{ old('birth_place') }}">
        @error('birth_place') <small class="text-red">{{ $message }}</small> @enderror
      </div>
      <div>
        <label>Tanggal Lahir</label>
        <input type="date" name="birth_date" value="{{ old('birth_date') }}">
        @error('birth_date') <small class="text-red">{{ $message }}</small> @enderror
      </div>

      <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <small class="text-red">{{ $message }}</small> @enderror
      </div>
      <div>
        <label>Telepon</label>
        <input name="phone" value="{{ old('phone') }}">
        @error('phone') <small class="text-red">{{ $message }}</small> @enderror
      </div>

      <div class="col-span-2">
        <label>Alamat</label>
        <input name="address" value="{{ old('address') }}">
        @error('address') <small class="text-red">{{ $message }}</small> @enderror
      </div>
    </div>

    <div style="margin-top:12px">
      <button class="btn" type="submit">Simpan</button>
      <a class="btn" href="{{ route('employees.index') }}">Batal</a>
    </div>
  </form>
</div>
@endsection
