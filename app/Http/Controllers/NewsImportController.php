<?php

namespace App\Http\Controllers;

use App\Services\NewsImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsImportController extends Controller
{
    protected $importService;

    public function __construct(NewsImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Mostrar formulário de importação
     */
    public function showForm()
    {
        return view('news.import');
    }

    /**
     * Processar upload e importação de CSV
     */
    public function import(Request $request)
    {
        // Validação do arquivo
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ], [
            'csv_file.required' => 'Selecione um arquivo CSV',
            'csv_file.file' => 'O arquivo deve ser um arquivo válido',
            'csv_file.mimes' => 'O arquivo deve ser um CSV',
            'csv_file.max' => 'O arquivo não pode exceder 10MB',
        ]);

        try {
            // Salvar arquivo temporário
            $file = $request->file('csv_file');
            $tempPath = $file->store('imports/temp', 'local');
            $fullPath = storage_path('app/' . $tempPath);

            // Processar importação
            $result = $this->importService->importFromCSV($fullPath);

            // Deletar arquivo temporário
            Storage::delete($tempPath);

            if ($result['success']) {
                return redirect()->route('news-import.form')
                    ->with('success', "Importação concluída! {$result['imported']} notíciaS importadas com sucesso.")
                    ->with('import_result', $result);
            } else {
                return redirect()->route('news-import.form')
                    ->with('error', 'Erro na importação: ' . $result['error'])
                    ->with('import_result', $result);
            }

        } catch (\Exception $e) {
            // Limpar arquivo temporário em caso de erro
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            return redirect()->route('news-import.form')
                ->with('error', 'Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    /**
     * API para importação (JSON)
     */
    public function importApi(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $tempPath = $file->store('imports/temp', 'local');
            $fullPath = storage_path('app/' . $tempPath);

            $result = $this->importService->importFromCSV($fullPath);

            Storage::delete($tempPath);

            return response()->json($result, $result['success'] ? 200 : 422);

        } catch (\Exception $e) {
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
