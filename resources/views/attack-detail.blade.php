@extends('layouts.app')

@section('title', 'Detalhes do Ataque - Anality')

@section('content')
    <div class="container-fluid py-4">

        <a href="{{ route('attacks') }}" class="btn btn-secondary mb-4">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        @php
            $sev = $attack->severity;
            $sevCls = $sev === 'critical' ? 'badge-critical' : ($sev === 'high' ? 'badge-high' : ($sev === 'medium' ? 'badge-medium' : 'badge-low'));
        @endphp

        <div class="page-header mb-4">
            <p class="page-header-tag">// detalhe do incidente</p>
            <h1 class="page-header-title">{{ $attack->title }}</h1>
            <p class="page-header-sub">
                <span class="badge {{ $sevCls }} me-2">{{ ucfirst($sev) }}</span>
                {{ $attack->attack_type }}
            </p>
        </div>

        <div class="row g-3">
            {{-- Detalhes --}}
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Informações do Ataque</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <p class="form-label">Tipo de Ataque</p>
                                <p style="color: var(--text); font-size: 14px;">{{ $attack->attack_type }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="form-label">Severidade</p>
                                <p><span class="badge {{ $sevCls }}">{{ ucfirst($sev) }}</span></p>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <p class="form-label">Data do Ataque</p>
                                <p style="color: var(--text); font-size: 14px; font-family: 'Share Tech Mono', monospace;">{{ $attack->attack_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="form-label">Entidade Afetada</p>
                                <p style="color: var(--text); font-size: 14px;">{{ $attack->affected_entity ?? 'Não especificada' }}</p>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <p class="form-label">Fonte</p>
                                <p style="color: var(--text); font-size: 14px;">{{ $attack->source_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="form-label">Registrado em</p>
                                <p style="color: var(--text); font-size: 14px; font-family: 'Share Tech Mono', monospace;">{{ $attack->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if ($attack->description)
                            <hr>
                            <p class="form-label">Descrição</p>
                            <p style="color: var(--text); font-size: 13px; font-weight: 300; line-height: 1.6;">{{ $attack->description }}</p>
                        @endif

                        @if ($attack->tags)
                            <hr>
                            <p class="form-label">Tags / Palavras-chave</p>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($attack->tags as $tag)
                                    <span class="badge bg-secondary">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($attack->source_url)
                            <hr>
                            <a href="{{ $attack->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-link-45deg"></i> Ver Fonte Original
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notícias Correlacionadas --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Notícias Correlacionadas ({{ $relatedNews->count() }})</h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                        @forelse($relatedNews as $correlation)
                            <div class="p-3" style="border-bottom: 1px solid var(--border);">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span style="font-size: 13px; color: var(--text); font-weight: 400;">{{ Str::limit($correlation->news->title, 35) }}</span>
                                    <span class="badge badge-low ms-2">{{ round($correlation->correlation_score) }}%</span>
                                </div>
                                <div style="font-family: 'Share Tech Mono', monospace; font-size: 10px; color: var(--muted); letter-spacing: 0.05em;">
                                    {{ $correlation->news->source_name }}
                                    &nbsp;|&nbsp; {{ ucfirst($correlation->correlation_type) }}
                                </div>
                                @if ($correlation->analysis_reason)
                                    <p style="font-size: 11px; color: var(--accent); margin-top: 4px; margin-bottom: 0; font-family: 'Share Tech Mono', monospace;">
                                        // {{ $correlation->analysis_reason }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">
                                Nenhuma notícia correlacionada encontrada
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="page-footer d-flex justify-content-between mt-4">
            <span>DETALHE DO ATAQUE — ANALITY</span>
            <span id="detail-footer-ts"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('detail-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
    </script>
    @endpush
@endsection
