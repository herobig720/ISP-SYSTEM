<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Http\Controllers\ClientController;

Route::middleware(['web'])
    ->prefix('client')
    ->group(function () {
        Route::get('/', [ClientController::class, 'index']);
    });