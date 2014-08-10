<?php namespace Cysha\Modules\Darchoods\Controllers\Api\V1;

use Cysha\Modules\Core\Controllers\BaseApiController as BAC;

class BaseController extends BAC
{

    public function getIndex()
    {
        return $this->sendOK('ok');
    }
}
