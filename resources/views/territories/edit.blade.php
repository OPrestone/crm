@extends('layouts.app')
@section('title', 'Edit Territory')
@section('page-title', 'Edit Territory')
@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $territory->name }}</h1></div>
    <a href="{{ route('territories.show', $territory) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form method="POST" action="{{ route('territories.update', $territory) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header fw-600">Territory Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $territory->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $territory->description) }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Type</label>
                            <select name="type" class="form-select">
                                @foreach(['geographic','account','industry','custom'] as $t)
                                <option value="{{ $t }}" {{ old('type',$territory->type)===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Colour</label>
                            <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $territory->color) }}" style="width:60px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-600">Assigned Reps</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($users as $u)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="user_ids[]" value="{{ $u->id }}" id="user_{{ $u->id }}" {{ in_array($u->id, old('user_ids', $territory->users->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="user_{{ $u->id }}">{{ $u->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i>Update Territory</button>
                <a href="{{ route('territories.show', $territory) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
