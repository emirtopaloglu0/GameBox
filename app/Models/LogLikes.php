<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LogLikes extends Model
{
    use HasFactory;
    protected $table = 'log_likes';
    protected $fillable = ['user_id', 'log_id'];

    public function likes()
    {
        return $this->belongsTo(Log::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
