<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});
