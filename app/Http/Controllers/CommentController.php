<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Log;
use App\Models\PlayLater;
use Illuminate\Container\Attributes\Auth;
use MarcReichel\IGDBLaravel\Models\Game;
use MarcReichel\IGDBLaravel\Builder as IGDB;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class CommentController extends Controller
{
    public function sendComment(Request $request){
        $validated = $request->validate([
            'reply' => 'required',
        ]);

        Comment::create([
            'user_id'=> FacadesAuth::id(),
            'parent_id' => $request['parent_id'],
            'content' => $validated['reply']
        ]);
        return back()->with('success');

    }
}
