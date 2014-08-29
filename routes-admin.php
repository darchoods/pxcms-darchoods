<?php


Route::group(array('prefix' => Config::get('core::routes.paths.admin')), function () use ($namespace) {
    $namespace .= '\Admin';

    Route::group(['prefix' => 'channels'], function () use ($namespace) {

        // URI: admin/channels/{channel}/(network|default|blacklist)
        Route::group(['prefix' => '{channel}'], function () use ($namespace) {
            Route::get('network', ['uses' => $namespace.'\ChannelController@markAsNetwork']);
            Route::get('blacklist', ['uses' => $namespace.'\ChannelController@markAsBlacklist']);
            Route::get('default', ['uses' => $namespace.'\ChannelController@markAsDefault']);
        });

        Route::get('search.json', array('as' => 'admin.channels.ajax', 'uses' => $namespace.'\ChannelController@getDataTableJson', 'before' => 'permissions:admin.channels.index'));
        Route::get('/', array('as' => 'admin.channels.index', 'uses' => $namespace.'\ChannelController@getDataTableIndex', 'before' => 'permissions'));
    });

    Route::group(['prefix' => 'servers'], function () use ($namespace) {

        // URI: admin/servers/{channel}/(network|default|blacklist)
        Route::group(['prefix' => '{channel}'], function () use ($namespace) {
            Route::get('network', ['uses' => $namespace.'\ServerController@markAsNetwork']);
            Route::get('blacklist', ['uses' => $namespace.'\ServerController@markAsBlacklist']);
            Route::get('default', ['uses' => $namespace.'\ServerController@markAsDefault']);
        });

        Route::get('search.json', array('as' => 'admin.servers.ajax', 'uses' => $namespace.'\ServerController@getDataTableJson', 'before' => 'permissions:admin.servers.index'));
        Route::get('/', array('as' => 'admin.servers.index', 'uses' => $namespace.'\ServerController@getDataTableIndex', 'before' => 'permissions'));
    });

});
