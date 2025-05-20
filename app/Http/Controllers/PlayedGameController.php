<?php

namespace App\Http\Controllers;

use App\Models\PlayedGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Http;

class PlayedGameController extends Controller
{
    public function togglePlay(Request $request)
    {
        $request->validate([
            'game_id' => 'required|integer'
        ]);

        $playedGame = PlayedGame::where([
            'user_id' => FacadesAuth::id(),
            'game_id' => $request->game_id
        ])->first();

        if ($playedGame) {
            $playedGame->delete();
            $message = 'Game removed from Played List!';
        } else {
            PlayedGame::create([
                'user_id' => FacadesAuth::id(),
                'game_id' => $request->game_id
            ]);
            $message = 'Game add to Played List!';
        }

        return back()->with('success', $message);
    }

    
}
