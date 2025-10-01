@extends('admin.layout')

@section('title', 'Tạo sự cố mới')
@section('page-title', 'Tạo sự cố mới')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Báo cáo sự cố an toàn
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

                <form action="{{ route('incidents.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    
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
                                   value="{{ old('title') }}" required
                                   placeholder="Mô tả ngắn gọn về sự cố">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                            <textarea name="content" rows="5" class="form-control @error('content') is-invalid @enderror" required
                                      placeholder="Cung cấp thông tin chi tiết về sự cố, thời gian, nguyên nhân...">{{ old('content') }}</textarea>
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
                                   value="{{ old('location') }}" required
                                   placeholder="Nơi xảy ra sự cố">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian xảy ra <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" 
                                   value="{{ old('occurred_at') }}" required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Evidence -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-camera me-2"></i>Hình ảnh minh chứng
                            </h6>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Upload hình ảnh</label>
                            <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" 
                                   accept="image/*" multiple
                                   onchange="previewImages(this)">
                            <small class="form-text text-muted">Upload các hình ảnh liên quan (tùy chọn)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Image Preview -->
                        <div class="col-12">
                            <div id="imagePreview" class="row mt-3" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-2"></i>Tạo sự cố
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

// Set default occurred_at to current time
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.querySelector('input[name="occurred_at"]').value = localDateTime;
});
</script>
@endsection

