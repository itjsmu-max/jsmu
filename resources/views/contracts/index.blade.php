@extends('layouts.app')
@section('title','Daftar Kontrak')

@section('content')
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
    <h3 style="margin:0">Kontrak</h3>
    <a class="btn-primary" href="{{ route('contracts.create') }}">+ Buat PKWT</a>
  </div>

  <div style="overflow:auto">
    <table style="border-collapse:collapse;width:100%">
      <thead>
        <tr style="background:#f3f4f6">
          <th style="text-align:left;padding:8px">No</th>
          <th style="text-align:left;padding:8px">Kontrak</th>
          <th style="text-align:left;padding:8px">Karyawan</th>
          <th style="text-align:left;padding:8px">Project</th>
          <th style="text-align:left;padding:8px">Periode</th>
          <th style="text-align:left;padding:8px">Status</th>
          <th style="text-align:left;padding:8px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rows as $r)
        <tr>
          <td style="padding:8px">{{ $r->id }}</td>
          <td style="padding:8px">{{ $r->contract_no }}</td>
          <td style="padding:8px">{{ $r->emp_name }}</td>
          <td style="padding:8px">{{ $r->proj_name }}</td>
          <td style="padding:8px">{{ $r->start_date }} → {{ $r->end_date }}</td>
          <td style="padding:8px">{{ $r->status }}</td>
          <td style="padding:8px">
            <a class="btn" href="{{ route('contracts.preview',$r->id) }}">Preview</a>
          </td>
        </tr>
        @empty
          <tr><td colspan="7" style="padding:12px">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px">{{ $rows->links() }}</div>
</div>
@endsection
