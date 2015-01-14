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

        // set default order for the array
        $data = ['user' => [], 'stats' => [], 'channels' => []];

        // get user info
        $data['user'] = $this->ircUser->getUserByNick($username);
        if (!count($data['user'])) {
            return $this->sendError('User does not exist', 404);
        }

        // grab the users channels
        $data['channels'] = $this->ircChannel->getUsersChannels($username);

        // total up user mode stats
        $modeCount = ['q' => 0, 'a' => 0, 'o' => 0, 'h' => 0, 'v' => 0];
        if (count($data['channels']) > 0) {
            foreach (array_get($data, 'channels') as $modes) {
                if (empty($modes)) {
                    continue;
                }

                $modes = str_split($modes);

                foreach ($modes as $mode) {
                    $modeCount[$mode]++;
                }
            }
        }

        // add the stats to the array
        $data['stats'] = [
            'channel_count' => count($data['channels']),
            'mode_counts' => $modeCount,
        ];

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

        $users = $this->ircChannel->getUsersInChannel($channelName);
        if ($users === false) {
            return $this->sendError('Channel not found.');
        }

        return $this->sendResponse('ok', 200, [
            'count' => count($users),
            'users' => $users,
        ]);
    }
}
