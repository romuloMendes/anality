@extends('layouts.app')

@section('title', 'Dashboard - Anality')

@section('content')
    <div class="container-fluid py-5">
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4">Dashboard de Análise</h1>
                <p class="text-muted">Análise Integrada de Ataques Hackers e Notícias</p>
            </div>
        </div>

        <!-- Painel de Ações Rápidas -->
        <div class="row mb-5">
            <div class="col-md-4">
                <button class="btn btn-primary btn-lg w-100" id="scrapeAttacksBtn">
                    <i class="bi bi-shield-exclamation"></i> Atualizar Ataques
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-info btn-lg w-100" id="scrapeNewsBtn">
                    <i class="bi bi-newspaper"></i> Atualizar Notícias
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-success btn-lg w-100" id="analyzeBtn">
                    <i class="bi bi-graph-up"></i> Executar Análise Completa
                </button>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Ataques Críticos
                        </h5>
                        <p class="card-text display-5">{{ $stats['critical_attacks'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-shield-exclamation"></i> Total de Ataques
                        </h5>
                        <p class="card-text display-5">{{ $stats['total_attacks'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            <i class="bi bi-newspaper"></i> Notícias
                        </h5>
                        <p class="card-text display-5">{{ $stats['total_news'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-link-45deg"></i> Correlações
                        </h5>
                        <p class="card-text display-5">{{ $stats['total_correlations'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linha 2 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>Ataques Recentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Severidade</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAttacks as $attack)
                                        <tr>
                                            <td>
                                                <a href="{{ route('attack-detail', $attack->id) }}">
                                                    {{ $attack->attack_type }}
                                                </a>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ \App\Helpers\ViewHelper::severityColor($attack->severity) }}">
                                                    {{ ucfirst($attack->severity) }}
                                                </span>
                                            </td>
                                            <td>{{ $attack->attack_date->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Nenhum ataque registrado</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Notícias Recentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Fonte</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentNews as $news)
                                        <tr>
                                            <td>{{ Str::limit($news->title, 30) }}</td>
                                            <td>{{ $news->source_name }}</td>
                                            <td>{{ $news->published_date->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Nenhuma notícia registrada
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Correlações principais -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Correlações Mais Fortes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ataque</th>
                                        <th>Notícia</th>
                                        <th>Score</th>
                                        <th>Tipo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCorrelations as $correlation)
                                        <tr>
                                            <td>{{ Str::limit($correlation->hackerAttack->title, 40) }}</td>
                                            <td>{{ Str::limit($correlation->news->title, 40) }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $correlation->correlation_score }}%">
                                                        {{ round($correlation->correlation_score) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary">{{ $correlation->correlation_type }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Nenhuma correlação encontrada
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBase = '{{ url('/api') }}';

        document.getElementById('scrapeAttacksBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

            fetch(`${apiBase}/scrape/attacks`, {
                    method: 'POST'
                })
                .then(r => r.json())
                .then(data => {
                    alert('✓ ' + data.message);
                    location.reload();
                })
                .catch(e => alert('✗ Erro: ' + e.message))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-shield-exclamation"></i> Atualizar Ataques';
                });
        });

        document.getElementById('scrapeNewsBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

            fetch(`${apiBase}/scrape/news`, {
                    method: 'POST'
                })
                .then(r => r.json())
                .then(data => {
                    alert('✓ ' + data.message);
                    location.reload();
                })
                .catch(e => alert('✗ Erro: ' + e.message))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-newspaper"></i> Atualizar Notícias';
                });
        });

        document.getElementById('analyzeBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

            fetch(`${apiBase}/analyze/full`, {
                    method: 'POST'
                })
                .then(r => r.json())
                .then(data => {
                    alert('✓ Análise completa: ' + JSON.stringify(data.data));
                    location.reload();
                })
                .catch(e => alert('✗ Erro: ' + e.message))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-graph-up"></i> Executar Análise Completa';
                });
        });
    </script>
@endsection
