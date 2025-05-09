<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Comment;
use App\Models\Log;
use App\Models\LogLikes;
use App\Models\PlayedGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;


class ProfileController extends Controller
{


    public function show()
    {
        $games = [];
        $currentUserId = Auth::id();

        $playedGames = PlayedGame::with('user')
            ->where('user_id', $currentUserId)
            ->orderByDesc('updated_at')->get();

        $gameIds = $playedGames->pluck('game_id')
            ->toArray();

        $gameIds = implode(',', $gameIds);
        $response = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
        ])->withBody(
            "fields id, name, cover.url, total_rating, total_rating_count; 
             where id = ($gameIds);
             limit 100;",
            'text/plain'
        )->post(env('IGDB_API_URL') . '/games');


        // Gelen veriyi JSON olarak al
        $apiGames = collect($response->json());
        $games = $playedGames->map(function ($played) use ($apiGames) {
            $game = $apiGames->firstWhere('id', $played->game_id);

            return $game ? array_merge($game, [
                'played_at' => $played->updated_at,
                'rating' => $played->rating
            ]) : null;
        })->filter(); // null olanları at

        $reviews = $this->GetReviews();
        $comments = $this->GetComments();
        $logLikes = $this->GetLogLikes();


        return view('profile.show', compact('games', 'playedGames', 'reviews', 'comments', 'logLikes'));
    }

    public function GetLogLikes()
    {
        return LogLikes::with('likes.user')->get();
    }

    public function GetReviews()
    {
        $currentUserId = Auth::id();
        $sortClause = "sort total_rating_count desc";

        $reviews = Log::with('user')
            ->where('user_id', $currentUserId)
            ->orderByDesc('updated_at')
            ->get();

        $gameIds = $reviews->pluck('game_id')
            ->toArray();

        $gameIds = implode(',', $gameIds);

        $response = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
        ])->withBody(
            "fields id, name, cover.url, total_rating, total_rating_count; 
             $sortClause;
             where id = ($gameIds);",
            'text/plain'
        )->post(env('IGDB_API_URL') . '/games');

        // Gelen veriyi JSON olarak al
        $apiGames = collect($response->json());

        $sortedReviews = $reviews->map(function ($review) use ($apiGames) {
            $game = $apiGames->firstWhere('id', $review->game_id);

            return $game ? array_merge($game, [
                'updated_at' => $review->updated_at,
                'note' => $review->note,
                'user_id' => $review->user->id,
                'username' => $review->user->username,
                'review_id' => $review->id,
                'rating' => $review->rating
            ]) : null;
        })->filter();

        return $sortedReviews;
    }

    public function GetComments()
    {
        return Comment::with('logs.user')->orderByDesc('updated_at')->get();
    }


    public function showPlayed(Request $request)
    {

        $limit = 9; // sayfa başına gösterilecek veri
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;


        $games = [];
        $currentUserId = Auth::id();

        $playedGames = PlayedGame::with('user')
            ->where('user_id', $currentUserId)
            ->orderByDesc('updated_at')->get();

        $gameIds = $playedGames->pluck('game_id')
            ->toArray();

        $gameIds = implode(',', $gameIds);

        $response = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
        ])->withBody(
            "fields id, name, cover.url, total_rating, total_rating_count; 
             where id = ($gameIds);
             limit " . ($limit + 1) . ";
             offset $offset;",
            'text/plain'
        )->post(env('IGDB_API_URL') . '/games');

        $pageCOntrol = $response->json();

        // Gelen veriyi JSON olarak al
        $apiGames = collect($response->json());

        $games = $playedGames->map(function ($played) use ($apiGames) {
            $game = $apiGames->firstWhere('id', $played->game_id);

            return $game ? array_merge($game, [
                'played_at' => $played->updated_at,
                'rating' => $played->rating
            ]) : null;
        })->filter(); // null olanları at
        $hasNextPage = count($pageCOntrol) > $limit;

        return view('profile.allPlayedGames', compact('games', 'page', 'hasNextPage'));
    }



    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
