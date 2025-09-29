<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('incidents.index') }}">Safety Incident</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ route('incidents.index') }}">{{ __('Incidents') }}</a></li>
        @auth
          <li class="nav-item"><a class="nav-link" href="{{ route('incidents.create') }}">{{ __('Report') }}</a></li>
          @if(auth()->user()->role === 'manager')
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.incidents.index') }}">{{ __('Admin') }}</a></li>
          @endif
        @endauth
      </ul>
      <form action="{{ route('locale.set') }}" method="post" class="d-flex me-2">
        @csrf
        <select name="locale" class="form-select form-select-sm me-2" onchange="this.form.submit()">
          <option value="ja" @selected(app()->getLocale()==='ja')>日本語</option>
          <option value="vn" @selected(app()->getLocale()==='vn')>Tiếng Việt</option>
        </select>
      </form>
      <ul class="navbar-nav mb-2 mb-lg-0">
        @auth
          <li class="nav-item"><span class="navbar-text me-2">{{ auth()->user()->name }}</span></li>
          <li class="nav-item">
            <form action="{{ route('logout') }}" method="post" class="d-inline">
              @csrf
              <button class="btn btn-sm btn-outline-light">{{ __('Logout') }}</button>
            </form>
          </li>
        @else
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
        @endauth
      </ul>
    </div>
  </div>
  </nav>
  <main class="container py-4">
    @yield('content')
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


