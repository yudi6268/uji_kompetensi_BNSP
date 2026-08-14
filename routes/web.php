<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Models\Article;

use App\Models\HealthMetric;

Route::get('/', function () {
    $allArticles = Article::latest()->get();
    $headline = $allArticles->first();
    $articles = $allArticles->skip(1);
    
    $metrics = HealthMetric::all();
    
    return view('welcome', compact('headline', 'articles', 'metrics'));
});

Route::get('/article/{article}', [ArticleController::class, 'show'])->name('article.show');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/metric', [DashboardController::class, 'storeMetric'])->name('metrics.store');
    Route::put('/dashboard/metric/{metric}', [DashboardController::class, 'updateMetric'])->name('metrics.update');
    Route::delete('/dashboard/metric/{metric}', [DashboardController::class, 'destroyMetric'])->name('metrics.destroy');

    // Articles CRUD
    Route::resource('articles', ArticleController::class);
});
