<?php

function getUserInfo($username = '.')
{
    if (Session::has('userInfo')) {
        return Session::get('userInfo');
    }

    $info = with(new Cysha\Modules\Darchoods\Helpers\IRC\NickServ)->getInfo($username);

    if (array_get($info, '0', false) == false) {
        return [];
    }

    $info = explode("\n", $info[1]);

    $userInfo = array();
    foreach ($info as $val) {
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
}
