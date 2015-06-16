<?php

namespace TwitterAPI;

use \TwitterAPI\HTTP\Request as HTTPRequest;

class OAuthBuilder
{
    /**
     * @var \TwitterAPI\SessionCredentials
     */
    private $defaultSessionCredentials;

    /**
     * @param \TwitterAPI\SessionCredentials $defaultSessionCredentials
     */
    public function __construct(SessionCredentials $defaultSessionCredentials = null)
    {
        $this->defaultSessionCredentials = $defaultSessionCredentials;
    }

    /**
     * @param \TwitterAPI\SessionCredentials $sessionCredentials
     * @return string[]
     */
    private function buildOAuthBaseData($sessionCredentials)
    {
        $now = (string)time();

        return [
            'oauth_consumer_key' => $sessionCredentials->getConsumerKey(),
            'oauth_nonce' => $now, // todo: generate a better nonce?
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $now,
            'oauth_token' => $sessionCredentials->getAccessToken(),
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
     * @param \TwitterAPI\SessionCredentials $sessionCredentials
     * @return string
     */
    private function buildOAuthHMACKey($sessionCredentials)
    {
        return sprintf(
            '%s&%s',
            rawurlencode($sessionCredentials->getConsumerSecret()),
            rawurlencode($sessionCredentials->getAccessTokenSecret())
        );
    }

    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @param \TwitterAPI\SessionCredentials $sessionCredentials
     * @param string[] $oauthData
     * @return string
     */
    private function buildOAuthSignature($request, $sessionCredentials, $oauthData)
    {
        $hmacSubject = $this->buildOAuthHMACSubject($request, $oauthData);
        $hmacKey = $this->buildOAuthHMACKey($sessionCredentials);

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
     * @param \TwitterAPI\SessionCredentials $sessionCredentials
     * @return string
     * @throws \LogicException
     */
    public function buildOAuthAuthorizationHeader(HTTPRequest $request, SessionCredentials $sessionCredentials = null)
    {
        $sessionCredentials = $sessionCredentials ?: $this->defaultSessionCredentials;
        if (!$sessionCredentials) {
            throw new \LogicException('No session credentials provided and no default credentials stored');
        }

        $oauthData = $this->buildOAuthBaseData($sessionCredentials);
        $signature = $this->buildOAuthSignature($request, $sessionCredentials, $oauthData);

        return $this->buildAuthorizationHeader($oauthData, $signature);
    }

    /**
     * @return \TwitterAPI\SessionCredentials
     */
    public function getDefaultSessionCredentials()
    {
        return $this->defaultSessionCredentials;
    }

    /**
     * @param \TwitterAPI\SessionCredentials $defaultSessionCredentials
     */
    public function setDefaultSessionCredentials(SessionCredentials $defaultSessionCredentials)
    {
        $this->defaultSessionCredentials = $defaultSessionCredentials;
    }
}
