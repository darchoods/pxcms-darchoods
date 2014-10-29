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

    protected function getIrpgQuestCollection($sort = array())
    {
        if (\App::environment() == 'local') {
            $db = file_get_contents('https://www.darchoods.net/questinfo.txt');
        } else {
            $db = file_get_contents(base_path().'/../irpg/questinfo.txt');
        }

        $lines = explode("\n", trim($db));
        if (!count($lines)) {
            return [];
        }

        $info = [];
        $i = 0;
        foreach ($lines as $line) {
            $arg = explode(' ', trim($line));

            switch ($arg[0]) {
                case 'T':
                    $i++;
                    unset($arg[0]);
                    $info[$i]['quest'] = implode(' ', $arg);
                break;

                case 'Y':
                    $info[$i]['type'] = $type = $arg[1];
                break;

                case 'P':
                    $info[$i]['goals']['p1'][0] = $arg[1];
                    $info[$i]['goals']['p1'][1] = $arg[2];
                    $info[$i]['goals']['p2'][0] = $arg[3];
                    $info[$i]['goals']['p2'][1] = $arg[4];
                break;

                case 'S':
                    if ($type == 1) {
                        $info[$i]['started'] = $time = $arg[1];
                    } elseif ($type == 2) {
                        $info[$i]['stage'] = $stage = $arg[1];
                    }
                break;

                case 'P1':
                case 'P2':
                case 'P3':
                case 'P4':
                    $info[$i]['players'][strtolower($arg[0])]['name'] = $arg[1];
                    $info[$i]['players'][strtolower($arg[0])]['x'] = array_get($arg, '2', 0);
                    $info[$i]['players'][strtolower($arg[0])]['y'] = array_get($arg, '3', 0);
                break;
            }

        }

        if (empty($info)) {
            return [];
        }

        return $info;
    }

    protected function getIrpgDBCollection($sort = array())
    {
        if (\App::environment() == 'local') {
            $db = file_get_contents('https://www.darchoods.net/irpg.db');
        } else {
            $db = file_get_contents(base_path().'/../irpg/irpg.db');
        }

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
