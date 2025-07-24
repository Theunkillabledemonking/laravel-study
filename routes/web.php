<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Hello, World!';
});

Route::get('about', function() {
    return view('about');
});

Route::get('products', function() {
    return view('products');
});

Route::get('services', function() {
    return view('services');
});

