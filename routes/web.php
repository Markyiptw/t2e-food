<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', MapController::class)->name('map');
Route::get('/map/restaurants', [MapController::class, 'restaurants'])->name('map.restaurants');
Route::get('/export', [ExportController::class, 'index']);
Route::post('/export', [ExportController::class, 'store']);
