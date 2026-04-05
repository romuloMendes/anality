@extends('layouts.app')

@section('title', 'Ataques - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="page-header mb-4">
            <p class="page-header-tag">// monitoramento</p>
            <h1 class="page-header-title">Ataques Hackers</h1>
            <p class="page-header-sub">registro e filtragem de incidentes de segurança</p>
        </div>

        {{-- Filtros --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('attacks') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" placeholder="Buscar..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Severidade</label>
                        <select name="severity" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($severities as $severity)
                                <option value="{{ $severity }}" {{ request('severity') == $severity ? 'selected' : '' }}>
                                    {{ ucfirst($severity) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="">Todos os Tipos</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fonte</label>
                        <select name="source" class="form-select">
                            <option value="">Todas as Fontes</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                    {{ $source }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('attacks') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Severidade</th>
                                <th>Data</th>
                                <th>Fonte</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attacks as $attack)
                                <tr>
                                    <td>{{ Str::limit($attack->title, 50) }}</td>
                                    <td><span class="badge badge-medium">{{ $attack->attack_type }}</span></td>
                                    <td>
                                        @php
                                            $sev = $attack->severity;
                                            $cls = $sev === 'critical' ? 'badge-critical' : ($sev === 'high' ? 'badge-high' : ($sev === 'medium' ? 'badge-medium' : 'badge-low'));
                                        @endphp
                                        <span class="badge {{ $cls }}">{{ ucfirst($sev) }}</span>
                                    </td>
                                    <td>{{ $attack->attack_date->format('d/m/Y') }}</td>
                                    <td>{{ $attack->source_name }}</td>
                                    <td>
                                        <a href="{{ route('attack-detail', $attack->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        Nenhum ataque encontrado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attacks->count())
                    <div class="p-3">
                        {{ $attacks->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="page-footer d-flex justify-content-between mt-2">
            <span>ATAQUES — ANALITY</span>
            <span id="attacks-footer-ts"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('attacks-footer-ts').textContent = 'gerado em ' + new Date().toLocaleString('pt-BR');
    </script>
    @endpush
@endsection
