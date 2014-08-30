<?php namespace Cysha\Modules\Darchoods\Repositories\Irc\Server;

use Cysha\Modules\Core\Repositories\BaseDbRepository;
use Cysha\Modules\Darchoods\Models\Irc as Irc;
use Config;

class DbRepository extends BaseDbRepository implements RepositoryInterface
{
    public function __construct(Irc\Server $repo)
    {
        $this->model = $repo;
    }

    public function getAll(array $with = [])
    {
        $servers = $this->make($with)->get();

        // grab the blacklist
        $serverList = Config::get('darchoods::servers.list', null);
        $serverList = ($serverList !== null ? json_decode($serverList, true) : []);

        // and filter away
        $servers = $servers->filter(function ($server) use ($serverList) {
            if (array_get($serverList, $server->server) == 'blacklist') {
                return false;
            }
            return true;
        });

        return $this->transformModel($servers);
    }




}
