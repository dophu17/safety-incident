@extends('admin.layout')

@section('title', __('admin.Incident Management'))
@section('page-title', __('admin.Incident Management'))

@section('content')
<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-3">{{ __('admin.Filters') }}</h5>
            <form method="GET" action="{{ route('admin.incidents.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">{{ __('admin.From Date') }}</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">{{ __('admin.To Date') }}</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="location" class="form-label">{{ __('messages.Location') }}</label>
                        <input type="text" class="form-control" id="location" name="location" 
                               value="{{ request('location') }}" placeholder="{{ __('admin.Enter location...') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> {{ __('admin.Search') }}
                            </button>
                            <a href="{{ route('admin.incidents.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> {{ __('admin.Clear') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('admin.All Incidents') }} ({{ $incidents->total() }} {{ __('admin.results') }})</h5>
            <div>
                <a href="{{ route('admin.incidents.export', request()->query()) }}" 
                   class="btn btn-success me-2">
                    <i class="fas fa-download"></i> {{ __('admin.Export CSV') }}
                </a>
                <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('admin.Create New Incident') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Incidents Table -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('admin.Title') }}</th>
                            <th>{{ __('messages.Location') }}</th>
                            <th>{{ __('admin.Severity') }}</th>
                            <th>{{ __('admin.Status') }}</th>
                            <th>{{ __('admin.Reporter') }}</th>
                            <th>{{ __('admin.Occurred At') }}</th>
                            <th>{{ __('admin.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $incident)
                        <tr>
                            <td>#{{ $incident->id }}</td>
                            <td>
                                <a href="{{ route('admin.incidents.show', $incident) }}" class="text-decoration-none">
                                    {{ Str::limit($incident->title, 40) }}
                                </a>
                            </td>
                            <td>{{ $incident->location ?: __('admin.No') }}</td>
                            <td>
                                @switch($incident->severity ?? 'low')
                                    @case('low')
                                        <span class="badge bg-info">{{ __('messages.Low') }}</span>
                                        @break
                                    @case('medium')
                                        <span class="badge bg-warning">{{ __('messages.Medium') }}</span>
                                        @break
                                    @case('high')
                                        <span class="badge bg-danger">{{ __('messages.High') }}</span>
                                        @break
                                    @case('critical')
                                        <span class="badge bg-dark">{{ __('messages.Critical') }}</span>
                                        @break
                                @endswitch
                                @if($incident->immediate_action === 'yes')
                                    <i class="fas fa-bolt text-danger ms-1" title="{{ __('messages.Immediate action required') }}"></i>
                                @endif
                            </td>
                            <td>
                                @switch($incident->status ?? 'pending')
                                    @case('pending')
                                        <span class="badge bg-warning">{{ __('admin.Pending') }}</span>
                                        @break
                                    @case('investigating')
                                        <span class="badge bg-info">{{ __('admin.Investigating') }}</span>
                                        @break
                                    @case('resolved')
                                        <span class="badge bg-success">{{ __('admin.Resolved') }}</span>
                                        @break
                                    @case('closed')
                                        <span class="badge bg-secondary">{{ __('admin.Closed') }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning">{{ __('admin.Pending') }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        {{ substr($incident->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $incident->user->name }}</div>
                                        <small class="text-muted">{{ $incident->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($incident->occurred_at)
                                    {{ \Carbon\Carbon::parse($incident->occurred_at)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Không xác định</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.incidents.show', $incident) }}" 
                                       class="btn btn-sm btn-outline-primary" title="{{ __('admin.View Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.incidents.edit', $incident) }}" 
                                       class="btn btn-sm btn-outline-warning" title="{{ __('messages.Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('incidents.destroy', $incident) }}" 
                                          class="d-inline" onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this incident?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                {{ __('admin.No incidents found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($incidents->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $incidents->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-submit form when date inputs change
document.getElementById('date_from').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('date_to').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endsection

