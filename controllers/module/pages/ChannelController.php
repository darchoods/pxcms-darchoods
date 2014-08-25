<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Illuminate\Support\Collection;
use Auth;
use DB;
use URL;
use Session;
use Config;

class ChannelController extends BaseController
{

    public function getIndex()
    {



        return $this->setView('pages.channels.index', [
            'chans' => $this->getCollection(),
        ]);
    }

    public function getCollection()
    {
        try {
            $dbChans = DB::connection('denora')->table('chan')->get();
        } catch (\PDOException $e) {
            Session::flash('error', 'Cannot get channel list from IRC.');
            return [];
        }

        $chanList = Config::get('darchoods::channels.list', null);
        $chanList = ($chanList !== null ? json_decode($chanList, true) : []);

        $dbChans = new Collection($dbChans);
        $dbChans = $dbChans->filter(function (&$channel) use ($chanList) {
            if (array_get($chanList, $channel->channel) == 'blacklist') {
                return false;
            }

            $checkModes = chan_modes($channel);
            if (strstr($checkModes, ' ')) {
                $checkModes = explode(' ', $checkModes);
                $checkModes = $checkModes[0];
            }

            if (strstr($checkModes, 'p')) { //private
                return false;
            }
            if (strstr($checkModes, 's')) { //secret
                return false;
            }
            if (strstr($checkModes, 'O')) { //opers
                return false;
            }
            if ($channel->currentusers <= 1) { // no channel count
                return false;
            }

            $channel->modes = $checkModes;


            $colorize = new IRC\MircColorParser();
            $channel->topic = e($channel->topic);
            $channel->topic = $colorize->colorize($channel->topic);
            $channel->topic = denora_colorconvert($channel->topic);

            if (!empty($channel->topic) && !empty($channel->topicauthor)) {
                $channel->topic .= '<span class="pull-right"><small> set by '.profile($channel->topicauthor).'</small></span>';
            }

            if (array_get($chanList, $channel->channel) == 'network') {
                $channel->extra = 'success';
            }

            if (array_get($chanList, $channel->channel) == 'community') {
                $channel->extra = 'info';
            }


            return true;
        });

        $dbChans = $dbChans->sort(function ($x, $y) {
            if ($x->currentusers == $y->currentusers) {
                return 0;
            } elseif ($x->currentusers < $y->currentusers) {
                return 1;
            } else {
                return -1;
            }
        });
// echo \Debug::dump($dbChans, '');die;
        return $dbChans;
    }

}
