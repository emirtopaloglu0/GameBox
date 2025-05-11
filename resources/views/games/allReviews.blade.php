<x-app-layout>
    @php
        $counter = 0;
        $last_review = 0;
        $comment_counter = 0;
        $like_counter = 0;
        $last_likedReview = 0;
        $review_counter = 0;
    @endphp
    <div class="container py-8">
        <div class="d-flex  justify-content-evenly mb-4">
            <h2 class="h2">Reviews</h2>
        </div>
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
            <div class="card mb-4 border-0 shadow-lg">
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
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
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
                                                                data-bs-toggle="modal" data-bs-target="#commentModal"
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
                    <a href="{{ route('games.comments', $review->id) }}" class="btn btn-outline-primary btn-sm">
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
</x-app-layout>
