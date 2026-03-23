@extends('layouts.app')

@section('title', 'Timeline de Ataques - Anality')

@section('content')
    <div class="container-fluid py-5">
        <h1 class="mb-4">Timeline de Ataques</h1>

        <div class="card">
            <div class="card-body">
                <div class="timeline">
                    @forelse($attacks as $attack)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker">
                                <span
                                    class="badge bg-{{ $attack->severity == 'critical' ? 'danger' : ($attack->severity == 'high' ? 'warning' : 'success') }}">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </span>
                            </div>
                            <div class="timeline-content ms-4">
                                <h5 class="mb-1">
                                    <a href="{{ route('attack-detail', $attack->id) }}">
                                        {{ $attack->title }}
                                    </a>
                                </h5>
                                <div class="text-muted mb-2">
                                    <small>
                                        <i class="bi bi-calendar-event"></i> {{ $attack->attack_date->format('d/m/Y') }}
                                        |
                                        <i class="bi bi-tag"></i> {{ $attack->attack_type }}
                                        |
                                        <span class="badge bg-secondary">{{ ucfirst($attack->severity) }}</span>
                                    </small>
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
    </div>

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
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 5px;
            z-index: 1;
        }
    </style>
@endsection
