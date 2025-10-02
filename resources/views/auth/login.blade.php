@extends('layouts.app')

@section('title', __('auth.Login') . ' - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="content-card">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-check-fill text-primary" style="font-size: 3rem;"></i>
                    <h1 class="h3 mb-2">{{ __('auth.Login') }}</h1>
                    <p class="text-muted">{{ __('auth.Sign in to your SafetyAI account') }}</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="post" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-envelope me-1"></i>{{ __('auth.Email Address') }}
                        </label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" required autofocus
                               placeholder="{{ __('auth.Enter your email') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-lock me-1"></i>{{ __('auth.Password') }}
                        </label>
                        <input type="password" name="password" class="form-control" required
                               placeholder="{{ __('auth.Enter your password') }}">
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            {{ __('auth.Remember Me') }}
                        </label>
                    </div>
                    
                    <button class="btn btn-primary w-100 mb-3" type="submit">
                        <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('auth.Login') }}
                    </button>
                </form>

                <div class="text-center">
                    <p class="mb-0 text-muted">
                        {{ __('auth.Don\'t have an account?') }}
                        <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
                            {{ __('auth.Register') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection