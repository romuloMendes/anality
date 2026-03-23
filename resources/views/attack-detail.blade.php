@extends('layouts.app')

@section('title', 'Detalhes do Ataque - Anality')

@section('content')
    <div class="container-fluid py-5">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('attacks') }}" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Detalhes do Ataque -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h4>{{ $attack->title }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Tipo de Ataque</h6>
                                <p><strong>{{ $attack->attack_type }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Severidade</h6>
                                <p>
                                    <span
                                        class="badge bg-{{ $attack->severity == 'critical' ? 'danger' : ($attack->severity == 'high' ? 'warning' : 'success') }}">
                                        {{ ucfirst($attack->severity) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Data do Ataque</h6>
                                <p><strong>{{ $attack->attack_date->format('d/m/Y') }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Entidade Afetada</h6>
                                <p><strong>{{ $attack->affected_entity ?? 'Não especificada' }}</strong></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Fonte</h6>
                                <p><strong>{{ $attack->source_name }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Registrado em</h6>
                                <p><strong>{{ $attack->created_at->format('d/m/Y H:i') }}</strong></p>
                            </div>
                        </div>

                        @if ($attack->description)
                            <hr>
                            <h6 class="text-muted">Descrição</h6>
                            <p>{{ $attack->description }}</p>
                        @endif

                        @if ($attack->tags)
                            <hr>
                            <h6 class="text-muted">Tags/Palavras-chave</h6>
                            <div>
                                @foreach ($attack->tags as $tag)
                                    <span class="badge bg-secondary">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($attack->source_url)
                            <hr>
                            <a href="{{ $attack->source_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-link-45deg"></i> Ver Fonte Original
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notícias Correlacionadas -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Notícias Correlacionadas ({{ $relatedNews->count() }})</h5>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        @forelse($relatedNews as $correlation)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ Str::limit($correlation->news->title, 35) }}</h6>
                                    <span class="badge bg-success">
                                        {{ round($correlation->correlation_score) }}%
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ $correlation->news->source_name }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    Tipo: <strong>{{ ucfirst($correlation->correlation_type) }}</strong>
                                </small>
                                <br>
                                @if ($correlation->analysis_reason)
                                    <small class="text-info mt-2 d-block">
                                        💡 {{ $correlation->analysis_reason }}
                                    </small>
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
    </div>
@endsection
