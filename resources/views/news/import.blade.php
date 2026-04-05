@extends('layouts.app')

@section('title', 'Importar Notícias - Anality')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">

                <div class="page-header mb-4">
                    <p class="page-header-tag">// admin / importação</p>
                    <h1 class="page-header-title">Importar Notícias</h1>
                    <p class="page-header-sub">upload de arquivo JSON para ingestão de notícias</p>
                </div>

                @if ($message = session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <strong>Sucesso!</strong> {{ $message }}
                        @if (session('import_result'))
                            <div class="row text-center mt-3">
                                <div class="col-4">
                                    <div style="color: var(--ok);">
                                        <h5>{{ session('import_result.imported') }}</h5>
                                        <small>Importadas</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div style="color: var(--danger);">
                                        <h5>{{ session('import_result.failed') }}</h5>
                                        <small>Falhadas</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div style="color: var(--accent);">
                                        <h5>{{ session('import_result.total') }}</h5>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                            @if (!empty(session('import_result.errors')))
                                <hr>
                                <ul class="small mb-0" style="color: var(--danger);">
                                    @foreach (session('import_result.errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Erro!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('news-import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf

                            <div class="alert alert-info mb-4">
                                <p class="alert-heading mb-2"><i class="bi bi-info-circle"></i> Formato do Arquivo JSON</p>
                                <p class="mb-2" style="font-size: 12px;">Array de objetos com os campos:</p>
                                <ul class="mb-2" style="font-size: 12px;">
                                    <li><strong>title</strong> — Título da notícia (obrigatório)</li>
                                    <li><strong>summary</strong> — Resumo / conteúdo</li>
                                    <li><strong>date</strong> — Data (ex: <code>31.dez.2022 às 23h15</code>)</li>
                                </ul>
                                <small><strong>Exemplo:</strong> <code>[{"title":"...","summary":"...","date":"31.dez.2022 às 23h15"}]</code></small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Arquivo JSON</label>
                                <div id="dropzone" class="rounded p-5 text-center" style="cursor: pointer;">
                                    <input type="file" id="json_file" name="json_file" accept=".json,.txt" style="display: none;">
                                    <i class="bi bi-cloud-upload fs-1 d-block mb-2" style="color: var(--accent);"></i>
                                    <p class="mb-1" style="color: var(--text); font-size: 13px; font-weight: 400;">Arraste e solte o arquivo aqui</p>
                                    <p class="mb-0" style="color: var(--muted); font-size: 11px; font-family: 'Share Tech Mono', monospace;">ou clique para selecionar &nbsp;|&nbsp; JSON, TXT &nbsp;|&nbsp; máx. 100 MB</p>
                                </div>
                                <div id="fileInfo" class="mt-3 p-3 rounded d-none" style="background: rgba(0,229,255,0.05); border: 1px solid var(--border);">
                                    <small style="font-family: 'Share Tech Mono', monospace; font-size: 11px; color: var(--text);">
                                        <i class="bi bi-file-earmark-check me-1" style="color: var(--ok);"></i>
                                        <strong id="fileName"></strong>
                                        <span class="ms-2" style="color: var(--muted);" id="fileSize"></span>
                                    </small>
                                </div>
                            </div>

                            <div class="accordion mb-4" id="advancedOptions">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#advancedContent">
                                            <i class="bi bi-sliders me-2"></i> Opções Avançadas
                                        </button>
                                    </h2>
                                    <div id="advancedContent" class="accordion-collapse collapse" data-bs-parent="#advancedOptions">
                                        <div class="accordion-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="skipDuplicates" checked>
                                                <label class="form-check-label" for="skipDuplicates">Pular notícias duplicadas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="validateData" checked>
                                                <label class="form-check-label" for="validateData">Validar dados antes de importar</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-upload"></i> Importar Notícias
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Voltar
                                </a>
                            </div>

                            <div id="progressContainer" class="mt-4 d-none">
                                <div class="d-flex justify-content-between mb-1">
                                    <small id="progressLabel" style="font-family:'Share Tech Mono',monospace;font-size:11px;color:var(--muted);"></small>
                                    <small id="progressPct" style="font-family:'Share Tech Mono',monospace;font-size:11px;color:var(--accent);">0%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div id="progressBar" class="progress-bar progress-bar-animated" role="progressbar" style="width: 0%; transition: width .4s ease;"></div>
                                </div>
                                <small id="progressSub" style="font-family:'Share Tech Mono',monospace;font-size:10px;color:var(--muted);display:block;margin-top:4px;"></small>
                            </div>

                            <div id="resultArea"></div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <p class="form-label mb-2"><i class="bi bi-lightbulb"></i> Dicas úteis</p>
                        <ul class="mb-0" style="font-size: 12px; color: var(--text); font-weight: 300;">
                            <li>O arquivo deve estar em codificação UTF-8</li>
                            <li>Certifique-se de que as datas seguem o formato: <code>DD.mês.YYYY às HHhMM</code></li>
                            <li>Notícias duplicadas serão ignoradas automaticamente</li>
                            <li>Máximo de 100 MB por arquivo</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let selectedFile = null;
        const dropzone  = document.getElementById('dropzone');
        const fileInput = document.getElementById('json_file');

        dropzone.addEventListener('click', () => fileInput.click());

        ['dragenter', 'dragover'].forEach(evt => dropzone.addEventListener(evt, e => {
            e.preventDefault(); e.stopPropagation();
            dropzone.style.borderColor = 'var(--accent)';
            dropzone.style.background  = 'rgba(0,229,255,0.06)';
        }));

        dropzone.addEventListener('dragleave', e => {
            if (!dropzone.contains(e.relatedTarget)) {
                dropzone.style.borderColor = '';
                dropzone.style.background  = '';
            }
        });

        dropzone.addEventListener('drop', e => {
            e.preventDefault(); e.stopPropagation();
            dropzone.style.borderColor = '';
            dropzone.style.background  = '';
            if (e.dataTransfer.files[0]) setFile(e.dataTransfer.files[0]);
        });

        fileInput.addEventListener('change', function() { if (this.files[0]) setFile(this.files[0]); });

        function setFile(file) {
            if (file.size > 104857600) { showResult('error', 'Arquivo muito grande. O limite é 100MB.'); return; }
            selectedFile = file;
            const mb = file.size / 1048576;
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = mb >= 1 ? mb.toFixed(2) + ' MB' : (file.size/1024).toFixed(1) + ' KB';
            document.getElementById('fileInfo').classList.remove('d-none');
        }

        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const file = selectedFile || fileInput.files[0];
            if (!file) { showResult('error', 'Selecione um arquivo JSON.'); return; }

            const formData = new FormData();
            formData.append('json_file', file);

            const progressContainer = document.getElementById('progressContainer');
            const progressBar       = document.getElementById('progressBar');
            const progressLabel     = document.getElementById('progressLabel');
            const progressPct       = document.getElementById('progressPct');
            const progressSub       = document.getElementById('progressSub');
            const submitBtn         = document.getElementById('submitBtn');

            document.getElementById('resultArea').innerHTML = '';
            setProgress(0, 'Enviando arquivo...', '');
            progressContainer.classList.remove('d-none');
            submitBtn.disabled = true;

            let serverTimer = null, serverPct = 80;

            function setProgress(pct, label, sub) {
                progressBar.style.width = pct + '%';
                progressPct.textContent = Math.round(pct) + '%';
                progressLabel.textContent = label;
                progressSub.textContent = sub;
            }

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', e => {
                if (e.lengthComputable) {
                    const uploadPct = (e.loaded / e.total) * 80;
                    setProgress(uploadPct, 'Enviando arquivo...', `${(e.loaded/1048576).toFixed(1)} MB de ${(e.total/1048576).toFixed(1)} MB`);
                }
            });

            xhr.upload.addEventListener('load', () => {
                setProgress(80, 'Processando no servidor...', 'Aguarde...');
                serverTimer = setInterval(() => {
                    if (serverPct < 99) { serverPct += (99 - serverPct) * 0.06; setProgress(serverPct, 'Processando...', ''); }
                }, 200);
            });

            xhr.addEventListener('load', () => {
                clearInterval(serverTimer);
                submitBtn.disabled = false;
                progressBar.style.width = '100%';
                progressPct.textContent = '100%';
                progressLabel.textContent = 'Concluído!';
                setTimeout(() => {
                    progressContainer.classList.add('d-none');
                    try {
                        const result = JSON.parse(xhr.responseText);
                        if (xhr.status === 200 && result.success) showResult('success', result);
                        else showResult('error', result.error || result.message || 'Erro ao importar.');
                    } catch (_) { showResult('error', 'Resposta inválida do servidor.'); }
                }, 600);
            });

            xhr.addEventListener('error', () => {
                clearInterval(serverTimer); submitBtn.disabled = false;
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
                const total  = data.total  ?? (data.imported + failed);
                const errs   = data.errors?.length ? `<hr><ul class="small mb-0">${data.errors.map(e=>`<li>${e}</li>`).join('')}</ul>` : '';
                area.innerHTML = `<div class="alert alert-success alert-dismissible fade show mt-4">
                    <i class="bi bi-check-circle"></i> <strong>Sucesso!</strong>
                    <div class="row text-center mt-3">
                        <div class="col-4"><h5 style="color:var(--ok)">${data.imported}</h5><small>Importadas</small></div>
                        <div class="col-4"><h5 style="color:var(--danger)">${failed}</h5><small>Falhadas</small></div>
                        <div class="col-4"><h5 style="color:var(--accent)">${total}</h5><small>Total</small></div>
                    </div>${errs}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            } else {
                area.innerHTML = `<div class="alert alert-danger alert-dismissible fade show mt-4">
                    <i class="bi bi-exclamation-circle"></i> <strong>Erro!</strong> ${data}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            }
        }
    </script>
    @endpush
@endsection
