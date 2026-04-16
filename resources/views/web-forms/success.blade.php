<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $webForm->name }} — Thank You</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', system-ui, sans-serif; }
        .form-wrapper { max-width: 560px; margin: 80px auto; padding: 0 16px; text-align: center; }
        .form-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 20px rgba(0,0,0,.08); padding: 3rem 2.5rem; }
        .icon { font-size: 3.5rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="form-wrapper">
    <div class="form-card">
        <div class="icon">✅</div>
        <h2 class="fw-700 mb-3">Thank You!</h2>
        <p class="text-muted">{{ $successMessage }}</p>
    </div>
</div>
</body>
</html>
