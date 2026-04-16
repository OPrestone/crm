<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $webForm->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', system-ui, sans-serif; }
        .form-wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px; }
        .form-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 20px rgba(0,0,0,.08); padding: 2.5rem; }
        .form-title { font-size: 1.4rem; font-weight: 700; margin-bottom: .5rem; }
        .btn-submit { width: 100%; padding: .75rem; font-weight: 600; border-radius: 8px; }
    </style>
</head>
<body>
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-title">{{ $webForm->name }}</div>
        @if($webForm->description)<p class="text-muted mb-4">{{ $webForm->description }}</p>@endif

        <form method="POST" action="{{ route('web_forms.public.submit', $webForm) }}">
            @csrf
            @if($errors->any())
            <div class="alert alert-danger mb-3">Please fix the errors below.</div>
            @endif

            @foreach($webForm->fields ?? [] as $field)
            <div class="mb-3">
                <label class="form-label fw-600">{{ $field['label'] }} @if($field['required'] ?? false)<span class="text-danger">*</span>@endif</label>
                @if($field['type'] === 'textarea')
                <textarea name="{{ $field['name'] }}" class="form-control @error($field['name']) is-invalid @enderror" rows="4" {{ ($field['required']??false)?'required':'' }}>{{ old($field['name']) }}</textarea>
                @else
                <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" class="form-control @error($field['name']) is-invalid @enderror" value="{{ old($field['name']) }}" {{ ($field['required']??false)?'required':'' }}>
                @endif
                @error($field['name'])<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endforeach

            <button type="submit" class="btn btn-primary btn-submit mt-2">Submit</button>
        </form>
    </div>
</div>
</body>
</html>
