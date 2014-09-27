<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Irpg;

use Illuminate\Support\Collection;
use URL;

class LeaderboardController extends BaseIrpgController
{
    use \Cysha\Modules\Admin\Traits\DataTableTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setTitle('IdleRPG Player List');
        $this->assets();

        $this->setTableOptions([
            'filtering'     => false,
            'pagination'    => false,
            'sorting'       => false,
            'sort_column'   => ['level' => 'desc', 'secs' => 'asc'],
            'source'        => URL::route('darchoods.irpg.leaderboard-ajax'),
            'collection'    => function () {
                return $this->getIrpgDBCollection([
                    'level' => [SORT_DESC, SORT_NUMERIC],
                    'secs' => [SORT_ASC, SORT_NUMERIC],
                ]);
            },
        ]);

        $this->setTableColumns([
            'player' => [
                'th' => 'Player',
                'tr' => function ($model) {
                    $column = null;

                    $online = $model['online'] == 1 ? 'success' : 'danger';
                    $status = $model['online'] == 1 ? 'User is online' : 'User is offline';
                    $column .= sprintf('<span class="label label-%s" data-toggle="tooltip" title="%s">&nbsp;</span>', $online, $status);
                    $column .= ' '.e($model['name']);

                    return $column;
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '10%',
            ],
            'level' => [
                'th' => 'Level',
                'tr' => function ($model) {
                    return $model['level'];
                },
                'sorting' => true,
                'filtering' => false,
                'width' => '5%',
            ],
            'alignment' => [
                'th' => 'Alignment',
                'tr' => function ($model) {
                    switch($model['alignment']) {
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
                'sorting' => true,
                'filtering' => false,
                'width' => '5%',
            ],
            'next_level' => [
                'th' => 'Next Level In..',
                'tr' => function ($model) {
                    return secs_to_h($model['secs']);
                },
                'sorting' => true,
                'filtering' => false,
                'width' => '20%',
            ],
            'items' => [
                'th' => 'Items',
                'tr' => function ($model) {
                    $items = [];
                    foreach ($model['items'] as $key => $val) {
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

                        $items[] = '<span data-toggle="tooltip" data-placement="top" title="'.ucwords($key).'">'.intval($val).'</span>'.$extra;
                    }

                    return sprintf('(%s)', implode(' | ', $items));
                },
                'sorting' => false,
                'filtering' => false,
                'width' => '30%',
            ],
            'total' => [
                'th' => 'Item Level',
                'tr' => function ($model) {
                    return $model['total'];
                },
                'sorting' => true,
                'filtering' => false,
                'width' => '5%',
            ],
            'actualTotal' => [
                'th' => 'Battle Level',
                'tr' => function ($model) {
                    return $model['actualTotal'];
                },
                'sorting' => true,
                'filtering' => false,
                'width' => '5%',
            ],

        ]);
    }

}
