@extends('layouts.app')
@section('title','Laporan')
@section('page_title','Laporan Kontrak')
@section('content')
<div class="card">
  <form method="get">
    <div class="grid" style="grid-template-columns:repeat(5,1fr)">
      <div>
        <label>Status</label>
        <select name="status">
          <option value="">Semua</option>
          <option value="DRAFT" {{ request('status')=='DRAFT'?'selected':'' }}>DRAFT</option>
          <option value="WAITING_SIGN" {{ request('status')=='WAITING_SIGN'?'selected':'' }}>WAITING_SIGN</option>
          <option value="SIGNED" {{ request('status')=='SIGNED'?'selected':'' }}>SIGNED</option>
        </select>
      </div>
      <div>
        <label>Project</label>
        <select name="project_id">
          <option value="">Semua</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ request('project_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div><label>Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}"></div>
      <div><label>Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}"></div>
      <div style="align-self:end"><button class="btn" type="submit">Filter</button></div>
    </div>
  </form>
  <div style="margin-top:10px">
    <a class="btn" href="{{ route('reports.contracts.export', request()->all()) }}">Export CSV</a>
  </div>
</div>

<div class="card">
  <table>
    <thead><tr>
      <th>ID</th><th>No</th><th>Pegawai</th><th>Proyek</th><th>Mulai</th><th>Selesai</th><th>Status</th><th>Gaji</th><th>Tunjangan</th>
    </tr></thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r->id }}</td>
        <td>{{ $r->contract_no }}</td>
        <td>{{ $r->full_name }}</td>
        <td>{{ $r->project_name }}</td>
        <td>{{ $r->start_date }}</td>
        <td>{{ $r->end_date }}</td>
        <td>{{ $r->status }}</td>
        <td>{{ number_format($r->base_salary,0,',','.') }}</td>
        <td>{{ number_format($r->allowance,0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div style="margin-top:10px">{{ $rows->links() }}</div>
</div>
@endsection
