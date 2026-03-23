<?php

namespace App\Http\Controllers;

use App\Services\HackerAttackScraperService;
use App\Services\NewsScraperService;
use App\Services\CorrelationAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalysisController extends Controller
{
    /**
     * Scraping de ataques hackers
     */
    public function scrapeAttacks()
    {
        try {
            $scraperService = new HackerAttackScraperService();
            $attacks = $scraperService->scrapeFromMultipleSources();
            $savedCount = $scraperService->saveAttacks($attacks);

            return response()->json([
                'success' => true,
                'message' => "Processados {$savedCount} novos ataques",
                'count' => $savedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Scrape attacks error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Scraping de notícias
     */
    public function scrapeNews()
    {
        try {
            $scraperService = new NewsScraperService();
            $news = $scraperService->scrapeFromMultipleSources();
            $savedCount = $scraperService->saveNews($news);

            return response()->json([
                'success' => true,
                'message' => "Processadas {$savedCount} novas notícias",
                'count' => $savedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Scrape news error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Executar análise de correlações
     */
    public function runCorrelationAnalysis()
    {
        try {
            $analysisService = new CorrelationAnalysisService();
            $results = $analysisService->analyzeCorrelations();

            return response()->json([
                'success' => true,
                'message' => 'Análise concluída com sucesso',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Correlation analysis error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Executar todos os processos: scraping + análise
     */
    public function runFullAnalysis()
    {
        try {
            // 1. Scraping de ataques
            $attackScraper = new HackerAttackScraperService();
            $attacks = $attackScraper->scrapeFromMultipleSources();
            $attacksCount = $attackScraper->saveAttacks($attacks);

            // 2. Scraping de notícias
            $newsScraper = new NewsScraperService();
            $news = $newsScraper->scrapeFromMultipleSources();
            $newsCount = $newsScraper->saveNews($news);

            // 3. Análise de correlações
            $analysisService = new CorrelationAnalysisService();
            $correlationResults = $analysisService->analyzeCorrelations();

            return response()->json([
                'success' => true,
                'message' => 'Análise completa concluída',
                'data' => [
                    'attacks_processed' => $attacksCount,
                    'news_processed' => $newsCount,
                    'correlations_found' => $correlationResults['correlations_found'],
                    'patterns' => $correlationResults['patterns_identified'],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Full analysis error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe status dos dados
     */
    public function status()
    {
        return response()->json([
            'attacks' => \App\Models\HackerAttack::count(),
            'news' => \App\Models\News::count(),
            'correlations' => \App\Models\CorrelationAnalysis::count(),
            'last_attack' => \App\Models\HackerAttack::latest('created_at')->first()?->created_at,
            'last_news' => \App\Models\News::latest('created_at')->first()?->created_at,
        ]);
    }
}
