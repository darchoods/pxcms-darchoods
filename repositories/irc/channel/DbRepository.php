<?php namespace Cysha\Modules\Darchoods\Repositories\Irc\Channel;

use Cysha\Modules\Core\Repositories\BaseDbRepository;
use Cysha\Modules\Darchoods\Models\Irc as Irc;
use Cache;
use DB;
use Str;
use Config;

class DbRepository extends BaseDbRepository implements RepositoryInterface
{
    public function __construct(Irc\Channel $channel, Irc\User $user)
    {
        $this->model = $channel;
        $this->user = $user;
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
        $channel = $channel->first();

        if (strstr($channel->modes, 'O') !== false) { return -1; }
        if (strstr($channel->modes, 's') !== false) { return -1; }
        if (strstr($channel->modes, 'p') !== false) { return -1; }

        return $channel->transform();
    }

    public function getUsersChannels($nick)
    {
        $nick = $this->user->whereNick($nick)->get()->first();
        if ($nick === null) {
            return [];
        }

        $chans = \DB::connection('denora')->table('user')
            ->where('user.nick', '=', $nick->nick)
            ->join('ison', 'ison.nickid', '=', 'user.nickid')
            ->join('chan', 'ison.chanid', '=', 'chan.chanid')
            ->select('chan.channel', 'ison.*')
            ->get();

        $channels = [];
        foreach ($chans as $chan) {
            $channelModes = null;
            if ($chan->mode_lq == 'Y') { $channelModes .= 'q'; }
            if ($chan->mode_la == 'Y') { $channelModes .= 'a'; }
            if ($chan->mode_lo == 'Y') { $channelModes .= 'o'; }
            if ($chan->mode_lh == 'Y') { $channelModes .= 'h'; }
            if ($chan->mode_lv == 'Y') { $channelModes .= 'v'; }

            $channels[$chan->channel] = $channelModes;
        }

        return $channels;
    }

    public function getUsersInChannel($channel)
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
            $clientModes = null;
            if ($user->mode_ub == 'Y') { $clientModes .= 'B'; }
            if ($user->mode_lc == 'Y') { $clientModes .= 'c'; }
            if ($user->mode_ld == 'Y') { $clientModes .= 'd'; }
            if ($user->mode_lg == 'Y') { $clientModes .= 'g'; }
            if ($user->mode_ug == 'Y') { $clientModes .= 'G'; }
            if ($user->mode_lh == 'Y') { $clientModes .= 'h'; }
            if ($user->mode_uh == 'Y') { $clientModes .= 'H'; }
            if ($user->mode_li == 'Y') { $clientModes .= 'i'; }
            if ($user->mode_ui == 'Y') { $clientModes .= 'I'; }
            if ($user->mode_lo == 'Y') { $clientModes .= 'o'; }
            if ($user->mode_uq == 'Y') { $clientModes .= 'Q'; }
            if ($user->mode_ur == 'Y') { $clientModes .= 'R'; }
            if ($user->mode_lr == 'Y') { $clientModes .= 'r'; }
            if ($user->mode_ls == 'Y') { $clientModes .= 's'; }
            if ($user->mode_us == 'Y') { $clientModes .= 'S'; }
            if ($user->mode_uw == 'Y') { $clientModes .= 'W'; }
            if ($user->mode_lx == 'Y') { $clientModes .= 'x'; }

            $channelModes = null;
            if ($user->mode_lq == 'Y') { $channelModes .= 'q'; }
            if ($user->mode_la == 'Y') { $channelModes .= 'a'; }
            if ($user->mode_lo == 'Y') { $channelModes .= 'o'; }
            if ($user->mode_lh == 'Y') { $channelModes .= 'h'; }
            if ($user->mode_lv == 'Y') { $channelModes .= 'v'; }


            if ($user->hiddenhostname == 'services.darkscience.net') {
                $user->ctcpversion = 'atheme';
            }

            return [
                'nick'          => (string) $user->nick,
                'username'      => (string) $user->username,
                'realname'      => (string) $user->realname,
                'mask'          => (string) $user->hiddenhostname,
                'account'       => (string) $user->account,
                'modes'         => (string) $clientModes,
                'channel_modes' => (string) $channelModes,
                'userstring'    => (string) $user->nick.'!'.$user->username.'@'.$user->hiddenhostname,

                'online'        => (bool) ($user->online !== 'Y' ? false : true),
                'online_last'   => $user->lastquit ? strtotime($user->lastquit) : null,
                'identified'    => (bool) ($user->online !== 'Y' || empty($user->account) ? false : true),
                'is_bot'        => (bool) (strpos($clientModes, 'B') === false ? false : true),

                'away'          => (bool) ($user->away === 'Y' ? true : false),
                'away_msg'      => ($user->away == 'Y' ? (string) $user->awaymsg : null),

                'country_code'  => (string) $user->countrycode,
                'country'       => (string) $user->country,

                'version'       => (string) $user->ctcpversion,
                'server'        => (string) $user->server,
            ];
        }, $users);

        return ($users);
    }
}
