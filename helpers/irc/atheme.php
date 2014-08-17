<?php namespace Cysha\Modules\Darchoods\Helpers\IRC;

class atheme
{
    public $xmlURL;
    public $params;

    public function __construct()
    {
        list($ip, $port) = explode(':', \Config::get('darchoods::module.atheme'));
        $this->xmlURL = 'http://'.$ip.':'.$port.'/xmlrpc';
    }

    public function doCmd()
    {
        $args = func_get_args();

        return call_user_func_array(array($this, 'cmd'), $args);
    }

    public function cmd($nick = '.', $id = '.', $cmd = 'atheme.command')
    {
        $client = new Zend\XmlRpc\Client($this->xmlURL);

            $params = [];
            $params[] = $id;
            $params[] = $nick;
            $params[] = $_SERVER['REMOTE_ADDR'];
            $params = array_merge($params, (count($this->params) ? $this->params : []));

        $request = new Zend\XmlRpc\Request();
            $request->setMethod($cmd);
            $request->setParams($params);

        try {
            $client->doRequest($request);

        } catch ( Zend\XmlRpc\Client\FaultException $e) {
            $a = $client->getLastRequest();
            echo \Debug::dump($a, '', 'red');
        }

        $response = $client->getLastResponse();
        $this->testTimeout($response);

        return $response;
    }

    public function addParam($param)
    {
        $this->params[] = $param;
    }

    public function addParams($params = [])
    {
        if (!is_array($params)) {
            $params = explode(' ', $params);
        }

        foreach ($params as $param) {
            $this->addParam($param);
        }
    }

    public function parseXML($xml)
    {
        return json_decode(json_encode((array) simplexml_load_string($xml)), true);
    }

    public function checkResponse($response, $faultCodes = [])
    {
        if ($response->isFault() && in_array($response->getFault()->getCode(), $faultCodes)) {
            return [$response->getFault()->getCode(), $response->getFault()->getMessage()];
        }

        $str = $this->parseXML($response->__toString());
        return [true, $str['params']['param']['value']['string']];
    }
}
