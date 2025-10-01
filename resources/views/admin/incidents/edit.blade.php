@extends('admin.layout')

@section('title', 'Chỉnh sửa sự cố')
@section('page-title', 'Chỉnh sửa sự cố #' . $incident->id)

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Cập nhật thông tin sự cố
                </h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Vui lòng sửa các lỗi sau:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('incidents.update', $incident) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                            </h6>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Tiêu đề sự cố <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $incident->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                            <textarea name="content" rows="5" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $incident->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Location and Time -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Vị trí & Thời gian
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vị trí <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                   value="{{ old('location', $incident->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian xảy ra <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" 
                                   value="{{ old('occurred_at', $incident->occurred_at?->format('Y-m-d\TH:i')) }}" required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Images -->
                    @if ($incident->images && count($incident->images) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-images me-2"></i>Hình ảnh hiện tại
                                </h6>
                            </div>
                            <div class="col-12">
                                <div class="row g-3" id="currentImages">
                                    @foreach ($incident->images as $index => $img)
                                        <div class="col-md-3 col-6">
                                            <div class="card border">
                                                <img class="card-img-top" 
                                                     src="{{ asset('storage/' . $img) }}" 
                                                     alt="Incident image {{ $index + 1 }}"
                                                     style="height: 150px; object-fit: cover;">
                                                <div class="card-body p-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="delete_images[]" value="{{ $img }}" 
                                                               id="delete-{{ $index }}">
                                                        <label class="form-check-label small" for="delete-{{ $index }}">
                                                            Xóa
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- New Images -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-camera me-2"></i>Thêm hình ảnh mới
                            </h6>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Upload hình ảnh</label>
                            <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" 
                                   accept="image/*" multiple
                                   onchange="previewImages(this)">
                            <small class="form-text text-muted">Thêm hình ảnh minh chứng (tùy chọn)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <div id="imagePreview" class="row mt-3" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.incidents.show', $incident) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <a href="{{ route('admin.incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Danh sách
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-2"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        preview.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-3';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection

