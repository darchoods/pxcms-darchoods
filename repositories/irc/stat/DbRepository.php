<?php namespace Cysha\Modules\Darchoods\Repositories\Irc\Stat;

use Cysha\Modules\Core\Repositories\BaseDbRepository;
use Cysha\Modules\Darchoods\Models\Irc as Irc;
use Config;

class DbRepository extends BaseDbRepository implements RepositoryInterface
{
    public function __construct(Irc\Stat $repo, Irc\Maxvalue $maxValue)
    {
        $this->model = $repo;
        $this->maxValue = $maxValue;
    }

    public function getUserCount($hours=24)
    {
        // Query the database for some user stats, and dynamically create a graph using the google api
        $stats = Irc\Stat::orderBy('id', 'desc')->take(2)->remember(60)->get();
        if (!count($stats)) {
            return [];
        }

        list($today, $yday) = $stats->toArray();

        $users = [];
        foreach (['yday', 'today'] as $var) {
            foreach (range(0, 23) as $i) {
                $count = array_get($$var, 'time_'.$i);
                if ($count == 0) {
                    continue;
                }

                $day = array_get($$var, 'day');
                $month = array_get($$var, 'month');
                $year = array_get($$var, 'year');
                $users[date('\n\e\w \D\a\t\e\(Y, m, d, H, i\)', gmmktime($i, 0, 0, $month, $day, $year))] = (int)$count;
            }
        }

        return array_splice($users, -$hours);
    }

    public function getUserPeak()
    {
        $stats = $this->maxValue->whereType('users')->select(['val', 'time'])->get()->first();

        return $stats;
    }
}
