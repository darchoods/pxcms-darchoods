<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

use Cysha\Modules\Auth as PXAuth;
use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Auth;
use Input;
use Cookie;
use Redirect;
use URL;
use Config;
use Session;
use Lang;
use NickServ;

class AuthController extends BaseController
{

    public function getLogin()
    {
        if (!Auth::guest()) {
            return Redirect::route('pxcms.user.dashboard');
        }

        return $this->setView('partials.core.login', array(
        ), 'theme');
    }

    public function postLogin()
    {
        $input = Input::only('email', 'password');

        // try auth with nickserv with the credentials the user have provided
        $response = with(new IRC\NickServ)->login($input['email'], $input['password']);

        // if they are incorrect, throw them back
        if ($response[0] !== true) {
            return Redirect::route('pxcms.user.login')->withError(Lang::get('core::auth.user.notfound'));
        }

        // throw the token into a session
        Cookie::queue('darchoods_token', $response[1], 60);

        // if they passed, try looking for the user in the database
        $userModel = Config::get('auth.model');
        $objUser = $userModel::whereUsername($input['email'])->get()->first();

        $userInfo = ['info' => getUserInfo($input['email'])];
        // if we cant find the user, register their details in the db
        if (!count($objUser)) {
            $objUser = \Event::fire('darchoods.user.register', $userInfo);
            $objUser = array_get($objUser, '0', []);
        }
        \Event::fire('darchoods.user.update', $userInfo);

        if (!count($objUser)) {
            return Redirect::route('pxcms.user.login')->withError(Lang::get('core::auth.user.notfound'));
        }

        // actually log em in
        Auth::login($objUser, false);

        return Redirect::intended(URL::route(Config::get('auth::user.redirect_to')))
            ->withCookie($tokenCookie)
            ->withInfo(Lang::get('darchoods::auth.user.welcome', [$objUser->username]));
    }

    public function getLogout()
    {
        // if we have an active session
        if (Auth::check()) {
            // remove the atheme cookie if we have one
            if (($token = Cookie::get('darchoods.token', null)) !== null) {
                with(new IRC\NickServ)->logout(Auth::user()->username, $token);
                Cookie::forget('darchoods.token');
            }

            // log the user out and flush the sessions
            Auth::logout();
            Session::flush();

            // and go home
            return Redirect::route('pxcms.pages.home')->withInfo('Successfully logged out.');
        }

        return Redirect::back();
    }

    /**
     *
     * Account Activation
     *
     */
    public function getActivate($code)
    {
        if ($user->isActive()) {
            return Redirect::to('/')->withWarning(Lang::get('core::auth.user.alreadyactive'));
        }

        if ($user->activate($code)) {
            Auth::login($user);

            return Redirect::route('pxcms.user.dashboard')->withInfo(Lang::get('core::auth.user.activated'));
        } else {
            return Redirect::to('/')->withError(Lang::get('core::auth.user.invalidkey'));
        }
    }

    /**
     *
     * Register
     *
     */
    public function getRegister()
    {
        if (!Auth::guest()) {
            return Redirect::route('pxcms.user.dashboard');
        }

        return $this->setView('partials.core.register', array(
        ), 'theme');
    }

    public function getRegistered()
    {
        if (!Auth::guest()) {
            return Redirect::route('pxcms.user.dashboard');
        }

        $user_id = Session::get('user') ?: 0;
        if ($user_id == 0) {
            return Redirect::route('pxcms.pages.home');
        }

        $objUser = PXAuth\Models\User::findOrFail($user_id);
        if ($objUser === null) {
            return Redirect::route('pxcms.pages.home');
        }

        return $this->setView('partials.core.registered', array(
            'user' => $objUser
        ), 'theme');
    }

    public function postRegister()
    {
        $objUser = new PXAuth\Models\User;
        $objUser->hydrateFromInput();

        if (Config::get('users::user.require_activating') === false) {
            $objUser->verified = 1;
        }

        if ($objUser->save()) {
            Event::fire('user.created', array($objUser));

            return Redirect::route('user.registered')->withUser($objUser->id);
        }

        Input::flash();

        return Redirect::route('user.register')->withErrors($objUser->getErrors());
    }
}
