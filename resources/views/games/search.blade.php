<x-app-layout>
    <div class="container mt-4">
        <h2 class="mb-4">Search Results for: "{{ $query }}"</h2>

        @if (count($games) > 0)

            <!-- Sayfalama -->
            <div class="pagination justify-content-end mt-4">
                @if ($page > 1)
                    <a href="{{ route('games.search', ['query' => $query, 'page' => $page - 1]) }}"
                        class="page-link">Previous</a>
                @endif

                <a href="{{ route('games.search', ['query' => $query, 'page' => $page + 1]) }}"
                    class="page-link">Next</a>
            </div>
            <br>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4">
                @foreach ($games as $game)
                    <div class="col">
                        <div class="game-card">
                            @if (isset($game['cover']['url']))
                                <a href="{{ route('games.show', $game['id']) }}" class="stretched-link">
                                    <img src="https:{{ str_replace('t_thumb', 't_cover_big', $game['cover']['url']) }}"
                                        class="card-img-top" alt="{{ $game['name'] }}">
                                </a>
                            @else
                                <a href="{{ route('games.show', $game['id']) }}" class="stretched-link">
                                    The photo was not found.
                                </a>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $game['name'] }}</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                No games found for "{{ $query }}"
            </div>
        @endif
    </div>
</x-app-layout>
