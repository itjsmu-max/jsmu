<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>@yield('title','E-PKWT JSMU')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root { --purple:#3f36c8; --orange:#ef6d1f; --orange-d:#e45e0a; --bg:#f4f6fb; }
    *{box-sizing:border-box} body{margin:0;font-family:Segoe UI,Roboto,Arial;background:var(--bg);}
    .topbar{height:56px;background:var(--purple);color:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 16px;}
    .brand{font-weight:700;letter-spacing:.2px}
    .container{display:flex;min-height:calc(100vh - 56px);}
    .sidebar{width:240px;background:var(--orange);padding:16px 12px;display:flex;flex-direction:column;gap:10px}
    .navbtn{display:block;background:rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:12px;padding:12px 14px;font-weight:600}
    .navbtn.active, .navbtn:hover{background:#fff;color:#111}
    .content{flex:1;padding:24px;}
    .btn{background:#111;color:#fff;border:0;border-radius:10px;padding:9px 14px;text-decoration:none;cursor:pointer}
    .card{background:#fff;border-radius:14px;box-shadow:0 4px 14px rgba(0,0,0,.06);padding:16px;margin-bottom:14px}
    table{width:100%;border-collapse:collapse;background:#fff}
    th,td{border:1px solid #e5e7eb;padding:8px 10px;font-size:14px}
    th{background:#f3f4f6;text-align:left}
    input,select{padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;width:100%}
    .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
    @media(max-width:960px){ .sidebar{display:none} .content{padding:16px} }
  </style>
  @stack('styles')

  @yield('head')
</head>
<body>
  <div class="topbar">
    <div class="brand">E‑PKWT JSMU</div>
    <form action="{{ route('logout') }}" method="POST">@csrf
      <button class="btn" type="submit">Logout</button>
    </form>
  </div>
  <div class="container">
    <aside class="sidebar">
      <a class="navbtn {{ request()->is('/') ? 'active':'' }}" href="{{ route('beranda') }}">BERANDA</a>
      <a class="navbtn {{ request()->is('master-data')||request()->is('projects*')||request()->is('employees*') ? 'active':'' }}"
   href="{{ url('/master-data') }}">MASTER DATA</a>
      <a class="navbtn {{ request()->routeIs('contracts.generate.index') ? 'active' : '' }}"
   href="{{ route('contracts.generate.index') }}">
  GENERATE PKWT
</a>

      <a class="navbtn {{ request()->is('monitoring-kontrak') ? 'active':'' }}" href="{{ route('monitoring') }}">MONITORING KONTRAK</a>
      <a class="navbtn {{ request()->is('laporan*') ? 'active':'' }}" href="{{ route('reports.contracts') }}">LAPORAN</a>
    </aside>
    <main class="content">
      <h2 style="margin:0 0 10px">@yield('page_title')</h2>
      @yield('content')
    </main>
  </div>
  @yield('scripts')
  @stack('scripts')

</body>
</html>
