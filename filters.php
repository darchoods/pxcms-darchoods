<?php

Route::filter('auth.user', function () {
    if (Auth::guest()) {
        Session::flash('info', 'You need to login');

        return Redirect::guest(URL::route('pxcms.user.login'));
    }
});

Route::filter('auth.admin', function () {
    if (Auth::guest()) {
        Session::flash('info', 'You need to login');

        return Redirect::guest(URL::route('pxcms.user.login'));
    }

    if (Cookie::get('darchoods_token', null) === null) {
        Session::flash('info', 'You complete that action, you need to reauthenticate.');

        return Redirect::guest(URL::route('pxcms.user.login'));
    }

    if (Auth::user()->isAdmin() === false) {
        return Redirect::route('pxcms.pages.home')->withError(Lang::get('admin::admin.unauthorized'));
    }
});

// Check permissions when we start in the admin panel
Route::when(Config::get('core::routes.paths.user').'/*', 'auth.user');
