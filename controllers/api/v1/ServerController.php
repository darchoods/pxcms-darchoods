<?php namespace Cysha\Modules\Darchoods\Controllers\Api\V1;

use Cysha\Modules\Core\Controllers\BaseApiController as BAC;
use Cysha\Modules\Darchoods\Repositories\Irc\Server\RepositoryInterface as IrcServerRepository;

class ServerController extends BAC
{
    public function __construct(IrcServerRepository $server)
    {
        parent::__construct();
        $this->repo = $server;
    }

    public function getServers()
    {
        $servers = $this->repo->getAll();

        $data['count'] = count($servers);
        $data['servers'] = $servers;

        return $this->sendResponse('ok', 200, $data);
    }

    public function getServer()
    {
        $servers = $this->repo->whereName();

        $data['count'] = count($servers);
        $data['servers'] = $servers;

        return $this->sendResponse('ok', 200, $data);
    }
}
