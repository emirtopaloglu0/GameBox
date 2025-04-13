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


class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        //Genre verilerini alıyoruz
        $genresResponse = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
        ])->withBody(
            "fields id, name; 
            sort name;
            limit 50;",
            'text/plain'
        )->post(env('IGDB_API_URL') . '/genres');


        //Yıl Verileri
        $currentYear = date('Y');
        $years = [];
        for ($year = $currentYear; $year >= 1980; $year--) {
            $years[] = $year;
        }


        // JSON verisini diziye çevirme
        $genres = $genresResponse->json();

        // Sayfa numarasını al, eğer yoksa 1 olarak kabul et
        $page = $request->query('page', 1);
        $limit = 14; // Sayfa başına 10 oyun göstereceğiz
        $offset = ($page - 1) * $limit;
        $sortOrder = $request->query('sort', 'desc'); // Varsayılan sıralama desc


        $sortClause = "sort total_rating_count $sortOrder";


        // Cache key'i oluştur (yıl ve sayfa numarasına göre)
        $cacheKey = "games-year-$year-page-$page-sort-$sortOrder";

        $games = Cache::remember($cacheKey, 3600, function () use ($limit, $offset, $sortClause) {

            $response = Http::withHeaders([
                'Client-ID' => env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
            ])->withBody(
                "fields id, name, cover.url, total_rating, total_rating_count; 
            $sortClause;
            limit $limit; 
            offset $offset;",
                'text/plain'
            )->post(env('IGDB_API_URL') . '/games');
            return $response->json();
        });

        // Gelen veriyi JSON olarak al
        // $games = $response->json();


        // return view('games', ['games' => $games]);
        return view('games.games', compact('games', 'page', 'years', 'genres', 'sortOrder'));
    }

    public function yearFilterGames(Request $request)
    {

        $page = $request->query('page', 1);
        $year = request()->get('years');


        $limit = 14; // Sayfa başına 10 oyun göstereceğiz
        $offset = ($page - 1) * $limit; // Offset hesaplaması düzeltildi

        $sortOrder = $request->query('sort', 'desc'); // Varsayılan sıralama desc

        if ($year) {
            $startOfYear = strtotime("1 January $year"); // Seçilen yılın başlangıcı
            $endOfYear = strtotime("31 December $year"); // Seçilen yılın bitişi
            $yearFilter = "where first_release_date >= $startOfYear & first_release_date <= $endOfYear";
        } else {
            $yearFilter = ""; // Eğer yıl seçilmemişse, filtreleme yapma
        }

        $sortClause = "sort total_rating_count $sortOrder";

        // Cache key'i oluştur (yıl ve sayfa numarasına göre)
        $cacheKey = "games-year-$year-page-$page-sort-$sortOrder";

        $games = Cache::remember($cacheKey, 3600, function () use ($yearFilter, $limit, $offset, $sortClause) {

            $response = Http::withHeaders([
                'Client-ID' => env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
            ])->withBody(
                "fields id, name, cover.url, first_release_date, total_rating, total_rating_count; 
            $sortClause;
            $yearFilter;
            limit $limit; 
            offset $offset;",
                'text/plain'
            )->post(env('IGDB_API_URL') . '/games');

            return $response->json();
        });

        // $games = $response->json();

        //Yıl Verileri
        $currentYear = date('Y');
        $manuelYears = [];
        for ($manuelYear = $currentYear; $manuelYear >= 1980; $manuelYear--) {
            $manuelYears[] = $manuelYear;
        }

        return view('games.year', compact(
            'games',
            'year',
            'page',
            'manuelYears',
            'sortOrder'
        ));
    }

    public function genreFilterGames(Request $request)
    {
        $page = $request->query('page', 1);
        $genreId = $request->query('genres');
        $sortOrder = $request->query('sort', 'desc');
        $limit = 14;
        $offset = ($page - 1) * $limit;

        $genreName = Cache::remember("genre-name-$genreId", 3600, function () use ($genreId) {
            $response = Http::withHeaders([
                'Client-ID' => env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
            ])
                ->withBody(
                    "fields name;
                     where id = $genreId;
                     limit 1;",
                    'text/plain'
                )->post(env('IGDB_API_URL') . '/genres');

            $genreData = $response->json();

            // Hata kontrolü ekleyelim
            if (empty($genreData) || !isset($genreData[0]['name'])) {
                return 'Bilinmeyen Tür';
            }

            return $genreData[0]['name'];
        });

        // Cache key
        $cacheKey = "games-genre-$genreId-page-$page-sort-$sortOrder";

        $games = Cache::remember($cacheKey, 3600, function () use ($genreId, $limit, $offset, $sortOrder) {
            $response = Http::withHeaders([
                'Client-ID' => env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
            ])->withBody(
                "fields id, name, cover.url, genres, total_rating, total_rating_count;
                 where genres = [$genreId];
                 sort total_rating_count $sortOrder;
                 limit $limit;
                 offset $offset;",
                'text/plain'
            )->post(env('IGDB_API_URL') . '/games');

            return $response->json();
        });

        $genres = Cache::remember('all-genres', 3600, function () {
            $response = Http::withHeaders([
                'Client-ID' => env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
            ])
                ->withBody("fields id, name; sort name; limit 50;", 'text/plain')
                ->post(env('IGDB_API_URL') . '/genres');

            $genresData = $response->json();

            // Eğer API'dan düzgün bir dizi gelmezse boş dizi döndür
            return is_array($genresData) ? $genresData : [];
        });



        return view('games.genre', compact('games', 'genreId', 'page', 'genres', 'sortOrder', 'genreName'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $response = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
        ])->withBody(
            "fields id, name, cover.url, total_rating, total_rating_count;
         search \"{$query}\";
         limit $limit;
         offset $offset;",
            'text/plain'
        )->post(env('IGDB_API_URL') . '/games');

        $games = $response->json();

        return view('games.search', compact('games', 'query', 'page'));
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
        // Cache key oluştur (game-details-123 gibi)
        $cacheKey = "game-details-{$id}";

        $game = Cache::remember($cacheKey, 3600, function () use ($id) {
            $response = Http::withHeaders([
                'Client-ID' => env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
            ])->withBody(
                "fields id, name, cover.url, summary, 
                first_release_date, genres.name, platforms.name, 
                involved_companies.company.name, artworks.url,
                dlcs.name, dlcs.cover.url,
                age_ratings.category, age_ratings.rating,
                similar_games.name, similar_games.cover.url,
                franchise.name,
                total_rating, total_rating_count,
                videos.video_id, videos.name,
                websites.category, websites.url;
             where id = {$id};
             limit 1;",
                'text/plain'
            )->post(env('IGDB_API_URL') . '/games');

            return $response->json()[0] ?? abort(404);
        });

        if (!$game) {
            return redirect()->route('games.index')->with('error', 'Game not found!');
        }

        return view('games.show', compact('game'));
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
