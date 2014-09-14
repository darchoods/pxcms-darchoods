<div class="panel panel-default">
    <div class="panel-heading">
        {{ array_get($post, 'title') }} <small>by {{ array_get($post, 'author.name') }} </small>
    </div>
    <div class="panel-body">
        {{ array_get($post, 'content') }}
    </div>
</div>
