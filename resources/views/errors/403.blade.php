<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.Access Denied') }} - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .error-container {
            text-align: center;
            color: white;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            text-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }
        .error-icon {
            font-size: 100px;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card p-5">
                    <div class="error-container">
                        <i class="bi bi-shield-exclamation error-icon text-danger"></i>
                        <div class="error-code text-danger">403</div>
                        <h2 class="mb-3 text-dark">{{ __('messages.Access Denied') }}</h2>
                        <p class="lead text-muted mb-4">
                            {{ __('messages.You do not have permission to access this page.') }}
                        </p>
                        
                        @auth
                            @if(auth()->user()->role === 'employee')
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    {{ __('messages.As an employee, you can only report new incidents. Viewing incident lists and details is restricted to managers only.') }}
                                </div>
                            @endif
                        @endauth
                        
                        <div class="d-flex gap-3 justify-content-center mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="bi bi-house-door me-2"></i>{{ __('messages.Go Home') }}
                            </a>
                            @auth
                                <a href="{{ route('incidents.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-2"></i>{{ __('messages.Report Incident') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

