<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface as IrcChannelRepository;
use Cysha\Modules\Qdb\Repositories\Channel\RepositoryInterface as QdbChannelRepository;
use Cysha\Modules\Qdb\Repositories\Quote\RepositoryInterface as QdbQuoteRepository;
use Illuminate\Support\Collection;
use Auth;
use DB;
use URL;
use Session;
use Config;

class ChannelController extends BaseController
{
    public function __construct(IrcChannelRepository $ircChan, QdbChannelRepository $qdbChan, QdbQuoteRepository $qdbQuote)
    {
        parent::__construct();

        $this->ircChan = $ircChan;
        $this->qdbChan = $qdbChan;
        $this->qdbQuote = $qdbQuote;
    }

    public function getIndex()
    {
        $this->setTitle('Channel List');
        $this->setDecorativeMode();

        return $this->setView('pages.channels.index', [
            'chans' => $this->getCollection(),
        ])->header('Content-type', 'text/html; charset=utf-8');
    }

    public function getChannel($channel)
    {
        //$this->setTitle('Channel List');
        //$this->setDecorativeMode();

        $ircChan = $this->ircChan->getChannel($channel);
        $qdbChan = $this->qdbChan->getChannel($channel);
        echo \Debug::dump($ircChan, 'irc channel');
        echo \Debug::dump($qdbChan->transform(), 'qdb channel');
        echo \Debug::dump($this->qdbQuote->getRandomByChannel($qdbChan, 5), 'qdb quotes');

        return $this->setView('pages.channels.view', [
            'channel' => $channel
        ])->header('Content-type', 'text/html; charset=utf-8');
    }

    public function getCollection()
    {
        try {
            $dbChans = $this->ircChan->getAll();
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

            $modes = str_split($chan['modes']);
            if (!in_array('n', $modes) && !in_array('t', $modes)) {
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
