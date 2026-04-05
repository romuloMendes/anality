@extends('layouts.app')

@section('title', 'Dashboard de Ataques – Anality')

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

    .btn-export {
        background: transparent;
        color: var(--ok);
        border: 1px solid var(--ok);
        font-family: "Share Tech Mono", monospace;
        font-size: .82rem;
        padding: .42rem .9rem;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: opacity .15s;
    }

    .btn-export:hover { opacity: .8; color: var(--ok); }

    .btn-table {
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--border);
        font-family: "Share Tech Mono", monospace;
        font-size: .82rem;
        padding: .42rem .9rem;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: opacity .15s;
    }

    .btn-table:hover { color: var(--text); border-color: var(--text); }

    .kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .75rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 768px) {
        .kpis { grid-template-columns: repeat(2, 1fr); }
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

    .kpi .kpi-value.red    { color: var(--danger); }
    .kpi .kpi-value.blue   { color: var(--accent); }
    .kpi .kpi-value.yellow { color: var(--warn); }
    .kpi .kpi-value.white  { color: var(--text); }

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

    .chart-legend { display: flex; gap: 1.25rem; }

    .legend-item {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .72rem;
        color: var(--muted);
    }

    .legend-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-line  { width: 20px; height: 2px; flex-shrink: 0; }
    .legend-dashed { width: 20px; height: 0; border-top: 2px dashed; flex-shrink: 0; }

    #chart-wrapper { position: relative; height: 340px; }

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

    @keyframes spin { to { transform: rotate(360deg); } }

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
        font-family: "Share Tech Mono", monospace;
    }

    #news-panel-close:hover { color: var(--text); border-color: var(--text); }

    .news-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 640px) { .news-columns { grid-template-columns: 1fr; } }

    .news-col-title {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: .6rem;
        padding-bottom: .4rem;
        border-bottom: 1px solid var(--border);
    }

    .news-col-title.minus { color: var(--accent); }
    .news-col-title.plus  { color: var(--warn); }

    .news-item { padding: .5rem 0; border-bottom: 1px solid var(--border); }
    .news-item:last-child { border-bottom: none; }
    .news-item-title { font-size: .8rem; color: var(--text); line-height: 1.35; margin-bottom: .2rem; }
    .news-item-meta  { font-size: .7rem; color: var(--muted); }
    .news-empty      { font-size: .78rem; color: var(--muted); font-style: italic; }

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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

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
        <a id="btn-export" class="btn-export" href="#">⬇ Exportar CSV</a>
        <a href="{{ route('report-attacks-view') }}" class="btn-table">☰ Ver Tabela</a>
    </div>

    <div id="error-msg"></div>

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

</div>
@endsection

