<?php namespace Cysha\Modules\Darchoods\Controllers\Api\V1;

use Cysha\Modules\Core\Controllers\BaseApiController as BAC;
use Cysha\Modules\Darchoods\Repositories\Irc\User\RepositoryInterface as IrcUserRepository;

class UserController extends BAC
{
    public function __construct(IrcUserRepository $user)
    {
        parent::__construct();
        $this->ircUser = $user;
    }

    public function getClients()
    {

        $clients = $this->ircUser->getClientVersions();
        if (!count($clients)) {
            return [];
        }

        $output = ['count' => 0, 'clients' => []];
        foreach ($clients as list($ident, $count)) {
            $output['clients'][] = [$ident, $count];
        }
        $output['count'] = count($output['clients']);


        return $this->sendResponse('ok', 200, $output);
    }
}
