<?php namespace Cysha\Modules\Darchoods\Controllers\Api\V1;

use Cysha\Modules\Core\Controllers\BaseApiController as BAC;
use Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface as IrcChannelRepository;
use Input;

class ChannelController extends BAC
{
    public function __construct(IrcChannelRepository $channel)
    {
        parent::__construct();
        $this->repo = $channel;
    }

    public function getChannels()
    {
        $channels = $this->repo->getAll();
        if (!count($channels)) {
            return $this->sendError('No channels returned.');
        }

        $data['count'] = count($channels);
        $data['channels'] = $channels;

        return $this->sendResponse('ok', 200, $data);
    }

    public function postChannelView()
    {
        $channel = Input::get('channel', false);
        if ($channel === false) {
            return $this->sendError('Channel not found.');
        }

        $channel = $this->repo->getChannel($channel);
        if ($channel === false) {
            return $this->sendError('Channel not found.');
        }

        return $this->sendResponse('ok', 200, [
            'channel' => $channel,
        ]);
    }

    public function postChannelUsers()
    {
        $channel = Input::get('channel', false);
        if ($channel === false) {
            return $this->sendError('Channel not found.');
        }

        $users = $this->repo->getChannelUsers($channel);
        if ($users === false) {
            return $this->sendError('Channel not found.');
        }

        return $this->sendResponse('ok', 200, [
            'count' => count($users),
            'users' => $users,
        ]);
    }
}
