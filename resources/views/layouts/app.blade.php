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
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-shaded"></i> Anality
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    {{-- Ataques --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('attacks', 'attack-detail', 'charts.attacks-weekly', 'timeline') ? 'active' : '' }}"
                            href="#" id="attacksDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-bug"></i> Ataques
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="attacksDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('attacks') ? 'active' : '' }}"
                                    href="{{ route('attacks') }}">
                                    <i class="bi bi-list-ul me-2"></i> Lista de Ataques
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('timeline') ? 'active' : '' }}"
                                    href="{{ route('timeline') }}">
                                    <i class="bi bi-clock-history me-2"></i> Timeline
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('charts.attacks-weekly') ? 'active' : '' }}"
                                    href="{{ route('charts.attacks-weekly') }}">
                                    <i class="bi bi-bar-chart-line me-2"></i> Gráfico Semanal
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Análise --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('correlations', 'statistics') ? 'active' : '' }}"
                            href="#" id="analysisDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-graph-up-arrow"></i> Análise
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="analysisDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('correlations') ? 'active' : '' }}"
                                    href="{{ route('correlations') }}">
                                    <i class="bi bi-diagram-3 me-2"></i> Correlações
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('statistics') ? 'active' : '' }}"
                                    href="{{ route('statistics') }}">
                                    <i class="bi bi-bar-chart me-2"></i> Estatísticas
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Relatórios --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('report-attacks-view', 'report-attacks-chart') ? 'active' : '' }}"
                            href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-file-earmark-bar-graph"></i> Relatórios
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('report-attacks-view') ? 'active' : '' }}"
                                    href="{{ route('report-attacks-view') }}">
                                    <i class="bi bi-table me-2"></i> Relatório de Ataques
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('report-attacks-chart') ? 'active' : '' }}"
                                    href="{{ route('report-attacks-chart') }}">
                                    <i class="bi bi-pie-chart me-2"></i> Gráfico de Ataques
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Admin --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('news-import.form', 'attacks-import.form', 'attacks-batches.index') ? 'active' : '' }}"
                            href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-gear-fill"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li>
                                <h6 class="dropdown-header"><i class="bi bi-newspaper me-1"></i> Notícias</h6>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('news-import.form') ? 'active' : '' }}"
                                    href="{{ route('news-import.form') }}">
                                    <i class="bi bi-upload me-2"></i> Importar Notícias
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <h6 class="dropdown-header"><i class="bi bi-bug me-1"></i> Ataques</h6>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('attacks-import.form') ? 'active' : '' }}"
                                    href="{{ route('attacks-import.form') }}">
                                    <i class="bi bi-upload me-2"></i> Importar Ataques
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('attacks-batches.index') ? 'active' : '' }}"
                                    href="{{ route('attacks-batches.index') }}">
                                    <i class="bi bi-collection me-2"></i> Gerenciar Lotes
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
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

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
    </script>

    @stack('scripts')
</body>

</html>
