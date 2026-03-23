<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\HackerAttack;
use Illuminate\Support\Facades\Log;

class HackerAttackScraperService
{
    private $client;

    public function __construct()
    {
        $this->client = HttpClient::create([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
    }

    /**
     * Scrape de fontes de ataques hackers (exemplo: CISA alerts, Dark web monitored news)
     */
    public function scrapeFromMultipleSources(): array
    {
        $attacks = [];

        try {
            // Fonte 1: CISA Alerts (exemplo)
            $cisaAttacks = $this->scrapeCISAAlerts();
            $attacks = array_merge($attacks, $cisaAttacks);
        } catch (\Exception $e) {
            Log::warning('Erro ao fazer scrape CISA: ' . $e->getMessage());
        }

        try {
            // Fonte 2: SecurityAffairs (exemplo)
            $securityAffairsAttacks = $this->scrapeSecurityAffairs();
            $attacks = array_merge($attacks, $securityAffairsAttacks);
        } catch (\Exception $e) {
            Log::warning('Erro ao fazer scrape SecurityAffairs: ' . $e->getMessage());
        }

        return $attacks;
    }

    /**
     * Scrape de CISA (Cybersecurity & Infrastructure Security Agency)
     */
    private function scrapeCISAAlerts(): array
    {
        $attacks = [];

        try {
            $response = $this->client->request('GET', 'https://www.cisa.gov/news-events/alerts');
            $content = $response->getContent();
            $crawler = new Crawler($content);

            $crawler->filter('article')->each(function (Crawler $article) use (&$attacks) {
                $title = $article->filter('h3 a')->text(null);
                $url = $article->filter('h3 a')->attr('href');
                $publicationDate = $article->filter('time')->attr('datetime');

                if ($title && strpos(strtolower($title), 'attack') !== false) {
                    $attacks[] = [
                        'title' => $title,
                        'attack_type' => $this->classifyAttackType($title),
                        'severity' => $this->estimateSeverity($title),
                        'source_name' => 'CISA',
                        'source_url' => $url,
                        'attack_date' => $publicationDate ? substr($publicationDate, 0, 10) : now()->toDateString(),
                        'tags' => $this->extractTags($title),
                    ];
                }
            });
        } catch (\Exception $e) {
            Log::error('CISA scrape error: ' . $e->getMessage());
        }

        return $attacks;
    }

    /**
     * Scrape de SecurityAffairs
     */
    private function scrapeSecurityAffairs(): array
    {
        $attacks = [];

        try {
            $response = $this->client->request('GET', 'https://securityaffairs.com');
            $content = $response->getContent();
            $crawler = new Crawler($content);

            $crawler->filter('article')->slice(0, 20)->each(function (Crawler $article) use (&$attacks) {
                $titleElement = $article->filter('h2, h3');
                $title = $titleElement->text(null);
                $url = $article->filter('a')->attr('href');

                if ($title && (strpos(strtolower($title), 'hacker') !== false ||
                    strpos(strtolower($title), 'breach') !== false ||
                    strpos(strtolower($title), 'attack') !== false)) {

                    $attacks[] = [
                        'title' => $title,
                        'attack_type' => $this->classifyAttackType($title),
                        'severity' => $this->estimateSeverity($title),
                        'source_name' => 'SecurityAffairs',
                        'source_url' => $url,
                        'attack_date' => now()->toDateString(),
                        'tags' => $this->extractTags($title),
                    ];
                }
            });
        } catch (\Exception $e) {
            Log::error('SecurityAffairs scrape error: ' . $e->getMessage());
        }

        return $attacks;
    }

    /**
     * Classifica o tipo de ataque baseado no título
     */
    private function classifyAttackType(string $title): string
    {
        $title = strtolower($title);

        if (strpos($title, 'ransomware') !== false) return 'Ransomware';
        if (strpos($title, 'ddos') !== false) return 'DDoS';
        if (strpos($title, 'phishing') !== false) return 'Phishing';
        if (strpos($title, 'breach') !== false) return 'Data Breach';
        if (strpos($title, 'malware') !== false) return 'Malware';
        if (strpos($title, 'exploit') !== false) return 'Exploit';
        if (strpos($title, 'vulnerability') !== false) return 'Vulnerability';
        if (strpos($title, 'zero-day') !== false || strpos($title, '0-day') !== false) return 'Zero-Day';

        return 'Other';
    }

    /**
     * Estima o nível de severidade
     */
    private function estimateSeverity(string $title): string
    {
        $title = strtolower($title);

        $criticalKeywords = ['critical', 'critical vulnerability', 'zero-day', 'massive breach', 'widespread'];
        $highKeywords = ['ransomware', 'major', 'significant', 'breach'];
        $mediumKeywords = ['vulnerability', 'issue', 'attack'];

        foreach ($criticalKeywords as $keyword) {
            if (strpos($title, $keyword) !== false) return 'critical';
        }

        foreach ($highKeywords as $keyword) {
            if (strpos($title, $keyword) !== false) return 'high';
        }

        foreach ($mediumKeywords as $keyword) {
            if (strpos($title, $keyword) !== false) return 'medium';
        }

        return 'low';
    }

    /**
     * Extrai tags/palavras-chave do título
     */
    private function extractTags(string $title): array
    {
        $keywords = [
            'ransomware', 'ddos', 'phishing', 'malware', 'breach',
            'exploit', 'vulnerability', 'microsoft', 'apple', 'google',
            'amazon', 'hospital', 'government', 'bank', 'cryptolocker'
        ];

        $tags = [];
        $titleLower = strtolower($title);

        foreach ($keywords as $keyword) {
            if (strpos($titleLower, $keyword) !== false) {
                $tags[] = $keyword;
            }
        }

        return array_unique($tags);
    }

    /**
     * Salva ataques no banco de dados
     */
    public function saveAttacks(array $attacks): int
    {
        $savedCount = 0;

        foreach ($attacks as $attack) {
            try {
                // Evita duplicatas
                $exists = HackerAttack::where('source_url', $attack['source_url'] ?? null)
                    ->where('title', $attack['title'])
                    ->exists();

                if (!$exists) {
                    HackerAttack::create($attack);
                    $savedCount++;
                }
            } catch (\Exception $e) {
                Log::error('Erro ao salvar ataque: ' . $e->getMessage());
            }
        }

        return $savedCount;
    }
}
