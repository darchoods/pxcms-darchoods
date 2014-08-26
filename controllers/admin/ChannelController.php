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

class ChannelController extends BaseAdminController
{
    use \Cysha\Modules\Admin\Traits\DataTableTrait;

    public function __construct()
    {
        parent::__construct();

        $this->objTheme->setTitle('<i class="glyphicon glyphicon-list-alt"></i> Channel Manager');
        $this->objTheme->breadcrumb()->add('Channel Manager', URL::route('admin.channels.index'));
        $this->assets();
        $this->objTheme->asset()->add('datatable-channelsjs', 'packages/module/darchoods/assets/admin/js/channel_manager.js', array('datatable-js'));

        $this->setTableOptions([
            'filtering'     => true,
            'pagination'    => true,
            'sorting'       => true,
            'sort_column'   => 'id',
            'source'        => URL::route('admin.channels.ajax'),
            'collection'    => function () {
                return $this->getCollection();
            },
        ]);

        $chanList = Config::get('darchoods::channels.list', null);
        $chanList = ($chanList !== null ? json_decode($chanList, true) : []);
        $this->setTableColumns([
            'channel' => [
                'th'        => 'Channel Name',
                'tr'        => function ($model) {
                    return $model->channel;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '15%',
            ],
            'modes' => [
                'th'        => 'Channel Modes',
                'tr'        => function ($model) {
                    return ($model->modes == '+' ? '...' : $model->modes);
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '10%',
            ],
            'currentusers' => [
                'th'        => 'Current Users',
                'tr'        => function ($model) {
                    return $model->currentusers;
                },
                'tr-class' => 'hide-sm',
                'filtering' => true,
                'width'     => '5%',
            ],
            'maxusers' => [
                'th'        => 'Peak Users',
                'tr'        => function ($model) {
                    return $model->maxusers;
                },
                'tr-class' => 'hide-sm',
                'filtering' => true,
                'width'     => '5%',
            ],
            'topic' => [
                'th'        => 'Channel Topic',
                'tr'        => function ($model) {
                    return $model->topic;
                },
                'tr-class' => 'hide-sm',
                'sorting'   => true,
                'filtering' => true,
                'width'     => '45%',
            ],

            'action' => [
                'th' => 'Actions',
                'tr' => function ($model) use ($chanList) {
                    $chan = array_get($chanList, $model->channel, null);
                    return $this->modelButtons($model->channel, $chan);
                },
                'sorting'   => false,
                'width'     => '20%',
            ]
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

        $dbChans = new Collection($dbChans);
        $dbChans = $dbChans->filter(function (&$channel) {

            $checkModes = chan_modes((array)$channel);
            if (strstr($checkModes, ' ')) {
                $checkModes = explode(' ', $checkModes);
                $checkModes = $checkModes[0];
            }

            $channel->modes = $checkModes;

            $colorize = new IRC\MircColorParser();
            $channel->topic = e($channel->topic);
            $channel->topic = $colorize->colorize($channel->topic);
            $channel->topic = denora_colorconvert($channel->topic);

            return true;
        });

        $sortColumnID = Input::get('iSortCol_0', '0');
        $column = Input::get('mDataProp_'.$sortColumnID, 'channel');
        $order = Input::get('sSortDir_0', 'asc');

        $dbChans = $dbChans->sort(function ($x, $y) use ($column, $order) {
            $value1 = $x->$column;
            $value2 = $y->$column;

            if ($value1 == $value2) {
                return 0;
            }

            $query = $order == 'asc' ? ($value1 > $value2) : ($value1 < $value2);
            return ($query === true ? 1 : -1);
        });

        return $dbChans;
    }

    public function modelButtons($channel, $mode)
    {
        $btn = '<a href="/admin/channels/%1$s/network" class="btn %2$s btn-sm btn-labeled" data-toggle="tooltip" title="Assign Channel Network Status"><span class="btn-label"><i class="glyphicon glyphicon-tower"></i></span><span></span></a>&nbsp;
        <a href="/admin/channels/%1$s/blacklist" class="btn %3$s btn-sm btn-labeled" data-toggle="tooltip" title="Blacklist Channel"><span class="btn-label"><i class="glyphicon glyphicon-ban-circle"></i></span><span></span></a>
        <a href="/admin/servers/%1$s/default" class="btn %4$s btn-sm btn-labeled" data-toggle="tooltip" title="Reset Channel to Default"><span class="btn-label"><i class="glyphicon glyphicon-certificate"></i></span><span></span></a>';

        $btn = sprintf(
            $btn,
            str_replace('#', '§', $channel),
            ($mode != 'network' ? 'btn-success' : 'btn-disabled'),
            ($mode != 'blacklist' ? 'btn-danger' : 'btn-disabled'),
            (!empty($mode) ? 'btn-default' : 'btn-disabled')
        );
        return $btn;
    }

    public function markAs($mode, $channel)
    {
        $channel = str_replace('§', '#', $channel);

        $chanlist = Config::get('darchoods::channels.list', null);
        $chanlist = ($chanlist === null ? [] : json_decode($chanlist, true));

        $chanlist[$channel] = $mode;
        if ($mode == 'default') {
            unset($chanlist[$channel]);
        }

        $chanlist = json_encode($chanlist);

        with(new DBConfig)->findOrCreate([
            'environment' => \App::environment(),
            'namespace'   => 'darchoods',
            'group'       => 'channels',
            'item'        => 'list',
        ], [
            'value'     => $chanlist
        ]);

        if (Request::ajax()) {
            echo $this->modelButtons($channel, $mode);
            exit;
        }

        return Redirect::back()->withInfo($channel.' has been set as a '.$mode.' channel.');
    }
}
