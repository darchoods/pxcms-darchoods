<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface as IrcChannelRepository;
use Illuminate\Support\Collection;
use Auth;
use DB;
use URL;
use Session;
use Config;

class ChannelController extends BaseController
{
    public function __construct(IrcChannelRepository $channels)
    {
        parent::__construct();
        $this->repo = $channels;

    }

    public function getIndex()
    {
        $this->setTitle('Channel List');
        $this->setDecorativeMode();

        return $this->setView('pages.channels.index', [
            'chans' => $this->getCollection(),
        ])->header('Content-type', 'text/html; charset=utf-8');
    }

    public function getCollection()
    {
        try {
            $dbChans = $this->repo->getAll();
        } catch (\PDOException $e) {
            Session::flash('error', 'Cannot get channel list from IRC.');
            return [];
        }

        // grab the chanlist
        $channelList = Config::get('darchoods::channels.list', null);
        $channelList = ($channelList !== null ? json_decode($channelList, true) : []);

        // filter through blacklist and network channels, drop any channel with less than 1 user in
        $dbChans = array_filter($dbChans, function (&$chan) use ($channelList) {
            if (array_get($chan, 'stats.current_users', 0) <= 1) {
                return false;
            }
            if (array_get($channelList, $chan['name']) == 'blacklist') {
                return false;
            }

            $chan['extra'] = null;
            if (array_get($channelList, $chan['name']) == 'network') {
                $chan['extra'] = 'success';
            }

            return true;
        });

        // sort the channels before output
        usort($dbChans, function ($x, $y) {
            if (array_get($x, 'stats.current_users', 0) == array_get($y, 'stats.current_users', 0)) {
                return 0;
            } elseif (array_get($x, 'stats.current_users', 0) < array_get($y, 'stats.current_users', 0)) {
                return 1;
            } else {
                return -1;
            }
        });

        return $dbChans;
    }

}
