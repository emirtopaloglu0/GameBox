<x-app-layout>

    <div class="container mt-4">

        <br>
        <div class="d-flex  justify-content-center mb-4">
            <form action="{{ route('games.yearFilter') }}" method="GET">
                <select name="years" id="" class="form-select mx-2 w-auto" onchange="this.form.submit()">
                    <option value="" disabled selected>YEAR</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

            </form>

            <form action="{{ route('games.genreFilter') }}" method="GET">
                <select name="genres" id="" class="form-select mx-2 w-auto" onchange="this.form.submit()">
                    <option value="" disabled selected>GENRE</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre['id'] }}">{{ $genre['name'] }}</option>
                    @endforeach
                </select>
            </form>

            <form action="">
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
            </form>

        </div>

        <div class="pagination justify-content-end">
            @if ($page > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1, 'sort' => $sortOrder]) }} "
                    class="page-link">← Previous</a>
            @else
                <a href="" class="page-link disabled">← Previous</a>
            @endif
            @if ($page > 2)
                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 2, 'sort' => $sortOrder]) }}"
                    class="page-link">{{ $page - 2 }}</a>
            @endif
            @if ($page > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1, 'sort' => $sortOrder]) }}"
                    class="page-link">{{ $page - 1 }}</a>
            @endif
            <a href="{{ request()->fullUrlWithQuery(['page' => $page, 'sort' => $sortOrder]) }}"
                class="page-link active">{{ $page }}</a>
            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1, 'sort' => $sortOrder]) }}"
                class="page-link">{{ $page + 1 }}</a>
            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 2, 'sort' => $sortOrder]) }}"
                class="page-link">{{ $page + 2 }}</a>
            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1, 'sort' => $sortOrder]) }} "class="page-link">Next
                →</a>
        </div>

        <br>
        <div class="d-lg-flex justify-content-center row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4" style="gap: 15px;">
            @foreach ($games as $game)
                <div class="game-card">
                    {{-- <h2 style="font-size: larger">{{ $game['name'] }}</h2> --}}
                    <br>
                    @if (isset($game['cover']['url']))
                        @php
                            $highResCover = str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
                        @endphp
                        <a href="{{ route('games.show', $game['id']) }}"  class="stretched-link">
                            <img class="rounded-top-3" src="https:{{ $highResCover }}"
                                alt="{{ $game['name'] ?? 'Kapak Yok' }}">
                        </a>
                    @else
                        <p>Can't Find a Cover Photo</p>
                    @endif

                    <br>
                </div>
            @endforeach
        </div>
        <br>

    </div>
</x-app-layout>
