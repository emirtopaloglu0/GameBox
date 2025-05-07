<x-app-layout>
    @php
        $like_counter = 0;
    @endphp

    <div class="container">
        <div class="card" style="margin: 10px;">
            <div class="card-body">
                @foreach ($reviews as $review)
                    <h6 class="mb-2">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ $review->user->username ?? 'Bilinmeyen Kullanıcı' }}
                    </h6>

                    {{-- Yıldızlı Puanlama --}}
                    <div class="mb-2">
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

                    {{-- Beğeni Sayısı  --}}
                    <p class="mb-0">
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
                            <i class="bi bi-heart-fill text-danger me-1">{{ $like_counter }} </i>
                        @else
                            <i class="bi bi-heart text-danger me-1">{{ $like_counter }} - No Likes Yet...
                            </i>
                        @endif
                    </p>

                    {{-- İnceleme Notu --}}
                    <div class="mb-2">
                        <p class="card-text lead">{{ $review->note }}</p>
                    </div>

                    <form method="POST" action="{{ route('games.log.comments') }}">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $review->id }}">

                        <input type="text" id="reply" name="reply" style="font-style: italic"
                            placeholder="Reply">

                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                @endforeach
            </div>
        </div>

        <h1 class="text-secondary" style="font-size: larger; margin-top: 20px;">Comments</h1>

        <hr>

        @foreach ($comments as $comment)
            <div class="card" style="margin: 10px">
                <div class="card-body">
                    <div class="flex flex-row">
                        <h6 class="basis-64">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ $comment->user->username ?? 'Bilinmeyen Kullanıcı' }}
                        </h6>

                        <div class="basis-64">
                            <p class="card-text lead">{{ $comment->content }}</p>
                        </div>


                        <div class="size-14 grow">
                            <div class="text-muted small">
                                {{ $comment->created_at->diffForHumans() }}</div>
                            @if ($comment->user_id == auth()->user()->id)
                                <div style="padding: 10px; display: flex">

                                    <button style="margin-right: 10px" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#removeCommentModal"
                                        removeComment-data-id="{{ $comment->id }}"
                                        onclick="fillRemoveCommentModalFields(this)">
                                        Remove
                                    </button>

                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#commentModal" comment-data-id="{{ $comment->id }}"
                                        comment-data-text="{{ e($comment->content) }}"
                                        onclick="fillCommentModalFields(this)">
                                        Edit
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

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
