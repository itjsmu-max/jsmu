<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - E-PKWT JSMU</title>
  <style>
    body{margin:0;height:100vh;display:grid;place-items:center;background:#f4f6fb;font-family:Segoe UI,Roboto,Arial}
    .card{width:360px;background:#fff;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.08);padding:22px}
    .title{font-size:20px;font-weight:700;margin:0 0 8px}
    label{font-size:12px;color:#555}
    input{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px;margin:6px 0 14px}
    .btn{width:100%;padding:10px 14px;background:#3f36c8;color:#fff;border:none;border-radius:10px;cursor:pointer}
    .err{color:#b91c1c;font-size:12px;margin:-8px 0 10px}
  </style>
</head>
<body>
  <div class="card">
    <p class="title">E‑PKWT JSMU</p>
    <form method="POST" action="{{ route('doLogin') }}">
      @csrf
      <label>Username / Email</label>
      <input type="text" name="username" value="{{ old('username') }}" autofocus>
      @error('username')<div class="err">{{ $message }}</div>@enderror

      <label>Password</label>
      <input type="password" name="password">
      @error('password')<div class="err">{{ $message }}</div>@enderror

      <button class="btn" type="submit">Login</button>
    </form>
  </div>
</body>
</html>
