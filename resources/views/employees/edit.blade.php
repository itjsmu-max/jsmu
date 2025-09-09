@extends('layouts.app')
@section('title','Edit Karyawan')
@section('page_title','Edit Karyawan')

@section('content')
<div class="card">
  <form method="POST" action="{{ route('employees.update', $row->id) }}">
    @csrf
    @method('PUT')

    <div class="grid">
      <div>
        <label>NIK</label>
        <input name="nik" value="{{ old('nik', $row->nik) }}" required>
        @error('nik') <small class="text-red">{{ $message }}</small> @enderror
      </div>
      <div>
        <label>Nama Lengkap</label>
        <input name="full_name" value="{{ old('full_name', $row->full_name) }}" required>
        @error('full_name') <small class="text-red">{{ $message }}</small> @enderror
      </div>

      <div>
        <label>Tempat Lahir</label>
        <input name="birth_place" value="{{ old('birth_place', $row->birth_place) }}">
        @error('birth_place') <small class="text-red">{{ $message }}</small> @enderror
      </div>
      <div>
        <label>Tanggal Lahir</label>
        <input type="date" name="birth_date" value="{{ old('birth_date', $row->birth_date) }}">
        @error('birth_date') <small class="text-red">{{ $message }}</small> @enderror
      </div>

      <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $row->email) }}">
        @error('email') <small class="text-red">{{ $message }}</small> @enderror
      </div>
      <div>
        <label>Telepon</label>
        <input name="phone" value="{{ old('phone', $row->phone) }}">
        @error('phone') <small class="text-red">{{ $message }}</small> @enderror
      </div>

      <div class="col-span-2">
        <label>Alamat</label>
        <input name="address" value="{{ old('address', $row->address) }}">
        @error('address') <small class="text-red">{{ $message }}</small> @enderror
      </div>
    </div>
    <div class="form-group">
    <label>Project</label>
    <select name="project_id" class="form-control">
        <option value="">- Pilih Project -</option>
        @foreach($projects as $p)
            <option value="{{ $p->id }}" 
                @if(optional($assignment)->project_id == $p->id) selected @endif>
                {{ $p->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Posisi</label>
    <input type="text" name="position" class="form-control"
           value="{{ old('position', optional($assignment)->position) }}">
</div>

<div class="form-group">
    <label>Gaji Pokok</label>
    <input type="number" name="base_salary" class="form-control"
           value="{{ old('base_salary', optional($assignment)->base_salary) }}">
</div>


    <div style="margin-top:12px">
      <button class="btn" type="submit">Simpan</button>
      <a class="btn" href="{{ route('employees.index') }}">Batal</a>
    </div>
  </form>
</div>
@endsection
