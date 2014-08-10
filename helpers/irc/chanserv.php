<?php namespace Cysha\Modules\Darchoods\Helpers\IRC;

class chanserv extends Atheme
{

    public function getChannels($nickname = 'x', $uid = '.')
    {
        $this->addParams('NICKSERV LISTCHANS');
        return $this->checkResponse($this->doCmd($nickname, $uid));
    }

}
