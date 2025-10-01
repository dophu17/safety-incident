<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Chart.js for statistics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .admin-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dropdown-item.active {
            background-color: #667eea;
            color: white;
        }
        .dropdown-item:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
        /* Pagination Styling */
        .pagination {
            margin-bottom: 0;
        }
        .page-link {
            color: #667eea;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }
        .page-link:hover {
            color: #764ba2;
            background-color: rgba(102, 126, 234, 0.1);
            border-color: #667eea;
        }
        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
        /* Action Buttons Spacing */
        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }
        .btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 admin-sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-shield-alt me-2"></i>
                        Admin Panel
                    </h4>
                    
                    <nav class="nav flex-column">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.statistics') }}" class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar me-2"></i>
                            Thống kê chi tiết
                        </a>
                        <a href="{{ route('admin.company') }}" class="nav-link {{ request()->routeIs('admin.company') ? 'active' : '' }}">
                            <i class="fas fa-building me-2"></i>
                            Thông tin công ty
                        </a>
                        <a href="{{ route('admin.incidents.index') }}" class="nav-link {{ request()->routeIs('admin.incidents.*') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Quản lý sự cố
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users me-2"></i>
                            Quản lý người dùng
                        </a>
                        <hr class="text-white-50">
                        <a href="{{ url('/') }}" class="nav-link">
                            <i class="fas fa-home me-2"></i>
                            Về trang chủ
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted me-2">Xin chào, {{ Auth::user()->name }}</span>
                            
                            <!-- Language Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-globe me-1"></i>
                                    @if(app()->getLocale() == 'ja')
                                        日本語
                                    @else
                                        Tiếng Việt
                                    @endif
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() == 'vn' ? 'active' : '' }}" href="{{ route('language.switch', 'vn') }}">
                                            <span class="me-2">🇻🇳</span>Tiếng Việt
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() == 'ja' ? 'active' : '' }}" href="{{ route('language.switch', 'ja') }}">
                                            <span class="me-2">🇯🇵</span>日本語
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
