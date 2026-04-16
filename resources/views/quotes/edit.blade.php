@extends('layouts.app')
@section('title', 'Edit Quote')
@section('page-title', 'Edit Quote')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit: {{ $quote->quote_number }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<form method="POST" action="{{ route('quotes.update', $quote) }}" id="quoteForm">
    @csrf @method('PUT')
    @include('quotes._form', ['quote' => $quote])
</form>
@endsection
@push('scripts')
<script>
@include('quotes._form_js')
</script>
@endpush
