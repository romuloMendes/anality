@extends('layouts.app')

@section('title', 'Relatório de Ataques - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="page-header mb-4">
            <p class="page-header-tag">// relatórios</p>
            <h1 class="page-header-title">Relatório de Ataques</h1>
            <p class="page-header-sub">análise temporal por período configurável</p>
        </div>

        {{-- Filtros --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5>Filtros</h5>
            </div>
            <div class="card-body">
                <form id="reportForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Data Inicial</label>
                        <input type="text" id="startDate" name="start_date" placeholder="01/01/2022"
                            value="01/01/2022" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Final</label>
                        <input type="text" id="endDate" name="end_date" placeholder="06/01/2022"
                            value="06/01/2022" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Formato</label>
                        <select id="format" name="format" class="form-select">
                            <option value="weekly">Semanal (7 dias)</option>
                            <option value="daily">Diário</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="button" onclick="loadReport()" class="btn btn-primary flex-fill">
                            <i class="bi bi-bar-chart"></i> Gerar
                        </button>
                        <button type="button" onclick="exportReport()" class="btn btn-success flex-fill">
                            <i class="bi bi-download"></i> Exportar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Loading --}}
        <div id="loading" class="d-none text-center py-5">
            <div class="spinner-border" role="status"></div>
            <p class="text-muted mt-3" style="font-family: 'Share Tech Mono', monospace; font-size: 11px; letter-spacing: 0.1em;">CARREGANDO DADOS...</p>
        </div>

        {{-- Relatório --}}
        <div id="reportContainer" class="d-none">
            <div class="card mb-4">
                <div class="card-body d-flex gap-4 flex-wrap">
                    <div class="kpi-card">
                        <span class="kpi-value" id="totalDisplay">—</span>
                        <span class="kpi-label">Total de Ataques</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-value" id="periodDisplay" style="font-size: 13px;">—</span>
                        <span class="kpi-label">Período</span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Detalhamento por Período</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th class="text-center">Total de Ataques</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Visualização</h5>
                </div>
                <div class="card-body">
                    <canvas id="reportChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Erro --}}
        <div id="errorContainer" class="d-none">
            <div class="alert alert-danger">
                <strong class="alert-heading">Erro ao carregar relatório</strong>
                <p id="errorMessage" class="mb-0 mt-1" style="font-size: 12px;"></p>
            </div>
        </div>

        <div class="page-footer d-flex justify-content-between mt-4">
            <span>RELATÓRIO DE ATAQUES — ANALITY</span>
            <span id="report-footer-ts"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('report-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
        let chart = null;

        async function loadReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate   = document.getElementById('endDate').value;
            const format    = document.getElementById('format').value;

            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('reportContainer').classList.add('d-none');
            document.getElementById('errorContainer').classList.add('d-none');

            try {
                const response = await fetch(`/api/reports/attacks/${format}?start_date=${startDate}&end_date=${endDate}`);
                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Erro ao carregar dados');
                displayReport(data);
            } catch (error) {
                document.getElementById('errorMessage').textContent = error.message;
                document.getElementById('errorContainer').classList.remove('d-none');
            } finally {
                document.getElementById('loading').classList.add('d-none');
            }
        }

        function displayReport(data) {
            const tableBody = document.getElementById('reportTableBody');
            tableBody.innerHTML = '';
            data.detailed.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="font-family:'Share Tech Mono',monospace;font-size:12px;">${item.formatted.split(' | ')[0]}</td>
                    <td class="text-center">
                        <span class="badge badge-critical">${item.total}</span>
                    </td>`;
                tableBody.appendChild(row);
            });

            document.getElementById('periodDisplay').textContent = `${data.period.start} — ${data.period.end}`;
            document.getElementById('totalDisplay').textContent  = data.total_attacks;

            if (chart) chart.destroy();
            chart = new Chart(document.getElementById('reportChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.detailed.map(i => i.date),
                    datasets: [{
                        label: 'Ataques',
                        data: data.detailed.map(i => i.total),
                        backgroundColor: 'rgba(255,59,92,0.6)',
                        borderColor: '#ff3b5c',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { labels: { color: '#c8d8e8', font: { family: "'Share Tech Mono', monospace", size: 11 } } } },
                    scales: {
                        x: { ticks: { color: '#4a6480', font: { family: "'Share Tech Mono', monospace", size: 10 } }, grid: { color: '#1a2d45' } },
                        y: { beginAtZero: true, ticks: { stepSize: 1, color: '#4a6480', font: { family: "'Share Tech Mono', monospace", size: 10 } }, grid: { color: '#1a2d45' } }
                    }
                }
            });

            document.getElementById('reportContainer').classList.remove('d-none');
        }

        async function exportReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate   = document.getElementById('endDate').value;
            window.location.href = `/api/reports/attacks/export/weekly?start_date=${startDate}&end_date=${endDate}`;
        }

        window.addEventListener('load', () => loadReport());
    </script>
    @endpush
@endsection
