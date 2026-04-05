<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Anality')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Barlow:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Anality Theme -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Anality</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('attacks') ? 'active' : '' }}"
                            href="{{ route('attacks') }}">Ataques</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('correlations') ? 'active' : '' }}"
                            href="{{ route('correlations') }}">Correlações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('statistics') ? 'active' : '' }}"
                            href="{{ route('statistics') }}">Estatísticas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('timeline') ? 'active' : '' }}"
                            href="{{ route('timeline') }}">Timeline</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('news-import.form') ? 'active' : '' }}"
                                    href="{{ route('news-import.form') }}">
                                    <i class="bi bi-upload"></i> Importar Notícias
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('attacks-import.form') ? 'active' : '' }}"
                                    href="{{ route('attacks-import.form') }}">
                                    <i class="bi bi-upload"></i> Importar Ataques
                                </a>
                            </li>
                        </ul>
                    </li>
            </div>
        </div>
    </nav>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @if ($errors->any())
            <div class="container-fluid mb-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erros encontrados:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="container-fluid mb-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-4 mt-5">
        <div class="container-fluid">
            <div class="page-footer d-flex justify-content-between">
                <span>ANALITY — SISTEMA DE CORRELAÇÃO DE ATAQUES v1.0</span>
                <span id="footer-ts"></span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
    </script>

    @stack('scripts')
</body>

</html>
