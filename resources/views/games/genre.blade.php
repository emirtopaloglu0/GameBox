<x-app-layout>
    <div class="container mt-4">

        {{-- <h1 class="navbar navbar-brand justify-content-center">{{ $genreName }}</h1>
        <br> --}}

        <div class="d-flex justify-content-center mb-4">
            <form method="GET" action="{{ route('games.genreFilter') }}" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <select name="genres" class="form-select" onchange="this.form.submit()">
                            @foreach ($genres as $genre)
                                @if (isset($genre['id']) && isset($genre['name']))
                                    <option value="{{ $genre['id'] }}"
                                        {{ $genre['id'] == $genreId ? 'selected' : '' }}>
                                        {{ $genre['name'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <!-- Sıralama Butonları -->
                    <div class="col-auto">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}"
                            class="btn {{ $sortOrder == 'desc' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Highest Ratings
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}"
                            class="btn {{ $sortOrder == 'asc' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Lowest Ratings
                        </a>
                    </div>
                </div>
            </form>

        </div>

        {{-- Sayfalama --}}
        <div class="pagination justify-content-end mt-4">
            @if ($page > 1)
                <a href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page - 1]) }}"
                    class="page-link">←
                    Previous</a>
            @else
                <a href="" class="page-link disabled">← Previous</a>
            @endif
            @if ($page > 2)
                <a href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page - 2]) }}"
                    class="page-link">{{ $page - 2 }}</a>
            @endif
            @if ($page > 1)
                <a href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page - 1]) }}"
                    class="page-link">{{ $page - 1 }}</a>
            @endif
            <a href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page]) }}"
                class="page-link active">{{ $page }}</a>
            @if (count($games) >= 14)
                <a href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page + 1]) }}"
                    class="page-link">{{ $page + 1 }}</a>
                <a href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page + 2]) }}"
                    class="page-link">{{ $page + 2 }}</a>
                <a
                    href="{{ route('games.genreFilter', [...request()->except('page'), 'page' => $page + 1]) }}"class="page-link">Next →</a>
            @endif

        </div>
        <br>
        <!-- Oyun Listesi -->
        <div class="d-lg-flex justify-content-center row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4" style="gap: 15px;">
            @foreach ($games as $game)
                <div class="game-card ">
                    <br>
                    @if (isset($game['cover']['url']))
                        @php
                            $highResCover = str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
                        @endphp
                        <img class="rounded-top-3" src="https:{{ $highResCover }}"
                            alt="{{ $game['name'] ?? 'Kapak Yok' }}">
                    @else
                        <p>Can't Find a Cover Photo</p>
                    @endif

                    <br>
                    @if (isset($game['total_rating_count']))
                        <p>Votes: {{ $game['total_rating_count'] }}</p>
                    @endif

                    @if (isset($game['total_rating']))
                        <p class="small text-muted">Avarage Ratings: {{ round($game['total_rating'], 2) }}</p>
                    @endif

                </div>
            @endforeach
        </div>
        <br>


    </div>
</x-app-layout>
