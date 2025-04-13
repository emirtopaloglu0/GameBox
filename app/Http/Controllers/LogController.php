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


class LogController extends Controller
{
    public function storeLog(Request $request)
    {

        $validated = $request->validate([
            'game_id' => 'required|integer',
            'rating' => 'required|numeric|min:0|max:5',
            'notes' => 'nullable|string|max:500',
        ]);

        Log::create([
            'user_id' => FacadesAuth::id(),
            'game_id' => $validated['game_id'],
            'rating' => $validated['rating'],
            'note' => $validated['notes'],
        ]);
        $message = "Game Successfuly Logged!";

        return back()->with('success', $message);
    }
}
