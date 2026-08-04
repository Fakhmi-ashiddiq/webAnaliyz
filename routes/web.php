<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [AnalyzerController::class, 'index'])->name('analyzer.index');
    Route::post('/analyze', [AnalyzerController::class, 'analyze'])->name('analyzer.process');
    Route::get('/analyze/{id}', [AnalyzerController::class, 'result'])->name('analyzer.result');
    Route::get('/analyze/{id}/export/pdf', [AnalyzerController::class, 'exportPdf'])->name('analyzer.export');
    Route::get('/analyze/{id}/export/word', [AnalyzerController::class, 'exportWord'])->name('analyzer.exportWord');
});