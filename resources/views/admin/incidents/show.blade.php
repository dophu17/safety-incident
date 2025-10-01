@extends('admin.layout')

@section('title', 'Chi tiết sự cố')
@section('page-title', 'Chi tiết sự cố #' . $incident->id)

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
                            <i class="fas fa-edit me-1"></i>Sửa
                        </a>
                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự cố này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash me-1"></i>Xóa
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
                                <i class="fas fa-clock me-1"></i>Đang chờ xử lý
                            </span>
                            @break
                        @case('investigating')
                            <span class="badge bg-info fs-6">
                                <i class="fas fa-search me-1"></i>Đang điều tra
                            </span>
                            @break
                        @case('resolved')
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle me-1"></i>Đã giải quyết
                            </span>
                            @break
                        @case('closed')
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-times-circle me-1"></i>Đã đóng
                            </span>
                            @break
                        @default
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock me-1"></i>Đang chờ xử lý
                            </span>
                    @endswitch
                </div>

                <!-- Incident Description -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-file-text me-2"></i>Mô tả chi tiết
                    </h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $incident->content }}</p>
                    </div>
                </div>

                <!-- Images -->
                @if ($incident->images && count($incident->images) > 0)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-camera me-2"></i>Hình ảnh minh chứng
                        </h6>
                        <div class="row g-3" id="imageGallery">
                            @foreach ($incident->images as $index => $img)
                                <div class="col-md-4 col-6">
                                    <div class="card border-0 shadow-sm">
                                        <img class="card-img-top" 
                                             src="{{ asset('storage/' . $img) }}" 
                                             alt="Incident image {{ $index + 1 }}"
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
                        <i class="fas fa-tasks me-2"></i>Quản lý trạng thái
                    </h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-warning" onclick="updateStatus({{ $incident->id }}, 'investigating')">
                            <i class="fas fa-search me-2"></i>Bắt đầu điều tra
                        </button>
                        <button class="btn btn-success" onclick="updateStatus({{ $incident->id }}, 'resolved')">
                            <i class="fas fa-check-circle me-2"></i>Đánh dấu đã giải quyết
                        </button>
                        <button class="btn btn-secondary" onclick="updateStatus({{ $incident->id }}, 'closed')">
                            <i class="fas fa-times-circle me-2"></i>Đóng sự cố
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
                    <i class="fas fa-info-circle me-2"></i>Thông tin sự cố
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-hashtag me-1"></i>ID</small>
                    <strong>#{{ $incident->id }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-user me-1"></i>Người báo cáo</small>
                    <strong>{{ $incident->user?->name ?? 'Unknown' }}</strong>
                    <br><small class="text-muted">{{ $incident->user?->email }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i>Vị trí</small>
                    <strong>{{ $incident->location }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-clock me-1"></i>Thời gian xảy ra</small>
                    <strong>{{ $incident->occurred_at?->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block"><i class="fas fa-calendar-plus me-1"></i>Ngày báo cáo</small>
                    <strong>{{ $incident->created_at?->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block"><i class="fas fa-calendar-check me-1"></i>Cập nhật lần cuối</small>
                    <strong>{{ $incident->updated_at?->format('d/m/Y H:i') }}</strong>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Hành động nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.incidents.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>Danh sách sự cố
                    </a>
                    <a href="{{ route('admin.incidents.edit', $incident) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>In báo cáo
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
                <h5 class="modal-title">Hình ảnh minh chứng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded" alt="Incident image">
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
    if (!confirm('Bạn có chắc chắn muốn cập nhật trạng thái?')) {
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
            alert('Lỗi khi cập nhật trạng thái');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Lỗi khi cập nhật trạng thái');
    });
}
</script>
@endsection

