<?php namespace Cysha\Modules\Darchoods\Helpers\IRC;

class nickserv extends atheme
{
    public function register($nickname, $password, $email)
    {
        $this->addParams(sprintf('NICKSERV REGISTER %s %s %s', $nickname, $password, $email));
        $response = $this->checkResponse($this->doCmd());

        return ($response[0] === true ? $response : [$response[0], 'Unable to register account. Username already exists.']);
    }

    public function login($nickname, $password)
    {
        return $this->checkResponse($this->doCmd($password, $nickname, 'atheme.login'), [1, 3, 5, 6]);
    }

    public function logout($nickname)
    {
        return $this->checkResponse($this->doCmd($nickname, $this->getToken(), 'atheme.logout'), [1, 3, 5]);
    }

    public function getInfo($nickname = 'x')
    {
        $this->addParams(sprintf('NICKSERV INFO %s', $nickname));
        return $this->checkResponse($this->doCmd($nickname, $this->getToken()));
    }

}
