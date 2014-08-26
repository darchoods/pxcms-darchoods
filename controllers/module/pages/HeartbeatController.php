<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Illuminate\Support\Collection;
use Auth;
use DB;
use URL;
use Session;
use Config;

class HeartbeatController extends BaseController
{

    public function getIndex()
    {
        $this->setTitle('Network Heartbeat <div class="animated pulse"><i class="fa fa-heart"></i></div>');
        $this->setDecorativeMode();

        return $this->setView('pages.heartbeat.index', [
            'serverList' => $this->getCollection(),
        ]);
    }

    public function getCollection()
    {
        try {
            $dbServers = DB::connection('denora')->table('server')->get();
        } catch (\PDOException $e) {
            Session::flash('error', 'Cannot get server list from IRC.');
            return [];
        }

        $serverList = Config::get('darchoods::servers.list', null);
        $serverList = ($serverList !== null ? json_decode($serverList, true) : []);

        $dbServers = new Collection($dbServers);
        $dbServers = $dbServers->filter(function ($server) use ($serverList) {
            if (array_get($serverList, $server->server) == 'blacklist') {
                return false;
            }

            return true;
        });

        $dbServers = $dbServers->sort(function ($x, $y) {
            if ($x->countrycode == $y->countrycode) {
                return 0;
            } elseif ($x->countrycode < $y->countrycode) {
                return 1;
            } else {
                return -1;
            }
        });

        return $dbServers;
    }

}
