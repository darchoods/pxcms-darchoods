<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

class PagesController extends BaseController
{
    public function getNews()
    {
        return $this->setView('pages.news.index');
    }

    public function getDashboard()
    {
        return $this->setView('account.dashboard.index');
    }

}
