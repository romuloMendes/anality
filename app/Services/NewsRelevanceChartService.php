<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class NewsRelevanceChartService
{
    /**
     * Classificação dos níveis de relevância.
     */
    private const LEVELS = [
        'baixo' => ['min' => 1, 'max' => 3,  'label' => '1-3'],
        'medio' => ['min' => 4, 'max' => 6,  'label' => '4-6'],
        'alto'  => ['min' => 7, 'max' => 10, 'label' => '7-10'],
    ];

    /**
     * Gera os dados agregados por blocos fixos de 7 dias e nível de relevância.
     *
     * Para cada período e cada nível (baixo, medio, alto) é emitido um registro:
     * [
     *   'period'          => '01/01/2022 - 06/01/2022',
     *   'name'            => 'baixo',
     *   'relevance_score' => '1-3',
     *   'total'           => 10,
     * ]
     *
     * O primeiro bloco vai de $from até $from + 6 dias (7 dias no total).
     * O último bloco pode ser menor se o intervalo não for múltiplo de 7.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function generate(Carbon $from, Carbon $to): Collection
    {
        // Uma única query trazendo contagens por dia + score
        // minimiza memória e transferência de dados
        $rows = News::query()
            ->whereBetween('published_date', [
                $from->toDateString(),
                $to->toDateString(),
            ])
            ->selectRaw('DATE(published_date) as day, relevance_score, COUNT(*) as total')
            ->groupBy('day', 'relevance_score')
            ->orderBy('day')
            ->get()
            ->groupBy('day');         // Collection keyed by date string

        $result  = collect();
        $current = $from->copy()->startOfDay();

        while ($current->lte($to)) {
            $periodStart = $current->copy();
            $periodEnd   = $current->copy()->addDays(6);

            if ($periodEnd->gt($to)) {
                $periodEnd = $to->copy();
            }

            $label = $periodStart->format('d/m/Y') . ' - ' . $periodEnd->format('d/m/Y');

            // Soma totais para cada nível dentro do bloco
            $levelTotals = array_fill_keys(array_keys(self::LEVELS), 0);

            $cursor = $periodStart->copy();
            while ($cursor->lte($periodEnd)) {
                $dayKey = $cursor->toDateString();

                foreach ($rows->get($dayKey, collect()) as $record) {
                    $score = (int) $record->relevance_score;
                    $level = $this->scoreToLevel($score);

                    if ($level !== null) {
                        $levelTotals[$level] += (int) $record->total;
                    }
                }

                $cursor->addDay();
            }

            foreach (self::LEVELS as $name => $cfg) {
                $result->push([
                    'period'          => $label,
                    'name'            => $name,
                    'relevance_score' => $cfg['label'],
                    'total'           => $levelTotals[$name],
                ]);
            }

            $current->addDays(7);
        }

        return $result;
    }

    /**
     * Transforma o resultado em estrutura pronta para Chart.js / ApexCharts.
     *
     * Retorna:
     * [
     *   'labels'   => ['01/01/2022 - 06/01/2022', ...],
     *   'datasets' => [
     *     ['name' => 'baixo', 'data' => [10, 8, ...]],
     *     ['name' => 'medio', 'data' => [25, 12, ...]],
     *     ['name' => 'alto',  'data' => [18, 9, ...]],
     *   ],
     * ]
     *
     * @param  Collection<int, array<string, mixed>> $rows
     * @return array<string, mixed>
     */
    public function toChartDataset(Collection $rows): array
    {
        $labels   = $rows->pluck('period')->unique()->values();
        $datasets = [];

        foreach (array_keys(self::LEVELS) as $name) {
            $datasets[] = [
                'name' => $name,
                'data' => $rows
                    ->where('name', $name)
                    ->pluck('total')
                    ->values()
                    ->all(),
            ];
        }

        return [
            'labels'   => $labels->all(),
            'datasets' => $datasets,
        ];
    }

    private function scoreToLevel(int $score): ?string
    {
        foreach (self::LEVELS as $name => $cfg) {
            if ($score >= $cfg['min'] && $score <= $cfg['max']) {
                return $name;
            }
        }

        return null;
    }
}
