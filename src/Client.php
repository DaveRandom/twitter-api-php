<?php

namespace TwitterAPI;

use TwitterAPI\HTTP\Client as HTTPClient;
use TwitterAPI\HTTP\ClientFactory as HTTPClientFactory;
use TwitterAPI\HTTP\Header as HTTPHeader;
use TwitterAPI\HTTP\Request as HTTPRequest;

class Client
{
    /**
     * @var \TwitterAPI\OAuthBuilder
     */
    private $oauthBuilder;

    /**
     * @var \TwitterAPI\HTTP\Client
     */
    private $httpClient;

    /**
     * @param \TwitterAPI\OAuthBuilder $oauthBuilder
     * @param \TwitterAPI\HTTP\Client $httpClient
     */
    public function __construct(
        OAuthBuilder $oauthBuilder,
        HTTPClient $httpClient = null
    ) {
        $this->oauthBuilder = $oauthBuilder;
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

    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @param \TwitterAPI\SessionCredentials $sessionCredentials
     */
    public function addOAuthHeaderToRequest(HTTPRequest $request, SessionCredentials $sessionCredentials = null)
    {
        $headerValue = $this->oauthBuilder->buildOAuthAuthorizationHeader($request, $sessionCredentials);
        $header = new HTTPHeader('Authorization', $headerValue);
        $request->getHeaders()->addHeader($header);
    }
}
