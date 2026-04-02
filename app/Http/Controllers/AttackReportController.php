<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AttackReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AttackReportController extends Controller
{
    public function __construct(
        private readonly AttackReportService $reportService,
    ) {}

    /**
     * Retorna a view de tabela do relatório.
     * Aceita query params ?from=dd/mm/YYYY&to=dd/mm/YYYY
     */
    public function view(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay()
            : Carbon::create(2022, 1, 1)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $rows = $this->reportService->generate($from, $to);

        return view('reports.attacks', compact('rows', 'from', 'to'));
    }

    /**
     * Retorna a view do dashboard gráfico.
     */
    public function chart(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::create(2022, 1, 1)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::create(2022, 12, 31)->endOfDay();

        return view('reports.attacks_chart', compact('from', 'to'));
    }

    /**
     * /api/reports/attacks/daily — delega ao semanal.
     */
    public function dailyReport(Request $request): JsonResponse
    {
        return $this->weeklyReport($request);
    }

    /**
     * /api/reports/attacks/weekly — JSON estruturado para o gráfico.
     * Usa generateSummary() (2 queries GROUP BY) — sem carregar registros em memória.
     */
    public function weeklyReport(Request $request): JsonResponse
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::create(2022, 1, 1)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $rows = $this->reportService->generateSummary($from, $to);

        return response()->json([
            'from'  => $from->toDateString(),
            'to'    => $to->toDateString(),
            'total' => $rows->sum('attack_count'),
            'rows'  => $rows->map(fn($r) => [
                'period'            => $r['name'],
                'start_date'        => $r['start_date']->toDateString(),
                'end_date'          => $r['end_date']->toDateString(),
                'attack_count'      => $r['attack_count'],
                'news_minus7_count' => $r['news_minus7_count'],
                'news_plus7_count'  => $r['news_plus7_count'],
            ]),
        ]);
    }

    /**
     * /api/reports/attacks/period-news — notícias de um período específico (para o painel de clique).
     */
    public function periodNews(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date', 'after_or_equal:start'],
        ]);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $data = $this->reportService->getPeriodNews($start, $end);

        $format = fn($n) => [
            'title'          => $n->title,
            'published_date' => $n->published_date->format('d/m/Y'),
            'source_name'    => $n->source_name,
        ];

        return response()->json([
            'news_minus7' => $data['news_minus7']->map($format)->values(),
            'news_plus7'  => $data['news_plus7']->map($format)->values(),
        ]);
    }

    /**
     * /api/reports/attacks/export/weekly — download CSV.
     */
    public function exportWeekly(Request $request): Response
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::create(2022, 1, 1)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $rows = $this->reportService->generate($from, $to);

        $lines = ["Período;Qtd Ataques;Notícias -7 dias;Notícias +7 dias"];

        foreach ($rows as $row) {
            $minus7 = $row['news_minus7']->map(
                fn($n) => $n->title . ' (' . $n->published_date->format('d/m/Y') . ' · ' . $n->source_name . ')'
            )->implode(' | ');

            $plus7 = $row['news_plus7']->map(
                fn($n) => $n->title . ' (' . $n->published_date->format('d/m/Y') . ' · ' . $n->source_name . ')'
            )->implode(' | ');

            $lines[] = "\"{$row['name']}\";{$row['attack_count']};\"$minus7\";\"$plus7\"";
        }

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="report-attacks-weekly.csv"');
    }
}
