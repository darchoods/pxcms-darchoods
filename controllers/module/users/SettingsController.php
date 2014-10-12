<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Users;

use Cysha\Modules\Darchoods as DH;
use Cysha\Modules\Auth as PXAuth;
use Auth;
use Former;
use Input;
use Redirect;
use Config;

class SettingsController extends BaseUserController
{
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->setDecorativeMode();

        $this->user = DH\Models\User::find(Auth::id());
    }

    public function getUserSettings()
    {
        $this->setTitle('User Settings <small>> User Information</small>');

        Former::populate($this->user);

        return $this->setView('settings.user', [
            'user' => $this->user,
        ], 'module');
    }

    public function postUserSettings()
    {
        $this->user->fill(Input::only(['first_name', 'last_name', 'use_nick', 'weather']));
        $save = $this->user->save();

        if ($save === false) {
            return Redirect::back()->withInput()->withError($save->getErrors());
        }

        return Redirect::back()->withInfo('User Details Updated');
    }

    public function getApiSettings()
    {
        $this->setTitle('User Settings <small>> Api Authentication</small>');

        $keys = $this->user->apiKey()->get();

        return $this->setView('settings.api', [
            'user' => $this->user,
            'keys' => $keys,
        ], 'module');
    }

    public function postApiSettings()
    {
        $input = Input::only(['description', 'expires_at']);

        if (!in_array($input['expires_at'], [6, 12, 18])) {
            $input['expires_at'] = 6;
        }

        $input['key']        = md5(Config::get('app.key').microtime(true));
        $input['expires_at'] = date('Y-m-d H:i:s', time()+(60*60*24*30*$input['expires_at']));

        $save = $this->user->apiKey()->create($input);

        if ($save === false) {
            return Redirect::back()->withInput()->withError($save->getErrors());
        }

        return Redirect::back()->withInfo('New API Key Generated');
    }

    public function deleteApiSettings()
    {
        $key_id = Input::get('key_id', false);
        if ($key_id === false) {
            return Redirect::back()->withWarning('API Key not found.');
        }
        $key = PXAuth\Models\ApiKey::find($key_id);
        if ($key === null) {
            return Redirect::back()->withWarning('API Key not found.');
        }

        if ($key->user_id !== Auth::id()) {
            return Redirect::back()->withWarning('API Key not found.');
        }

        $confirm = Input::get('confirm', '0');
        if ($confirm == '0') {
            return Redirect::back()->withWarning('You need to confirm the deletion.');
        }

        $key->delete();

        return Redirect::back()->withInfo('Api Key Deleted.');
    }
}
