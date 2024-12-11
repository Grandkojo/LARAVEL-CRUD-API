<?php

use App\Http\Controllers\API\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/', function () {
    $routes = require base_path('routes/routes_list.php');
    $routesArray = [];

    foreach ($routes as $route) {
        $routesArray[] = [
            'method' => $route['method'],
            'uri' => $route['uri'],
            'message' => $route['msg']
        ];
    }

    $data = [
        'idx' =>'Welcome to Laravel Crud API',
        'status_code' => '200',
        'data' => $routesArray,
    ];
    return response()->json($data, 200);
});


// Todo routes

Route::get('todos/', [TodoController::class, 'index']);
Route::get('todos/status/{status_type}', [TodoController::class, 'filter_by_status']);
Route::get('todos/status/', [TodoController::class, 'filter_message']);


//Not Found route

Route::fallback(function (){
    return response()->json([
        'status_code' => '404',
        'message' =>'Route does not exist. Try route api/ to see available endpoints'
    ], 404);
});