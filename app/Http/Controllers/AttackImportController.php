<?php
namespace App\Http\Controllers;

use App\Services\NewsImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AttackImportController extends Controller
{
    private NewsImportService $importService;

    public function __construct(NewsImportService $importService)
    {
        $this->importService = $importService;
    }

    public function showForm()
    {
        return view('attacks.import');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            // 'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:10240',
        ], [
            'json_file.required'  => 'Selecione um arquivo JSON',
            'json_file.file'      => 'O arquivo deve ser um arquivo válido',
            'json_file.mimes'     => 'O arquivo deve ser um JSON',
            'json_file.mimetypes' => 'O arquivo deve ser um JSON',
            'json_file.max'       => 'O arquivo não pode exceder 10MB',
        ]);

        try {
            $file = $request->file('json_file');
            // dd('importAttacksFromJsonString');
            $jsonContent = File::get($file->getPathname());
            $result      = $this->importService->importAttacksFromJsonString($jsonContent);

            if ($result['success']) {
                return redirect()->route('attacks-import.form')
                    ->with('success', "Importação concluída! {$result['imported']} ataques importados.")
                    ->with('import_result', $result);
            }

            return redirect()->route('attacks-import.form')
                ->with('error', 'Erro na importação: ' . $result['error'])
                ->with('import_result', $result);

        } catch (\Exception $e) {
            return redirect()->route('attacks-import.form')
                ->with('error', 'Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    public function importApi(Request $request)
    {
        $validated = $request->validate([
            'json_file' => 'required|file|mimes:json,txt|mimetypes:application/json,text/plain|max:10240',
        ]);

        try {
            $file        = $request->file('json_file');
            $jsonContent = File::get($file->getPathname());
            $result      = $this->importService->importAttacksFromJsonString($jsonContent);

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}