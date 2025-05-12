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
                                    data-rating="{{ $review['rating'] }}" onclick="fillModalFields(this)">
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
                        <!-- Rating -->
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
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
</x-app-layout>

<script>
    // Kullanıcı yeni bir yıldız seçerse input'u güncelle
    document.addEventListener('DOMContentLoaded', () => {
        const stars = document.querySelectorAll('#editModal .star');
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = parseInt(star.getAttribute('data-value'));
                document.getElementById('ratingInput').value = value;

                // Seçimi güncelle
                stars.forEach(s => {
                    if (parseInt(s.getAttribute('data-value')) <= value) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    });


    function fillModalFields(button) {
        var text = button.getAttribute('data-text');
        var id = button.getAttribute('data-id');
        var rating = button.getAttribute('data-rating');
        // Yıldızları işaretle
        const stars = document.querySelectorAll('#editModal .star');
        stars.forEach(star => {
            const value = parseInt(star.getAttribute('data-value'));
            if (value <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });

        document.getElementById('modalTextarea').value = text;
        document.getElementById('modalHiddenId').value = id;
        document.getElementById('modalRating').value = rating;

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
