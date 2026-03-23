<?php

namespace App\Http\Controllers;

use App\Models\HackerAttack;
use App\Models\News;
use App\Models\CorrelationAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe dashboard principal com estatísticas
     */
    public function index()
    {
        $stats = [
            'total_attacks' => HackerAttack::count(),
            'total_news' => News::count(),
            'total_correlations' => CorrelationAnalysis::count(),
            'critical_attacks' => HackerAttack::where('severity', 'critical')->count(),
            'high_correlations' => CorrelationAnalysis::where('correlation_score', '>', 70)->count(),
        ];

        $recentAttacks = HackerAttack::latest('attack_date')->limit(10)->get();
        $recentNews = News::latest('published_date')->limit(10)->get();
        $topCorrelations = CorrelationAnalysis::orderByDesc('correlation_score')->limit(5)->with('hackerAttack', 'news')->get();

        return view('dashboard', compact('stats', 'recentAttacks', 'recentNews', 'topCorrelations'));
    }

    /**
     * Exibe estatísticas detalhadas
     */
    public function statistics()
    {
        $attacksByType = HackerAttack::selectRaw('attack_type, COUNT(*) as count')
            ->groupBy('attack_type')
            ->get();

        $attacksBySeverity = HackerAttack::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get();

        $correlationsByType = CorrelationAnalysis::selectRaw('correlation_type, COUNT(*) as count')
            ->groupBy('correlation_type')
            ->get();

        $averageCorrelationScore = CorrelationAnalysis::avg('correlation_score');

        return view('statistics', compact(
            'attacksByType',
            'attacksBySeverity',
            'correlationsByType',
            'averageCorrelationScore'
        ));
    }

    /**
     * Exibe detalhes de correlações
    */
    public function correlations()
    {
        $correlations = CorrelationAnalysis::with('hackerAttack', 'news')
            ->orderByDesc('correlation_score')
            ->paginate(20);

        return view('correlations', compact('correlations'));
    }

    /**
     * Filtra e exibe ataques específicos
    */
    public function attacks(Request $request)
    {
        $query = HackerAttack::query();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('type')) {
            $query->where('attack_type', $request->type);
        }

        if ($request->filled('source')) {
            $query->where('source_name', $request->source);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search)
                  ->orWhere('affected_entity', 'like', $search);
            });
        }

        $attacks = $query->orderBy('attack_date', 'desc')->paginate(20);
        $severities = HackerAttack::distinct('severity')->pluck('severity');
        $types = HackerAttack::distinct('attack_type')->pluck('attack_type');
        $sources = HackerAttack::distinct('source_name')->pluck('source_name');

        return view('attacks', compact('attacks', 'severities', 'types', 'sources'));
    }

    /**
     * Exibe detalhes de um ataque específico
    */
    public function attackDetail($id)
    {
        $attack = HackerAttack::findOrFail($id);
        $relatedNews = CorrelationAnalysis::where('hacker_attack_id', $id)
            ->with('news')
            ->orderByDesc('correlation_score')
            ->get();

        return view('attack-detail', compact('attack', 'relatedNews'));
    }

    /**
     * Timeline visual de ataques
    */
    public function timeline()
    {
        $attacks = HackerAttack::orderBy('attack_date', 'desc')
            ->select('id', 'title', 'attack_date', 'severity', 'attack_type')
            ->limit(50)
            ->get();

        return view('timeline', compact('attacks'));
    }
}
