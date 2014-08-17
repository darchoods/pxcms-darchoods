<?php

function getUserInfo()
{
    return Session::get('userInfo', []);
}
