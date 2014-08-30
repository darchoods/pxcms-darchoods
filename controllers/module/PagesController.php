<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

use Redirect;
use Session;

class PagesController extends BaseController
{
    public function getDashboard()
    {
        Session::reflash();
        return Redirect::route('pxcms.pages.home');
        return $this->setView('account.dashboard.index');
    }


}
