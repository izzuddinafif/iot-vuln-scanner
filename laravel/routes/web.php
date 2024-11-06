<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;


Route::post('/start-scan', [ScanController::class, 'startScan'])->name('startScan');
Route::get('/scan-results', [ScanController::class, 'getScanResults'])->name('getScanResults');


Route::get('/', function () {
    return view('scan');
});
