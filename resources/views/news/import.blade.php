@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Cabeçalho -->
                <div class="mb-5">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-upload"></i> Importar Notícias
                    </h1>
                    <p class="text-muted">Faça upload de um arquivo CSV para importar notícias para o sistema</p>
                </div>

                <!-- Alertas de sucesso/erro -->
                @if ($message = session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <strong>Sucesso!</strong> {{ $message }}
                        @if (session('import_result'))
                            <div class="mt-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-success">
                                            <h5>{{ session('import_result.imported') }}</h5>
                                            <small>Importadas</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-danger">
                                            <h5>{{ session('import_result.failed') }}</h5>
                                            <small>Falhadas</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info">
                                            <h5>{{ session('import_result.total') }}</h5>
                                            <small>Total</small>
                                        </div>
                                    </div>
                                </div>

                                @if (!empty(session('import_result.errors')))
                                    <hr>
                                    <h6 class="text-danger mt-3">Erros encontrados:</h6>
                                    <div style="max-height: 300px; overflow-y: auto;">
                                        <ul class="small text-danger">
                                            @foreach (session('import_result.errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Erro!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif

                <!-- Card Principal -->
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <form action="{{ route('news-import.process') }}" method="POST" enctype="multipart/form-data"
                            id="importForm">
                            @csrf

                            <!-- Informações sobre formato -->
                            <div class="alert alert-info mb-4">
                                <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Formato do Arquivo CSV</h6>
                                <p class="mb-2">Seu arquivo CSV deve ter as seguintes colunas:</p>
                                <ul class="mb-0">
                                    <li><strong>title</strong> - Título da notícia (obrigatório)</li>
                                    <li><strong>summary</strong> - Resumo/Conteúdo da notícia</li>
                                    <li><strong>date</strong> - Data de publicação (ex: 31.dez.2022 às 23h15)</li>
                                </ul>
                                <p class="mt-3 mb-0">
                                    <small>
                                        <strong>Exemplo:</strong>
                                        <code>title;summary;date</code>
                                    </small>
                                </p>
                            </div>

                            <!-- Upload de arquivo -->
                            <div class="mb-4">
                                <label for="csv_file" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-csv"></i> Selecione o arquivo CSV
                                </label>
                                <div class="position-relative">
                                    <input type="file"
                                        class="form-control form-control-lg @error('csv_file') is-invalid @enderror"
                                        id="csv_file" name="csv_file" accept=".csv,.txt" required
                                        onchange="updateFileName(this)">
                                    <small class="form-text text-muted d-block mt-2">
                                        Formatos aceitos: CSV, TXT | Tamanho máximo: 10MB
                                    </small>
                                </div>
                                @error('csv_file')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="fileInfo" class="mt-3 p-3 bg-light rounded d-none">
                                    <small>
                                        <strong>Arquivo selecionado:</strong> <span id="fileName"></span>
                                        <br>
                                        <strong>Tamanho:</strong> <span id="fileSize"></span>
                                    </small>
                                </div>
                            </div>

                            <!-- Opções avançadas -->
                            <div class="accordion mb-4" id="advancedOptions">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#advancedContent">
                                            <i class="bi bi-sliders"></i> Opções Avançadas
                                        </button>
                                    </h2>
                                    <div id="advancedContent" class="accordion-collapse collapse"
                                        data-bs-parent="#advancedOptions">
                                        <div class="accordion-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="skipDuplicates" checked>
                                                <label class="form-check-label" for="skipDuplicates">
                                                    Pular notícias duplicadas
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="validateData" checked>
                                                <label class="form-check-label" for="validateData">
                                                    Validar dados antes de importar
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-between">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="bi bi-upload"></i> Importar Notícias
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left"></i> Voltar
                                </a>
                            </div>

                            <!-- Barra de progresso (oculta inicialmente) -->
                            <div id="progressContainer" class="mt-4 d-none">
                                <div class="progress" style="height: 25px;">
                                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar" style="width: 0%">
                                        <span id="progressText">Processando...</span>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 small">Por favor, aguarde enquanto o arquivo está sendo
                                    processado...</p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card com informações adicionais -->
                <div class="card mt-4 bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="bi bi-lightbulb"></i> Dicas úteis</h6>
                        <ul class="mb-0 small">
                            <li>O arquivo deve estar em codificação UTF-8</li>
                            <li>Use ponto e vírgula (;) como separador de colunas</li>
                            <li>Certifique-se de que as datas seguem o formato: <code>DD.mês.YYYY às HHhMM</code></li>
                            <li>Notícias duplicadas serão ignoradas automaticamente</li>
                            <li>Máximo de 10MB por arquivo</li>
                        </ul>
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
