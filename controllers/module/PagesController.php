<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

use Auth;
use DB;
use Session;

class PagesController extends BaseController
{
    public function getNews()
    {
        return $this->setView('pages.news.index');
    }

    public function getDashboard()
    {
        return $this->setView('account.dashboard.index');
    }

    public function getChannels()
    {
        //$data['channels'] = irc('chanserv')->getChannels(Auth::user()->username);
        //$channels = explode("\n", $data['channels'][1]);

        try {
            $channels = DB::connection('denora')->table('chan')->get();
        } catch (\PDOException $e) {
            Session::flash('error', 'Cannot get channel list from IRC.');
            return $this->setView('pages.channels.index', ['chans' => []]);
        }


        echo \Debug::dump($channels, '');

        return $this->setView('pages.channels.index', [
            'chans' => $channels,
        ]);
    }


}
