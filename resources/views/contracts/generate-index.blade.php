@extends('layouts.app')

@section('page_title', 'Generate PKWT')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 style="margin:0">Generate PKWT</h3>

  {{-- Bulk generate --}}
  <form method="POST" action="{{ route('contracts.generate.bulk') }}" id="bulkForm">
    @csrf
    <div id="bulkHidden"></div>
    <button type="button" class="btn" onclick="submitBulk()">Bulk Generate</button>
  </form>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle" style="width:100%;border-collapse:collapse">
      <thead>
        <tr>
          <th style="width:48px"></th>
          <th>Employee</th>
          <th>Project</th>
          <th style="width:220px;text-align:right">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
          <tr>
            <td>
              <input type="checkbox" class="emp-check" value="{{ $r->employee_id }}">
            </td>
            <td>{{ $r->employee_name }}</td>
            <td>{{ $r->project_name ?? '-' }}</td>

            <td style="text-align:right">
              {{-- PREVIEW: aktif kalau sudah ada kontrak --}}
              @if(!empty($r->contract_id) && (int) $r->contract_id > 0)
                <a class="btn" href="{{ route('contracts.preview', $r->contract_id) }}">Preview</a>
              @else
                <button class="btn" type="button" disabled>Preview</button>
              @endif

              {{-- GENERATE satu orang --}}
              <form class="d-inline" method="POST" action="{{ route('contracts.generate.one') }}">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $r->employee_id }}">
                {{-- kolom2 berikut opsional; kirim kalau query listForGenerate sudah menyiapkannya --}}
                @if(!empty($r->project_name)) <input type="hidden" name="project_name" value="{{ $r->project_name }}"> @endif
                @if(!empty($r->position))     <input type="hidden" name="position"     value="{{ $r->position }}"> @endif
                <button class="btn" type="submit">Generate</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $rows->withQueryString()->links() }}
</div>

@push('scripts')
<script>
  function submitBulk(){
    const checks = Array.from(document.querySelectorAll('.emp-check:checked'));
    if (checks.length === 0) { alert('Pilih minimal satu karyawan.'); return; }

    // bersihkan container lalu isi ulang hidden input
    const holder = document.getElementById('bulkHidden');
    holder.innerHTML = '';
    checks.forEach(cb => {
      const i = document.createElement('input');
      i.type  = 'hidden';
      i.name  = 'employee_ids[]';
      i.value = cb.value;
      holder.appendChild(i);
    });

    document.getElementById('bulkForm').submit();
  }
</script>
@endpush
@endsection
