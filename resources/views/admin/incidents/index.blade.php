@extends('admin.layout')

@section('title', 'Quản lý sự cố')
@section('page-title', 'Quản lý sự cố')

@section('content')
<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-3">Bộ lọc</h5>
            <form method="GET" action="{{ route('admin.incidents.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="location" class="form-label">Vị trí</label>
                        <input type="text" class="form-control" id="location" name="location" 
                               value="{{ request('location') }}" placeholder="Nhập vị trí...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.incidents.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Xóa
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
            <h5 class="mb-0">Danh sách sự cố ({{ $incidents->total() }} kết quả)</h5>
            <div>
                <a href="{{ route('admin.incidents.export', request()->query()) }}" 
                   class="btn btn-success me-2">
                    <i class="fas fa-download"></i> Xuất CSV
                </a>
                <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo sự cố mới
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
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Vị trí</th>
                            <th>Người báo cáo</th>
                            <th>Thời gian xảy ra</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $incident)
                        <tr>
                            <td>#{{ $incident->id }}</td>
                            <td>
                                <a href="{{ route('admin.incidents.show', $incident) }}" class="text-decoration-none">
                                    {{ Str::limit($incident->title, 30) }}
                                </a>
                            </td>
                            <td>{{ Str::limit($incident->content, 50) ?: 'Không có' }}</td>
                            <td>{{ $incident->location ?: 'Không xác định' }}</td>
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
                            <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.incidents.show', $incident) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.incidents.edit', $incident) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('incidents.destroy', $incident) }}" 
                                          class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự cố này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
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
                                Không có sự cố nào được tìm thấy
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

