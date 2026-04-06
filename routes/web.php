<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AttackImportController;
use App\Http\Controllers\AttackImportBatchController;
use App\Http\Controllers\AttackReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsImportController;
use App\Http\Controllers\NewsRelevanceChartController;
use Illuminate\Support\Facades\Route;

// Dashboard e visualizações
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
Route::get('/correlations', [DashboardController::class, 'correlations'])->name('correlations');
Route::get('/attacks', [DashboardController::class, 'attacks'])->name('attacks');
Route::get('/attacks/{id}', [DashboardController::class, 'attackDetail'])->name('attack-detail');
Route::get('/timeline', [DashboardController::class, 'timeline'])->name('timeline');
Route::get('/charts/attacks/weekly', [DashboardController::class, 'weeklyAttacksChart'])->name('charts.attacks-weekly');
Route::get('/api/charts/attacks/weekly', [DashboardController::class, 'weeklyAttacksChartData'])->name('api.charts.attacks-weekly');
Route::get('/report/attacks', [AttackReportController::class, 'view'])->name('report-attacks-view');
Route::get('/report/attacks/chart', [AttackReportController::class, 'chart'])->name('report-attacks-chart');

// APIs para análise e scraping
Route::post('/api/scrape/attacks', [AnalysisController::class, 'scrapeAttacks'])->name('scrape-attacks');
Route::post('/api/scrape/news', [AnalysisController::class, 'scrapeNews'])->name('scrape-news');
Route::post('/api/analyze/correlations', [AnalysisController::class, 'runCorrelationAnalysis'])->name('analyze-correlations');
Route::post('/api/analyze/full', [AnalysisController::class, 'runFullAnalysis'])->name('full-analysis');
Route::get('/api/status', [AnalysisController::class, 'status'])->name('api-status');

// Importação de notícias
Route::prefix('admin/news')->group(function () {
    Route::get('/import', [NewsImportController::class, 'showForm'])->name('news-import.form');
    Route::post('/import', [NewsImportController::class, 'import'])->name('news-import.process');
    Route::post('/import/api', [NewsImportController::class, 'importApi'])->name('news-import.api');
    Route::post('/import/weekly/api', [NewsImportController::class, 'importWeeklyApi'])->name('news-import.weekly-api');
});

// Importação de ataques
Route::prefix('admin/attacks')->group(function () {
    Route::get('/import', [AttackImportController::class, 'showForm'])->name('attacks-import.form');
    Route::post('/import', [AttackImportController::class, 'import'])->name('attacks-import.process');
    Route::post('/import/api', [AttackImportController::class, 'importApi'])->name('attacks-import.api');

    // Gestão de lotes (uploads)
    Route::get('/batches', [AttackImportBatchController::class, 'index'])->name('attacks-batches.index');
    Route::delete('/batches/{batch}', [AttackImportBatchController::class, 'destroy'])->name('attacks-batches.destroy');
});

// Relatórios de ataques
Route::prefix('api/reports/attacks')->group(function () {
    Route::get('/weekly', [AttackReportController::class, 'weeklyReport'])->name('report-attacks-weekly');
    Route::get('/daily', [AttackReportController::class, 'dailyReport'])->name('report-attacks-daily');
    Route::get('/export/weekly', [AttackReportController::class, 'exportWeekly'])->name('export-report-attacks-weekly');
    Route::get('/period-news', [AttackReportController::class, 'periodNews'])->name('report-attacks-period-news');
});

// Relatório de notícias por relevância
Route::get('/report/news/relevance', [NewsRelevanceChartController::class, 'index'])->name('report-news-relevance');
Route::get('/api/reports/news/relevance-chart', [NewsRelevanceChartController::class, 'data'])->name('api.report-news-relevance');
