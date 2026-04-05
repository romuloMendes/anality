<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AttackImportBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class AttackImportBatchController extends Controller
{
    public function index(): View
    {
        $batches = AttackImportBatch::query()
            ->withCount('attacks')
            ->latest()
            ->paginate(20);

        return view('attacks.batches.index', compact('batches'));
    }

    public function destroy(AttackImportBatch $batch): RedirectResponse
    {
        if ($batch->isReverted()) {
            return redirect()->route('attacks-batches.index')
                ->with('error', 'Este lote já foi desfeito.');
        }

        $deleted = $batch->attacks()->count();
        $batch->revert();

        return redirect()->route('attacks-batches.index')
            ->with('success', "Lote desfeito com sucesso. {$deleted} ataques removidos.");
    }
}
