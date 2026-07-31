<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Rute untuk menampilkan form pencarian (Method GET)
Route::get('/', [AnalyzerController::class, 'index'])->name('analyzer.index');

// Rute untuk memproses analisis ketika form di-submit (Method POST)
Route::post('/analyze', [AnalyzerController::class, 'analyze'])->name('analyzer.process');

// Rute status laporan untuk polling AJAX
Route::get('/analyze/{id}/status', [AnalyzerController::class, 'status'])->name('analyzer.status');

// Rute menyimpan hasil PageSpeed yang dikirim browser
Route::post('/analyze/{id}/report', [AnalyzerController::class, 'storePageSpeed'])->name('analyzer.store');

// Rute melihat hasil laporan
Route::get('/analyze/{id}', [AnalyzerController::class, 'show'])->name('analyzer.show');

// Rute untuk download PDF
Route::get('/analyze/{id}/pdf', [AnalyzerController::class, 'exportPdf'])->name('analyzer.pdf');