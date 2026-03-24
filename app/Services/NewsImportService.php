<?php
namespace App\Services;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsImportService
{
    private int $imported = 0;
    private int $failed   = 0;
    private array $errors = [];

    public function importFromCSV(string $filePath): array
    {
        try {
            $handle = fopen($filePath, 'r');

            if (! $handle) {
                throw new \Exception('Não foi possível abrir o arquivo CSV');
            }

            $header    = fgetcsv($handle, 0, ';');
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $rowNumber++;

                try {
                    $this->processRow($row, $header);
                    $this->imported++;
                } catch (\Exception $e) {
                    $this->failed++;
                    $this->errors[] = "Linha {$rowNumber}: " . $e->getMessage();
                    Log::error("Erro na importação de notícia (linha {$rowNumber})", [
                        'error' => $e->getMessage(),
                        'row'   => $row,
                    ]);
                }
            }

            fclose($handle);

            return [
                'success'  => true,
                'imported' => $this->imported,
                'failed'   => $this->failed,
                'errors'   => $this->errors,
                'total'    => $this->imported + $this->failed,
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao importar CSV de notícias', [
                'error' => $e->getMessage(),
                'file'  => $filePath,
            ]);

            return [
                'success'  => false,
                'error'    => $e->getMessage(),
                'imported' => $this->imported,
                'failed'   => $this->failed,
            ];
        }
    }

    public function importFromJson(string $storedPath): array
    {
        try {
            $filePath = Storage::disk('local')->path($storedPath);

            if (! File::exists($filePath)) {
                throw new \Exception('Arquivo JSON não encontrado: ' . $filePath);
            }

            $jsonContent = File::get($filePath);
            $decoded     = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON inválido: ' . json_last_error_msg());
            }

            if (! is_array($decoded)) {
                throw new \Exception('Formato de JSON inesperado. Deve ser array de objetos.');
            }

            $rowNumber = 0;
            foreach ($decoded as $item) {
                $rowNumber++;

                try {
                    if (! is_array($item)) {
                        throw new \Exception('Formato de item inválido na linha ' . $rowNumber);
                    }

                    $this->processData($item, basename($filePath));
                    $this->imported++;
                } catch (\Exception $e) {
                    $this->failed++;
                    $this->errors[] = "Linha {$rowNumber}: " . $e->getMessage();
                    Log::error("Erro na importação de notícia JSON (linha {$rowNumber})", [
                        'error' => $e->getMessage(),
                        'item'  => $item,
                    ]);
                }
            }

            return [
                'success'  => true,
                'imported' => $this->imported,
                'failed'   => $this->failed,
                'errors'   => $this->errors,
                'total'    => $this->imported + $this->failed,
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao importar JSON de notícias', [
                'error' => $e->getMessage(),
                'file'  => $storedPath,
            ]);

            return [
                'success'  => false,
                'error'    => $e->getMessage(),
                'imported' => $this->imported,
                'failed'   => $this->failed,
            ];
        }
    }

    public function importJsonString(string $jsonContent): array
    {
        $hashName   = $this->generateHashName();
        $storedPath = "imports/temp/{$hashName}.json";

        Storage::disk('local')->put($storedPath, $jsonContent);

        $result = $this->importFromJson($storedPath);

        Storage::disk('local')->delete($storedPath);

        return $result;
    }

    private function generateHashName(): string
    {
        $randomNumber = random_int(100000, 999999);
        $timestamp    = Carbon::now()->timestamp;
        $base         = sprintf('%d_%d_%s', $randomNumber, $timestamp, uniqid('', true));

        return sha1($base);
    }

    private function processRow(array $row, array $header): void
    {
        $data = array_combine($header, $row);
        $this->processData($data, 'log_das_noticias.csv');
    }

    private function processData(array $data, string $sourceFile): void
    {
        $title   = trim($data['title'] ?? '');
        $summary = trim($data['summary'] ?? '');
        $date    = trim($data['date'] ?? '');

        if (empty($title)) {
            throw new \Exception('Título é obrigatório');
        }

        if (empty($date)) {
            throw new \Exception('Data é obrigatória');
        }

        $publishedDate = $this->parseDate($date);

        $exists = News::where('title', $title)
            ->where('published_date', $publishedDate)
            ->exists();

        if ($exists) {
            throw new \Exception('Notícia duplicada');
        }

        $keywords = $this->extractKeywords($title . ' ' . $summary);

        News::create([
            'title'           => $title,
            'content'         => $summary,
            'summary'         => $summary,
            'source_name'     => 'Folha de S.Paulo',
            'source_url'      => null,
            'published_date'  => $publishedDate,
            'category'        => 'Politics',
            'keywords'        => $keywords,
            'relevance_score' => $this->calculateRelevanceScore($summary),
            'metadata'        => [
                'imported_at' => now(),
                'source_file' => $sourceFile,
            ],
        ]);
    }

    private function parseDate(string $dateString): Carbon
    {
        try {
            if (preg_match('/(\d{1,2})\.(\w+)\.(\d{4})\s+às\s+(\d{1,2})h(\d{2})/', $dateString, $matches)) {
                $day      = $matches[1];
                $monthStr = $matches[2];
                $year     = $matches[3];
                $hour     = $matches[4];
                $minute   = $matches[5];

                $months = [
                    'jan' => 1, 'janeiro'   => 1,
                    'fev' => 2, 'fevereiro' => 2,
                    'mar' => 3, 'março'     => 3,
                    'abr' => 4, 'abril'     => 4,
                    'mai' => 5, 'maio'      => 5,
                    'jun' => 6, 'junho'     => 6,
                    'jul' => 7, 'julho'     => 7,
                    'ago' => 8, 'agosto'    => 8,
                    'set' => 9, 'setembro'  => 9,
                    'out' => 10, 'outubro'  => 10,
                    'nov' => 11, 'novembro' => 11,
                    'dez' => 12, 'dezembro' => 12,
                ];

                $month = $months[strtolower($monthStr)] ?? null;
                if (! $month) {
                    throw new \Exception("Mês inválido: {$monthStr}");
                }

                return Carbon::createFromDate($year, $month, $day, $hour, $minute);
            }

            return Carbon::parse($dateString);

        } catch (\Exception $e) {
            throw new \Exception("Data inválida: {$dateString}");
        }
    }

    private function extractKeywords(string $text, int $limit = 10): array
    {
        $stopwords = [
            'a', 'o', 'e', 'de', 'da', 'do', 'em', 'para', 'com', 'por', 'que', 'por', 'se',
            'não', 'é', 'são', 'ser', 'tem', 'tinha', 'sido', 'já', 'também', 'mais',
            'uma', 'um', 'he', 'ela', 'aos', 'as', 'os', 'ele', 'seu', 'sua', 'seu',
            'aos', 'às', 'como', 'este', 'esse', 'aquele', 'este',
        ];

        $words = preg_split('/[\s\W]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) > 3 && ! in_array($word, $stopwords);
        });

        $frequencies = array_count_values($words);
        arsort($frequencies);

        return array_keys(array_slice($frequencies, 0, $limit));
    }

    private function extractTags(string $text, int $limit = 5): array
    {
        $attackKeywords = [
            'ransomware', 'ddos', 'phishing', 'malware', 'breach', 'exploit', 'vulnerability',
            'microsoft', 'apple', 'google', 'amazon', 'hospital', 'government', 'bank',
            'cryptolocker', 'worm', 'trojan', 'botnet', 'spyware', 'adware', 'rootkit',
            'http', 'flood', 'sql', 'injection', 'xss', 'csrf', 'zero', 'day',
        ];

        $stopwords = [
            'a', 'o', 'e', 'de', 'da', 'do', 'em', 'para', 'com', 'por', 'que', 'por', 'se',
            'não', 'é', 'são', 'ser', 'tem', 'tinha', 'sido', 'já', 'também', 'mais',
        ];

        $tags      = [];
        $textLower = strtolower($text);

        // Buscar palavras-chave de ataque conhecidas
        foreach ($attackKeywords as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $tags[] = $keyword;
            }
        }

        // Se não encontrou keywords de ataque, extrair palavras mais relevantes
        if (empty($tags)) {
            $words = preg_split('/[\s\W]+/', $textLower, -1, PREG_SPLIT_NO_EMPTY);
            $words = array_filter($words, function ($word) use ($stopwords) {
                return strlen($word) > 3 && ! in_array($word, $stopwords);
            });

            $frequencies = array_count_values($words);
            arsort($frequencies);
            $tags = array_keys(array_slice($frequencies, 0, $limit));
        }

        return array_unique(array_slice($tags, 0, $limit));
    }

    private function calculateRelevanceScore(string $text): float
    {
        $lengthScore = min(100, (strlen($text) / 50) * 10);

        $keywords = [
            'segurança'       => 10,
            'ataque'          => 15,
            'hacker'          => 15,
            'dados'           => 10,
            'cibernética'     => 15,
            'vulnerabilidade' => 12,
            'exploit'         => 15,
            'malware'         => 14,
        ];

        $keywordScore = 0;
        foreach ($keywords as $keyword => $score) {
            if (stripos($text, $keyword) !== false) {
                $keywordScore = max($keywordScore, $score);
            }
        }

        $finalScore = (($lengthScore * 0.4) + ($keywordScore * 0.6));

        return min(100, round($finalScore, 2));
    }

    public function importAttacksFromJsonString(string $jsonContent): array
    {
        $hashName   = $this->generateHashName();
        $storedPath = "imports/temp/{$hashName}.json";

        Storage::disk('local')->put($storedPath, $jsonContent);

        $result = $this->importAttacksFromJson($storedPath);

        Storage::disk('local')->delete($storedPath);

        return $result;
    }

    private function importAttacksFromJson(string $storedPath): array
    {
        try {
            if (Str::startsWith($storedPath, ['/', '\\'])) {
                $filePath = $storedPath;
            } else {
                $filePath = Storage::disk('local')->path($storedPath);
            }

            if (! File::exists($filePath)) {
                throw new \Exception('Arquivo JSON não encontrado: ' . $filePath);
            }

            $jsonContent = File::get($filePath);
            $decoded     = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON inválido: ' . json_last_error_msg());
            }

            if (! is_array($decoded)) {
                throw new \Exception('Formato de JSON inesperado. Deve ser array de objetos.');
            }

            $rowNumber = 0;
            foreach ($decoded as $item) {
                $rowNumber++;

                try {
                    if (! is_array($item)) {
                        throw new \Exception('Formato de item inválido na linha ' . $rowNumber);
                    }

                    $this->processAttackData($item, basename($filePath));
                    $this->imported++;
                } catch (\Exception $e) {
                    dd($e);
                    $this->failed++;
                    $this->errors[] = "Linha {$rowNumber}: " . $e->getMessage();
                    Log::error("Erro na importação de ataque JSON (linha {$rowNumber})", [
                        'error' => $e->getMessage(),
                        'item'  => $item,
                    ]);
                }
            }

            return [
                'success'  => true,
                'imported' => $this->imported,
                'failed'   => $this->failed,
                'errors'   => $this->errors,
                'total'    => $this->imported + $this->failed,
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao importar JSON de ataques', [
                'error' => $e->getMessage(),
                'file'  => $storedPath,
            ]);

            return [
                'success'  => false,
                'error'    => $e->getMessage(),
                'imported' => $this->imported,
                'failed'   => $this->failed,
            ];
        }
    }

    private function processAttackData(array $data, string $sourceFile): void
    {
        $title          = trim($data['title'] ?? ($data['tipo_ataque'] ?? 'Ataque Desconhecido'));
        $description    = trim($data['summary'] ?? ($data['descricao'] ?? ''));
        $attackType     = trim($data['tipo_ataque'] ?? ($data['type'] ?? ($data['attack_type'] ?? 'Other')));
        $severity       = $this->normalizeSeverity($data['severity'] ?? $data['ataques'] ?? ($data['rating'] ?? 'low'));
        $affectedEntity = trim($data['pais_alvo'] ?? $data['affected_entity'] ?? null);
        $originCountry  = trim($data['pais_origem'] ?? $data['source_name'] ?? null);
        $attackDate     = trim($data['data'] ?? ($data['date'] ?? ($data['attack_date'] ?? '')));

        if (empty($attackDate)) {
            throw new \Exception('Data do ataque é obrigatória');
        }
        $attackDate = $this->convertToDate($attackDate);
        if (empty($attackDate)) {
            throw new \Exception('Data do ataque é obrigatória');
        }

        // $exists = \App\Models\HackerAttack::where('title', $title)
        //     ->where('attack_date', $attackDate)
        //     ->exists();

        // if ($exists) {
        //     throw new \Exception('Ataque duplicado');
        // }

        \App\Models\HackerAttack::create([
            'title'           => $title,
            'description'     => $description,
            'attack_type'     => $attackType,
            'severity'        => $severity,
            'affected_entity' => $affectedEntity,
            'attack_date'     => $attackDate,
            'source_name'     => $data['source_name'] ?? 'log_dos_ataques',
            'source_url'      => $data['source_url'] ?? null,
            'tags'            => $this->extractTags($title . ' ' . $description),
            'metadata'        => [
                'imported_at' => now(),
                'source_file' => $sourceFile,
            ],
        ]);
    }

    private function convertToDate(string $datetime): string
    {
        try {
            return Carbon::parse($datetime)->toDateString();
        } catch (\Exception $e) {
            return null;
            // throw new \Exception("Data inválida: {$datetime}");
        }
    }
    private function normalizeSeverity($rawSeverity): string
    {
        if (is_numeric($rawSeverity)) {
            $value = (float) $rawSeverity;
            return match (true) {
                $value >= 0.75 => 'critical',
                $value >= 0.5  => 'high',
                $value >= 0.25 => 'medium',
                default        => 'low',
            };
        }

        $level = strtolower(trim((string) $rawSeverity));

        return match ($level) {
            'critical', 'critico', 'crítico' => 'critical',
            'high', 'alto'    => 'high',
            'medium', 'médio' => 'medium',
            'low', 'baixo'    => 'low',
            default => 'low',
        };
    }
}