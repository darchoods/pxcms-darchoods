<?php namespace Cysha\Modules\Darchoods\Models\Irc;

class User extends BaseModel
{
    public $table = 'user';

    public function transform()
    {

        if ($this->hiddenhostname == 'services.darkscience.net') {
            $this->ctcpversion = 'atheme';
        }

        return [
            'nick'         => (string) $this->nick,
            'username'     => (string) $this->username,
            'realname'     => (string) $this->realname,
            'mask'         => (string) $this->hiddenhostname,
            'account'      => (string) $this->account,
            'modes'        => (string) $this->modes,
            'userstring'   => (string) $this->username.'!'.$this->realname.'@'.$this->hiddenhostname,

            'online'       => (bool) ($this->online !== 'Y' ? false : true),
            'online_last'  => $this->lastquit ? strtotime($this->lastquit) : null,
            'identified'   => (bool) ($this->online !== 'Y' || empty($this->account) ? false : true),

            'away'         => (bool) ($this->away === 'Y' ? true : false),
            'away_msg'     => ($this->away == 'Y' ? (string) $this->away_msg : null),

            'country_code' => (string) $this->countrycode,
            'country'      => (string) $this->country,

            'version'      => (string) $this->ctcpversion,
            'server'       => (string) $this->server,
        ];
    }
}
