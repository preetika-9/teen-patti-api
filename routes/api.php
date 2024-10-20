<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::middleware('auth:sanctum')->group(function () {
    Route::post('game/create', [GameController::class, 'createGame']);
    Route::post('game/join', [GameController::class, 'joinGame']);
    Route::post('game/start', [GameController::class, 'startGame']);
    Route::post('game/bet', [GameController::class, 'placeBet']);
    Route::post('game/checkWinner', [GameController::class, 'checkWinner']);
// });


Route::get('/health', function () {
    return response()->json(['status' => 'API is working']);
});
