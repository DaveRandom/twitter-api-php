<?php

namespace TwitterAPI;

class SessionCredentials
{
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

        $this->accessToken       = (string)$accessToken;
        $this->accessTokenSecret = (string)$accessTokenSecret;
        $this->consumerKey       = (string)$consumerKey;
        $this->consumerSecret    = (string)$consumerSecret;
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
