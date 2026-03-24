<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AttackReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsImportController;
use Illuminate\Support\Facades\Route;

// Dashboard e visualizações
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
Route::get('/correlations', [DashboardController::class, 'correlations'])->name('correlations');
Route::get('/attacks', [DashboardController::class, 'attacks'])->name('attacks');
Route::get('/attacks/{id}', [DashboardController::class, 'attackDetail'])->name('attack-detail');
Route::get('/timeline', [DashboardController::class, 'timeline'])->name('timeline');
Route::get('/report/attacks', [AttackReportController::class, 'view'])->name('report-attacks-view');

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
});

// Importação de ataques
Route::prefix('admin/attacks')->group(function () {
    Route::get('/import', [\App\Http\Controllers\AttackImportController::class, 'showForm'])->name('attacks-import.form');
    Route::post('/import', [\App\Http\Controllers\AttackImportController::class, 'import'])->name('attacks-import.process');
    Route::post('/import/api', [\App\Http\Controllers\AttackImportController::class, 'importApi'])->name('attacks-import.api');
});

// Relatórios de ataques
Route::prefix('api/reports/attacks')->group(function () {
    Route::get('/weekly', [AttackReportController::class, 'weeklyReport'])->name('report-attacks-weekly');
    Route::get('/daily', [AttackReportController::class, 'dailyReport'])->name('report-attacks-daily');
    Route::get('/export/weekly', [AttackReportController::class, 'exportWeekly'])->name('export-report-attacks-weekly');
});
