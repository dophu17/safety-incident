@extends('admin.layout')

@section('title', __('admin.Dashboard'))
@section('page-title', __('admin.Overview Statistics'))

@push('styles')
<link href="{{ asset('css/ai-dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- AI Analysis Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm ai-analysis-card">
            <div class="card-header ai-analysis-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-robot me-2 ai-robot-icon"></i>{{ __('admin.AI Analysis & Recommendations') }}
                </h5>
                <button type="button" class="btn btn-sm ai-refresh-btn" id="refreshAIAnalysis" onclick="refreshAIAnalysis()">
                    <i class="fas fa-sync-alt me-1"></i>{{ __('admin.Refresh AI Analysis') }}
                </button>
            </div>
            <div class="card-body" id="aiAnalysisContent">
                <div class="text-center py-5">
                    <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('admin.No AI Analysis Available') }}</h5>
                    <p class="text-muted">{{ __('admin.Click refresh to generate AI analysis based on your incident data') }}</p>
                    <button type="button" class="btn ai-generate-btn" onclick="refreshAIAnalysis()">
                        <i class="fas fa-sync-alt me-1"></i>{{ __('admin.Generate AI Analysis') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($totalIncidents) }}</h3>
                    <p class="text-muted mb-0">{{ __('admin.Total Incidents') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($totalUsers) }}</h3>
                    <p class="text-muted mb-0">{{ __('admin.Total Employees') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning me-3">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($totalManagers) }}</h3>
                    <p class="text-muted mb-0">{{ __('admin.Managers') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info me-3">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($totalEmployees) }}</h3>
                    <p class="text-muted mb-0">{{ __('admin.Employees') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Incidents Chart -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">{{ __('admin.Monthly Incident Trend') }}</h5>
            <div style="height: 250px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Incidents by Location -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">{{ __('admin.Incidents by Location') }}</h5>
            <div style="height: 250px;">
                <canvas id="locationChart"></canvas>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <!-- Incidents by User -->
    <div class="col-lg-12 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">{{ __('admin.User Statistics') }}</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>{{ __('admin.User') }}</th>
                            <th width="120" class="text-center">{{ __('admin.Incidents') }}</th>
                            <th>{{ __('admin.Statistics') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxCount = $incidentsByUser->max('count') ?? 1; @endphp
                        @forelse($incidentsByUser as $index => $userIncident)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 14px;">
                                        {{ substr($userIncident->user->name, 0, 1) }}
                                    </div>
                                    <span>{{ $userIncident->user->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">{{ $userIncident->count }}</span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ ($userIncident->count / $maxCount) * 100 }}%" 
                                         aria-valuenow="{{ $userIncident->count }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="{{ $maxCount }}">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">{{ __('admin.No incidents found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Monthly Chart - Đơn giản dạng bar
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyIncidents->pluck('month')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($monthlyIncidents->pluck('count')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 2,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: 12
                    }
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Location Chart - Dạng horizontal bar
const locationCtx = document.getElementById('locationChart').getContext('2d');
const locationChart = new Chart(locationCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($incidentsByLocation->take(5)->pluck('location')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($incidentsByLocation->take(5)->pluck('count')) !!},
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            borderRadius: 5
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: 12
                    }
                }
            },
            y: {
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// AI Analysis Refresh Function
function refreshAIAnalysis() {
    const button = document.getElementById('refreshAIAnalysis');
    const content = document.getElementById('aiAnalysisContent');
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __("admin.Analyzing...") }}';
    
    // Show loading content with detailed steps
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="ai-loading mx-auto mb-3"></div>
            <h5 class="text-primary">{{ __('admin.AI is analyzing your data...') }}</h5>
            <div class="mt-3">
                <div class="mb-2">
                    <i class="fas fa-chart-line text-info me-2"></i>
                    <span class="small">{{ __('admin.Analyzing incident patterns and trends...') }}</span>
                </div>
                <div class="mb-2">
                    <i class="fas fa-building text-success me-2"></i>
                    <span class="small">{{ __('admin.Evaluating company profile and risk factors...') }}</span>
                </div>
                <div class="mb-2">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <span class="small">{{ __('admin.Generating safety recommendations...') }}</span>
                </div>
            </div>
            <p class="text-muted mt-3">{{ __('admin.This may take a few moments') }}</p>
        </div>
    `;
    
    // Make AJAX request to refresh AI analysis
    fetch('{{ route("admin.dashboard.ai-refresh") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Display the AI analysis results directly
            displayAIAnalysis(data.data);
        } else {
            throw new Error(data.message || 'AI analysis failed');
        }
    })
    .catch(error => {
        console.error('AI Analysis Error:', error);
        
        // Show error state
        content.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">{{ __('admin.AI Analysis Failed') }}</h5>
                <p class="text-muted">{{ __('admin.Please try again later') }}</p>
                <button type="button" class="btn ai-generate-btn" onclick="refreshAIAnalysis()">
                    <i class="fas fa-sync-alt me-1"></i>{{ __('admin.Try Again') }}
                </button>
            </div>
        `;
        
        // Reset button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sync-alt me-1"></i>{{ __("admin.Refresh AI Analysis") }}';
    });
}

// Function to display AI analysis results
function displayAIAnalysis(data) {
    const content = document.getElementById('aiAnalysisContent');
    const button = document.getElementById('refreshAIAnalysis');
    
    // Debug logging
    console.log('AI Analysis Data:', data);
    console.log('Department Analysis:', data.aiAnalysis?.department_analysis);
    console.log('Location Analysis:', data.aiAnalysis?.location_analysis);
    
    // Reset button
    button.disabled = false;
    button.innerHTML = '<i class="fas fa-sync-alt me-1"></i>{{ __("admin.Refresh AI Analysis") }}';
    
    let html = '<div class="row">';
    
    // Incident Analysis
    if (data.aiAnalysis) {
        html += `
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>{{ __('admin.Incident Analysis') }}
                        </h6>
                    </div>
                    <div class="card-body">`;
        
        if (data.aiAnalysis.department_analysis && data.aiAnalysis.department_analysis.high_risk_departments && data.aiAnalysis.department_analysis.high_risk_departments.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-primary">{{ __('admin.High Risk Departments') }}</h6>
                    <div class="d-flex flex-wrap gap-1">`;
            data.aiAnalysis.department_analysis.high_risk_departments.forEach(dept => {
                html += `<span class="badge ai-department-badge">${dept}</span>`;
            });
            html += `
                    </div>
                    <p class="small text-muted mt-2">${data.aiAnalysis.department_analysis.analysis || ''}</p>
                </div>`;
        }
        
        if (data.aiAnalysis.location_analysis && data.aiAnalysis.location_analysis.high_risk_locations && data.aiAnalysis.location_analysis.high_risk_locations.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-warning">{{ __('admin.High Risk Locations') }}</h6>
                    <div class="d-flex flex-wrap gap-1">`;
            data.aiAnalysis.location_analysis.high_risk_locations.forEach(location => {
                html += `<span class="badge ai-location-badge">${location}</span>`;
            });
            html += `
                    </div>
                    <p class="small text-muted mt-2">${data.aiAnalysis.location_analysis.analysis || ''}</p>
                </div>`;
        }
        
        if (data.aiAnalysis.content_analysis) {
            html += `
                <div class="mb-3">
                    <h6 class="text-info">{{ __('admin.Content Analysis') }}</h6>
                    <div class="row">
                        <div class="col-6">
                            <strong class="small">{{ __('admin.Main Themes') }}:</strong>
                            <ul class="list-unstyled small mb-0">`;
            if (data.aiAnalysis.content_analysis.main_themes && data.aiAnalysis.content_analysis.main_themes.length > 0) {
                data.aiAnalysis.content_analysis.main_themes.forEach(theme => {
                    html += `<li><i class="fas fa-tag text-info me-1"></i>${theme}</li>`;
                });
            } else {
                html += `<li class="text-muted">Không có dữ liệu</li>`;
            }
            html += `
                            </ul>
                        </div>
                        <div class="col-6">
                            <strong class="small">{{ __('admin.Common Issues') }}:</strong>
                            <ul class="list-unstyled small mb-0">`;
            if (data.aiAnalysis.content_analysis.common_issues && data.aiAnalysis.content_analysis.common_issues.length > 0) {
                data.aiAnalysis.content_analysis.common_issues.forEach(issue => {
                    html += `<li><i class="fas fa-exclamation-circle text-warning me-1"></i>${issue}</li>`;
                });
            } else {
                html += `<li class="text-muted">Không có dữ liệu</li>`;
            }
            html += `
                            </ul>
                        </div>
                    </div>
                </div>`;
        }

        if (data.aiAnalysis.recommendations && data.aiAnalysis.recommendations.length > 0) {
            html += `
                <div>
                    <h6 class="text-success">{{ __('admin.Recommendations') }}</h6>
                    <ul class="list-unstyled mb-0">`;
            data.aiAnalysis.recommendations.forEach(recommendation => {
                html += `
                        <li class="small mb-1 ai-recommendation-item">
                            <i class="fas fa-check-circle text-success me-1"></i>${recommendation}
                        </li>`;
            });
            html += `
                    </ul>
                </div>`;
        }
        
        html += `
                    </div>
                </div>
            </div>`;
    }
    
    // Company Analysis
    if (data.companyAnalysis) {
        html += `
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>{{ __('admin.Company Analysis') }}
                        </h6>
                    </div>
                    <div class="card-body">`;
        
        if (data.companyAnalysis.company_profile) {
            html += `
                <div class="mb-3">
                    <h6 class="text-primary">{{ __('admin.Business Profile') }}</h6>
                    <p class="small">${data.companyAnalysis.company_profile.business_type}</p>
                    <p class="small">${data.companyAnalysis.company_profile.scale_assessment}</p>
                </div>`;
        }
        
        if (data.companyAnalysis.safety_recommendations) {
            html += `
                <div class="mb-3">
                    <h6 class="text-warning">{{ __('admin.Safety Recommendations') }}</h6>`;
            data.companyAnalysis.safety_recommendations.forEach(rec => {
                html += `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <strong class="small">${rec.category}</strong>
                            <p class="small mb-0">${rec.recommendation}</p>
                        </div>
                        <span class="badge ai-priority-${rec.priority.toLowerCase()}">
                            ${rec.priority}
                        </span>
                    </div>`;
            });
            html += `</div>`;
        }
        
        html += `
                    </div>
                </div>
            </div>`;
    }
    
    // Safety Warnings
    if (data.safetyRecommendations) {
        html += `
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ __('admin.Safety Warnings') }}
                        </h6>
                    </div>
                    <div class="card-body">`;
        
        if (data.safetyRecommendations.future_warnings) {
            html += `
                <div class="mb-3">
                    <h6 class="text-danger">{{ __('admin.Future Warnings') }}</h6>`;
            data.safetyRecommendations.future_warnings.forEach(warning => {
                const alertClass = warning.severity === 'High' ? 'danger' : (warning.severity === 'Medium' ? 'warning' : 'info');
                html += `
                    <div class="alert alert-${alertClass} py-2 px-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="small">${warning.type}</strong>
                                <p class="small mb-0">${warning.warning}</p>
                            </div>
                            <small class="text-muted">${warning.timeframe}</small>
                        </div>
                    </div>`;
            });
            html += `</div>`;
        }
        
        if (data.safetyRecommendations.preventive_measures) {
            html += `
                <div>
                    <h6 class="text-success">{{ __('admin.Preventive Measures') }}</h6>`;
            data.safetyRecommendations.preventive_measures.forEach(measure => {
                html += `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <p class="small mb-0">${measure.measure}</p>
                            <small class="text-muted">${measure.department}</small>
                        </div>
                        <span class="badge ai-priority-${measure.priority.toLowerCase()}">
                            ${measure.priority}
                        </span>
                    </div>`;
            });
            html += `</div>`;
        }
        
        html += `
                    </div>
                </div>
            </div>`;
    }
    
    html += '</div>';
    content.innerHTML = html;
}
</script>
@endsection

