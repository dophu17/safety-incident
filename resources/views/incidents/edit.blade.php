@extends('layouts.app')

@section('title', __('Edit Incident') . ' - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    {{ __('Edit Safety Incident') }}
                </h1>
                <p class="lead mb-0">
                    {{ __('Update incident information') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Details') }}
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

                <form action="{{ route('incidents.update', $incident) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
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
                                   value="{{ old('title', $incident->title) }}" required
                                   placeholder="{{ __('Brief description of the incident') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-file-text me-1"></i>{{ __('Detailed Description') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" rows="5" class="form-control" required
                                      placeholder="{{ __('Provide detailed information about what happened, when, and any contributing factors') }}">{{ old('content', $incident->content) }}</textarea>
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
                                       value="{{ old('location', $incident->location) }}" required
                                       placeholder="{{ __('Where did the incident occur?') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar me-1"></i>{{ __('Date & Time') }} <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="occurred_at" class="form-control" 
                                       value="{{ old('occurred_at', $incident->occurred_at?->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Current Images -->
                    @if ($incident->images && count($incident->images) > 0)
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="bi bi-images text-primary me-2"></i>
                                {{ __('Current Images') }}
                            </h3>
                            <div class="row g-3" id="currentImages">
                                @foreach ($incident->images as $index => $img)
                                    <div class="col-md-3 col-6" id="image-{{ $index }}">
                                        <div class="card border">
                                            <img class="card-img-top" 
                                                 src="{{ asset('storage/' . $img) }}" 
                                                 alt="{{ __('Incident image') }} {{ $index + 1 }}"
                                                 style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="delete_images[]" value="{{ $img }}" 
                                                           id="delete-{{ $index }}">
                                                    <label class="form-check-label small" for="delete-{{ $index }}">
                                                        {{ __('Delete') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- New Evidence -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">
                            <i class="bi bi-camera text-primary me-2"></i>
                            {{ __('Add New Images') }}
                        </h3>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-image me-1"></i>{{ __('Upload Images') }}
                            </label>
                            <input type="file" name="images[]" class="form-control" 
                                   accept="image/*" multiple
                                   onchange="previewImages(this)">
                            <div class="form-text">
                                {{ __('Upload additional photos related to the incident (optional)') }}
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
                                    <option value="low" {{ old('severity', $incident->severity ?? 'low') == 'low' ? 'selected' : '' }}>
                                        {{ __('Low') }} - {{ __('Minor incident, no injuries') }}
                                    </option>
                                    <option value="medium" {{ old('severity', $incident->severity ?? 'low') == 'medium' ? 'selected' : '' }}>
                                        {{ __('Medium') }} - {{ __('Moderate impact, minor injuries') }}
                                    </option>
                                    <option value="high" {{ old('severity', $incident->severity ?? 'low') == 'high' ? 'selected' : '' }}>
                                        {{ __('High') }} - {{ __('Serious incident, significant impact') }}
                                    </option>
                                    <option value="critical" {{ old('severity', $incident->severity ?? 'low') == 'critical' ? 'selected' : '' }}>
                                        {{ __('Critical') }} - {{ __('Major incident, severe injuries or fatalities') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Immediate Action Required') }}</label>
                                <select name="immediate_action" class="form-select">
                                    <option value="no" {{ old('immediate_action', $incident->immediate_action ?? 'no') == 'no' ? 'selected' : '' }}>
                                        {{ __('No immediate action required') }}
                                    </option>
                                    <option value="yes" {{ old('immediate_action', $incident->immediate_action ?? 'no') == 'yes' ? 'selected' : '' }}>
                                        {{ __('Immediate action required') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-check-circle me-2"></i>{{ __('Update Incident') }}
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
</script>
@endpush
@endsection

