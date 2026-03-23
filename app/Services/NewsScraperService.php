<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\News;
use Illuminate\Support\Facades\Log;

class NewsScraperService
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
     * Scrape notícias de múltiplos portais
     */
    public function scrapeFromMultipleSources(): array
    {
        $allNews = [];

        try {
            $techNews = $this->scrapeTechNewsPortals();
            $allNews = array_merge($allNews, $techNews);
        } catch (\Exception $e) {
            Log::warning('Erro ao scrape tech news: ' . $e->getMessage());
        }

        try {
            $securityNews = $this->scrapeSecurityNews();
            $allNews = array_merge($allNews, $securityNews);
        } catch (\Exception $e) {
            Log::warning('Erro ao scrape security news: ' . $e->getMessage());
        }

        return $allNews;
    }

    /**
     * Scrape de portais de tecnologia (exemplo: TechCrunch)
     */
    private function scrapeTechNewsPortals(): array
    {
        $news = [];

        try {
            // Simulando scrape de portal de news sobre segurança
            $response = $this->client->request('GET', 'https://www.techcrunch.com/category/security');
            $content = $response->getContent();
            $crawler = new Crawler($content);

            $crawler->filter('article')->slice(0, 15)->each(function (Crawler $article) use (&$news) {
                $titleElement = $article->filter('h2, h3');
                $title = $titleElement->text(null);
                $linkElement = $article->filter('a');
                $url = $linkElement->attr('href');

                if ($title && strpos(strtolower($title), 'security') !== false) {
                    $news[] = [
                        'title' => trim($title),
                        'content' => $this->extractContent($article),
                        'source_name' => 'TechCrunch',
                        'source_url' => $url,
                        'published_date' => now()->toDateString(),
                        'category' => 'Segurança',
                        'keywords' => $this->extractKeywords($title),
                        'summary' => substr(trim($this->extractContent($article)), 0, 200),
                        'relevance_score' => $this->calculateRelevance($title),
                    ];
                }
            });
        } catch (\Exception $e) {
            Log::error('TechCrunch scrape error: ' . $e->getMessage());
        }

        return $news;
    }

    /**
     * Scrape de portais de segurança
     */
    private function scrapeSecurityNews(): array
    {
        $news = [];

        try {
            $response = $this->client->request('GET', 'https://www.zdnet.com/security');
            $content = $response->getContent();
            $crawler = new Crawler($content);

            $crawler->filter('article')->slice(0, 15)->each(function (Crawler $article) use (&$news) {
                $titleElement = $article->filter('h2, h3, .title');
                $title = $titleElement->text(null);
                $url = $article->filter('a')->attr('href');

                if ($title) {
                    $news[] = [
                        'title' => trim($title),
                        'content' => $this->extractContent($article),
                        'source_name' => 'ZDNet',
                        'source_url' => $url,
                        'published_date' => now()->toDateString(),
                        'category' => 'Tecnologia',
                        'keywords' => $this->extractKeywords($title),
                        'summary' => substr(trim($this->extractContent($article)), 0, 200),
                        'relevance_score' => $this->calculateRelevance($title),
                    ];
                }
            });
        } catch (\Exception $e) {
            Log::error('ZDNet scrape error: ' . $e->getMessage());
        }

        return $news;
    }

    /**
     * Extrai conteúdo do artigo
     */
    private function extractContent(Crawler $article): string
    {
        $content = '';

        try {
            $content = $article->filter('p')->text(null) ?: '';
        } catch (\Exception $e) {
            $content = '';
        }

        return $content;
    }

    /**
     * Extrai palavras-chave relevantes
     */
    private function extractKeywords(string $title): array
    {
        $keywords = [
            'hacker', 'ataque', 'vírus', 'malware', 'ransomware', 'phishing',
            'criptografia', 'banco', 'dados', 'segurança', 'computador',
            'internet', 'rede', 'firewall', 'exploit', 'vulnerability',
            'breach', 'hacking', 'hackers', 'cybercriminals', 'crime'
        ];

        $found = [];
        $titleLower = strtolower($title);

        foreach ($keywords as $keyword) {
            if (strpos($titleLower, $keyword) !== false) {
                $found[] = $keyword;
            }
        }

        return array_unique($found);
    }

    /**
     * Calcula score de relevância
     */
    private function calculateRelevance(string $title): int
    {
        $score = 5; // Base score
        $title = strtolower($title);

        $relevanceKeywords = [
            'hacker' => 3,
            'ataque' => 3,
            'ransomware' => 4,
            'exploit' => 3,
            'breach' => 4,
            'malware' => 3,
            'crime' => 2,
            'internet' => 1,
        ];

        foreach ($relevanceKeywords as $keyword => $points) {
            if (strpos($title, $keyword) !== false) {
                $score += $points;
            }
        }

        return min($score, 10); // Máximo de 10
    }

    /**
     * Salva notícias no banco de dados
     */
    public function saveNews(array $newsArray): int
    {
        $savedCount = 0;

        foreach ($newsArray as $newsItem) {
            try {
                // Evita duplicatas
                $exists = News::where('source_url', $newsItem['source_url'] ?? null)
                    ->where('title', $newsItem['title'])
                    ->exists();

                if (!$exists) {
                    News::create($newsItem);
                    $savedCount++;
                }
            } catch (\Exception $e) {
                Log::error('Erro ao salvar notícia: ' . $e->getMessage());
            }
        }

        return $savedCount;
    }
}
