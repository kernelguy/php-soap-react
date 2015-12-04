<?php

namespace Clue\React\Soap;

use React\EventLoop\LoopInterface;
use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Message\Response;

class Factory
{
    private $loop;
    private $browser;

    public function __construct(LoopInterface $loop, Browser $browser = null)
    {
        if ($browser === null) {
            $b = new Browser($loop);
            $browser = $b->withOptions(array(
                'followRedirects' => true,
                'maxRedirects' => 10,
                'obeySuccessCode' => false // This is required since SOAP returns SoapFault
                                           // messages with HTTP response code 500
            ));
        }
        $this->loop = $loop;
        $this->browser = $browser;
    }

    public function createClient($wsdl, $clientClass='\Clue\React\Soap\Client', $options=null)
    {
        $browser = $this->browser;

        return $this->browser->get($wsdl)->then(function (Response $response) use ($browser, $clientClass) {
            $url = 'data://text/plain;base64,' . base64_encode((string)$response->getBody());

            return new $clientClass($url, $browser, $options);
        });
    }
}
