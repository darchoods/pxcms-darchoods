<?php namespace Cysha\Modules\Darchoods\Models;

use Lang;
use Config;

class User extends \Cysha\Modules\Auth\Models\User
{
    use \Cysha\Modules\Core\Traits\SelfValidationTrait,
        \Cysha\Modules\Core\Traits\LinkableTrait{
        \Cysha\Modules\Core\Traits\SelfValidationTrait::boot as validationBoot;
    }

    protected static $rules = [
        'creating' => [
            'username' => 'required|unique:users,username',
        ],
        'updating' => [
            'username'   => 'unique:users,username,:id:',
            'first_name' => 'alpha_dash',
            'last_name'  => 'alpha_dash',
            'use_nick'   => 'integer',
        ],
    ];

    protected static $messages;
    protected $identifiableName = 'screenname';
    protected $appends = ['screenname'];

    protected $link = [
        'route'      => 'pxcms.user.view',
        'attributes' => ['name' => 'screenname'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->linkableConstructor();

        self::$messages = [
            'username.unique' => Lang::get('auth::register.username'),
            'email.unique'    => Lang::get('auth::register.email'),
            'password'        => Lang::get('auth::register.password'),
            'password.min'    => Lang::get('auth::register.password.min'),
        ];
    }

    public static function boot()
    {
        static::validationBoot();
    }

    public function scopeWhereNick($query, $username)
    {
        return $query->whereUsername($username)->orWhere('nicks', 'LIKE', '%"'.$username.'"%');
    }

    public function getScreennameAttribute()
    {
        if ($this->use_nick == '-1' && !empty($this->first_name) && !empty($this->last_name)) {
            return $this->fullName;
        }

        if (!isset($this->nicks) || !count($this->nicks)) {
            return $this->username;
        }

        return array_get($this->nicks, $this->use_nick, $this->username);
    }

    public function getNicksAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setNicksAttribute($value)
    {
        $this->attributes['nicks'] = json_encode($value);
    }

    public function transform()
    {
        $return = parent::transform();
        return array_merge($return, [
            'name'       => (string) $this->screenname,
        ]);
    }
}
