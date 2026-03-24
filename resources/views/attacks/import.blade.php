@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="mb-5">
                    <h1 class="h2 mb-2"><i class="bi bi-upload"></i> Importar Ataques</h1>
                    <p class="text-muted">Faça upload de um arquivo JSON de ataques para importar para o sistema</p>
                </div>

                @if ($message = session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <strong>Sucesso!</strong> {{ $message }}
                        @if (session('import_result'))
                            <div class="mt-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-success">
                                            <h5>{{ session('import_result.imported') }}</h5><small>Importados</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-danger">
                                            <h5>{{ session('import_result.failed') }}</h5><small>Falhados</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info">
                                            <h5>{{ session('import_result.total') }}</h5><small>Total</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <strong>Erro!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <form action="{{ route('attacks-import.process') }}" method="POST" enctype="multipart/form-data"
                            id="importForm">
                            @csrf

                            <div class="alert alert-info mb-4">
                                <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Formato do Arquivo JSON</h6>
                                <p class="mb-2">Seu arquivo JSON deve ser um array de objetos com as chaves:</p>
                                <ul class="mb-0">
                                    <li><strong>title</strong> - Título do ataque (obrigatório)</li>
                                    <li><strong>summary</strong> - Descrição do ataque</li>
                                    <li><strong>date</strong> - Data do ataque (ex: 31.dez.2022 às 23h15)</li>
                                </ul>
                                <p class="mt-3 mb-0"><small><strong>Exemplo:</strong>
                                        <code>[{"title":"...","summary":"...","date":"31.dez.2022 às 23h15"}]</code></small>
                                </p>
                            </div>

                            <div class="mb-4">
                                <label for="json_file" class="form-label fw-semibold"><i
                                        class="bi bi-file-earmark-text"></i> Selecione o arquivo JSON</label>
                                <input type="file"
                                    class="form-control form-control-lg @error('json_file') is-invalid @enderror"
                                    id="json_file" name="json_file" accept=".json,.txt" required
                                    onchange="updateFileName(this)">
                                @error('json_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-between">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn"><i
                                        class="bi bi-upload"></i> Importar Ataques</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg"><i
                                        class="bi bi-arrow-left"></i> Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(2) + ' KB';

                document.getElementById('fileName').textContent = fileName;
                document.getElementById('fileSize').textContent = fileSize;
                document.getElementById('fileInfo').classList.remove('d-none');
            }
        }

        // Mostrar/ocultar barra de progresso
        document.getElementById('importForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('csv_file');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                alert('Por favor, selecione um arquivo');
                return;
            }

            // Mostrar barra de progresso
            document.getElementById('progressContainer').classList.remove('d-none');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
@endsection
