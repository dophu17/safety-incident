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
                    {{ __('Report Safety Incident') }}
                </h1>
                <p class="lead mb-0">
                    {{ __('Help us maintain a safe workplace by reporting incidents promptly') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Incidents') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
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
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            {{ __('Basic Information') }}
                        </h3>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-card-text me-1"></i>{{ __('Incident Title') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ old('title') }}" required
                                   placeholder="{{ __('Brief description of the incident') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-file-text me-1"></i>{{ __('Detailed Description') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" rows="5" class="form-control" required
                                      placeholder="{{ __('Provide detailed information about what happened, when, and any contributing factors') }}">{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <!-- Location and Time -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-geo-alt text-primary me-2"></i>
                            {{ __('Location & Time') }}
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>{{ __('Location') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="location" class="form-control" 
                                       value="{{ old('location') }}" required
                                       placeholder="{{ __('Where did the incident occur?') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar me-1"></i>{{ __('Date & Time') }} <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="occurred_at" class="form-control" 
                                       value="{{ old('occurred_at') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Evidence -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-camera text-primary me-2"></i>
                            {{ __('Evidence & Documentation') }}
                        </h3>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-image me-1"></i>{{ __('Upload Images') }}
                            </label>
                            <input type="file" name="images[]" class="form-control" 
                                   accept="image/*" multiple
                                   onchange="previewImages(this)">
                            <div class="form-text">
                                {{ __('Upload photos related to the incident (optional)') }}
                            </div>
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="row mt-3" style="display: none;">
                            <!-- Preview images will be inserted here -->
                        </div>
                    </div>

                    <!-- Severity Level -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-exclamation-triangle text-primary me-2"></i>
                            {{ __('Severity Assessment') }}
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
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
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-send me-2"></i>{{ __('Submit Report') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

// Set default occurred_at to current time
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.querySelector('input[name="occurred_at"]').value = localDateTime;
});
</script>
@endpush
@endsection