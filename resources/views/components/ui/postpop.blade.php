@props(['post_id', 'label'])

<a href="{{ route('r4.guest.posts.show', ['post' => $post_id]) }}" class="post-pop" data-id="{{$post_id}}" data-bs-toggle="modal" data-bs-target="#postViewModal">{{$label}}</a>