@push('scripts')
<script>
    const API_URL         = '{{ route("report-attacks-weekly") }}';
    const PERIOD_NEWS_URL = '{{ route("report-attacks-period-news") }}';
    const EXPORT_URL      = '{{ route("export-report-attacks-weekly") }}';
    const DEFAULT_FROM    = '{{ $from->toDateString() }}';
    const DEFAULT_TO      = '{{ $to->toDateString() }}';

    let reportData = null;
    let chart      = null;

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
        items.forEach(function (n) {
            $el.append(
                '<div class="news-item">' +
                '<div class="news-item-title">' + $('<span>').text(n.title).html() + '</div>' +
                '<div class="news-item-meta">' + n.published_date + ' &middot; ' + $('<span>').text(n.source_name).html() + '</div>' +
                '</div>'
            );
        });
    }

    function showNewsPanel(row) {
        const label = 'Notícias do período — ' + row.period + ' a ' + row.end_date.split('-').reverse().join('/');
        $('#news-panel-title').text(label);
        $('#news-minus7-list').html('<div class="news-empty">Carregando...</div>');
        $('#news-plus7-list').html('<div class="news-empty">Carregando...</div>');
        $('#news-panel').slideDown(200);
        $('html, body').animate({ scrollTop: $('#news-panel').offset().top - 20 }, 300);

        $.ajax({
            url: PERIOD_NEWS_URL,
            data: { start: row.start_date, end: row.end_date },
            dataType: 'json',
            timeout: 20000,
        })
        .done(function (data) {
            renderNewsList('news-minus7-list', data.news_minus7);
            renderNewsList('news-plus7-list', data.news_plus7);
        })
        .fail(function () {
            $('#news-minus7-list').html('<div class="news-empty">Erro ao carregar notícias.</div>');
            $('#news-plus7-list').html('<div class="news-empty">Erro ao carregar notícias.</div>');
        });
    }

    function updateKPIs(data) {
        const totalAttacks = data.total;
        const periods      = data.rows.length;
        const totalNews    = data.rows.reduce(function (acc, r) {
            return acc + (r.news_minus7_count || 0) + (r.news_plus7_count || 0);
        }, 0);
        const peak = data.rows.reduce(function (max, r) {
            return r.attack_count > max ? r.attack_count : max;
        }, 0);

        $('#kpi-attacks').text(totalAttacks.toLocaleString('pt-BR'));
        $('#kpi-periods').text(periods);
        $('#kpi-news').text(totalNews.toLocaleString('pt-BR'));
        $('#kpi-peak').text(peak.toLocaleString('pt-BR'));
    }

    function buildChart(data) {
        reportData = data;
        updateKPIs(data);

        const labels   = data.rows.map(function (r) { return r.period; });
        const counts   = data.rows.map(function (r) { return r.attack_count; });
        const minus7   = data.rows.map(function (r) { return r.news_minus7_count || 0; });
        const plus7    = data.rows.map(function (r) { return r.news_plus7_count  || 0; });
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
                datasets: [
                    {
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
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#161b22',
                        borderColor: '#21262d',
                        borderWidth: 1,
                        titleColor: '#c9d1d9',
                        bodyColor: '#8b949e',
                        titleFont: { family: "'Share Tech Mono', monospace", size: 11 },
                        bodyFont:  { family: "'Share Tech Mono', monospace", size: 11 },
                        padding: 10,
                        callbacks: {
                            title: function (items) { return '⬡ ' + items[0].label; },
                            label: function (item) {
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
                            font: { family: "'Share Tech Mono', monospace", size: 10 },
                            maxRotation: 45,
                            minRotation: 30,
                        },
                        grid: { color: '#21262d' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6e7681',
                            font: { family: "'Share Tech Mono', monospace", size: 10 },
                            precision: 0,
                        },
                        grid: { color: '#21262d' },
                    }
                },
                onClick: function (evt) {
                    const elements = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, false);
                    if (elements.length > 0) {
                        showNewsPanel(reportData.rows[elements[0].index]);
                    }
                },
            }
        });
    }

    function loadData() {
        const from = $('#date-from').val();
        const to   = $('#date-to').val();

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

        $('#btn-export').attr('href', EXPORT_URL + '?from=' + from + '&to=' + to);

        const url = new URL(window.location.href);
        url.searchParams.set('from', from);
        url.searchParams.set('to', to);
        window.history.replaceState({}, '', url);

        $.ajax({
            url: API_URL,
            data: { from: from, to: to },
            dataType: 'json',
            timeout: 30000,
        })
        .done(function (data) {
            $('#chart-loader').hide();
            if (!data.rows || data.rows.length === 0) {
                $('#error-msg').text('Nenhum dado encontrado para o período selecionado.').show();
                return;
            }
            buildChart(data);
        })
        .fail(function (jqXHR, status) {
            $('#chart-loader').hide();
            const msg = status === 'timeout'
                ? 'A requisição demorou muito. Tente um período menor.'
                : 'Erro ao buscar os dados (' + (jqXHR.status || status) + '). Verifique os parâmetros e tente novamente.';
            $('#error-msg').text(msg).show();
        });
    }

    $(function () {
        $('#btn-export').attr('href', EXPORT_URL + '?from=' + DEFAULT_FROM + '&to=' + DEFAULT_TO);
        $('#btn-filter').on('click', loadData);
        $('#date-from, #date-to').on('keydown', function (e) { if (e.key === 'Enter') loadData(); });
        $('#btn-reset').on('click', function () {
            $('#date-from').val(DEFAULT_FROM);
            $('#date-to').val(DEFAULT_TO);
            loadData();
        });
        $('#news-panel-close').on('click', function () { $('#news-panel').slideUp(150); });
        loadData();
    });
</script>
@endpush
