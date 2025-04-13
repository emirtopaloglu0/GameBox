<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LikedComments extends Model
{
    use HasFactory;
    protected $table = 'liked_comments';

    public function likes()
    {
        return $this->belongsTo(LikedComments::class);
    }
}
