<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\NewsImportController;

// Dashboard e visualizações
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
Route::get('/correlations', [DashboardController::class, 'correlations'])->name('correlations');
Route::get('/attacks', [DashboardController::class, 'attacks'])->name('attacks');
Route::get('/attacks/{id}', [DashboardController::class, 'attackDetail'])->name('attack-detail');
Route::get('/timeline', [DashboardController::class, 'timeline'])->name('timeline');

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