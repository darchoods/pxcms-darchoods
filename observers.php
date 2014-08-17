<?php

Event::listen('darchoods.user.register', function ($info) {
    $info = array_get($info, '0', []);

    $userInfo = [
        'username'           => array_get($info, 'acct name'),
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
