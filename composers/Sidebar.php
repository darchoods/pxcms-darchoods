<?php namespace Cysha\Modules\Darchoods\Composers;

class Sidebar
{

    public function compose($view)
    {
        $users = with(\App::make('Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface'))->getUsersInChannel('#darchoods');

        $output = [];
        foreach ($users as $user) {
            $modes = array_get($user, 'modes');


            if (strstr($modes, 'q') !== false) {
                $output['Owner'][] = array_get($user, 'nick');
                continue;
            }
            if (strstr($modes, 'a') !== false) {
                $output['Admins'][] = array_get($user, 'nick');
                continue;
            }
            if (strstr($modes, 'o') !== false) {
                $output['Operators'][] = array_get($user, 'nick');
                continue;
            }
            if (strstr($modes, 'h') !== false) {
                $output['Half Operators'][] = array_get($user, 'nick');
                continue;
            }
            if (strstr($modes, 'v') !== false) {
                $output['Voiced Users'][] = array_get($user, 'nick');
                continue;
            }

            $output['Users'][] = array_get($user, 'nick');
        }

        $view->with('dhUsers', $output);
    }
}
