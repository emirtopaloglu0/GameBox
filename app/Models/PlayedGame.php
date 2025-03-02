<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayedGame extends Model
{
    use HasFactory;
    protected $table = '_played_games';

    protected $fillable = [
        'rating'
    ];
}
