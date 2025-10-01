@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('page-title', 'Tổng quan hệ thống')

@section('content')
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
                    <p class="text-muted mb-0">Tổng sự cố</p>
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
                    <p class="text-muted mb-0">Tổng người dùng</p>
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
                    <p class="text-muted mb-0">Quản lý</p>
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
                    <p class="text-muted mb-0">Nhân viên</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Incidents Chart -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo tháng (6 tháng gần nhất)</h5>
            <div style="height: 250px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Incidents by Location -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo vị trí (Top 5)</h5>
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
            <h5 class="mb-3">Sự cố theo người dùng (Top 10)</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Người dùng</th>
                            <th width="120" class="text-center">Số lượng</th>
                            <th>Biểu đồ</th>
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
                            <td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td>
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
</script>
@endsection

