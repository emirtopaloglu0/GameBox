<?php

namespace App\Http\Controllers;

use MarcReichel\IGDBLaravel\Models\Game;
use MarcReichel\IGDBLaravel\Builder as IGDB;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        // IGDB API'ye istek gönder
        $response = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('IGDB_ACCESS_TOKEN'),
        ])->withBody(
            "fields id, name, cover.url, total_rating, total_rating_count; 
            sort total_rating_count desc; 
            limit $limit; 
            offset $offset;",
            'text/plain'
        )->post(env('IGDB_API_URL') . '/games');

        // Gelen veriyi JSON olarak al
        $games = $response->json();


        // return view('games', ['games' => $games]);
        return view('games', compact('games', 'page', 'years', 'genres'));
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
