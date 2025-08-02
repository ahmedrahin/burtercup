<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/c-clean', function (){
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    session()->flush();
    return env('APP_NAME') . ' All cache cleared.';
});

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';
