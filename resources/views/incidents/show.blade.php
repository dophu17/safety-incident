@extends('layouts.app')

@section('title', $incident->title . ' - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('incidents.index') }}" class="text-decoration-none">
                                {{ __('Incidents') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Incident Details') }}</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold mb-3">{{ $incident->title }}</h1>
                <div class="d-flex align-items-center text-muted">
                    <i class="bi bi-calendar me-2"></i>
                    <span class="me-4">{{ $incident->occurred_at?->format('M d, Y H:i') }}</span>
                    <i class="bi bi-geo-alt me-2"></i>
                    <span>{{ $incident->location }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Incidents') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="content-card">
                <!-- Status Badge -->
                <div class="mb-4">
                    @switch($incident->status ?? 'pending')
                        @case('pending')
                            <span class="badge status-pending fs-6">
                                <i class="bi bi-clock me-1"></i>{{ __('Pending Review') }}
                            </span>
                            @break
                        @case('investigating')
                            <span class="badge status-investigating fs-6">
                                <i class="bi bi-search me-1"></i>{{ __('Under Investigation') }}
                            </span>
                            @break
                        @case('resolved')
                            <span class="badge status-resolved fs-6">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Resolved') }}
                            </span>
                            @break
                        @case('closed')
                            <span class="badge status-closed fs-6">
                                <i class="bi bi-x-circle me-1"></i>{{ __('Closed') }}
                            </span>
                            @break
                        @default
                            <span class="badge status-pending fs-6">
                                <i class="bi bi-clock me-1"></i>{{ __('Pending Review') }}
                            </span>
                    @endswitch
                </div>

                <!-- Incident Description -->
                <div class="mb-4">
                    <h3 class="h5 mb-3">
                        <i class="bi bi-file-text text-primary me-2"></i>
                        {{ __('Incident Description') }}
                    </h3>
                    <div class="bg-light p-3 rounded-3">
                        <p class="mb-0">{{ $incident->content }}</p>
                    </div>
                </div>

                <!-- Images -->
                @if ($incident->images && count($incident->images) > 0)
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-camera text-primary me-2"></i>
                            {{ __('Evidence Photos') }}
                        </h3>
                        <div class="row g-3" id="imageGallery">
                            @foreach ($incident->images as $index => $img)
                                <div class="col-md-4 col-6">
                                    <div class="card border-0 shadow-sm">
                                        <img class="card-img-top" 
                                             src="{{ Storage::disk('public')->url($img) }}" 
                                             alt="{{ __('Incident image') }} {{ $index + 1 }}"
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             onclick="openImageModal('{{ Storage::disk('public')->url($img) }}')">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                @auth
                    @if(auth()->user()->role === 'manager')
                        <div class="border-top pt-4">
                            <h3 class="h5 mb-3">
                                <i class="bi bi-gear text-primary me-2"></i>
                                {{ __('Management Actions') }}
                            </h3>
                            <div class="d-flex gap-2">
                                <button class="btn btn-warning" onclick="updateStatus('investigating')">
                                    <i class="bi bi-search me-2"></i>{{ __('Start Investigation') }}
                                </button>
                                <button class="btn btn-success" onclick="updateStatus('resolved')">
                                    <i class="bi bi-check-circle me-2"></i>{{ __('Mark as Resolved') }}
                                </button>
                                <button class="btn btn-secondary" onclick="updateStatus('closed')">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Close Incident') }}
                                </button>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Incident Details -->
            <div class="content-card mb-4">
                <h3 class="h5 mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    {{ __('Incident Details') }}
                </h3>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person text-muted me-3"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Reported by') }}</small>
                                <span class="fw-semibold">{{ $incident->user?->name ?? __('Unknown') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar text-muted me-3"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Reported on') }}</small>
                                <span class="fw-semibold">{{ $incident->created_at?->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt text-muted me-3"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Location') }}</small>
                                <span class="fw-semibold">{{ $incident->location }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-muted me-3"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Occurred at') }}</small>
                                <span class="fw-semibold">{{ $incident->occurred_at?->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <h3 class="h5 mb-3">
                    <i class="bi bi-lightning text-primary me-2"></i>
                    {{ __('Quick Actions') }}
                </h3>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('incidents.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('Report New Incident') }}
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>{{ __('Print Report') }}
                    </button>
                    <button class="btn btn-outline-info" onclick="shareIncident()">
                        <i class="bi bi-share me-2"></i>{{ __('Share') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Incident Evidence') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded" alt="{{ __('Incident image') }}">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function updateStatus(status) {
    if (confirm('{{ __("Are you sure you want to update the status?") }}')) {
        // Here you would typically make an AJAX request to update the status
        console.log('Updating status to:', status);
        // For now, just show an alert
        alert('{{ __("Status update functionality will be implemented") }}');
    }
}

function shareIncident() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $incident->title }}',
            text: '{{ Str::limit($incident->content, 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('{{ __("Link copied to clipboard") }}');
        });
    }
}
</script>
@endpush
@endsection