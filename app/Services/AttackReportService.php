<?php

declare (strict_types = 1);

namespace App\Services;

use App\Models\HackerAttack;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class AttackReportService
{
    /**
     * Gera relatório de ataques agrupados por semana (7 dias)
     *
     * @param string $startDate Data inicial (formato: dd/mm/yyyy)
     * @param string $endDate Data final (formato: dd/mm/yyyy)
     * @return Collection
     */
    public function generateWeeklyReport(string $startDate, string $endDate): Collection
    {
        $start = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();

        $attacks = HackerAttack::query()
            ->whereBetween('attack_date', [$start, $end])
            ->orderBy('attack_date')
            ->get();

        $report      = collect([]);
        $currentDate = $start->clone();

        while ($currentDate <= $end) {
            $weekEnd = $currentDate->clone()->addDays(6)->endOfDay();
            $weekEnd = $weekEnd > $end ? $end : $weekEnd;

            $weekAttacks = $attacks->filter(function (HackerAttack $attack) use ($currentDate, $weekEnd) {
                return $attack->attack_date >= $currentDate->toDateString()
                && $attack->attack_date <= $weekEnd->toDateString();
            });

            $total         = $weekAttacks->count();
            $formattedDate = $currentDate->format('d/m/Y');

            $report->push([
                'date'      => $formattedDate,
                'total'     => $total,
                'formatted' => "{$formattedDate} | total: {$total}",
                'start_date' => $currentDate->toDateString(),
                'end_date'   => $weekEnd->toDateString(),
            ]);

            $currentDate = $weekEnd->clone()->addDay()->startOfDay();
        }

        return $report;
    }

    /**
     * Gera relatório de ataques por dia
     *
     * @param string $startDate Data inicial (formato: dd/mm/yyyy)
     * @param string $endDate Data final (formato: dd/mm/yyyy)
     * @return Collection
     */
    public function generateDailyReport(string $startDate, string $endDate): Collection
    {
        $start = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();

        return HackerAttack::query()
            ->whereBetween('attack_date', [$start, $end])
            ->orderBy('attack_date')
            ->get()
            ->groupBy('attack_date')
            ->map(function ($attacks, $date) {
                $formatted = Carbon::parse($date)->format('d/m/Y');
                return [
                    'date'      => $formatted,
                    'total'     => $attacks->count(),
                    'formatted' => "{$formatted} | total: {$attacks->count()}",
                ];
            })
            ->values();
    }

    /**
     * Retorna o total de ataques em um período
     *
     * @param string $startDate Data inicial (formato: dd/mm/yyyy)
     * @param string $endDate Data final (formato: dd/mm/yyyy)
     * @return int
     */
    public function getTotalAttacks(string $startDate, string $endDate): int
    {
        $start = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();

        return HackerAttack::query()
            ->whereBetween('attack_date', [$start, $end])
            ->count();
    }
}