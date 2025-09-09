@extends('layouts.app')
@section('title','Projects')

@section('content')
  <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:12px">
    <h2 style="margin:0">Projects</h2>
    <a href="{{ route('projects.create') }}" class="btn">+ Tambah Project</a>
  </div>

  @if(session('ok'))
    <div style="background:#e8f7ee;border:1px solid #b7e1c2;color:#106b2f;padding:10px 12px;border-radius:8px;margin-bottom:12px">
      {{ session('ok') }}
    </div>
  @endif

  <form method="GET" action="{{ route('projects.index') }}" style="margin-bottom:12px">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari code / nama / lokasi"
           style="width:320px;padding:10px 12px;border:1px solid #ddd;border-radius:8px">
    <button class="btn" type="submit">Cari</button>
  </form>

  <div style="overflow:auto;border:1px solid #eee;border-radius:12px">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="background:#fafafa">
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee">Code</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee">Nama</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee">Lokasi</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee">Koordinat</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee">Alamat</th>
          <th style="padding:10px;border-bottom:1px solid #eee;width:140px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td style="padding:10px;border-top:1px solid #f2f2f2">{{ $r->code }}</td>
            <td style="padding:10px;border-top:1px solid #f2f2f2">{{ $r->name }}</td>
            <td style="padding:10px;border-top:1px solid #f2f2f2">{{ $r->location }}</td>
            <td style="padding:10px;border-top:1px solid #f2f2f2">
              @if($r->latitude && $r->longitude)
                {{ $r->latitude }}, {{ $r->longitude }}
              @endif
            </td>
            <td style="padding:10px;border-top:1px solid #f2f2f2">{{ $r->address }}</td>
            <td style="padding:10px;border-top:1px solid #f2f2f2">
              <a href="{{ route('projects.edit',$r->id) }}">Edit</a>
              <form method="POST" action="{{ route('projects.destroy',$r->id) }}" style="display:inline"
                    onsubmit="return confirm('Hapus project ini?')">
                @csrf
                <button type="submit" style="margin-left:8px">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:14px">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px">
    {{ $rows->links() }}
  </div>
@endsection
