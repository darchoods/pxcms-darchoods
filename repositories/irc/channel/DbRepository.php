<?php namespace Cysha\Modules\Darchoods\Repositories\Irc\Channel;

use Cysha\Modules\Core\Repositories\BaseDbRepository;
use Cysha\Modules\Darchoods\Models\Irc as Irc;
use Cache;
use DB;
use Str;
use Config;

class DbRepository extends BaseDbRepository implements RepositoryInterface
{
    public function __construct(Irc\Channel $repo)
    {
        $this->model = $repo;
    }

    public function getAll(array $with = [])
    {
        $channels = $this->make($with)->get();

        // and filter away
        $channels = $channels->filter(function ($channel) {
            if (empty($channel->modes)) { // not registered? hrm
                return false;
            }

            if (strstr($channel->modes, 'p')) { //private
                return false;
            }
            if (strstr($channel->modes, 's')) { //secret
                return false;
            }
            if (strstr($channel->modes, 'O')) { //opers
                return false;
            }

            return true;
        });

        return $this->transformModel($channels);
    }

    public function getChannel($channel, array $with = [])
    {
        $channel = $this->make($with)->whereChannel($channel)->get();
        if ($channel === null) {
            return false;
        }

        if (!count($channel)) {
            return false;
        }

        return $channel->first()->transform();
    }

    public function getChannelUsers($channel)
    {
        $users = \DB::connection('denora')->select(
            'SELECT user.mode_lh AS helper, user.*, ison.*, server.uline
                FROM ison, chan, user, server
                WHERE LOWER(chan.channel)=LOWER("'.$channel.'")
                    AND ison.chanid = chan.chanid
                    AND ison.nickid = user.nickid
                    AND user.server = server.server
                ORDER BY user.nick ASC'
        );
        if (!count($users)) {
            return false;
        }

        // simulate the transformer
        $users = array_map(function ($user) {
            $mode = null;
            if ($user->mode_lq == 'Y') {
                $mode .= 'q';
            }
            if ($user->mode_la == 'Y') {
                $mode .= 'a';
            }
            if ($user->mode_lo == 'Y') {
                $mode .= 'o';
            }
            if ($user->mode_lh == 'Y') {
                $mode .= 'h';
            }
            if ($user->mode_lv == 'Y') {
                $mode .= 'v';
            }

            return [
                'nick'         => (string) $user->nick,
                'username'     => (string) $user->username,
                'realname'     => (string) $user->realname,
                'mask'         => (string) $user->hiddenhostname,
                'modes'        => (string) $mode,
                'registered'   => (bool) (empty($user->account) ? false : true),
                'away'         => (bool) ($user->away == 'Y' ? true : false),
                'away_msg'     => ($user->away == 'Y' ? (string) $user->awaymsg : null),
                'country_code' => (string) $user->countrycode,
                'country'      => (string) $user->country,
                'version'      => (string) $user->ctcpversion,
                'server'       => (string) $user->server,
            ];
        }, $users);

        return ($users);
    }
}
