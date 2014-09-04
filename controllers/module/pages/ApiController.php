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

        $route = '/api/irc';
        $api = [
            [
                'method'      => 'GET',
                'url'         => $route.'/servers',
                'description' => 'Gets a network map.',
                'vars'        => [],
            ],
            [
                'method'      => 'GET',
                'url'         => $route.'/channels',
                'description' => 'Get a /list for the network.',
                'vars'        => [],
            ],
            [
                'method'      => 'POST',
                'url'         => $route.'/channel/view',
                'description' => 'Gets data about a channel.',
                'vars'        => [[
                    'var'   => 'channel',
                    'value' => 'eg #darchoods',
                    'use'   => 'Channel Name.'
                ]],
            ],
            [
                'method'      => 'POST',
                'url'         => $route.'/channel/users',
                'description' => 'Gets the userlist from #channel.',
                'vars'        => [[
                    'var'   => 'channel',
                    'value' => 'eg #darchoods',
                    'use'   => 'Channel Name.'
                ]],
            ],
            [
                'method'      => 'POST',
                'url'         => $route.'/user/view',
                'description' => 'Gather information about a username on the network.',
                'vars'        => [[
                    'var'   => 'username',
                    'value' => '',
                    'use'   => 'User to gain information about.'
                ]],
            ]
        ];

        $comment = 'Using the API you can access various statistics about the IRC Network. This api will return JSON regardless of headers set, this may change in the future if there is enough request for it.';
        return $this->setView('pages.api.index', [
            'api'     => $api,
            'comment' => $comment,
        ], 'module:core');
    }

}
