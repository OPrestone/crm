<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 – Page Not Found</title>
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/bootstrap-icons.min.css">
<style>
  body { background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .err-code { font-size: 7rem; font-weight: 900; line-height: 1; color: #3b82f6; letter-spacing: -4px; }
  .err-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .5rem; }
  .err-sub   { color: #94a3b8; max-width: 400px; margin: 0 auto 2rem; }
</style>
</head>
<body>
<div class="text-center px-3">
  <div class="err-code">404</div>
  <div class="err-title mt-3">Page Not Found</div>
  <p class="err-sub">The page you're looking for doesn't exist or has been moved. Check the URL or head back to the dashboard.</p>
  <div class="d-flex gap-3 justify-content-center">
    <a href="{{ url('/dashboard') }}" class="btn btn-primary"><i class="bi bi-house me-1"></i> Go to Dashboard</a>
    <a href="javascript:history.back()" class="btn btn-outline-secondary">← Go Back</a>
  </div>
</div>
</body>
</html>
