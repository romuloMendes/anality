@extends('layouts.app')

@section('title', 'Dashboard - Anality')

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="page-header mb-4">
            <p class="page-header-tag">// visão geral</p>
            <h1 class="page-header-title">Dashboard de Análise</h1>
            <p class="page-header-sub">análise integrada de ataques hackers e notícias</p>
        </div>

        {{-- KPI Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <span class="kpi-value danger">{{ $stats['critical_attacks'] }}</span>
                    <span class="kpi-label">Ataques Críticos</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <span class="kpi-value">{{ $stats['total_attacks'] }}</span>
                    <span class="kpi-label">Total de Ataques</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <span class="kpi-value ok">{{ $stats['total_news'] }}</span>
                    <span class="kpi-label">Notícias</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <span class="kpi-value warn">{{ $stats['total_correlations'] }}</span>
                    <span class="kpi-label">Correlações</span>
                </div>
            </div>
        </div>

        {{-- Ações Rápidas --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <button class="btn btn-primary w-100" id="scrapeAttacksBtn">
                    <i class="bi bi-shield-exclamation"></i> Atualizar Ataques
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-info w-100" id="scrapeNewsBtn">
                    <i class="bi bi-newspaper"></i> Atualizar Notícias
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-success w-100" id="analyzeBtn">
                    <i class="bi bi-graph-up"></i> Executar Análise Completa
                </button>
            </div>
        </div>

        {{-- Tabelas Recentes --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Ataques Recentes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
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
                                                <span class="badge badge-{{ $attack->severity }}">
                                                    {{ ucfirst($attack->severity) }}
                                                </span>
                                            </td>
                                            <td>{{ $attack->attack_date->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Nenhum ataque registrado</td>
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
                    <div class="card-header">
                        <h5>Notícias Recentes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
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
                                            <td colspan="3" class="text-center text-muted py-4">Nenhuma notícia registrada</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Correlações Mais Fortes --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Correlações Mais Fortes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
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
                                                <div class="progress" style="height: 18px; width: 120px;">
                                                    <div class="progress-bar"
                                                        style="width: {{ $correlation->correlation_score }}%">
                                                        {{ round($correlation->correlation_score) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $correlation->correlation_type }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Nenhuma correlação encontrada</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Page Footer --}}
        <div class="page-footer d-flex justify-content-between mt-4">
            <span>SISTEMA DE CORRELAÇÃO DE ATAQUES v1.0</span>
            <span id="dash-footer-ts"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('dash-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
        const apiBase = '{{ url('/api') }}';

        document.getElementById('scrapeAttacksBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            fetch(`${apiBase}/scrape/attacks`, { method: 'POST' })
                .then(r => r.json())
                .then(data => { alert('✓ ' + data.message); location.reload(); })
                .catch(e => alert('✗ Erro: ' + e.message))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-shield-exclamation"></i> Atualizar Ataques';
                });
        });

        document.getElementById('scrapeNewsBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            fetch(`${apiBase}/scrape/news`, { method: 'POST' })
                .then(r => r.json())
                .then(data => { alert('✓ ' + data.message); location.reload(); })
                .catch(e => alert('✗ Erro: ' + e.message))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-newspaper"></i> Atualizar Notícias';
                });
        });

        document.getElementById('analyzeBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            fetch(`${apiBase}/analyze/full`, { method: 'POST' })
                .then(r => r.json())
                .then(data => { alert('✓ Análise completa: ' + JSON.stringify(data.data)); location.reload(); })
                .catch(e => alert('✗ Erro: ' + e.message))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-graph-up"></i> Executar Análise Completa';
                });
        });
    </script>
    @endpush
@endsection
