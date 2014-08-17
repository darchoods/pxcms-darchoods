<?php

Event::listen('darchoods.user.info', function ($username) {
    if (Session::has('userInfo')) {
        return Session::get('userInfo');
    }

    $info = with(new Cysha\Modules\Darchoods\Helpers\IRC\NickServ)->getInfo($username);

    if (array_get($info, '0', false) == false) {
        return [];
    }

    $info = explode("\n", $info[1]);

    $userInfo = array();
    foreach ($info as $idx => $val) {
        if (!preg_match('~^(.*) :\s(.*)\s\((.*)\)$~U', $val, $m) && !preg_match('~^(.*) : (.*)$~U', $val, $m)) {
            continue;
        }

        $userInfo[strtolower(trim($m[1]))] = $m[2];
    }

    if (preg_match('~\(account (.*)\)~U', $info[0], $m)) {
        $userInfo['acct name'] = $m[1];
    }

    Session::set('userInfo', $userInfo);

    return $userInfo;
});

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
