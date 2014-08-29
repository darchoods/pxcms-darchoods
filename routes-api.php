<?php

Route::api(['version' => 'v1', 'prefix' => \Config::get('core::routes.paths.api', 'api')], function () use ($namespace) {
    $namespace .= '\Api\V1';

    Route::group(['prefix' => 'irc'], function () use ($namespace) {

        Route::get('servers', ['uses' => $namespace.'\ServerController@getServers']);
        Route::get('channels/{channel}', ['uses' => $namespace.'\ChannelController@getChannel']);
        Route::get('channels', ['uses' => $namespace.'\ChannelController@getChannels']);
        Route::get('clients', ['uses' => $namespace.'\UserController@getClients']);
    });

});
