<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;

class ApiController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->setDecorativeMode();

    }

    public function getApi()
    {
        $this->setTitle('IRC Api Documentation');

        $api = [
            [
                'method'      => 'GET',
                'url'         => '/api/irc/servers',
                'description' => 'Get server information from the network.',
                'vars'        => [],
            ],
            [
                'method'      => 'GET',
                'url'         => '/api/irc/channels',
                'description' => 'Get a /list for the network.',
                'vars'        => [],
            ],
            [
                'method'      => 'POST',
                'url'         => '/api/irc/channel/view',
                'description' => 'Get all data on a channel.',
                'vars'        => [[
                    'var'   => 'channel',
                    'value' => 'eg #darchoods',
                    'use'   => 'Channel Name.'
                ]],
            ],
            [
                'method'      => 'POST',
                'url'         => '/api/irc/channel/users',
                'description' => 'Get all user data on a channel.',
                'vars'        => [[
                    'var'   => 'channel',
                    'value' => 'eg #darchoods',
                    'use'   => 'Channel Name.'
                ]],
            ]
        ];


        return $this->setView('pages.api.index', [
            'api' => $api,
        ], 'module');
    }

}
