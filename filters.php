<?php

Route::filter('auth.user', function () {
    if (Auth::guest()) {
        Session::flash('info', 'You need to login');

        return Redirect::guest(URL::route('pxcms.user.login'));
    }
});

// Check permissions when we start in the admin panel
Route::when(Config::get('core::routes.paths.user').'/*', 'auth.user');
