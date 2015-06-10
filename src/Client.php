<?php

namespace TwitterAPI;

use TwitterAPI\HTTP\Client as HTTPClient;
use TwitterAPI\HTTP\ClientFactory as HTTPClientFactory;
use TwitterAPI\HTTP\Request as HTTPRequest;
use TwitterAPI\HTTP\Request;

class Client
{
    /**
     * @var \TwitterAPI\SessionCredentials
     */
    private $sessionCredentials;

    /**
     * @var \TwitterAPI\HTTP\Client
     */
    private $httpClient;

    /**
     * @param \TwitterAPI\SessionCredentials $sessionCredentials
     * @param \TwitterAPI\HTTP\Client $httpClient
     */
    public function __construct(SessionCredentials $sessionCredentials, HTTPClient $httpClient = null)
    {
        $this->sessionCredentials = $sessionCredentials;
        $this->httpClient = $httpClient ?: (new HTTPClientFactory)->select();
    }

    /**
     * @param \TwitterAPI\HTTP\Request $request $request
     * @return \TwitterAPI\HTTP\Response
     */
    public function sendRequest(HTTPRequest $request)
    {
        if (!$request->getHeaders()->hasHeader('Authorization')) {
            $this->addOAuthHeaderToRequest($request);
        }

        return $this->httpClient->sendRequest($request);
    }

    private function getOAuthBaseData()
    {

    }

    private function buildOAuthHMACSubject(HTTPRequest $request)
    {

    }

    /**
     * @param \TwitterAPI\HTTP\Request $request
     */
    private function addOAuthHeaderToRequest(HTTPRequest $request)
    {
        $hmacSubject = $this->buildOAuthHMACSubject($request);
    }
}
