<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LogLikes extends Model
{
    use HasFactory;
    protected $table = 'log_likes';

    public function likes()
    {
        return $this->belongsTo(Log::class);
    }
}
