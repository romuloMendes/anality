<?php

namespace App\Services;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsImportService
{
    private $imported = 0;
    private $failed = 0;
    private $errors = [];

    public function importFromCSV(string $filePath): array
    {
        try {
            $handle = fopen($filePath, 'r');

            if (!$handle) {
                throw new \Exception('Não foi possível abrir o arquivo CSV');
            }

            // Pular cabeçalho
            $header = fgetcsv($handle, 0, ';');

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
                        'row' => $row
                    ]);
                }
            }

            fclose($handle);

            return [
                'success' => true,
                'imported' => $this->imported,
                'failed' => $this->failed,
                'errors' => $this->errors,
                'total' => $this->imported + $this->failed,
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao importar CSV de notícias', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported' => $this->imported,
                'failed' => $this->failed,
            ];
        }
    }

    public function importFromJson(string $filePath): array
    {
        try {
            if (!File::exists($filePath)) {
                throw new \Exception('Arquivo JSON não encontrado');
            }

            $jsonContent = File::get($filePath);
            $decoded = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON inválido: ' . json_last_error_msg());
            }

            if (!is_array($decoded)) {
                throw new \Exception('Formato de JSON inesperado. Deve ser array de objetos.');
            }

            $rowNumber = 0;
            foreach ($decoded as $item) {
                $rowNumber++;

                try {
                    if (!is_array($item)) {
                        throw new \Exception('Formato de item inválido na linha '.$rowNumber);
                    }

                    $this->processData($item, 'log_das_noticias.json');
                    $this->imported++;
                } catch (\Exception $e) {
                    $this->failed++;
                    $this->errors[] = "Linha {$rowNumber}: " . $e->getMessage();
                    Log::error("Erro na importação de notícia JSON (linha {$rowNumber})", [
                        'error' => $e->getMessage(),
                        'item' => $item
                    ]);
                }
            }

            return [
                'success' => true,
                'imported' => $this->imported,
                'failed' => $this->failed,
                'errors' => $this->errors,
                'total' => $this->imported + $this->failed,
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao importar JSON de notícias', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported' => $this->imported,
                'failed' => $this->failed,
            ];
        }
    }

    private function processRow(array $row, array $header): void
    {
        // Mapear dados da linha com os cabeçalhos
        $data = array_combine($header, $row);
        $this->processData($data, 'log_das_noticias.csv');
    }

    private function processData(array $data, string $sourceFile): void
    {
        // Validar campos obrigatórios
        $title = trim($data['title'] ?? '');
        $summary = trim($data['summary'] ?? '');
        $date = trim($data['date'] ?? '');

        if (empty($title)) {
            throw new \Exception('Título é obrigatório');
        }

        if (empty($date)) {
            throw new \Exception('Data é obrigatória');
        }

        // Converter data para formato correto (dd.mês.yyyy para Y-m-d)
        $publishedDate = $this->parseDate($date);

        // Verificar se a notícia já existe
        $exists = News::where('title', $title)
            ->where('published_date', $publishedDate)
            ->exists();

        if ($exists) {
            throw new \Exception('Notícia duplicada');
        }

        // Extrair palavras-chave do título e summary
        $keywords = $this->extractKeywords($title . ' ' . $summary);

        // Criar notícia
        News::create([
            'title' => $title,
            'content' => $summary,
            'summary' => $summary,
            'source_name' => 'Folha de S.Paulo',
            'source_url' => null,
            'published_date' => $publishedDate,
            'category' => 'Politics',
            'keywords' => $keywords,
            'relevance_score' => $this->calculateRelevanceScore($summary),
            'metadata' => [
                'imported_at' => now(),
                'source_file' => $sourceFile,
            ],
        ]);
    }

    private function parseDate(string $dateString): Carbon
    {
        try {
            // Tentar diferentes formatos
            // Formato: "31.dez.2022 às 23h15"
            if (preg_match('/(\d{1,2})\.(\w+)\.(\d{4})\s+às\s+(\d{1,2})h(\d{2})/', $dateString, $matches)) {
                $day = $matches[1];
                $monthStr = $matches[2];
                $year = $matches[3];
                $hour = $matches[4];
                $minute = $matches[5];

                // Mapear mês em português para número
                $months = [
                    'jan' => 1, 'janeiro' => 1,
                    'fev' => 2, 'fevereiro' => 2,
                    'mar' => 3, 'março' => 3,
                    'abr' => 4, 'abril' => 4,
                    'mai' => 5, 'maio' => 5,
                    'jun' => 6, 'junho' => 6,
                    'jul' => 7, 'julho' => 7,
                    'ago' => 8, 'agosto' => 8,
                    'set' => 9, 'setembro' => 9,
                    'out' => 10, 'outubro' => 10,
                    'nov' => 11, 'novembro' => 11,
                    'dez' => 12, 'dezembro' => 12,
                ];

                $month = $months[strtolower($monthStr)] ?? null;
                if (!$month) {
                    throw new \Exception("Mês inválido: {$monthStr}");
                }

                return Carbon::createFromDate($year, $month, $day, $hour, $minute);
            }

            // Tentar Carbon::parse como fallback
            return Carbon::parse($dateString);

        } catch (\Exception $e) {
            throw new \Exception("Data inválida: {$dateString}");
        }
    }

    private function extractKeywords(string $text, int $limit = 10): array
    {
        // Remover palavras comuns em português
        $stopwords = [
            'a', 'o', 'e', 'de', 'da', 'do', 'em', 'para', 'com', 'por', 'que', 'por', 'se',
            'não', 'é', 'são', 'ser', 'tem', 'tinha', 'sido', 'já', 'também', 'mais',
            'uma', 'um', 'he', 'ela', 'aos', 'as', 'os', 'ele', 'seu', 'sua', 'seu',
            'aos', 'às', 'como', 'este', 'esse', 'aquele', 'este',
        ];

        // Converter para minúsculas e extrair palavras
        $words = preg_split('/[\s\W]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 3 && !in_array($word, $stopwords);
        });

        // Contar frequência
        $frequencies = array_count_values($words);
        arsort($frequencies);

        return array_keys(array_slice($frequencies, 0, $limit));
    }

    private function calculateRelevanceScore(string $text): float
    {
        // Score baseado no comprimento do texto
        $lengthScore = min(100, (strlen($text) / 50) * 10);

        // Verificar se contém palavras-chave importantes
        $keywords = [
            'segurança' => 10,
            'ataque' => 15,
            'hacker' => 15,
            'dados' => 10,
            'cibernética' => 15,
            'vulnerabilidade' => 12,
            'exploit' => 15,
            'malware' => 14,
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
}
