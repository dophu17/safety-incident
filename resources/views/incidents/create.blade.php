@extends('layouts.app')

@section('title', __('Report Incident') . ' - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="bi bi-plus-circle text-primary me-2"></i>
                    {{ __('messages.Report Safety Incident') }}
                </h1>
                <p class="lead mb-0">
                    {{ __('messages.Help us maintain a safe workplace by reporting incidents promptly') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('messages.Back to Home') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="content-card">
                @if(session('welcome'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-emoji-smile-fill me-2"></i>
                        <strong>{{ __('messages.Success!') }}</strong> {{ session('welcome') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>{{ __('messages.Success!') }}</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>{{ __('messages.Error!') }}</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>{{ __('messages.Please correct the following errors:') }}</strong>
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
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            {{ __('messages.Basic Information') }}
                        </h3>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-card-text me-1"></i>{{ __('messages.Incident Title') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}"
                                   placeholder="{{ __('messages.Brief description of the incident') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-file-text me-1"></i>{{ __('messages.Detailed Description') }} <span class="text-danger">*</span>
                                    <span class="badge bg-gradient text-white ms-2" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                        <i class="bi bi-robot me-1"></i>{{ __('admin.AI will analyze') }}
                                    </span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="fillTemplate()">
                                    <i class="bi bi-lightning-fill me-1"></i>{{ __('admin.Quick Fill') }}
                                </button>
                            </label>
                            <textarea id="contentTextarea" name="content" rows="8" class="form-control @error('content') is-invalid @enderror" style="resize: vertical; overflow-y: auto;"
                                      placeholder="{{ __('messages.Provide detailed information about what happened, when, and any contributing factors') }}">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="card border-primary mt-2">
                                <div class="card-body p-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-lightbulb-fill text-warning me-2" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <strong class="text-primary">{{ __('admin.How to input for best AI analysis:') }}</strong>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <ul class="mb-0 small">
                                                        <li class="mb-1">{{ __('admin.Incident Progress:') }} <strong>{{ __('admin.Detail the incident') }}</strong></li>
                                                        <li class="mb-1">{{ __('admin.Equipment:') }} <strong>{{ __('admin.Machine name, model, condition') }}</strong></li>
                                                        <li class="mb-1">{{ __('admin.People Involved:') }} <strong>{{ __('admin.Number of people, witnesses') }}</strong></li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="mb-0 small">
                                                        <li class="mb-1">{{ __('admin.Cause:') }} <strong>{{ __('admin.Possible causes') }}</strong></li>
                                                        <li class="mb-1">{{ __('admin.Consequences:') }} <strong>{{ __('admin.Injuries, damage') }}</strong></li>
                                                        <li class="mb-1">{{ __('admin.Actions Taken:') }} <strong>{{ __('admin.Initial measures') }}</strong></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="alert alert-warning mb-0 mt-2 py-2 px-3 small">
                                                <i class="bi bi-robot me-1"></i>
                                                <strong>{{ __('admin.AI will analyze:') }}</strong> {{ __('admin.Risk level, root causes, solutions, and predict similar incidents') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location and Time -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-geo-alt text-primary me-2"></i>
                            {{ __('messages.Location & Time') }}
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>{{ __('messages.Location') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                       value="{{ old('location') }}"
                                       placeholder="{{ __('messages.Where did the incident occur?') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar me-1"></i>{{ __('messages.Date & Time') }} <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" 
                                       value="{{ old('occurred_at') }}">
                                @error('occurred_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Severity Level -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-exclamation-triangle text-primary me-2"></i>
                            {{ __('messages.Severity Assessment') }}
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.Severity Level') }}</label>
                                <select name="severity" class="form-select">
                                    <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>
                                        {{ __('messages.Low') }} - {{ __('messages.Minor incident, no injuries') }}
                                    </option>
                                    <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>
                                        {{ __('messages.Medium') }} - {{ __('messages.Moderate impact, minor injuries') }}
                                    </option>
                                    <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>
                                        {{ __('messages.High') }} - {{ __('messages.Serious incident, significant impact') }}
                                    </option>
                                    <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>
                                        {{ __('messages.Critical') }} - {{ __('messages.Major incident, severe injuries or fatalities') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.Immediate Action Required') }}</label>
                                <select name="immediate_action" class="form-select">
                                    <option value="no" {{ old('immediate_action') == 'no' ? 'selected' : '' }}>
                                        {{ __('messages.No immediate action required') }}
                                    </option>
                                    <option value="yes" {{ old('immediate_action') == 'yes' ? 'selected' : '' }}>
                                        {{ __('messages.Immediate action required') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Evidence -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-camera text-primary me-2"></i>
                            {{ __('messages.Evidence & Documentation') }}
                        </h3>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-image me-1"></i>{{ __('messages.Upload Images') }}
                            </label>
                            <input type="file" name="images[]" class="form-control" 
                                   accept="image/*" multiple
                                   onchange="previewImages(this)">
                            <div class="form-text">
                                {{ __('messages.Upload photos related to the incident (optional)') }}
                            </div>
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="row mt-3" style="display: none;">
                            <!-- Preview images will be inserted here -->
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>{{ __('messages.Cancel') }}
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-send me-2"></i>{{ __('messages.Submit Report') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Enhanced styling for create incident page */
.page-header {
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.content-card {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control:focus {
    animation: inputGlow 0.3s ease;
}

@keyframes inputGlow {
    0% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.5);
    }
    100% {
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }
}

/* Better section headers */
.content-card h3 {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

/* Enhanced buttons */
.btn-primary {
    position: relative;
    overflow: hidden;
}

.btn-primary::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
}

.btn-primary:hover::after {
    width: 300px;
    height: 300px;
}

/* Required field asterisk */
.text-danger {
    color: #ef4444 !important;
    font-weight: 700;
}
</style>
@endpush

@push('scripts')
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
        // Scroll to textarea
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Set default occurred_at to current time
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.querySelector('input[name="occurred_at"]').value = localDateTime;
    
    // Scroll to top if there's a success/error message
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Auto-hide success and welcome messages after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(successAlert);
                bsAlert.close();
            }, 5000);
        }
        
        const welcomeAlert = document.querySelector('.alert-info');
        if (welcomeAlert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(welcomeAlert);
                bsAlert.close();
            }, 7000);
        }
    }
});
</script>
@endpush
@endsection