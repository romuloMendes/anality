@extends('layouts.app')

@section('title', 'Estatísticas - Anality')

@section('content')
    <div class="container-fluid py-5">
        <h1 class="mb-4">Estatísticas Detalhadas</h1>

        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>Ataques por Tipo</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="attackTypesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5>Distribuição por Severidade</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="severityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Tipos de Correlação</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="correlationTypesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Resumo</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Score Médio de Correlação:</strong> {{ round($averageCorrelationScore, 2) }}%</p>
                        <hr>
                        <p><strong>Ataques por Tipo:</strong></p>
                        <ul>
                            @foreach ($attacksByType as $item)
                                <li>{{ $item->attack_type }}: <strong>{{ $item->count }}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Ataques por Tipo
        const attackCtx = document.getElementById('attackTypesChart').getContext('2d');
        new Chart(attackCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($attacksByType->pluck('attack_type')) !!},
                datasets: [{
                    data: {!! json_encode($attacksByType->pluck('count')) !!},
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            }
        });

        // Gráfico de Severidade
        const severityCtx = document.getElementById('severityChart').getContext('2d');
        new Chart(severityCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($attacksBySeverity->pluck('severity')) !!},
                datasets: [{
                    label: 'Quantidade',
                    data: {!! json_encode($attacksBySeverity->pluck('count')) !!},
                    backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#28a745']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Gráfico de Tipos de Correlação
        const corrCtx = document.getElementById('correlationTypesChart').getContext('2d');
        new Chart(corrCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($correlationsByType->pluck('correlation_type')) !!},
                datasets: [{
                    data: {!! json_encode($correlationsByType->pluck('count')) !!},
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            }
        });
    </script>
@endsection
