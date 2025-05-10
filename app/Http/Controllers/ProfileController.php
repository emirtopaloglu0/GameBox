<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Log;
use App\Models\LogLikes;
use App\Models\PlayedGame;
use App\Models\PlayLater;
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

        $comments = $this->GetComments();
        $logLikes = $this->GetLogLikes();
        $latestReviews = $this->GetLatestReviews();
        $mostLikedReviews = $this->GetMostLikedReviews();

        return view('profile.show', compact('games', 'playedGames', 'latestReviews', 'comments', 'logLikes', 'mostLikedReviews'));
    }
    public function ShowPlayed(Request $request)
    {
        $page = $request->input('page', 1);


        $limit = 9; // sayfa başına gösterilecek veri
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

    public function ShowLogs(Request $request)
    {
        $pagReviews = $this->GetPaginationReviws($request);
        $reviews = $pagReviews['reviews'];
        $hasNextPage = $pagReviews['hasNextPage'];
        $page = $pagReviews['page'];

        $logLikes = $this->GetLogLikes();
        $comments = $this->GetComments();
        return view('profile.logs', compact('reviews', 'logLikes', 'comments', 'hasNextPage', 'page'));
    }

    public function ShowLikes(Request $request)
    {
        $pagLikedGames = $this->GetPaginationLikedGames($request);
        $page = $pagLikedGames['page'];
        $hasNextPage = $pagLikedGames['hasNextPage'];
        $games = $pagLikedGames['likedGames'];
        return view('profile.likes', compact('page', 'hasNextPage', 'games'));
    }

    public function ShowLaterList(Request $request)
    {

        $pagLikedGames = $this->GetPaginationLaterListGames($request);
        $page = $pagLikedGames['page'];
        $hasNextPage = $pagLikedGames['hasNextPage'];
        $games = $pagLikedGames['laterGames'];
        return view('profile.later', compact('page', 'hasNextPage', 'games'));
    }

    public function GetPaginationLaterListGames(Request $request)
    {
        $page = $request->input('page', 1);


        $limit = 9; // sayfa başına gösterilecek veri
        $offset = ($page - 1) * $limit;


        $games = [];
        $currentUserId = Auth::id();

        $playedGames = PlayLater::with('user')
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

        return [
            'hasNextPage' => $hasNextPage,
            'page' => $page,
            'laterGames' => $games
        ];
    }


    public function GetPaginationLikedGames(Request $request)
    {
        $page = $request->input('page', 1);


        $limit = 9; // sayfa başına gösterilecek veri
        $offset = ($page - 1) * $limit;


        $games = [];
        $currentUserId = Auth::id();

        $playedGames = Like::with('user')
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

        return [
            'hasNextPage' => $hasNextPage,
            'page' => $page,
            'likedGames' => $games
        ];
    }


    public function GetPaginationReviws(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = 4; // sayfa başına gösterilecek veri
        $offset = ($page - 1) * $limit;


        $games = [];
        $currentUserId = Auth::id();

        $pagLogs = Log::with('user')
            ->where('user_id', $currentUserId)
            ->orderByDesc('updated_at')->get();

        $gameIds = $pagLogs->pluck('game_id')
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

        $reviewsWithGames = $pagLogs->map(function ($review) use ($apiGames) {
            $game = $apiGames->firstWhere('id', $review->game_id);

            return $game ? array_merge($game, [
                'updated_at' => $review->updated_at,
                'note' => $review->note,
                'user_id' => $review->user->id,
                'username' => $review->user->username,
                'review_id' => $review->id,
                'rating' => $review->rating
            ]) : null;
        })->filter(); // null olanları at
        $hasNextPage = count($pageCOntrol) > $limit;
        return [
            'reviews' => $reviewsWithGames,
            'hasNextPage' => $hasNextPage,
            'page' => $page
        ];
    }


    public function GetLatestReviews()
    {
        $reviews = $this->GetReviews();
        return $reviews->sortByDesc('updated_at')->take(3);
    }

    public function GetMostLikedReviews()
    {
        $reviews = $this->GetReviews();

        $topLogIds = LogLikes::select('log_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('log_id')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('log_id');

        return $reviews->whereIn('review_id', $topLogIds); //whereIn birden fazla değeri getirtiyormuş.

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
