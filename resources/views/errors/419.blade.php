<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>419 – Session Expired</title>
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/bootstrap-icons.min.css">
<style>
  body { background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .err-code { font-size: 7rem; font-weight: 900; line-height: 1; color: #8b5cf6; letter-spacing: -4px; }
  .err-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .5rem; }
  .err-sub   { color: #94a3b8; max-width: 400px; margin: 0 auto 2rem; }
</style>
</head>
<body>
<div class="text-center px-3">
  <div class="err-code">419</div>
  <div class="err-title mt-3">Session Expired</div>
  <p class="err-sub">Your session has expired or the page has been open too long. Please refresh and try again.</p>
  <div class="d-flex gap-3 justify-content-center">
    <a href="javascript:history.back()" class="btn btn-primary"><i class="bi bi-arrow-clockwise me-1"></i>Go Back & Retry</a>
    <a href="{{ url('/login') }}" class="btn btn-outline-secondary">Sign In Again</a>
  </div>
</div>
</body>
</html>
