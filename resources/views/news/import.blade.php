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
                    <p class="text-muted">Faça upload de um arquivo JSON para para o sistema</p>
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
                                <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Formato do Arquivo JSON</h6>
                                <p class="mb-2">Seu arquivo JSON deve ser um array de objetos com os campos:</p>
                                <ul class="mb-0">
                                    <li><strong>title</strong> - Título da notícia (obrigatório)</li>
                                    <li><strong>summary</strong> - Resumo/Conteúdo da notícia</li>
                                    <li><strong>date</strong> - Data de publicação (ex: 31.dez.2022 às 23h15)</li>
                                </ul>
                                <p class="mt-3 mb-0">
                                    <small>
                                        <strong>Exemplo:</strong>
                                        <code>[{"title":"...","summary":"...","date":"31.dez.2022 às 23h15"}]</code>
                                    </small>
                                </p>
                            </div>

                            <!-- Upload de arquivo -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-text"></i> Arquivo JSON
                                </label>
                                <div id="dropzone" class="rounded-3 p-5 text-center"
                                    style="border: 2px dashed #0d6efd; cursor: pointer; transition: background .15s, border-color .15s;">
                                    <input type="file" id="json_file" name="json_file" accept=".json,.txt"
                                        style="display: none;">
                                    <i class="bi bi-cloud-upload fs-1 text-primary mb-2 d-block"></i>
                                    <p class="mb-1 fw-semibold">Arraste e solte o arquivo aqui</p>
                                    <p class="text-muted small mb-0">ou clique para selecionar</p>
                                    <small class="text-muted">Formatos: JSON, TXT &nbsp;|&nbsp; Máximo: 100MB</small>
                                </div>
                                <div id="fileInfo" class="mt-3 p-3 bg-light rounded d-none">
                                    <small>
                                        <i class="bi bi-file-earmark-check text-success me-1"></i>
                                        <strong id="fileName"></strong>
                                        <span class="text-muted ms-2" id="fileSize"></span>
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
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted" id="progressLabel">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"
                                            aria-hidden="true"></span>
                                        Enviando arquivo...
                                    </small>
                                    <small class="fw-semibold text-primary" id="progressPct">0%</small>
                                </div>
                                <div class="progress rounded-pill" style="height: 18px;">
                                    <div id="progressBar"
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                        role="progressbar" style="width: 0%; transition: width .4s ease;">
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1" id="progressSub"></small>
                            </div>

                            <!-- Resultado inline -->
                            <div id="resultArea"></div>
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
                            <li>Máximo de 100MB por arquivo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedFile = null;
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('json_file');

        // ── Clique para abrir seletor ──────────────────────────────
        dropzone.addEventListener('click', () => fileInput.click());

        // ── Drag & Drop ───────────────────────────────────────────
        ['dragenter', 'dragover'].forEach(evt => dropzone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.background = '#e8f0fe';
            dropzone.style.borderColor = '#0a58ca';
        }));

        dropzone.addEventListener('dragleave', e => {
            if (!dropzone.contains(e.relatedTarget)) {
                dropzone.style.background = '';
                dropzone.style.borderColor = '#0d6efd';
            }
        });

        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.background = '';
            dropzone.style.borderColor = '#0d6efd';
            const file = e.dataTransfer.files[0];
            if (file) setFile(file);
        });

        fileInput.addEventListener('change', function() {
            if (this.files[0]) setFile(this.files[0]);
        });

        function setFile(file) {
            if (file.size > 104857600) {
                showResult('error', 'Arquivo muito grande. O limite é 100MB.');
                return;
            }
            selectedFile = file;
            const mb = file.size / 1048576;
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent =
                mb >= 1 ? mb.toFixed(2) + ' MB' : (file.size / 1024).toFixed(1) + ' KB';
            document.getElementById('fileInfo').classList.remove('d-none');
        }

        // ── Upload via XHR com barra de progresso real ────────────
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const file = selectedFile || (fileInput.files && fileInput.files[0]);
            if (!file) {
                showResult('error', 'Selecione um arquivo JSON.');
                return;
            }

            const formData = new FormData();
            formData.append('json_file', file);

            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressLabel = document.getElementById('progressLabel');
            const progressPct = document.getElementById('progressPct');
            const progressSub = document.getElementById('progressSub');
            const submitBtn = document.getElementById('submitBtn');

            // Resetar e mostrar
            document.getElementById('resultArea').innerHTML = '';
            setProgress(0, 'Enviando arquivo...', '');
            progressContainer.classList.remove('d-none');
            submitBtn.disabled = true;

            let serverTimer = null;
            let serverPct = 80;

            function setProgress(pct, label, sub) {
                progressBar.style.width = pct + '%';
                progressPct.textContent = Math.round(pct) + '%';
                progressLabel.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ${label}`;
                progressSub.textContent = sub;
            }

            function startServerPhase() {
                // Anima de 80% até 99% de forma gradual enquanto o servidor processa
                serverTimer = setInterval(() => {
                    if (serverPct < 99) {
                        serverPct += (99 - serverPct) * 0.06; // desacelera ao se aproximar de 99%
                        setProgress(serverPct, 'Processando no servidor...',
                            'Aguarde, isso pode levar alguns segundos…');
                    }
                }, 200);
            }

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', e => {
                if (e.lengthComputable) {
                    // Fase 1: upload real ocupa 0–80%
                    const uploadPct = (e.loaded / e.total) * 80;
                    const mb = (e.loaded / 1048576).toFixed(1);
                    const total = (e.total / 1048576).toFixed(1);
                    setProgress(uploadPct, 'Enviando arquivo...', `${mb} MB de ${total} MB enviados`);
                }
            });

            xhr.upload.addEventListener('load', () => {
                // Upload concluído → começa fase 2
                setProgress(80, 'Processando no servidor...', 'Upload completo. Aguardando resposta…');
                startServerPhase();
            });

            xhr.addEventListener('load', () => {
                clearInterval(serverTimer);
                submitBtn.disabled = false;

                // Snapa para 100% e exibe resultado após breve pausa
                progressBar.style.transition = 'width .2s ease';
                progressBar.style.width = '100%';
                progressPct.textContent = '100%';
                progressLabel.innerHTML = '<i class="bi bi-check-circle me-1"></i> Concluído!';
                progressSub.textContent = '';

                setTimeout(() => {
                    progressContainer.classList.add('d-none');
                    try {
                        const result = JSON.parse(xhr.responseText);
                        if (xhr.status === 200 && result.success) {
                            showResult('success', result);
                        } else {
                            const msg = result.error ||
                                (result.errors && Object.values(result.errors).flat()[0]) ||
                                result.message ||
                                'Erro ao importar arquivo.';
                            showResult('error', msg);
                        }
                    } catch (_) {
                        showResult('error', 'Resposta inválida do servidor.');
                    }
                }, 600);
            });

            xhr.addEventListener('error', () => {
                clearInterval(serverTimer);
                submitBtn.disabled = false;
                progressContainer.classList.add('d-none');
                showResult('error', 'Falha na comunicação com o servidor.');
            });

            xhr.open('POST', '{{ route('news-import.api') }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(formData);
        });

        function showResult(type, data) {
            const area = document.getElementById('resultArea');
            if (type === 'success') {
                const failed = data.failed ?? 0;
                const total = data.total ?? (data.imported + failed);
                const errsHtml = data.errors && data.errors.length ?
                    `<hr><ul class="small text-danger mb-0">${data.errors.map(e => `<li>${e}</li>`).join('')}</ul>` :
                    '';
                area.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show mt-4">
                        <i class="bi bi-check-circle"></i>
                        <strong>Sucesso!</strong> Importação concluída.
                        <div class="row text-center mt-3">
                            <div class="col-4"><h5 class="text-success mb-0">${data.imported}</h5><small>Importadas</small></div>
                            <div class="col-4"><h5 class="text-danger mb-0">${failed}</h5><small>Falhadas</small></div>
                            <div class="col-4"><h5 class="text-info mb-0">${total}</h5><small>Total</small></div>
                        </div>
                        ${errsHtml}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
            } else {
                area.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show mt-4">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Erro!</strong> ${data}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
            }
        }
    </script>
@endsection
