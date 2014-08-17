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
        Route::get('{username}', array('as' => 'pxcms.user.view', 'uses' => $namespace.'\DashboardController@getDashboard'));
    });

    Route::get('dashboard', array('as' => 'pxcms.user.dashboard', 'uses' => $namespace.'\DashboardController@getDashboard'));
    Route::get('/', function () {
        return Redirect::route('pxcms.user.dashboard');
    });

});

Route::get('/',             array('as' => 'pxcms.pages.home',              'uses' => 'App\Controllers\Site\PageController@viewNews'));
Route::get('news',          array('as' => 'darchoods.pages.news',          'uses' => 'App\Controllers\Site\PageController@viewNews'));
Route::get('heartbeat',     array('as' => 'darchoods.pages.heartbeat',     'uses' => 'App\Controllers\Site\PageController@viewHeartbeat'));
Route::get('channels',      array('as' => 'darchoods.pages.channels',      'uses' => 'App\Controllers\Site\PageController@viewChannels'));
Route::get('api',           array('as' => 'darchoods.pages.apidoc',        'uses' => 'App\Controllers\Site\PageController@viewApiDoc'));
Route::post('api.php',      array('as' => 'darchoods.pages.api',           'uses' => 'App\Controllers\Site\PageController@viewApi'));
