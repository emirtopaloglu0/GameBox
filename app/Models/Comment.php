<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comments';
    protected $fillable = [
        'content',
        'parent_id',
        'user_id'
    ];

    public function logs()
    {
        return $this->belongsTo(Log::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
