@extends('layouts.app')

@section('title', 'Estatísticas - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="page-header mb-4">
            <p class="page-header-tag">// métricas</p>
            <h1 class="page-header-title">Estatísticas Detalhadas</h1>
            <p class="page-header-sub">distribuição e análise quantitativa dos dados</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Ataques por Tipo</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="attackTypesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Distribuição por Severidade</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="severityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Tipos de Correlação</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="correlationTypesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Resumo</h5>
                    </div>
                    <div class="card-body">
                        <div class="kpi-card mb-3">
                            <span class="kpi-value">{{ round($averageCorrelationScore, 1) }}%</span>
                            <span class="kpi-label">Score Médio de Correlação</span>
                        </div>
                        <hr>
                        <p class="form-label mb-2">Ataques por Tipo</p>
                        <ul class="mb-0" style="list-style: none; padding: 0;">
                            @foreach ($attacksByType as $item)
                                <li class="d-flex justify-content-between py-1" style="border-bottom: 1px solid var(--border); font-size: 12px; font-family: 'Share Tech Mono', monospace;">
                                    <span style="color: var(--text);">{{ $item->attack_type }}</span>
                                    <span style="color: var(--accent);">{{ $item->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-footer d-flex justify-content-between mt-4">
            <span>ESTATÍSTICAS — ANALITY</span>
            <span id="stats-footer-ts"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('stats-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');

        const chartDefaults = {
            color: '#4a6480',
            plugins: { legend: { labels: { color: '#c8d8e8', font: { family: "'Share Tech Mono', monospace", size: 11 } } } },
            scales: {}
        };

        // Ataques por Tipo
        new Chart(document.getElementById('attackTypesChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($attacksByType->pluck('attack_type')) !!},
                datasets: [{ data: {!! json_encode($attacksByType->pluck('count')) !!},
                    backgroundColor: ['rgba(255,59,92,0.7)','rgba(0,229,255,0.7)','rgba(255,184,0,0.7)','rgba(0,230,118,0.7)','rgba(74,100,128,0.7)'],
                    borderColor: '#1a2d45', borderWidth: 1 }]
            },
            options: { ...chartDefaults }
        });

        // Severidade
        new Chart(document.getElementById('severityChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($attacksBySeverity->pluck('severity')) !!},
                datasets: [{ label: 'Quantidade', data: {!! json_encode($attacksBySeverity->pluck('count')) !!},
                    backgroundColor: ['rgba(255,59,92,0.7)','rgba(255,184,0,0.7)','rgba(0,229,255,0.7)','rgba(0,230,118,0.7)'],
                    borderColor: '#1a2d45', borderWidth: 1, borderRadius: 4 }]
            },
            options: { responsive: true, plugins: chartDefaults.plugins,
                scales: { x: { ticks: { color: '#4a6480', font: { family: "'Share Tech Mono', monospace", size: 10 } }, grid: { color: '#1a2d45' } },
                          y: { beginAtZero: true, ticks: { color: '#4a6480', font: { family: "'Share Tech Mono', monospace", size: 10 } }, grid: { color: '#1a2d45' } } } }
        });

        // Tipos de Correlação
        new Chart(document.getElementById('correlationTypesChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($correlationsByType->pluck('correlation_type')) !!},
                datasets: [{ data: {!! json_encode($correlationsByType->pluck('count')) !!},
                    backgroundColor: ['rgba(255,59,92,0.7)','rgba(0,229,255,0.7)','rgba(255,184,0,0.7)','rgba(0,230,118,0.7)'],
                    borderColor: '#1a2d45', borderWidth: 1 }]
            },
            options: { ...chartDefaults }
        });
    </script>
    @endpush
@endsection
