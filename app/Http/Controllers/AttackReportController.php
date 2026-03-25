<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Services\AttackReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function view(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)
            : Carbon::create(2022, 1, 1);

        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)
            : Carbon::now();
        // dd("xx");
        $rows = $this->reportService->generate($from, $to);

        return view('reports.attacks', compact('rows', 'from', 'to'));
    }

    /**
     * Gera e retorna relatório diário de ataques
     *
     * @return JsonResponse
     */

    /**
     * /api/reports/attacks/daily
     */
    public function dailyReport(Request $request)
    {
        // mesma lógica mas com addDays(1) no service — ou pode reusar generate()
        return $this->weeklyReport($request);
    }

    /**
     * /export/report/attacks/weekly — exporta CSV
     */
    public function exportWeekly(Request $request)
    {
        dd("xxx");
        $from = Carbon::parse($request->from ?? '2022-01-01');
        $to   = Carbon::parse($request->to ?? now());
        $rows = $this->service->generate($from, $to);

        $csv = "Período;Qtd Ataques;Notícias -7 dias;Notícias +7 dias\n";

        foreach ($rows as $row) {
            $minus7 = $row['news_minus7']->map(fn($n) =>
                $n->title . ' (' . $n->published_date->format('d/m/Y') . ')'
            )->implode(' | ');

            $plus7 = $row['news_plus7']->map(fn($n) =>
                $n->title . ' (' . $n->published_date->format('d/m/Y') . ')'
            )->implode(' | ');

            $csv .= "\"{$row['name']}\";{$row['attack_count']};\"$minus7\";\"$plus7\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="report-attacks.csv"');
    }

    /**
     * /api/reports/attacks/weekly  — JSON para APIs ou exportação
     */
    public function weeklyReport(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)
            : Carbon::create(2022, 1, 1);

        $to = $request->filled('to')
            ? Carbon::parse($request->to)
            : Carbon::now();

        $rows = $this->service->generate($from, $to);

        return response()->json([
            'from'  => $from->toDateString(),
            'to'    => $to->toDateString(),
            'total' => $rows->count(),
            'rows'  => $rows->map(fn($r) => [
                'period'       => $r['name'],
                'start_date'   => $r['start_date']->toDateString(),
                'end_date'     => $r['end_date']->toDateString(),
                'attack_count' => $r['attack_count'],
                'news_minus7'  => $r['news_minus7']->map(fn($n) => [
                    'title'          => $n->title,
                    'published_date' => $n->published_date->format('d/m/Y'),
                    'source_name'    => $n->source_name,
                ]),
                'news_plus7'   => $r['news_plus7']->map(fn($n) => [
                    'title'          => $n->title,
                    'published_date' => $n->published_date->format('d/m/Y'),
                    'source_name'    => $n->source_name,
                ]),
            ]),
        ]);
    }
}
