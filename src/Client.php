<?php

namespace TwitterAPI;

use TwitterAPI\HTTP\Client as HTTPClient;
use TwitterAPI\HTTP\ClientFactory as HTTPClientFactory;
use TwitterAPI\HTTP\Header as HTTPHeader;
use TwitterAPI\HTTP\Request as HTTPRequest;

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

    /**
     * @return string[]
     */
    private function getOAuthBaseData()
    {
        $now = (string)time();

        return [
            'oauth_consumer_key' => $this->sessionCredentials->getConsumerKey(),
            'oauth_nonce' => $now, // todo: generate a better nonce?
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $now,
            'oauth_token' => $this->sessionCredentials->getAccessToken(),
            'oauth_version' => '1.0'
        ];
    }

    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @param string[] $oauthData
     * @return string
     */
    private function buildOAuthHMACSubject($request, $oauthData)
    {
        $requestParamStore = $request->getMethod() === 'GET' ? $request->getURLParams() : $request->getBodyParams();
        foreach ($requestParamStore as $key => $value) {
            $oauthData[$key] = $value;
        }

        ksort($oauthData);

        $fields = [];
        foreach($oauthData as $key => $value) {
            $fields[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        return sprintf(
            '%s&%s&%s',
            $request->getMethod(),
            rawurlencode($request->getURL()),
            rawurlencode(implode('&', $fields))
        );
    }

    /**
     * @return string
     */
    private function buildOAuthHMACKey()
    {
        return sprintf(
            '%s&%s',
            rawurlencode($this->sessionCredentials->getConsumerSecret()),
            rawurlencode($this->sessionCredentials->getAccessTokenSecret())
        );
    }

    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @param string[] $oauthData
     * @return string
     */
    private function buildOAuthSignature($request, $oauthData)
    {
        $hmacSubject = $this->buildOAuthHMACSubject($request, $oauthData);
        $hmacKey = $this->buildOAuthHMACKey();

        return base64_encode(hash_hmac('sha1', $hmacSubject, $hmacKey, true));
    }

    /**
     * @param string[] $oauthData
     * @param string $signature
     * @return string
     */
    private function buildAuthorizationHeader($oauthData, $signature)
    {
        $values = ['oauth_signature' => $signature];

        foreach ($oauthData as $key => $value) {
            $values[] = $key . '="' . rawurlencode($value) . '"';
        }

        return 'OAuth ' . implode(', ', $values);
    }

    /**
     * @param \TwitterAPI\HTTP\Request $request
     */
    private function addOAuthHeaderToRequest(HTTPRequest $request)
    {
        $oauthData = $this->getOAuthBaseData();
        $signature = $this->buildOAuthSignature($request, $oauthData);
        $headerValue = $this->buildAuthorizationHeader($oauthData, $signature);

        $request->getHeaders()->addHeader(new HTTPHeader('Authorization', $headerValue));
    }
}
