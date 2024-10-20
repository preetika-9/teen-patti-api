<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'user_id', 'hand', 'balance', 'bet'];

    protected $casts = [
        'hand' => 'array',  // To store the hand (cards) as an array
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }
}
