@extends('layouts.app')

@section('title', __('Report Incident'))

@section('content')
    <h1 class="h4 mb-3">{{ __('Report Safety Incident') }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incidents.store') }}" method="post" enctype="multipart/form-data" class="card p-3">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('Title') }}</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Content') }}</label>
            <textarea name="content" rows="5" class="form-control">{{ old('content') }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Location') }}</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Occurred At') }}</label>
                <input type="datetime-local" name="occurred_at" class="form-control" value="{{ old('occurred_at') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Images') }}</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">{{ __('Submit') }}</button>
            <a href="{{ route('incidents.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection


