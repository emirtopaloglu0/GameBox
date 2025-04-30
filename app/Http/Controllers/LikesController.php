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

class LikesController extends Controller
{

    public function toggleLike(Request $request)
    {
        $request->validate([
            'game_id' => 'required|integer'
        ]);

        $like = Like::where([
            'user_id' => FacadesAuth::id(),
            'game_id' => $request->game_id
        ])->first();

        if ($like) {
            $like->delete();
            $message = 'Game unliked!';
        } else {
            Like::create([
                'user_id' => FacadesAuth::id(),
                'game_id' => $request->game_id
            ]);
            $message = 'Game liked!';
        }

        return back()->with('success', $message);
    }




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
