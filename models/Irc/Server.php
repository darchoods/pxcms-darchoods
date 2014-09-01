<?php namespace Cysha\Modules\Darchoods\Models\Irc;

class Server extends BaseModel
{
    public $table = 'server';

    public function transform()
    {
        return [
            'name'   => (string) $this->server,
            'status' => (bool) ($this->online == 'Y' ? true : false),
            'uptime' => (int) $this->uptime,
            'users'  => [
                'current' => (int) $this->currentusers,
                'peak'    => (int) $this->maxusers,
                'opers'   => (int) $this->opers,
            ],
            'country' => [
                'code' => (string) $this->countrycode,
                'name' => (string) $this->country,
            ]
        ];
    }
}
