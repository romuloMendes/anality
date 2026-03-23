@extends('layouts.app')

@section('title', 'Ataques - Anality')

@section('content')
    <div class="container-fluid py-5">
        <h1 class="mb-4">Ataques Hackers</h1>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('attacks') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Buscar..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="severity" class="form-select">
                            <option value="">Severidade</option>
                            @foreach ($severities as $severity)
                                <option value="{{ $severity }}"
                                    {{ request('severity') == $severity ? 'selected' : '' }}>
                                    {{ ucfirst($severity) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">Tipo de Ataque</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="source" class="form-select">
                            <option value="">Fonte</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                    {{ $source }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
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

        <!-- Tabela de Ataques -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
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
                                    <td><span class="badge bg-info">{{ $attack->attack_type }}</span></td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $attack->severity == 'critical' ? 'danger' : ($attack->severity == 'high' ? 'warning' : 'success') }}">
                                            {{ ucfirst($attack->severity) }}
                                        </span>
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
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Nenhum ataque encontrado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attacks->count())
                    <div class="mt-4">
                        {{ $attacks->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
