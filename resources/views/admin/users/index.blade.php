@extends('admin.layout')

@section('title', __('admin.User Management'))
@section('page-title', __('admin.User Management'))

@section('content')
<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-3">{{ __('admin.Filters') }}</h5>
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">{{ __('admin.Search') }}</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="role" class="form-label">{{ __('messages.Role') }}</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">{{ __('admin.All Status') }}</option>
                            <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>{{ __('admin.Manager') }}</option>
                            <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>{{ __('admin.Employee') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> {{ __('admin.Search') }}
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> {{ __('admin.Clear') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('admin.All Users') }} ({{ $users->total() }} {{ __('admin.results') }})</h5>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>{{ __('admin.Create New User') }}
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('admin.Name') }}</th>
                            <th>{{ __('admin.Email') }}</th>
                            <th>{{ __('admin.Role') }}</th>
                            <th>{{ __('admin.Joined') }}</th>
                            <th>{{ __('admin.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <small class="text-muted">ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'manager')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-user-tie me-1"></i>{{ __('admin.Manager') }}
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="fas fa-user me-1"></i>{{ __('admin.Employee') }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="{{ __('messages.Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this incident?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                {{ __('admin.No users found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="stat-card p-3 text-center">
            <h4 class="text-primary">{{ $users->where('role', 'manager')->count() }}</h4>
            <p class="text-muted mb-0">{{ __('admin.Managers') }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-3 text-center">
            <h4 class="text-info">{{ $users->where('role', 'employee')->count() }}</h4>
            <p class="text-muted mb-0">{{ __('admin.Employees') }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-3 text-center">
            <h4 class="text-success">{{ $users->whereNotNull('email_verified_at')->count() }}</h4>
            <p class="text-muted mb-0">{{ __('admin.Verified') }}</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-submit form when role select changes
document.getElementById('role').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endsection

