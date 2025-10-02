<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SafetyAI Risk Management') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="bi bi-shield-check-fill text-primary me-2"></i>
                <span class="text-gradient">SafetyAI</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Language Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-globe me-1"></i>
                            @if(app()->getLocale() == 'ja')
                                日本語
                            @else
                                Tiếng Việt
                            @endif
                        </a>
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
                    </li>
                    
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                @if(auth()->user()->role === 'manager')
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-1"></i>{{ __('messages.Dashboard') }}
                                    </a>
                                @endif
                            </li>
                            <li class="nav-item">
                                @if(auth()->user()->role === 'employee')
                                    <a class="nav-link" href="{{ route('incidents.create') }}">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('messages.Report Incident') }}
                                    </a>
                                @endif
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>
                                    {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form action="{{ route('logout') }}" method="post" class="d-inline">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('messages.Logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('messages.Login') }}
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-primary" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-1"></i>{{ __('messages.Register') }}
                                    </a>
                                </li>
                            @endif
                        @endauth
        @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4">
                        {{ __('messages.Smart Risk Management with') }}
                        <span class="text-warning">AI</span>
                    </h1>
                    <p class="lead mb-4">
                        {{ __('messages.AI-powered enterprise incident and risk management system') }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
            @if (Route::has('login'))
                    @auth
                        @if(auth()->user()->role === 'manager')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-custom">
                                <i class="bi bi-speedometer2 me-2"></i>{{ __('messages.Access Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('incidents.create') }}" class="btn btn-warning btn-custom">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('messages.Report Risk') }}
                            </a>
                        @endif
                    @else
                                <a href="{{ route('login') }}" class="btn btn-warning btn-custom">
                                    <i class="bi bi-play-circle me-2"></i>{{ __('messages.Get Started') }}
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-custom">
                                        <i class="bi bi-person-plus me-2"></i>{{ __('messages.Free Registration') }}
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="floating-animation">
                        <i class="bi bi-robot" style="font-size: 15rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-counter">1000+</div>
                    <p class="text-secondary-custom">{{ __('messages.Trusted businesses') }}</p>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-counter">99.9%</div>
                    <p class="text-secondary-custom">{{ __('messages.AI Accuracy') }}</p>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-counter">24/7</div>
                    <p class="text-secondary-custom">{{ __('messages.Continuous Monitoring') }}</p>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-counter">50+</div>
                    <p class="text-secondary-custom">{{ __('messages.Advanced Features') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section-padding features-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5 section-header">
                    <h2 class="display-5 fw-bold mb-3">{{ __('messages.Outstanding Features') }}</h2>
                    <p class="lead">
                        {{ __('messages.Advanced AI technology helps businesses manage risks comprehensively and effectively') }}
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary mx-auto">
                                <i class="bi bi-lightbulb"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ __('messages.AI Risk Prediction') }}</h5>
                            <p class="card-text">
                                {{ __('messages.Use machine learning to analyze data and predict potential risks before they occur.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-success mx-auto">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ __('messages.Real-time Alerts') }}</h5>
                            <p class="card-text">
                                {{ __('messages.24/7 monitoring system with instant notifications via email, SMS and mobile app.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-info mx-auto">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ __('messages.Smart Reports') }}</h5>
                            <p class="card-text">
                                {{ __('messages.Automatically generate detailed reports with visual charts and risk trend analysis.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-warning mx-auto">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ __('messages.Team Management') }}</h5>
                            <p class="card-text">
                                {{ __('messages.Manage work groups, assign incident handling tasks systematically.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-danger mx-auto">
                                <i class="bi bi-clock"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ __('messages.Emergency Response') }}</h5>
                            <p class="card-text">
                                {{ __('messages.Automated incident handling process with clear steps and optimal response time.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-dark mx-auto">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ __('messages.High Security') }}</h5>
                            <p class="card-text">
                                {{ __('messages.Data encryption and compliance with international security standards to protect business information.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section text-white section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-4 fw-bold mb-4">{{ __('messages.Ready to get started?') }}</h2>
                    <p class="lead mb-5">
                        {{ __('messages.Join thousands of businesses that trust SafetyAI for effective risk management.') }}
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                @if(auth()->user()->role === 'manager')
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-custom">
                                        <i class="bi bi-speedometer2 me-2"></i>{{ __('messages.Access Dashboard') }}
                                    </a>
                                @else
                                    <a href="{{ route('incidents.create') }}" class="btn btn-light btn-custom">
                                        <i class="bi bi-plus-circle me-2"></i>{{ __('messages.Report Risk') }}
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-light btn-custom">
                                    <i class="bi bi-play-circle me-2"></i>{{ __('messages.Start Free') }}
                                </a>
                        @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-custom">
                                        <i class="bi bi-person-plus me-2"></i>{{ __('messages.Register Now') }}
                            </a>
                        @endif
                    @endauth
            @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-shield-check-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                        <span class="h4 mb-0 text-gradient">SafetyAI</span>
                    </div>
                    <p>
                        {{ __('messages.AI-powered enterprise incident and risk management system') }}
                    </p>
                </div>

                <div class="col-lg-4"></div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">{{ __('messages.Contact') }}</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2"></i>{{ __('messages.Contact Email') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i>{{ __('messages.Contact Phone') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i>{{ __('messages.Contact Address') }}
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(59, 130, 246, 0.2);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 SafetyAI. {{ __('messages.All rights reserved') }}.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end gap-3">
                        <a href="#" class="text-light"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add animation to feature cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Counter animation with glow effect
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current) + (target >= 100 ? '+' : '%');
                
                // Add glow effect during animation
                element.style.textShadow = `0 0 ${Math.random() * 15 + 5}px rgba(59, 130, 246, 0.6)`;
            }, 20);
            
            // Remove glow effect after animation
            setTimeout(() => {
                element.style.textShadow = '0 0 20px rgba(59, 130, 246, 0.3)';
            }, 2000);
        }

        // Trigger counter animation when stats section is visible
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stats-counter');
                    counters.forEach(counter => {
                        const text = counter.textContent;
                        const target = parseInt(text.replace(/[^\d]/g, ''));
                        animateCounter(counter, target);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.py-5');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // Add particle effect to hero section
        function createParticles() {
            const heroSection = document.querySelector('.hero-section');
            if (!heroSection) return;

            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.style.position = 'absolute';
                particle.style.width = Math.random() * 4 + 1 + 'px';
                particle.style.height = particle.style.width;
                particle.style.background = `hsl(${Math.random() * 60 + 200}, 70%, 60%)`;
                particle.style.borderRadius = '50%';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.opacity = Math.random() * 0.5 + 0.2;
                particle.style.animation = `float ${Math.random() * 10 + 5}s linear infinite`;
                particle.style.pointerEvents = 'none';
                heroSection.appendChild(particle);
            }
        }

        // Add floating animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Initialize particles when page loads
        createParticles();

        // Add mouse tracking effect for feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
            });
        });
    </script>
    </body>
</html>
