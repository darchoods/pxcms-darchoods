<?php


Route::group(['prefix' => 'admin'], function () use ($namespace) {
    $namespace .= '\Admin';

    Route::group(['prefix' => 'channels'], function () use ($namespace) {

        // URI: admin/channels/{channel}/(network|community|blacklist)
        Route::group(['prefix' => '{channel}'], function () use ($namespace) {
            Route::get('network', ['uses' => $namespace.'\ChannelController@markAsNetwork']);
            Route::get('community', ['uses' => $namespace.'\ChannelController@markAsCommunity']);
            Route::get('blacklist', ['uses' => $namespace.'\ChannelController@markAsBlacklist']);
        });

        Route::get('search.json', array('as' => 'admin.channels.ajax', 'uses' => $namespace.'\ChannelController@getDataTableJson', 'before' => 'permissions:admin.channels.index'));
        Route::get('/', array('as' => 'admin.channels.index', 'uses' => $namespace.'\ChannelController@getDataTableIndex', 'before' => 'permissions'));
    });

});
