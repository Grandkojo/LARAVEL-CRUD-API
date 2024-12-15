<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('api/');
    // return response()->json('Welcome to Laravel Crud API. access api/routes-list to access all available routes or api/docs/ to view the api documentation', 200);
});

Route::get('/coverage', function () {
    return response()->file(base_path('coverage/index.html'));
});