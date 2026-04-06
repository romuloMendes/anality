<?php

namespace App\Http\Controllers;

use App\Services\NewsImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class NewsImportController extends Controller
{
    private const MAX_FILE_KB = 102400; // 100MB

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
        if ($this->isPostTooLarge($request)) {
            return redirect()->route('news-import.form')
                ->with('error', 'Arquivo muito grande para envio. Limite atual: ' . $this->getReadablePostMaxSize() . '.')
                ->with('import_result', [
                    'success' => false,
                    'error' => 'POST excedeu o limite permitido.',
                ]);
        }

        if ($uploadError = $this->getUploadErrorMessage($request)) {
            return redirect()->route('news-import.form')
                ->with('error', $uploadError)
                ->with('import_result', [
                    'success' => false,
                    'error' => $uploadError,
                ]);
        }

        // Validação do arquivo
        $validated = $request->validate([
            'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:' . self::MAX_FILE_KB,
        ], [
            'json_file.required'  => 'Selecione um arquivo JSON',
            'json_file.file'      => 'O arquivo deve ser um arquivo válido',
            'json_file.uploaded'  => 'Falha no upload do arquivo. Limite atual do PHP: ' . $this->getReadableUploadMaxSize() . '.',
            'json_file.mimes'     => 'O arquivo deve ser um JSON',
            'json_file.mimetypes' => 'O arquivo deve ser um JSON',
            'json_file.max'       => 'O arquivo não pode exceder 100MB',
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
        if ($this->isPostTooLarge($request)) {
            return response()->json([
                'success' => false,
                'error' => 'Arquivo muito grande para envio. Limite atual: ' . $this->getReadablePostMaxSize() . '.',
            ], 413);
        }

        if ($uploadError = $this->getUploadErrorMessage($request)) {
            return response()->json([
                'success' => false,
                'error' => $uploadError,
                'php_limits' => [
                    'upload_max_filesize' => $this->getReadableUploadMaxSize(),
                    'post_max_size' => $this->getReadablePostMaxSize(),
                ],
            ], 422);
        }

        $validated = $request->validate([
            'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:' . self::MAX_FILE_KB,
        ], [
            'json_file.required'  => 'Selecione um arquivo JSON',
            'json_file.file'      => 'O arquivo deve ser um arquivo válido',
            'json_file.uploaded'  => 'Falha no upload do arquivo. Limite atual do PHP: ' . $this->getReadableUploadMaxSize() . '.',
            'json_file.mimes'     => 'O arquivo deve ser um JSON',
            'json_file.mimetypes' => 'O arquivo deve ser um JSON',
            'json_file.max'       => 'O arquivo não pode exceder 100MB',
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

    /**
     * API para importação de JSON no formato semanal (Título, Resumo, Timestamp, Categoria)
     */
    public function importWeeklyApi(Request $request)
    {
        if ($this->isPostTooLarge($request)) {
            return response()->json([
                'success' => false,
                'error' => 'Arquivo muito grande para envio. Limite atual: ' . $this->getReadablePostMaxSize() . '.',
            ], 413);
        }

        if ($uploadError = $this->getUploadErrorMessage($request)) {
            return response()->json([
                'success' => false,
                'error' => $uploadError,
            ], 422);
        }

        $request->validate([
            'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:' . self::MAX_FILE_KB,
        ], [
            'json_file.required'  => 'Selecione um arquivo JSON',
            'json_file.uploaded'  => 'Falha no upload. Limite atual do PHP: ' . $this->getReadableUploadMaxSize() . '.',
            'json_file.mimes'     => 'O arquivo deve ser um JSON',
            'json_file.mimetypes' => 'O arquivo deve ser um JSON',
            'json_file.max'       => 'O arquivo não pode exceder 100MB',
        ]);

        try {
            $file        = $request->file('json_file');
            $jsonContent = File::get($file->getPathname());
            $result      = $this->importService->importWeeklyJsonString($jsonContent);

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function isPostTooLarge(Request $request): bool
    {
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);

        if ($contentLength <= 0) {
            return false;
        }

        return $contentLength > $this->parseIniSizeToBytes((string) ini_get('post_max_size'));
    }

    private function parseIniSizeToBytes(string $value): int
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return 0;
        }

        $unit = strtolower(substr($trimmed, -1));
        $number = (float) $trimmed;

        return match ($unit) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }

    private function getReadablePostMaxSize(): string
    {
        $postMax = trim((string) ini_get('post_max_size'));

        return $postMax !== '' ? $postMax : 'desconhecido';
    }

    private function getReadableUploadMaxSize(): string
    {
        $uploadMax = trim((string) ini_get('upload_max_filesize'));

        return $uploadMax !== '' ? $uploadMax : 'desconhecido';
    }

    private function getUploadErrorMessage(Request $request): ?string
    {
        if (!$request->hasFile('json_file')) {
            return null;
        }

        $file = $request->file('json_file');

        if (!$file || $file->isValid()) {
            return null;
        }

        $errorCode = $file->getError();

        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'Falha no upload: arquivo excede upload_max_filesize (' . $this->getReadableUploadMaxSize() . ').',
            UPLOAD_ERR_FORM_SIZE => 'Falha no upload: arquivo excede o limite definido pelo formulário.',
            UPLOAD_ERR_PARTIAL => 'Falha no upload: arquivo enviado parcialmente. Tente novamente.',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falha no upload: pasta temporária não está configurada no PHP.',
            UPLOAD_ERR_CANT_WRITE => 'Falha no upload: sem permissão para escrever no disco.',
            UPLOAD_ERR_EXTENSION => 'Falha no upload: uma extensão do PHP interrompeu o envio.',
            default => 'Falha no upload do arquivo (código ' . $errorCode . ').',
        };
    }
}
