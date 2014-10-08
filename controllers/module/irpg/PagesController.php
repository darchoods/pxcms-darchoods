<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Irpg;

use Illuminate\Support\Collection;
use URL;

class PagesController extends BaseIrpgController
{

    public function getStats()
    {
        $this->setTitle('IdleRPG Stats');
        // load in the assets for the graph
        $this->objTheme->asset()->add('d3', 'packages/module/darchoods/assets/d3.js');
        $this->objTheme->asset()->add('c3-js', 'packages/module/darchoods/assets/chart/js/c3.min.js', ['d3']);
        $this->objTheme->asset()->add('c3-css', 'packages/module/darchoods/assets/chart/css/c3.css', ['d3']);

        $sorts = [
            'actualTotal' => [
                'keep'   => 5,
                'sort'   => [SORT_DESC, SORT_NUMERIC],
                'output' => ['Player' => 'name', 'Level' => 'level', 'Actual Item Level' => 'actualTotal']
            ],
            'secs' => [
                'keep'           => 5,
                'sort'           => [SORT_ASC, SORT_NUMERIC],
                'output'         => ['Player' => 'name', 'Level' => 'level', 'Next Level in..' => 'level_up'],
                'requireOnline' => true,
            ],
        ];

        foreach ($sorts as $key => $options) {
            $sorts[$key]['data'] = $this->getIrpgDBCollection([$key => $options['sort']])->filter(function ($row) use ($options) {
                if ($row['online'] != '1' && array_get($options, 'requireOnline', false) == true) {
                    return false;
                }

                return ($row['level'] > 0);
            })->slice(0, $options['keep']);
        }

        $users = $this->getIrpgDBCollection();
        $this->getOnlineOffline($users);
        $this->getAlignment($users);
        $this->getLevelSpread($users);
        $sorts['stats'] = [
            'output' => ['Key' => 'key', 'Value' => 'value'],
            'data' => [
                ['key' => 'User Count',         'value' => $users->count()],
                ['key' => 'Online',             'value' => $users->filter(function ($row) { return $row['online'] == '1'; })->count()],
                ['key' => 'Total Idle Time',    'value' => secs_to_h($users->sum('idled'))],
                ['key' => 'Average Idle Time',  'value' => secs_to_h($users->sum('idled')/$users->count())],
            ]
        ];

        return $this->setView('pages.irpg.index', ['data' => $sorts], 'module');
    }

    public function getOnlineOffline(Collection $users)
    {
        $userCount = $users->count();
        $online = $users->filter(function ($row) { return $row['online'] == '1'; })->count();

        $js = 'jQuery(window).ready(function () {
            var clientChart = c3.generate({
                bindto: \'#onffline\',
                height: 300,
                width: 300,
                data: {
                    columns: [[\'Online ('.$online.')\', '.$online.'], [\'Offline ('.($userCount - $online).')\', '.($userCount - $online).']],
                    type: \'donut\'
                },
                donut: {
                    title: "Clients Online",
                    label: {
                        show: false
                    }
                },
                legend: {
                    show: false
                }
            });
        });';


        $this->objTheme->asset()->writeScript('inlinejs-'.__METHOD__, $js, ['c3-js']);

        return [];
    }

    public function getAlignment($users)
    {
        $counters = ['Good' => 0, 'Evil' => 0, 'Neutral' => 0];

        $counters['Good'] = $users->filter(function ($row) {
            return $row['alignment'] == 'g';
        })->count();
        $counters['Evil'] = $users->filter(function ($row) {
            return $row['alignment'] == 'e';
        })->count();
        $counters['Neutral'] = $users->filter(function ($row) {
            return $row['alignment'] == 'n';
        })->count();


        $js = 'jQuery(window).ready(function () {
            var clientChart = c3.generate({
                bindto: \'#alignment\',
                height: 300,
                width: 300,
                data: {
                    columns: [
                        [\'Good ('.$counters['Good'].')\', '.$counters['Good'].'],
                        [\'Evil ('.$counters['Evil'].')\', '.$counters['Evil'].'],
                        [\'Neutral ('.$counters['Neutral'].')\', '.$counters['Neutral'].']
                    ],
                    type: \'donut\'
                },
                donut: {
                    title: "Players Alignment",
                    label: {
                        show: false
                    }
                },
                legend: {
                    show: false
                }
            });
        });';


        $this->objTheme->asset()->writeScript('inlinejs-'.__METHOD__, $js, ['c3-js']);

        return [];
    }

    public function getLevelSpread($users)
    {
        $levels = [];

        $users->each(function ($row) use (&$levels) {
            if (!isset($levels[$row['level']])) {
                $levels[$row['level']] = 0;
            }

            $levels[$row['level']]++;
        });

        $levelSpread = [];
        foreach ($levels as $level => $count) {
            $levelSpread[] = '["'.$level.' ('.$count.')", '.$count.']';
        }

        $js = 'jQuery(window).ready(function () {
            var clientChart = c3.generate({
                bindto: \'#spread\',
                height: 300,
                width: 300,
                data: {
                    columns: ['.implode(', ', $levelSpread).'],
                    type: \'donut\'
                },
                donut: {
                    title: "Level Spread",
                    label: {
                        show: false
                    }
                },
                legend: {
                    show: false
                }
            });
        });';


        $this->objTheme->asset()->writeScript('inlinejs-'.__METHOD__, $js, ['c3-js']);

        return [];
    }
}
