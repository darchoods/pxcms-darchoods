<?php

Event::listen('darchoods.user.register', function ($info) {
    if (empty($info)) {
        return false;
    }

    $userInfo = [
        'email'              => array_get($info, 'email'),
        'nicks'              => strpos(array_get($info, 'nicks'), ' ') ? explode(' ', array_get($info, 'nicks')) : [array_get($info, 'nicks')],
        'verified'           => true,
        'disabled'           => false,
        'created_at'         => ($time = array_get($info, 'registered', null)) === null ? time() :strtotime(array_get($info, 'registered')),
        'updated_at'         => ($time = array_get($info, 'last seen', null)) === null ? time() :strtotime(array_get($info, 'last seen')),
    ];

    $model = Config::get('auth.model');
    $objUser = with(new $model)->findOrCreate([
        'username' => array_get($info, 'acct name'),
    ], $userInfo);

    return $objUser;
});

Event::listen('darchoods.user.update', function ($info) {
    // update the alias list, email
    $userInfo = [
        'email'              => array_get($info, 'email'),
        'nicks'              => strpos(array_get($info, 'nicks'), ' ') ? explode(' ', array_get($info, 'nicks')) : [array_get($info, 'nicks')],
    ];

    $model = Config::get('auth.model');
    $objUser = with(new $model)->findOrCreate([
        'username' => array_get($info, 'acct name'),
    ], $userInfo);

    if (!count($objUser)) {
        return false;
    }

    // user is an oper, give him admin privs
    if (array_get($info, 'oper class', false) !== false) {
        $objRoleSA = \Cysha\Modules\Auth\Models\Role::whereName(Config::get('auth::roles.super_group_name'))->first();
        $save = $objUser->roles()->sync(array($objRoleSA->id));
    }
});
