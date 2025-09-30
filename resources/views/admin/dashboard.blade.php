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
    <div class="col-lg-8 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo tháng (6 tháng gần nhất)</h5>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Incidents by Location -->
    <div class="col-lg-4 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo vị trí (Top 10)</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Vị trí</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidentsByLocation as $location)
                        <tr>
                            <td>{{ $location->location ?: 'Không xác định' }}</td>
                            <td><span class="badge bg-primary">{{ $location->count }}</span></td>
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

<div class="row">
    <!-- Daily Incidents Chart -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo ngày (30 ngày gần nhất)</h5>
            <canvas id="dailyChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Incidents by User -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">Sự cố theo người dùng (Top 10)</h5>
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
                            <td>{{ $userIncident->user->name }}</td>
                            <td><span class="badge bg-success">{{ $userIncident->count }}</span></td>
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

<!-- Recent Incidents -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Sự cố gần đây nhất</h5>
                <a href="{{ route('admin.incidents.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Vị trí</th>
                            <th>Người báo cáo</th>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentIncidents as $incident)
                        <tr>
                            <td>#{{ $incident->id }}</td>
                            <td>
                                <a href="{{ route('incidents.show', $incident) }}" class="text-decoration-none">
                                    {{ Str::limit($incident->title, 50) }}
                                </a>
                            </td>
                            <td>{{ $incident->location ?: 'Không xác định' }}</td>
                            <td>{{ $incident->user->name }}</td>
                            <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Không có sự cố nào</td>
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
// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyIncidents->pluck('month')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($monthlyIncidents->pluck('count')) !!},
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

// Daily Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyChart = new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($dailyIncidents->pluck('date')) !!},
        datasets: [{
            label: 'Số sự cố',
            data: {!! json_encode($dailyIncidents->pluck('count')) !!},
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
</script>
@endsection
