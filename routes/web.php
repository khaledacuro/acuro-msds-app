<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app'); // Blade file with Vue entry point
})->where('any', '.*'); // Catch-all route for Vue Router
