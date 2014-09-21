<?php
    $navArr = [
        'User Settings' => 'darchoods.settings.user',
        'API Authentication' => 'darchoods.settings.api',
    ];
?>


<div class="list-group">
    @foreach($navArr as $title => $route)
    <a href="{{ URL::route($route) }}" class="list-group-item{{ Route::is($route) ? ' active' : '' }}">{{{ $title }}}</a>
    @endforeach
</div>
