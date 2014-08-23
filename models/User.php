<?php namespace Cysha\Modules\Darchoods\Models;

use \Toddish\Verify\Models\User as VerifyVersion;
use Lang;
use Config;

class User extends VerifyVersion
{
    use \Cysha\Modules\Core\Traits\SelfValidationTrait,
        \Cysha\Modules\Core\Traits\LinkableTrait,
        \Venturecraft\Revisionable\RevisionableTrait{
        \Cysha\Modules\Core\Traits\SelfValidationTrait::boot as validationBoot;
        \Venturecraft\Revisionable\RevisionableTrait::boot as revisionableBoot;
    }

    protected $revisionEnabled = false;

    protected static $rules = [
        'creating' => [
            'username' => 'required|unique:users,username',
        ],
        'updating' => [
            'username' => 'unique:users,username,:id:',
        ],
    ];

    protected static $messages;
    protected $fillable = ['username', 'first_name', 'last_name', 'email', 'nicks', 'verified', 'disabled', 'created_at', 'updated_at'];
    protected $identifiableName = 'username';

    protected $link = [
        'route'      => 'pxcms.user.view',
        'attributes' => ['name' => 'username'],
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
        static::revisionableBoot();
    }

    public function roles()
    {
        return $this->belongsToMany('\Cysha\Modules\Auth\Models\Role', $this->prefix.'role_user')->withTimestamps();
    }

    // public function permissions()
    // {
    //     return $this->hasManyThrough(Config::get('verify::permission_model'), Config::get('verify::group_model'));
    // }

    public function getScreennameAttribute($value)
    {
        $nick = $value;
        if ($this->use_nick == '-1' && !empty($this->first_name) && !empty($this->last_name)) {
            return $this->name;
        }

        if (!isset($this->nicks) || !count($this->nicks)) {
            return $nick;
        }

        return array_get($this->nicks, $this->use_nick, $nick);
    }

    public function getNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    public function getAvatarAttribute($val)
    {
        if (empty($val) || $val == 'gravatar') {
            return sprintf('http://www.gravatar.com/avatar/%s.png', md5($this->attributes['email']));
        }

        return $val;
    }

    public function getNicksAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setNicksAttribute($value)
    {
        $this->attributes['nicks'] = json_encode($value);
    }

    public function scopeFindOrCreate($query, array $where, array $attrs = array())
    {
        $objModel = static::firstOrCreate($where);

        if (count($attrs) > 0) {
            $objModel->fill($attrs);

            if (count($objModel->getDirty())) {
                $objModel->save();
            }
        }

        return $objModel;
    }

    public function isAdmin()
    {
        return $this->is(array(Config::get('auth::roles.super_group_name'), Config::get('auth::roles.admin_group_name')));
    }

    public function transform()
    {
        return [
            'id'         => (int)$this->id,
            'username'   => (string) $this->username,
            'name'       => (string) $this->name,
            'href'       => (string) $this->makeLink(true),
            'link'       => (string) $this->makeLink(false),

            'email'      => (string) $this->email,
            'avatar'     => (string) $this->avatar,


            'registered' => date_array($this->created_at),
        ];
    }
}