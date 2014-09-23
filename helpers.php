<?php

function getUserInfo($username = '.')
{
    if ($username == '.') {
        $username = Auth::user()->username;
    }

    $info = irc('nickserv')->getInfo($username);
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

    //Session::set('userInfo', $userInfo);

    return $userInfo;
}

function irc($module)
{
    $module = 'Cysha\Modules\Darchoods\Helpers\IRC\\'.$module;
    return with(new $module);
}

function profile($username)
{
    if (empty($username)) {
        return 'Unknown';
    }
    return $username; // no profile page yet

    return Cache::remember('users.'.$username, 60, function () use ($username) {
        $authModel = Config::get('auth.model');
        $objUser = $authModel::whereNick($username)->get()->first();
        if ($objUser !== null) {
            return array_get($objUser->transform(), 'link');
        }

        return $username;
    });
}


//functions for this page to work
function compare($x, $y)
{
    if($x['count'] == $y['count']){
        return 0;
    }else if($x['count'] < $y['count']){
        return 1;
    }else{
        return -1;
    }
}

function denora_colorconvert($body)
{
    $lines = explode("\n", $body);
    $out = '';

    foreach($lines as $line) {
        $line = nl2br($line);
        // replace control codes
        $line = preg_replace('/[\003](\d{0,2})(,\d{1,2})?([^\003\x0F]*)(?:[\003](?!\d))?/', '<font color="\\1">\\3</font>', $line);
        $line = preg_replace('/[\002]([^\002\x0F]*)(?:[\002])?/', '<font style="font-weight: bold;">$1</font>', $line);
        $line = preg_replace('/[\x1F]([^\x1F\x0F]*)(?:[\x1F])?/', '<span style="text-decoration: underline;">$1</span>', $line);
        $line = preg_replace('/[\x12]([^\x12\x0F]*)(?:[\x12])?/', '<span style="text-decoration: line-through;">$1</span>', $line);
        $line = preg_replace('/[\x16]([^\x16\x0F]*)(?:[\x16])?/', '<span style="font-style: italic;">$1</span>', $line);
        $line = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\S+]*(\?\S+)?)?)?)@', '<a href="$1" class="topic" rel="nofollow" target="_blank">$1</a>', e($line));
        // remove dirt
        $line = preg_replace('/[\x00-\x1F]/', '', $line);
        $line = preg_replace('/[\x7F-\xFF]/', '', $line);
        // append line
        if($line != '') { $out .= $line; }
    }

    return $out;
}

/**
 * Retreives part of a string
 *
 * @version 1.0
 * @since   1.0.0
 *
 * @param   string     $begin
 * @param   string     $end
 * @param   string     $contents
 *
 * @return  string
 */
function inBetween($begin, $end, $contents)
{
    $pos1 = strpos($contents, $begin);
    if($pos1 !== false){
        $pos1 += strlen($begin);
        $pos2 = strpos($contents, $end, $pos1);
        if ($pos2 !== false) {
            $substr = substr($contents, $pos1, $pos2 - $pos1);
            return $substr;
        }
    }
    return false;
}

/* Returns channel modes */
function chan_modes($query)
{
    $i = 0;
    $array = array();
    $cmodes = '';
    $j = 97;

    $array[$i] = (array)$query;
    for ($i = 0; $i <= count($array) - 1; $i++) {
        while ($j <= 122) {
            if (array_get($array, $i.'.mode_l'.chr($j)) == 'Y') {
                $cmodes .= chr($j);
            }
            if (array_get($array, $i.'.mode_u'.chr($j)) == 'Y') {
                $cmodes .= chr($j - 32);
            }
            $j++;
        }
        if (array_get($array, $i.'.mode_lf_data', null) !== null) {
            $cmodes .= ' '.$array[$i]['mode_lf_data'];
        }
        if (array_get($array, $i.'.mode_lj_data', null) !== null) {
            $cmodes .= ' '.$array[$i]['mode_lj_data'];
        }
        if (array_get($array, $i.'.mode_ll_data', null) !== null) {
            $cmodes .= ' '.$array[$i]['mode_ll_data'];
        }
        if (array_get($array, $i.'.mode_uf_data', null) !== null) {
            $cmodes .= ' '.$array[$i]['mode_uf_data'];
        }
        if (array_get($array, $i.'.mode_uj_data', null) !== null) {
            $cmodes .= ' '.$array[$i]['mode_uj_data'];
        }
        if (array_get($array, $i.'.mode_ul_data', null) !== null) {
            $cmodes .= ' '.$array[$i]['mode_ul_data'];
        }
    }
    if (!empty($cmodes)) {
        $cmodes = '+'.$cmodes;
    }
    return $cmodes;
}


function secs_to_h($secs)
{
    $units = array(
        'year'   => 365*24*3600,
        'month'  => 30*24*3600,
        'week'   => 7*24*3600,
        'day'    => 24*3600,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1,
    );

    // specifically handle zero
    if ($secs == 0) {
        return '0 seconds';
    }

    $s = '';

    foreach ($units as $name => $divisor) {
        if ($quot = intval($secs / $divisor)) {
            $s .= $quot.' '.$name;
            $s .= (abs($quot) > 1 ? 's' : '') . ', ';
            $secs -= $quot * $divisor;
        }
    }

    return substr($s, 0, -2);
}
