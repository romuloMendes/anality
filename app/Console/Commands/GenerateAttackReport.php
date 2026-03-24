<?php

declare (strict_types = 1);

namespace App\Console\Commands;

use App\Services\AttackReportService;
use Illuminate\Console\Command;

final class GenerateAttackReport extends Command
{
    protected $signature = 'report:attacks
                            {--start-date=01/01/2022 : Data inicial (formato dd/mm/yyyy)}
                            {--end-date=06/01/2022 : Data final (formato dd/mm/yyyy)}
                            {--format=weekly : weekly ou daily}
                            {--export : Exporta para arquivo}';

    protected $description = 'Gera relatório de ataques (pico de 7 em 7 dias e total)';

    public function __construct(
        private readonly AttackReportService $reportService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $startDate = $this->option('start-date');
        $endDate   = $this->option('end-date');
        $format    = $this->option('format');
        $export    = $this->option('export');

        $this->info("Gerando relatório de ataques...\n");

        try {
            if ($format === 'weekly') {
                $report = $this->reportService->generateWeeklyReport($startDate, $endDate);
            } else {
                $report = $this->reportService->generateDailyReport($startDate, $endDate);
            }

            $total = $this->reportService->getTotalAttacks($startDate, $endDate);

            $this->line("RELATÓRIO DE ATAQUES - " . strtoupper($format));
            $this->line("Período: {$startDate} até {$endDate}");
            $this->line("Total de ataques: {$total}\n");
            $this->line(str_repeat("=", 60));

            foreach ($report as $item) {
                $this->line($item['formatted']);
            }

            $this->line(str_repeat("=", 60));
            $this->newLine();

            if ($export) {
                $filename = storage_path("app/reports/relatorio-ataques-{$format}-" . now()->format('Y-m-d-His') . ".txt");

                if (! is_dir(dirname($filename))) {
                    mkdir(dirname($filename), 0755, true);
                }

                $content = "RELATÓRIO DE ATAQUES - " . strtoupper($format) . "\n";
                $content .= "Período: {$startDate} até {$endDate}\n";
                $content .= "Total de ataques: {$total}\n";
                $content .= str_repeat("=", 60) . "\n\n";

                foreach ($report as $item) {
                    $content .= $item['formatted'] . "\n";
                }

                file_put_contents($filename, $content);

                $this->info("\n✓ Relatório exportado para: {$filename}");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao gerar relatório: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}