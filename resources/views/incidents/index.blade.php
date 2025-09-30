@extends('layouts.app')

@section('title', __('Incidents') . ' - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="bi bi-shield-exclamation text-primary me-2"></i>
                    {{ __('Company Incidents') }}
                </h1>
                <p class="lead mb-0">
                    {{ __('Monitor and manage safety incidents across your organization') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                @auth
                    <a href="{{ route('incidents.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('Report Incident') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="content-card text-center">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                <h3 class="h4 mt-2 mb-1">{{ $incidents->total() }}</h3>
                <p class="text-muted mb-0">{{ __('Total Incidents') }}</p>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="content-card text-center">
                <i class="bi bi-clock text-info" style="font-size: 2rem;"></i>
                <h3 class="h4 mt-2 mb-1">{{ $incidents->where('status', 'pending')->count() }}</h3>
                <p class="text-muted mb-0">{{ __('Pending') }}</p>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="content-card text-center">
                <i class="bi bi-search text-primary" style="font-size: 2rem;"></i>
                <h3 class="h4 mt-2 mb-1">{{ $incidents->where('status', 'investigating')->count() }}</h3>
                <p class="text-muted mb-0">{{ __('Investigating') }}</p>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="content-card text-center">
                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                <h3 class="h4 mt-2 mb-1">{{ $incidents->where('status', 'resolved')->count() }}</h3>
                <p class="text-muted mb-0">{{ __('Resolved') }}</p>
            </div>
        </div>
    </div>

    <!-- Incidents List -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">
                <i class="bi bi-list-ul me-2"></i>{{ __('Recent Incidents') }}
            </h2>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>{{ __('All Status') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="investigating">{{ __('Investigating') }}</option>
                    <option value="resolved">{{ __('Resolved') }}</option>
                    <option value="closed">{{ __('Closed') }}</option>
                </select>
            </div>
        </div>

        @forelse($incidents as $incident)
            <div class="incident-item border rounded-3 p-4 mb-3 hover-shadow">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                @switch($incident->status)
                                    @case('pending')
                                        <span class="badge status-pending">
                                            <i class="bi bi-clock me-1"></i>{{ __('Pending') }}
                                        </span>
                                        @break
                                    @case('investigating')
                                        <span class="badge status-investigating">
                                            <i class="bi bi-search me-1"></i>{{ __('Investigating') }}
                                        </span>
                                        @break
                                    @case('resolved')
                                        <span class="badge status-resolved">
                                            <i class="bi bi-check-circle me-1"></i>{{ __('Resolved') }}
                                        </span>
                                        @break
                                    @case('closed')
                                        <span class="badge status-closed">
                                            <i class="bi bi-x-circle me-1"></i>{{ __('Closed') }}
                                        </span>
                                        @break
                                @endswitch
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2">
                                    <a href="{{ route('incidents.show', $incident) }}" class="text-decoration-none text-dark">
                                        {{ $incident->title }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-2">{{ Str::limit($incident->content, 150) }}</p>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <span class="me-3">{{ $incident->location }}</span>
                                    <i class="bi bi-person me-1"></i>
                                    <span class="me-3">{{ $incident->user?->name }}</span>
                                    <i class="bi bi-calendar me-1"></i>
                                    <span>{{ $incident->occurred_at?->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
                <h3 class="h4 mt-3 mb-2">{{ __('No Incidents Found') }}</h3>
                <p class="text-muted mb-4">{{ __('Great! No safety incidents have been reported yet.') }}</p>
                @auth
                    <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('Report First Incident') }}
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($incidents->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $incidents->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
.incident-item {
    transition: all 0.3s ease;
    cursor: pointer;
}

.incident-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.hover-shadow {
    transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
</style>
@endpush
@endsection