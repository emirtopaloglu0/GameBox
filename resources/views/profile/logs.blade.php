<x-app-layout>
    @php
        $counter = 0;
        $last_review = 0;
        $comment_counter = 0;
        $like_counter = 0;
        $last_likedReview = 0;
    @endphp
    <div class="container py-8">
        <div class="d-flex  justify-content-evenly mb-4">
            <h2 class="h2">Your Reviews</h2>
        </div>
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
        @forelse($reviews as $review)

            <div class="">
                <div class="">
                    @php
                        $last_review++;
                    @endphp
                    @if ($last_review > 1)
                        @php
                            $comment_counter = 0;
                            $last_review = 0;
                        @endphp
                    @endif

                    <div class="card mb-3 shadow-sm">
                        {{-- Remove Edit Log Buttons --}}

                        @if ((int) $review['user_id'] == auth()->user()->id)
                            <div style="padding: 10px; display: flex">
                                <button style="margin-right: 10px" class="btn btn-dark" data-bs-toggle="modal"
                                    data-bs-target="#removeLogModal"
                                    removeLog-data-id="{{ (int) $review['review_id'] }}"
                                    onclick="fillRemoveLogModalFields(this)">
                                    Remove
                                </button>

                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ (int) $review['review_id'] }}" data-text="{{ e($review['note']) }}"
                                    onclick="fillModalFields(this)">
                                    Edit
                                </button>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="row">
                                <!-- Profil Resmi ve Temel Bilgiler -->
                                <div class="profile-img col-md-2">
                                    @if (isset($review['cover']['url']))
                                        @php
                                            $highResCover = str_replace(
                                                't_thumb',
                                                't_cover_big',
                                                $review['cover']['url'],
                                            );
                                        @endphp
                                        <a href="{{ route('games.show', $review['id']) }}">
                                            <img class="img-fluid rounded mb-3" src="https:{{ $highResCover }}"
                                                alt="{{ $review['name'] ?? 'Kapak Yok' }}">
                                        </a>
                                    @else
                                        <div class="bg-light p-3 rounded text-center mb-3">
                                            <i class="bi bi-image text-muted fs-1"></i>
                                            <p class="small text-muted mt-2">No cover</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- İnceleme İçeriği -->
                                <div class="col-md-8">
                                    <!-- Kullanıcı Bilgileri -->
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <h6 class="mb-0">{{ $review['username'] ?? 'Bilinmeyen Kullanıcı' }}</h6>
                                        <span class="text-muted small ms-auto">
                                            {{ \Carbon\Carbon::parse($review['updated_at'])->format('d M Y, H:i') }}
                                        </span>
                                    </div>

                                    <!-- Yıldız Değerlendirme -->
                                    <div class="mb-3">
                                        @php
                                            $stars = floor((float) $review['rating']);
                                            $half = (float) $review['rating'] - $stars >= 0.5;
                                        @endphp

                                        @for ($i = 0; $i < $stars; $i++)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @endfor

                                        @if ($half)
                                            <i class="bi bi-star-half text-warning"></i>
                                        @endif

                                        @for ($i = $stars + ($half ? 1 : 0); $i < 5; $i++)
                                            <i class="bi bi-star text-warning"></i>
                                        @endfor

                                        <span
                                            class="text-muted ms-2">({{ number_format((float) $review['rating'], 2) }})</span>
                                    </div>

                                    <!-- İnceleme Metni -->
                                    <div class="mb-3">
                                        <p class="lead">{{ $review['note'] }}</p>
                                    </div>

                                    <!-- Beğeni Sayısı -->
                                    <div class="mb-3">
                                        @php
                                            $like_counter = 0;
                                            foreach ($logLikes as $like) {
                                                if ($like->log_id == (int) $review['review_id']) {
                                                    $like_counter++;
                                                }
                                            }
                                        @endphp

                                        @if ($like_counter > 0)
                                            <span class="text-danger"><i class="bi bi-heart-fill"></i>
                                                {{ $like_counter }}</span>
                                        @else
                                            <span class="text-muted"><i class="bi bi-heart"></i> No likes yet</span>
                                        @endif
                                    </div>

                                    <!-- Yorumlar -->
                                    <div class="mb-3">
                                        <h6 class="text-secondary mb-2">Comments</h6>
                                        @php $comment_counter = 0; @endphp
                                        <div class="border rounded p-2 bg-light">
                                            @foreach ($comments as $comment)
                                                @if ((int) $review['review_id'] == $comment->parent_id && $comment_counter < 2)
                                                    <div class="mb-2 pb-2 border-bottom">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>{{ $comment->user->username }}:</strong>
                                                                <span>{{ $comment->content }}</span>
                                                            </div>
                                                            <small
                                                                class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                        </div>

                                                        @if ($comment->user_id == auth()->user()->id)
                                                            <div class="mt-1 d-flex">
                                                                <button class="btn btn-sm btn-outline-secondary me-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#commentModal"
                                                                    comment-data-id="{{ $comment->id }}"
                                                                    comment-data-text="{{ e($comment->content) }}"
                                                                    onclick="fillCommentModalFields(this)">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#removeCommentModal"
                                                                    removeComment-data-id="{{ $comment->id }}"
                                                                    onclick="fillRemoveCommentModalFields(this)">
                                                                    <i class="bi bi-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @php $comment_counter++; @endphp
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Yorum Formu -->
                                    <form method="POST" action="{{ route('games.log.comments') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="parent_id"
                                            value="{{ (int) $review['review_id'] }}">
                                        <div class="input-group">
                                            <input type="text" name="reply" class="form-control"
                                                placeholder="Write a comment...">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send"></i> Send
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Like Butonu -->
                                <div class="col-md-2 text-end">
                                    <form action="{{ route('games.log.like') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="review_id"
                                            value="{{ (int) $review['review_id'] }}">
                                        <button type="submit"
                                            class="btn btn-sm {{ auth()->user()->log_likes()->where('log_id', (int) $review['review_id'])->exists() ? 'btn-danger' : 'btn-outline-danger' }}">
                                            @if (auth()->user()->log_likes()->where('log_id', (int) $review['review_id'])->exists())
                                                <i class="bi bi-heart-fill"></i> Liked
                                            @else
                                                <i class="bi bi-heart"></i> Like
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('games.comments', (int) $review['review_id']) }}" class="btn btn-light">See
                            All
                            Comments</a>
                    </div>
                @empty
                    <p class="text-muted">There are no reviews yet...</p>
        @endforelse
    </div>
</x-app-layout>
