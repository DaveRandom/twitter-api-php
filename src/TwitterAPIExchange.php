<?php

namespace TwitterAPI;

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
 */
class TwitterAPIExchange
{
    /**
     * @var \TwitterAPI\SessionCredentials
     */
    private $sessionCredentials;

    /**
     * @var array
     */
    private $postfields;

    /**
     * @var string
     */
    private $getfield;

    /**
     * @var mixed
     */
    protected $oauth;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $requestMethod;

    /**
     * Create the API access object. Requires an array of settings::
     * oauth access token, oauth access token secret, consumer key, consumer secret
     * These are all available by creating your own application on dev.twitter.com
     * Requires the cURL library
     *
     * @throws \Exception When cURL isn't installed or incorrect settings parameters are provided
     *
     * @param array $settings
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
    }

    /**
     * Set postfields array, example: array('screen_name' => 'J7mbo')
     *
     * @param array $array Array of parameters to send to API
     *
     * @throws \Exception When you are trying to set both get and post fields
     *
     * @return TwitterAPIExchange Instance of self for method chaining
     */
    public function setPostfields(array $array)
    {
        if (!is_null($this->getGetfield()))
        {
            throw new \Exception('You can only choose get OR post fields.');
        }

        if (isset($array['status']) && substr($array['status'], 0, 1) === '@')
        {
            $array['status'] = sprintf("\0%s", $array['status']);
        }

        $this->postfields = $array;

        // rebuild oAuth
        if (isset($this->oauth['oauth_signature'])) {
            $this->buildOauth($this->url, $this->requestMethod);
        }

        return $this;
    }

    /**
     * Set getfield string, example: '?screen_name=J7mbo'
     *
     * @param string $string Get key and value pairs as string
     *
     * @throws \Exception
     *
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function setGetfield($string)
    {
        if (!is_null($this->getPostfields()))
        {
            throw new \Exception('You can only choose get OR post fields.');
        }

        $getfields = preg_replace('/^\?/', '', explode('&', $string));
        $params = array();

        foreach ($getfields as $field)
        {
            if ($field !== '')
            {
                list($key, $value) = explode('=', $field);
                $params[$key] = $value;
            }
        }

        $this->getfield = '?' . http_build_query($params);

        return $this;
    }

    /**
     * Get getfield string (simple getter)
     *
     * @return string $this->getfields
     */
    public function getGetfield()
    {
        return $this->getfield;
    }

    /**
     * Get postfields array (simple getter)
     *
     * @return array $this->postfields
     */
    public function getPostfields()
    {
        return $this->postfields;
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
        if (!in_array(strtolower($requestMethod), array('post', 'get')))
        {
            throw new \Exception('Request method must be either POST or GET');
        }

        $oauth = array(
            'oauth_consumer_key' => $this->sessionCredentials->getConsumerKey(),
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->sessionCredentials->getAccessToken(),
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );

        $getfield = $this->getGetfield();

        if (!is_null($getfield))
        {
            $getfields = str_replace('?', '', explode('&', $getfield));

            foreach ($getfields as $g)
            {
                $split = explode('=', $g);

                /** In case a null is passed through **/
                if (isset($split[1]))
                {
                    $oauth[$split[0]] = urldecode($split[1]);
                }
            }
        }

        $postfields = $this->getPostfields();

        if (!is_null($postfields)) {
            foreach ($postfields as $key => $value) {
                $oauth[$key] = $value;
            }
        }

        $hmac_subject = $this->buildBaseString($url, $requestMethod, $oauth);
        $hmac_key = rawurlencode($this->sessionCredentials->getConsumerSecret()) . '&' . rawurlencode($this->sessionCredentials->getAccessTokenSecret());
        $oauth_signature = base64_encode(hash_hmac('sha1', $hmac_subject, $hmac_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        $this->url = $url;
        $this->requestMethod = $requestMethod;
        $this->oauth = $oauth;

        return $this;
    }

    /**
     * Private method to generate the base string used by cURL
     *
     * @param string $baseURI
     * @param string $method
     * @param array  $params
     *
     * @return string Built base string
     */
    private function buildBaseString($baseURI, $method, $params)
    {
        $return = array();
        ksort($params);

        foreach($params as $key => $value)
        {
            $return[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
    }

    /**
     * Private method to generate authorization header used by cURL
     *
     * @param array $oauth Array of oauth data generated by buildOauth()
     *
     * @return string $return Header used by cURL for request
     */
    private function buildAuthorizationHeader(array $oauth)
    {
        $return = 'Authorization: OAuth ';
        $values = array();

        foreach($oauth as $key => $value)
        {
            if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
                'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))) {
                $values[] = "$key=\"" . rawurlencode($value) . "\"";
            }
        }

        $return .= implode(', ', $values);
        return $return;
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
        if (!is_bool($return))
        {
            throw new \Exception('performRequest parameter must be true or false');
        }

        $header =  array($this->buildAuthorizationHeader($this->oauth), 'Expect:');

        $getfield = $this->getGetfield();
        $postfields = $this->getPostfields();

        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ) + $curlOptions;

        if (!is_null($postfields))
        {
            $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
        }
        else
        {
            if ($getfield !== '')
            {
                $options[CURLOPT_URL] .= $getfield;
            }
        }

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);

        if (($error = curl_error($feed)) !== '')
        {
            curl_close($feed);

            throw new \Exception($error);
        }

        curl_close($feed);

        return $json;
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
        if (strtolower($method) === 'get')
        {
            $this->setGetfield($data);
        }
        else
        {
            $this->setPostfields($data);
        }

        return $this->buildOauth($url, $method)->performRequest(true, $curlOptions);
    }
}
