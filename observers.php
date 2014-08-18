<?php

Event::listen('darchoods.user.register', function ($info) {

    $userInfo = [
        'username'           => array_get($info, 'acct name'),
        'email'              => array_get($info, 'email'),
        'nicks'              => strpos(array_get($info, 'nicks'), ' ') ? explode(' ', array_get($info, 'nicks')) : [array_get($info, 'nicks')],
        'verified'           => true,
        'disabled'           => false,
        'created_at'         => strtotime(array_get($info, 'registered')),
        'updated_at'         => strtotime(array_get($info, 'last seen')),
    ];

    $model = Config::get('auth.model');
    $objUser = with(new $model)->findOrCreate([
        'username' => array_get($userInfo, 'username'),
    ], $userInfo);

    return $objUser;
});
