@extends('layouts.app')
@section('title','Tanda Tangan Karyawan')

@section('content')
@if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger"><ul style="margin:0;padding-left:20px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card" style="margin-bottom:12px">
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a class="btn" href="{{ route('contracts.preview',$contract->id) }}">← Kembali</a>
  </div>
</div>

<div class="card">
  <h3 style="margin:0 0 12px">Tanda Tangan Karyawan — {{ $contract->emp_name }}</h3>

  @if($empSigned)
    <p style="margin-top:0">Sudah ditandatangani pada {{ $empSigned->signed_at }}.</p>
    <img src="{{ asset('storage/'.$empSigned->signature_path) }}" alt="signature" style="max-width:420px;border:1px solid #eee;border-radius:8px">
  @else
    <p style="margin:0 0 6px">Silakan tanda tangan pada kotak putih di bawah:</p>

    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-start">
      <canvas id="pad" width="600" height="220" style="border:1px solid #d1d5db;background:#fff;border-radius:10px"></canvas>

      <form id="signform" method="POST" action="{{ route('contracts.sign.employee',$contract->id) }}" style="display:flex;flex-direction:column;gap:8px">
        @csrf
        <input type="hidden" name="signature_dataurl" id="signature_dataurl">
        <label>Nama lengkap karyawan (opsional)</label>
        <input name="employee_name" placeholder="Nama sesuai KTP">
        <div style="display:flex;gap:8px">
          <button type="button" id="clear" class="btn">Bersihkan</button>
          <button type="button" id="save" class="btn-primary">Simpan Tanda Tangan</button>
        </div>
      </form>
    </div>
  @endif
</div>

@push('body')
<script>
(function(){
  const canvas = document.getElementById('pad');
  if(!canvas) return;
  const ctx = canvas.getContext('2d');

  // background watermark "JSMU" (30% opacity)
  function drawWatermark(){
    const txt = 'JSMU';
    ctx.save();
    ctx.globalAlpha = 0.3;
    ctx.translate(canvas.width/2, canvas.height/2);
    ctx.rotate(-Math.PI/12);
    ctx.font = 'bold 72px Segoe UI, Roboto, sans-serif';
    ctx.fillStyle = '#6b7280';
    ctx.textAlign = 'center';
    ctx.fillText(txt, 0, 25);
    ctx.restore();
  }

  let drawing = false, last = null;
  function start(e){ drawing=true; last = getPos(e); }
  function end(){ drawing=false; last=null; }
  function move(e){
    if(!drawing) return;
    const p = getPos(e);
    ctx.lineWidth = 2.2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#111';
    ctx.beginPath();
    ctx.moveTo(last.x,last.y);
    ctx.lineTo(p.x,p.y);
    ctx.stroke();
    last = p;
  }
  function getPos(e){
    const rect = canvas.getBoundingClientRect();
    const t = (e.touches && e.touches[0]) ? e.touches[0] : e;
    return { x: (t.clientX-rect.left)* (canvas.width/rect.width),
             y: (t.clientY-rect.top) * (canvas.height/rect.height) };
  }

  // init
  ctx.fillStyle = '#fff'; ctx.fillRect(0,0,canvas.width,canvas.height);
  drawWatermark();

  // events
  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);
  canvas.addEventListener('touchstart', (e)=>{e.preventDefault(); start(e);});
  canvas.addEventListener('touchmove',  (e)=>{e.preventDefault(); move(e);});
  canvas.addEventListener('touchend',   (e)=>{e.preventDefault(); end(e);});

  // clear + redraw watermark
  document.getElementById('clear').addEventListener('click', ()=>{
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.fillStyle = '#fff'; ctx.fillRect(0,0,canvas.width,canvas.height);
    drawWatermark();
  });

  // save as dataURL (png)
  document.getElementById('save').addEventListener('click', ()=>{
    const data = canvas.toDataURL('image/png');
    document.getElementById('signature_dataurl').value = data;
    document.getElementById('signform').submit();
  });
})();
</script>
@endpush
@endsection
