<?php

$namespace .= '\Module';

/** Auth Module Override **/

// Login & out
Route::get('login', array('as' => 'pxcms.user.login', 'uses' => $namespace.'\AuthController@getLogin'));
Route::post('login', $namespace.'\AuthController@postLogin');
Route::get('logout', array('as' => 'pxcms.user.logout', 'uses' => $namespace.'\AuthController@getLogout'));

// Register User - need to swap to irc implementation at some point
// Route::get('register', array('as' => 'pxcms.user.register', 'uses' => $namespace.'\AuthController@getRegister'));
// Route::post('register', $namespace.'\AuthController@postRegister');
// Route::get('registered', array('as' => 'pxcms.user.registered', 'uses' => $namespace.'\AuthController@getRegistered'));

// User Control Panel
Route::group(array('prefix' => Config::get('core::routes.paths.user')), function () use ($namespace) {

    Route::group(['prefix' => 'view'], function () use ($namespace) {
        Route::get('{username}', array('as' => 'pxcms.user.view', 'uses' => $namespace.'\PagesController@getDashboard'));
    });

    Route::group(['prefix' => 'settings'], function () use ($namespace) {
        $namespace .= '\Users';

        Route::get('api', array('as' => 'darchoods.settings.api', 'uses' => $namespace.'\SettingsController@getApiSettings'));
        Route::post('api', array('uses' => $namespace.'\SettingsController@postApiSettings'));
        Route::post('api/remove-key', array('as' => 'darchoods.settings.apiremove', 'uses' => $namespace.'\SettingsController@deleteApiSettings'));

        Route::get('/', array('as' => 'darchoods.settings.user', 'uses' => $namespace.'\SettingsController@getUserSettings'));
        Route::post('/', array('uses' => $namespace.'\SettingsController@postUserSettings'));
    });

    Route::get('dashboard', array('as' => 'pxcms.user.dashboard', 'uses' => $namespace.'\PagesController@getDashboard'));
    Route::get('/', function () {
        return Redirect::route('pxcms.user.dashboard');
    });

});

/** News Module Override **/
Route::group(['prefix' => 'news'], function () use ($namespace) {

    Route::model('newsid', 'Cysha\Modules\News\Models\News');
    Route::get('{newsid}-{slug}', ['as' => 'pxcms.news.view', 'uses' => $namespace.'\Pages\NewsController@getNewsById']);
});

/** IRPG **/
Route::group(['prefix' => 'irpg'], function () use ($namespace) {
    $namespace .= '\Irpg';

    Route::get('stats', ['as' => 'darchoods.irpg.stats', 'uses' => $namespace.'\PagesController@getStats']);
    Route::get('quests', ['as' => 'darchoods.irpg.quests', 'uses' => $namespace.'\PagesController@getQuests']);

    Route::get('players/search.json', ['as' => 'darchoods.irpg.leaderboard-ajax', 'uses' => $namespace.'\LeaderboardController@getDataTableJson']);
    Route::get('players', ['as' => 'darchoods.irpg.leaderboard', 'uses' => $namespace.'\LeaderboardController@getDataTableIndex']);
});

Route::get('/', ['as' => 'pxcms.pages.home', 'uses' => $namespace.'\Pages\NewsController@getNews']);


// Route::get('qdb', array('as' => 'darchoods.qdb.index', 'uses' => $namespace.'\PagesController@getNews'));
Route::get('heartbeat', array('as' => 'darchoods.pages.heartbeat', 'uses' => $namespace.'\Pages\HeartbeatController@getIndex'));
Route::get('heartbeat/population.csv', array('uses' => $namespace.'\Pages\HeartbeatController@getCountryStats'));
Route::get('channels', array('as' => 'darchoods.pages.channels', 'uses' => $namespace.'\Pages\ChannelController@getIndex'));
