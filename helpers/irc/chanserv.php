<?php namespace Cysha\Modules\Darchoods\Helpers\IRC;

class chanserv extends Atheme
{

    public function getList($nickname = 'x')
    {
        $this->addParams('chanserv list');

        $return = $this->doCmd($nickname, $this->getToken(), 'atheme.command');
        return $this->checkResponse($return);
    }

}
