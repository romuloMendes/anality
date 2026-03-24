<?php
namespace App\Http\Controllers;

use App\Services\NewsImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
     * Processar upload e importação de JSON
     */
    public function import(Request $request)
    {
        // Validação do arquivo
        $validated = $request->validate([
            'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:10240', // 10MB max
        ], [
            'json_file.required'  => 'Selecione um arquivo JSON',
            'json_file.file'      => 'O arquivo deve ser um arquivo válido',
            'json_file.mimes'     => 'O arquivo deve ser um JSON',
            'json_file.mimetypes' => 'O arquivo deve ser um JSON',
            'json_file.max'       => 'O arquivo não pode exceder 10MB',
        ]);

        try {

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('JSON inválido: ' . json_last_error_msg());
            }

            $file        = $request->file('json_file');
            $jsonContent = File::get($file->getPathname());

            // Processar importação usando conteúdo JSON, com hash no storage
            $result = $this->importService->importJsonString($jsonContent);

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
            if (isset($storedPath) && Storage::disk('local')->exists($storedPath)) {
                Storage::disk('local')->delete($storedPath);
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
            'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:10240',
        ]);

        try {
            $file        = $request->file('json_file');
            $jsonContent = File::get($file->getPathname());
            $result      = $this->importService->importJsonString($jsonContent);

            return response()->json($result, $result['success'] ? 200 : 422);

        } catch (\Exception $e) {
            if (isset($storedPath) && Storage::disk('local')->exists($storedPath)) {
                Storage::disk('local')->delete($storedPath);
            }

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
