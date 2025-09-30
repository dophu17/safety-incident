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
            <canvas id="dayOfWeekChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Incidents by Hour -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo giờ trong ngày</h5>
            <canvas id="hourChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <!-- Incidents by Month -->
    <div class="col-lg-8 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo tháng</h5>
            <canvas id="monthChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Top Users -->
    <div class="col-lg-4 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Top người dùng báo cáo</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidentsByUser as $userIncident)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 12px;">
                                        {{ substr($userIncident->user->name, 0, 1) }}
                                    </div>
                                    <span>{{ $userIncident->user->name }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">{{ $userIncident->count }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Không có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
// Day of Week Chart
const dayOfWeekCtx = document.getElementById('dayOfWeekChart').getContext('2d');
const dayOfWeekChart = new Chart(dayOfWeekCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($incidentsByDayOfWeek->map(function($item) {
            $dayNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
            return $dayNames[$item->day_of_week - 1] ?? 'Không xác định';
        })) !!},
        datasets: [{
            data: {!! json_encode($incidentsByDayOfWeek->pluck('count')) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Hour Chart
const hourCtx = document.getElementById('hourChart').getContext('2d');
const hourChart = new Chart(hourCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($incidentsByHour->pluck('hour')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($incidentsByHour->pluck('count')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Month Chart
const monthCtx = document.getElementById('monthChart').getContext('2d');
const monthChart = new Chart(monthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($incidentsByMonth->pluck('month')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($incidentsByMonth->pluck('count')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection
