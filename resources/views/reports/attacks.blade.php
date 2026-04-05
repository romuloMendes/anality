@extends('layouts.app')

@section('title', 'Relatório de Ataques - Anality')

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <p class="page-header-tag">// relatórios</p>
                    <h1 class="page-header-title">Relatório de Ataques por Semana</h1>
                    <p class="page-header-sub">ataques e notícias agrupados em intervalos de 7 dias</p>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('report-attacks-chart') }}" class="btn btn-primary">
                    <i class="bi bi-bar-chart-fill"></i> Ver Gráfico
                </a>
            </div>
        </div>

        {{-- Filtro --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('report-attacks-view') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="from" class="form-label">Data Inicial</label>
                        <input type="text" id="from" name="from" class="form-control" placeholder="dd/mm/aaaa"
                            value="{{ $from->format('d/m/Y') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label">Data Final</label>
                        <input type="text" id="to" name="to" class="form-control" placeholder="dd/mm/aaaa"
                            value="{{ $to->format('d/m/Y') }}">
                    </div>
                    <div class="col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                        <a href="{{ route('export-report-attacks-weekly', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
                            class="btn btn-outline-success">
                            <i class="bi bi-download"></i> Exportar CSV
                        </a>
                        <a href="{{ route('report-attacks-view') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Períodos — {{ $from->format('d/m/Y') }} a {{ $to->format('d/m/Y') }}</h5>
                <span class="badge badge-critical">{{ $rows->count() }} períodos · {{ $rows->sum('attack_count') }}
                    ataques</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:14%">Período</th>
                            <th class="text-center" style="width:10%">Qtd. Ataques</th>
                            <th style="width:38%">Notícias –7 dias <small class="fw-normal text-muted">(antes do
                                    início)</small></th>
                            <th style="width:38%">Notícias +7 dias <small class="fw-normal text-muted">(a partir do
                                    início)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row['name'] }}</strong>
                                    <br>
                                    <small class="text-muted">até {{ $row['end_date']->format('d/m/Y') }}</small>
                                </td>

                                <td class="text-center">
                                    @if ($row['attack_count'] > 0)
                                        <span class="badge badge-critical">{{ $row['attack_count'] }}</span>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                </td>

                                <td>
                                    @forelse ($row['news_minus7'] as $news)
                                        <div class="mb-1 pb-1 border-bottom border-light">
                                            <div class="fw-semibold small">{{ $news->title }}</div>
                                            <div class="text-muted" style="font-size:0.78rem">
                                                {{ $news->published_date->format('d/m/Y') }}
                                                &middot; {{ $news->source_name }}
                                            </div>
                                        </div>
                                    @empty
                                        <em class="text-muted small">Nenhuma notícia</em>
                                    @endforelse
                                </td>

                                <td>
                                    @forelse ($row['news_plus7'] as $news)
                                        <div class="mb-1 pb-1 border-bottom border-light">
                                            <div class="fw-semibold small">{{ $news->title }}</div>
                                            <div class="text-muted" style="font-size:0.78rem">
                                                {{ $news->published_date->format('d/m/Y') }}
                                                &middot; {{ $news->source_name }}
                                            </div>
                                        </div>
                                    @empty
                                        <em class="text-muted small">Nenhuma notícia</em>
                                    @endforelse
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Nenhum dado encontrado para o período selecionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
