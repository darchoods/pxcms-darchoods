<?php namespace Cysha\Modules\Darchoods\Controllers\Module;


class PagesController extends BaseController
{


    public function getDashboard()
    {
        $userInfo = getUserInfo();
        $pageData['userInfo'] = array(
            'Registered Since' => array_get($userInfo, 'registered', null),
            'vHost'            => array_get($userInfo, 'vhost', null),
            'Last Seen'        => array_get($userInfo, 'user seen', null),
        );

        return $this->setView('account.dashboard.index', $pageData);
    }
}
