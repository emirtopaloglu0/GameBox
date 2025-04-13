<?php

namespace App\Http\Controllers;

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


class PlayLaterController extends Controller
{
    public function toggleLater(Request $request)
    {

        $request->validate([
            'game_id' => 'required|integer'
        ]);

        $later = PlayLater::where([
            'user_id' => FacadesAuth::id(),
            'game_id' => $request->game_id
        ])->first();

        if ($later) {
            $later->delete();
            $message = 'Game removed from Play Later!';
        } else {
            PlayLater::create([
                'user_id' => FacadesAuth::id(),
                'game_id' => $request->game_id
            ]);
            $message = 'Game added to Play Later!';
        }


        return back()->with('success', $message);
    }
}
