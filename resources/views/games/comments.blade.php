<x-app-layout>
    @php
        $like_counter = 0;
        $last_likedReview = 0;
    @endphp

    <div class="container py-4">
    <!-- Reviews Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 text-primary"><i class="bi bi-chat-square-text me-2"></i>Reviews</h5>
            
            @foreach ($reviews as $review)
                <div class="review-item mb-4 pb-4 border-bottom">
                    <!-- User Info and Rating -->
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-circle fs-5 text-muted me-2"></i>
                        <h6 class="mb-0 me-3">{{ $review->user->username ?? 'Bilinmeyen Kullanıcı' }}</h6>
                        
                        <!-- Star Rating -->
                        <div class="rating-section">
                            @php
                                $stars = floor($review->rating);
                                $half = $review->rating - $stars >= 0.5 ? true : false;
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

                            <span class="text-muted ms-2">({{ number_format($review->rating, 2) }})</span>
                        </div>
                    </div>

                    <!-- Review Content -->
                    <div class="mb-3">
                        <p class="card-text">{{ $review->note }}</p>
                    </div>

                    <!-- Like Section -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="like-count">
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
                            
                            @if ($like_counter > 0)
                                <span class="text-danger"><i class="bi bi-heart-fill me-1"></i>{{ $like_counter }} likes</span>
                            @else
                                <span class="text-muted"><i class="bi bi-heart me-1"></i>No likes yet</span>
                            @endif
                        </div>

                        <!-- Like Button -->
                        <form action="{{ route('games.log.like') }}" method="POST">
                            @csrf
                            <input type="hidden" name="review_id" value="{{ $review->id }}">
                            <button type="submit" class="btn btn-sm {{ auth()->user()->log_likes()->where('log_id', $review->id)->exists() ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="bi bi-heart-fill"></i> {{ auth()->user()->log_likes()->where('log_id', $review->id)->exists() ? 'Liked' : 'Like' }}
                            </button>
                        </form>
                    </div>

                    <!-- Comment Form -->
                    <form method="POST" action="{{ route('games.log.comments') }}">
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
            @endforeach
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section">
        <h5 class="text-primary mb-4"><i class="bi bi-chat-left-text me-2"></i>Comments</h5>
        
        @foreach ($comments as $comment)
            <div class="card shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <!-- Comment Content -->
                        <div class="flex-grow-1 me-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-circle text-muted me-2"></i>
                                <h6 class="mb-0">{{ $comment->user->username ?? 'Bilinmeyen Kullanıcı' }}</h6>
                                <small class="text-muted ms-auto">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="card-text mb-0">{{ $comment->content }}</p>
                        </div>
                        
                        <!-- Comment Actions (if owner) -->
                        @if ($comment->user_id == auth()->user()->id)
                            <div class="btn-group btn-group-sm align-self-start">
                                <button class="btn btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#removeCommentModal"
                                    removeComment-data-id="{{ $comment->id }}"
                                    onclick="fillRemoveCommentModalFields(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button class="btn btn-outline-warning" data-bs-toggle="modal"
                                    data-bs-target="#commentModal" 
                                    comment-data-id="{{ $comment->id }}"
                                    comment-data-text="{{ e($comment->content) }}"
                                    onclick="fillCommentModalFields(this)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>



    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
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
                            <textarea placeholder="Edit review..." class="form-control" id="comment_modalTextarea" name="content" rows="3"></textarea>
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

</x-app-layout>

<script>
    function fillCommentModalFields(button) {
        var text = button.getAttribute('comment-data-text');
        var id = button.getAttribute('comment-data-id');

        document.getElementById('comment_modalTextarea').value = text;
        document.getElementById('comment_modalHiddenId').value = id;
    }

    function fillRemoveCommentModalFields(button) {
        var id = button.getAttribute('removeComment-data-id');

        document.getElementById('removeComment_modalHiddenId').value = id;
    }
</script>
