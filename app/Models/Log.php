<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table = 'Logs';
    protected $fillable = [
        'user_id',
        'game_id',
        'note',
        'rating',
        'user_likes'
    ];
}
