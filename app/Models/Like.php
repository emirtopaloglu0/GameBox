<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $table = '_likes';
    protected $fillable = ['user_id', 'game_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
