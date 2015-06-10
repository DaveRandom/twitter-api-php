<?php

namespace TwitterAPI;

/**
 * @property-read $accessToken
 * @property-read $accessTokenSecret
 * @property-read $consumerKey
 * @property-read $consumerSecret
 */
class SessionCredentials
{
    use MagicPropertyAccess;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $accessTokenSecret;

    /**
     * @var string
     */
    private $consumerKey;

    /**
     * @var string
     */
    private $consumerSecret;

    /**
     * @param string $accessToken
     * @param string $accessTokenSecret
     * @param string $consumerKey
     * @param string $consumerSecret
     */
    public function __construct($accessToken, $accessTokenSecret, $consumerKey, $consumerSecret)
    {
        if ($this->accessToken !== null) {
            throw new \LogicException('Session credentials are immutable');
        }

        $readOnlyProperties = ['accessToken', 'accessTokenSecret', 'consumerKey', 'consumerSecret'];
        $this->defineMagicProperties($public = null, $readOnlyProperties);

        foreach ($readOnlyProperties as $propName) {
            $this->$propName = (string)$$propName;
        }
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getAccessTokenSecret()
    {
        return $this->accessTokenSecret;
    }

    /**
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }
}
