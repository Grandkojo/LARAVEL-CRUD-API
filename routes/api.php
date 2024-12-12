<?php

use App\Http\Controllers\API\TodoController;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/', function(){
    return response()->json('Welcome to Laravel Crud API. access api/routes-list to access all available routes', 200);
});


// Routes list
Route::get('routes-list/', [TodoController::class, 'routes_list']);

// Todo routes

Route::get('todos/', [TodoController::class, 'index']);
Route::get('todos/status/{status_type}', [TodoController::class, 'filter_by_status']);
Route::get('todos/status/', [TodoController::class, 'filter_message']);
Route::post('todos/new/', [TodoController::class, 'store']);
Route::put('todos/edit/{id}', [TodoController::class, 'update']);
Route::delete('todos/delete/{id}', [TodoController::class, 'destroy']);


//Not Found route

Route::fallback(function (){
    return response()->json([
        'status_code' => '404',
        'message' =>'Route does not exist. Try route api/routes-list to see available endpoints'
    ], 404);
});