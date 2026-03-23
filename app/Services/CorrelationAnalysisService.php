<?php

namespace App\Services;

use App\Models\HackerAttack;
use App\Models\News;
use App\Models\CorrelationAnalysis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CorrelationAnalysisService
{
    /**
     * Analisa correlações entre ataques e notícias
     */
    public function analyzeCorrelations(): array
    {
        $results = [
            'correlations_found' => 0,
            'patterns_identified' => [],
            'details' => []
        ];

        $attacks = HackerAttack::all();
        $news = News::all();

        foreach ($attacks as $attack) {
            foreach ($news as $newsItem) {
                $score = $this->calculateCorrelationScore($attack, $newsItem);

                if ($score > 40) { // Threshold de 40%
                    $this->storeCorrelation($attack, $newsItem, $score);
                    $results['correlations_found']++;
                    $results['details'][] = [
                        'attack' => $attack->title,
                        'news' => $newsItem->title,
                        'score' => $score,
                        'type' => $this->determineCorrelationType($attack, $newsItem)
                    ];
                }
            }
        }

        // Identifica padrões
        $results['patterns_identified'] = $this->identifyPatterns();

        return $results;
    }

    /**
     * Calcula score de correlação entre ataque e notícia
     */
    private function calculateCorrelationScore(HackerAttack $attack, News $news): float
    {
        $score = 0;
        $maxScore = 100;

        // 1. Análise de palavras-chave similares (peso: 30%)
        $keywordSimilarity = $this->compareKeywords($attack, $news);
        $score += $keywordSimilarity * 0.3;

        // 2. Análise temporal (peso: 20%)
        $timeSimilarity = $this->compareTimestamps($attack, $news);
        $score += $timeSimilarity * 0.2;

        // 3. Análise de entidades/empresas (peso: 25%)
        $entitySimilarity = $this->compareEntities($attack, $news);
        $score += $entitySimilarity * 0.25;

        // 4. Análise de tipo de ataque (peso: 25%)
        $typeSimilarity = $this->compareAttackType($attack, $news);
        $score += $typeSimilarity * 0.25;

        return min($score, $maxScore);
    }

    /**
     * Compara palavras-chave entre ataque e notícia
     */
    private function compareKeywords(HackerAttack $attack, News $news): float
    {
        $attackKeywords = $this->extractKeywords($attack->title . ' ' . $attack->description);
        $newsKeywords = $this->extractKeywords($news->title . ' ' . $news->content);

        if (empty($attackKeywords) || empty($newsKeywords)) {
            return 0;
        }

        $intersection = count(array_intersect($attackKeywords, $newsKeywords));
        $union = count(array_unique(array_merge($attackKeywords, $newsKeywords)));

        return ($union > 0) ? (($intersection / $union) * 100) : 0;
    }

    /**
     * Compara proximidade temporal
     */
    private function compareTimestamps(HackerAttack $attack, News $news): float
    {
        $attackDate = $attack->attack_date;
        $newsDate = $news->published_date;

        if (!$attackDate || !$newsDate) {
            return 0;
        }

        $daysDifference = abs($attackDate->diffInDays($newsDate));

        // Se a notícia foi publicada até 3 dias depois, consideramos correlada
        if ($daysDifference <= 3) {
            return 100 - ($daysDifference * 20); // Máximo 100 se no mesmo dia
        }

        // Se foi publicada até 7 dias depois, tem menos correlação
        if ($daysDifference <= 7) {
            return 60 - ($daysDifference * 5);
        }

        return 0;
    }

    /**
     * Compara entidades afetadas
     */
    private function compareEntities(HackerAttack $attack, News $news): float
    {
        $attackEntity = strtolower($attack->affected_entity ?? '');
        $newsContent = strtolower($news->content . ' ' . $news->title);

        if (empty($attackEntity)) {
            return 0;
        }

        // Procura entidade mencionada na notícia
        if (strpos($newsContent, $attackEntity) !== false) {
            return 100;
        }

        // Procura por palavras-chave da entidade
        $words = explode(' ', $attackEntity);
        $matches = 0;

        foreach ($words as $word) {
            if (strlen($word) > 3 && strpos($newsContent, $word) !== false) {
                $matches++;
            }
        }

        return ($matches > 0) ? (($matches / count($words)) * 100) : 0;
    }

    /**
     * Compara tipo de ataque
     */
    private function compareAttackType(HackerAttack $attack, News $news): float
    {
        $attackType = strtolower($attack->attack_type);
        $newsContent = strtolower($news->content . ' ' . $news->title);

        if (strpos($newsContent, $attackType) !== false) {
            return 100;
        }

        // Mapeamento de sinônimos
        $synonyms = [
            'ransomware' => ['encrypt', 'payment', 'restore', 'locked'],
            'ddos' => ['denial of service', 'down', 'unavailable', 'offline'],
            'phishing' => ['email', 'credential', 'fake', 'impersonate'],
            'malware' => ['virus', 'trojan', 'worm', 'infected'],
            'data breach' => ['leak', 'stolen', 'exposed', 'compromise'],
        ];

        if (isset($synonyms[$attackType])) {
            foreach ($synonyms[$attackType] as $synonym) {
                if (strpos($newsContent, $synonym) !== false) {
                    return 75;
                }
            }
        }

        return 0;
    }

    /**
     * Extrai palavras-chave
     */
    private function extractKeywords(string $text): array
    {
        $text = strtolower($text);
        // Remove pontuação
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        // Divide em palavras
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Filtra palavras muito curtas e stop words
        $stopWords = ['o', 'a', 'de', 'da', 'do', 'e', 'em', 'é', 'for', 'the', 'a', 'an'];
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        return array_unique($keywords);
    }

    /**
     * Determina tipo de correlação
     */
    private function determineCorrelationType(HackerAttack $attack, News $news): string
    {
        $typeScore = $this->compareAttackType($attack, $news);
        $entityScore = $this->compareEntities($attack, $news);
        $timeScore = $this->compareTimestamps($attack, $news);

        if ($typeScore > 60 && $entityScore > 60) {
            return 'direct'; // Ataque e notícia tratam exatamente o mesmo evento
        }

        if ($entityScore > 60) {
            return 'entity_based'; // Mesma entidade afetada
        }

        if ($timeScore > 60) {
            return 'temporal'; // Próximos temporalmente
        }

        return 'contextual'; // Relacionado ao contexto de segurança
    }

    /**
     * Armazena correlação no banco
     */
    private function storeCorrelation(HackerAttack $attack, News $news, float $score): void
    {
        $existingCorrelation = CorrelationAnalysis::where('hacker_attack_id', $attack->id)
            ->where('news_id', $news->id)
            ->first();

        if ($existingCorrelation) {
            $existingCorrelation->update([
                'correlation_score' => $score,
                'analysis_date' => now()->toDateString()
            ]);
        } else {
            CorrelationAnalysis::create([
                'hacker_attack_id' => $attack->id,
                'news_id' => $news->id,
                'correlation_score' => $score,
                'analysis_reason' => $this->generateAnalysisReason($attack, $news),
                'correlation_type' => $this->determineCorrelationType($attack, $news),
                'analysis_date' => now()->toDateString(),
                'is_validated' => false,
            ]);
        }
    }

    /**
     * Gera explicação da correlação
     */
    private function generateAnalysisReason(HackerAttack $attack, News $news): string
    {
        $reasons = [];

        if ($attack->affected_entity && strpos(strtolower($news->content), strtolower($attack->affected_entity)) !== false) {
            $reasons[] = "Notícia menciona a entidade afetada: {$attack->affected_entity}";
        }

        if (strpos(strtolower($news->content), strtolower($attack->attack_type)) !== false) {
            $reasons[] = "Notícia menciona o tipo de ataque: {$attack->attack_type}";
        }

        $daysDiff = $attack->attack_date->diffInDays($news->published_date);
        if ($daysDiff <= 3) {
            $reasons[] = "Proximidade temporal: {$daysDiff} dia(s)";
        }

        return implode('; ', $reasons) ?: 'Correlação por análise de contexto';
    }

    /**
     * Identifica padrões nos dados
     */
    private function identifyPatterns(): array
    {
        $patterns = [];

        // Padrão 1: Ataques mais frequentes
        $topAttackTypes = HackerAttack::selectRaw('attack_type, COUNT(*) as count')
            ->groupBy('attack_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $patterns['top_attack_types'] = $topAttackTypes->map(fn($item) => [
            'type' => $item->attack_type,
            'count' => $item->count
        ])->toArray();

        // Padrão 2: Severidade média
        $severityCounts = HackerAttack::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get();

        $patterns['severity_distribution'] = $severityCounts->map(fn($item) => [
            'severity' => $item->severity,
            'count' => $item->count
        ])->toArray();

        // Padrão 3: Correlações mais fortes
        $strongCorrelations = CorrelationAnalysis::where('correlation_score', '>', 70)
            ->orderByDesc('correlation_score')
            ->limit(10)
            ->get();

        $patterns['strong_correlations'] = $strongCorrelations->count();

        // Padrão 4: Período mais ativo
        $activeMonth = HackerAttack::selectRaw('DATE_FORMAT(attack_date, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderByDesc('count')
            ->first();

        if ($activeMonth) {
            $patterns['most_active_period'] = $activeMonth->month;
        }

        return $patterns;
    }
}
