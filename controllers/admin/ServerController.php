<?php namespace Cysha\Modules\Darchoods\Controllers\Admin;

use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Cysha\Modules\Core\Models\DBConfig;
use Illuminate\Support\Collection;
use Auth;
use DB;
use URL;
use Input;
use Session;
use Config;
use Redirect;
use Request;

class ServerController extends BaseAdminController
{
    use \Cysha\Modules\Admin\Traits\DataTableTrait;

    public function __construct()
    {
        parent::__construct();

        $this->objTheme->setTitle('<i class="glyphicon glyphicon-list-alt"></i> Server Manager');
        $this->objTheme->breadcrumb()->add('Server Manager', URL::route('admin.servers.index'));
        $this->assets();
        $this->objTheme->asset()->add('datatable-serversjs', 'packages/module/darchoods/assets/admin/js/server_manager.js', array('datatable-js'));

        $this->setTableOptions([
            'filtering'     => false,
            'pagination'    => true,
            'sorting'       => true,
            'sort_column'   => 'id',
            'tfoot'         => true,
            'source'        => URL::route('admin.servers.ajax'),
            'collection'    => function () {
                return $this->getCollection();
            },
        ]);

        $serverList = Config::get('darchoods::servers.list', null);
        $serverList = ($serverList !== null ? json_decode($serverList, true) : []);
        $this->setTableColumns([
            'servid' => [
                'th'        => 'ID',
                'tr'        => function ($model) {
                    return $model->servid;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '3%',
            ],
            'linkedto' => [
                'th'        => 'Linked to ID',
                'tr'        => function ($model) {
                    return $model->linkedto;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '5%',
            ],
            'server' => [
                'th'        => 'Node Name',
                'tr'        => function ($model) {
                    return ($model->online == 'Y' ? '<div class="label label-success">Online</div>' : '<div class="label label-danger">offline</div>') .
                    ' '.$model->server;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '10%',
            ],
            'countrycode' => [
                'th'        => 'Location',
                'tr'        => function ($model) {
                    return '<span data-toggle="tooltip" title="'.$model->country.'">'.$model->countrycode.'</span>';
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '3%',
            ],
            'version' => [
                'th'        => 'Node Version',
                'tr'        => function ($model) {
                    return $model->version;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '7%',
            ],
            'connecttime' => [
                'th'        => 'Last Connect',
                'tr'        => function ($model) {
                    return date_difference(time()-date_carbon($model->connecttime, 'U'));
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '7%',
            ],
            'lastsplit' => [
                'th'        => 'Last Split',
                'tr'        => function ($model) {
                    return date_difference(time()-date_carbon($model->lastsplit, 'U'));
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '7%',
            ],
            'currentusers' => [
                'th'        => 'Current Users',
                'tr'        => function ($model) {
                    return $model->currentusers;
                },
                'filtering' => true,
                'width'     => '5%',
            ],
            'maxusers' => [
                'th'        => 'Peak Users',
                'tr'        => function ($model) {
                    return $model->maxusers;
                },
                'filtering' => true,
                'width'     => '5%',
            ],
            'opers' => [
                'th'        => 'Node Opers',
                'tr'        => function ($model) {
                    return $model->opers;
                },
                'filtering' => true,
                'width'     => '5%',
            ],

            'action' => [
                'th' => 'Actions',
                'tr' => function ($model) use ($serverList) {
                    $mode = array_get($serverList, $model->server, null);
                    return $this->modelButtons($model->server, $mode);
                },
                'sorting'   => false,
                'width'     => '10%',
            ]
        ]);
    }

    public function getCollection()
    {
        try {
            $dbServers = DB::connection('denora')->table('server')->get();
        } catch (\PDOException $e) {
            return [];
        }

        $dbServers = new Collection($dbServers);

        $sortColumnID = Input::get('iSortCol_0', '0');
        $column = Input::get('mDataProp_'.$sortColumnID, 'server');
        $order = Input::get('sSortDir_0', 'asc');

        $dbServers = $dbServers->sort(function ($x, $y) use ($column, $order) {
            $value1 = $x->$column;
            $value2 = $y->$column;

            if ($value1 == $value2) {
                return 0;
            }

            $query = $order == 'asc' ? ($value1 > $value2) : ($value1 < $value2);
            return ($query === true ? 1 : -1);
        });

        return $dbServers;
    }

    public function modelButtons($server, $mode)
    {
        $btn = '<a href="/admin/servers/%1$s/blacklist" class="btn %2$s btn-sm btn-labeled" data-toggle="tooltip" title="Blacklist Server from Heartbeat Page"><span class="btn-label"><i class="glyphicon glyphicon-ban-circle"></i></span><span></span></a>
        <a href="/admin/servers/%1$s/default" class="btn %3$s btn-sm btn-labeled" data-toggle="tooltip" title="Reset Server Status"><span class="btn-label"><i class="glyphicon glyphicon-certificate"></i></span><span></span></a>';

        $btn = sprintf(
            $btn,
            str_replace('#', '§', $server),
            ($mode != 'blacklist' ? 'btn-danger' : 'btn-disabled'),
            (!empty($mode) ? 'btn-default' : 'btn-disabled')
        );

        return $btn;
    }

    public function markAs($mode, $server)
    {

        $server = str_replace('§', '#', $server);

        $serverList = Config::get('darchoods::servers.list', null);
        $serverList = ($serverList === null ? [] : json_decode($serverList, true));

        $serverList[$server] = $mode;
        if ($mode == 'default') {
            unset($serverList[$server]);
        }

        $serverList = json_encode($serverList);

        with(new DBConfig)->findOrCreate([
            'environment' => \App::environment(),
            'namespace'   => 'darchoods',
            'group'       => 'servers',
            'item'        => 'list',
        ], [
            'value'     => $serverList
        ]);

        if (Request::ajax()) {
            echo $this->modelButtons($server, $mode);
            exit;
        }

        return Redirect::back()->withInfo($server.' has been set as a '.$mode.' server.');
    }


}
