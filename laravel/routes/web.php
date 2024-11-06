<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;


Route::post('/start-scan', [ScanController::class, 'startScan'])->name('startScan');
Route::get('/scan-results', [ScanController::class, 'getScanResults'])->name('getScanResults');


Route::get('/', function () {
    return view('scan');
});

Route::get('/scan', function () {
    $response = Http::timeout(6000)->get('http://localhost:8080/scan');
    return response($response->body(), $response->status())
           ->header('Content-Type', 'application/json');
});
