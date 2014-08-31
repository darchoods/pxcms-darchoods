<?php

Route::api(['version' => 'v1', 'prefix' => \Config::get('core::routes.paths.api', 'api')], function () use ($namespace) {
    $namespace .= '\Api\V1';

    Route::group(['prefix' => 'irc'], function () use ($namespace) {

        Route::group(['prefix' => 'channel'], function () use ($namespace) {
            Route::post('users', ['uses' => $namespace.'\ChannelController@postChannelUsers']);
            Route::post('view', ['uses' => $namespace.'\ChannelController@postChannelView']);
        });

        Route::get('servers', ['uses' => $namespace.'\ServerController@getServers']);
        Route::get('channels', ['uses' => $namespace.'\ChannelController@getChannels']);
    });

});

Route::group(['prefix' => \Config::get('core::routes.paths.api', 'api')], function () use ($namespace) {
    $namespace .= '\Module';

    Route::group(['prefix' => 'irc'], function () use ($namespace) {
        Route::get('/', ['as' => 'darchoods.pages.apidoc', 'uses' => 'Cysha\Modules\Darchoods\Controllers\Module\Pages\ApiController@getApi']);
    });
});
