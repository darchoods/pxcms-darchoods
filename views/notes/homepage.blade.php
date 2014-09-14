@if (count($posts))
    @foreach($posts as $post)

        @include(partial('news::news._row'), ['post' => $post->transform()])

    @endforeach
@endif
