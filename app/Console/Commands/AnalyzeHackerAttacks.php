<?php

namespace App\Console\Commands;

use App\Services\HackerAttackScraperService;
use App\Services\NewsScraperService;
use App\Services\CorrelationAnalysisService;
use Illuminate\Console\Command;

class AnalyzeHackerAttacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analysis:run {--attacks : Scrape apenas ataques} {--news : Scrape apenas notícias} {--correlations : Apenas análise de correlações} {--all : Executar tudo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute scraping de ataques e notícias, e análise de correlações';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $doAll = $this->option('all');
        $doAttacks = $this->option('attacks') || $doAll;
        $doNews = $this->option('news') || $doAll;
        $doCorrelations = $this->option('correlations') || $doAll;

        // Se nenhuma opção for selecionada
        if (!$doAttacks && !$doNews && !$doCorrelations) {
            $this->info('Selecione uma opção: --attacks, --news, --correlations, ou --all');
            return;
        }

        if ($doAttacks) {
            $this->handleAttacksScraping();
        }

        if ($doNews) {
            $this->handleNewsScraping();
        }

        if ($doCorrelations) {
            $this->handleCorrelationAnalysis();
        }

        $this->info('✓ Análise concluída com sucesso!');
    }

    /**
     * Scraping de ataques hackers
     */
    private function handleAttacksScraping()
    {
        $this->info('🔍 Iniciando scraping de ataques hackers...');

        try {
            $scraperService = new HackerAttackScraperService();

            $this->info('  └─ Processando fontes de ataques...');
            $attacks = $scraperService->scrapeFromMultipleSources();

            $this->info('  └─ Salvando ataques no banco de dados...');
            $savedCount = $scraperService->saveAttacks($attacks);

            $this->info("✓ {$savedCount} novos ataques foram salvos");
        } catch (\Exception $e) {
            $this->error('✗ Erro durante scraping de ataques: ' . $e->getMessage());
        }
    }

    /**
     * Scraping de notícias
     */
    private function handleNewsScraping()
    {
        $this->info('📰 Iniciando scraping de notícias...');

        try {
            $scraperService = new NewsScraperService();

            $this->info('  └─ Processando fontes de notícias...');
            $news = $scraperService->scrapeFromMultipleSources();

            $this->info('  └─ Salvando notícias no banco de dados...');
            $savedCount = $scraperService->saveNews($news);

            $this->info("✓ {$savedCount} novas notícias foram salvas");
        } catch (\Exception $e) {
            $this->error('✗ Erro durante scraping de notícias: ' . $e->getMessage());
        }
    }

    /**
     * Análise de correlações
     */
    private function handleCorrelationAnalysis()
    {
        $this->info('🔗 Executando análise de correlações...');

        try {
            $this->info('  └─ Calculando correlações entre ataques e notícias...');
            $analysisService = new CorrelationAnalysisService();
            $results = $analysisService->analyzeCorrelations();

            $this->info("✓ {$results['correlations_found']} correlações encontradas");

            if (!empty($results['patterns_identified'])) {
                $this->info('  └─ Padrões identificados:');
                foreach ($results['patterns_identified'] as $key => $value) {
                    $this->info("      • {$key}: " . json_encode($value));
                }
            }
        } catch (\Exception $e) {
            $this->error('✗ Erro durante análise de correlações: ' . $e->getMessage());
        }
    }
}
