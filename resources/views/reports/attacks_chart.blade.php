<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Ataques – Anality</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #080c10;
            --surface: #0d1117;
            --surface2: #161b22;
            --border: #21262d;
            --text: #c9d1d9;
            --muted: #6e7681;
            --red: rgba(255, 59, 92, 1);
            --red-soft: rgba(255, 59, 92, 0.45);
            --yellow: rgba(255, 184, 0, 1);
            --blue: rgba(56, 139, 253, 1);
            --blue-dim: rgba(56, 139, 253, 0.15);
            --mono: 'Courier New', Courier, monospace;
        }

        html,
        body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: var(--mono);
        }

        a {
            color: var(--blue);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* ── Header ───────────────────────────────────────── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .header-title {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: .05em;
            color: #fff;
        }

        .header-title span {
            color: var(--red);
        }

        .header-nav a {
            color: var(--muted);
            font-size: .8rem;
            margin-left: 1rem;
        }

        .header-nav a:hover {
            color: var(--text);
            text-decoration: none;
        }

        /* ── Main layout ──────────────────────────────────── */
        .main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* ── Filter bar ───────────────────────────────────── */
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
            font-family: var(--mono);
            font-size: .85rem;
            padding: .4rem .6rem;
            border-radius: 4px;
            outline: none;
        }

        .filter-bar input[type="date"]:focus {
            border-color: var(--blue);
        }

        .btn {
            cursor: pointer;
            font-family: var(--mono);
            font-size: .82rem;
            padding: .42rem .9rem;
            border-radius: 4px;
            border: 1px solid transparent;
            transition: opacity .15s;
        }

        .btn:hover {
            opacity: .85;
        }

        .btn-primary {
            background: #1f6feb;
            color: #fff;
            border-color: #1f6feb;
        }

        .btn-outline {
            background: transparent;
            color: var(--muted);
            border-color: var(--border);
        }

        .btn-outline:hover {
            color: var(--text);
            border-color: var(--text);
            opacity: 1;
        }

        .btn-export {
            background: transparent;
            color: #3fb950;
            border-color: #3fb950;
        }

        .btn-table {
            background: transparent;
            color: var(--muted);
            border-color: var(--border);
        }

        /* ── KPI cards ────────────────────────────────────── */
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

        .kpi-label {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
            margin-bottom: .3rem;
        }

        .kpi-value {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1;
        }

        .kpi-value.red {
            color: var(--red);
        }

        .kpi-value.blue {
            color: var(--blue);
        }

        .kpi-value.yellow {
            color: var(--yellow);
        }

        .kpi-value.white {
            color: #e6edf3;
        }

        /* ── Chart card ───────────────────────────────────── */
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

        .legend-line {
            width: 20px;
            height: 2px;
            flex-shrink: 0;
        }

        .legend-dashed {
            width: 20px;
            height: 0;
            border-top: 2px dashed;
            flex-shrink: 0;
        }

        #chart-wrapper {
            position: relative;
            height: 340px;
        }

        /* spinner */
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
            border-top-color: var(--blue);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── News panel ───────────────────────────────────── */
        #news-panel {
            display: none;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 1rem 1.25rem;
        }

        #news-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .9rem;
        }

        #news-panel-title {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
        }

        #news-panel-close {
            cursor: pointer;
            color: var(--muted);
            font-size: .75rem;
            border: 1px solid var(--border);
            border-radius: 3px;
            padding: .15rem .5rem;
            background: transparent;
            font-family: var(--mono);
        }

        #news-panel-close:hover {
            color: var(--text);
            border-color: var(--text);
        }

        .news-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 640px) {
            .news-columns {
                grid-template-columns: 1fr;
            }
        }

        .news-col-title {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: .6rem;
            padding-bottom: .4rem;
            border-bottom: 1px solid var(--border);
        }

        .news-col-title.minus {
            color: var(--blue);
        }

        .news-col-title.plus {
            color: var(--yellow);
        }

        .news-item {
            padding: .5rem 0;
            border-bottom: 1px solid var(--border);
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-item-title {
            font-size: .8rem;
            color: var(--text);
            line-height: 1.35;
            margin-bottom: .2rem;
        }

        .news-item-meta {
            font-size: .7rem;
            color: var(--muted);
        }

        .news-empty {
            font-size: .78rem;
            color: var(--muted);
            font-style: italic;
        }

        /* ── Error ────────────────────────────────────────── */
        #error-msg {
            display: none;
            background: rgba(255, 59, 92, .1);
            border: 1px solid rgba(255, 59, 92, .4);
            border-radius: 6px;
            padding: .75rem 1rem;
            font-size: .82rem;
            color: var(--red);
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <div class="header-title">
            <span>⬡</span> Anality — <span>Dashboard de Ataques</span>
        </div>
        <nav class="header-nav">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('report-attacks-view') }}">Tabela</a>
            <a href="{{ route('attacks') }}">Ataques</a>
        </nav>
    </div>

    <div class="main">

        <!-- Filter bar -->
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
            <button id="btn-reset" class="btn btn-outline">Limpar</button>
            <a id="btn-export" class="btn btn-export" href="#">⬇ Exportar CSV</a>
            <a href="{{ route('report-attacks-view') }}" class="btn btn-table">☰ Ver Tabela</a>
        </div>

        <!-- Error -->
        <div id="error-msg"></div>

        <!-- KPIs -->
        <div class="kpis">
            <div class="kpi">
                <div class="kpi-label">Total de Ataques</div>
                <div class="kpi-value red" id="kpi-attacks">—</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Períodos</div>
                <div class="kpi-value white" id="kpi-periods">—</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Total de Notícias</div>
                <div class="kpi-value blue" id="kpi-news">—</div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Pico Semanal</div>
                <div class="kpi-value yellow" id="kpi-peak">—</div>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-card">
            <div class="chart-card-header">
                <span class="chart-card-title">Ataques &amp; Notícias · Intervalos de 7 dias</span>
                <div class="chart-legend">
                    <span class="legend-item">
                        <span class="legend-dot" style="background:rgba(255,59,92,0.85)"></span> Ataques
                    </span>
                    <span class="legend-item">
                        <span class="legend-line" style="background:rgba(56,139,253,1)"></span> Notícias –7d
                    </span>
                    <span class="legend-item">
                        <span class="legend-dashed" style="border-color:rgba(255,184,0,1)"></span> Notícias +7d
                    </span>
                </div>
            </div>
            <div id="chart-wrapper">
                <div id="chart-loader">
                    <div class="spinner"></div>
                </div>
                <canvas id="attacksChart"></canvas>
            </div>
        </div>

        <!-- News panel -->
        <div id="news-panel">
            <div id="news-panel-header">
                <span id="news-panel-title">Notícias do período —</span>
                <button id="news-panel-close">✕ Fechar</button>
            </div>
            <div class="news-columns">
                <div>
                    <div class="news-col-title minus">◈ Notícias –7 dias (antes do período)</div>
                    <div id="news-minus7-list"></div>
                </div>
                <div>
                    <div class="news-col-title plus">◈ Notícias +7 dias (a partir do período)</div>
                    <div id="news-plus7-list"></div>
                </div>
            </div>
        </div>

    </div><!-- .main -->

    <script>
        const API_URL = '{{ route('report-attacks-weekly') }}';
        const EXPORT_URL = '{{ route('export-report-attacks-weekly') }}';
        const DEFAULT_FROM = '{{ $from->toDateString() }}';
        const DEFAULT_TO = '{{ $to->toDateString() }}';

        let reportData = null;
        let chart = null;

        /* ── Helpers ──────────────────────────────────────────── */
        function getBarColor(count) {
            if (count >= 70) return 'rgba(255,59,92,0.85)';
            if (count >= 40) return 'rgba(255,184,0,0.75)';
            return 'rgba(255,59,92,0.45)';
        }

        function renderNewsList(containerId, items) {
            const $el = $('#' + containerId);
            $el.empty();
            if (!items || items.length === 0) {
                $el.append('<div class="news-empty">Nenhuma notícia neste intervalo.</div>');
                return;
            }
            items.forEach(function(n) {
                $el.append(
                    '<div class="news-item">' +
                    '<div class="news-item-title">' + $('<span>').text(n.title).html() + '</div>' +
                    '<div class="news-item-meta">' + n.published_date + ' &middot; ' + $('<span>').text(n
                        .source_name).html() + '</div>' +
                    '</div>'
                );
            });
        }

        function showNewsPanel(row) {
            $('#news-panel-title').text('Notícias do período — ' + row.period + ' a ' + row.end_date.split('-').reverse()
                .join('/'));
            renderNewsList('news-minus7-list', row.news_minus7);
            renderNewsList('news-plus7-list', row.news_plus7);
            $('#news-panel').slideDown(200);
            $('html, body').animate({
                scrollTop: $('#news-panel').offset().top - 20
            }, 300);
        }

        /* ── KPIs ─────────────────────────────────────────────── */
        function updateKPIs(data) {
            const totalAttacks = data.total;
            const periods = data.rows.length;
            const totalNews = data.rows.reduce(function(acc, r) {
                return acc + r.news_minus7.length + r.news_plus7.length;
            }, 0);
            const peak = data.rows.reduce(function(max, r) {
                return r.attack_count > max ? r.attack_count : max;
            }, 0);

            $('#kpi-attacks').text(totalAttacks.toLocaleString('pt-BR'));
            $('#kpi-periods').text(periods);
            $('#kpi-news').text(totalNews.toLocaleString('pt-BR'));
            $('#kpi-peak').text(peak.toLocaleString('pt-BR'));
        }

        /* ── Build / refresh chart ────────────────────────────── */
        function buildChart(data) {
            reportData = data;
            updateKPIs(data);

            const labels = data.rows.map(function(r) {
                return r.period;
            });
            const counts = data.rows.map(function(r) {
                return r.attack_count;
            });
            const minus7 = data.rows.map(function(r) {
                return r.news_minus7.length;
            });
            const plus7 = data.rows.map(function(r) {
                return r.news_plus7.length;
            });
            const bgColors = counts.map(getBarColor);

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = counts;
                chart.data.datasets[0].backgroundColor = bgColors;
                chart.data.datasets[1].data = minus7;
                chart.data.datasets[2].data = plus7;
                chart.update();
                return;
            }

            const ctx = document.getElementById('attacksChart').getContext('2d');
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            type: 'bar',
                            label: 'Ataques',
                            data: counts,
                            backgroundColor: bgColors,
                            borderRadius: 3,
                            order: 2,
                        },
                        {
                            type: 'line',
                            label: 'Notícias –7 dias',
                            data: minus7,
                            borderColor: 'rgba(56,139,253,1)',
                            backgroundColor: 'rgba(56,139,253,0.08)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(56,139,253,1)',
                            tension: 0.3,
                            fill: false,
                            order: 1,
                        },
                        {
                            type: 'line',
                            label: 'Notícias +7 dias',
                            data: plus7,
                            borderColor: 'rgba(255,184,0,1)',
                            backgroundColor: 'rgba(255,184,0,0.06)',
                            borderWidth: 2,
                            borderDash: [6, 3],
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(255,184,0,1)',
                            tension: 0.3,
                            fill: false,
                            order: 0,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#161b22',
                            borderColor: '#21262d',
                            borderWidth: 1,
                            titleColor: '#c9d1d9',
                            bodyColor: '#8b949e',
                            titleFont: {
                                family: "'Courier New', monospace",
                                size: 11
                            },
                            bodyFont: {
                                family: "'Courier New', monospace",
                                size: 11
                            },
                            padding: 10,
                            callbacks: {
                                title: function(items) {
                                    return '⬡ ' + items[0].label;
                                },
                                label: function(item) {
                                    const icons = ['⬡ Ataques', '◈ Notícias –7d', '◈ Notícias +7d'];
                                    return '  ' + icons[item.datasetIndex] + ': ' + item.raw;
                                },
                            }
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#6e7681',
                                font: {
                                    family: "'Courier New', monospace",
                                    size: 10
                                },
                                maxRotation: 45,
                                minRotation: 30,
                            },
                            grid: {
                                color: '#21262d'
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#6e7681',
                                font: {
                                    family: "'Courier New', monospace",
                                    size: 10
                                },
                                precision: 0,
                            },
                            grid: {
                                color: '#21262d'
                            },
                        }
                    },
                    onClick: function(evt) {
                        const elements = chart.getElementsAtEventForMode(evt, 'nearest', {
                            intersect: true
                        }, false);
                        if (elements.length > 0) {
                            const idx = elements[0].index;
                            showNewsPanel(reportData.rows[idx]);
                        }
                    },
                }
            });
        }

        /* ── Load data ─────────────────────────────────────────── */
        function loadData() {
            const from = $('#date-from').val();
            const to = $('#date-to').val();

            if (!from || !to) {
                $('#error-msg').text('Preencha os dois campos de data.').show();
                return;
            }
            if (from > to) {
                $('#error-msg').text('A data inicial não pode ser posterior à data final.').show();
                return;
            }

            $('#error-msg').hide();
            $('#news-panel').hide();
            $('#chart-loader').show();

            // update export link
            $('#btn-export').attr('href', EXPORT_URL + '?from=' + from + '&to=' + to);

            // update URL without reload
            const url = new URL(window.location.href);
            url.searchParams.set('from', from);
            url.searchParams.set('to', to);
            window.history.replaceState({}, '', url);

            $.getJSON(API_URL, {
                    from: from,
                    to: to
                })
                .done(function(data) {
                    $('#chart-loader').hide();
                    if (!data.rows || data.rows.length === 0) {
                        $('#error-msg').text('Nenhum dado encontrado para o período selecionado.').show();
                        return;
                    }
                    buildChart(data);
                })
                .fail(function() {
                    $('#chart-loader').hide();
                    $('#error-msg').text('Erro ao buscar os dados. Verifique os parâmetros e tente novamente.').show();
                });
        }

        /* ── Events ────────────────────────────────────────────── */
        $(function() {
            // set initial export link
            $('#btn-export').attr('href', EXPORT_URL + '?from=' + DEFAULT_FROM + '&to=' + DEFAULT_TO);

            $('#btn-filter').on('click', loadData);

            $('#date-from, #date-to').on('keydown', function(e) {
                if (e.key === 'Enter') loadData();
            });

            $('#btn-reset').on('click', function() {
                $('#date-from').val(DEFAULT_FROM);
                $('#date-to').val(DEFAULT_TO);
                loadData();
            });

            $('#news-panel-close').on('click', function() {
                $('#news-panel').slideUp(150);
            });

            loadData();
        });
    </script>
</body>

</html>
