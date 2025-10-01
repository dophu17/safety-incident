@extends('admin.layout')

@section('title', 'Thống kê chi tiết')
@section('page-title', 'Thống kê chi tiết')

@section('content')
<!-- Date Range Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-3">Chọn khoảng thời gian</h5>
            <form method="GET" action="{{ route('admin.statistics') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Cập nhật thống kê
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="{{ route('admin.statistics') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4 text-center">
            <h3 class="text-primary">{{ number_format($incidentsInPeriod) }}</h3>
            <p class="text-muted mb-0">Sự cố trong khoảng thời gian</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4 text-center">
            <h3 class="text-success">{{ $incidentsByUser->count() }}</h3>
            <p class="text-muted mb-0">Người dùng báo cáo</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4 text-center">
            <h3 class="text-info">{{ $incidentsByMonth->count() }}</h3>
            <p class="text-muted mb-0">Tháng có sự cố</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4 text-center">
            <h3 class="text-warning">{{ $incidentsByDayOfWeek->count() }}</h3>
            <p class="text-muted mb-0">Ngày trong tuần có sự cố</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Incidents by Day of Week -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo ngày trong tuần</h5>
            <div style="height: 280px;">
                <canvas id="dayOfWeekChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Incidents by Hour -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo giờ trong ngày</h5>
            <div style="height: 280px;">
                <canvas id="hourChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Incidents by Month -->
    <div class="col-lg-12 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo tháng</h5>
            <div style="height: 300px;">
                <canvas id="monthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics Table -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-3">Thống kê chi tiết theo ngày trong tuần</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Thứ</th>
                            <th>Số sự cố</th>
                            <th>Tỷ lệ (%)</th>
                            <th>Biểu đồ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalIncidents = $incidentsByDayOfWeek->sum('count');
                            $dayNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
                        @endphp
                        @foreach($incidentsByDayOfWeek as $dayData)
                        @php
                            $dayName = $dayNames[$dayData->day_of_week - 1] ?? 'Không xác định';
                            $percentage = $totalIncidents > 0 ? round(($dayData->count / $totalIncidents) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $dayName }}</td>
                            <td><span class="badge bg-primary">{{ $dayData->count }}</span></td>
                            <td>{{ $percentage }}%</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $percentage }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Day of Week Chart - Đơn giản dạng bar thay vì doughnut
const dayOfWeekCtx = document.getElementById('dayOfWeekChart').getContext('2d');
const dayOfWeekChart = new Chart(dayOfWeekCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($incidentsByDayOfWeek->map(function($item) {
            $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
            return $dayNames[$item->day_of_week - 1] ?? 'N/A';
        })) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($incidentsByDayOfWeek->pluck('count')) !!},
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(201, 203, 207, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(201, 203, 207, 1)'
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
                        size: 12,
                        weight: 'bold'
                    }
                }
            }
        }
    }
});

// Hour Chart - Giữ dạng bar nhưng cải thiện
const hourCtx = document.getElementById('hourChart').getContext('2d');
const hourChart = new Chart(hourCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($incidentsByHour->pluck('hour')->map(function($h) { return $h . 'h'; })) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($incidentsByHour->pluck('count')) !!},
            backgroundColor: 'rgba(153, 102, 255, 0.8)',
            borderColor: 'rgba(153, 102, 255, 1)',
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
                        size: 10
                    }
                }
            }
        }
    }
});

// Month Chart - Đơn giản dạng bar thay vì line
const monthCtx = document.getElementById('monthChart').getContext('2d');
const monthChart = new Chart(monthCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($incidentsByMonth->pluck('month')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($incidentsByMonth->pluck('count')) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
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
</script>
@endsection
