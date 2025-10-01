@extends('admin.layout')

@section('title', __('admin.Incident Details') . ' #' . $incident->id)
@section('page-title', __('admin.Incident Details') . ' #' . $incident->id)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $incident->title }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.incidents.edit', $incident) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i>{{ __('Edit') }}
                        </a>
                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this incident?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Status Badge -->
                <div class="mb-4">
                    @switch($incident->status ?? 'pending')
                        @case('pending')
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock me-1"></i>{{ __('admin.Pending') }}
                            </span>
                            @break
                        @case('investigating')
                            <span class="badge bg-info fs-6">
                                <i class="fas fa-search me-1"></i>{{ __('admin.In Progress') }}
                            </span>
                            @break
                        @case('resolved')
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle me-1"></i>{{ __('admin.Resolved') }}
                            </span>
                            @break
                        @case('closed')
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-times-circle me-1"></i>{{ __('admin.Closed') }}
                            </span>
                            @break
                        @default
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock me-1"></i>{{ __('admin.Pending') }}
                            </span>
                    @endswitch
                </div>

                <!-- Incident Description -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-file-text me-2"></i>{{ __('admin.Description') }}
                    </h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $incident->content }}</p>
                    </div>
                </div>

                <!-- Images -->
                @if ($incident->images && count($incident->images) > 0)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-camera me-2"></i>{{ __('admin.Evidence Photos') }}
                        </h6>
                        <div class="row g-3" id="imageGallery">
                            @foreach ($incident->images as $index => $img)
                                <div class="col-md-4 col-6">
                                    <div class="card border-0 shadow-sm">
                                        <img class="card-img-top" 
                                             src="{{ asset('storage/' . $img) }}" 
                                             alt="{{ __('Incident image') }} {{ $index + 1 }}"
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             onclick="openImageModal('{{ asset('storage/' . $img) }}')">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Management Actions -->
                <div class="border-top pt-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-tasks me-2"></i>{{ __('admin.Update Status') }}
                    </h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-warning" onclick="updateStatus({{ $incident->id }}, 'investigating')">
                            <i class="fas fa-search me-2"></i>{{ __('admin.In Progress') }}
                        </button>
                        <button class="btn btn-success" onclick="updateStatus({{ $incident->id }}, 'resolved')">
                            <i class="fas fa-check-circle me-2"></i>{{ __('admin.Resolved') }}
                        </button>
                        <button class="btn btn-secondary" onclick="updateStatus({{ $incident->id }}, 'closed')">
                            <i class="fas fa-times-circle me-2"></i>{{ __('admin.Closed') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Incident Details -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('admin.Incident Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-hashtag me-1"></i>ID</small>
                    <strong>#{{ $incident->id }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-user me-1"></i>{{ __('admin.Reported By') }}</small>
                    <strong>{{ $incident->user?->name ?? 'Unknown' }}</strong>
                    <br><small class="text-muted">{{ $incident->user?->email }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i>{{ __('Location') }}</small>
                    <strong>{{ $incident->location }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-clock me-1"></i>{{ __('admin.Occurred At') }}</small>
                    <strong>{{ $incident->occurred_at?->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-calendar-plus me-1"></i>{{ __('admin.Reported At') }}</small>
                    <strong>{{ $incident->created_at?->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-calendar-check me-1"></i>{{ __('admin.Last Updated') }}</small>
                    <strong>{{ $incident->updated_at?->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-exclamation-triangle me-1"></i>{{ __('admin.Severity') }}</small>
                    @switch($incident->severity ?? 'low')
                        @case('low')
                            <span class="badge bg-info">{{ __('Low') }}</span>
                            @break
                        @case('medium')
                            <span class="badge bg-warning">{{ __('Medium') }}</span>
                            @break
                        @case('high')
                            <span class="badge bg-danger">{{ __('High') }}</span>
                            @break
                        @case('critical')
                            <span class="badge bg-dark">{{ __('Critical') }}</span>
                            @break
                    @endswitch
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block"><i class="fas fa-bolt me-1"></i>{{ __('admin.Immediate Action') }}</small>
                    @if($incident->immediate_action === 'yes')
                        <span class="badge bg-danger">{{ __('admin.Required') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('admin.Not Required') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>{{ __('admin.Actions') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.incidents.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>{{ __('admin.All Incidents') }}
                    </a>
                    <a href="{{ route('admin.incidents.edit', $incident) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>{{ __('admin.Export Data') }}
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
                <h5 class="modal-title">{{ __('admin.Evidence Photos') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded" alt="{{ __('Incident image') }}">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function updateStatus(incidentId, newStatus) {
    if (!confirm("{{ __('Are you sure you want to update the status?') }}")) {
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
            location.reload();
        } else {
            alert("{{ __('admin.Status Updated') }}");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("{{ __('Error!') }}");
    });
}
</script>
@endsection
