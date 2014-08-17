<?php

$namespace .= '\Module';

/** Auth Module Override **/

// Login & out
Route::get('login', array('as' => 'pxcms.user.login', 'uses' => $namespace.'\AuthController@getLogin'));
Route::post('login', $namespace.'\AuthController@postLogin');
Route::get('logout', array('as' => 'pxcms.user.logout', 'uses' => $namespace.'\AuthController@getLogout'));

// Register User
Route::get('register', array('as' => 'pxcms.user.register', 'uses' => $namespace.'\AuthController@getRegister'));
Route::post('register', $namespace.'\AuthController@postRegister');
Route::get('registered', array('as' => 'pxcms.user.registered', 'uses' => $namespace.'\AuthController@getRegistered'));

// User Control Panel
Route::group(array('prefix' => Config::get('core::routes.paths.user')), function () use ($namespace) {

    Route::group(['prefix' => 'view'], function () use ($namespace) {
        Route::get('{username}', array('as' => 'pxcms.user.view', 'uses' => $namespace.'\PagesController@getDashboard'));
    });

    Route::get('dashboard', array('as' => 'pxcms.user.dashboard', 'uses' => $namespace.'\PagesController@getDashboard'));
    Route::get('/', function () {
        return Redirect::route('pxcms.user.dashboard');
    });

});

Route::get('/',             array('as' => 'pxcms.pages.home',              'uses' => $namespace.'\PageController@viewNews'));
Route::get('news',          array('as' => 'darchoods.pages.news',          'uses' => $namespace.'\PageController@viewNews'));
Route::get('heartbeat',     array('as' => 'darchoods.pages.heartbeat',     'uses' => $namespace.'\PageController@viewHeartbeat'));
Route::get('channels',      array('as' => 'darchoods.pages.channels',      'uses' => $namespace.'\PageController@viewChannels'));
Route::get('api',           array('as' => 'darchoods.pages.apidoc',        'uses' => $namespace.'\PageController@viewApiDoc'));
Route::post('api.php',      array('as' => 'darchoods.pages.api',           'uses' => $namespace.'\PageController@viewApi'));
