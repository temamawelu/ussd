<?php
use Illuminate\Support\Facades\Route;
use Stilinski\Ussd\Controllers\OnlineController;

Route::any('process-payload/55034fd5-bd23h5d9948f', [OnlineController::class, 'processPayload'])->name('online.processPayload');