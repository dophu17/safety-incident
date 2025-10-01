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
                                <label class="form-label">{{ __('messages.Company Description') }}</label>
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                          placeholder="">{{ old('description', $company->description) }}</textarea>
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

