@extends('layouts.app')

@section('title', 'Correlações - Anality')

@section('content')
    <div class="container-fluid py-5">
        <h1 class="mb-4">Correlações Identificadas</h1>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Ataque</th>
                                <th>Notícia</th>
                                <th>Score</th>
                                <th>Tipo</th>
                                <th>Data da Análise</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($correlations as $correlation)
                                <tr>
                                    <td>
                                        <a href="{{ route('attack-detail', $correlation->hackerAttack->id) }}">
                                            {{ Str::limit($correlation->hackerAttack->title, 40) }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($correlation->news->title, 40) }}</td>
                                    <td>
                                        <div class="progress" style="height: 22px;">
                                            <div class="progress-bar {{ $correlation->correlation_score > 70 ? 'bg-success' : 'bg-info' }}"
                                                style="width: {{ $correlation->correlation_score }}%">
                                                {{ round($correlation->correlation_score) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $correlation->correlation_type }}</span>
                                    </td>
                                    <td>{{ $correlation->analysis_date->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Nenhuma correlação encontrada. Execute a análise para começar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($correlations->count())
                    <div class="mt-4">
                        {{ $correlations->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
