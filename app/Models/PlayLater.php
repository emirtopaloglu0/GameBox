<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayLater extends Model
{
    use HasFactory;
    protected $table = '_play_laters';
    protected $fillable = ['user_id', 'game_id'];

}
