<?php
use Illuminate\Support\Facades\Route;
use Stilinski\Ussd\Controllers\OnlineController;

Route::any('process-payload', [OnlineController::class, 'processPayload'])->name('online.processPayload');