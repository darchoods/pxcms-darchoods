<?php namespace Cysha\Modules\Darchoods\Controllers\Api\V1;

use Cysha\Modules\Core\Controllers\BaseApiController as BAC;
use Cysha\Modules\Darchoods\Repositories\Irc\User\RepositoryInterface as IrcUserRepository;
use Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface as IrcChannelRepository;
use Input;

class UserController extends BAC
{
    public function __construct(IrcUserRepository $user, IrcChannelRepository $channel)
    {
        parent::__construct();
        $this->ircUser = $user;
        $this->ircChannel = $channel;
    }

    public function getClients()
    {

        $clients = $this->ircUser->getClientVersions();
        if (!count($clients)) {
            return [];
        }

        $output = ['count' => 0, 'clients' => []];
        foreach ($clients as $client) {
            list($ident, $count) = $client;
            $output['clients'][] = [$ident, $count];
        }
        $output['count'] = count($output['clients']);


        return $this->sendResponse('ok', 200, $output);
    }

    public function postUserView()
    {
        $username = Input::get('username', false);
        if ($username === false) {
            return $this->sendError('No username given.');
        }

        $data = [];
        $data['user'] = $this->ircUser->getUserByNick($username);
        unset($data['user']['modes']);
        $data['channels'] = $this->ircChannel->getUsersChannels($username);

        return $this->sendResponse('ok', 200, $data);
    }

    public function postChannelView()
    {
        $channelName = Input::get('channel', false);
        if ($channelName === false) {
            return $this->sendError('Channel not found.');
        }

        $channel = $this->ircChannel->getChannel($channelName);
        if ($channel === false) {
            return $this->sendError('Channel not found.');
        }

        if ($channel == -1) {
            return $this->sendError('Channel is set to Private or Secret. Cannot obtain information.');
        }

        $users = $this->repo->getUsersInChannel($channelName);
        if ($users === false) {
            return $this->sendError('Channel not found.');
        }

        return $this->sendResponse('ok', 200, [
            'count' => count($users),
            'users' => $users,
        ]);
    }
}
