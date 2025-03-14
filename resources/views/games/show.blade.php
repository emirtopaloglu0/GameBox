<x-app-layout>
    <div class="container py-8">

        <!-- Game Header -->
        <div class="row mb-5">
            <div class="col-md-4">
                @if (isset($game['cover']['url']))
                    @php
                        $coverUrl = str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
                    @endphp
                    <img src="https:{{ $coverUrl }}" alt="{{ $game['name'] }}"
                        class="img-fluid rounded-3 shadow-lg hover-zoom">
                @endif
            </div>

            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-3">{{ $game['name'] ?? 'Untitled Game' }}</h1>

                <!-- Release Date & Rating -->
                <div class="d-flex gap-4 mb-4">
                    @isset($game['first_release_date'])
                        <div class="badge bg-primary p-2">
                            📅 {{ date('Y', $game['first_release_date']) }}
                        </div>
                    @endisset

                    @isset($game['total_rating'])
                        <div class="badge bg-success p-2">
                            ⭐ {{ round($game['total_rating'], 1) }}/100
                        </div>
                    @endisset
                </div>

                <!-- Genres -->
                @isset($game['genres'])
                    <div class="mb-4">
                        <h5 class="mb-3">Genres</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($game['genres'] as $genre)
                                <span class="badge bg-secondary py-2 px-3">{{ $genre['name'] }}</span>
                            @endforeach
                        </div>
                    </div>
                @endisset

                <!-- Platforms -->
                @isset($game['platforms'])
                    <div class="mb-4">
                        <h5 class="mb-3">Platforms</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($game['platforms'] as $platform)
                                <span class="badge bg-info py-2 px-3">{{ $platform['name'] }}</span>
                            @endforeach
                        </div>
                    </div>
                @endisset
            </div>

            <div class="col-md-2">
                <div class="d-flex flex-column gap-3">
                    <!-- Log Butonu -->
                    <button class="btn btn-outline-primary btn-lg rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#logModal">
                        <i class="bi bi-journal-plus me-2"></i> Log
                    </button>

                    <!-- Play Later Butonu -->
                    <button class="btn btn-outline-success btn-lg rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#playLaterModal">
                        <i class="bi bi-clock me-2"></i> Play Later
                    </button>
                </div>
            </div>
            
        </div>

        <!-- Summary Section -->
        @isset($game['summary'])
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">📖 Storyline</h3>
                    <p class="card-text lead">{{ $game['summary'] }}</p>
                </div>
            </div>
        @endisset

        <!-- 1. Artworks Gallery -->
        @isset($game['artworks'])
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">🎨 Artworks</h3>
                    <div class="row row-cols-2 row-cols-md-4 g-4">
                        @foreach ($game['artworks'] as $artwork)
                            <div class="col">
                                <img src="https:{{ str_replace('t_thumb', 't_1080p', $artwork['url']) }}"
                                    class="img-fluid rounded-3 hover-zoom" alt="Game artwork">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endisset

        <!-- 2. DLCs & Expansions -->
        @isset($game['dlcs'])
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">🕹️ DLCs & Expansions</h3>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @foreach ($game['dlcs'] as $dlc)
                            <div class="col">
                                <div class="game-card-small">
                                    @if (isset($dlc['cover']['url']))
                                        <img src="https:{{ str_replace('t_thumb', 't_cover_big', $dlc['cover']['url']) }}"
                                            class="img-fluid rounded-top" alt="{{ $dlc['name'] }}">
                                    @endif
                                    <div class="p-2">
                                        <h6>{{ $dlc['name'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endisset

        <!-- 3. Age Ratings -->
        @isset($game['age_ratings'])
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">🔞 Age Ratings</h3>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($game['age_ratings'] as $rating)
                            <div class="badge bg-dark p-2">
                                {{ match ($rating['category']) {
                                    1 => 'ESRB: ' . $rating['rating'],
                                    2 => 'PEGI: ' . $rating['rating'],
                                    default => 'Age Rating: ' . $rating['rating'],
                                } }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endisset

        <!-- 4. Franchise & Series -->
        @isset($game['franchise'])
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">📚 Franchise</h3>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-collection-play fs-2"></i>
                        <h4>{{ $game['franchise']['name'] }}</h4>
                    </div>
                </div>
            </div>
        @endisset

        <!-- 5. Advanced Rating System -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body">
                <h3 class="card-title mb-4">📊 Ratings</h3>
                <div class="row">
                    <!-- User Rating -->
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="fs-5">👥 User Score</span>
                            @isset($game['total_rating'])
                                <div class="progress w-50">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ $game['total_rating'] }}%"
                                        aria-valuenow="{{ $game['total_rating'] }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ round($game['total_rating'], 1) }}%
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Not rated yet</span>
                            @endisset
                        </div>
                        <small class="text-muted">
                            Based on {{ $game['total_rating_count'] ?? 0 }} ratings
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Videos Section -->
        @isset($game['videos'])
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">🎥 Trailers & Videos</h3>
                    <div class="row row-cols-1 row-cols-lg-2 g-4">
                        @foreach ($game['videos'] as $video)
                            <div class="col">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/{{ $video['video_id'] }}"
                                        title="{{ $video['name'] }}" allowfullscreen></iframe>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endisset

        <!-- 7. Websites & Links -->
        @isset($game['websites'])
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">🌐 Official Links</h3>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($game['websites'] as $website)
                            <a href="{{ $website['url'] }}" class="btn btn-outline-dark" target="_blank">
                                @switch($website['category'])
                                    @case(1)
                                        🌐 Official Site
                                    @break

                                    @case(2)
                                        📱 Twitter
                                    @break

                                    @case(3)
                                        📘 Facebook
                                    @break

                                    @default
                                        🔗 Other
                                @endswitch
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endisset
        <!-- Back Button -->
        <a href="{{ route('games.index') }}" class="btn btn-outline-primary">
            ← Back to All Games
        </a>

    </div>



    </div>
</x-app-layout>

<style>
    .hover-zoom {
        transition: transform 0.3s ease;
    }

    .hover-zoom:hover {
        transform: scale(1.03);
    }
</style>
