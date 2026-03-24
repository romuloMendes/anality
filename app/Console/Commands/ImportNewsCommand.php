<?php

namespace App\Console\Commands;

use App\Services\NewsImportService;
use Illuminate\Console\Command;

class ImportNewsCommand extends Command
{
    protected $signature = 'news:import {file : Caminho para o arquivo CSV}';
    protected $description = 'Importar notícias de um arquivo CSV';

    protected $importService;

    public function __construct(NewsImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle()
    {
        $filePath = $this->argument('file');

        // Validar se arquivo existe
        if (!file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");
            return 1;
        }

        $this->info('Iniciando importação de notícias...');
        $this->newLine();

        // Mostrar barra de progresso
        $progressBar = $this->output->createProgressBar(100);
        $progressBar->start();

        try {
            $result = $this->importService->importFromCSV($filePath);

            $progressBar->finish();
            $this->newLine(2);

            if ($result['success']) {
                $this->info("✓ Importação concluída com sucesso!");
                $this->newLine();

                // Mostrar tabela com resultados
                $this->table(
                    ['Métrica', 'Valor'],
                    [
                        ['Total processado', $result['total']],
                        ['Importadas', $result['imported']],
                        ['Falhadas', $result['failed']],
                    ]
                );

                if (!empty($result['errors'])) {
                    $this->newLine();
                    $this->error("Erros encontrados:");
                    foreach ($result['errors'] as $error) {
                        $this->line("  • {$error}");
                    }
                }

                return 0;
            } else {
                $this->error("Erro na importação: {$result['error']}");
                return 1;
            }

        } catch (\Exception $e) {
            $progressBar->finish();
            $this->newLine();
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }
    }
}
