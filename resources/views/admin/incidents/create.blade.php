@extends('admin.layout')

@section('title', __('admin.Create New Incident'))
@section('page-title', __('admin.Create New Incident'))

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    {{ __('Report Safety Incident') }}
                </h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>{{ __('Please correct the following errors:') }}</strong>
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
                                <i class="fas fa-info-circle me-2"></i>{{ __('Basic Information') }}
                            </h6>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('Incident Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}"
                                   placeholder="{{ __('Brief description of the incident') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>
                                    {{ __('Detailed Description') }} <span class="text-danger">*</span>
                                    <span class="badge bg-primary ms-2">
                                        <i class="fas fa-robot me-1"></i>{{ __('admin.AI will analyze') }}
                                    </span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="fillTemplate()">
                                    <i class="fas fa-bolt me-1"></i>{{ __('admin.Quick Fill') }}
                                </button>
                            </label>
                            <textarea id="contentTextarea" name="content" rows="8" class="form-control @error('content') is-invalid @enderror" style="resize: vertical; overflow-y: auto;"
                                      placeholder="{{ __('Provide detailed information about what happened, when, and any contributing factors') }}">{{ old('content') }}</textarea>
                            
                            <div class="card border-primary mt-2">
                                <div class="card-body p-3 bg-light">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-lightbulb text-warning me-2" style="font-size: 1.2rem;"></i>
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
                                                <i class="fas fa-robot me-1"></i>
                                                <strong>{{ __('admin.AI will analyze:') }}</strong> {{ __('admin.Risk level, root causes, solutions, and predict similar incidents') }}
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
                                <i class="fas fa-map-marker-alt me-2"></i>{{ __('Location & Time') }}
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Location') }} <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                   value="{{ old('location') }}"
                                   placeholder="{{ __('Where did the incident occur?') }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Occurred at') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" 
                                   value="{{ old('occurred_at') }}">
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Severity Assessment -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ __('Severity Assessment') }}
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Severity Level') }}</label>
                            <select name="severity" class="form-select">
                                <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>
                                    {{ __('Low') }} - {{ __('Minor incident, no injuries') }}
                                </option>
                                <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>
                                    {{ __('Medium') }} - {{ __('Moderate impact, minor injuries') }}
                                </option>
                                <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>
                                    {{ __('High') }} - {{ __('Serious incident, significant impact') }}
                                </option>
                                <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>
                                    {{ __('Critical') }} - {{ __('Major incident, severe injuries or fatalities') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Immediate Action Required') }}</label>
                            <select name="immediate_action" class="form-select">
                                <option value="no" {{ old('immediate_action') == 'no' ? 'selected' : '' }}>
                                    {{ __('No immediate action required') }}
                                </option>
                                <option value="yes" {{ old('immediate_action') == 'yes' ? 'selected' : '' }}>
                                    {{ __('Immediate action required') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Evidence -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-camera me-2"></i>{{ __('Evidence & Documentation') }}
                            </h6>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('Upload Images') }}</label>
                            <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" 
                                   accept="image/*" multiple
                                   onchange="previewImages(this)">
                            <small class="form-text text-muted">{{ __('Upload photos related to the incident (optional)') }}</small>
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
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-2"></i>{{ __('Submit Report') }}
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

// Fill template function
function fillTemplate() {
    @if(app()->getLocale() == 'ja')
    const template = `📍 インシデントの経過:
CNC-05機械が[日付を記入]14:30に突然停止。事前に異常音と軽い焦げ臭がありました。

⚙️ 関連機器:
- 機器名: [例: CNC FANUC Series 30i]
- 使用年数: [例: 5年]
- 状態: [例: インシデント前は正常動作]

👥 関係者:
- 影響を受けた人数: [例: 2名の作業員]
- 目撃者: [例: 1名のエンジニア、他3名の作業員]

💥 原因（推測）:
[例: 冷却システムの故障、冷却ファン不動作の疑い]

🩹 結果:
- 負傷: [例: 負傷なし]
- 財産損害: [例: 未評価、推定500-1000万円]
- 業務中断: [例: 生産ライン2時間停止]

🔧 実施した措置:
- 機器の電源を直ちに切断
- 安全のため区域を隔離
- 保守部門に通報
- [その他の措置...]`;
    @else
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
    @endif

    const textarea = document.getElementById('contentTextarea');
    if (confirm("{{ __('admin.Fill template will replace current content. Are you sure?') }}")) {
        textarea.value = template;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
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

