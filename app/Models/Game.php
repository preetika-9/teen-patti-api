<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'creatoe_id', 'status', 'type', 'password'];

    //relationship with user
    public function creator(){
        return $this->belongsTo(User::class , 'creator_id');
    }

    //relationship with the player model
    // public function players(){
    //     return $this->hasMany(Player::class);
    // }


}
