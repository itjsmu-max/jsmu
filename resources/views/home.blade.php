<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard</title></head>
<body style="font-family:system-ui;margin:32px">
  <h2>Halo, {{ data_get(session('account'), 'username', 'User') }}</h2>
  <p>Ini placeholder dashboard. Nanti kita isi menu: Master Data, Generate Kontrak, Monitoring, Laporan.</p>
  <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
</body>
</html>
