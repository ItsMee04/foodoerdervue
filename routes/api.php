<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me'])->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('create-order', function () {
        return "create-order";
    })->middleware(['ableCreateOrder']);

    Route::post('finish-order', function () {
        return "finish-order";
    })->middleware(['ableFinishOrder']);

    Route::post('create-user', [UserController::class, 'store'])->middleware(['ableCreateUser']);

    Route::post('create-item', [ItemController::class, 'store'])->middleware(['ableCreateUpdateItem']);
    Route::patch('update-item/{id}', [ItemController::class, 'update'])->middleware(['ableCreateUpdateItem']);
});