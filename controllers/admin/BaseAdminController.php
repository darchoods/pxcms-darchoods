<?php namespace Cysha\Modules\Darchoods\Controllers\Admin;

use Cysha\Modules\Core\Controllers\BaseAdminController as BAC;
use Config;

class BaseAdminController extends BAC
{

    public function markAsNetwork($arg)
    {
        return $this->markAs('network', $arg);
    }

    public function markAsBlacklist($arg)
    {
        return $this->markAs('blacklist', $arg);
    }

    public function markAsDefault($arg)
    {
        return $this->markAs('default', $arg);
    }
}
