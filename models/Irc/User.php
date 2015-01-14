<?php namespace Cysha\Modules\Darchoods\Models\Irc;

class User extends BaseModel
{
    public $table = 'user';


    public function getCtcpversionAttribute($value)
    {
        if ($this->hiddenhostname == 'services.darkscience.net') {
            return 'atheme';
        }

        return $value;
    }

    public function getModesAttribute()
    {
        $clientModes = null;
        if ($this->mode_ub == 'Y') { $clientModes .= 'B'; }
        if ($this->mode_lc == 'Y') { $clientModes .= 'c'; }
        if ($this->mode_ld == 'Y') { $clientModes .= 'd'; }
        if ($this->mode_lg == 'Y') { $clientModes .= 'g'; }
        if ($this->mode_ug == 'Y') { $clientModes .= 'G'; }
        if ($this->mode_lh == 'Y') { $clientModes .= 'h'; }
        if ($this->mode_uh == 'Y') { $clientModes .= 'H'; }
        if ($this->mode_li == 'Y') { $clientModes .= 'i'; }
        if ($this->mode_ui == 'Y') { $clientModes .= 'I'; }
        if ($this->mode_lo == 'Y') { $clientModes .= 'o'; }
        if ($this->mode_uq == 'Y') { $clientModes .= 'Q'; }
        if ($this->mode_ur == 'Y') { $clientModes .= 'R'; }
        if ($this->mode_lr == 'Y') { $clientModes .= 'r'; }
        if ($this->mode_ls == 'Y') { $clientModes .= 's'; }
        if ($this->mode_us == 'Y') { $clientModes .= 'S'; }
        if ($this->mode_uw == 'Y') { $clientModes .= 'W'; }
        if ($this->mode_lx == 'Y') { $clientModes .= 'x'; }

        return $clientModes;
    }

    public function getChannelModesAttribute()
    {
        $channelModes = null;
        if ($this->mode_lq == 'Y') { $channelModes .= 'q'; }
        if ($this->mode_la == 'Y') { $channelModes .= 'a'; }
        if ($this->mode_lo == 'Y') { $channelModes .= 'o'; }
        if ($this->mode_lh == 'Y') { $channelModes .= 'h'; }
        if ($this->mode_lv == 'Y') { $channelModes .= 'v'; }

        return $channelModes;
    }

    public function transform()
    {
        return [
            'nick'          => (string) $this->nick,
            'username'      => (string) $this->username,
            'realname'      => (string) $this->realname,
            'mask'          => (string) $this->hiddenhostname,
            'account'       => (string) $this->account,
            'modes'         => (string) $this->modes,
            'channel_modes' => (string) $this->channelModes,
            'userstring'    => (string) $this->username.'!'.$this->realname.'@'.$this->hiddenhostname,

            'online'        => (bool) ($this->online !== 'Y' ? false : true),
            'online_last'   => $this->lastquit ? strtotime($this->lastquit) : null,
            'identified'    => (bool) ($this->online !== 'Y' || empty($this->account) ? false : true),
            'is_bot'        => (bool) (strpos($this->modes, 'B') === false ? false : true),

            'away'          => (bool) ($this->away === 'Y' ? true : false),
            'away_msg'      => ($this->away == 'Y' ? (string) $this->away_msg : null),

            'country_code'  => (string) $this->countrycode,
            'country'       => (string) $this->country,

            'version'       => (string) $this->ctcpversion,
            'server'        => (string) $this->server,
        ];
    }
}
