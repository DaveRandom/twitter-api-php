<?php
/**
 * Backwards-compatibility API wrapper
 */

use TwitterAPI\SessionCredentials;
use TwitterAPI\OAuthBuilder;
use TwitterAPI\HTTP\CurlClient;
use TwitterAPI\HTTP\Header;
use TwitterAPI\HTTP\Request;

/**
 * @property-read string $url
 * @property-read string $requestMethod
 */
class TwitterAPIExchange
{
    /**
     * @var \TwitterAPI\SessionCredentials
     */
    private $sessionCredentials;

    /**
     * @var \TwitterAPI\HTTP\CurlClient
     */
    private $client;

    /**
     * @var \TwitterAPI\OAuthBuilder
     */
    private $oauthBuilder;

    /**
     * @var \TwitterAPI\HTTP\Request
     */
    private $request;

    /**
     * @return string
     */
    private function getURL()
    {
        return $this->request->getURL();
    }

    /**
     * @return string
     */
    private function getRequestMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * Create the API access object. Requires an array of settings::
     * oauth access token, oauth access token secret, consumer key, consumer secret
     * These are all available by creating your own application on dev.twitter.com
     * Requires the cURL library
     *
     * @param array $settings
     * @throws \Exception When cURL isn't installed or incorrect settings parameters are provided
     */
    public function __construct(array $settings)
    {
        foreach (array('oauth_access_token', 'oauth_access_token_secret', 'consumer_key', 'consumer_secret') as $key) {
            if (!isset($settings[$key])) {
                throw new \Exception('Missing required setting: ' . $key);
            }
        }

        $this->sessionCredentials = new SessionCredentials(
            $settings['oauth_access_token'], $settings['oauth_access_token_secret'],
            $settings['consumer_key'], $settings['consumer_secret']
        );

        $this->oauthBuilder = new OAuthBuilder($this->sessionCredentials);
        $this->client = new CurlClient;
        $this->request = new Request;

        $this->request->getHeaders()->addHeader(new Header('Expect', '')); // todo: Connection: close?
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'url') {
            return $this->getURL();
        } else if ($name === 'requestMethod') {
            return $this->getRequestMethod();
        }

        throw new \LogicException('Undefined property: ' . __CLASS__ . '::' . $name);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        if ($name === 'url' || $name === 'requestMethod') {
            throw new \LogicException('Cannot write to read-only property: ' . __CLASS__ . '::' . $name);
        }

        throw new \LogicException('Undefined property: ' . __CLASS__ . '::' . $name);
    }

    /**
     * Set postfields array, example: array('screen_name' => 'J7mbo')
     *
     * @param array $array Array of parameters to send to API
     * @return TwitterAPIExchange Instance of self for method chaining
     * @throws \Exception When you are trying to set both get and post fields
     */
    public function setPostfields(array $array)
    {
        $this->request->setBodyParams($array);
        return $this;
    }

    /**
     * Set getfield string, example: '?screen_name=J7mbo'
     *
     * @param string $string Get key and value pairs as string
     * @return \TwitterAPIExchange Instance of self for method chaining
     * @throws \Exception
     */
    public function setGetfield($string)
    {
        parse_str(ltrim($string, '?'), $queryParams);
        $this->request->setURLParams($queryParams);

        return $this;
    }

    /**
     * Get getfield string (simple getter)
     *
     * @return string
     */
    public function getGetfield()
    {
        return '?' . http_build_query($this->request->getURLParams()->getArrayCopy());
    }

    /**
     * Get postfields array (simple getter)
     *
     * @return array
     */
    public function getPostfields()
    {
        return $this->request->getURLParams()->getArrayCopy();
    }

    /**
     * Build the Oauth object using params set in construct and additionals
     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
     *
     * @param string $url           The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
     * @param string $requestMethod Either POST or GET
     * @throws \Exception
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function buildOauth($url, $requestMethod)
    {
        try {
            $this->request->setURL($url);
            $this->request->setMethod($requestMethod);

            $headerValue = $this->oauthBuilder->buildOAuthAuthorizationHeader($this->request);
            $this->request->getHeaders()->addHeader(new Header('Authorization', $headerValue));

            return $this;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Perform the actual data retrieval from the API
     *
     * @param boolean $return      If true, returns data. This is left in for backward compatibility reasons
     * @param array   $curlOptions Additional Curl options for this request
     * @throws \Exception
     * @return string json If $return param is true, returns json data.
     */
    public function performRequest($return = true, $curlOptions = array())
    {
        if (!is_bool($return)) { // pointless, retained for BC only
            throw new \Exception('$return parameter must be true or false');
        }

        $curlOptions += array(CURLOPT_TIMEOUT => 10);

        try {
            return $this->client
                ->sendRequest($this->request, $curlOptions)
                ->getBody();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Helper method to perform our request
     *
     * @param string $url
     * @param string $method
     * @param string|array $data
     * @param array $curlOptions
     * @throws \Exception
     * @return string
     */
    public function request($url, $method = 'get', $data = null, $curlOptions = array())
    {
        if (strtolower($method) === 'get') {
            $this->setGetfield($data);
        } else {
            $this->setPostfields($data);
        }

        return $this
            ->buildOauth($url, $method)
            ->performRequest(true, $curlOptions);
    }
}
