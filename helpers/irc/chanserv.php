<?php namespace Cysha\Modules\Darchoods\Helpers\IRC;

class chanserv extends Atheme
{

    public function getChannels($nickname = 'x')
    {
        $this->addParams('NICKSERV LISTCHANS');
        return $this->checkResponse($this->doCmd($nickname, $this->getToken()));
    }

}
