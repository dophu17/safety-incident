@extends('admin.layout')

@section('title', __('messages.Company Information'))
@section('page-title', __('messages.Company Information'))

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>
                    {{ __('messages.Company Details') }}
                </h5>
            </div>
            <div class="card-body">
                @if(!$company)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('admin.No incidents found.') }}
                    </div>
                @else
                    <form action="{{ route('admin.company.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>{{ __('messages.Basic Information') }}
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.Company Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $company->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.Industry') }}</label>
                                <input type="text" name="industry" class="form-control @error('industry') is-invalid @enderror" 
                                       value="{{ old('industry', $company->industry) }}"
                                       placeholder="Manufacturing, IT, Healthcare...">
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('messages.Address') }}</label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                                       value="{{ old('address', $company->address) }}"
                                       placeholder="">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-phone me-2"></i>{{ __('messages.Contact') }}
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.Phone') }}</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $company->phone) }}"
                                       placeholder="+84 123 456 789">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.Email') }}</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $company->email) }}"
                                       placeholder="info@company.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('messages.Website') }}</label>
                                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                                       value="{{ old('website', $company->website) }}"
                                       placeholder="https://www.company.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Company Size -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-chart-line me-2"></i>{{ __('messages.Company Size') }}
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.Company Size') }} <span class="text-danger">*</span></label>
                                <select name="size" class="form-select @error('size') is-invalid @enderror" required>
                                    <option value="small" {{ old('size', $company->size) == 'small' ? 'selected' : '' }}>
                                        {{ __('messages.Small') }} (1-50 {{ __('messages.employees') }})
                                    </option>
                                    <option value="medium" {{ old('size', $company->size) == 'medium' ? 'selected' : '' }}>
                                        {{ __('messages.Medium') }} (51-250 {{ __('messages.employees') }})
                                    </option>
                                    <option value="large" {{ old('size', $company->size) == 'large' ? 'selected' : '' }}>
                                        {{ __('messages.Large') }} (251-1000 {{ __('messages.employees') }})
                                    </option>
                                    <option value="enterprise" {{ old('size', $company->size) == 'enterprise' ? 'selected' : '' }}>
                                        {{ __('messages.Enterprise') }} (1000+ {{ __('messages.employees') }})
                                    </option>
                                </select>
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.Number of Employees') }}</label>
                                <input type="number" name="employee_count" class="form-control @error('employee_count') is-invalid @enderror" 
                                       value="{{ old('employee_count', $company->employee_count) }}"
                                       placeholder="100" min="1">
                                @error('employee_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-file-alt me-2"></i>{{ __('messages.Description') }}
                                </h6>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span>
                                        {{ __('messages.Company Description') }}
                                        <span class="badge bg-primary ms-2">
                                            <i class="fas fa-robot me-1"></i>{{ __('admin.AI will analyze') }}
                                        </span>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="fillCompanyTemplate()">
                                        <i class="fas fa-bolt me-1"></i>{{ __('admin.Quick Fill') }}
                                    </button>
                                </label>
                                <textarea id="descriptionTextarea" name="description" rows="6" class="form-control @error('description') is-invalid @enderror" style="resize: vertical; overflow-y: auto;"
                                          placeholder="{{ __('messages.Provide detailed information about your company, business activities, and operational characteristics') }}">{{ old('description', $company->description) }}</textarea>
                                
                                <div class="card border-primary mt-2">
                                    <div class="card-body p-3 bg-light">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-lightbulb text-warning me-2" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="text-primary">{{ __('admin.How to input for best AI analysis:') }}</strong>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <ul class="mb-0 small">
                                                            <li class="mb-1">{{ __('admin.Business Type:') }} <strong>{{ __('admin.Industry, main activities') }}</strong></li>
                                                            <li class="mb-1">{{ __('admin.Operations:') }} <strong>{{ __('admin.Manufacturing processes, services') }}</strong></li>
                                                            <li class="mb-1">{{ __('admin.Workforce:') }} <strong>{{ __('admin.Number of employees, departments') }}</strong></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <ul class="mb-0 small">
                                                            <li class="mb-1">{{ __('admin.Facilities:') }} <strong>{{ __('admin.Workplaces, equipment, locations') }}</strong></li>
                                                            <li class="mb-1">{{ __('admin.Safety Focus:') }} <strong>{{ __('admin.Safety measures, protocols') }}</strong></li>
                                                            <li class="mb-1">{{ __('admin.Environment:') }} <strong>{{ __('admin.Working conditions, hazards') }}</strong></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="alert alert-warning mb-0 mt-2 py-2 px-3 small">
                                                    <i class="fas fa-robot me-1"></i>
                                                    <strong>{{ __('admin.AI will analyze:') }}</strong> {{ __('admin.Company profile, risk patterns, safety recommendations, and incident predictions') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>{{ __('messages.Reset') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('messages.Save Changes') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Company Info Summary -->
    <div class="col-lg-4">
        @if($company)
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>{{ __('messages.Company Summary') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.Company ID') }}</small>
                        <strong>#{{ $company->id }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.Created') }}</small>
                        <strong>{{ $company->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.Last Updated') }}</small>
                        <strong>{{ $company->updated_at->format('M d, Y H:i') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.Total Employees') }}</small>
                        <strong>{{ $company->users()->count() }}</strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">{{ __('messages.Total Incidents') }}</small>
                        <strong>{{ $company->incidents()->count() }}</strong>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>{{ __('messages.Tips') }}
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2 small">{{ __('messages.Keep your company information up to date') }}</li>
                        <li class="mb-2 small">{{ __('messages.Accurate company size helps in analytics') }}</li>
                        <li class="mb-2 small">{{ __('messages.Contact details are visible to employees') }}</li>
                        <li class="mb-0 small">{{ __('messages.Description appears in reports') }}</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Fill company template function
function fillCompanyTemplate() {
    let template = '';
    
    @if(app()->getLocale() == 'ja')
    template = `🏢 会社概要:
[会社名]は[業界]に特化した企業で、[設立年]年に設立されました。

🏭 事業内容:
- 主要事業: [例: 自動車部品製造、ITサービス、建設業など]
- 製造プロセス: [例: CNC加工、溶接、組立、検査など]
- 提供サービス: [例: 設計、製造、メンテナンス、コンサルティングなど]

👥 組織構成:
- 従業員数: [例: 150名]
- 部門構成: [例: 製造部、品質管理部、安全衛生部、営業部など]
- 勤務体制: [例: 3交代制、日勤のみ、リモートワーク併用など]

🏗️ 施設・設備:
- 工場・オフィス: [例: 本社工場、支社、倉庫など]
- 主要設備: [例: CNCマシン、溶接機、クレーン、検査機器など]
- 作業環境: [例: 屋内作業、屋外作業、高温環境、化学物質使用など]

🛡️ 安全対策:
- 安全方針: [例: ゼロ災害を目指す、継続的改善など]
- 安全設備: [例: 保護具、安全装置、監視カメラなど]
- 教育・訓練: [例: 安全研修、緊急時対応訓練など]

⚠️ 潜在リスク:
- 作業リスク: [例: 機械操作、高所作業、化学物質取扱いなど]
- 環境リスク: [例: 騒音、振動、有害物質、高温など]
- 人的要因: [例: 疲労、集中力不足、経験不足など]`;
    @else
    template = `🏢 Thông tin công ty:
[Công ty] hoạt động trong lĩnh vực [ngành nghề], được thành lập năm [năm].

🏭 Hoạt động kinh doanh:
- Lĩnh vực chính: [Ví dụ: Sản xuất linh kiện ô tô, dịch vụ IT, xây dựng...]
- Quy trình sản xuất: [Ví dụ: Gia công CNC, hàn, lắp ráp, kiểm tra...]
- Dịch vụ cung cấp: [Ví dụ: Thiết kế, sản xuất, bảo trì, tư vấn...]

👥 Cơ cấu tổ chức:
- Số nhân viên: [Ví dụ: 150 người]
- Các phòng ban: [Ví dụ: Sản xuất, QC, An toàn lao động, Kinh doanh...]
- Chế độ làm việc: [Ví dụ: 3 ca, ca ngày, kết hợp làm việc từ xa...]

🏗️ Cơ sở vật chất:
- Nhà xưởng/văn phòng: [Ví dụ: Nhà máy chính, chi nhánh, kho bãi...]
- Thiết bị chính: [Ví dụ: Máy CNC, máy hàn, cần cẩu, thiết bị kiểm tra...]
- Môi trường làm việc: [Ví dụ: Trong nhà, ngoài trời, môi trường nhiệt độ cao, hóa chất...]

🛡️ Biện pháp an toàn:
- Chính sách an toàn: [Ví dụ: Hướng tới không tai nạn, cải tiến liên tục...]
- Thiết bị an toàn: [Ví dụ: PPE, thiết bị bảo vệ, camera giám sát...]
- Đào tạo: [Ví dụ: Huấn luyện an toàn, diễn tập ứng phó sự cố...]

⚠️ Rủi ro tiềm ẩn:
- Rủi ro công việc: [Ví dụ: Vận hành máy móc, làm việc trên cao, tiếp xúc hóa chất...]
- Rủi ro môi trường: [Ví dụ: Tiếng ồn, rung động, chất độc hại, nhiệt độ cao...]
- Yếu tố con người: [Ví dụ: Mệt mỏi, thiếu tập trung, thiếu kinh nghiệm...]`;
    @endif

    const textarea = document.getElementById('descriptionTextarea');
    if (confirm("{{ __('admin.Fill template will replace current content. Are you sure?') }}")) {
        textarea.value = template;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>
@endsection

