<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class);

Route::get('/map', MapController::class)->name('map');
Route::get('/map/restaurants', [RestaurantController::class, 'index'])->name('map.restaurants');
Route::get('/export', [ExportController::class, 'index']);
Route::post('/export', [ExportController::class, 'store']);
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
