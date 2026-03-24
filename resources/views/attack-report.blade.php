@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Relatório de Ataques</h1>

            <!-- Filtros -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Filtros</h2>

                <form id="reportForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">
                            Data Inicial
                        </label>
                        <input type="text" id="startDate" name="start_date" placeholder="01/01/2022" value="01/01/2022"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">
                            Data Final
                        </label>
                        <input type="text" id="endDate" name="end_date" placeholder="06/01/2022" value="06/01/2022"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                            Formato
                        </label>
                        <select id="format" name="format"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="weekly">Semanal (7 dias)</option>
                            <option value="daily">Diário</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="button" onclick="loadReport()"
                            class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-medium">
                            📈 Gerar
                        </button>
                        <button type="button" onclick="exportReport()"
                            class="flex-1 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition font-medium">
                            ⬇️ Exportar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Loading -->
            <div id="loading" class="hidden text-center py-8">
                <p class="text-gray-600">Carregando dados...</p>
                <div class="mt-4 flex justify-center">
                    <div class="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full"></div>
                </div>
            </div>

            <!-- Relatório -->
            <div id="reportContainer" class="hidden">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Período:</span>
                        <span id="periodDisplay"></span>
                    </p>
                    <p class="text-sm text-gray-700 mt-2">
                        <span class="font-semibold">Total de Ataques:</span>
                        <span id="totalDisplay" class="text-lg font-bold text-red-600"></span>
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Período</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total de Ataques</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico -->
                <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Visualização</h3>
                    <canvas id="reportChart"></canvas>
                </div>
            </div>

            <!-- Erro -->
            <div id="errorContainer" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-700 font-semibold">⚠️ Erro ao carregar relatório</p>
                <p id="errorMessage" class="text-red-600 text-sm mt-2"></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chart = null;

        async function loadReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const format = document.getElementById('format').value;

            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('reportContainer').classList.add('hidden');
            document.getElementById('errorContainer').classList.add('hidden');

            try {
                const response = await fetch(
                    `/api/reports/attacks/${format}?start_date=${startDate}&end_date=${endDate}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || 'Erro ao carregar dados');
                }

                displayReport(data);
            } catch (error) {
                showError(error.message);
            } finally {
                document.getElementById('loading').classList.add('hidden');
            }
        }

        function displayReport(data) {
            const tableBody = document.getElementById('reportTableBody');
            tableBody.innerHTML = '';

            data.detailed.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-3 border-b border-gray-200 text-sm text-gray-700">${item.formatted.split(' | ')[0]}</td>
                    <td class="px-6 py-3 border-b border-gray-200 text-center">
                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 font-semibold rounded-full">
                            ${item.total}
                        </span>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            document.getElementById('periodDisplay').textContent =
                `${data.period.start} até ${data.period.end}`;
            document.getElementById('totalDisplay').textContent =
                data.total_attacks;

            createChart(data.detailed);

            document.getElementById('reportContainer').classList.remove('hidden');
        }

        function createChart(data) {
            const ctx = document.getElementById('reportChart').getContext('2d');

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Ataques',
                        data: data.map(item => item.total),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(220, 38, 38, 1)',
                        borderWidth: 2,
                        borderRadius: 4,
                        hoverBackgroundColor: 'rgba(220, 38, 38, 0.9)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        async function exportReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            window.location.href = `/api/reports/attacks/export/weekly?start_date=${startDate}&end_date=${endDate}`;
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorContainer').classList.remove('hidden');
        }

        // Carregar relatório ao abrir a página
        window.addEventListener('load', () => {
            loadReport();
        });
    </script>

    <style>
        #reportChart {
            max-height: 400px;
        }
    </style>
@endsection

{{-- @endsection --}}
