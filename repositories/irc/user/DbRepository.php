<?php namespace Cysha\Modules\Darchoods\Repositories\Irc\User;

use Cysha\Modules\Core\Repositories\BaseDbRepository;
use Cysha\Modules\Darchoods\Models\Irc as Irc;
use Cache;
use DB;
use Str;

class DbRepository extends BaseDbRepository implements RepositoryInterface
{
    public function __construct(Irc\User $repo)
    {
    }


    public function getByAccountName($nick)
    {

    }

    public function getClientVersions()
    {
        return Cache::remember('stats.clients', 1, function () {
            // Query the database for some user stats, and dynamically create a graph using the google api
            $stats = (array)DB::Connection('denora')->table('ctcp')->where('count', '>', 0)->orderBy('count', 'desc')->get();
            if (!count($stats)) {
                return [];
            }

            // group any clients with the same name, going of the first word in the version tag here
            $clients = [];
            foreach ($stats as $client) {
                $ident = Str::lower(str_replace('"', '', head(explode(' ', $client->version))));

                if (!array_key_exists($ident, $clients)) {
                    $clients[$ident] = 0;
                }
                $clients[$ident] += $client->count;
            }

            // if any client group has <= $threshold, group them into all
            $threshold = 1;
            foreach ($clients as $ident => $count) {
                if ($count <= $threshold) {
                    unset($clients[$ident]);

                    if (!array_key_exists('Other', $clients)) {
                        $clients['Other'] = 0;
                    }
                    $clients['Other'] += $count;
                }
            }

            $output = [];
            foreach ($clients as $ident => $count) {
                $output[] = [$ident, $count];
            }

            return $output;
        });
    }
}
