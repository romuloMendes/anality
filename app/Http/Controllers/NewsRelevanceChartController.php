<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\NewsRelevanceChartRequest;
use App\Services\NewsRelevanceChartService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NewsRelevanceChartController extends Controller
{
    public function __construct(
        private readonly NewsRelevanceChartService $chartService,
    ) {}

    /**
     * Exibe a view do gráfico de relevância de notícias.
     * Aceita ?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::create(2022, 1, 1)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        return view('reports.news_relevance_chart', compact('from', 'to'));
    }

    /**
     * Endpoint JSON que alimenta o gráfico.
     * GET /api/reports/news/relevance-chart?from=YYYY-MM-DD&to=YYYY-MM-DD
     *
     * Resposta:
     * {
     *   "from": "2022-01-01",
     *   "to": "2022-12-31",
     *   "rows": [...],       // formato flat para tabelas
     *   "chart": {...}       // labels + datasets prontos para Chart.js
     * }
     */
    public function data(NewsRelevanceChartRequest $request): JsonResponse
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $rows  = $this->chartService->generate($from, $to);
        $chart = $this->chartService->toChartDataset($rows);

        return response()->json([
            'from'  => $from->toDateString(),
            'to'    => $to->toDateString(),
            'rows'  => $rows->values(),
            'chart' => $chart,
        ]);
    }
}
