<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

use Auth;
use DB;
use Session;

class PagesController extends BaseController
{
    public function getDashboard()
    {
        return $this->setView('account.dashboard.index');
    }


}
