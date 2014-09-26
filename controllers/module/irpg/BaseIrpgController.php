<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Irpg;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Illuminate\Support\Collection;
use URL;

class BaseIrpgController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->setDecorativeMode();
        $this->setLayout('col-1');
    }

    protected function getIrpgDBCollection($sort = array())
    {
        // $db = file_get_contents(base_path().'/../irpg/irpg.db');
        $db = file_get_contents('https://www.darchoods.net/irpg.db');

        $players = explode("\n", trim($db));

        unset($players[0]);
        $users = [];
        foreach ($players as $player) {
            $info = explode("\t", trim($player));

            //if ($info[8] == 0) {
            //    continue;
            //}

            $user = (array)[
                'name'       => $info[0],
                'admin'      => $info[2],
                'level'      => $info[3],
                'class'      => $info[4],
                'secs'       => $info[5],
                'level_up'   => secs_to_h($info[5]),
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

            $total = 0;
            foreach ($user['items'] as $i) {
                $total += intval($i);
            }
            $user['total'] = $total;

            $user['modTotal'] = 0;
            if ($user['alignment'] == 'e') {
                $user['modTotal'] -= 10;
            } elseif ($user['alignment'] == 'g') {
                $user['modTotal'] += 10;
            }

            if ($user['modTotal'] !== 0) {
                $user['difference'] = floor($user['total']/$user['modTotal']);
                $user['actualTotal'] = round($user['total'] + $user['difference']);
            } else {
                $user['difference'] = 0;
                $user['actualTotal'] = $user['total'];
            }


            $users[] = $user;
        }

        if (!empty($sort)) {
            $users = MultiSort($users, $sort, true);
        }

        return new Collection($users);
    }
}
