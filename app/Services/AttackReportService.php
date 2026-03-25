<?php
namespace App\Services;

use App\Models\HackerAttack;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttackReportService
{
    /**
     * Gera períodos semanais (7 dias) entre $from e $to,
     * contando ataques e coletando notícias -7d / +7d de cada período.
     */
    public function generate(Carbon $from, Carbon $to): Collection
    {
        // Pré-carrega tudo em 2 queries — evita N+1
        $allAttacks = HackerAttack::whereBetween('attack_date', [
            $from->toDateString(),
            $to->toDateString(),
        ])->limit(200)->get();

        $allNews = News::whereBetween('published_date', [
            $from->copy()->subDays(7)->toDateString(),
            $to->copy()->addDays(7)->toDateString(),
        ])->orderBy('published_date')->get();

        $rows    = collect();
        $current = $from->copy()->startOfDay();

        while ($current->lte($to)) {
            $periodStart = $current->copy();
            $periodEnd   = $current->copy()->addDays(6)->endOfDay();

            // Janela -7: 7 dias ANTES do início do período
            $minus7Start = $periodStart->copy()->subDays(7);
            $minus7End   = $periodStart->copy()->subDay()->endOfDay();

            // Janela +7: do início do período + 7 dias adiante
            $plus7Start = $periodStart->copy();
            $plus7End   = $periodStart->copy()->addDays(7)->endOfDay();

            $rows->push([
                'name'         => $periodStart->format('d/m/Y'),
                'start_date'   => $periodStart,
                'end_date'     => $periodEnd,

                'attack_count' => $allAttacks
                    ->whereBetween('attack_date', [
                        $periodStart->toDateString(),
                        $periodEnd->toDateString(),
                    ])
                    ->count(),

                'news_minus7'  => $allNews->filter(fn($n) =>
                    $n->published_date->between($minus7Start, $minus7End)
                )->values(),

                'news_plus7'   => $allNews->filter(fn($n) =>
                    $n->published_date->between($plus7Start, $plus7End)
                )->values(),
            ]);

            $current->addDays(7);
        }

        return $rows;
    }
}
