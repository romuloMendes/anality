@extends('layouts.app')

@section('title', 'Timeline de Ataques - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="page-header mb-4">
            <p class="page-header-tag">// cronologia</p>
            <h1 class="page-header-title">Timeline de Ataques</h1>
            <p class="page-header-sub">sequência temporal dos incidentes registrados</p>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="timeline">
                    @forelse($attacks as $attack)
                        @php
                            $sev = $attack->severity;
                            $cls = $sev === 'critical' ? 'badge-critical' : ($sev === 'high' ? 'badge-high' : ($sev === 'medium' ? 'badge-medium' : 'badge-low'));
                        @endphp
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker">
                                <span class="badge {{ $cls }}">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </span>
                            </div>
                            <div class="timeline-content ms-4">
                                <h5 class="mb-1" style="font-family: 'Barlow', sans-serif; font-size: 15px; font-weight: 600; color: #e8f4ff;">
                                    <a href="{{ route('attack-detail', $attack->id) }}">
                                        {{ $attack->title }}
                                    </a>
                                </h5>
                                <div class="mb-1" style="font-family: 'Share Tech Mono', monospace; font-size: 11px; color: var(--muted);">
                                    <i class="bi bi-calendar-event"></i> {{ $attack->attack_date->format('d/m/Y') }}
                                    &nbsp;|&nbsp;
                                    <i class="bi bi-tag"></i> {{ $attack->attack_type }}
                                    &nbsp;|&nbsp;
                                    <span class="badge {{ $cls }}">{{ ucfirst($attack->severity) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-5">
                            Nenhum ataque registrado
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="page-footer d-flex justify-content-between mt-2">
            <span>TIMELINE — ANALITY</span>
            <span id="tl-footer-ts"></span>
        </div>
    </div>

    @push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 10px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 25px;
            top: 0;
            bottom: 0;
            width: 1px;
            background: var(--border);
        }
        .timeline-item { position: relative; }
        .timeline-marker {
            position: absolute;
            left: 0;
            top: 4px;
            z-index: 1;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.getElementById('tl-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
    </script>
    @endpush
@endsection
