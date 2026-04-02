<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HackerAttack;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttackReportService
{
    /**
     * Gera períodos semanais com dados completos de notícias.
     * Usado pela view de tabela (PHP-rendered).
     *
     * Estratégia: pré-indexa por data string para O(1) lookup —
     * evita o filter() O(n * semanas) com 31k+ registros.
     */
    public function generate(Carbon $from, Carbon $to): Collection
    {
        $allAttacks = HackerAttack::whereBetween('attack_date', [
            $from->toDateString(),
            $to->toDateString(),
        ])->get();

        $allNews = News::whereBetween('published_date', [
            $from->copy()->subDays(7)->toDateString(),
            $to->copy()->addDays(6)->toDateString(),
        ])->orderBy('published_date')->get();

        // Indexa ataques e notícias por data string para lookup O(1)
        $attacksByDate = [];
        foreach ($allAttacks as $a) {
            if ($a->attack_date) {
                $attacksByDate[$a->attack_date->toDateString()][] = $a;
            }
        }

        $newsByDate = [];
        foreach ($allNews as $n) {
            if ($n->published_date) {
                $newsByDate[$n->published_date->toDateString()][] = $n;
            }
        }

        $rows    = collect();
        $current = $from->copy()->startOfDay();

        while ($current->lte($to)) {
            $periodStart = $current->copy();
            $periodEnd   = $current->copy()->addDays(6);
            if ($periodEnd->gt($to)) {
                $periodEnd = $to->copy()->startOfDay();
            }

            $attackCount = 0;
            $cursor      = $periodStart->copy();
            while ($cursor->lte($periodEnd)) {
                $attackCount += count($attacksByDate[$cursor->toDateString()] ?? []);
                $cursor->addDay();
            }

            $minus7News = collect();
            $cursor     = $periodStart->copy()->subDays(7);
            while ($cursor->lt($periodStart)) {
                foreach ($newsByDate[$cursor->toDateString()] ?? [] as $n) {
                    $minus7News->push($n);
                }
                $cursor->addDay();
            }

            $plus7News = collect();
            $cursor    = $periodStart->copy();
            $plus7End  = $periodStart->copy()->addDays(6);
            while ($cursor->lte($plus7End)) {
                foreach ($newsByDate[$cursor->toDateString()] ?? [] as $n) {
                    $plus7News->push($n);
                }
                $cursor->addDay();
            }

            $rows->push([
                'name'        => $periodStart->format('d/m/Y'),
                'start_date'  => $periodStart,
                'end_date'    => $periodEnd,
                'attack_count' => $attackCount,
                'news_minus7' => $minus7News,
                'news_plus7'  => $plus7News,
            ]);

            $current->addDays(7);
        }

        return $rows;
    }

    /**
     * Versão leve para o endpoint JSON do gráfico.
     * Usa apenas 2 queries SQL com GROUP BY — sem carregar registros em memória.
     * Retorna apenas contagens; não inclui arrays de notícias.
     */
    public function generateSummary(Carbon $from, Carbon $to): Collection
    {
        $attacksPerDay = HackerAttack::whereBetween('attack_date', [
            $from->toDateString(),
            $to->toDateString(),
        ])
            ->selectRaw('DATE(attack_date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        $newsPerDay = News::whereBetween('published_date', [
            $from->copy()->subDays(7)->toDateString(),
            $to->copy()->addDays(6)->toDateString(),
        ])
            ->selectRaw('DATE(published_date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        $rows    = collect();
        $current = $from->copy()->startOfDay();

        while ($current->lte($to)) {
            $periodStart = $current->copy();
            $periodEnd   = $current->copy()->addDays(6);
            if ($periodEnd->gt($to)) {
                $periodEnd = $to->copy()->startOfDay();
            }

            $attackCount = 0;
            $cursor      = $periodStart->copy();
            while ($cursor->lte($periodEnd)) {
                $attackCount += (int) ($attacksPerDay[$cursor->toDateString()] ?? 0);
                $cursor->addDay();
            }

            $minus7Count = 0;
            $cursor      = $periodStart->copy()->subDays(7);
            while ($cursor->lt($periodStart)) {
                $minus7Count += (int) ($newsPerDay[$cursor->toDateString()] ?? 0);
                $cursor->addDay();
            }

            $plus7Count = 0;
            $cursor     = $periodStart->copy();
            $plus7End   = $periodStart->copy()->addDays(6);
            while ($cursor->lte($plus7End)) {
                $plus7Count += (int) ($newsPerDay[$cursor->toDateString()] ?? 0);
                $cursor->addDay();
            }

            $rows->push([
                'name'              => $periodStart->format('d/m/Y'),
                'start_date'        => $periodStart,
                'end_date'          => $periodEnd,
                'attack_count'      => $attackCount,
                'news_minus7_count' => $minus7Count,
                'news_plus7_count'  => $plus7Count,
            ]);

            $current->addDays(7);
        }

        return $rows;
    }

    /**
     * Carrega notícias de um período específico (usado pelo detalhe ao clicar na barra).
     */
    public function getPeriodNews(Carbon $periodStart, Carbon $periodEnd): array
    {
        $minus7Start = $periodStart->copy()->subDays(7);
        $minus7End   = $periodStart->copy()->subDay();

        $minus7 = News::whereBetween('published_date', [
            $minus7Start->toDateString(),
            $minus7End->toDateString(),
        ])->orderBy('published_date')->get();

        $plus7 = News::whereBetween('published_date', [
            $periodStart->toDateString(),
            $periodEnd->toDateString(),
        ])->orderBy('published_date')->get();

        return [
            'news_minus7' => $minus7,
            'news_plus7'  => $plus7,
        ];
    }
}
