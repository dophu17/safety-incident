@extends('layouts.app')

@section('title', $incident->title)

@section('content')
    <a href="{{ route('incidents.index') }}" class="btn btn-link">&larr; {{ __('Back') }}</a>
    <div class="card">
        <div class="card-body">
            <h1 class="h4">{{ $incident->title }}</h1>
            <div class="text-muted mb-2">{{ $incident->occurred_at?->format('Y-m-d H:i') }} • {{ $incident->location }}</div>
            <p>{{ $incident->content }}</p>
            @if ($incident->images)
                <div class="row g-2">
                    @foreach ($incident->images as $img)
                        <div class="col-6 col-md-3">
                            <img class="img-fluid rounded" src="{{ Storage::disk('public')->url($img) }}" alt="image">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection


