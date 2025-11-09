<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vite-check', function () {
    $path = public_path('build/manifest.json');
    return file_exists($path) ? '✅ Manifest found' : '❌ Manifest missing';
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
