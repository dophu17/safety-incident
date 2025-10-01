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
                                   value="{{ old('title', $incident->title) }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>
                                    Mô tả chi tiết <span class="text-danger">*</span>
                                    <span class="badge bg-primary ms-2">
                                        <i class="fas fa-robot me-1"></i>AI sẽ phân tích
                                    </span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="fillTemplate()">
                                    <i class="fas fa-bolt me-1"></i>Điền mẫu nhanh
                                </button>
                            </label>
                            <textarea id="contentTextarea" name="content" rows="8" class="form-control @error('content') is-invalid @enderror" style="resize: vertical; overflow-y: auto;" 
                                      placeholder="Nhập mô tả chi tiết...">{{ old('content', $incident->content) }}</textarea>
                            
                            <div class="card border-primary mt-2">
                                <div class="card-body p-3 bg-light">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-lightbulb text-warning me-2" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <strong class="text-primary">Hướng dẫn nhập cho AI phân tích tốt nhất:</strong>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <ul class="mb-0 small">
                                                        <li class="mb-1"><strong>📍 Diễn biến:</strong> Chi tiết những gì xảy ra</li>
                                                        <li class="mb-1"><strong>⚙️ Thiết bị:</strong> Tên máy, model, năm sử dụng</li>
                                                        <li class="mb-1"><strong>👥 Người liên quan:</strong> Số người, vai trò</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="mb-0 small">
                                                        <li class="mb-1"><strong>💥 Nguyên nhân:</strong> Nguyên nhân dự đoán</li>
                                                        <li class="mb-1"><strong>🩹 Hậu quả:</strong> Thương tích, thiệt hại</li>
                                                        <li class="mb-1"><strong>🔧 Đã xử lý:</strong> Biện pháp đã làm</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="alert alert-warning mb-0 mt-2 py-2 px-3 small">
                                                <i class="fas fa-robot me-1"></i>
                                                AI sẽ phân tích: Mức độ rủi ro, nguyên nhân gốc, giải pháp khắc phục & dự đoán sự cố tương tự
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
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
                                   value="{{ old('location', $incident->location) }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian xảy ra <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" 
                                   value="{{ old('occurred_at', $incident->occurred_at?->format('Y-m-d\TH:i')) }}">
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Severity Assessment -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Đánh giá mức độ nghiêm trọng
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mức độ nghiêm trọng</label>
                            <select name="severity" class="form-select">
                                <option value="low" {{ old('severity', $incident->severity ?? 'low') == 'low' ? 'selected' : '' }}>
                                    Thấp - Sự cố nhỏ, không có thương tích
                                </option>
                                <option value="medium" {{ old('severity', $incident->severity ?? 'low') == 'medium' ? 'selected' : '' }}>
                                    Trung bình - Tác động vừa phải, thương tích nhẹ
                                </option>
                                <option value="high" {{ old('severity', $incident->severity ?? 'low') == 'high' ? 'selected' : '' }}>
                                    Cao - Sự cố nghiêm trọng, tác động đáng kể
                                </option>
                                <option value="critical" {{ old('severity', $incident->severity ?? 'low') == 'critical' ? 'selected' : '' }}>
                                    Rất nghiêm trọng - Sự cố lớn, thương tích nặng hoặc tử vong
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Yêu cầu hành động ngay lập tức</label>
                            <select name="immediate_action" class="form-select">
                                <option value="no" {{ old('immediate_action', $incident->immediate_action ?? 'no') == 'no' ? 'selected' : '' }}>
                                    Không cần hành động ngay
                                </option>
                                <option value="yes" {{ old('immediate_action', $incident->immediate_action ?? 'no') == 'yes' ? 'selected' : '' }}>
                                    Cần hành động ngay lập tức
                                </option>
                            </select>
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
// Fill template function
function fillTemplate() {
    const template = `📍 Diễn biến sự cố:
Máy CNC-05 dừng đột ngột lúc 14:30 ngày [điền ngày]. Trước đó có tiếng kêu bất thường và mùi cháy nhẹ.

⚙️ Thiết bị liên quan:
- Tên thiết bị: [Ví dụ: Máy CNC FANUC Series 30i]
- Năm sử dụng: [Ví dụ: 5 năm]
- Tình trạng: [Ví dụ: Đang hoạt động bình thường trước khi xảy ra sự cố]

👥 Người liên quan:
- Số người ảnh hưởng: [Ví dụ: 2 công nhân]
- Người chứng kiến: [Ví dụ: 1 kỹ sư, 3 công nhân khác]

💥 Nguyên nhân (dự đoán):
[Ví dụ: Nghi ngờ do hệ thống làm mát bị hỏng, quạt làm mát không hoạt động]

🩹 Hậu quả:
- Thương tích: [Ví dụ: Không có thương tích]
- Thiệt hại tài sản: [Ví dụ: Chưa đánh giá, ước tính 5-10 triệu]
- Gián đoạn công việc: [Ví dụ: Dừng dây chuyền sản xuất 2 giờ]

🔧 Biện pháp đã thực hiện:
- Tắt nguồn điện máy ngay lập tức
- Cách ly khu vực để đảm bảo an toàn
- Thông báo cho bộ phận bảo trì
- [Các biện pháp khác...]`;

    const textarea = document.getElementById('contentTextarea');
    if (confirm('Điền mẫu nhanh sẽ thay thế nội dung hiện tại. Bạn có chắc chắn?')) {
        textarea.value = template;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

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

