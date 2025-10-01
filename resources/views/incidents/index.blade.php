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
                    {{ __('My Incidents') }}
                </h1>
                <p class="lead mb-0">
                    {{ __('View and manage your reported safety incidents') }}
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

    <!-- Incidents List -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">
                <i class="bi bi-list-ul me-2"></i>{{ __('My Reported Incidents') }}
            </h2>
            <div class="text-muted">
                {{ __('Total: ') . $incidents->total() . __(' incidents') }}
            </div>
        </div>

        @forelse($incidents as $incident)
            <div class="incident-item border rounded-3 p-4 mb-3 hover-shadow">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                @if(auth()->user()->role === 'manager')
                                    <!-- Status Dropdown for Manager -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle status-badge-{{ $incident->status ?? 'pending' }}" 
                                                type="button" 
                                                id="statusDropdown{{ $incident->id }}" 
                                                data-bs-toggle="dropdown" 
                                                aria-expanded="false">
                                            @switch($incident->status ?? 'pending')
                                                @case('pending')
                                                    <i class="bi bi-clock me-1"></i>{{ __('Pending') }}
                                                    @break
                                                @case('investigating')
                                                    <i class="bi bi-search me-1"></i>{{ __('Investigating') }}
                                                    @break
                                                @case('resolved')
                                                    <i class="bi bi-check-circle me-1"></i>{{ __('Resolved') }}
                                                    @break
                                                @case('closed')
                                                    <i class="bi bi-x-circle me-1"></i>{{ __('Closed') }}
                                                    @break
                                            @endswitch
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $incident->id }}">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="updateStatus({{ $incident->id }}, 'pending'); return false;">
                                                    <i class="bi bi-clock me-2"></i>{{ __('Pending') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="updateStatus({{ $incident->id }}, 'investigating'); return false;">
                                                    <i class="bi bi-search me-2"></i>{{ __('Investigating') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="updateStatus({{ $incident->id }}, 'resolved'); return false;">
                                                    <i class="bi bi-check-circle me-2"></i>{{ __('Resolved') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="updateStatus({{ $incident->id }}, 'closed'); return false;">
                                                    <i class="bi bi-x-circle me-2"></i>{{ __('Closed') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <!-- Static Badge for Employee -->
                                    @switch($incident->status ?? 'pending')
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
                                @endif
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
                    <div class="col-lg-5 text-md-end mt-3 mt-lg-0">
                        <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>{{ __('View') }}
                            </a>
                            @can('update', $incident)
                                <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                </a>
                            @endcan
                            @can('delete', $incident)
                                <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this incident?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
                <h3 class="h4 mt-3 mb-2">{{ __('No Incidents Reported') }}</h3>
                <p class="text-muted mb-4">{{ __('You haven\'t reported any safety incidents yet.') }}</p>
                @auth
                    <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('Report Your First Incident') }}
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($incidents->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <div class="pagination-wrapper">
                        {{ $incidents->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.incident-item {
    transition: all 0.3s ease;
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

.pagination-wrapper {
    background: white;
    padding: 1rem 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
}

/* Status Badge Styles */
.status-badge-pending {
    background-color: #ffc107;
    color: #000;
    border: none;
}

.status-badge-investigating {
    background-color: #0dcaf0;
    color: #000;
    border: none;
}

.status-badge-resolved {
    background-color: #198754;
    color: #fff;
    border: none;
}

.status-badge-closed {
    background-color: #6c757d;
    color: #fff;
    border: none;
}

.status-badge-pending:hover,
.status-badge-investigating:hover,
.status-badge-resolved:hover,
.status-badge-closed:hover {
    opacity: 0.9;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination-wrapper {
        padding: 0.75rem 1rem;
    }
    
    .pagination .page-link {
        padding: 0.4rem 0.6rem;
        min-width: 35px;
        font-size: 0.9rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function updateStatus(incidentId, newStatus) {
    if (!confirm('{{ __("Are you sure you want to update the status?") }}')) {
        return;
    }

    fetch(`/incidents/${incidentId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated status
            location.reload();
        } else {
            alert('{{ __("Error updating status") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("Error updating status") }}');
    });
}
</script>
@endpush
@endsection