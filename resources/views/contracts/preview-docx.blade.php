@extends('layouts.app')
@section('title','Preview PKWT')

@push('head')
<style>
  .paper{background:#fff;border:1px solid #eee;border-radius:14px;padding:24px}
  .toolbar{display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap}
  .btn{border-radius:10px;padding:9px 12px;font-weight:700;border:1px solid #ddd;background:#fff}
  .btn-primary{background:#3836a8;border:0;color:#fff}
  iframe.html-view{width:100%;min-height:70vh;border:1px solid #eee;border-radius:10px}
</style>
@endpush

@section('content')
  <h2 class="mb-3">Preview PKWT</h2>

  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if(session('err')) <div class="alert alert-danger">{{ session('err') }}</div> @endif

  <div class="toolbar">
    <a class="btn" href="{{ route('contracts.index') }}">Daftar Kontrak</a>
    <a class="btn" href="{{ route('contracts.sign.page', $contract->id) }}">Tanda Tangan Karyawan</a>
    <form action="{{ route('contracts.generate', $contract->id) }}" method="POST" style="display:inline">
      @csrf
      <button class="btn-primary" type="submit">Simpan sebagai HTML (paket)</button>
      <a class="btn" href="{{ route('contracts.docx', $contract->id) }}">
  Download DOCX
</a>

    </form>
  </div>

  <div class="paper">
    {{-- Tampilkan hasil render di dalam iframe agar style template tetap bersih --}}
    @php
      // bungkus html agar bisa ditampilkan di iframe lewat data URL:
      $src = 'data:text/html;charset=utf-8,'.rawurlencode($html);
    @endphp
    <iframe class="html-view" src="{{ $src }}"></iframe>
  </div>
@endsection
