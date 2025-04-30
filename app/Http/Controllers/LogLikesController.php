<?php

namespace App\Http\Controllers;

use App\Models\LogLikes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;


class LogLikesController extends Controller
{

    public function likeLog(Request $request)
    {
        $request->validate([
            'review_id' => 'required|integer'
        ]);

        $like = LogLikes::where([
            'user_id' => FacadesAuth::id(),
            'log_id' => $request->review_id
        ])->first();

        if ($like) {
            $like->delete();
            $message = 'Review unliked!';
        } else {
            LogLikes::create([
                'user_id' => FacadesAuth::id(),
                'log_id' => $request->review_id
            ]);
            $message = 'Review liked!';
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
