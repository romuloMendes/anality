@extends('layouts.app')

@section('title', 'Histórico de Uploads - Ataques')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                <div class="page-header mb-4">
                    <p class="page-header-tag">// admin / ataques / uploads</p>
                    <h1 class="page-header-title">Histórico de Uploads</h1>
                    <p class="page-header-sub">gerencie e desfaça importações de ataques</p>
                </div>

                @if ($message = session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <strong>Sucesso!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <strong>Erro!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span style="font-size:12px;color:var(--muted);font-family:'Share Tech Mono',monospace;">
                        {{ $batches->total() }} lote(s) encontrado(s)
                    </span>
                    <a href="{{ route('attacks-import.form') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-upload"></i> Novo Upload
                    </a>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        @if ($batches->isEmpty())
                            <div class="text-center py-5" style="color:var(--muted);">
                                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0" style="font-size:13px;">Nenhum upload realizado ainda.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size:13px;">
                                    <thead>
                                        <tr
                                            style="color:var(--muted);font-family:'Share Tech Mono',monospace;font-size:11px;border-bottom:1px solid var(--border);">
                                            <th class="px-4 py-3">#</th>
                                            <th class="px-3 py-3">Arquivo</th>
                                            <th class="px-3 py-3 text-center">Tamanho</th>
                                            <th class="px-3 py-3 text-center">Importados</th>
                                            <th class="px-3 py-3 text-center">Falhados</th>
                                            <th class="px-3 py-3 text-center">Ataques ativos</th>
                                            <th class="px-3 py-3 text-center">Status</th>
                                            <th class="px-3 py-3">Data</th>
                                            <th class="px-3 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($batches as $batch)
                                            <tr style="border-bottom:1px solid var(--border);">
                                                <td class="px-4 py-3"
                                                    style="color:var(--muted);font-family:'Share Tech Mono',monospace;">
                                                    {{ $batch->id }}
                                                </td>
                                                <td class="px-3 py-3">
                                                    <span style="font-family:'Share Tech Mono',monospace;font-size:12px;">
                                                        <i class="bi bi-file-earmark-code me-1"
                                                            style="color:var(--accent);"></i>
                                                        {{ $batch->filename }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-3 text-center"
                                                    style="color:var(--muted);font-family:'Share Tech Mono',monospace;font-size:11px;">
                                                    @if ($batch->file_size)
                                                        {{ $batch->file_size >= 1048576
                                                            ? number_format($batch->file_size / 1048576, 2) . ' MB'
                                                            : number_format($batch->file_size / 1024, 1) . ' KB' }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <span
                                                        style="color:var(--ok);font-weight:600;">{{ $batch->imported_count }}</span>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <span
                                                        style="color:{{ $batch->failed_count > 0 ? 'var(--danger)' : 'var(--muted)' }};">
                                                        {{ $batch->failed_count }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    @if ($batch->isReverted())
                                                        <span style="color:var(--muted);">—</span>
                                                    @else
                                                        <span
                                                            style="color:var(--accent);font-weight:600;">{{ $batch->attacks_count }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    @if ($batch->isReverted())
                                                        <span class="badge"
                                                            style="background:rgba(255,82,82,0.15);color:#ff5252;font-family:'Share Tech Mono',monospace;font-size:10px;">
                                                            <i class="bi bi-arrow-counterclockwise"></i> desfeito
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                            style="background:rgba(0,229,255,0.1);color:var(--accent);font-family:'Share Tech Mono',monospace;font-size:10px;">
                                                            <i class="bi bi-check-circle"></i> ativo
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3"
                                                    style="color:var(--muted);font-family:'Share Tech Mono',monospace;font-size:11px;">
                                                    {{ $batch->created_at->format('d/m/Y H:i') }}
                                                    @if ($batch->isReverted())
                                                        <br><small style="color:#ff5252;">desfeito
                                                            {{ $batch->reverted_at->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3 text-end">
                                                    @if (!$batch->isReverted())
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            style="font-size:11px;font-family:'Share Tech Mono',monospace;"
                                                            data-bs-toggle="modal" data-bs-target="#confirmRevert"
                                                            data-batch-id="{{ $batch->id }}"
                                                            data-batch-filename="{{ $batch->filename }}"
                                                            data-batch-count="{{ $batch->attacks_count }}">
                                                            <i class="bi bi-arrow-counterclockwise"></i> Desfazer
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($batches->hasPages())
                                <div class="px-4 py-3 border-top" style="border-color:var(--border)!important;">
                                    {{ $batches->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal de confirmação --}}
    <div class="modal fade" id="confirmRevert" tabindex="-1" aria-labelledby="confirmRevertLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:var(--surface);border:1px solid var(--border);">
                <div class="modal-header" style="border-color:var(--border);">
                    <h5 class="modal-title" id="confirmRevertLabel"
                        style="font-family:'Share Tech Mono',monospace;font-size:14px;color:var(--accent);">
                        <i class="bi bi-exclamation-triangle" style="color:var(--danger);"></i> Confirmar Desfazer Upload
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="font-size:13px;">
                    <p>Você está prestes a desfazer o upload do arquivo:</p>
                    <p style="font-family:'Share Tech Mono',monospace;color:var(--accent);" id="revertFilename"></p>
                    <p class="mb-0">
                        Isso irá <strong style="color:var(--danger);">excluir permanentemente</strong>
                        <strong id="revertCount"></strong> ataque(s) importados por este lote.
                    </p>
                    <div class="mt-3 p-2 rounded"
                        style="background:rgba(255,82,82,0.08);border:1px solid rgba(255,82,82,0.2);font-size:11px;color:var(--muted);">
                        <i class="bi bi-info-circle"></i> Ataques vinculados a análises de correlação não serão afetados
                        pelas correlações, mas os próprios dados do ataque serão removidos.
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <form id="revertForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-arrow-counterclockwise"></i> Sim, desfazer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const confirmModal = document.getElementById('confirmRevert');
            confirmModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const batchId = btn.dataset.batchId;
                const filename = btn.dataset.batchFilename;
                const count = btn.dataset.batchCount;

                document.getElementById('revertFilename').textContent = filename;
                document.getElementById('revertCount').textContent = count;
                document.getElementById('revertForm').action =
                    '/admin/attacks/batches/' + batchId;
            });
        </script>
    @endpush
@endsection
