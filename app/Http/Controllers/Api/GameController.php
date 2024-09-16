<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class GameController extends Controller
{
    public function createGame(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public, private',
            'password' => 'required_if:type,private|string|nullable|max:255'
        ]);

        $game = Game::create([
            'name' => $request->name,
            'creator_id' => FacadesAuth::id(),
            'type' => $request->type,
            'password' => $request->type === 'private' ? bcrypt($request->password) : null,
        ]);


        return response()->json([
            'message' => 'Game created successfully',
            'game' => $game
        ], 201);
    }
}
