<?php

namespace Clue\React\Soap\Protocol;

use \SoapClient;
use Clue\React\Buzz\Message\Request;
use Clue\React\Buzz\Message\Headers;
use Clue\React\Buzz\Message\Body;

class ClientEncoder extends SoapClient
{
    private $request = null;

    public function encode($name, $args)
    {
        $this->__soapCall($name, $args);

        $request = $this->request;
        $this->request = null;

        return $request;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $headers = array(
            'SOAPAction' => (string)$action,
            'Content-Type' => 'text/xml; charset=utf-8',
            'Content-Length' => strlen($request)
        );
        if (isset($this->_login)) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->_login . ':' . $this->_password);
        }
        $this->request = new Request(
            'POST',
            (string)$location,
            new Headers($headers),
            new Body((string)$request)
        );

        // do not actually block here, just pretend we're done...
        return '';
    }
}
