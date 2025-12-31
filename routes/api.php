<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Endpoint ini yang nanti akan ditembak oleh Midtrans
Route::post('/midtrans-callback', [PaymentNotificationController::class, 'handle']);