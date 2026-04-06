@extends('layouts.app')

@section('title', 'Notícias por Relevância – Anality')

@push('styles')
    <style>
        .filter-bar {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
            flex-wrap: wrap;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: .9rem 1.1rem;
            margin-bottom: 1.25rem;
        }

        .filter-bar label {
            display: block;
            font-size: .7rem;
            color: var(--muted);
            margin-bottom: .25rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .filter-bar input[type="date"] {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: "Share Tech Mono", monospace;
            font-size: .85rem;
            padding: .4rem .6rem;
            border-radius: 4px;
            outline: none;
        }

        .filter-bar input[type="date"]:focus {
            border-color: var(--accent);
        }

        .kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: .75rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 768px) {
            .kpis {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .kpi {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: .9rem 1rem;
        }

        .kpi .kpi-label {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
            margin-bottom: .3rem;
        }

        .kpi .kpi-value {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1;
        }

        .kpi .kpi-value.green {
            color: #3fb950;
        }

        .kpi .kpi-value.yellow {
            color: var(--warn);
        }

        .kpi .kpi-value.red {
            color: var(--danger);
        }

        .kpi .kpi-value.white {
            color: var(--text);
        }

        .chart-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 1rem 1.25rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .chart-card-title {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
        }

        .chart-legend {
            display: flex;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            color: var(--muted);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        #chart-wrapper {
            position: relative;
            height: 360px;
        }

        #chart-loader {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            z-index: 10;
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        #error-msg {
            display: none;
            background: rgba(255, 59, 92, .1);
            border: 1px solid rgba(255, 59, 92, .4);
            border-radius: 6px;
            padding: .75rem 1rem;
            font-size: .82rem;
            color: var(--danger);
            margin-bottom: 1rem;
        }

        /* Tabela de dados brutos */
        .data-table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .data-table-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: .78rem;
        }

        .data-table-card thead th {
            background: rgba(255, 255, 255, .04);
            padding: .55rem .9rem;
            text-align: left;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }

        .data-table-card tbody tr:hover {
            background: rgba(255, 255, 255, .03);
        }

        .data-table-card tbody td {
            padding: .52rem .9rem;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
            color: var(--text);
        }

        .badge-baixo {
            color: #3fb950;
        }

        .badge-medio {
            color: var(--warn);
        }

        .badge-alto {
            color: var(--danger);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- Filtro --}}
        <div class="filter-bar">
            <div>
                <label for="date-from">Data Inicial</label>
                <input type="date" id="date-from" value="{{ $from->toDateString() }}">
            </div>
            <div>
                <label for="date-to">Data Final</label>
                <input type="date" id="date-to" value="{{ $to->toDateString() }}">
            </div>
            <button id="btn-filter" class="btn btn-primary">Filtrar</button>
            <button id="btn-reset" class="btn btn-secondary">Limpar</button>
        </div>

        <div id="error-msg"></div>

        {{-- KPIs --}}
        <div class="kpis">
            <div class="kpi">
                <div class="kpi-label">Total de Notícias</div>
                <div class="kpi-value white" id="kpi-total">—</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Relevância Alta</div>
                <div class="kpi-value red" id="kpi-alto">—</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Relevância Média</div>
                <div class="kpi-value yellow" id="kpi-medio">—</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Relevância Baixa</div>
                <div class="kpi-value green" id="kpi-baixo">—</div>
            </div>
        </div>

        {{-- Gráfico --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <span class="chart-card-title">Notícias por Nível de Relevância · Intervalos de 7 dias</span>
                <div class="chart-legend">
                    <span class="legend-item">
                        <span class="legend-dot" style="background: rgba(63, 185, 80, 0.9)"></span> Baixo (1–3)
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: rgba(255, 184, 0, 0.9)"></span> Médio (4–6)
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: rgba(255, 59, 92, 0.9)"></span> Alto (7–10)
                    </span>
                </div>
            </div>
            <div id="chart-wrapper">
                <div id="chart-loader">
                    <div class="spinner"></div>
                </div>
                <canvas id="news-relevance-chart"></canvas>
            </div>
        </div>

        {{-- Tabela de dados brutos --}}
        <div class="data-table-card">
            <table id="raw-table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Nível</th>
                        <th>Score</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="raw-tbody">
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--muted);padding:1.5rem">Carregando…</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';

            // ── Configurações ──────────────────────────────────────────────
            const API_URL = '{{ route('api.report-news-relevance') }}';
            const DEFAULT_FROM = '{{ $from->toDateString() }}';
            const DEFAULT_TO = '{{ $to->toDateString() }}';

            const SERIES_CONFIG = {
                baixo: {
                    label: 'Baixo (1–3)',
                    color: 'rgba(63, 185, 80, 0.9)',
                    fill: 'rgba(63, 185, 80, 0.1)'
                },
                medio: {
                    label: 'Médio (4–6)',
                    color: 'rgba(255, 184, 0, 0.9)',
                    fill: 'rgba(255, 184, 0, 0.1)'
                },
                alto: {
                    label: 'Alto (7–10)',
                    color: 'rgba(255, 59, 92, 0.9)',
                    fill: 'rgba(255, 59, 92, 0.1)'
                },
            };

            // ── Estado ─────────────────────────────────────────────────────
            let chart = null;

            // ── Elementos DOM ──────────────────────────────────────────────
            const inputFrom = document.getElementById('date-from');
            const inputTo = document.getElementById('date-to');
            const btnFilter = document.getElementById('btn-filter');
            const btnReset = document.getElementById('btn-reset');
            const errorMsg = document.getElementById('error-msg');
            const loader = document.getElementById('chart-loader');
            const tbody = document.getElementById('raw-tbody');

            const kpiTotal = document.getElementById('kpi-total');
            const kpiAlto = document.getElementById('kpi-alto');
            const kpiMedio = document.getElementById('kpi-medio');
            const kpiBaixo = document.getElementById('kpi-baixo');

            // ── Helpers ────────────────────────────────────────────────────
            function showError(msg) {
                errorMsg.textContent = msg;
                errorMsg.style.display = 'block';
            }

            function clearError() {
                errorMsg.style.display = 'none';
                errorMsg.textContent = '';
            }

            function showLoader() {
                loader.style.display = 'flex';
            }

            function hideLoader() {
                loader.style.display = 'none';
            }

            function buildUrl(from, to) {
                const url = new URL(API_URL, window.location.origin);
                url.searchParams.set('from', from);
                url.searchParams.set('to', to);
                return url.toString();
            }

            // ── Fetch de dados ─────────────────────────────────────────────
            async function loadData(from, to) {
                clearError();
                showLoader();

                try {
                    const resp = await fetch(buildUrl(from, to), {
                        headers: {
                            'Accept': 'application/json'
                        },
                    });

                    if (!resp.ok) {
                        const err = await resp.json().catch(() => ({}));
                        throw new Error(err.message ?? `Erro HTTP ${resp.status}`);
                    }

                    const payload = await resp.json();
                    renderChart(payload.chart);
                    renderKpis(payload.rows);
                    renderTable(payload.rows);
                } catch (e) {
                    showError('Falha ao carregar dados: ' + e.message);
                } finally {
                    hideLoader();
                }
            }

            // ── Renderização do gráfico ────────────────────────────────────
            function renderChart(chartData) {
                const datasets = chartData.datasets.map(ds => {
                    const cfg = SERIES_CONFIG[ds.name] ?? {
                        label: ds.name,
                        color: '#888',
                        fill: 'rgba(136,136,136,0.1)'
                    };
                    return {
                        label: cfg.label,
                        data: ds.data,
                        borderColor: cfg.color,
                        backgroundColor: cfg.fill,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.3,
                        fill: false,
                    };
                });

                if (chart) {
                    chart.data.labels = chartData.labels;
                    chart.data.datasets = datasets;
                    chart.update('active');
                    return;
                }

                const ctx = document.getElementById('news-relevance-chart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: datasets,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false, // legenda customizada em HTML
                            },
                            tooltip: {
                                backgroundColor: 'rgba(20, 22, 28, 0.95)',
                                borderColor: 'rgba(255,255,255,.1)',
                                borderWidth: 1,
                                titleColor: '#e6edf3',
                                bodyColor: '#8b949e',
                                padding: 10,
                                callbacks: {
                                    title: (items) => items[0].label,
                                    label: (item) => ` ${item.dataset.label}: ${item.formattedValue} notícias`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#8b949e',
                                    font: {
                                        family: '"Share Tech Mono", monospace',
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    autoSkip: true,
                                    maxTicksLimit: 16,
                                },
                                grid: {
                                    color: 'rgba(255,255,255,.06)'
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#8b949e',
                                    font: {
                                        family: '"Share Tech Mono", monospace',
                                        size: 10
                                    },
                                    precision: 0,
                                },
                                grid: {
                                    color: 'rgba(255,255,255,.06)'
                                },
                            },
                        },
                    },
                });
            }

            // ── KPIs ───────────────────────────────────────────────────────
            function renderKpis(rows) {
                let total = 0,
                    alto = 0,
                    medio = 0,
                    baixo = 0;

                rows.forEach(row => {
                    total += row.total;
                    if (row.name === 'alto') alto += row.total;
                    if (row.name === 'medio') medio += row.total;
                    if (row.name === 'baixo') baixo += row.total;
                });

                kpiTotal.textContent = total.toLocaleString('pt-BR');
                kpiAlto.textContent = alto.toLocaleString('pt-BR');
                kpiMedio.textContent = medio.toLocaleString('pt-BR');
                kpiBaixo.textContent = baixo.toLocaleString('pt-BR');
            }

            // ── Tabela de dados ────────────────────────────────────────────
            function renderTable(rows) {
                if (!rows.length) {
                    tbody.innerHTML =
                        '<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:1.5rem">Sem dados para o período selecionado.</td></tr>';
                    return;
                }

                const badgeClass = {
                    baixo: 'badge-baixo',
                    medio: 'badge-medio',
                    alto: 'badge-alto'
                };

                tbody.innerHTML = rows.map(row => `
            <tr>
                <td>${row.period}</td>
                <td class="${badgeClass[row.name] ?? ''}">${row.name}</td>
                <td>${row.relevance_score}</td>
                <td>${row.total.toLocaleString('pt-BR')}</td>
            </tr>
        `).join('');
            }

            // ── Eventos ────────────────────────────────────────────────────
            btnFilter.addEventListener('click', () => {
                const from = inputFrom.value;
                const to = inputTo.value;

                if (!from || !to) {
                    showError('Preencha as duas datas antes de filtrar.');
                    return;
                }
                if (from > to) {
                    showError('A data inicial deve ser anterior à data final.');
                    return;
                }

                loadData(from, to);
            });

            btnReset.addEventListener('click', () => {
                inputFrom.value = DEFAULT_FROM;
                inputTo.value = DEFAULT_TO;
                loadData(DEFAULT_FROM, DEFAULT_TO);
            });

            // ── Carga inicial ──────────────────────────────────────────────
            loadData(DEFAULT_FROM, DEFAULT_TO);

        })();
    </script>
@endpush
