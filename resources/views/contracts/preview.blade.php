@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>Preview PKWT</strong>

    <div class="d-flex gap-2">
      @if(!empty($pdfUrl))
        <a class="btn btn-dark" target="_blank" href="{{ $pdfUrl }}">Download PDF</a>
      @endif
      <a class="btn btn-primary" href="{{ route('contracts.generate', $id) }}">Generate Ulang</a>
    </div>
  </div>

  @if(session('ok'))
    <div class="alert alert-success m-3">{{ session('ok') }}</div>
  @endif

  <div class="card-body p-0" style="height: calc(100vh - 220px);">
    @if(!empty($pdfUrl))
      <iframe src="{{ route('contracts.preview.pdf', $id) }}"
        style="border:0;width:100%;height:100vh;"></iframe>

    @else
      <div class="p-4">
        PDF belum tersedia. Klik <b>Generate Ulang</b> di kanan atas.
      </div>
    @endif
  </div>
</div>
@endsection
