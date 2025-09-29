@extends('layouts.app')

@section('title', __('Incidents'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ __('Company Incidents') }}</h1>
    @auth
        <a href="{{ route('incidents.create') }}" class="btn btn-primary">{{ __('Report Incident') }}</a>
    @endauth
</div>

<div class="list-group">
    @forelse($incidents as $incident)
        <a href="{{ route('incidents.show', $incident) }}" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">{{ $incident->title }}</h5>
                <small class="text-muted">{{ $incident->occurred_at?->format('Y-m-d H:i') }}</small>
            </div>
            <p class="mb-1 text-truncate">{{ $incident->content }}</p>
            <small class="text-muted">{{ $incident->location }} • {{ $incident->user?->name }}</small>
        </a>
    @empty
        <div class="alert alert-info">{{ __('No incidents found.') }}</div>
    @endforelse
</div>

<div class="mt-3">{{ $incidents->links() }}</div>
@endsection


