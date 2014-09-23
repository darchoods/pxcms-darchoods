<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Illuminate\Support\Collection;
use URL;

class IRPGController extends BaseController
{
    use \Cysha\Modules\Admin\Traits\DataTableTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDecorativeMode();
        $this->setLayout('col-1');

        $this->setTitle('IdleRPG Player List');
        //$this->objTheme->breadcrumb()->add('Channel Manager', URL::route('admin.channels.index'));
        $this->assets();

        $this->setTableOptions([
            'filtering'     => true,
            'pagination'    => true,
            'sorting'       => true,
            'sort_column'   => 'level',
            'source'        => URL::route('darchoods.pages.irpg-ajax'),
            'collection'    => function () {
                return $this->getCollection();
            },
        ]);


        $this->setTableColumns([
            'player' => [
                'th' => 'Player',
                'tr' => function ($model) {
                    $column = null;

                    $online = $model->online == 1 ? 'success' : 'danger';
                    $status = $model->online == 1 ? 'User is online' : 'User is offline';
                    $column .= sprintf('<span class="label label-%s" data-toggle="tooltip" title="%s">&nbsp;</span>', $online, $status);
                    $column .= ' '.e($model->name);

                    return $column;
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '10%',
            ],
            'level' => [
                'th' => 'Level',
                'tr' => function ($model) {
                    return $model->level;
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '5%',
            ],
            //'idled' => [
            //    'th' => 'Seconds Idled',
            //    'tr' => function ($model) {
            //        return secs_to_h($model->idled);
            //    },
            //    'sorting' => false,
            //    'filtering' => false,
            //    'width' => '5%',
            //],
            'alignment' => [
                'th' => 'Alignment',
                'tr' => function ($model) {
                    switch($model->alignment) {
                        case 'e':
                            $label = 'danger';
                            $text = 'Evil';
                        break;

                        case 'g':
                            $label = 'success';
                            $text = 'Good';
                        break;

                        default:
                        case 'n':
                            $label = 'info';
                            $text = 'Neutral';
                        break;
                    }

                    return sprintf('<div class="label label-%s">%s</div>', $label, $text);
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '5%',
            ],
            'next_level' => [
                'th' => 'Next Level In..',
                'tr' => function ($model) {
                    return secs_to_h($model->secs);
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '20%',
            ],
            'total' => [
                'th' => 'Total',
                'tr' => function ($model) {
                    $total = 0;
                    foreach ($model->items as $i) {
                        $total += intval($i);
                    }

                    return $total;
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '5%',
            ],
            'items' => [
                'th' => 'Items',
                'tr' => function ($model) {
                    $items = [];
                    foreach ($model->items as $key => $val) {
                        $extra = null;
                        if ($key == 'helm' && substr($val, -1, 1) == 'a') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Matt\'s Omniscience Grand Crown"></i>';
                        }
                        if ($key == 'tunic' && substr($val, -1, 1) == 'b') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Res0\'s Protectorate Plate Mail"></i>';
                        }
                        if ($key == 'amulet' && substr($val, -1, 1) == 'c') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Dwyn\'s Storm Magic Amulet"></i>';
                        }
                        if ($key == 'weapon' && substr($val, -1, 1) == 'd') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Jotun\'s Fury Colossal Sword"></i>';
                        }
                        if ($key == 'weapon' && substr($val, -1, 1) == 'e') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Drdink\'s Cane of Blind Rage"></i>';
                        }
                        if ($key == 'boots' && substr($val, -1, 1) == 'f') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Mrquick\'s Magical Boots of Swiftness"></i>';
                        }
                        if ($key == 'weapon' && substr($val, -1, 1) == 'g') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Jeff\'s Cluehammer of Doom"></i>';
                        }
                        if ($key == 'ring' && substr($val, -1, 1) == 'h') {
                            $extra = ' <i class="fa fa-star" data-toggle="tooltip" data-placement="top" title="Juliet\'s Glorious Ring of Sparkliness"></i>';
                        }

                        $items[] = '<span data-toggle="tooltip" data-placement="top" title="'.ucwords($key).'">'.$val.'</span>'.$extra;
                    }

                    return sprintf('(%s)', implode(' | ', $items));
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '30%',
            ],
        ]);
    }

    private function getCollection()
    {
        $file = file_get_contents(base_path().'/../irpg/irpg.db');
        $players = explode("\n", trim($file));

        unset($players[0]);
        $users = [];
        foreach ($players as $player) {
            $info = explode("\t", trim($player));

            $users[] = (object)[
                'name'       => $info[0],
                'admin'      => $info[2],
                'level'      => $info[3],
                'class'      => $info[4],
                'secs'       => $info[5],
                //'uhost'      => $info[7],
                'online'     => $info[8],
                'idled'      => $info[9],
                'created'    => $info[19],
                'last_login' => $info[20],
                'alignment'  => $info[31],

                'location' => [
                    'x' => $info[10],
                    'y' => $info[11],
                ],
                'penalties' => [
                    'mesg'   => $info[12],
                    'nick'   => $info[13],
                    'part'   => $info[14],
                    'kick'   => $info[15],
                    'quit'   => $info[16],
                    'quest'  => $info[17],
                    'logout' => $info[18],
                ],
                'items' => [
                    'amulet'   => $info[21],
                    'charm'    => $info[22],
                    'helm'     => $info[23],
                    'boots'    => $info[24],
                    'gloves'   => $info[25],
                    'ring'     => $info[26],
                    'leggings' => $info[27],
                    'shield'   => $info[28],
                    'tunic'    => $info[29],
                    'weapon'   => $info[30],
                ],
            ];
        }

        return new Collection($users);
    }
}
