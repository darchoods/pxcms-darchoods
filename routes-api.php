<?php

Route::api(['version' => 'v1', 'prefix' => \Config::get('core::routes.paths.api', 'api'), 'protected' => true], function () use ($namespace) {
    $namespace .= '\Api\V1';

    Route::group(['prefix' => 'irc'], function () use ($namespace) {

        Route::group(['prefix' => 'channel'], function () use ($namespace) {
            Route::post('view', $namespace.'\ChannelController@postChannelView');
            Route::post('users', $namespace.'\UserController@postChannelView');
        });

        Route::group(['prefix' => 'user'], function () use ($namespace) {
            Route::post('view', $namespace.'\UserController@postUserView');
        });

        Route::get('servers', $namespace.'\ServerController@getServers');
        Route::get('channels', $namespace.'\ChannelController@getChannels');
    });

});

Route::group(['prefix' => \Config::get('core::routes.paths.api', 'api')], function () use ($namespace) {
    $namespace .= '\Module';

    Route::group(['prefix' => 'irc'], function () use ($namespace) {
        Route::get('/', ['as' => 'darchoods.pages.apidoc', 'uses' => 'Cysha\Modules\Darchoods\Controllers\Module\Pages\ApiController@getApi']);
    });
});
