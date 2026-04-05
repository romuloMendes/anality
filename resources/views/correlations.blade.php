@extends('layouts.app')

@section('title', 'Correlações - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="page-header mb-4">
            <p class="page-header-tag">// análise</p>
            <h1 class="page-header-title">Correlações Identificadas</h1>
            <p class="page-header-sub">relações entre ataques e notícias detectadas pelo sistema</p>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
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
                                        <div class="progress" style="height: 18px; width: 120px;">
                                            <div class="progress-bar {{ $correlation->correlation_score > 70 ? '' : '' }}"
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
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Nenhuma correlação encontrada. Execute a análise para começar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($correlations->count())
                    <div class="p-3">
                        {{ $correlations->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="page-footer d-flex justify-content-between mt-2">
            <span>CORRELAÇÕES — ANALITY</span>
            <span id="corr-footer-ts"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('corr-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
    </script>
    @endpush
@endsection
