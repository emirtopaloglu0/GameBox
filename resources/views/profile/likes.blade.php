<x-app-layout>
    <div class="container mt-4">
        <div class="d-flex  justify-content-evenly mb-4">
            <h1 class="h4">Liked Games</h1>
        </div>
        {{-- sayfa numaraları --}}
        <div class="pagination justify-content-end">
            @if ($page > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }} " class="page-link">← Previous</a>
                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}"
                    class="page-link">{{ $page - 1 }}</a>
            @else
                <a href="" class="page-link disabled">← Previous</a>
            @endif
            <a href="{{ request()->fullUrlWithQuery(['page' => $page]) }}"
                class="page-link active">{{ $page }}</a>
            @if ($hasNextPage)
                <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
                    class="page-link">{{ $page + 1 }}</a>
                <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }} "class="page-link">Next
                    →</a>
            @else
                <a href=""class="page-link disabled">Next →</a>
            @endif

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
                        <a href="{{ route('games.show', $game['id']) }}" class="stretched-link">
                            <img class="rounded-top-3" src="https:{{ $highResCover }}"
                                alt="{{ $game['name'] ?? 'Kapak Yok' }}">
                        </a>
                    @else
                        <p>Can't Find a Cover Photo</p>
                    @endif
                    <br>
                    

                    <br>
                    <p class="text-gray-600 text-sm mb-1">
                        Liked: {{ \Carbon\Carbon::parse($game['played_at'])->format('d M Y - H:i') }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
