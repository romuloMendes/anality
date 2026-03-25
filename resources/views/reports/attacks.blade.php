@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Filtro de datas --}}
        <form method="GET" action="{{ route('report-attacks-view') }}" class="mb-4 flex gap-2">
            <input type="text" name="from" value="{{ $from->format('d/m/Y') }}" placeholder="De (dd/mm/aaaa)"
                class="input">
            <input type="text" name="to" value="{{ $to->format('d/m/Y') }}" placeholder="Até (dd/mm/aaaa)"
                class="input">
            <button type="submit" class="btn">Filtrar</button>
            <a href="{{ route('export-report-attacks-weekly', request()->query()) }}" class="btn btn-secondary">Exportar
                CSV</a>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Período (início)</th>
                    <th>Qtd. Ataques</th>
                    <th>Notícias -7 dias</th>
                    <th>Notícias +7 dias</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['name'] }}</strong>
                            <br>
                            <small class="text-muted">
                                até {{ $row['end_date']->format('d/m/Y') }}
                            </small>
                        </td>

                        <td class="text-center">
                            <span class="badge {{ $row['attack_count'] > 0 ? 'badge-danger' : 'badge-secondary' }}">
                                {{ $row['attack_count'] }}
                            </span>
                        </td>

                        <td>
                            @forelse ($row['news_minus7'] as $news)
                                <div class="news-item">
                                    <span>{{ $news->title }}</span>
                                    <small class="text-muted">
                                        {{ $news->published_date->format('d/m/Y') }}
                                        · {{ $news->source_name }}
                                    </small>
                                </div>
                            @empty
                                <em class="text-muted">Nenhuma notícia</em>
                            @endforelse
                        </td>

                        <td>
                            @forelse ($row['news_plus7'] as $news)
                                <div class="news-item">
                                    <span>{{ $news->title }}</span>
                                    <small class="text-muted">
                                        {{ $news->published_date->format('d/m/Y') }}
                                        · {{ $news->source_name }}
                                    </small>
                                </div>
                            @empty
                                <em class="text-muted">Nenhuma notícia</em>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Nenhum dado encontrado para o período selecionado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
