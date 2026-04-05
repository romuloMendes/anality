@extends('layouts.app')

@section('title', 'Ataques por Semana - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <p class="page-header-tag">// gráficos</p>
                    <h1 class="page-header-title">Ataques por Semana</h1>
                    <p class="page-header-sub">quantidade total de ataques agrupados em intervalos de 7 dias</p>
                </div>
            </div>
        </div>

        {{-- Filtro de intervalo de datas --}}
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('charts.attacks-weekly') }}"
                    class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">Data Inicial</label>
                        <input type="date" id="date_from" name="date_from" class="form-control"
                            value="{{ $dateFrom->toDateString() }}" max="{{ $dateTo->toDateString() }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">Data Final</label>
                        <input type="date" id="date_to" name="date_to" class="form-control"
                            value="{{ $dateTo->toDateString() }}" min="{{ $dateFrom->toDateString() }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                        <a href="{{ route('charts.attacks-weekly') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Gráfico --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Ataques semanais — {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</h5>
                <span class="badge badge-medium">{{ count($chartData) }}
                    {{ Str::plural('semana', count($chartData)) }}</span>
            </div>
            <div class="card-body">
                @if (count($chartData) === 0)
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Nenhum dado encontrado para o período selecionado.
                    </div>
                @else
                    <canvas id="weeklyAttacksChart" style="max-height: 420px;"></canvas>
                @endif
            </div>
        </div>

        {{-- Gráfico de Linha --}}
        @if (count($chartData) > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Tendência semanal — {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="weeklyAttacksLineChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        @endif

        {{-- Tabela de dados --}}
        @if (count($chartData) > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Dados por Intervalo</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Intervalo</th>
                                <th>Data Inicial</th>
                                <th>Data Final</th>
                                <th class="text-end">Total de Ataques</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chartData as $i => $bucket)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>{{ $bucket['label'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bucket['start'])->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bucket['end'])->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <span class="badge {{ $bucket['total'] > 0 ? 'bg-danger' : 'bg-secondary' }} fs-6">
                                            {{ $bucket['total'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total no período:</td>
                                <td class="text-end">
                                    <span class="badge bg-primary fs-6">
                                        {{ collect($chartData)->sum('total') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

    </div>

    @if (count($chartData) > 0)
        <script>
            const chartData = @json($chartData);

            const labels = chartData.map(b => b.label);
            const totals = chartData.map(b => b.total);
            const maxVal = Math.max(...totals);

            // Cores: vermelho para picos, azul para valores normais
            const bgColors = totals.map(v => {
                if (maxVal === 0) return 'rgba(0,229,255,0.5)';
                const ratio = v / maxVal;
                if (ratio >= 0.8) return 'rgba(255,59,92,0.7)';
                if (ratio >= 0.5) return 'rgba(255,184,0,0.7)';
                return 'rgba(0,229,255,0.5)';
            });

            const ctx = document.getElementById('weeklyAttacksChart').getContext('2d');
            const ctxLine = document.getElementById('weeklyAttacksLineChart').getContext('2d');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total de Ataques',
                        data: totals,
                        fill: true,
                        backgroundColor: 'rgba(0,229,255,0.06)',
                        borderColor: 'rgba(0,229,255,0.9)',
                        pointBackgroundColor: totals.map(v => {
                            if (maxVal === 0) return 'rgba(0,229,255,0.9)';
                            const ratio = v / maxVal;
                            if (ratio >= 0.8) return 'rgba(255,59,92,1)';
                            if (ratio >= 0.5) return 'rgba(255,184,0,1)';
                            return 'rgba(0,229,255,0.9)';
                        }),
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.35,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: (items) => 'Semana: ' + items[0].label,
                                label: (item) => ' ' + item.raw + ' ataque(s)',
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Intervalo Semanal',
                                color: '#4a6480',
                                font: {
                                    weight: 'bold',
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 30,
                                color: '#4a6480',
                                font: {
                                    size: 10,
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            grid: {
                                color: '#1a2d45'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantidade de Ataques',
                                color: '#4a6480',
                                font: {
                                    weight: 'bold',
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                color: '#4a6480',
                                font: {
                                    size: 10,
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            grid: {
                                color: '#1a2d45'
                            }
                        }
                    }
                }
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total de Ataques',
                        data: totals,
                        backgroundColor: bgColors,
                        borderColor: bgColors.map(c => c.replace('0.7', '1').replace('0.8', '1')),
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: (items) => 'Semana: ' + items[0].label,
                                label: (item) => ' ' + item.raw + ' ataque(s)',
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Intervalo Semanal',
                                color: '#4a6480',
                                font: {
                                    weight: 'bold',
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 30,
                                color: '#4a6480',
                                font: {
                                    size: 10,
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            grid: {
                                color: '#1a2d45'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantidade de Ataques',
                                color: '#4a6480',
                                font: {
                                    weight: 'bold',
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                color: '#4a6480',
                                font: {
                                    size: 10,
                                    family: "'Share Tech Mono', monospace"
                                }
                            },
                            grid: {
                                color: '#1a2d45'
                            }
                        }
                    }
                }
            });

            // Re-fetch via API quando o filtro mudar (AJAX opcional)
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const dateFrom = document.getElementById('date_from').value;
                const dateTo = document.getElementById('date_to').value;

                if (!dateFrom || !dateTo) return;

                fetch(`{{ route('api.charts.attacks-weekly') }}?date_from=${dateFrom}&date_to=${dateTo}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.errors) {
                            alert('Datas inválidas. Verifique os campos e tente novamente.');
                            return;
                        }

                        const chart = Chart.getChart('weeklyAttacksChart');
                        const lineChart = Chart.getChart('weeklyAttacksLineChart');
                        const newLabels = data.map(b => b.label);
                        const newTotals = data.map(b => b.total);
                        const newMax = Math.max(...newTotals);

                        const newColors = newTotals.map(v => {
                            if (newMax === 0) return 'rgba(0,229,255,0.5)';
                            const ratio = v / newMax;
                            if (ratio >= 0.8) return 'rgba(220, 53, 69, 0.8)';
                            if (ratio >= 0.5) return 'rgba(255, 193, 7, 0.8)';
                            return 'rgba(54, 162, 235, 0.7)';
                        });

                        const newPointColors = newTotals.map(v => {
                            if (newMax === 0) return 'rgba(13, 110, 253, 0.9)';
                            const ratio = v / newMax;
                            if (ratio >= 0.8) return 'rgba(255,59,92,1)';
                            if (ratio >= 0.5) return 'rgba(255,184,0,1)';
                            return 'rgba(0,229,255,0.9)';
                        });

                        chart.data.labels = newLabels;
                        chart.data.datasets[0].data = newTotals;
                        chart.data.datasets[0].backgroundColor = newColors;
                        chart.data.datasets[0].borderColor = newColors.map(c => c.replace('0.7', '1').replace('0.8',
                            '1'));
                        chart.update();

                        lineChart.data.labels = newLabels;
                        lineChart.data.datasets[0].data = newTotals;
                        lineChart.data.datasets[0].pointBackgroundColor = newPointColors;
                        lineChart.update();

                        // Atualiza a URL sem recarregar
                        const url = new URL(window.location);
                        url.searchParams.set('date_from', dateFrom);
                        url.searchParams.set('date_to', dateTo);
                        window.history.replaceState({}, '', url);
                    })
                    .catch(() => alert('Erro ao buscar os dados. Tente novamente.'));
            });
        </script>
    @endif
@endsection
