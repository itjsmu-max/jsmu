@extends('layouts.app')
@section('title','Kontrak')

@section('content')
<div class="card" style="margin-bottom:12px">
  <a class="btn" href="{{ route('contracts.preview',$c->id) }}">← Kembali</a>
</div>

<div class="card">
  <h3 style="margin:0 0 10px">Kontrak: {{ $c->contract_no }}</h3>
  <div style="border:1px dashed #e5e7eb;border-radius:10px;padding:16px;background:#fff">
    {!! $html !!}
  </div>
</div>
@endsection
