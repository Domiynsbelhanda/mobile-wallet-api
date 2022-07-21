<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

$api = app(Router::class);

Route::post('hello', function(Request $request) {
    return response()->json([
        'name' => str($request->job),
        'job' => str($request->name)
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
