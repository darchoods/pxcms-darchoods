<?php namespace Cysha\Modules\Darchoods\Controllers\Admin;

use Cysha\Modules\Core\Controllers\Admin\Config\BaseConfigController;
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

class ChannelController extends BaseConfigController
{
    use \Cysha\Modules\Admin\Traits\DataTableTrait;

    public function __construct()
    {
        parent::__construct();

        $this->objTheme->setTitle('<i class="fa fa-user"></i> Channel Manager');
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
            'topic' => [
                'th'        => 'Channel Topic',
                'tr'        => function ($model) {
                    return $model->topic;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '45%',
            ],

            'action' => [
                'th' => 'Actions',
                'tr' => function ($model) use ($chanList) {

                    $chan = array_get($chanList, $model->channel, '');

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
        $btn = '<a href="/admin/channels/%1$s/community" class="btn %2$s btn-sm btn-labeled"><span class="btn-label"><i class="glyphicon glyphicon-home"></i></span><span></span></a>&nbsp;
        <a href="/admin/channels/%1$s/network" class="btn %3$s btn-sm btn-labeled"><span class="btn-label"><i class="glyphicon glyphicon-tower"></i></span><span></span></a>&nbsp;
        <a href="/admin/channels/%1$s/blacklist" class="btn %4$s btn-sm btn-labeled"><span class="btn-label"><i class="glyphicon glyphicon-ban-circle"></i></span><span></span></a>';

        $btn = sprintf(
            $btn,
            str_replace('#', '§', $channel),
            ($mode != 'community' ? 'btn-info' : 'btn-disabled'),
            ($mode != 'network' ? 'btn-success' : 'btn-disabled'),
            ($mode != 'blacklist' ? 'btn-danger' : 'btn-disabled')
        );

        return $btn;
    }

    public function markAsCommunity($channel)
    {
        return $this->markAs('community', $channel);
    }

    public function markAsNetwork($channel)
    {
        return $this->markAs('network', $channel);
    }

    public function markAsBlacklist($channel)
    {
        return $this->markAs('blacklist', $channel);
    }

    private function markAs($mode, $channel)
    {

        $channel = str_replace('§', '#', $channel);

        $chanlist = Config::get('darchoods::channels.list', null);
        $chanlist = ($chanlist === null ? [] : json_decode($chanlist, true));

        $chanlist[$channel] = $mode;

        $chanlist = json_encode($chanlist);

        with(new DBConfig)->findOrCreate([
            'namespace' => 'darchoods',
            'group'     => 'channels',
            'item'      => 'list',
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
