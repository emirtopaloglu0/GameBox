<x-app-layout>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast("{{ session('success') }}", "primary");
            });
        </script>
    @endif

    @php
        $last_review = 0;
        $comment_counter = 0;
        $like_counter = 0;
        $last_likedReview = 0;
        $review_counter = 1;
    @endphp

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
                    <!-- Played Butonu, Loglamadan oynadımı işaretleyecek sadece -->
                    <form action="{{ route('games.play.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game['id'] }}">
                        <button type="submit"
                            class="btn                              
                            {{ auth()->user()->playedGames()->where('game_id', $game['id'])->exists() ? 'btn-primary' : 'btn-outline-primary' }} btn-lg rounded-pill w-100">
                            🎮
                            {{ auth()->user()->playedGames()->where('game_id', $game['id'])->exists() ? 'Played' : 'Play' }}
                        </button>
                    </form>

                    <!-- Log Butonu -->
                    <!-- Modal Trigger Button -->
                    <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#logModal">
                        📝 Log
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h5 class="modal-title" id="logModalLabel">I Played...</h5>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                        aria-label="Close">Close</button>
                                </div>

                                <!-- Modal Body (Form) -->
                                <div class="modal-body">
                                    <form action="{{ route('games.log.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="game_id" value="{{ $game['id'] }}">

                                        <!-- Rating -->
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Rating</label>
                                            {{-- <input type="number" class="form-control" id="rating" name="rating"
                                                min="1" max="5"> --}}
                                            <br>
                                            <div class="stars" data-rating="{{ $game->rating ?? 0 }}">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <div class="star" data-value="{{ $i }}">
                                                        ★
                                                    </div>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="ratingInput"
                                                value="{{ old('rating', $game->rating ?? 0) }}">
                                        </div>

                                        <!-- Notes -->
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Review</label>
                                            <textarea placeholder="Add a review..." class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Play Later Butonu -->
                    <form action="{{ route('games.later.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game['id'] }}">
                        <button type="submit"
                            class="btn {{ auth()->user()->playLater()->where('game_id', $game['id'])->exists() ? 'btn-success' : 'btn-outline-success' }}
                            btn-lg rounded-pill w-100">
                            ⏳
                            {{ auth()->user()->playLater()->where('game_id', $game['id'])->exists() ? 'Remove' : 'Play Later' }}
                        </button>
                    </form>

                    <!-- Like Butonu -->
                    <form action="{{ route('games.like.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game['id'] }}">
                        <button type="submit"
                            class="btn                              
                            {{ auth()->user()->likes()->where('game_id', $game['id'])->exists() ? 'btn-danger' : 'btn-outline-danger' }} btn-lg rounded-pill w-100">
                            ❤️
                            {{ auth()->user()->likes()->where('game_id', $game['id'])->exists() ? 'Unlike' : 'Like' }}
                        </button>
                    </form>
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

        {{-- Logs / Comment kısmı --}}
        <div class="">
            <div class="">
                <h2 class="card-title mb-4">📝 Reviews</h2>
                @forelse($reviews as $review)
                    @php
                        $last_review++;
                    @endphp
                    @if ($last_review > 1)
                        @php
                            $comment_counter = 0;
                            $last_review = 0;
                        @endphp
                    @endif

                    @if ($review_counter < 3)
                        @php
                            @$review_counter++;
                        @endphp
                    @else
                        @break;
                    @endif

                    <div class="card mb-4 border-0 shadow-sm">
                        {{-- Log Remove / Edit --}}
                        @if ($review->user_id == auth()->user()->id)
                            <div class="card-header bg-light d-flex justify-content-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-danger me-2" data-bs-toggle="modal"
                                        data-bs-target="#removeLogModal" removeLog-data-id="{{ $review->id }}"
                                        onclick="fillRemoveLogModalFields(this)">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-id="{{ $review->id }}"
                                        data-text="{{ e($review->note) }}" onclick="fillModalFields(this)">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <!-- User Info -->
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-person-circle fs-4 text-muted me-2"></i>
                                        <h5 class="mb-0">{{ $review->user->username ?? 'Bilinmeyen Kullanıcı' }}
                                        </h5>
                                        <span class="text-muted ms-auto">
                                            {{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y, H:i') }}
                                        </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="mb-3">
                                        @php
                                            $stars = floor($review->rating);
                                            $half = $review->rating - $stars >= 0.5 ? true : false;
                                        @endphp

                                        @for ($i = 0; $i < $stars; $i++)
                                            <i class="bi bi-star-fill text-warning fs-5"></i>
                                        @endfor

                                        @if ($half)
                                            <i class="bi bi-star-half text-warning fs-5"></i>
                                        @endif

                                        @for ($i = $stars + ($half ? 1 : 0); $i < 5; $i++)
                                            <i class="bi bi-star text-warning fs-5"></i>
                                        @endfor

                                        <span
                                            class="badge bg-light text-dark ms-2">{{ number_format($review->rating, 2) }}</span>
                                    </div>

                                    <!-- Review Content -->
                                    <div class="mb-3">
                                        <p class="card-text">{{ $review->note }}</p>
                                    </div>

                                    <!-- Like Section -->
                                    <div class="d-flex align-items-center mb-4">
                                        <form action="{{ route('games.log.like') }}" method="POST" class="me-3">
                                            @csrf
                                            <input type="hidden" name="review_id" value="{{ $review->id }}">
                                            <button type="submit"
                                                class="btn btn-sm {{ auth()->user()->log_likes()->where('log_id', $review->id)->exists() ? 'btn-danger' : 'btn-outline-danger' }}">
                                                <i class="bi bi-heart-fill"></i> Like
                                            </button>
                                        </form>

                                        @foreach ($logLikes as $like)
                                            @if ($like->log_id == $review->id)
                                                @php
                                                    $last_likedReview = $review->id;
                                                    $like_counter++;
                                                @endphp
                                            @endif
                                            @if ($last_likedReview !== $review->id)
                                                @php
                                                    $like_counter = 0;
                                                @endphp
                                            @endif
                                        @endforeach

                                        <span class="text-muted">
                                            @if ($like_counter > 0)
                                                {{ $like_counter }} {{ $like_counter == 1 ? 'like' : 'likes' }}
                                            @else
                                                No likes yet
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Comments Section -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-3"><i class="bi bi-chat-left-text me-2"></i>Comments
                                        </h6>
                                        <div class="bg-light rounded p-3">
                                            @foreach ($comments as $comment)
                                                @if ($review->id == $comment->parent_id)
                                                    @if ($comment_counter <= 1)
                                                        <div class="mb-3 pb-2 border-bottom">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-1">
                                                                <strong
                                                                    class="text-primary">{{ $comment->user->username }}</strong>
                                                                <small
                                                                    class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                            </div>
                                                            <p class="mb-2">{{ $comment->content }}</p>

                                                            @if ($comment->user_id == auth()->user()->id)
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-sm"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#removeCommentModal"
                                                                        removeComment-data-id="{{ $comment->id }}"
                                                                        onclick="fillRemoveCommentModalFields(this)">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-warning btn-sm"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#commentModal"
                                                                        comment-data-id="{{ $comment->id }}"
                                                                        comment-data-text="{{ e($comment->content) }}"
                                                                        onclick="fillCommentModalFields(this)">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @php
                                                            $comment_counter++;
                                                        @endphp
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Reply Form -->
                                    <form method="POST" action="{{ route('games.log.comments') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $review->id }}">
                                        <div class="input-group">
                                            <input type="text" id="reply" name="reply" class="form-control"
                                                placeholder="Write a comment..." aria-label="Comment">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send"></i> Send
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light">
                            <a href="{{ route('games.comments', $review->id) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-chat-square-text"></i> View all comments
                            </a>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <br>

                @empty
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> There are no reviews yet...
                    </div>
                @endforelse
            </div>
        </div>

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
                                        aria-valuenow="{{ $game['total_rating'] }}" aria-valuemin="0"
                                        aria-valuemax="100">
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
        <hr>

        <!-- Back Button -->
        <a href="{{ route('games.index') }}" class="btn btn-outline-primary">
            ← Back to All Games
        </a>




        <!-- Review Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editing...</h5>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>

                    <!-- Modal Body (Form) -->
                    <div class="modal-body">
                        <form action="{{ route('games.log.edit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" id="modalHiddenId">
                            <!-- Notes -->
                            <div class="mb-3">
                                <label for="notes" class="form-label">Review</label>
                                <textarea placeholder="Edit review..." class="form-control" id="modalTextarea" name="notes" rows="3"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Remove Log Modal --}}
        <div class="modal fade" id="removeLogModal" tabindex="-1" aria-labelledby="removeLogModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeLogModalLabel">Are you sure?</h5>
                        <button type="button" class="btn btn-gray" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>

                    <!-- Modal Body (Form) -->
                    <div class="modal-body">
                        <form action="{{ route('games.log.remove') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" id="removeLog_modalHiddenId">
                            <button class="btn btn-danger">Remove</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Comment Modal -->
        <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel">Editing...</h5>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>

                    <!-- Modal Body (Form) -->
                    <div class="modal-body">
                        <form action="{{ route('games.comment.edit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" id="comment_modalHiddenId">
                            <!-- Notes -->
                            <div class="mb-3">
                                <label for="notes" class="form-label">Comment</label>
                                <textarea placeholder="Edit review..." class="form-control" id="comment_modalTextarea" name="content"
                                    rows="3"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Remove Comment Modal --}}
        <div class="modal fade" id="removeCommentModal" tabindex="-1" aria-labelledby="removeCommentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeCommentModalLabel">Are you sure?</h5>
                        <button type="button" class="btn btn-gray" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>

                    <!-- Modal Body (Form) -->
                    <div class="modal-body">
                        <form action="{{ route('games.comment.remove') }}" method="POST">

                            @csrf
                            <input type="hidden" name="id" id="removeComment_modalHiddenId">
                            <button class="btn btn-danger btn-sm">
                                Remove</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <button class="back-to-top" title="Go to top">
            <i class="bi bi-arrow-up"></i>
        </button>

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

<script>
    function fillModalFields(button) {
        var text = button.getAttribute('data-text');
        var id = button.getAttribute('data-id');

        document.getElementById('modalTextarea').value = text;
        document.getElementById('modalHiddenId').value = id;
    }

    function fillCommentModalFields(button) {
        var text = button.getAttribute('comment-data-text');
        var id = button.getAttribute('comment-data-id');

        document.getElementById('comment_modalTextarea').value = text;
        document.getElementById('comment_modalHiddenId').value = id;
    }

    function fillRemoveLogModalFields(button) {
        var id = button.getAttribute('removeLog-data-id');

        document.getElementById('removeLog_modalHiddenId').value = id;
    }

    function fillRemoveCommentModalFields(button) {
        var id = button.getAttribute('removeComment-data-id');

        document.getElementById('removeComment_modalHiddenId').value = id;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.querySelector('.back-to-top');

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
