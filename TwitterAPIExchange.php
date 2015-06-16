<?php
/**
 * Backwards-compatibility API wrapper
 */

use TwitterAPI\SessionCredentials;

/**
 * Twitter-API-PHP : Simple PHP wrapper for the v1.1 API
 *
 * PHP version 5.3.10
 *
 * @category Awesomeness
 * @package  Twitter-API-PHP
 * @author   James Mallison <me@j7mbo.co.uk>
 * @license  MIT License
 * @link     http://github.com/j7mbo/twitter-api-php
 * @property-read string $url
 * @property-read string $requestMethod
 */
class TwitterAPIExchange
{
    /**
     * @var mixed
     * @todo maybe get rid of this?
     */
    protected $oauth;

    /**
     * @var \TwitterAPI\SessionCredentials
     */
    private $sessionCredentials;

    /**
     * @return string
     */
    private function getURL()
    {
        // todo
    }

    /**
     * @return string
     */
    private function getRequestMethod()
    {
        // todo
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
        foreach (['oauth_access_token', 'oauth_access_token_secret', 'consumer_key', 'consumer_secret'] as $key) {
            if (!isset($settings[$key])) {
                throw new \Exception('Missing required setting: ' . $key);
            }
        }

        $this->sessionCredentials = new SessionCredentials(
            $settings['oauth_access_token'], $settings['oauth_access_token_secret'],
            $settings['consumer_key'], $settings['consumer_secret']
        );

        // todo
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
        // todo
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
        // todo
    }

    /**
     * Get getfield string (simple getter)
     *
     * @return string
     */
    public function getGetfield()
    {
        // todo
    }

    /**
     * Get postfields array (simple getter)
     *
     * @return array
     */
    public function getPostfields()
    {
        // todo
    }

    /**
     * Build the Oauth object using params set in construct and additionals
     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
     *
     * @param string $url           The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
     * @param string $requestMethod Either POST or GET
     *
     * @throws \Exception
     *
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function buildOauth($url, $requestMethod)
    {
        // todo
    }

    /**
     * Perform the actual data retrieval from the API
     *
     * @param boolean $return      If true, returns data. This is left in for backward compatibility reasons
     * @param array   $curlOptions Additional Curl options for this request
     *
     * @throws \Exception
     *
     * @return string json If $return param is true, returns json data.
     */
    public function performRequest($return = true, $curlOptions = array())
    {
        // todo
    }

    /**
     * Helper method to perform our request
     *
     * @param string $url
     * @param string $method
     * @param string $data
     * @param array  $curlOptions
     *
     * @throws \Exception
     *
     * @return string The json response from the server
     */
    public function request($url, $method = 'get', $data = null, $curlOptions = array())
    {
        // todo
    }
}
