<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Services\AttackReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class AttackReportController extends Controller
{
    public function __construct(
        private readonly AttackReportService $reportService,
    ) {}

    /**
     * Retorna a view do relatório
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        return view('attack-report');
    }

    /**
     * Gera e retorna relatório diário de ataques
     *
     * @return JsonResponse
     */
    public function dailyReport(): JsonResponse
    {
        $startDate = request('start_date', '01/01/2022');
        $endDate   = request('end_date', '06/01/2022');

        try {
            $report = $this->reportService->generateDailyReport($startDate, $endDate);
            $total  = $this->reportService->getTotalAttacks($startDate, $endDate);

            return response()->json([
                'success'       => true,
                'period'        => [
                    'start' => $startDate,
                    'end'   => $endDate,
                ],
                'total_attacks' => $total,
                'report'        => $report->map(fn($item) => $item['formatted'])->values(),
                'detailed'      => $report,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Retorna relatório em formato texto/CSV
     *
     * @return Response
     */
    public function exportWeekly(): Response
    {
        $startDate = request('start_date', '01/01/2022');
        $endDate   = request('end_date', '06/01/2022');

        try {
            $report = $this->reportService->generateWeeklyReport($startDate, $endDate);
            $total  = $this->reportService->getTotalAttacks($startDate, $endDate);

            $content  = "RELATÓRIO DE ATAQUES - SEMANAL\n";
            $content .= "Período: {$startDate} até {$endDate}\n";
            $content .= "Total de ataques: {$total}\n";
            $content .= "=" . str_repeat("=", 50) . "\n\n";

            foreach ($report as $week) {
                $content .= $week['formatted'] . "\n";
            }

            return response($content)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="relatorio-ataques-' . date('Y-m-d') . '.txt"');
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }
}