<?php

use Illuminate\Support\Facades\Route;

// keep it empty or a simple test route
Route::get('/ping', fn () => response()->json(['ok' => true]));
