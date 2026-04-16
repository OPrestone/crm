@extends('layouts.app')
@section('title', 'New Quote')
@section('page-title', 'New Quote')

@section('content')
<div class="page-header">
    <div>
        <h1>New Quote</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>
<form method="POST" action="{{ route('quotes.store') }}" id="quoteForm">
    @csrf
    @include('quotes._form', ['quote' => null])
</form>
@endsection
@push('scripts')
<script>
@include('quotes._form_js')
</script>
@endpush
